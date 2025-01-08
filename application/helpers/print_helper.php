<?php
function doc_type($role)
{
	switch($role)
	{
		case 'S' :
			$content	= "order";
			$title 		= "ใบสั่งขาย";
		break;

		case 'C' :
			$content = "consign";
			$title = "ใบส่งของฝากขาย";
		break;

		case 'N' :
			$content = "consign";
			$title = "ใบส่งของสินค้าฝากขาย";
		break;

		case 'U' :
			$content = "support";
			$title = "ใบเบิกอภินันทนาการ";
		break;

		case 'P' :
			$content = "sponsor";
			$title = "ใบเบิกอภินันทนาการ";
		break;

		case 'T' :
			$content = "transform";
			$title = "ใบเบิกสินค้าเพื่อแปรรูป";
		break;

		case 'L' :
			$content = "lend";
			$title = "ใบยืมสินค้า";
		break;

		case 'R' :
			$content 	= "requisition";
			$title 		= "ใบเบิกสินค้า";
		break;

		default :
			$content = "order";
			$title = "ใบส่งของ";
		break;
	}

	return array("content"=>$content, "title"=>$title);
}






function get_header($order)
{
	$CI =& get_instance();

	//---	เบิกสปอนเซอร์
	if( $order->role == 'P')
	{
		$header	= array(
				"ผู้รับ" => $order->customer_name,
				"วันที่" => thai_date($order->date_add, FALSE, '/'),
				"ผู้เบิก" => $CI->user_model->get_name($order->user),
				"เลขที่" => $order->code,
				"ผู้ทำรายการ" =>  $CI->user_model->get_name($order->user)
			);
	}



	//---	ยิมสินค้า
	else if($order->role == 'L' )
	{
				$header		= array(
								"เลขที่"	=> $order->code,
								"วันที่"	=> thai_date($order->date_add, FALSE, '/'),
								"ผู้ยืม"	=> $order->customer_name,
								"ผู้ทำรายการ" => $CI->user_model->get_name($order->user)
							);
	}


	//---	เบิก หรือ เบิกแปรสภาพ
	else if( $order->role == 'R' || $order->role == 'T' )
	{
		$header		= array(
									"ลูกค้า"	=> $order->customer_name,
									"วันที่"	=> thai_date($order->date_add, FALSE, '/'),
									"ผู้เบิก"	=> $CI->user_model->get_name($order->user),
									"เลขที่"	=> $order->code
									);
	}

	//---	เบิกอภินันท์
	else if( $order->role == 'U')
	{
		$header	= array(
									"ผู้เบิก"	=> $order->customer_name,
									"วันที่"	=> thai_date($order->date_add, FALSE, '/'),
									"ผู้ทำรายการ"	=> $CI->user_model->get_name($order->user),
									"เลขที่"	=> $order->code
									);
	}
	else if( $order->role == 'C' OR $order->role == 'N')
	{
		$header	= array(
							"ลูกค้า"	 => $order->customer_name,
							"วันที่"		=> thai_date($order->date_add, FALSE, '/'),
							"พนักงาน" => $CI->user_model->get_name($order->user),
							"เลขที่" => $order->code
							);
	}
	else
	{
		$ref = !empty($order->reference) ? '['.$order->reference.']' : '';
		$header	= array(
							"ลูกค้า"	=> $order->customer_name,
							"วันที่"		=> thai_date($order->date_add, FALSE, '/'),
							"พนักงาน" => $order->sale_code,
							"เลขที่" => $order->code.$ref
							);
	}

	return $header;
}



function barcodeImage($barcode, $height = 8)
{
	return '<img src="'.base_url().'assets/barcode/barcode.php?text='.$barcode.'" style="height:'.$height.'mm;" />';
}


function inputRow($text, $style='')
{
  return '<input type="text" class="print-row" value="'.$text.'" style="'.$style.'" disabled/>';
}


 ?>
