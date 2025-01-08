<?php
class Delivery_order_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_sold_details($reference)
  {
    $rs = $this->db->where('reference', $reference)->get('order_sold');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function count_rows(array $ds = array(), $state = 7)
  {
    $this->db->where('state', $state);

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

		if(!empty($ds['invoice_code']))
		{
			$this->db->like('invoice_code', $ds['invoice_code']);
		}

		if(isset($ds['is_inv']) && $ds['is_inv'] != "all")
		{
			if($ds['is_inv'] == 1)
			{
				$this->db->where('invoice_code IS NOT NULL', NULL, FALSE);
			}
			else
			{
				$this->db->where('invoice_code IS NULL', NULL, FALSE);
			}
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
    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    if($ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if($ds['payment'] != 'all')
    {
      $this->db->where('payment_code', $ds['payment']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results('orders');
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0, $state = 7)
  {
    $this->db->where('state', $state);

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

		if(!empty($ds['invoice_code']))
		{
			$this->db->like('invoice_code', $ds['invoice_code']);
		}

		if(isset($ds['is_inv']) && $ds['is_inv'] != "all")
		{
			if($ds['is_inv'] == 1)
			{
				$this->db->where('invoice_code IS NOT NULL', NULL, FALSE);
			}
			else
			{
				$this->db->where('invoice_code IS NULL', NULL, FALSE);
			}
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
    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    if($ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if($ds['payment'] != 'all')
    {
      $this->db->where('payment_code', $ds['payment']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

		$this->db->order_by('date_add', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_data(array $ds = array(), $perpage = 20, $offset = 0, $state = 7)
  {
    $this->db->select('orders.*')
    ->select('channels.name AS channels_name')
    ->select('payment_method.name AS payment_name, payment_method.role AS payment_role')
    ->select('customers.name AS customer_name')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('payment_method', 'payment_method.code = orders.payment_code', 'left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', $state);

    if(!empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

		if(!empty($ds['invoice_code']))
		{
			$this->db->like('orders.invoice_code', $ds['invoice_code']);
		}

		if(isset($ds['is_inv']) && $ds['is_inv'] != "all")
		{
			if($ds['is_inv'] == 1)
			{
				$this->db->where('invoice_code IS NOT NULL', NULL, FALSE);
			}
			else
			{
				$this->db->where('invoice_code IS NULL', NULL, FALSE);
			}
		}


    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('orders.user', $users);
    }


    if($ds['role'] != 'all')
    {
      $this->db->where('orders.role', $ds['role']);
    }

    if($ds['channels'] != 'all')
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if($ds['payment'] != 'all')
    {
      $this->db->where('orders.payment_code', $ds['payment']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

		$this->db->order_by('orders.date_add', 'DESC')->limit($perpage, $offset);


    $rs = $this->db->get();

    return $rs->result();
  }




    //------------------ สำหรับแสดงยอดที่มีการบันทึกขายไปแล้ว -----------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสอนค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะได้ยอดสั่งซื้อเป็นหลัก หากไม่มียอดตรวจ จะได้ยอดตรวจ เป็น NULL
    //--- กรณีสินค้าเป็นสินค้าที่ไม่นับสต็อกจะบันทึกตามยอดที่สั่งมา
    public function get_billed_detail($code, $use_qc = TRUE)
    {
      $qr = "SELECT o.id, o.product_code, o.product_name, o.qty AS order_qty, o.is_count, ";
      $qr .= "o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "WHERE o.order_code = '{$code}'";

      $qs = $this->db->query($qr);

      if($qs->num_rows() > 0)
      {
        $details = $qs->result();

        foreach($details as $rs)
        {
          $rs->prepared = $rs->is_count == 0 ? $rs->order_qty : $this->get_sum_prepared($code, $rs->product_code);
          $rs->qc = $use_qc ? ($rs->is_count == 0 ? $rs->order_qty : $this->get_sum_qc($code, $rs->product_code)) : $rs->prepared;
        }

        return $details;
      }

      return NULL;
    }


    //------------- สำหรับใช้ในแสดงรายการก่อนการบันทึกขาย ---------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสินค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะไม่ได้ยอดที่มีการสั่งซื้อแต่ไม่มียอดตรวจ หรือ มียอดตรวจแต่ไม่มียอดสั่งซื้อ (กรณีมีการแก้ไขออเดอร์)

    public function get_pre_bill_detail($code, $use_qc = TRUE)
    {
      $qr = "SELECT o.id, o.product_code, o.product_name, o.qty AS order_qty, o.is_count, ";
      $qr .= "o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "WHERE o.order_code = '{$code}'";

      $qs = $this->db->query($qr);

      if($qs->num_rows() > 0)
      {
        $details = $qs->result();

        foreach($details as $rs)
        {
          $rs->prepared = $rs->is_count == 0 ? $rs->order_qty : $this->get_sum_buffer($code, $rs->product_code);
          $rs->qc = $use_qc ? ($rs->is_count == 0 ? $rs->order_qty : $this->get_sum_qc($code, $rs->product_code)) : $rs->prepared;
        }

        return $details;
      }

      return NULL;
    }


    //------------- สำหรับใช้ในการบันทึกขาย ---------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสินค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะไม่ได้ยอดที่มีการสั่งซื้อแต่ไม่มียอดตรวจ หรือ มียอดตรวจแต่ไม่มียอดสั่งซื้อ (กรณีมีการแก้ไขออเดอร์)

    public function get_bill_detail($code, $use_qc = TRUE)
    {
      $qr = "SELECT o.id, o.style_code, o.product_code, o.product_name, o.qty AS order_qty, ";
      $qr .= "o.cost, o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "o.id_rule, ru.id_policy, o.is_count, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "LEFT JOIN discount_rule AS ru ON ru.id = o.id_rule ";
      $qr .= "WHERE o.order_code = '{$code}' ";
      $qr .= "AND o.is_count = 1 ";

      $qs = $this->db->query($qr);

      if($qs->num_rows() > 0)
      {
        $details = $qs->result();

        foreach($details as $rs)
        {
          $rs->prepared = $this->get_sum_buffer($code, $rs->product_code);
          $rs->qc = $use_qc ? $this->get_sum_qc($code, $rs->product_code) : $rs->prepared;
        }

        return $details;
      }

      return NULL;
    }


		//---- กรณีที่ มีการตั้งค่า ไม่จัดสินค้าไว้ ใช้ข้องมูลจาก function นี้ในการบันทึกขายเต็มจำนวนที่สั่งมา
		public function get_order_details($code)
		{
			$rs = $this->db
			->select('o.id, o.style_code, o.product_code, o.product_name, o.qty AS order_qty')
			->select('o.cost, o.price, o.discount1, o.discount2, o.discount3')
			->select('o.id_rule, ru.id_policy, o.is_count')
			->select('(o.discount_amount/o.qty) AS discount_amount', FALSE)
			->select('(o.total_amount/o.qty) AS final_price', FALSE)
			->from('order_details AS o')
			->join('discount_rule AS ru', 'ru.id = o.id_rule', 'left')
			->where('o.order_code', $code)
			->get();


			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}

			return NULL;
		}


    public function get_sum_buffer($order_code, $product_code)
    {
      $rs = $this->db
      ->select_sum('qty')
      ->where('order_code', $order_code)
      ->where('product_code', $product_code)
      ->get('buffer');

      if($rs->num_rows() > 0)
      {
        return $rs->row()->qty;
      }

      return 0;
    }

    public function get_sum_prepared($order_code, $product_code)
    {
      $rs = $this->db
      ->select_sum('qty')
      ->where('order_code', $order_code)
      ->where('product_code', $product_code)
      ->get('prepare');

      if($rs->num_rows() > 0)
      {
        return $rs->row()->qty;
      }

      return 0;
    }


    public function get_sum_qc($order_code, $product_code)
    {
      $rs = $this->db
      ->select_sum('qty')
      ->where('order_code', $order_code)
      ->where('product_code', $product_code)
      ->get('qc');

      if($rs->num_rows() > 0)
      {
        return $rs->row()->qty;
      }

      return 0;
    }


		public function get_order_detail($id)
		{
      $rs = $this->db->where('id', $id)->get('order_details');

      if($rs->num_rows() == 1)
      {
        return $rs->row();
      }

      return NULL;
		}




    public function get_non_count_bill_detail($code)
    {
      $qr  = "SELECT o.product_code, o.product_name, o.style_code, o.qty, ";
      $qr .= "o.cost, o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "o.id_rule, ru.id_policy, o.is_count, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "LEFT JOIN discount_rule AS ru ON ru.id = o.id_rule ";
      $qr .= "WHERE o.order_code = '{$code}' ";
      $qr .= "AND o.is_count = 0 ";

      $rs = $this->db->query($qr);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }


    public function sold(array $ds = array())
    {
      if(!empty($ds))
      {
        return $this->db->insert('order_sold', $ds);
      }

      return FALSE;
    }


		public function get_sum_non_bill_qty($code)
		{
			$rs = $this->db->select_sum('qty')->where('is_count', 0)->where('order_code', $code)->get('order_details');

			return $rs->row()->qty;
		}



		public function drop_sold_data($code)
		{
			return $this->db->where('reference', $code)->delete('order_sold');
		}
}

 ?>
