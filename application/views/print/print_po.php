<?php
$this->load->helper('print');
$total_row 	= empty($details) ? 0 :count($details);
$row_span = 3;
$config 		= array(
	"row" => 14,
	"total_row" => $total_row,
	"font_size" => 10,
	"text_color" => "text-purple" //--- hilight text color class
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

if(!empty($vender))
{
	$header['left']['B'] = array(
		"client" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; color:#910DDE;'>Vendor</span>",
		"customer" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder;'>{$vender->name}</span>",
		"address1" => "{$vender->address}",
		"phone" => "โทร: {$vender->phone}",
		"taxid" => "Tax ID: {$vender->tax_id}"
	);
}
else
{
	$header['left']['B'] = array(
		"client" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; color:orange;'>Vendor</span>",
		"customer" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder;'>{$po->vender_name}</span>",
		"taxid" => "Tax ID: -"
	);
}


//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => $po->code),
	array('label' => 'วันที่', 'value' => thai_date($po->date_add, FALSE, '/')),
	array('label' => 'ครบกำหนด', 'value' => thai_date($po->due_date, FALSE, '/')),
	array('label' => 'ผู้สั่ง', 'value' => get_display_name($po->user))
);


$this->printer->add_header($header);


$row = $this->printer->row;
$total_page  = $this->printer->total_page;
$total_qty = 0; //--  จำนวนรวม
$total_amount= 0;  //--- มูลค่ารวม(หลังหักส่วนลด ไมีรวมภาษี
$total_vat = 0;

//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("#", "width:5%; text-align:center;"),
          array("รายละเอียด", "width:50%; text-align:center;"),
					array("จำนวน", "width:20%; text-align:right;"),
          array("ราคา/หน่วย", "width:10%; text-align:right;"),
          array("มูลค่า", "width:15%; text-align:right;")
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
	array("ผู้อนุมัติ", "","วันที่"),
	array("ผู้ตรวจสอบ", "","วันที่"),
  array("ผู้จัดทำ", "","วันที่")
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
      if($po->status == 4)
      {
        $page .= '
        <div style="width:0px; height:0px; position:relative; left:30%; line-height:0px; top:300px;color:red; text-align:center; z-index:100000; opacity:0.1; transform:rotate(-45deg)">
            <span style="font-size:150px; border-color:red; border:solid 10px; border-radius:20px; padding:0 20 0 20;">ยกเลิก</span>
        </div>';
      }

      $i = 0;

      while($i < $row)
      {
        $rs = isset($details[$index]) ? $details[$index] : array();
        if(!empty($rs))
        {
					$detail = $rs->product_code .' : '.$rs->product_name;
          $data = array(
            $n,
            inputRow($detail),
						number($rs->qty).(empty($rs->unit_name) ? '' : ' '.$rs->unit_name),
            number($rs->price, 2),
            number($rs->total_amount, 2)
          );

  				$total_qty += $rs->qty;
		      $total_amount += $rs->total_amount;
					$total_vat += ($rs->price * ($rs->rate * 0.01)) * $rs->qty;

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
		    $qty  = "<b>*** จำนวนรวม  ".number($total_qty)."  หน่วย ***</b>";
				$totalBfTax = number($total_amount, 2);
				$total_vat_amount = number($total_vat, 2);
				$net_amount = number($total_amount + $total_vat, 2);
		    $remark = $po->remark;
				$baht_text = "(".baht_text($total_amount + $total_vat).")";
		  }
		  else
		  {
				$qty  = "";
				$totalBfTax = "";
				$total_vat_amount = "";
				$net_amount = "";
		    $remark = "";
				$baht_text = "";
		  }


			$subTotal = array();

			if($this->printer->current_page == $this->printer->total_page)
		  {
				//--- จำนวนรวม   ตัว
			  $sub_qty  = '<td class="width-60 text-center" style="border:0;">';
				$sub_qty .= '<span class="'.$this->printer->text_color.'">'.$qty.'</span>';
			  $sub_qty .= '</td>';
			  $sub_qty .= '<td class="width-20" style="border:0;">';
			  $sub_qty .= '</td>';
				$sub_qty .= '<td class="width-20 text-right" style="border:0;"></td>';

			  array_push($subTotal, array($sub_qty));
			}

			$sub_price  = '<td rowspan="3" class="width-60 subtotal-first-row middle text-center"><span class="'.$this->printer->text_color.'">'.$baht_text.'</span></td>';
			$sub_price .= '<td class="width-20 subtotal subtotal-first-row">';
		  $sub_price .=  '<strong class="'.$this->printer->text_color.'">รวมเป็นเงิน</strong>';
		  $sub_price .= '</td>';
		  $sub_price .= '<td class="width-20 subtotal subtotal-first-row text-right">';
		  $sub_price .=  $totalBfTax;
		  $sub_price .= '</td>';
		  array_push($subTotal, array($sub_price));

			$sub_disc  = '<td class="subtotal">';
			$sub_disc .=  '<strong class="'.$this->printer->text_color.'">ภาษีมูลค่าเพิ่ม &nbsp;'.getConfig('SALE_VAT_RATE').' %</strong>';
			$sub_disc .= '</td>';
			$sub_disc .= '<td class="subtotal text-right">';
			$sub_disc .=  $total_vat_amount;
			$sub_disc .= '</td>';
			array_push($subTotal, array($sub_disc));

			//--- ยอดสุทธิ
			$sub_net  = "";

			$sub_net .= '<td class="subtotal subtotal-last-row">';
		  $sub_net .=  '<strong class="'.$this->printer->text_color.'">จำนวนเงินรวมทั้งสิ้น</strong>';
		  $sub_net .= '</td>';
		  $sub_net .= '<td class="subtotal subtotal-last-row text-right">';
		  $sub_net .=  $net_amount;
		  $sub_net .= '</td>';

		  array_push($subTotal, array($sub_net));

		if($this->printer->current_page == $this->printer->total_page)
		{
			//--- หมายเหตุ
			$sub_remark  = '<td colspan="3" class="no-border" style="white-space:normal;"><span class="'.$this->printer->text_color.'"><b>หมายเหตุ : </b></span>'.$remark.'</td>';
		  array_push($subTotal, array($sub_remark));
		}


    $page .= $this->printer->print_sub_total($subTotal);
    $page .= $this->printer->content_end();
    $page .= $this->printer->footer;
    $page .= $this->printer->page_end();
    $total_page --;
    $this->printer->current_page++;
}

$page .= $this->printer->doc_footer();

echo $page;
 ?>
