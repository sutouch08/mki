<?php
class Order_backlogs_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_data(array $ds = array())
  {
    if(!empty($ds))
		{
			$this->db
			->select('o.date_add, o.code, o.total_amount AS amount')
			->select('c.name AS customer_name')
			->select('ch.name AS channels_name')
			->select('pm.name AS payment_name')
			->select('st.name AS status_name')
			->from('orders AS o')
			->join('customers AS c', 'o.customer_code = c.code', 'left')
			->join('channels AS ch', 'o.channels_code = ch.code', 'left')
			->join('payment_method AS pm', 'o.payment_code = pm.code', 'left')
			->join('order_state AS st', 'o.state = st.state', 'left')
			->where('o.role', 'S')
			->where('o.state <', 8)
			->where('o.is_expired', 0);

			if(empty($ds['allCustomer']) && !empty($ds['fromCustomer']) && !empty($ds['toCustomer']))
			{
				$this->db->where('o.customer_code >=', $ds['fromCustomer']);
				$this->db->where('o.customer_code <=', $ds['toCustomer']);
			}

			if(empty($ds['allDate']) && !empty($ds['fromDate']) && !empty($ds['toDate']))
			{
				$this->db->where('o.date_add >=', from_date($ds['fromDate']));
				$this->db->where('o.date_add <=', to_date($ds['toDate']));
			}

			if(empty($ds['allChannels']) && !empty($ds['channels']))
			{
				$this->db->where_in('o.channels_code', $ds['channels']);
			}

			if(empty($ds['allPayment']) && !empty($ds['payment']))
			{
				$this->db->where_in('o.payment_code', $ds['payment']);
			}

			$this->db->order_by('o.date_add', 'ASC');

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}

			return NULL;
		}

		return NULL;
  }



} //--- end class
 ?>
