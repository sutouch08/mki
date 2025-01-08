<?php
function select_box_type($code = NULL)
{
  $sc = "";
  $CI =& get_instance();
  $CI->load->model('masters/box_size_model');

  $box_type = $CI->box_size_model->get_box_type();

  if(!empty($box_type))
  {
    foreach($box_type as $rs)
    {
      $selected = is_selected($rs->code, $code);

      $sc .= "<option value='{$rs->code}' {$selected}>{$rs->name}</option>";
    }

  }

  return $sc;
}


function select_box_size($code = NULL)
{
	$sc = "";
  $CI =& get_instance();
  $CI->load->model('masters/box_size_model');

  $box = $CI->box_size_model->get_list();

  if(!empty($box))
  {
    foreach($box as $rs)
    {
      $selected = is_selected($rs->code, $code);

      $sc .= "<option value='{$rs->code}' {$selected}>{$rs->name}</option>";
    }

  }

  return $sc;
}

 ?>
