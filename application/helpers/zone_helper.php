<?php
function zone_in($txt)
{
  $sc = array('0');
  $CI =& get_instance();
  $CI->load->model('inventory/zone_model');
  $zone = $CI->zone_model->search($txt);
  if(!empty($zone))
  {
    foreach($zone as $rs)
    {
      $sc[] = $rs->code;
    }
  }

  return $sc;
}



function select_zone($warehouse = NULL, $se = NULL)
{
	$sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/zone_model');
  $options = $CI->zone_model->get_zone($warehouse);

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($se, $rs->code).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_sell_zone($se = NULL)
{
	$sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/zone_model');
  $options = $CI->zone_model->get_sell_zone();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($se, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_consign_zone($se = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('masters/zone_model');
  $options = $ci->zone_model->get_consign_zone();

  if( ! empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($se, $rs->code).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $sc;
}


function zone_name_array()
{
  $ds = [];
  $ci =& get_instance();
  $ci->load->model('masters/zone_model');
  $list = $ci->zone_model->get_all_zone();

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
