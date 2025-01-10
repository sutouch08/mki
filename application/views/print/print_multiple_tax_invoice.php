<?php
$this->load->helper('print');
$page  = '';
$page .= $this->printer->doc_header();

if(!empty($data))
{
	foreach($data as $ds)
	{
		$details = $ds->details;
		$order = $ds->order;
		$use_vat = $ds->use_vat;
		$saleman = $ds->saleman;
		$title = $ds->title;
		$address = $ds->address;

		$current_page = 1;

		$total_row 	= empty($details) ? 0 :count($details);
		$row_span = 6;

		$config 		= array(
			"row" => 10,
			"total_row" => $total_row,
			"font_size" => 10,
			"text_color" => "text-green" //--- hilight text color class
		);

		$this->printer->config($config);

		$this->printer->add_title($title);


		$header		= array();

		//---- Header block Company details On Left side
		$header['left'] = array();

		$header['left']['A'] = array(
			'company_name' => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; white-space:normal;'>".getConfig('COMPANY_FULL_NAME')."</span>",
			'address1' => getConfig('COMPANY_ADDRESS1').' '.getConfig('COMPANY_ADDRESS2').' '.getConfig('COMPANY_POST_CODE'),
			'phone' => 'โทร: '. getConfig('COMPANY_PHONE')
		);

		if($use_vat)
		{
			$header['left']['A']['taxid'] = "เลขประจำตัวผู้เสียภาษี  ".getConfig('COMPANY_TAX_ID')." (".getConfig('COMPANY_BRANCH_NAME').")";
		}


		$header['left']['B'] = array(
			"client" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; white-space:normal; color:green;'>ลูกค้า</span>",
			"customer" => "<span style='font-size:".($this->printer->font_size + 1)."px; font-weight:bolder; white-space:normal;'>({$order->customer_code}) : {$order->customer_name}</span>",
			"address1" => "{$address}",
			"phone" => "โทร. {$order->phone}",
			"taxid" => "เลขประจำตัวผู้เสียภาษี ".(empty($order->tax_id) ? "-" : "{$order->tax_id} ({$order->branch_name})")
		);


		//--- Header block  Document details On the right side
		$header['right'] = array();

		$header['right']['A'] = array(
			array('label' => 'เลขที่', 'value' => $order->code),
			array('label' => 'วันที่', 'value' => thai_date($order->doc_date, FALSE, '/')),
			array('label' => 'วันครบกำหนด', 'value' => (empty($order->due_date) ? thai_date($order->doc_date, FALSE, '/') : thai_date($order->due_date, FALSE, '/')))
		);

		$header['right']['B'] = array(
			array('label' => 'พนักงานขาย', 'value' => (empty($saleman) ? '-' : $saleman->name)),
			array('label' => 'อ้างอิง', 'value' => (strlen($order->reference) > 50 ? substr($order->reference, 0, 50)."..." : $order->reference))
		);

		$this->printer->add_header($header);


		//--- ถ้าเป็นฝากขาย(2) หรือ เบิกแปรสภาพ(5) หรือ ยืมสินค้า(6)
		//--- รายการพวกนี้ไม่มีการบันทึกขาย ใช้การโอนสินค้าเข้าคลังแต่ละประเภท
		//--- ฝากขาย โอนเข้าคลังฝากขาย เบิกแปรสภาพ เข้าคลังแปรสภาพ  ยืม เข้าคลังยืม
		//--- รายการที่จะพิมพ์ต้องเอามาจากการสั่งสินค้า เปรียบเทียบ กับยอดตรวจ ที่เท่ากัน หรือ ตัวที่น้อยกว่า

		$subtotal_row = 4;


		$row 		     = $this->printer->row;
		$total_page  = $this->printer->total_page;
		$total_qty 	 = 0; //--  จำนวนรวม
		$total_amount = 0;
		$total_vatable = 0;
		$total_non_vat = 0;
		$total_vat = 0;


		//**************  กำหนดหัวตาราง  ******************************//
		$thead	= array(
		          array("#", "width:5%; text-align:center;"),
		          array("รหัสสินค้า", "width:15%; text-align:center;"),
		          array("รายละเอียด", "width:25%; text-align:center;"),
		          array("จำนวน", "width:15%; text-align:right;"),
		          array("ราคาต่อหน่วย", "width:15%; text-align:right;"),
		          array("ส่วนลด", "width:10%; text-align:center;"),
							array("จำนวนเงิน", "width:15%; text-align:right;")
		          );

		$this->printer->add_subheader($thead);


		//***************************** กำหนด css ของ td *****************************//
		$pattern = array(
		            "text-align:center;",
		            "text-align:left;",
		            "text-aligh:left",
		            "text-align:right;",
		            "text-align:right;",
		            "text-align:center;",
		            "text-align:right;"
		            );

		$this->printer->set_pattern($pattern);


		//*******************************  กำหนดช่องเซ็นของ footer *******************************//
		$footer	= array(
		          array("ผู้รับของ", "ได้รับสินค้าถูกต้องตามรายการแล้ว","วันที่"),
		          array("ผู้ส่งของ", "","วันที่"),
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
						$rs->product_code,
						inputRow($rs->product_name),
						number($rs->qty, 2).' '.$rs->unit_name,
						number($rs->price, 2),
						$rs->discount_label,
						number($rs->amount, 2)
					);

					$total_qty += $rs->qty;
		    	$total_amount += $rs->amount;
					$total_vatable += $rs->vat_rate > 0 ? $rs->amount : 0;
					$total_non_vat += $rs->vat_rate > 0 ? 0 : $rs->amount;
					$total_vat += $rs->vat_amount;
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

		  if($current_page == $this->printer->total_page)
		  {
		    $qty  = "<b>*** จำนวนรวม  ".number($total_qty)."  หน่วย ***</b>";
				$f_total = number($total_amount, 2); //--- ราคารวมภาษี
				$f_total_vatable = number($total_vatable, 2);
				$f_total_bef_vat = number($total_vatable - $total_vat, 2);
				$f_total_non_vat = number($total_non_vat, 2);
				$f_total_vat = number($total_vat, 2);
				$remark = $order->remark;
				$baht_text = "(".baht_text($total_amount).")";
		  }
		  else
		  {
				$qty  = "";
				$f_total = "";
				$f_total_vatable = "";
				$f_total_bef_vat = "";
				$f_total_non_vat = "";
				$f_total_vat = "";
				$remark = "";
				$baht_text = "";
		  }

		  $subTotal = array();

			if($current_page == $this->printer->total_page)
		  {
				//--- จำนวนรวม   ตัว
			  $sub_qty  = '<td class="width-60 text-center" style="border:0;">';
				$sub_qty .= $qty;
			  $sub_qty .= '</td>';
			  $sub_qty .= '<td class="width-20" style="border:0;">';
			  $sub_qty .= '</td>';
				$sub_qty .= '<td class="width-20 text-right" style="border:0;"></td>';

			  array_push($subTotal, array($sub_qty));
			}

			if($use_vat)
			{
				$sub_price  = '<td rowspan="'.$row_span.'" class="width-60 subtotal-first-row middle text-center">'.$baht_text.'</td>';
				$sub_price .= '<td class="width-20 subtotal subtotal-first-row text-right">';
			  $sub_price .=  '<strong>รวมเป็นเงิน</strong>';
			  $sub_price .= '</td>';
			  $sub_price .= '<td class="width-20 subtotal subtotal-first-row text-right">';
			  $sub_price .=  $f_total;
			  $sub_price .= '</td>';
			  array_push($subTotal, array($sub_price));
			}

			if($use_vat)
			{
				$sub_disc  = '<td class="subtotal text-right">';
				$sub_disc .=  '<strong>มูลค่าที่ไม่มี/ยกเว้นภาษี</strong>';
				$sub_disc .= '</td>';
				$sub_disc .= '<td class="subtotal text-right">';
				$sub_disc .=  $f_total_non_vat;
				$sub_disc .= '</td>';
				array_push($subTotal, array($sub_disc));

				$sub_disc  = '<td class="subtotal text-right">';
				$sub_disc .=  '<strong>มูลค่าที่คำนวนภาษี</strong>';
				$sub_disc .= '</td>';
				$sub_disc .= '<td class="subtotal text-right">';
				$sub_disc .=  $f_total_vatable;
				$sub_disc .= '</td>';
				array_push($subTotal, array($sub_disc));

				$sub_disc  = '<td class="subtotal text-right">';
				$sub_disc .=  '<strong>มูลค่าก่อนภาษี</strong>';
				$sub_disc .= '</td>';
				$sub_disc .= '<td class="subtotal text-right">';
				$sub_disc .=  $f_total_bef_vat;
				$sub_disc .= '</td>';
				array_push($subTotal, array($sub_disc));

				$sub_disc  = '<td class="subtotal text-right">';
				$sub_disc .=  '<strong>ภาษีมูลค่าเพิ่ม &nbsp;'.getConfig('SALE_VAT_RATE').' %</strong>';
				$sub_disc .= '</td>';
				$sub_disc .= '<td class="subtotal text-right">';
				$sub_disc .=  $f_total_vat;
				$sub_disc .= '</td>';
				array_push($subTotal, array($sub_disc));

			}

		$first_row = $use_vat ? "" : "subtotal-first-row";
			//--- ยอดสุทธิ
			$sub_net  = "";

			if($use_vat === FALSE)
			{
				$sub_net  = '<td class="width-60 subtotal-first-row text-center">'.$baht_text.'</td>';
			}

		  $sub_net .= '<td class="subtotal subtotal-last-row '.$first_row.'">';
		  $sub_net .=  '<strong>จำนวนเงินรวมทั้งสิ้น</strong>';
		  $sub_net .= '</td>';
		  $sub_net .= '<td class="subtotal subtotal-last-row '.$first_row.' text-right">';
		  $sub_net .=  $f_total;
		  $sub_net .= '</td>';

		  array_push($subTotal, array($sub_net));

			//--- หมายเหตุ
			$sub_remark  = '<td colspan="3" class="no-border" style="white-space:normal;"><span class="green"><b>หมายเหตุ : </b></span>'.$remark.'</td>';
		  array_push($subTotal, array($sub_remark));

			$page .= $this->printer->print_sub_total($subTotal);
		  $page .= $this->printer->content_end();
			$page .= "<div class='divider-hidden'></div>";

		  $page .= $this->printer->footer;
		  $page .= $this->printer->page_end();

		  $total_page --;
		  $current_page++;
		}
	}
}



$page .= $this->printer->doc_footer();

echo $page;
 ?>
