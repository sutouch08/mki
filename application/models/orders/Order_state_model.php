<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_state_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }



  public function add_state(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_state_change', $ds);
    }

    return FALSE;
  }



  public function get_order_state($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_state_change');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  public function get_first_state_timestamp($code)
  {
    $rs = $this->db
    ->select('date_upd')
    ->where('order_code', $code)    
    ->order_by('id', 'ASC')
    ->limit(1)
    ->get('order_state_change');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->date_upd;
    }

    return NULL;
  }

}//--- end class
?>
