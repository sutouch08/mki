<?php
$this->load->helper('print');
$this->load->helper('vat');
$total_row 	= empty($details) ? 0 :count($details);
$row_span = 2;

$config 		= array(
	"row" => 12,
	"total_row" => $total_row,
	"font_size" => 10,
	"text_color" => "text-green" //--- hilight text color class
);

$this->printer->config($config);

$page  = '';
$page .= $this->printer->doc_header();

$this->printer->add_title($title);


$header		= array();

//---- Header block Company details On Left side
$header['left'] = array();

$header['left']['A'] = array(
	'company_name' => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; white-space:normal;'>".getConfig('COMPANY_FULL_NAME')."</span>",
	'address1' => getConfig('COMPANY_ADDRESS1').' '.getConfig('COMPANY_ADDRESS2').' '.getConfig('COMPANY_POST_CODE'),
	'phone' => 'โทร: '. getConfig('COMPANY_PHONE'),
	'taxid' => "เลขประจำตัวผู้เสียภาษี  ".getConfig('COMPANY_TAX_ID')." (".getConfig('COMPANY_BRANCH_NAME').")"
);

$header['left']['B'] = array(
	"client" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; white-space:normal; color:green;'>ลูกค้า</span>",
	"customer" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; white-space:normal;'>({$customer->code}) : {$customer->name}</span>",
	"address1" => "{$address}",
	"phone" => "โทร. ".(empty($adr) ? "" : $adr->phone),
	"taxid" => "เลขประจำตัวผู้เสียภาษี {$customer->Tax_Id} (".empty($adr) ? "" : $adr->branch_name.")"
);


//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => $order->code),
	array('label' => 'วันที่', 'value' => thai_date($order->date_add, FALSE, '/')),
	array('label' => 'พนักงานขาย', 'value' => (empty($saleman) ? '-' : $saleman->name)),
	array('label' => 'อ้างอิง', 'value' => $reference)
);


$this->printer->add_header($header);


//--- ถ้าเป็นฝากขาย(2) หรือ เบิกแปรสภาพ(5) หรือ ยืมสินค้า(6)
//--- รายการพวกนี้ไม่มีการบันทึกขาย ใช้การโอนสินค้าเข้าคลังแต่ละประเภท
//--- ฝากขาย โอนเข้าคลังฝากขาย เบิกแปรสภาพ เข้าคลังแปรสภาพ  ยืม เข้าคลังยืม
//--- รายการที่จะพิมพ์ต้องเอามาจากการสั่งสินค้า เปรียบเทียบ กับยอดตรวจ ที่เท่ากัน หรือ ตัวที่น้อยกว่า

$subtotal_row = 4;


$row 		     = $this->printer->row;
$total_page  = $this->printer->total_page;
$total_amount = 0;


//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("#", "width:5%; text-align:center;"),
          array("รายการ", "width:50%; text-align:center;"),
          array("จำนวนเงิน", "width:15%; text-align:right;"),
          array("ยอดค้าง", "width:15%; text-align:right;"),
					array("ยอดชำระ", "width:15%; text-align:right;")
          );

$this->printer->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align:center;",
            "text-align:left;",
            "text-align:right;",
            "text-align:right;",
            "text-align:right;"
            );

$this->printer->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer	= array(
          array("ผู้รับเงิน", "","วันที่"),
          array(NULL, NULL, NULL),
          array("ผู้จ่ายเงิน", "","วันที่")
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
	if($order->status == 2)
	{
		$page .= '
		<div style="width:0px; height:0px; position:relative; left:30%; line-height:0px; top:300px;color:red; text-align:center; z-index:100000; opacity:0.1; transform:rotate(-45deg)">
				<span style="font-size:150px; border-color:red; border:solid 10px; border-radius:20px; padding:0 20 0 20;">ยกเลิก</span>
		</div>';
	}

  $i = 0;

  while($i<$row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
			$data = array(
				$n,
				"ได้รับเงินตามเอกสารเลขที่ &nbsp;".$rs->reference." ลงวันที่ ".thai_date($this->order_repay_model->get_order_date($rs->reference), FALSE, '/'),
				number($rs->amount, 2),
				number($rs->balance, 2),
				number($rs->pay_amount, 2)
			);


      $total_amount += $rs->amount;
    }
    else
    {
      $data = array("", "", "", "","");
    }

    $page .= $this->printer->print_row($data);

    $n++;
    $i++;
    $index++;
  }

  $page .= $this->printer->table_end();

  if($this->printer->current_page == $this->printer->total_page)
  {
		$net_amount = number($total_amount, 2);
    $remark = $order->remark;
		$baht_text = "(".baht_text($total_amount).")";
  }
  else
  {
		$net_amount = "";
    $remark = "";
		$baht_text = "";
  }

  $subTotal = array();

	//--- ยอดสุทธิ
	$sub_net  = '<td class="subtotal subtotal-first-row subtotal-last-row text-center">'.$baht_text.'</td>';
  $sub_net .= '<td class="subtotal subtotal-first-row subtotal-last-row">';
  $sub_net .=  '<strong>จำนวนเงินรวมทั้งสิ้น</strong>';
  $sub_net .= '</td>';
  $sub_net .= '<td class="subtotal subtotal-first-row subtotal-last-row text-right">';
  $sub_net .=  $net_amount;
  $sub_net .= '</td>';
  array_push($subTotal, array($sub_net));

	//--- หมายเหตุ
	$sub_remark  = '<td colspan="3" class="no-border text-green" style="white-space:normal;"><b>หมายเหตุ : </b>'.$remark.'</td>';
  array_push($subTotal, array($sub_remark));

	$page .= $this->printer->print_sub_total($subTotal);
  $page .= $this->printer->content_end();
	$page .= "<div class='divider-hidden'></div>";

  $page .= $this->printer->footer;
  $page .= $this->printer->page_end();

  $total_page --;
  $this->printer->current_page++;
}

$page .= $this->printer->doc_footer();

echo $page;
 ?>
