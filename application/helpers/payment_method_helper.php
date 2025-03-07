<?php
function select_payment_method($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/payment_methods_model');
  $payments = $CI->payment_methods_model->get_active_list();

  if(!empty($payments))
  {
    foreach($payments as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" data-role="'.$rs->role.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_payment_role($id='')
{
  $sc = "";
  $CI =& get_instance();
  $CI->load->model('masters/payment_methods_model');
  $payments = $CI->payment_methods_model->get_role_list();
  if(!empty($payments))
  {
    foreach($payments as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_pos_payment_method($code = "")
{
	$sc = "";
	$CI =& get_instance();
	$CI->load->model('masters/payment_methods_model');
	$payments = $CI->payment_methods_model->get_pos_payment_list();

	if(!empty($payments))
	{
		foreach($payments as $rs)
		{
			$sc .= '<option value="'.$rs->code.'" data-acc="'.$rs->acc_id.'" data-role="'.$rs->role.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
		}
	}

	return $sc;
}

function payment_method_array()
{
  $sc = array();
  $ci =& get_instance();

  $ci->load->model('masters/payment_methods_model');
  $list = $ci->payment_methods_model->get_list();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $sc[$rs->code] = $rs;
    }
  }

  return $sc;
}


function payment_array()
{
  $pm = [];

  $ci =& get_instance();
  $ci->load->model('masters/payment_methods_model');
  $list = $ci->payment_methods_model->get_all();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $pm[$rs->code] = $rs->name;
    }
  }

  return $pm;
}

 ?>
