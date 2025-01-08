<?php
$this->load->helper('print');
$total_row 	= empty($details) ? 0 :count($details);
$config = array(
	"row" => 12,
	"total_row" => $total_row,
	"font_size" => 10,
	"text_color" => "text-blue" //--- hilight text color class
);

$this->printer->config($config);

$page  = '';
$page .= $this->printer->doc_header();
$docType = doc_type($order->role);
$this->printer->add_title($docType['title']);

$header		= array();

//---- Header block Company details On Left side
$header['left'] = array();

$header['left']['A'] = array(
	'company_name' => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder;'>".getConfig('COMPANY_FULL_NAME')."</span>",
	'address1' => getConfig('COMPANY_ADDRESS1'),
	'address2' => getConfig('COMPANY_ADDRESS2').' '.getConfig('COMPANY_POST_CODE'),
	'phone' => 'โทร: '. getConfig('COMPANY_PHONE'),
	'taxid' => 'Tax ID: ' . getConfig('COMPANY_TAX_ID')
);

if(!empty($address))
{
	$header['left']['B'] = array(
		"client" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; color:orange;'>ลูกค้า</span>",
		"customer" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder;'>({$customer->code}) : {$customer->name}</span>",
		"address1" => "{$address->address} ต.{$address->sub_district} อ.{$address->district} จ.{$address->province} {$address->postcode}",
		"phone" => "โทร: {$address->phone}",
		"taxid" => "Tax ID: {$customer->Tax_Id}"
	);
}
else
{
	$header['left']['B'] = array(
		"client" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; color:orange;'>ลูกค้า</span>",
		"customer" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder;'>({$customer->code}) : {$customer->name}</span>",
		"taxid" => "Tax ID: {$customer->Tax_Id}"
	);
}



//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => $order->code),
	array('label' => 'วันที่', 'value' => thai_date($order->date_add, FALSE, '/')),
	array('label' => 'ผู้ขาย', 'value' => $order->emp_name)
);

$header['right']['B'] = array(
	array('label' => '', 'value' => barcodeImage($order->code, 12))
);


$this->printer->add_header($header);


$row 		     = $this->printer->row;
$total_page  = $this->printer->total_page;
$total_qty 	 = 0; //--  จำนวนรวม
$total_amount 		= 0;  //--- มูลค่ารวม(หลังหักส่วนลด)
$total_discount 	= 0; //--- ส่วนลดรวม
$total_order  = 0;    //--- มูลค่าราคารวม

$bill_discount		= $order->bDiscAmount;


//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("#", "width:5%; text-align:center;"),
          array("บาร์โค้ด", "width:15%; text-align:left;"),
          array("สินค้า", "width:40%; text-align:text-left;"),
					array("ราคา", "width:10%; text-align:right;"),
          array("จำนวน", "width:10%; text-align:right;"),
					array("ส่วนลด", "width:10%; text-align:right;"),
          array("มูลค่า", "width:15%; text-align:right;")
          );

$this->printer->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align:center;",
            "text-align:center;",
            "text-align:left;",
            "text-align:right;",
            "text-align:right;",
            "text-align:right;",
            "text-align:right;"
            );

$this->printer->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer	= array(
          array("ผู้รับของ", "ได้รับสินค้าถูกต้องตามรายการแล้ว","วันที่"),
          array("ผู้ส่งของ", "","วันที่"),
          array("ผู้ตรวจสอบ", "","วันที่"),
          array("ผู้อนุมัติ", "","วันที่")
          );

$this->printer->set_footer($footer);


$n = 1;
$index = 0;
while($total_page > 0 )
{
  $page .= $this->printer->page_start();
  $page .= $this->printer->top_page();
  $page .= $this->printer->content_start();
  $page .= $this->printer->table_start();
  $i = 0;

  while($i<$row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
      //--- จำนวนสินค้า ถ้ามีการบันทึกขาย จะได้ข้อมูลจาก tbl_order_sold ซึ่งเป็น qty
      //--- แต่ถ้าไม่มีการบันทึกขายจะได้ข้อมูลจาก tbl_order_detail Join tbl_qc
      //--- ซึ่งได้จำนวน มา 3 ฟิลด์ คือ oreder_qty, prepared, qc
      //--- ต้องเอา order_qty กับ qc มาเปรียบเทียบกัน ถ้าเท่ากัน อันไหนก็ได้ ถ้าไม่เท่ากัน เอาอันที่น้อยกว่า
      $qty = $rs->qty;

      //--- ราคาสินค้า
      $price = $rs->price;

      //--- ส่วนลดสินค้า (ไว้แสดงไม่มีผลในการคำนวณ)
      $discount = discountLabel($rs->discount1, $rs->discount2, $rs->discount3);

      //--- ส่วนลดสินค้า (มีผลในการคำนวณ)
      //--- ทั้งสองตารางใช้ชือฟิลด์ เดียวกัน
      $discount_amount = $rs->discount_amount;

      //--- มูลค่าสินค้า หลังหักส่วนลดตามรายการสินค้า
      $amount = $rs->total_amount;

      $barcode = barcodeImage($rs->barcode); //$is_barcode === FALSE ? $rs->barcode : barcodeImage($rs->barcode);
      //--- เตรียมข้อมูลไว้เพิ่มลงตาราง
			$data = array(
				$n,
				$barcode,
				inputRow($rs->product_code.' : '.$rs->product_name),
				number($price, 2),
				number($qty),
				$discount,
				number($amount, 2)
			);

			$total_qty      += $qty;
			$total_amount   += $amount;
			$total_discount += $discount_amount;
			$total_order    += ($qty * $price);
    }
    else
    {
      $data = array("", "", "", "","", "","");
    }

    $page .= $this->printer->print_row($data);

    $n++;
    $i++;
    $index++;
  }

  $page .= $this->printer->table_end();

  if($this->printer->current_page == $this->printer->total_page)
  {
    $qty  = number($total_qty);
    $total_order = number($total_order, 2);
    $total_discount_amount = number(($total_discount + $bill_discount),2);
    $net_amount = number( ($total_amount + $order->shipping_fee + $order->service_fee) - $bill_discount - $order->deposit, 2);
    $service_fee = number($order->service_fee, 2);
    $shipping_fee = number($order->shipping_fee, 2);
    $deposit = number($order->deposit, 2);
    $remark = $order->remark;
		$baht_text = "(".baht_text(($total_amount + $order->shipping_fee + $order->service_fee) - $bill_discount - $order->deposit).")";
  }
  else
  {
    $qty = "";
    $amount = "";
    $shipping_fee = "";
    $service_fee = '';
    $deposit = '';
    $total_discount_amount = "";
    $net_amount = "";
    $remark = "";
		$baht_text = "";
  }

  $subTotal = array();

	$rowspan = $order->role == 'S' ? 4 : 3;
  //--- จำนวนรวม   ตัว
  $sub_qty  = '<td rowspan="'.$rowspan.'" class="width-60 subtotal-first-row">';
	$sub_qty .= '<strong>หมายเหตุ : </strong> '.$order->remark;
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="width-20 subtotal subtotal-first-row">';
  $sub_qty .=  '<strong>จำนวนรวม</strong>';
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="width-20 subtotal subtotal-first-row text-right">';
  $sub_qty .=    $qty;
  $sub_qty .= '</td>';

  array_push($subTotal, array($sub_qty));

  //--- ราคารวม

  $sub_price  = '<td class="subtotal">';
  $sub_price .=  '<strong>ราคารวม</strong>';
  $sub_price .= '</td>';
  $sub_price .= '<td class="subtotal text-right">';
  $sub_price .=  $total_order;
  $sub_price .= '</td>';
  array_push($subTotal, array($sub_price));

  //--- ส่วนลดรวม
  $sub_disc  = '<td class="subtotal">';
  $sub_disc .=  '<strong>ส่วนลดรวม</strong>';
  $sub_disc .= '</td>';
  $sub_disc .= '<td class="subtotal text-right"> -';
  $sub_disc .=  $total_discount_amount;
  $sub_disc .= '</td>';
  array_push($subTotal, array($sub_disc));

  // //--- shipping_fee
  // $sub_ship  = '<td class="subtotal">';
  // $sub_ship .=  '<strong>ค่าจัดส่ง</strong>';
  // $sub_ship .= '</td>';
  // $sub_ship .= '<td class="subtotal text-right">';
  // $sub_ship .=  $shipping_fee;
  // $sub_ship .= '</td>';
  // array_push($subTotal, array($sub_ship));

	//
  // //--- service_fee
  // $sub_serv  = '<td class="subtotal">';
  // $sub_serv .=  '<strong>อื่นๆ</strong>';
  // $sub_serv .= '</td>';
  // $sub_serv .= '<td class="subtotal text-right">';
  // $sub_serv .=  $service_fee;
  // $sub_serv .= '</td>';
  // array_push($subTotal, array($sub_serv));

	if($order->role == 'S')
	{
		//--- deposit
		$sub_depo  = '<td class="subtotal">';
		$sub_depo .=  '<strong>ชำระแล้ว</strong>';
		$sub_depo .= '</td>';
		$sub_depo .= '<td class="subtotal text-right"> -';
		$sub_depo .=  $deposit;
		$sub_depo .= '</td>';
		array_push($subTotal, array($sub_depo));
	}


  //--- ยอดสุทธิ
	$sub_net  = '<td class="no-border text-center">'.$baht_text.'</td>';
  $sub_net .= '<td class="subtotal subtotal-last-row">';
  $sub_net .=  '<strong>ยอดเงินสุทธิ</strong>';
  $sub_net .= '</td>';
  $sub_net .= '<td class="subtotal subtotal-last-row text-right">';
  $sub_net .=  $net_amount;
  $sub_net .= '</td>';

  array_push($subTotal, array($sub_net));

  $page .= $this->printer->print_sub_total($subTotal);
  $page .= $this->printer->content_end();
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
  $page .= $this->printer->footer;
  $page .= $this->printer->page_end();

  $total_page --;
  $this->printer->current_page++;
}

$page .= $this->printer->doc_footer();

echo $page;
 ?>
