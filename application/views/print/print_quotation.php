<?php
$this->load->helper('print');
$total_row 	= empty($details) ? 0 :count($details);
$config 		= array(
	"row" => 10,
	"total_row" => $total_row,
	"font_size" => 10,
	"text_color" => "text-orange" //--- hilight text color class
);

$this->printer->config($config);

$page  = '';
$page .= $this->printer->doc_header();

$this->printer->add_title($title);

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

$header['left']['B'] = array(
	"client" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; color:orange;'>ลูกค้า</span>",
	"customer" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder;'>({$customer->code}) : {$customer->name}</span>",
	"address1" => empty($address) ? "-": "{$address->address} ต.{$address->sub_district} อ.{$address->district} จ.{$address->province} {$address->postcode}",
	"phone" => empty($address)? "" : "โทร: {$address->phone}",
	"taxid" => "Tax ID: {$customer->Tax_Id}"
);


//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => $order->code),
	array('label' => 'วันที่', 'value' => thai_date($order->date_add, FALSE, '/')),
	array('label' => 'ผู้ขาย', 'value' => $order->emp_name)
);

$payment = $order->is_term == 0 ? 'เงินสด' : 'เครดิต '.$order->credit_term.' วัน';

$header['right']['B'] = array(
	array('label' => 'ชื่องาน', 'value' => $order->title),
	array('label' => 'ผู้ติดต่อ', 'value' => $order->contact),
	array('label' => 'การชำระเงิน', 'value' => $payment),
	array('label' => 'ยืนราคา', 'value' => intval($order->valid_days).' วัน')
);




$this->printer->add_header($header);


$row 		     = $this->printer->row;
$total_page  = $this->printer->total_page == 0 ? 1 : $this->printer->total_page;
$total_qty 	 = 0; //--  จำนวนรวม
$total_amount 		= 0;  //--- มูลค่ารวม(หลังหักส่วนลด)
$total_discount 	= 0; //--- ส่วนลดรวม
$total_order  = 0;    //--- มูลค่าราคารวม

$bill_discount = $order->bDiscAmount;

//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("#", "width:5%; text-align:center;"),
          array("รายการ", "width:43%;"),
          array("จำนวน", "width:5%; text-align:right;"),
					array("หน่วย", "width:10%; text-align:center;"),
          array("ราคาต่อหน่วย", "width:12%; text-align:right;"),
					array("ส่วนลด", "width:10%; text-align:right;"),
          array("มูลค่า", "width:15%; text-align:right;")
          );

$this->printer->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align: center;", //-- ลำดับ
            "text-align:left;",  //--- รายการ
            "text-align:right", //--- จำนวน
            "text-align:center;", //--- หน่วย
            "text-align:right;", //--- ราคาต่อหน่วย
						"text-align:right;",
            "text-align:right;" //--- มูลค่า
            );

$this->printer->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer	= array(
          array("อนุมัติโดย/Approved by", "","วันที่/Date"),
          array("อนุมัติซื้อ/Accepted by", "","วันที่/Date")
          );

$this->printer->set_footer($footer);


$n = 1;
$index = 0;
while($total_page > 0 )
{
  $page .= $this->printer->page_start();
  $page .= $this->printer->top_page();

	if($order->status == 2)
	{
		$page .= $this->printer->cancle_watermark;
	}

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

      //--- เตรียมข้อมูลไว้เพิ่มลงตาราง
      $data = array(
                    $n,
                    inputRow($rs->product_code.' : '.$rs->product_name),
										number($qty),
										$rs->unit_code,
                    number($price, 2),
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
      $data = array("", "", "", "","", "", "");
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
    $amount = number($total_order, 2);
    $total_discount_amount = number(($total_discount + $bill_discount),2);
    $net_amount = number( ($total_amount - $bill_discount), 2);
		$baht_text = "(".baht_text($total_amount - $bill_discount).")";
		$bDiscAmount = number($bill_discount,2);
    $remark = $order->remark;
  }
  else
  {
    $qty = "";
    $amount = "";
    $total_discount_amount = "";
    $net_amount = "";
		$baht_text = "";
		$bDiscAmount = "";
    $remark = $order->remark;
  }

  $subTotal = array();

  //--- ราคารวม
	$sub_price  = "<td class='width-60 subtotal-first-row'></td>";

  $sub_price .= "<td class='subtotal subtotal-first-row'>";
  $sub_price .=  "<strong class='{$this->printer->text_color}'>ส่วนลดท้ายบิล</strong>";
  $sub_price .= '</td>';
  $sub_price .= '<td class="subtotal subtotal-first-row text-right">';
  $sub_price .=  "<strong>{$bDiscAmount} THB</strong>";
  $sub_price .= '</td>';
  array_push($subTotal, array($sub_price));

	//--- ราคารวม
	$sub_price  = "<td class='width-60 no-border'></td>";

  $sub_price .= "<td class='subtotal subtotal-first-row'>";
  $sub_price .=  "<strong class='{$this->printer->text_color}'>ราคารวม</strong>";
  $sub_price .= '</td>';
  $sub_price .= '<td class="subtotal subtotal-first-row text-right">';
  $sub_price .=  "<strong>{$amount} THB</strong>";
  $sub_price .= '</td>';
  array_push($subTotal, array($sub_price));

  //--- ส่วนลดรวม
	$sub_disc  = "<td class='width-60 no-border'></td>";
  $sub_disc  .= "<td class='subtotal'>";
  $sub_disc .=  '<strong class="'.$this->printer->text_color.'">ส่วนลดรวม</strong>';
  $sub_disc .= '</td>';
  $sub_disc .= '<td class="subtotal text-right">';
  $sub_disc .=  "<strong>{$total_discount_amount} THB</strong>";
  $sub_disc .= '</td>';
  array_push($subTotal, array($sub_disc));

  //--- ยอดสุทธิ
	$sub_net  = "<td class='width-60 text-center no-border'>";
	$sub_net .= "<strong class='{$this->printer->text_color}'>{$baht_text}</strong>";
	$sub_net .= "</td>";
  $sub_net .= "<td class='subtotal subtotal-last-row'>";
  $sub_net .=  '<strong class="'.$this->printer->text_color.'">ยอดเงินสุทธิ</strong>';
  $sub_net .= '</td>';
  $sub_net .= '<td class="subtotal subtotal-last-row text-right">';
  $sub_net .=  "<strong>{$net_amount} THB</strong>";
  $sub_net .= '</td>';

  array_push($subTotal, array($sub_net));

  $page .= $this->printer->print_sub_total($subTotal);
  $page .= $this->printer->content_end();
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
	$page .= $this->printer->print_remark($remark);
  $page .= $this->printer->footer;
  $page .= $this->printer->page_end();

  $total_page --;
  $this->printer->current_page++;
}

$page .= $this->printer->doc_footer();

echo $page;
 ?>
