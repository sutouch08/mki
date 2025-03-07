<?php
function select_channels($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/channels_model');
  $channels = $CI->channels_model->get_all();
  if(!empty($channels))
  {
    foreach($channels as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function channels_array()
{
  $ch = [];

  $ci =& get_instance();
  $ci->load->model('masters/channels_model');
  $channels = $ci->channels_model->get_all();

  if( ! empty($channels))
  {
    foreach($channels as $rs)
    {
      $ch[$rs->code] = $rs->name;
    }
  }

  return $ch;
}
 ?>
