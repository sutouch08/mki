<?php

function update_order_total_amount($code)
{
	$CI =& get_instance();
	$CI->load->model('oders/orders_model');
	$order = $CI->orders_model->get($code);
	$amount = 0;

	$amount = $CI->orders_model->get_order_total_amount($code);

	$amount += $order->shipping_fee;
	$amount += $order->service_fee;
	$amount -= $order->bDiscAmount;
	$CI->orders_model->update_order_total_amount($code, $amount);
	$CI->orders_model->recal_order_balance($code);
}


function paymentLabel($payments=NULL)
{
	$sc = "";
	if(!empty($payments))
	{
		foreach($payments as $rs)
		{
			if($rs->valid ==1)
			{
				$sc .= '<button type="button" class="btn btn-sm btn-success" onClick="viewPaymentDetail('.$rs->id.')">';
				$sc .= 'จ่ายเงินแล้ว | '.number($rs->pay_amount,2);
				$sc .= '</button>';
			}
			else
			{
				$sc .= '<button type="button" class="btn btn-sm btn-primary" onClick="viewPaymentDetail('.$rs->id.')">';
				$sc .= 'แจ้งชำระแล้ว | '.number($rs->pay_amount,2);
				$sc .= '</button>';
			}

		}
	}


	return $sc;
}


function paymentExists($order_code)
{
  $CI =& get_instance();
  $CI->load->model('orders/order_payment_model');
  return $CI->order_payment_model->is_exists($order_code);
}


function payment_image_url($order_code)
{
  $CI =& get_instance();
	$link	= base_url().'images/payments/'.$order_code.'.jpg';
  $file = $CI->config->item('image_file_path').'payments/'.$order_code.'.jpg';
	if( ! file_exists($file) )
	{
		$link = FALSE;
	}

	return $link;
}


function getSpace($amount, $length)
{
	$sc = '';
	$i	= strlen($amount);
	$m	= $length - $i;
	while($m > 0 )
	{
		$sc .= '&nbsp;';
		$m--;
	}
	return $sc.$amount;
}


function get_summary($order, $details, $banks)
{
	$useName = getConfig('USE_PRODUCT_NAME');
	$payAmount = 0;
	$orderAmount = 0;
	$discount = 0;
	$totalAmount = 0;

	$orderTxt = '<div>สรุปการสั่งซื้อ</div>';
	$orderTxt .= '<div>Order No : '.$order->code.'</div>';
	$orderTxt .= '##############################<br/>';

	foreach($details as $rs)
	{
		$orderTxt .=   ($useName == 1 ? $rs->product_name : $rs->product_code).'  @'.number($rs->qty).' x '.number($rs->price, 2);
		$orderTxt .= '<br/>';
		$orderAmount += $rs->qty * $rs->price;
		$discount += $rs->discount_amount;
		$totalAmount += $rs->total_amount;
	}

	$orderTxt .= '=================================<br/>';
	$orderTxt .= 'ค่าสินค้ารวม'.getSpace(number( $orderAmount, 2), 24).'<br/>';

	if( ($discount + $order->bDiscAmount) > 0 )
	{
		$orderTxt .= 'ส่วนลดรวม'.getSpace('- '.number( ($discount + $order->bDiscAmount), 2), 27);
		$orderTxt .= '<br/>';
	}

	if( $order->shipping_fee > 0 )
	{
		$orderTxt .= 'ค่าจัดส่ง'.getSpace(number($order->shipping_fee, 2), 31).'<br/>';
	}

	if( $order->service_fee > 0 )
	{
		$orderTxt .= 'อื่นๆ'.getSpace(number($order->service_fee, 2), 36).'<br/>';
	}

	if($order->deposit > 0)
	{
		$orderTxt .= 'ชำระแล้ว'.getSpace('- '.number($order->deposit, 2), 24).'<br/>';
	}

	$payAmount = ($orderAmount + $order->shipping_fee + $order->service_fee) - ($discount + $order->bDiscAmount) - $order->deposit;
	$orderTxt .= 'ยอดชำระ' . getSpace(number( $payAmount, 2), 29).'<br/>';

	$orderTxt .= '=================================<br/>';


	if(!empty($banks))
	{
		$orderTxt .= 'สามารถชำระได้ที่ <br/>';
		$orderTxt .= '##############################<br/>';
		foreach($banks as $rs)
		{
			$orderTxt .= '- '.$rs->bank_name.'<br/>';
			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;สาขา '.$rs->branch.'<br/>';
			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;ชื่อบัญชี '.$rs->acc_name.'<br/>';
			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;เลขที่บัญชี '.$rs->acc_no.'<br/>';
			$orderTxt .= '-------------------------------------------------------------<br/>';
		}
	}

	return $orderTxt;
}


function select_order_role($role = '')
{
	$sc = '';
	$CI =& get_instance();
	$rs = $CI->db->where('active', 1)->order_by('position', 'ASC')->get('order_role');

	if($rs->num_rows() > 0)
	{
		foreach($rs->result() as $ro)
		{
			$sc .= '<option value="'.$ro->code.'" '.is_selected($role, $ro->code).'>'.$ro->name.'</option>';
		}
	}

	return $sc;
}


function role_name($role)
{
	$ds = array(
		'C' => 'ฝากขาย',
		'L'	=> 'ยิม',
		'M'	=> 'ตัดยอดฝากขาย',
		'P'	=> 'สปอนเซอร์',
		'R'	=> 'เบิก',
		'S'	=> 'ขาย',
		'T'	=> 'แปรสภาพ',
		'U'	=> 'อภินันท์',
	);

	return isset($ds[$role]) ? $ds[$role] : NULL;
}


function select_order_tags($name = NULL)
{
	$sc = '';
	$ci =& get_instance();
	$ci->load->model('orders/orders_model');

	$tags = $ci->orders_model->get_tags_list();

	if( ! empty($tags))
	{
		foreach($tags as $rs)
		{
			$sc .= '<option value="'.$rs->name.'" '.is_selected($name, $rs->name).'>'.$rs->name.'</option>';
		}
	}

	return $sc;
}


function select_order_round($name = NULL)
{
	$sc = '';
	$ci =& get_instance();
	$ci->load->model('masters/order_round_model');

	$list = $ci->order_round_model->get_all();

	if( ! empty($list))
	{
		foreach($list as $rs)
		{
			$sc .= '<option value="'.$rs->name.'" '.is_selected($name, $rs->name).'>'.$rs->name.'</option>';
		}
	}

	return $sc;
}

function select_shipping_round($name = NULL)
{
	$sc = '';
	$ci =& get_instance();
	$ci->load->model('masters/shipping_round_model');

	$list = $ci->shipping_round_model->get_all();

	if( ! empty($list))
	{
		foreach($list as $rs)
		{
			$sc .= '<option value="'.$rs->name.'" '.is_selected($name, $rs->name).'>'.$rs->name.'</option>';
		}
	}

	return $sc;
}

 ?>
