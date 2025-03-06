<?php
class Order_round_model extends CI_Model
{
  private $tb = "order_round";

  public function __construct()
  {
    parent::__construct();
  }

  public function get_all()
  {
    $rs = $this->db->where('active', 1)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get($name)
  {
    $rs = $this->db->where('name', $name)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_name($name)
  {
    $rs = $this->db->where('name', $name)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function is_exists($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->where('name', $name)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add($ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function get_name($id)
  {
    $rs = $this->db->select('name')->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function count_rows($ds = array())
  {
    if($ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }


    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list($ds = array(), $limit = 20, $offset = 0)
  {
    if($ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }


    if(!empty($limit))
    {
      $this->db->limit($limit, $offset);
    }

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }

} //--- End class

 ?>
