<?php
class Order_credit_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('oc.*')
    ->select('cs.name AS customer_name')
    ->select('od.customer_ref')
    ->from('order_credit AS oc')
    ->join('customers AS cs', 'oc.customer_code = cs.code', 'left')
    ->join('orders AS od', 'oc.order_code = od.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('oc.order_code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('cs.code', $ds['customer']);
      $this->db->or_like('cs.name', $ds['customer']);
      $this->db->or_like('od.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('oc.delivery_date >=', from_date($ds['from_date']));
      $this->db->where('oc.delivery_date <=', to_date($ds['to_date']));
    }

    if(!empty($ds['due_from_date']) && !empty($ds['due_to_date']))
    {
      $this->db->where('oc.due_date >=', from_date($ds['due_from_date']));
      $this->db->where('oc.due_date <=', to_date($ds['due_to_date']));
    }

    if($ds['valid'] != 2)
    {
			if($ds['valid'] == 3)
			{
				$this->db->where('oc.valid', 0);
				$this->db->where('oc.over_due_date <', date('Y-m-d'));
			}
			else
			{
				$this->db->where('oc.valid', $ds['valid']);
			}
    }

    $this->db->order_by('oc.order_code', 'DESC');

    if(!empty($perpage))
    {
      $offset = empty($offset) ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

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
    ->from('order_credit AS oc')
    ->join('customers AS cs', 'oc.customer_code = cs.code', 'left')
    ->join('orders AS od', 'oc.order_code = od.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('oc.order_code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('cs.code', $ds['customer']);
      $this->db->or_like('cs.name', $ds['customer']);
      $this->db->or_like('od.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('oc.delivery_date >=', from_date($ds['from_date']));
      $this->db->where('oc.delivery_date <=', to_date($ds['to_date']));
    }

    if(!empty($ds['due_from_date']) && !empty($ds['due_to_date']))
    {
      $this->db->where('oc.due_date >=', from_date($ds['due_from_date']));
      $this->db->where('oc.due_date <=', to_date($ds['due_to_date']));
    }

		if($ds['valid'] != 2)
    {
			if($ds['valid'] == 3)
			{
				$this->db->where('oc.valid', 0);
				$this->db->where('oc.over_due_date <', date('Y-m-d'));
			}
			else
			{
				$this->db->where('oc.valid', $ds['valid']);
			}
    }

    return $this->db->count_all_results();
  }



  public function get($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get('order_credit');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get('order_credit');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_unvalid_order($customer_code, $exclude)
  {
    $this->db
    ->where('customer_code', $customer_code)
    ->where('valid', 0);
    if(!empty($exclude))
    {
      $this->db->where_not_in('order_code', $exclude);
    }

    $rs = $this->db->get('order_credit');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //---- ตั้งหนี้
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_credit', $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('order_code', $code)->update('order_credit', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('order_code', $code)->delete('order_credit');
  }


  public function is_exists($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_credit');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function pay_order($code, $amount)
  {
    $rs = $this->db->set("paid", "paid + {$amount}", FALSE)->where('order_code', $code)->update('order_credit');
    if($rs)
    {
      $this->recal_balance($code);
      return TRUE;
    }

    return FALSE;
  }


  public function unpay_order($code, $amount)
  {
    $rs = $this->db->set('paid', "paid - {$amount}", FALSE)->where('order_code', $code)->update('order_credit');
    if($rs)
    {
      $this->recal_balance($code);
      return TRUE;
    }

    return FALSE;
  }


  public function recal_balance($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_credit');

    if($rs->num_rows() > 0)
    {
      $balance = $rs->row()->amount - $rs->row()->paid;

      if($balance > 0)
      {
        $this->db->query("UPDATE order_credit SET balance = (amount - paid), valid = 0 WHERE order_code = '{$code}'");
      }
      else
      {
        $this->db->query("UPDATE order_credit SET balance = 0, valid = 1 WHERE order_code = '{$code}'");
      }
    }
  }


	public function update_amount($code, $amount)
	{
		$rs = "UPDATE order_credit SET amount = amount + {$amount} WHERE order_code = '{$code}'";
		return $this->db->query($rs);
	}


	public function get_order_balance($code)
	{
		$rs = $this->db->select('balance')->where('order_code', $code)->get('order_credit');
		if($rs->num_rows() === 1)
		{
			return $rs->row()->balance;
		}

		return FALSE;
	}


	public function is_valid($code)
	{
		$rs = $this->db->where('order_code', $code)->where('valid', 1)->count_all_results('order_credit');

		if($rs === 1)
		{
			return TRUE;
		}

		return FALSE;
	}


} //--- end class

 ?>
