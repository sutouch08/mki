<?php
class Profile_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function add($name)
  {
    return $this->db->insert('profile', array('name' => $name));
  }




  public function update($id, $name)
  {
    return $this->db->where('id', $id)->update('profile', array('name' => $name));
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('profile');
  }




  public function is_extsts($name, $id = '')
  {
    if($id !== '')
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->where('name', $name)->get('profile');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }





  public function count_members($id)
  {
    $this->db->select('id');
    $this->db->where('id_profile', $id);
    $rs = $this->db->get('user');
    return $rs->num_rows();
  }





  public function get_profile($id)
  {
    $rs = $this->db->where('id', $id)->get('profile');
    return $rs->row();
  }



	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
    $this->db->where('id >', 0);
    
		if(!empty($ds['name']) && $ds['name'] != "")
		{
			$this->db->like('name', $ds['name']);
		}

		$rs = $this->db->order_by('name', 'DESC')->limit($perpage, $offset)->get('profile');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_profiles()
  {
		$rs = $this->db->where('id >', 0)->order_by('name', 'ASC')->get('profile');

		if($rs->num_rows() > 0)
		{
			return  $rs->result();
		}

		return NULL;
  }



	public function count_rows(array $ds = array())
	{
		if(!empty($ds['name']) && $ds['name'] != "")
		{
			$this->db->like('name', $ds['name']);
		}

		return $this->db->where('id >', 0)->count_all_results('profile');
	}


} //--- End class


 ?>
