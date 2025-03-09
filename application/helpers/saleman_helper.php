<?php

function select_saleman($code = "")
{
  $CI =& get_instance();
  $CI->load->model('masters/saleman_model');
  $result = $CI->saleman_model->get_data();
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function get_saleman_name($code)
{
  $ci =& get_instance();
  $ci->load->model('masters/saleman_model');
  return $ci->saleman_model->get_name($code);
}


function saleman_array()
{
  $ds = [];
  $ci =& get_instance();
  $ci->load->model('masters/saleman_model');
  $list = $ci->saleman_model->get_all();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds[$rs->code] = $rs->name;
    }
  }

  return $ds;
}
 ?>
