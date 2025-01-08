<?php
class Box_type_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


	public function get($code)
	{
		$rs = $this->db->where('code', $code)->get('box_type');
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
      return  $this->db->insert('box_type', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('box_type', $ds);
    }

    return FALSE;
  }




  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('box_type');
  }



	public function has_transection($code)
	{
		$rs = $this->db->where('box_type', $code)->count_all_results('box_size');

		if($rs != 0)
		{
			return TRUE;
		}

		return FALSE;
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

    return $this->db->count_all_results('box_type');

  }




	public function get_list(array $ds = array(), $perpage = NULL, $offset = 0)
	{

		if(!empty($ds['name']))
		{
			$this->db->like('name', $ds['name']);
		}

		if(!empty($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		if(!empty($perpage))
		{
			$this->db->limit($perpage, $offset);
		}

		$rs = $this->db->get('box_type');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



  public function is_exists($code, $old_code = NULL)
  {
		if(!empty($old_code))
		{
			$this->db->where('code !=', $old_code);
		}

    $rs = $this->db->where('code', $code)->get('box_type');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




}
?>
