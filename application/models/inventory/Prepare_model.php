<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_warehouse_code($zone_code)
  {
    $rs = $this->db->select('warehouse_code')->where('code', $zone_code)->get('zone');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->warehouse_code;
    }

    return  NULL;
  }



  public function update_buffer($order_code, $product_code, $zone_code, $qty)
  {
    if(!$this->is_exists_buffer($order_code, $product_code, $zone_code))
    {
      $arr = array(
        'order_code' => $order_code,
        'product_code' => $product_code,
        'warehouse_code' => $this->get_warehouse_code($zone_code),
        'zone_code' => $zone_code,
        'qty' => $qty,
        'user' => get_cookie('uname')
      );

      return $this->db->insert('buffer', $arr);
    }
    else
    {
      return $this->db
      ->set("qty", "qty + {$qty}", FALSE)
      ->where("order_code", $order_code)
      ->where("product_code", $product_code)
      ->where("zone_code", $zone_code)
      ->update("buffer");
    }

    return FALSE;
  }


  public function is_exists_buffer($order_code, $item_code, $zone_code)
  {
    $rs = $this->db->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->get('buffer');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function update_prepare($order_code, $product_code, $zone_code, $qty)
  {
    if(!$this->is_exists_prepare($order_code, $product_code, $zone_code))
    {
      $arr = array(
        'order_code' => $order_code,
        'product_code' => $product_code,
        'zone_code' => $zone_code,
        'qty' => $qty,
        'user' => get_cookie('uname')
      );

      return $this->db->insert('prepare', $arr);
    }
    else
    {
      return $this->db
      ->set("qty", "qty + {$qty}", FALSE)
      ->where('order_code', $order_code)
      ->where('product_code', $product_code)
      ->where('zone_code', $zone_code)
      ->update('prepare');

      return $this->db->query($qr);
    }

    return FALSE;
  }



  public function is_exists_prepare($order_code, $item_code, $zone_code)
  {
    $rs = $this->db->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->get('prepare');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }





  public function get_prepared($order_code, $item_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('buffer');

    return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
  }


  public function get_total_prepared($order_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->get('buffer');

    return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
  }


  //---- แสดงสินค้าว่าจัดมาจากโซนไหนบ้าง
  public function get_prepared_from_zone($order_code, $item_code)
  {
    $rs = $this->db->select('buffer.*, zone.name')
    ->from('buffer')
    ->join('zone', 'zone.code = buffer.zone_code')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- แสดงยอดรวมสินค้าที่ถูกจัดไปแล้วจากโซนนี้
  public function get_prepared_zone($zone_code, $item_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('zone_code', $zone_code)
    ->where('product_code', $item_code)
    ->get('buffer');

    return $rs->row()->qty;
  }


  public function remove_prepare($order_code, $item_code, $zone_code)
  {
    return $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->delete('prepare');
  }


  public function get_buffer_zone($item_code, $zone_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->get('buffer');

    return $rs->row()->qty;
  }


  public function count_rows(array $ds = array(), $state = 3)
  {
    $this->db->select('state')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', $state);

    if(!empty($ds['code']))
    {
			$this->db->group_start();
      $this->db->like('orders.code', $ds['code']);
			$this->db->or_like('orders.reference', $ds['code']);
			$this->db->group_end();
    }

    if(!empty($ds['customer']))
    {
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('user', $users);
    }

    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->get();

    return $rs->num_rows();
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0, $state = 3)
  {
    $this->db
    ->select('orders.*, channels.name AS channels_name')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->where('orders.state', $state);

    if(!empty($ds['code']))
    {
			$this->db
      ->group_start()
      ->like('orders.code', $ds['code'])
			->or_like('orders.reference', $ds['code'])
			->group_end();
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('orders.customer_code', $ds['customer'])
      ->or_like('orders.customer_name', $ds['customer'])
      ->or_like('orders.customer_ref', $ds['customer'])
      ->group_end();
    }

    //---- user name / display name
    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('orders.user', $ds['user']);
    }


    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }


    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('orders.date_add', 'ASC')
    ->limit($perpage, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function clear_prepare($code)
  {
    return $this->db->where('order_code', $code)->delete('prepare');
  }



} //--- end class


 ?>
