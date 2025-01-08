<?php
class Adjust_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    if(!empty($code))
    {
      $rs = $this->db->where('code', $code)->get('adjust');
      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return FALSE;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('adjust_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



	public function get_not_save_detail($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->where('adjust_code', $code)
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->where('valid', 0)
    ->where('is_cancle', 0)
    ->get('adjust_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    if(!empty($code))
    {
      $rs = $this->db
      ->select('adjust_detail.*')
      ->select('products.name AS product_name')
      ->select('zone.name AS zone_name')
      ->select('warehouse.name AS warehouse_name')
      ->from('adjust_detail')
      ->join('products', 'adjust_detail.product_code = products.code')
      ->join('zone', 'adjust_detail.zone_code = zone.code', 'left')
      ->join('warehouse', 'adjust_detail.warehouse_code = warehouse.code', 'left')
      ->where('adjust_detail.adjust_code', $code)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return FALSE;
  }



  public function get_exists_detail($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('adjust_detail.*')
    ->select('products.name AS product_name')
    ->select('zone.name AS zone_name')
    ->select('warehouse.name AS warehouse_name')
    ->from('adjust_detail')
    ->join('products', 'adjust_detail.product_code = products.code')
    ->join('zone', 'adjust_detail.zone_code = zone.code', 'left')
    ->join('warehouse', 'adjust_detail.warehouse_code = warehouse.code', 'left')
    ->where('adjust_detail.adjust_code', $code)
    ->where('adjust_detail.product_code', $product_code)
    ->where('adjust_detail.zone_code', $zone_code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('adjust', $ds);
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('adjust_detail', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('adjust', $ds);
    }
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('adjust_code', $code)->update('adjust_detail', $ds);
    }

    return FALSE;
  }


  public function update_detail_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where("id", $id)->update("adjust_detail");
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('adjust_detail');
  }


  public function delete_details($code)
  {
    return $this->db->where('adjust_code', $code)->delete('adjust_detail');
  }




  public function valid_detail($id)
  {
    return $this->db->set('valid', '1')->where('id', $id)->update('adjust_detail');
  }



  public function change_status($code, $status)
  {
    return $this->db->set('status', $status)->set('update_user', get_cookie('uname'))->where('code', $code)->update('adjust');
  }


  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      if(!empty($ds['code']))
      {
        $this->db->like('code', $ds['code']);
      }

      if(!empty($ds['reference']))
      {
        $this->db->like('reference', $ds['reference']);
      }

      if(isset($ds['user']) && $ds['user'] != 'all')
      {
        $this->db->where('user', $ds['user']);
      }


      if(!empty($ds['from_date']) && !empty($ds['to_date']))
      {
        $this->db->where('date_add >=', from_date($ds['from_date']));
        $this->db->where('date_add <=', to_date($ds['to_date']));
      }

      if(!empty($ds['remark']))
      {
        $this->db->like('remark', $ds['remark']);
      }


      if($ds['status'] !== 'all')
      {
        $this->db->where('status', $ds['status']);
      }

      return $this->db->count_all_results('adjust');
    }

    return 0;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('adj.*, user.name AS display_name')
    ->from('adjust AS adj')
    ->join('user AS user', 'adj.user = user.uname', 'left');

    if(!empty($ds))
    {
      if(!empty($ds['code']))
      {
        $this->db->like('adj.code', $ds['code']);
      }

      if(!empty($ds['reference']))
      {
        $this->db->like('ajd.reference', $ds['reference']);
      }

      if(isset($ds['user']) && $ds['user'] != 'all')
      {
        $this->db->where('adj.user', $ds['user']);
      }

      if(!empty($ds['from_date']) && !empty($ds['to_date']))
      {
        $this->db->where('adj.date_add >=', from_date($ds['from_date']));
        $this->db->where('adj.date_add <=', to_date($ds['to_date']));
      }

      if(!empty($ds['remark']))
      {
        $this->db->like('adj.remark', $ds['remark']);
      }


      if($ds['status'] !== 'all')
      {
        $this->db->where('adj.status', $ds['status']);
      }

      $this->db->order_by('adj.code', 'DESC');

      $this->db->limit($perpage, $offset);

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

    }

    return FALSE;
  }



  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('adjust');

    return $rs->row()->code;
  }
} //--- End Model
 ?>
