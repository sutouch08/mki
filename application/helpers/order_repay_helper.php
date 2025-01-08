<?php
function select_payment_type($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('account/order_repay_model');
  $list = $ci->order_repay_model->get_pay_type_list();
  $sc = "";
  if(!empty($list))
  {
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


//--- object
function get_order_in($details)
{
	$arr = array();
	if(!empty($details))
	{
		foreach($details as $rs)
		{
			$arr[] = $rs->reference;
		}
	}

	return $arr;
}

//---- for print receipt
function parse_reference($ds)
{
	$ref = "";
	if(!empty($ds))
	{
		$i = 1;
		foreach($ds as $rs)
		{
			$ref .= $i === 1 ? $rs->invoice_code : ", {$rs->invoice_code}";
			$i++;
		}
	}

	return $ref;
}

 ?>
