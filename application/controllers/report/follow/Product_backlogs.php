<?php
class Product_backlogs extends PS_Controller
{
	public $menu_code = 'REPDBL';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REFL';
	public $title = 'รายงาน สินค้าค้างส่ง';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/follow/product_backlogs';
    $this->load->model('report/follow/product_backlogs_model');
  }

  public function index()
  {
		$this->load->model('masters/channels_model');
		$this->load->model('masters/payment_methods_model');

		$ds = array(
			'channelsList' => $this->channels_model->get_data(),
			'paymentList' => $this->payment_methods_model->get_data()
		);

    $this->load->view('report/follow/report_product_backlogs', $ds);
  }


	public function get_report()
	{

		$arr = array(
			'allDate' => $this->input->get('allDate'),
			'fromDate' => $this->input->get('fromDate'),
			'toDate' => $this->input->get('toDate'),
			'allProduct' => $this->input->get('allProduct'),
			'fromProduct' => $this->input->get('fromProduct'),
			'toProduct' => $this->input->get('toProduct'),
			'allCustomer' => $this->input->get('allCustomer'),
			'fromCustomer' => $this->input->get('fromCustomer'),
			'toCustomer' => $this->input->get('toCustomer'),
			'allChannels' => $this->input->get('allChannels'),
			'channels' => $this->input->get('channels'),
			'allPayment' => $this->input->get('allPayment'),
			'payment' => $this->input->get('payment'),
			'isCount' => $this->input->get('isCount')
		);

		$count_title = $arr['isCount'] == 1 ? 'รวมสินค้าไม่นับสต็อก' : 'เฉพาะสินค้านับสต็อก';
		$pd_title = $arr['allProduct'] == 1 ? 'ทั้งหมด' : "(".$arr['fromProduct'].") - (".$arr['toProduct'].")";
		$cus_title = $arr['allCustomer'] == 1 ? 'ทั้งหมด' : "(".$arr['fromCustomer'].") - (".$arr['toCustomer'].")";
		$date_title = $arr['allDate'] == 1 ? 'ทั้งหมด' : "(".thai_date($arr['fromDate'], FALSE, '/').") - (".thai_date($arr['toDate'], FALSE, '/').")";
		$channels_title = $arr['allChannels'] == 1 ? 'ทั้งหมด' : $this->channels_list($arr['channels']);
		$payment_title = $arr['allPayment'] == 1 ? 'ทั้งหมด' : $this->payment_list($arr['payment']);

		$ds = array();

		$data = $this->product_backlogs_model->get_data($arr);

		if(!empty($data))
		{
			$no = 1;
			$total_amount = 0;
			$total_qty = 0;

			foreach($data as $rs)
			{
				$arr = array(
					'no' => $no,
					'item' => $rs->product_name .' ('.$rs->product_code.')',
					'order' => $rs->order_code,
					'customer' => $rs->customer_name,
					'channels' => $rs->channels_name,
					'payment' => $rs->payment_name,
					'qty' => $rs->qty,
					'amount' => number($rs->amount,2),
					'status' => $rs->status_name
				);

				array_push($ds, $arr);
				$no++;
				$total_amount += $rs->amount;
				$total_qty += $rs->qty;
			}

			$arr = array(
				'totalQty' => number($total_qty, 2),
				'totalAmount' => number($total_amount, 2)
			);

			array_push($ds, $arr);
		}
		else
		{
			$arr = array('nodata' => 'nodata');
			array_push($ds, $arr);
		}

		$result = array(
			'reportDate' => date('d/m/Y'),
			'pdList' => $pd_title,
			'isCount' => $count_title,
			'custList' => $cus_title,
			'dateList' => $date_title,
			'channelsList' => $channels_title,
			'paymentList' => $payment_title,
			'data' => $ds
		);

		echo json_encode($result);
	} //--- end get_report




	public function do_export()
	{
		$ds = array(
			'allDate' => $this->input->post('allDate'),
			'fromDate' => $this->input->post('fromDate'),
			'toDate' => $this->input->post('toDate'),
			'allCustomer' => $this->input->post('allCustomer'),
			'fromCustomer' => $this->input->post('fromCustomer'),
			'toCustomer' => $this->input->post('toCustomer'),
			'allProduct' => $this->input->post('allProduct'),
			'fromProduct' => $this->input->post('fromProduct'),
			'toProduct' => $this->input->post('toProduct'),
			'isCount' => $this->input->post('isCount'),
			'allChannels' => $this->input->post('allChannels'),
			'channels' => $this->input->post('channels'),
			'allPayment' => $this->input->post('allPayment'),
			'payment' => $this->input->post('payment')
		);

		$token = $this->input->post('token');

		$title = "รายงานออเดอร์ค้างส่ง ณ วันที่ ".date('d/m/Y');
		$pd_title = $ds['allProduct'] == 1 ? 'ทั้งหมด' : "(".$ds['fromProduct'].") - (".$ds['toProduct'].")";
		$count_title = $ds['isCount'] == 1 ? '(รวมสินค้านับสต็อก)' : '(เฉพาะสินค้านับสต็อก)';
		$cus_title = $ds['allCustomer'] == 1 ? 'ทั้งหมด' : "(".$ds['fromCustomer'].") - (".$ds['toCustomer'].")";
		$date_title = $ds['allDate'] == 1 ? 'ทั้งหมด' : "(".thai_date($ds['fromDate'], FALSE, '/').") - (".thai_date($ds['toDate'], FALSE, '/').")";
		$channels_title = $ds['allChannels'] == 1 ? 'ทั้งหมด' : $this->channels_list($ds['channels']);
		$payment_title = $ds['allPayment'] == 1 ? 'ทั้งหมด' : $this->payment_list($ds['payment']);

		$data = $this->product_backlogs_model->get_data($ds);

		//--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Products Backlogs');

		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $title);
    $this->excel->getActiveSheet()->mergeCells('A1:H1');
    $this->excel->getActiveSheet()->setCellValue('A2', "สินค้า : {$pd_title} {$count_title}");
    $this->excel->getActiveSheet()->mergeCells('A2:D2');
		$this->excel->getActiveSheet()->setCellValue('A3', "ลูกค้า : {$cus_title}");
    $this->excel->getActiveSheet()->mergeCells('A3:D3');
		$this->excel->getActiveSheet()->setCellValue('E3', "วันที่ : {$date_title}");
    $this->excel->getActiveSheet()->mergeCells('E3:H3');
    $this->excel->getActiveSheet()->setCellValue('A4', "ช่องทางขาย : {$channels_title}");
    $this->excel->getActiveSheet()->mergeCells('A4:D4');
		$this->excel->getActiveSheet()->setCellValue('E4', "ช่องทางการชำระเงิน : {$payment_title}");
    $this->excel->getActiveSheet()->mergeCells('E4:H4');

		$row = 5;

		$this->excel->getActiveSheet()->setCellValue("A{$row}", '#');
		$this->excel->getActiveSheet()->setCellValue("B{$row}", 'บาร์โค้ด');
		$this->excel->getActiveSheet()->setCellValue("C{$row}", 'รหัส');
		$this->excel->getActiveSheet()->setCellValue("D{$row}", 'สินค้า');
		$this->excel->getActiveSheet()->setCellValue("E{$row}", 'ออเดอร์');
		$this->excel->getActiveSheet()->setCellValue("F{$row}", 'รหัสลูกค้า');
		$this->excel->getActiveSheet()->setCellValue("G{$row}", 'ลูกค้า');
		$this->excel->getActiveSheet()->setCellValue("H{$row}", 'ช่องทางขาย');
		$this->excel->getActiveSheet()->setCellValue("I{$row}", 'การชำระเงิน');
		$this->excel->getActiveSheet()->setCellValue("J{$row}", 'สถานะ');
		$this->excel->getActiveSheet()->setCellValue("K{$row}", 'จำนวน');
		$this->excel->getActiveSheet()->setCellValue("L{$row}", 'มูลค่า');

		$this->excel->getActiveSheet()->getStyle("A{$row}:L{$row}")->getAlignment()->setHorizontal('center');

		$row++;

		if(!empty($data))
		{
			$no = 1;
			foreach($data as $rs)
			{
				$this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
				$this->excel->getActiveSheet()->setCellValue("B{$row}", $rs->barcode);
				$this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->product_code);
				$this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->product_name);
				$this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->order_code);
				$this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->customer_code);
				$this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->customer_name);
				$this->excel->getActiveSheet()->setCellValue("H{$row}", $rs->channels_name);
				$this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->payment_name);
				$this->excel->getActiveSheet()->setCellValue("J{$row}", $rs->status_name);
				$this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->qty);
				$this->excel->getActiveSheet()->setCellValue("L{$row}", $rs->amount);
				$no++;
				$row++;
			}

			$re = $row -1;

			$this->excel->getActiveSheet()->getStyle("A5:A{$re}")->getAlignment()->setHorizontal('center');

			$this->excel->getActiveSheet()->setCellValue("A{$row}", "รวม");
			$this->excel->getActiveSheet()->mergeCells("A{$row}:J{$row}");
			$this->excel->getActiveSheet()->getStyle("A{$row}")->getAlignment()->setHorizontal('right');

			$this->excel->getActiveSheet()->setCellValue("K{$row}", "=SUM(K6:K{$re})");
			$this->excel->getActiveSheet()->setCellValue("L{$row}", "=SUM(L6:L{$re})");

			$this->excel->getActiveSheet()->getStyle("K5:K{$row}")->getNumberFormat()->setFormatCode('#,##0');
			$this->excel->getActiveSheet()->getStyle("L5:L{$row}")->getNumberFormat()->setFormatCode('#,##0');
		}


		setToken($token);

    $file_name = "รายงานสินค้าค้างส่ง.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

	}



	public function channels_list($arr = array())
	{
		$this->load->model('masters/channels_model');
		$list = "";
		if(!empty($arr))
		{
			$channels = $this->channels_model->get_channels_name_list($arr);

			if(!empty($channels))
			{
				$i = 1;
				foreach($channels as $rs)
				{
					$list .= $i === 1 ? "{$rs->name}" : ", {$rs->name}";
					$i++;
				}
			}
		}

		return $list;
	}


	public function payment_list($arr = array())
	{
		$this->load->model('masters/payment_methods_model');
		$list = "";
		if(!empty($arr))
		{
			$payment = $this->payment_methods_model->get_payment_name_list($arr);

			if(!empty($payment))
			{
				$i = 1;
				foreach($payment as $rs)
				{
					$list .= $i === 1 ? "{$rs->name}" : ", {$rs->name}";
					$i++;
				}
			}
		}

		return $list;
	}
} //--- end class


 ?>
