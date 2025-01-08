<?php
class Box_code_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('box_code', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('box_code', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('box_code');
  }



  public function has_transection($code)
  {
    $count = $this->db->where('code', $code)->count_all_results('qc_box');

    if($count > 0 )
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
		$this->db
		->from('box_code')
		->join('box_size', 'box_code.size_code = box_size.code', 'left');

		if(!empty($ds['code']))
		{
			$this->db->like('box_code.code', $ds['code']);
		}

		if(!empty($ds['box_name']))
		{
			$this->db->like('box_size.name', $ds['box_name']);
		}

		if(!empty($ds['box_type']) && $ds['box_type'] !== 'all')
		{
			$this->db->where('box_size.box_type', $ds['box_type']);
		}


    return $this->db->count_all_results();

  }




	public function get_list(array $ds = array(), $perpage = NULL, $offset = 0)
	{
		$this->db
		->select('bc.*, bs.name AS name, bs.box_width, bs.box_length, bs.box_height, bt.name AS type_name')
		->from('box_code AS bc')
		->join('box_size AS bs', 'bc.size_code = bs.code', 'left')
    ->join('box_type AS bt', 'bs.box_type = bt.code');

		if(!empty($ds['code']))
		{
			$this->db->like('bc.code', $ds['code']);
		}

		if(!empty($ds['box_name']))
		{
			$this->db->like('bs.name', $ds['box_name']);
		}


		if(!empty($ds['box_type']) && $ds['box_type'] !== 'all')
		{
			$this->db->where('bs.box_type', $ds['box_type']);
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




	public function get_box($code)
	{
		$rs = $this->db
		->select('bc.*, bs.name, bs.box_width, bs.box_length, bs.box_height, bt.name AS type_name')
		->from('box_code AS bc')
		->join('box_size AS bs', 'bc.size_code = bs.code', 'left')
    ->join('box_type AS bt', 'bs.box_type = bt.code')
		->where('bc.code', $code)
		->get();

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function get_box_type()
	{
		$rs = $this->db->get('box_type');
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('box_code');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


	public function is_exists($code)
	{
		$rs = $this->db->where('code', $code)->count_all_results('box_code');
		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}

}
?>
