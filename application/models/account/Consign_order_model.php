<?php
class Consign_order_model extends CI_Model
{
  private $tb = 'consign_order';
  private $td = 'consign_order_detail';

  public function __construct()
  {
    parent::__construct();
  }


  public function add($ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function add_detail($ds = array())
  {
    if(!empty($ds))
    {
      $this->db->insert($this->td, $ds);
      return $this->db->insert_id();
    }

    return FALSE;
  }


  public function update($code, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_detail($id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('consign_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_ref_code($code, $check_code)
  {
    return $this->db->set('ref_code', $check_code)->where('code', $code)->update($this->tb);
  }


  public function drop_import_details($code, $check_code)
  {
    return $this->db->where('consign_code', $code)->where('ref_code', $check_code)->delete($this->td);
  }


  public function has_saved_imported($code, $check_code)
  {
    $rs = $this->db
    ->where('consign_code', $code)
    ->where('ref_code', $check_code)
    ->where('status', 1)
    ->limit(1)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('o.*, c.sale_code, c.type_code')
    ->from('consign_order AS o')
    ->join('customers AS c', 'o.customer_code = c.code', 'left')
    ->where('o.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('consign_code', $code)->get($this->td);
    if($rs->num_rows() >0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_exists_detail($code, $product_code, $price, $discountLabel, $input_type)
  {
    $rs = $this->db
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->where('price', $price)
    ->where('discount', $discountLabel)
    ->where('input_type', $input_type)
    ->where('status', 0)
    ->get($this->td);
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details_by_ids(array $ids = array())
  {
    return $this->db->where_in('id', $ids)->delete($this->td);
  }


  public function drop_details($code)
  {
    return $this->db->where('consign_code', $code)->delete($this->td);
  }


  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('consign_code', $code)->get($this->td);

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function get_item_gp($product_code, $zone_code)
  {
    $rs = $this->db
    ->select('order_sold.discount_label')
    ->from('order_sold')
    ->join('orders', 'order_sold.reference = orders.code', 'left')
    ->where_in('order_sold.role', array('C', 'N'))
    ->where('orders.zone_code', $zone_code)
    ->where('order_sold.product_code', $product_code)
    ->order_by('orders.date_add', 'DESC')
    ->limit(1)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row()->discount_label;
    }

    return 0;
  }


  public function get_unsave_qty($code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->where('status', 0)
    ->get($this->td);

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  public function change_detail_status($id, $status)
  {
    $this->db
    ->set('status', $status)
    ->where('id', $id);
    return $this->db->update($this->td);
  }


  public function change_all_detail_status($code, $status)
  {
    $this->db
    ->set('status', $status)
    ->where('consign_code', $code);
    return $this->db->update($this->td);
  }


  public function change_status($code, $status)
  {
    $this->db
    ->set('status', $status)
    ->set('update_user', get_cookie('uname'))
    ->where('code', $code);
    return $this->db->update($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('o.*, c.sale_code, c.type_code')
    ->from('consign_order AS o')
    ->join('customers AS c', 'o.customer_code = c.code', 'left');

    //--- status
    if($ds['status'] !== 'all')
    {
      $this->db->where('o.status', $ds['status']);
    }

    //--- document date
    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('o.date_add >=', from_date($ds['from_date']))->where('o.date_add <=', to_date($ds['to_date']));
    }

    if(! empty($ds['code']))
    {
      $this->db->like('o.code', $ds['code']);
    }

    //--- อ้างอิงเลขที่กระทบยอดสินค้า
    if(! empty($ds['ref_code']))
    {
      $this->db->like('o.ref_code', $ds['ref_code']);
    }


    if(!empty($ds['customer']))
    {
      $this->db->like('o.customer_code', $ds['customer'])->or_like('o.customer_name', $ds['customer']);
    }

    if( isset($ds['zone']) && $ds['zone'] != 'all')
    {
      $this->db->where('o.zone_code', $ds['zone']);
    }

    $rs = $this->db
    ->order_by('o.code', 'DESC')
    ->limit($perpage, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    //--- status
    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    //--- document date
    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']))->where('date_add <=', to_date($ds['to_date']));
    }


    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- อ้างอิงเลขที่กระทบยอดสินค้า
    if(!empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }


    if(!empty($ds['customer']))
    {
      $this->db->like('customer_code', $ds['customer'])->or_like('customer_name', $ds['customer']);
    }

    if( isset($ds['zone']) && $ds['zone'] != 'all')
    {
      $this->db->where('zone_code', $ds['zone']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM consign_order WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }

} //--- end class
?>
