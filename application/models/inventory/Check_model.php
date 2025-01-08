<?php
class Check_model extends CI_Model
{
  private $tb = "checks";
  private $td = "check_details";
  private $tr = "check_results";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_result(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tr, $ds);
    }

    return FALSE;
  }


  public function update($id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_details($check_id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('check_id', $check_id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($id)
  {
    $rs = $this->db
    ->select('cd.barcode, pd.code, pd.name, pd.cost, pd.price')
    ->select_sum('cd.qty')
    ->from('check_details AS cd')
    ->join('products AS pd', 'cd.barcode = pd.barcode', 'left')
    ->where('check_id', $id)
    ->group_by('cd.barcode')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sum_check_rows($check_id, $barcode)
  {
    $rs = $this->db
    ->select('cd.barcode, pd.code')
    ->select_sum('cd.qty')
    ->from('check_details AS cd')
    ->join('products AS pd', 'cd.barcode = pd.barcode', 'left')
    ->where('cd.check_id', $check_id)
    ->where('cd.barcode', $barcode)
    ->group_by('cd.barcode')
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_history($check_id, $limit, $user_id)
  {
    $rs = $this->db
    ->select('cd.id, cd.barcode, cd.qty, cd.date_add, pd.code')
    ->from('check_details AS cd')
    ->join('products AS pd', 'cd.barcode = pd.barcode', 'left')
    ->where('cd.check_id', $check_id)
    ->where('cd.user_id', $user_id)
    ->order_by('cd.id', 'DESC')
    ->limit($limit)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_results($check_id)
  {
    $rs = $this->db->where('check_id', $check_id)->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function update_result($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tr, $ds);
    }

    return FALSE;
  }


  public function get_result_row_by_product_code($check_id, $product_code)
  {
    $rs = $this->db
    ->where('check_id', $check_id)
    ->where('product_code', $product_code)
    ->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function delete_checked_rows(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where_in('id', $ds)->delete($this->td);
    }

    return FALSE;
  }


  public function drop_result($check_id)
  {
    return $this->db->where('check_id', $check_id)->delete($this->tr);
  }


  public function reset_stock_zone($check_id)
  {
    $arr = array(
      'stock_qty' => 0,
      'diff_qty' => 0
    );

    return $this->db->where('check_id', $check_id)->update($this->tr, $arr);
  }


  public function update_stock_zone($id, $stock_qty)
  {
    return $this->db->set("stock_qty", $stock_qty)->where('id', $id)->update($this->tr);
  }


  public function update_result_diff($check_id)
  {
    return $this->db->set("diff_qty", "check_qty - stock_qty", FALSE)->where('check_id', $check_id)->update($this->tr);
  }



  public function count_rows(array $ds = array())
  {
    if( isset($ds['code']) && $ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    if( isset($ds['subject']) && $ds['subject'] != '')
    {
      $this->db->like('subject', $ds['subject']);
    }

    if( isset($ds['user']) && $ds['user'] != '')
    {
      $this->db->like('user', $ds['user']);
    }

    if( isset($ds['zone_code']) && $ds['zone_code'] != '')
    {
      $this->db
      ->group_start()
      ->like('zone_code', $ds['zone_code'])
      ->or_like('zone_name', $ds['zone_code'])
      ->group_end();
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    if( isset($ds['code']) && $ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    if( isset($ds['subject']) && $ds['subject'] != '')
    {
      $this->db->like('subject', $ds['subject']);
    }

    if( isset($ds['user']) && $ds['user'] != '')
    {
      $this->db->like('user', $ds['user']);
    }

    if( isset($ds['zone_code']) && $ds['zone_code'] != '')
    {
      $this->db
      ->group_start()
      ->like('zone_code', $ds['zone_code'])
      ->or_like('zone_name', $ds['zone_code'])
      ->group_end();
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($limit, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //----- กรองรายการตรวจนับตามเงื่อนไข
  public function get_details_list($check_id, array $ds = array(), $limit = 20, $offset = 0)
  {
    $this->db
    ->select('cd.*, pd.code, pd.name, u.uname')
    ->from('check_details AS cd')
    ->join('products AS pd', 'cd.barcode = pd.barcode', 'left')
    ->join('user AS u', 'cd.user_id = u.id', 'left')
    ->where('cd.check_id', $check_id);

    if(isset($ds['barcode']) && $ds['barcode'] != '')
    {
      $this->db->like('cd.barcode', $ds['barcode']);
    }

    if(isset($ds['pd_code']) && $ds['pd_code'] != '')
    {
      $this->db
      ->group_start()
      ->like('pd.code', $ds['pd_code'])
      ->or_like('pd.name', $ds['pd_code'])
      ->group_end();
    }

    if(isset($ds['user']) && $ds['user'] != '')
    {
      $this->db
      ->group_start()
      ->like('u.uname', $ds['user'])
      ->or_like('u.name', $ds['user'])
      ->group_end();
    }

    $rs = $this->db->order_by('cd.id', 'DESC')->limit($limit, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_details_rows($check_id, array $ds = array())
  {
    $this->db
    ->from('check_details AS cd')
    ->join('products AS pd', 'cd.barcode = pd.barcode', 'left')
    ->join('user AS u', 'cd.user_id = u.id', 'left')
    ->where('cd.check_id', $check_id);

    if(isset($ds['barcode']) && $ds['barcode'] != '')
    {
      $this->db->like('cd.barcode', $ds['barcode']);
    }

    if(isset($ds['pd_code']) && $ds['pd_code'] != '')
    {
      $this->db
      ->group_start()
      ->like('pd.code', $ds['pd_code'])
      ->or_like('pd.name', $ds['pd_code'])
      ->group_end();
    }

    if(isset($ds['user']) && $ds['user'] != '')
    {
      $this->db
      ->group_start()
      ->like('u.uname', $ds['user'])
      ->or_like('u.name', $ds['user'])
      ->group_end();
    }

    return $this->db->count_all_results();
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() === 1)
		{
			return $rs->row()->code;
		}

		return NULL;
  }

  public function add_logs(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('check_logs', $ds);
    }

    return NULL;
  }

  public function get_logs($check_id)
  {
    $rs = $this->db->where('check_id', $check_id)->order_by('id', 'DESC')->get('check_logs');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
 ?>
