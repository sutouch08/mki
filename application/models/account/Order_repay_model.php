<?php
class Order_repay_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function add($ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_repay', $ds);
    }

    return FALSE;
  }


  public function add_detail($ds = array())
  {
    if(!empty($ds))
    {
      $this->db->insert('order_repay_detail', $ds);
      return $this->db->insert_id();
    }

    return FALSE;
  }


  public function update($code, $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->where('code', $code)->update('order_repay', $ds);
    }

    return FALSE;
  }


  public function update_detail($id, $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->where('id', $id)->update('order_repay_detail', $ds);
    }

    return FALSE;
  }



  public function get($code)
  {
    $rs = $this->db
    ->select('order_repay.*')
    ->select('customers.name AS customer_name')
    ->from('order_repay')
    ->join('customers', 'order_repay.customer_code = customers.code', 'left')
    ->where('order_repay.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $rs = $this->db->where('repay_code', $code)->get('order_repay_detail');
    if($rs->num_rows() >0)
    {
      return $rs->result();
    }

    return FALSE;
  }

  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('order_repay_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_detail_by_reference($repay_code, $reference)
  {
    $rs = $this->db->where('repay_code', $repay_code)->where('reference', $reference)->get('order_repay_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  //---- สำหรับเช็คว่าเอกสารมีการรับชำระไปแล้วหรือยัง
  public function is_exists_reference($reference)
  {
    $count = $this->db->where('reference', $reference)->where('valid !=', 2)->count_all_results('order_repay_detail');

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_exclude_order($repay_code)
  {
    $rs = $this->db->select('reference')->where('repay_code', $repay_code)->get('order_repay_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('order_repay_detail');
  }


  public function cancle_detail($id)
  {
    return $this->db->set('valid', 2)->where('id', $id)->update('order_repay_detail');
  }


  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('repay_code', $code)->get('order_repay_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('repay.*')
    ->select('customers.name AS customer_name')
    ->select('payment_type.name AS payment_type')
    ->from('order_repay AS repay')
    ->join('customers', 'repay.customer_code = customers.code', 'left')
    ->join('payment_type', 'repay.pay_type = payment_type.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('repay.code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.code', $ds['customer']);
      $this->db->or_like('customers.name', $ds['customer']);
      $this->db->group_end();
    }
    //--- status
    if($ds['status'] !== 'all')
    {
      $this->db->where('repay.status', $ds['status']);
    }

    //-- pay type
    if(!empty($ds['pay_type']))
    {
      $this->db->where('repay.pay_type', $ds['pay_type']);
    }

    //--- document date
    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('repay.date_add >=', from_date($ds['from_date']));
      $this->db->where('repay.date_add <=', to_date($ds['to_date']));
    }

    $this->db->order_by('repay.code', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function count_rows(array $ds = array())
  {
    $this->db
    ->select('repay.*')
    ->select('customers.name AS customer_name')
    ->from('order_repay AS repay')
    ->join('customers', 'repay.customer_code = customers.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('repay.code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.code', $ds['customer']);
      $this->db->or_like('customers.name', $ds['customer']);
      $this->db->group_end();
    }

    //--- status
    if($ds['status'] !== 'all')
    {
      $this->db->where('repay.status', $ds['status']);
    }

    //-- pay type
    if(!empty($ds['pay_type']))
    {
      $this->db->where('repay.pay_type', $ds['pay_type']);
    }

    //--- document date
    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('repay.date_add >=', from_date($ds['from_date']));
      $this->db->where('repay.date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }




  public function change_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('order_repay');
  }



  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM order_repay WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }


  public function get_pay_type_list()
  {
    $rs = $this->db->get('payment_type');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



	public function get_order_invoice(array $ds = array())
	{
		//--- ds is array of ordercode
		if(!empty($ds))
		{
			$this->db->distinct();
			$rs = $this->db
			->select('invoice_code')
			->where('invoice_code IS NOT NULL', NULL, FALSE)
			->where_in('code', $ds)
			->group_by('invoice_code')
			->get('orders');

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}


	public function get_order_date($code)
	{
		$rs = $this->db
		->select('date_add')
		->where('code', $code)
		->get('orders');

		if($rs->num_rows() === 1)
		{
			return $rs->row()->date_add;
		}

		return NULL;
	}



} //--- end class
?>
