<?php
class Pos_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  //--- add new zone (use with sync only)
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('shop_pos', $ds);
    }

    return FALSE;
  }


  //--- update zone with sync only
  public function update($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('shop_pos', $ds);
    }

    return FALSE;
  }

	public function get($id)
	{
		$rs = $this->db->where('id', $id)->get('shop_pos');
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}

	public function get_by_code($code = NULL)
	{
		if(! is_null($code))
		{
			$rs = $this->db
			->select('pos.*, shop.code AS shop_code, shop.name AS shop_name, shop.zone_code')
			->from('shop_pos AS pos')
			->join('shop', 'pos.shop_id = shop.id', 'left')
			->where('pos.code', $code)
			->get();

			if($rs->num_rows() === 1)
			{
				return $rs->row();
			}
		}

		return NULL;
	}



	public function get_shop_pos($shop_id)
	{
		$rs = $this->db->where('shop_id', $shop_id)->get('shop_pos');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_active_pos_list()
	{
		$this->db
		->select('pos.*')
		->select('shop.code AS shop_code, shop.name AS shop_name')
		->select('zone.name AS zone_name')
		->from('shop_pos AS pos')
		->join('shop', 'pos.shop_id = shop.id', 'left')
		->join('zone', 'shop.zone_code = zone.code', 'left')
		->where('shop.active', 1)
		->where('pos.active', 1)
		->order_by('shop.code', 'ASC');

		$rs = $this->db->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

	public function get_pos($id)
	{
		$this->db
		->select('pos.*')
		->select('shop.code AS shop_code, shop.name AS shop_name')
		->select('customers.code AS customer_code, customers.name AS customer_name')
		->select('shop.zone_code, zone.name AS zone_name, zone.warehouse_code')
		->from('shop_pos AS pos')
		->join('shop', 'pos.shop_id = shop.id', 'left')
		->join('zone', 'shop.zone_code = zone.code', 'left')
		->join('customers', 'shop.customer_code = customers.code', 'left')
		->where('shop.active', 1)
		->where('pos.active', 1)
		->where('pos.id', $id);

		$rs = $this->db->get();

		if($rs->num_rows() > 0)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function get_customer_shop_list($shop_id)
	{
		$rs = $this->db
		->select('customers.code, customers.name')
		->from('customers')
		->join('customer_shop', 'customers.code = customer_shop.customer_code')
		->where('customer_shop.shop_id', $shop_id)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return  NULL;
	}



  //---- delete zone  must use only mistake on sap and delete zone in SAP already
  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('shop_pos');
  }


	//---- checl transection
	public function has_transection($code)
	{
		//---- order


		return FALSE;
	}



  public function is_exists_code($code, $old_code = NULL)
  {
    $this->db->where('code', $code);

    if(! is_null($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->count_all_results('shop_pos');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $code = NULL)
  {
    $this->db->where('name', $name);

    if(! is_null($code))
    {
      $this->db->where('code !=', $code);
    }

    $rs = $this->db->count_all_results('shop_pos');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



	public function is_exists_prefix($prefix, $code = NULL)
	{
		$this->db->where('prefix', $prefix);

		if(! is_null($code))
		{
			$this->db->where('code !=', $code);
		}

		$rs = $this->db->count_all_results('shop_pos');

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;

	}



  public function count_rows(array $ds = array())
  {
  	if(! empty($ds))
		{
			$this->db
			->from('shop_pos AS pos')
			->join('shop', 'pos.shop_id = shop.id', 'left');

			if($ds['code'] !== '')
			{
				$this->db->like('pos.code', $ds['code']);
			}

			if($ds['name'] !== '')
			{
				$this->db->like('pos.name', $ds['name']);
			}

			if($ds['pos_code'] !== '')
			{
				$this->db->like('pos.pos_code', $ds['pos_code']);
			}


			if($ds['pos_no'] !== '')
			{
				$this->db->like('pos.pos_no', $ds['pos_no']);
			}

			if($ds['shop'] !== '')
			{
				$this->db
				->group_start()
				->like('shop.code', $ds['shop'])
				->or_like('shop.name', $ds['shop'])
				->group_end();
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('pos.active', $ds['status']);
			}

			return $this->db->count_all_results();
		}

		return 0;
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
		if(! empty($ds))
		{
			$this->db
			->select('pos.*, shop.code AS shop_code, shop.name AS shop_name')
			->from('shop_pos AS pos')
			->join('shop', 'pos.shop_id = shop.id', 'left');

			if($ds['code'] !== '')
			{
				$this->db->like('pos.code', $ds['code']);
			}

			if($ds['name'] !== '')
			{
				$this->db->like('pos.name', $ds['name']);
			}

			if($ds['pos_code'] !== '')
			{
				$this->db->like('pos.pos_code', $ds['pos_code']);
			}


			if($ds['pos_no'] !== '')
			{
				$this->db->like('pos.pos_no', $ds['pos_no']);
			}

			if($ds['shop'] !== '')
			{
				$this->db
				->group_start()
				->like('shop.code', $ds['shop'])
				->or_like('shop.name', $ds['shop'])
				->group_end();
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('pos.active', $ds['status']);
			}

			$this->db->limit($perpage, $offset);

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;

  }


} //--- end class

 ?>
