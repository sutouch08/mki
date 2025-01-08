<?php

function select_vat_group($code = NULL)
{
	$sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/vat_model');
  $options = $CI->vat_model->get_active_data();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


//---- แสดงราคาขาย แยก หรือ รวม vat ตามเงื่อนไขทีส่ตามเงื่อนไขทีส่งมา
//---- โดยราคาที่ส่งเข้ามา จะเป็นราคา รวม vat
//---- แต่จะ return ราคาที่ถอด vat หากเงื่อนไขเป็น E  โดย I = รวม vat E = ไม่รวม vat
function vat_price($price, $option = 'I', $rate = 7)
{
	if($price <= 0)
	{
		return $price;
	}

	if($option === 'I')
	{
		return $price;
	}

	if($rate != 0)
	{
		$re_vat = ($rate + 100)/100;
		return round(($price/$re_vat), 2);
	}

	return $price;	
}

 ?>
