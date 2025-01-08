<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Delivery_slip_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

	public function count_rows(array $ds = array())
  {
    $this->db
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('payment_method', 'payment_method.code = orders.payment_code', 'left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', 8);

		if($ds['print_status'] !== 'all')
		{
			$this->db->where('orders.printed', $ds['print_status']);
		}

    if(!empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

		if(!empty($ds['payment']) && $ds['payment'] != 'all')
		{
			$this->db->where('orders.payment_code', $ds['payment']);
		}


		if(!empty($ds['channels']) && $ds['channels'] != 'all')
		{
			$this->db->where('orders.channels_code', $ds['channels']);
		}

		if($ds['sender'] != 'all')
		{
			$this->db->where('orders.sender_id', $ds['sender']);
		}

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {

    $this->db->select('orders.*')
    ->select('channels.name AS channels_name')
    ->select('payment_method.name AS payment_name, payment_method.role AS payment_role')
    ->select('customers.name AS customer_name')
		->select('address_sender.name AS sender_name')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('payment_method', 'payment_method.code = orders.payment_code', 'left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
		->join('address_sender', 'orders.sender_id = address_sender.id', 'left')
    ->where('orders.state', 8);

		if($ds['print_status'] !== 'all')
		{
			$this->db->where('orders.printed', $ds['print_status']);
		}

    if(!empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

		if(!empty($ds['payment']) && $ds['payment'] != 'all')
		{
			$this->db->where('orders.payment_code', $ds['payment']);
		}


		if(!empty($ds['channels']) && $ds['channels'] != 'all')
		{
			$this->db->where('orders.channels_code', $ds['channels']);
		}

		if($ds['sender'] != 'all')
		{
			$this->db->where('orders.sender_id', $ds['sender']);
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


	public function update_status(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->set('printed', 1)->where_in('code', $ds)->update('orders');
		}

		return FALSE;
	}

}//-- end class
?>
