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
    $this->db->where('state', $state);

    if(!empty($ds['code']))
    {
			$this->db->group_start();
      $this->db->like('code', $ds['code']);
			$this->db->or_like('reference', $ds['code']);
			$this->db->group_end();
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->or_like('customer_ref', $ds['customer'])
      ->group_end();
    }

    //---- user name / display name
    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if(isset($ds['order_round']) && $ds['order_round'] != 'all')
    {
      $this->db->where('order_round', $ds['order_round']);
    }

    if(isset($ds['shipping_round']) && $ds['shipping_round'] != 'all')
    {
      $this->db->where('shipping_round', $ds['shipping_round']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['ship_from_date']))
    {
      $this->db->where('shipping_date >=', from_date($ds['ship_from_date']));
    }

    if( ! empty($ds['ship_to_date']))
    {
      $this->db->where('shipping_date <=', to_date($ds['ship_to_date']));
    }

    return $this->db->count_all_results('orders');
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

    if(isset($ds['order_round']) && $ds['order_round'] != 'all')
    {
      $this->db->where('orders.order_round', $ds['order_round']);
    }

    if(isset($ds['shipping_round']) && $ds['shipping_round'] != 'all')
    {
      $this->db->where('orders.shipping_round', $ds['shipping_round']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['ship_from_date']))
    {
      $this->db->where('orders.shipping_date >=', from_date($ds['ship_from_date']));
    }

    if( ! empty($ds['ship_to_date']))
    {
      $this->db->where('orders.shipping_date <=', to_date($ds['ship_to_date']));
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
