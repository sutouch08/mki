<?php
class Payment_methods_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('payment_method', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('payment_method', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('payment_method');
  }


  public function count_rows(array $ds = array())
  {
		if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(!empty($ds['role']) && $ds['role'] !== 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    return $this->db->count_all_results('payment_method');

  }


  public function get_active_list()
  {
    $rs = $this->db
		->select('pm.*')
		->select('pr.name AS role_name')
		->from('payment_method AS pm')
		->join('payment_role AS pr', 'pm.role = pr.id', 'left')
    ->where('pm.active', 1)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_list(array $ds = array(), $perpage = NULL, $offset = 0)
  {
		$this->db
		->select('pm.*')
		->select('pr.name AS role_name')
		->from('payment_method AS pm')
		->join('payment_role AS pr', 'pm.role = pr.id', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('pm.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('pm.name', $ds['name']);
    }

    if(!empty($ds['role']) && $ds['role'] !== 'all')
    {
      $this->db->where('pm.role', $ds['role']);
    }


    if(!empty($perpage))
    {
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
  }



	public function get_data()
	{
		$rs = $this->db->get('payment_method');
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



  public function get_payment_methods($code)
  {
    $rs = $this->db->where('code', $code)->get('payment_method');
    return $rs->row();
  }



  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('payment_method');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_default()
  {
    $rs = $this->db->where('is_default', 1)->get('payment_method');
    if($rs->num_rows() == 1)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_role($code)
  {
    $rs = $this->db->select('role')->where('code', $code)->get('payment_method');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->role;
    }

    return FALSE;
  }



  public function get_role_list()
  {
    $rs = $this->db->get('payment_role');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }








  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('payment_method');

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

    $rs = $this->db->where('name', $name)->get('payment_method');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('payment_method');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return FALSE;
  }



	public function get_payment_name_list($ds = array())
	{
		if(!empty($ds))
		{
			$rs = $this->db->where_in('code', $ds)->get('payment_method');
			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}


  public function has_term($code)
  {
    $rs = $this->db->where('code', $code)->where('has_term', 1)->get('payment_method');
    if($rs->num_rows() == 1)
    {
      return TRUE;
    }

    return FALSE;
  }


	public function has_default()
	{
		$rs = $this->db->where('is_default', 1)->count_all_results('payment_method');
		if($rs > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}


	public function set_default($code)
	{
		if($this->db->set('is_default', 0)->where('is_default', 1)->update('payment_method'))
		{
			if($this->db->set('is_default', 1)->where('code', $code)->update('payment_method'))
			{
				return TRUE;
			}
		}

		return FALSE;
	}


	public function get_pos_payment_list()
	{
		$qr  = "SELECT code, name, has_term, role, acc_id ";
		$qr .= "FROM payment_method ";
		$qr .= "WHERE has_term = 0 ";
		$qr .= "ORDER BY FIELD(role, '2', '3', '5', '1', '4') ASC, name ASC";

		$rs = $this->db->query($qr);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

}
?>
