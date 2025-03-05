<?php
class Customers_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }




  public function get_credit($code)
  {
    $rs = $this->db->select('balance')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->balance;
    }

    return 0.00;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('customers', $ds);
    }

    return FALSE;
  }


  //--- change credit line
  public function update_credit($code, $amount)
  {
    $arr = array(
      'amount' => $amount,
      'user_upd' => get_cookie('uname')
    );

    $rs = $this->db->where('code', $code)->update('customers', $arr);
    if($rs)
    {
      return $this->update_balance($code);
    }

    return FALSE;
  }


  public function update_balance($code)
  {
    return $this->db->set('balance', 'amount - used', FALSE)->where('code', $code)->update('customers');
  }


  public function update_used($code, $used)
  {
    $rs = $this->db->set('used', "used + {$used}", FALSE)->where('code', $code)->update('customers');
    if($rs)
    {
      return $this->update_balance($code);
    }

    return FALSE;
  }



  public function get_credit_balance($code)
  {
    $rs = $this->db->select('balance')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->balance;
    }

    return 0;
  }


  public function get_credit_used($code)
  {
    $rs = $this->db->select('used')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->used;
    }

    return 0;
  }


  public function get_credit_amount($code)
  {
    $rs = $this->db->select('amount')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return FALSE;
  }


  public function has_credit($code)
  {
    $rs = $this->db
    ->where('code', $code)
    ->where('amount >', 0, FALSE)
    ->where('credit_term >', 0, FALSE)
    ->get('customers');

    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('customers', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('customers');
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( isset($ds['group']) && $ds['group'] != 'all')
    {
      $this->db->where('group_code', $ds['group']);
    }

    if( isset($ds['kind']) && $ds['kind'] != 'all')
    {
      $this->db->where('kind_code', $ds['kind']);
    }

    if( isset($ds['type']) && $ds['type'] != 'all')
    {
      $this->db->where('type_code', $ds['type']);
    }

    if( isset($ds['class']) && $ds['class'] != 'all')
    {
      $this->db->where('class_code', $ds['class']);
    }

    if( isset($ds['area']) && $ds['area'] != 'all')
    {
      $this->db->where('area_code', $ds['area']);
    }

    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }


    return $this->db->count_all_results('customers');

  }




  public function get($code)
  {
    $rs = $this->db
    ->select('c.*, s.name AS sale_name')
    ->from('customers AS c')
    ->join('saleman AS s', 'c.sale_code = s.code', 'left')
    ->where('c.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


	public function get_bill_name($code)
	{
		$rs = $this->db->select('customer_name')->where('customer_code', $code)->get('address_bill_to');

		if($rs->num_rows() == 1)
		{
			return $rs->row()->customer_name;
		}

		return NULL;
	}


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( isset($ds['group']) && $ds['group'] != 'all')
    {
      $this->db->where('group_code', $ds['group']);
    }

    if( isset($ds['kind']) && $ds['kind'] != 'all')
    {
      $this->db->where('kind_code', $ds['kind']);
    }

    if( isset($ds['type']) && $ds['type'] != 'all')
    {
      $this->db->where('type_code', $ds['type']);
    }

    if( isset($ds['class']) && $ds['class'] != 'all')
    {
      $this->db->where('class_code', $ds['class']);
    }

    if( isset($ds['area']) && $ds['area'] != 'all')
    {
      $this->db->where('area_code', $ds['area']);
    }

    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    $rs = $this->db
    ->order_by('code', 'ASC')
    ->limit($perpage, $offset)
    ->get('customers');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('customers');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('customers');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get_sale_code($code)
  {
    $rs = $this->db->select('sale_code')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->sale_code;
    }

    return NULL;
  }


  public function search($txt)
  {
    $qr = "SELECT code FROM customers WHERE code LIKE '%".$txt."%' OR name LIKE '%".$txt."%'";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    else
    {
      return array();
    }

  }



  public function getSlp()
  {
    $rs = $this->db
    ->where('Active', 1)
    ->get('saleman');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


	public function get_saleman($customer_code)
	{
		$rs = $this->db
		->select('sale.code, sale.name')
		->from('customers AS cust')
		->join('saleman AS sale', 'cust.sale_code = sale.code', 'left')
		->where('cust.code', $customer_code)
		->where('cust.sale_code IS NOT NULL', NULL, FALSE)
		->get();

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}



	public function get_attribute($code)
	{
		$rs = $this->db
		->select('c.*')
		->select('cg.name AS group_name')
		->select('ck.name AS kind_name')
		->select('ct.name AS type_name')
		->select('cc.name AS class_name')
		->select('ca.name AS area_name')
		->select('sa.name AS sale_name')
		->from('customers AS c')
		->join('customer_group AS cg', 'c.group_code = cg.code', 'left')
		->join('customer_kind AS ck', 'c.kind_code = ck.code', 'left')
		->join('customer_type AS ct', 'c.type_code = ct.code', 'left')
		->join('customer_class AS cc', 'c.class_code = cc.code', 'left')
		->join('customer_area AS ca', 'c.area_code = ca.code', 'left')
		->join('saleman AS sa', 'c.sale_code = sa.code', 'left')
		->where('c.code', $code)
		->get();

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function is_exists_transection($code)
	{
		$orders = $this->exists_order($code);
		$pos = $this->exists_order_pos($code);
		$sq = $this->exists_quotation($code);
		$shop = $this->exists_shop($code);

		$transection = $orders + $pos + $sq + $shop;

		if($transection > 0)
		{
			return TRUE;
		}

		return FALSE;
	}

	public function exists_order($code)
	{
		return $this->db->where('customer_code', $code)->count_all_results('orders');
	}

	public function exists_order_pos($code)
	{
		return $this->db->where('customer_code', $code)->count_all_results('order_pos');
	}

	public function exists_quotation($code)
	{
		return $this->db->where('customer_code', $code)->count_all_results('order_quotation');
	}


	public function exists_shop($code)
	{
		return $this->db->where('customer_code', $code)->count_all_results('shop');
	}



	public function delete_address($code)
	{
		$this->db->trans_begin();

		$bill_to = $this->db->where('customer_code', $code)->delete('address_bill_to');
		$ship_to = $this->db->where('customer_code', $code)->delete('address_ship_to');

		if($bill_to && $ship_to)
		{
			$this->db->trans_commit();
			return TRUE;
		}
		else
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		return TRUE;
	}


  public function get_max_no()
  {
    $rs = $this->db
    ->select_max('run_no')
    ->get('customers');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->run_no;
    }

    return 0;
  }
}
?>
