<?php
class Sales_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

	public function get_sum_item_sales_by_date_upd(array $ds = array())
  {
    if(!empty($ds))
    {
			$this->db
			->select('product_code, product_name, price_ex AS price')
			->select_sum('qty')
			->select_sum('total_amount_ex', 'amount')
			->where_in('role', array('S', 'O'))
			->where('date_upd >=', $ds['fromDate'])
			->where('date_upd <=', $ds['toDate']);

			if(empty($ds['allProduct']) && !empty($ds['pdFrom']) && !empty($ds['pdTo']))
			{
				$this->db->where('product_code >=', $ds['pdFrom'])->where('product_code <=', $ds['pdTo']);
			}

			$this->db->group_by('product_code');

			if(!empty($ds['orderBy']))
			{
				$this->db->order_by($ds['orderBy'], 'DESC');
			}
			else
			{
				$this->db->order_by('amount', 'DESC');
			}

      $rs = $this->db->get('order_sold');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }

    return FALSE;
  }



	///---- sale by customer
	public function get_sum_customer_sales_by_date_upd(array $ds = array())
  {
    if(!empty($ds))
    {
			$this->db
			->select('customer_code, customer_name')
			->select_sum('qty')
			->select_sum('total_amount_ex', 'amount')
			->where_in('role', array('S', 'O'))
			->where('date_upd >=', $ds['fromDate'])
			->where('date_upd <=', $ds['toDate']);

			if(empty($ds['allCustomer']) && !empty($ds['cusFrom']) && !empty($ds['cusTo']))
			{
				$this->db->where('customer_code >=', $ds['cusFrom'])->where('customer_code <=', $ds['cusTo']);
			}

			$this->db->group_by('customer_code');

			if(!empty($ds['orderBy']))
			{
				$this->db->order_by($ds['orderBy'], 'DESC');
			}
			else
			{
				$this->db->order_by('amount', 'DESC');
			}

      $rs = $this->db->get('order_sold');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }

    return FALSE;
  }


  public function get_sum_wm($from_date, $to_date)
  {
    $rs = $this->db->select_sum('qty')
    ->select_sum('total_amount_ex', 'amount')
    ->where('role', 'M')
    ->where('date_upd >=', from_date($from_date))
    ->where('date_upd <=', to_date($to_date))
    ->get('order_sold');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_sum_sales_by_channels($channels, $from_date, $to_date)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->select_sum('total_amount_ex', 'amount')
    ->where_in('role', ['S', 'O'])
    ->where('channels_code', $channels)
    ->where('date_upd >=', from_date($from_date))
    ->where('date_upd <=', to_date($to_date))
    ->get('order_sold');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

	// ///---- sale by channels
	// public function get_sum_channels_sales_by_date_upd(array $ds = array())
  // {
  //   if( ! empty($ds))
  //   {
	// 		$this->db
	// 		->select('channels_code AS code, channels_name AS name')
	// 		->select_sum('qty')
	// 		->select_sum('total_amount_ex', 'amount')
	// 		->where_in('role', array('S', 'O', 'M'))
	// 		->where('date_upd >=', $ds['fromDate'])
	// 		->where('date_upd <=', $ds['toDate']);
  //
	// 		if(empty($ds['allChannels']) && !empty($ds['channels']))
	// 		{
	// 			$this->db->where_in('channels_code', $ds['channels']);
	// 		}
  //
	// 		$this->db->group_by('channels_code');
  //
	// 		if(!empty($ds['orderBy']))
	// 		{
	// 			$this->db->order_by($ds['orderBy'], 'DESC');
	// 		}
	// 		else
	// 		{
	// 			$this->db->order_by('amount', 'DESC');
	// 		}
  //
  //     $rs = $this->db->get('order_sold');
  //
  //     if($rs->num_rows() > 0)
  //     {
  //       return $rs->result();
  //     }
  //   }
  //
  //   return NULL;
  // }




  public function get_order_sold_by_date_upd(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->select('sold.date_add,sold.date_upd, sold.reference, ch.name AS channels, pm.name AS payment');
      $this->db->select('cus.name AS customer_name, sold.customer_ref');
      $this->db->select('sold.product_code, sold.product_name');
      $this->db->select('sold.price, sold.sell, sold.qty, sold.discount_label, sold.discount_amount, sold.total_amount');
      $this->db->select('credit.paid, credit.balance');
      $this->db->from('order_sold AS sold');
      $this->db->join('channels AS ch', 'sold.channels_code = ch.code', 'left');
      $this->db->join('payment_method AS pm', 'sold.payment_code = pm.code', 'left');
      $this->db->join('customers AS cus', 'sold.customer_code = cus.code', 'left');
      $this->db->join('order_credit AS credit', 'sold.reference = credit.order_code', 'left');
      $this->db->where_in('sold.role', array('S', 'O'));
      $this->db->where('sold.date_upd >=', $ds['fromDate']);
      $this->db->where('sold.date_upd <=', $ds['toDate']);

      if(empty($ds['allCustomer']) && !empty($ds['cusFrom']) && !empty($ds['cusTo']) )
      {
        $this->db->where('sold.customer_code >=', $ds['cusFrom']);
        $this->db->where('sold.customer_code <=', $ds['cusTo']);
      }

      if(empty($ds['allProduct']) && !empty($ds['pdFrom']) && !empty($ds['pdTo']))
      {
        $this->db->where('sold.product_code >=', $ds['pdFrom']);
        $this->db->where('sold.product_code <=', $ds['pdTo']);
      }

      $this->db->order_by('sold.customer_code', 'ASC');
      $this->db->order_by('sold.reference', 'ASC');

      $rs = $this->db->get();
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }

    return FALSE;
  }


  public function get_order_sold_by_customer_and_payment(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->select('sold.date_add,sold.date_upd, sold.reference, ch.name AS channels, pm.name AS payment');
      $this->db->select('cus.name AS customer_name, sold.customer_ref');
      $this->db->select_sum('sold.total_amount', 'total_amount');
      $this->db->select('credit.paid, credit.balance');
      $this->db->from('order_sold AS sold');
      $this->db->join('channels AS ch', 'sold.channels_code = ch.code', 'left');
      $this->db->join('payment_method AS pm', 'sold.payment_code = pm.code', 'left');
      $this->db->join('customers AS cus', 'sold.customer_code = cus.code', 'left');
      $this->db->join('order_credit AS credit', 'sold.reference = credit.order_code', 'left');
      $this->db->where_in('sold.role', array('S', 'O'));
      $this->db->where('sold.date_upd >=', $ds['fromDate']);
      $this->db->where('sold.date_upd <=', $ds['toDate']);

      if(empty($ds['allCustomer']) && !empty($ds['cusFrom']) && !empty($ds['cusTo']) )
      {
        $this->db->where('sold.customer_code >=', $ds['cusFrom']);
        $this->db->where('sold.customer_code <=', $ds['cusTo']);
      }

      if( ! empty($ds['channels']) && $ds['channels'] != 'all')
      {
        $this->db->where('sold.channels_code', $ds['channels']);
      }

      if( ! empty($ds['payments']) && $ds['payments'] != 'all')
      {
        $this->db->where('sold.payment_code', $ds['payments']);
      }

      if(isset($ds['options']) && $ds['options'] != 'all')
      {
        $this->db->where('credit.valid', $ds['options']);
      }

      $this->db->group_by('sold.reference');

      $this->db->order_by('sold.customer_code', 'ASC');
      $this->db->order_by('sold.reference', 'ASC');

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }

    return FALSE;
  }


	//--- รายงานวิเคราะห์ขายแบบละเอีด
	public function get_sold_details($ds = array())
	{
		if(!empty($ds))
		{
			if($ds['role'] != 'all')
			{
				$this->db->where('role', $ds['role']);
			}
			else
			{
				$this->db->where_in('role', array('S', 'O', 'M'));
			}

			$this->db
			->where('date_add >=', $ds['fromDate'])
			->where('date_add <=', $ds['toDate'])
			->order_by('date_add', 'ASC')
			->order_by('reference', 'ASC')
			->order_by('is_count', 'DESC');

			$rs = $this->db->get('order_sold');

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}


} //--- end class
?>
