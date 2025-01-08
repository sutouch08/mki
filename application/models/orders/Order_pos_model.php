<?php
class Order_pos_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


	public function get($code)
	{
		$rs = $this->db->where('code', $code)->get('order_pos');
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}



  public function get_not_save_order($pos_id)
  {
    $rs = $this->db->select('code')->where('pos_id', $pos_id)->where('status', 0)->get('order_pos');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_status($code)
  {
    $rs = $this->db->select('status')->where('code', $code)->get('order_pos');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->status;
    }

    return FALSE;
  }

	public function hold_details($order_code)
	{
		return $this->db->set('status', 2)->where('order_code', $order_code)->update('order_pos_detail');
	}


	public function hold_order($code, $ref_note)
	{
		$arr = array(
			'status' => 2,
			'reference_note' => $ref_note
		);

		return $this->db->where('code', $code)->update('order_pos', $arr);
	}



	public function get_hold_orders($pos_id)
	{
		$rs = $this->db
		->select('code, pos_id, reference_note')
		->where('pos_id', $pos_id)
		->where('status', 2)
		->get('order_pos');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function get_detail($id)
	{
		$rs = $this->db->where('id', $id)->get('order_pos_detail');
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}




	public function get_details($order_code)
	{
		$rs = $this->db->where('order_code', $order_code)->get('order_pos_detail');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function get_order_detail_by_product($order_code, $product_code)
	{
		$rs = $this->db->where('order_code', $order_code)->where('product_code', $product_code)->get('order_pos_detail');
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function add(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert('order_pos', $ds);
		}

		return FALSE;
	}


	public function update($code, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('code', $code)->update('order_pos', $ds);
		}

		return FALSE;
	}



	public function add_detail(array $ds = array())
	{
		if(!empty($ds))
		{
			if($this->db->insert('order_pos_detail', $ds))
			{
				return $this->db->insert_id();
			}
		}

		return FALSE;
	}


	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update('order_pos_detail', $ds);
		}

		return FALSE;
	}


	public function delete_detail($id)
	{
		return $this->db->where('id', $id)->delete('order_pos_detail');
	}



  public function count_hold_bills($pos_id)
  {
    return $this->db->where('pos_id', $pos_id)->where('status', 2)->count_all_results('order_pos');
  }


  public function get_hold_bills($pos_id)
  {
    $rs = $this->db->where('pos_id', $pos_id)->where('status', 2)->get('order_pos');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

	public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('order_pos');

		if($rs->num_rows() == 1)
		{
			return $rs->row()->code;
		}

		return  NULL;
  }

} //---- end model
?>
