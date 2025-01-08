<?php
class Box_size_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('box_size', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('box_size', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('box_size');
  }



  public function has_transection($code)
  {
    $count = $this->db->where('size_code', $code)->count_all_results('box_code');

    if($count > 0 )
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
		if(!empty($ds['code']))
		{
			$this->db->like('name', $ds['code']);
		}

		if(!empty($ds['box_name']))
		{
			$this->db->like('name', $ds['box_name']);
		}

		if(!empty($ds['box_width']))
		{
			$this->db->like('box_width', $ds['box_width']);
		}

		if(!empty($ds['box_length']))
		{
			$this->db->like('box_width', $ds['box_width']);
		}

		if(!empty($ds['box_height']))
		{
			$this->db->like('box_height', $ds['box_height']);
		}

		if(!empty($ds['box_type']) && $ds['box_type'] !== 'all')
		{
			$this->db->where('box_type', $ds['box_type']);
		}


    return $this->db->count_all_results('box_size');

  }




	public function get_list(array $ds = array(), $perpage = NULL, $offset = 0)
	{
		$this->db
		->select('b.*, bt.name AS type_name')
		->from('box_size AS b')
		->join('box_type AS bt', 'b.box_type = bt.code', 'left');

		if(!empty($ds['code']))
		{
			$this->db->like('b.code', $ds['code']);
		}

		if(!empty($ds['box_name']))
		{
			$this->db->like('b.name', $ds['box_name']);
		}

		if(!empty($ds['box_width']))
		{
			$this->db->like('b.box_width', $ds['box_width']);
		}

		if(!empty($ds['box_length']))
		{
			$this->db->like('b.box_width', $ds['box_width']);
		}

		if(!empty($ds['box_height']))
		{
			$this->db->like('b.box_height', $ds['box_height']);
		}

		if(!empty($ds['box_type']) && $ds['box_type'] !== 'all')
		{
			$this->db->where('b.box_type', $ds['box_type']);
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
    $rs = $this->db->where('code', $code)->get('box_size');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }

}
?>
