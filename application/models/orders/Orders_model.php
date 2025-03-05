<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Orders_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('orders', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('orders', $ds);
    }

    return FALSE;
  }


  public function update_order_total_amount($code, $amount)
  {
    return $this->db->set('total_amount', $amount)->where('code', $code)->update('orders');
  }


  public function recal_order_balance($code)
  {
    return $this->db->set('balance', 'total_amount - deposit', FALSE)->where('code', $code)->update('orders');
  }

  public function update_deposit($code, $amount)
  {
    $rs = $this->db->set('deposit', "deposit + {$amount}", FALSE)->where('code', $code)->update('orders');

    if($rs)
    {
      return $this->recal_order_balance($code);
    }

    return FALSE;
  }

  public function get_order_deposit($code)
  {
    $rs = $this->db->select('deposit')->where('code', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->deposit;
    }

    return 0;
  }


  public function get_order_balance($code)
  {
    $rs = $this->db->select('balance')->where('code', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->balance;
    }

    return 0;
  }


  public function get_order_balance_by_customer($customer_code, $order_code = NULL)
  {
    $this->db
    ->select_sum('o.balance')
    ->from('orders AS o')
    ->join('payment_method AS pm', 'o.payment_code = pm.code', 'left')
    ->where('o.role', 'S')
    ->where('o.is_term', 0)
    ->where('o.state <=', 8)
    ->where('o.state >=', 2)
    ->where_in('pm.role', [2, 3]);

    if( ! empty($order_code))
    {
      $this->db->where('o.code !=', $order_code);
    }

    $rs = $this->db->where('o.customer_code', $customer_code)->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->balance;
    }

    return NULL;
  }


  public function get($code)
  {
		$rs = $this->db
		->select('o.*, c.name AS customer_name')
		->from('orders AS o')
		->join('customers AS c', 'o.customer_code = c.code', 'left')
		->where('o.code', $code)
		->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }

  public function get_active_order_code_by_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->where('state !=', 9)->where('status !=', 2)->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


	public function get_with_payment_role($code)
	{
		$rs = $this->db
		->select('orders.*, payment_method.role AS payment_role')
		->from('orders')
		->join('payment_method', 'orders.payment_code = payment_method.code', 'left')
		->where('orders.code', $code)
		->get();

		if($rs->num_rows() == 1)
		{
			return $rs->row();
		}

		return NULL;
	}



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_details', $ds);
    }

    return FALSE;
  }




  public function update_detail($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update('order_details', $ds);
  }




  public function remove_detail($id)
  {
    return $this->db->where('id', $id)->delete('order_details');
  }


  public function remove_all_details($order_code)
  {
    return $this->db->where('order_code', $order_code)->delete('order_details');
  }


  public function is_exists_detail($order_code, $item_code)
  {
    $rs = $this->db->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



	public function is_limit($role = 'S', $limit = 0)
	{
		if($limit != 0)
		{
			$start_date = date('Y-m-01 00:00:00');
			$end_date = date('Y-m-t');
			$count = $this->db
			->where('role', $role)
			->where('createDate >=', date('Y-m-01 00:00:00'))
			->where('createDate <=', date('Y-m-t 23:59:59'))
			->count_all_results('orders');

			if($count >= $limit)
			{
				return TRUE;
			}
		}

		return FALSE;
	}


  public function get_order_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_order_detail_id($order_code, $item_code)
  {
    $rs = $this->db
    ->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('order_details');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }




  public function get_order_details($code)
  {
		$rs = $this->db
		->select('od.*, pd.unit_code')
		->from('order_details AS od')
		->join('products AS pd', 'od.product_code = pd.code', 'left')
		->where('order_code', $code)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_unvalid_details($code)
  {
    $rs = $this->db
    ->where('order_code', $code)
    ->where('valid', 0)
    ->where('is_count', 1)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_valid_details($code)
  {
    $rs = $this->db
    ->where('order_code', $code)
    ->group_start()
    ->where('valid', 1)
    ->or_where('is_count', 0)
    ->group_end()
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_state($code)
  {
    $rs = $this->db->select('state')->where('code', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->state;
    }

    return FALSE;
  }



  public function get_order_code_by_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->get('orders');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }



  public function valid_detail($id)
  {
    return $this->db->set('valid', 1)->where('id', $id)->update('order_details');
  }



  public function valid_all_details($code)
  {
    return $this->db->set('valid', 1)->where('order_code', $code)->update('order_details');
  }


  public function unvalid_detail($id)
  {
    return $this->db->set('valid', 0)->where('id', $id)->update('order_details');
  }


	public function cancle_details($code)
	{
		return $this->db->set('is_cancle', 1)->where('order_code', $code)->update('order_details');
	}


	public function un_cancle_details($code)
	{
		return $this->db->set('is_cancle', 0)->where('order_code', $code)->update('order_details');
	}


  public function change_state($code, $state)
  {
    $arr = array(
      'state' => $state,
      'update_user' => get_cookie('uname')
    );

    return $this->db->where('code', $code)->update('orders', $arr);
  }




  public function update_shipping_code($code, $ship_code)
  {
    return $this->db->set('shipping_code', $ship_code)->where('code', $code)->update('orders');
  }


  public function update_shipping_fee($code, $amount)
  {
    return $this->db->set('shipping_fee', $amount, FALSE)->where('code', $code)->update('orders');
  }


  public function update_service_fee($code, $amount)
  {
    return $this->db->set('service_fee', $amount, FALSE)->where('code', $code)->update('orders');
  }



  public function set_never_expire($code, $option)
  {
    return $this->db->set('never_expire', $option)->where('code', $code)->update('orders');
  }


  public function un_expired($code)
  {
    $this->db->trans_start();
    $this->db->set('is_expired', 0)->where('order_code', $code)->update('order_details');
    $this->db->set('is_expired', 0)->where('code', $code)->update('orders');
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      return FALSE;
    }

    return TRUE;
  }


  public function set_completed($code)
  {
    return $this->db->set('is_complete', 1)->where('order_code', $code)->update('order_details');
  }



  public function un_complete($code)
  {
    return $this->db->set('is_complete', 0)->where('order_code', $code)->update('order_details');
  }


  public function paid($code, $paid)
  {
    $paid = $paid === TRUE ? 1 : 0;
    return $this->db->set('is_paid', $paid)->where('code', $code)->update('orders');
  }


  public function update_approver($code, $user)
  {
    return $this->db
    ->set('approver', $user)
    ->set('approve_date', now())
    ->set('is_approved', 1)
    ->where('code', $code)
    ->update('orders');
  }


  public function un_approver($code, $user)
  {
    return $this->db
    ->set('approver', NULL)
    ->set('approve_date', now())
    ->set('is_approved', 0)
    ->where('code', $code)
    ->update('orders');
  }


	public function count_rows(array $ds = array(), $role = 'S')
	{
    $this->db->where('role', $role);

		//---- เลขที่เอกสาร
		if( ! empty($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		//--- รหัส/ชื่อ ลูกค้า
		if( ! empty($ds['customer']))
		{
			$this->db
      ->group_start()
			->like('customer_code', $ds['customer'])
			->or_like('customer_name', $ds['customer'])
			->or_like('customer_ref', $ds['customer'])
			->group_end();
		}

    if( isset($ds['sale_code']) && $ds['sale_code'] != 'all')
    {
      $this->db->where('sale_code', $ds['sale_code']);
    }

    if( isset($ds['type_code']) && $ds['type_code'] != 'all')
    {
      $this->db->where('type_code', $ds['type_code']);
    }

		//---- user name / display name
		if( isset($ds['user']) && $ds['user'] != 'all')
		{
			$this->db->where('user', $ds['user']);
		}

		//---- เลขที่อ้างอิงออเดอร์ภายนอก
		if( ! empty($ds['reference']))
		{
			$this->db->like('reference', $ds['reference']);
		}

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference2']))
    {
      $this->db->like('reference2', $ds['reference2']);
    }

		//---เลขที่จัดส่ง
		if( ! empty($ds['ship_code']))
		{
			$this->db->like('shipping_code', $ds['ship_code']);
		}

		//--- ช่องทางการขาย
		if( ! empty($ds['channels']))
		{
			$this->db->where('channels_code', $ds['channels']);
		}

		//--- ช่องทางการชำระเงิน
		if( ! empty($ds['payment']))
		{
			$this->db->where('payment_code', $ds['payment']);
		}

    if(isset($ds['zone_code']) && $ds['zone_code'] != 'all')
    {
      $this->db->where('zone_code', $ds['zone_code']);
    }

		if( ! empty($ds['user_ref']))
		{
			$this->db->like('user_ref', $ds['user_ref']);
		}

		if( ! empty($ds['empName']))
		{
			$this->db->like('empName', $ds['empName']);
		}

		if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
		{
			$this->db->where('date_add >=', from_date($ds['from_date']));
			$this->db->where('date_add <=', to_date($ds['to_date']));
		}

		if(!empty($ds['warehouse']))
		{
			$this->db->where('warehouse_code', $ds['warehouse']);
		}

		if( ! empty($ds['notSave']))
		{
			$this->db->where('status', 0);
		}
		else
		{
			if(isset($ds['isApprove']) && $ds['isApprove'] != 'all')
			{
        $this->db->where('orders.status', 1);
			}
		}

		if( ! empty($ds['onlyMe']))
		{
			$this->db->where('user', $this->_user->uname);
		}

		if( ! empty($ds['isExpire']))
		{
			$this->db->where('is_expired', 1);
		}

		if(!empty($ds['state_list']))
		{
			$this->db->where_in('state', $ds['state_list']);
		}

    //--- ใช้กับเอกสารที่ต้อง approve เท่านั้น
		if(isset($ds['isApprove']) && $ds['isApprove'] !== 'all')
		{
      $this->db->where('is_approved', $ds['isApprove']);
		}


		//--- ใช้กับเอกสารที่ต้อง ว่ารับสินค้าเข้าปลายทางหรือยัง เท่านั้น
		if(isset($ds['isValid']) && $ds['isValid'] != 'all')
		{
      $this->db->where('is_valid', $ds['isValid']);
		}

		if( ! empty($ds['is_paid']) && $ds['is_paid'] != 'all')
		{
			$is_paid = ($ds['is_paid'] == 'not_paid' ? 0 : 1);
			$this->db->where('is_paid', $is_paid);
		}

		return $this->db->count_all_results('orders');
	}



	public function get_data(array $ds = array(), $perpage = 20, $offset = 0, $role = 'S')
	{
		$this->db->where('role', $role);

		//---- เลขที่เอกสาร
		if( ! empty($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		//--- รหัส/ชื่อ ลูกค้า
		if( ! empty($ds['customer']))
		{
			$this->db
      ->group_start()
			->like('customer_code', $ds['customer'])
			->or_like('customer_name', $ds['customer'])
			->or_like('customer_ref', $ds['customer'])
			->group_end();
		}

    if( isset($ds['sale_code']) && $ds['sale_code'] != 'all')
    {
      $this->db->where('sale_code', $ds['sale_code']);
    }

    if( isset($ds['type_code']) && $ds['type_code'] != 'all')
    {
      $this->db->where('type_code', $ds['type_code']);
    }

		//---- user name / display name
		if( isset($ds['user']) && $ds['user'] != 'all')
		{
			$this->db->where('user', $ds['user']);
		}

		//---- เลขที่อ้างอิงออเดอร์ภายนอก
		if( ! empty($ds['reference']))
		{
			$this->db->like('reference', $ds['reference']);
		}

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference2']))
    {
      $this->db->like('reference2', $ds['reference2']);
    }

		//---เลขที่จัดส่ง
		if( ! empty($ds['ship_code']))
		{
			$this->db->like('shipping_code', $ds['ship_code']);
		}

		//--- ช่องทางการขาย
		if( ! empty($ds['channels']))
		{
			$this->db->where('channels_code', $ds['channels']);
		}

		//--- ช่องทางการชำระเงิน
		if( ! empty($ds['payment']))
		{
			$this->db->where('payment_code', $ds['payment']);
		}

    if(isset($ds['zone_code']) && $ds['zone_code'] != 'all')
    {
      $this->db->where('zone_code', $ds['zone_code']);
    }

		if( ! empty($ds['user_ref']))
		{
			$this->db->like('user_ref', $ds['user_ref']);
		}

		if( ! empty($ds['empName']))
		{
			$this->db->like('empName', $ds['empName']);
		}

		if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
		{
			$this->db->where('date_add >=', from_date($ds['from_date']));
			$this->db->where('date_add <=', to_date($ds['to_date']));
		}

		if(!empty($ds['warehouse']))
		{
			$this->db->where('warehouse_code', $ds['warehouse']);
		}

		if( ! empty($ds['notSave']))
		{
			$this->db->where('status', 0);
		}
		else
		{
			if(isset($ds['isApprove']) && $ds['isApprove'] != 'all')
			{
        $this->db->where('status', 1);
			}
		}

		if( ! empty($ds['onlyMe']))
		{
			$this->db->where('user', $this->_user->uname);
		}

		if( ! empty($ds['isExpire']))
		{
			$this->db->where('is_expired', 1);
		}

		if(!empty($ds['state_list']))
		{
			$this->db->where_in('state', $ds['state_list']);
		}

		//--- ใช้กับเอกสารที่ต้อง approve เท่านั้น
		if(isset($ds['isApprove']) && $ds['isApprove'] !== 'all')
		{
      $this->db->where('is_approved', $ds['isApprove']);
		}

		//--- ใช้กับเอกสารที่ต้อง ว่ารับสินค้าเข้าปลายทางหรือยัง เท่านั้น
		if(isset($ds['isValid']) && $ds['isValid'] != 'all')
		{
      $this->db->where('is_valid', $ds['isValid']);
		}

		if( ! empty($ds['is_paid']) && $ds['is_paid'] != 'all')
		{
			$is_paid = ($ds['is_paid'] == 'not_paid' ? 0 : 1);
			$this->db->where('is_paid', $is_paid);
		}


		if( ! empty($ds['order_by']))
		{
			$this->db->order_by($ds['order_by'], $ds['sort_by']);
		}
		else
		{
			$this->db->order_by('code', 'DESC');
		}

    $this->db->limit($perpage, $offset);

		$rs = $this->db->get('orders');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return FALSE;
	}



  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM orders WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }




  public function get_order_total_amount($code)
  {
    $this->db->select_sum('total_amount', 'amount');
    $this->db->where('order_code', $code);
    $rs = $this->db->get('order_details');
    return $rs->row()->amount;
  }


  public function get_bill_total_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount', 'amount')
    ->where('reference', $code)
    ->get('order_sold');

    return $rs->row()->amount;
  }



  public function get_order_total_qty($code)
  {
    $this->db->select_sum('qty', 'qty');
    $this->db->where('order_code', $code);
    $rs = $this->db->get('order_details');
    return $rs->row()->qty;
  }


  //--- ใช้คำนวนยอดเครดิตคงเหลือ
  public function get_sum_not_complete_amount($customer_code)
  {
    $rs = $this->db
    ->select_sum('order_details.total_amount', 'amount')
    ->from('order_details')
    ->join('orders', 'orders.code = order_details.order_code', 'left')
    ->where_in('orders.role', array('S', 'C', 'N'))
    ->where('orders.customer_code', $customer_code)
    ->where('order_details.is_complete', 0)
    ->where('orders.is_expired', 0)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }



  public function get_bill_discount($code)
  {
    $rs = $this->db->select('bDiscAmount')
    ->where('code', $code)
    ->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->bDiscAmount;
    }

    return 0;
  }


  public function get_sum_style_qty($order_code, $style_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('style_code', $style_code)
    ->get('order_detils');

    return $rs->row()->qty;
  }




  public function get_reserv_stock($item_code, $warehouse_code = NULL)
  {
    if( ! empty($warehouse_code))
    {
      $rs = $this->db
      ->select_sum('od.qty')
      ->from('order_details AS od')
      ->join('orders AS o', 'od.order_code = o.code', 'left')
      ->where('od.product_code', $item_code)
      ->where('od.is_cancle', 0)
      ->where('od.is_complete', 0)
      ->where('od.is_expired', 0)
      ->where('od.is_count', 1)
      ->where('o.warehouse_code', $warehouse_code)
      ->get();
    }
    else
    {
      $rs = $this->db->select_sum('qty')
      ->where('product_code', $item_code)
      ->where('is_cancle', 0)
      ->where('is_complete', 0)
      ->where('is_expired', 0)
      ->where('is_count', 1)
      ->get('order_details');
    }

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



  public function get_reserv_stock_by_style($style_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('style_code', $style_code)
    ->where('is_complete', 0)
    ->where('is_expired', 0)
    ->where('is_count', 1)
    ->get('order_details');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('orders');
  }


  public function clear_order_detail($code)
  {
    return $this->db->where('order_code', $code)->delete('order_details');
  }


  public function set_address_id($code, $id_address)
  {
    return $this->db->set('address_id', $id_address)->where('code', $code)->update('orders');
  }


	public function is_term($code)
	{
		$rs = $this->db->where('role', 'S')->where('code', $code)->where('is_term', 1)->count_all_results('orders');
		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}


  public function get_tags_list()
  {
    $rs = $this->db->where('active', 1)->get('order_tags');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


} //--- End class


 ?>
