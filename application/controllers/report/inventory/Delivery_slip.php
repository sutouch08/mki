<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Delivery_slip extends PS_Controller
{
  public $menu_code = 'REORDL';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานการจัดส่ง';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/delivery_slip';
		$this->load->model('report/inventory/delivery_slip_model');
		$this->load->model('inventory/invoice_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/customers_model');
    $this->load->model('inventory/delivery_order_model');
		$this->load->model('address/address_model');
    $this->load->helper('order');
		$this->load->helper('payment_method');
		$this->load->helper('channels');
		$this->load->helper('sender');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'customer' => get_filter('customer', 'customer', ''),
			'payment' => get_filter('payment', 'payment', 'all'),
			'channels' => get_filter('channels', 'channels', 'all'),
			'sender' => get_filter('sender', 'sender', 'all'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', ''),
			'print_status' => get_filter('print_status', 'print_status', '0')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 5; //-- url segment
		$rows     = $this->delivery_slip_model->count_rows($filter,8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->delivery_slip_model->get_list($filter, $perpage, $this->uri->segment($segment),8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('report/inventory/delivery_slip_list', $filter);
  }


	public function clear_filter()
	{
		$filter = array('code', 'customer', 'payment', 'channels', 'sender', 'from_date', 'to_date', 'print_status');

		clear_filter($filter);

		echo 'done';
	}

	public function get_address($address_id, $customer_ref, $customer_code)
	{

		$address = NULL;

		if(empty($address_id))
		{
			$adr_code = empty($customer_ref) ? $customer_code : $customer_ref;
			$address = $this->address_model->get_default_address($adr_code);
		}
		else
		{
			$address = $this->address_model->get_shipping_detail($address_id);
		}

		if(empty($address))
		{
			$arr = array(
				"code" => "",
				"name" => "",
				"address" => "",
				"sub_district" => "",
				"district" => "",
				"province" => "",
				"postcode" => "",
				"phone" => "",
				"email" => "",
				"alias" => "",
				"is_default" => 0
			);

			$address = (object) $arr;
		}

		return $address;
	}





	public function do_export()
  {
		//--- load excel library
		$this->load->library('excel');
		$token = $this->input->post('token');

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('ใบรายงานการส่งสินค้า');

		//--- set report title header
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(11);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(11);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(9);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(7.5);
		$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(9.5);
		$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(6.5);
		$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
		$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(7.5);
		$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(11);

		$this->excel->getDefaultStyle()->getFont()->setName('Cordia New');
		$this->excel->getDefaultStyle()->getFont()->setSize(14);


		$this->excel->getActiveSheet()->setCellValue('A1', "วันที่......./......./.......");
		$this->excel->getActiveSheet()->setCellValue('D1', 'ใบรายงานการส่งสินค้า  Delivery  Manifest  (สินค้าแพ็คเย็น)');
		$this->excel->getActiveSheet()->mergeCells('D1:M1');
		$this->excel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->setCellValue('N1', 'หน้าแรก');

		$this->excel->getActiveSheet()->setCellValue('A2', "รหัสลูกค้า........");
		$this->excel->getActiveSheet()->setCellValue('D2', "ผู้ส่งสินค้า/บริษัท.......".getConfig('COMPANY_FULL_NAME')."......"." เลขที่ผู้เสียภาษี ".getConfig('COMAPAY_TAX_ID'));
		$this->excel->getActiveSheet()->mergeCells('D2:M2');
		$this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->setCellValue('N2', 'ภาค..........');

		$this->excel->getActiveSheet()->setCellValue('A3', "No.");
		$this->excel->getActiveSheet()->setCellValue('D3', "ที่อยู่ผู้ส่ง  ".getConfig('COMPANY_ADDRESS1')." ".GetConfig('COMPANY_ADDRESS2')."  ".getConfig('COMPANY_POST_CODE')."  เบอร์ติดต่อ ".getConfig('COMPANY_PHONE'));
		$this->excel->getActiveSheet()->mergeCells('D3:M3');
		$this->excel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->setCellValue('N3', 'แผ่นที่...../.....');

		$this->excel->getActiveSheet()->getStyle('D1:D3')->getFont()->setSize(16);
		$this->excel->getActiveSheet()->getStyle('D1:D3')->getFont()->setBold(TRUE);


		$this->excel->getActiveSheet()->setCellValue('A4', "สำหรับลูกค้าที่มาใช้บริการครั้งแรก");
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(12);
		$this->excel->getActiveSheet()->mergeCells('A4:B4');
		$this->excel->getActiveSheet()->setCellValue('D4', 'ท่านทราบข้อมูลการบริการจากที่ใด?
  󠆸󠆸 เว็บไซต์  󠆸󠆸 Line@   󠆸󠆸 Facebook   󠆸󠆸 Google   󠆸󠆸 ป้ายโฆษณา………………...……………... 󠆸󠆸 ออกบูธ…………….……...……………….  󠆸󠆸 อื่นๆ โปรดระบุ…………………………………….');
		$this->excel->getActiveSheet()->mergeCells('D4:O4');
		$this->excel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(TRUE);
		$this->excel->getActiveSheet()->getRowDimension('4')->setRowHeight(40);
		$this->excel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setVertical('center');


		$this->excel->getActiveSheet()->setCellValue('A5', "*** หากท่านไม่ทราบวิธีคิดขนาดกล่อง  กรุณาระบุน้ำหนักต่อกล่องในช่องน้ำหนักและปริมาตร กว้าง X ยาว X สูง (เซ็นติเมตร) ในช่องหมายเหตุ ***");
		$this->excel->getActiveSheet()->mergeCells('A5:O5');
		$this->excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal('center');

		$this->excel->getActiveSheet()->setCellValue('A6', 'ลำดับ');
		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('B6', 'เลขที่บิล');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		$this->excel->getActiveSheet()->getStyle('B6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('C6', 'รหัสสาขา');
		$this->excel->getActiveSheet()->mergeCells('C6:C7');
		$this->excel->getActiveSheet()->getStyle('C6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('D6', 'ชื่อผู้รับสินค้า');
		$this->excel->getActiveSheet()->mergeCells('D6:D7');
		$this->excel->getActiveSheet()->getStyle('D6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('E6', 'เบอร์โทรศัพท์');
		$this->excel->getActiveSheet()->mergeCells('E6:E7');
		$this->excel->getActiveSheet()->getStyle('E6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('F6', 'ที่อยู่');
		$this->excel->getActiveSheet()->mergeCells('F6:F7');
		$this->excel->getActiveSheet()->getStyle('F6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('G6', 'อำเภอ');
		$this->excel->getActiveSheet()->mergeCells('G6:G7');
		$this->excel->getActiveSheet()->getStyle('G6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('H6', 'จังหวัด');
		$this->excel->getActiveSheet()->mergeCells('H6:H7');
		$this->excel->getActiveSheet()->getStyle('H6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('I6', 'รหัสไปรษณีย์');
		$this->excel->getActiveSheet()->mergeCells('I6:I7');
		$this->excel->getActiveSheet()->getStyle('I6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('I6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('I6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('J6', 'ประเภท'.PHP_EOL.'สินค้า');
		$this->excel->getActiveSheet()->mergeCells('J6:J7');
		$this->excel->getActiveSheet()->getStyle("J6:J7")->getAlignment()->setWrapText(TRUE);
		$this->excel->getActiveSheet()->getStyle('J6:J7')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('J6:J7')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('J6:J7')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('K6', 'ประเภท'.PHP_EOL.'การแพ็ค');
		$this->excel->getActiveSheet()->mergeCells('K6:K7');
		$this->excel->getActiveSheet()->getStyle("K6:K7")->getAlignment()->setWrapText(TRUE);
		$this->excel->getActiveSheet()->getStyle('K6:K7')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('K6:K7')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('K6:K7')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('L6', 'จำนวน'.PHP_EOL.'กล่อง');
		$this->excel->getActiveSheet()->mergeCells('L6:L7');
		$this->excel->getActiveSheet()->getStyle("L6:L7")->getAlignment()->setWrapText(TRUE);
		$this->excel->getActiveSheet()->getStyle('L6:L7')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('L6:L7')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('L6:L7')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('M6', 'ขนาด');
		$this->excel->getActiveSheet()->mergeCells('M6:M7');
		$this->excel->getActiveSheet()->getStyle('M6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('M6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('M6')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('N6', 'นน.ต่อ'.PHP_EOL.'กล่อง');
		$this->excel->getActiveSheet()->mergeCells('N6:N7');
		$this->excel->getActiveSheet()->getStyle("N6:N7")->getAlignment()->setWrapText(TRUE);
		$this->excel->getActiveSheet()->getStyle('N6:N7')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('N6:N7')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('N6:N7')->getAlignment()->setVertical('center');

		$this->excel->getActiveSheet()->setCellValue('O6', 'หมายเหตุ');
		$this->excel->getActiveSheet()->mergeCells('O6:O7');
		$this->excel->getActiveSheet()->getStyle('O6')->getFont()->setBold(TRUE);
		$this->excel->getActiveSheet()->getStyle('O6')->getAlignment()->setHorizontal('center');
		$this->excel->getActiveSheet()->getStyle('O6')->getAlignment()->setVertical('center');

		$box_size = "S    ⃣     A    ⃣     B1    ⃣".PHP_EOL."B2    ⃣    C    ⃣";
		$pack_type = "Chill    ⃣".PHP_EOL."Frozen    ⃣";

		$border_style = array(
			"borders" => array(
				"outline" => array(
					"style" => PHPExcel_Style_Border::BORDER_THIN,
					"color" => array('rgb' => '000000')
				)
			)
		);

		$all_border = array(
			"borders" => array(
				"allborders" => array(
					"style" => PHPExcel_Style_Border::BORDER_THIN,
					"color" => array("rgb" => "000000")
				)
			)
		);

		$this->excel->getActiveSheet()->getStyle('A1:B3')->applyFromArray($border_style);
		$this->excel->getActiveSheet()->getStyle('C1:C3')->applyFromArray($border_style);
		$this->excel->getActiveSheet()->getStyle('D1:M3')->applyFromArray($border_style);
		$this->excel->getActiveSheet()->getStyle('N1:O1')->applyFromArray($border_style);
		$this->excel->getActiveSheet()->getStyle('N2:O2')->applyFromArray($border_style);
		$this->excel->getActiveSheet()->getStyle('N3:O3')->applyFromArray($border_style);
		$this->excel->getActiveSheet()->getStyle('A4:O4')->applyFromArray($all_border);
		$this->excel->getActiveSheet()->getStyle('A5')->applyFromArray($border_style);

		$list = $this->input->post('code');

		if(! empty($list))
		{
			$addr = array();

			foreach($list as $order_code)
			{
				$order = $this->orders_model->get($order_code);

				if( ! empty($order))
				{
					$adr = $this->get_address($order->address_id, $order->customer_ref, $order->customer_code);
					$arr = new stdClass();

					$arr->order_code = $order->code;
					$arr->remark = $order->remark;
					$arr->address = $adr;

					$addr[] = $arr;
				}
			}

			$row = 7;

			if(!empty($addr))
			{

				$no = 1;

				foreach($addr as $rs)
				{
					$row++;
					$address = empty($rs->address->address) ? "" : $rs->address->address;;
					$address .= ! empty($rs->address->sub_district) ? " ต.{$rs->address->sub_district}" : "";

					$this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
					$this->excel->getActiveSheet()->setCellValue("B{$row}", $rs->order_code);
					$this->excel->getActiveSheet()->setCellValueExplicit("C{$row}", $rs->address->code, PHPExcel_Cell_DataType::TYPE_STRING);
					$this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->address->name);
					$this->excel->getActiveSheet()->setCellValueExplicit("E{$row}", $rs->address->phone, PHPExcel_Cell_DataType::TYPE_STRING);
					$this->excel->getActiveSheet()->setCellValue("F{$row}", $address);
					$this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->address->district);
					$this->excel->getActiveSheet()->setCellValue("H{$row}", $rs->address->province);
					$this->excel->getActiveSheet()->setCellValueExplicit("I{$row}", $rs->address->postcode, PHPExcel_Cell_DataType::TYPE_STRING);
					$this->excel->getActiveSheet()->getStyle("K{$row}")->getFont()->setSize(10);
					$this->excel->getActiveSheet()->getStyle("K{$row}")->getAlignment()->setWrapText(TRUE);
					$this->excel->getActiveSheet()->getStyle("K{$row}")->getAlignment()->setVertical("center");
					$this->excel->getActiveSheet()->setCellValue("K{$row}", $pack_type);
					$this->excel->getActiveSheet()->getStyle("M{$row}")->getFont()->setName('Calibri');
					$this->excel->getActiveSheet()->getStyle("M{$row}")->getFont()->setSize(8);
					$this->excel->getActiveSheet()->getStyle("M{$row}")->getAlignment()->setWrapText(TRUE);
					$this->excel->getActiveSheet()->getStyle("M{$row}")->getAlignment()->setVertical("center");
					$this->excel->getActiveSheet()->setCellValue("M{$row}", $box_size);
					$this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->remark);

					$no++;
				}

				$this->excel->getActiveSheet()->getStyle("A6:O{$row}")->applyFromArray($all_border);
				$this->excel->getActiveSheet()->getStyle("A6:A{$row}")->getAlignment()->setHorizontal("center");
				$this->excel->getActiveSheet()->getStyle("A6:O{$row}")->getAlignment()->setVertical("center");
			}
		}

		$qs = $this->db
		->select('product_code, product_name, unit_code')
		->select_sum('qty')
		->where_in('order_code', $list)
		->where('is_count', 1)
		->group_by('product_code')
		->get('order_details');

		if( ! empty($qs))
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "ItemSummary");
			$this->excel->addSheet($worksheet, 1);
			$this->excel->setActiveSheetIndex(1);

			$row = 1;
			$this->excel->getActiveSheet()->setCellValue("A{$row}", "ProductCode");
			$this->excel->getActiveSheet()->setCellValue("B{$row}", "ProductName");
			$this->excel->getActiveSheet()->setCellValue("C{$row}", "AMOUNT");
			$this->excel->getActiveSheet()->setCellValue("D{$row}", "UNIT");
			$this->excel->getActiveSheet()->getStyle("A{$row}:D{$row}")->getFont()->setName('Tahoma')->setSize(10);
			$this->excel->getActiveSheet()->getStyle("A{$row}:D{$row}")->getFont()->getColor()->setARGB("203764");
			$this->excel->getActiveSheet()->getStyle("A{$row}:D{$row}")->getFont()->setBold(TRUE);
			$this->excel->getActiveSheet()->getStyle("A{$row}:D{$row}")->getAlignment()->setVertical("center");
			$this->excel->getActiveSheet()->getStyle("A{$row}:D{$row}")->getAlignment()->setHorizontal("center");

			$this->excel
			->getActiveSheet()
			->getStyle("A{$row}:D{$row}")
			->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setARGB('D9E1F2');

			$this->excel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension("B")->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension("C")->setWidth(12);
			$this->excel->getActiveSheet()->getColumnDimension("D")->setWidth(5);

			foreach($qs->result() as $rs)
			{
				$row++;
				$time = gmmktime(0,0,0, date('m'), date('d'), date('Y'));
				$this->excel->getActiveSheet()->setCellValueExplicit("A{$row}", $rs->product_code, PHPExcel_Cell_DataType::TYPE_STRING);
				$this->excel->getActiveSheet()->setCellValue("B{$row}", $rs->product_name);
				$this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->qty);
				$this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->unit_code);

				$this->excel
				->getActiveSheet()
				->getStyle("A{$row}:D{$row}")
				->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()->setARGB('F2F2F2');
			}
		}



		if(!empty($list))
		{
			$i = 1;
			$index = 2;

			foreach($list as $order_code)
			{
				$order = $this->orders_model->get($order_code);

				if(!empty($order))
				{
					$adr = $this->get_address($order->address_id, $order->customer_ref, $order->customer_code);
					$details = $this->orders_model->get_order_details($order->code);

					if( ! empty($details))
					{
						$worksheet = new PHPExcel_Worksheet($this->excel, "{$i}");
						$this->excel->addSheet($worksheet, $index);
						$this->excel->setActiveSheetIndex($index);

						$row = 1;
						$this->excel->getActiveSheet()->setCellValue("A{$row}", "sale order no");
						$this->excel->getActiveSheet()->setCellValue("B{$row}", "DELIVERY DATE");
						$this->excel->getActiveSheet()->setCellValue("C{$row}", "SHIP TO");
						$this->excel->getActiveSheet()->setCellValue("D{$row}", "BRANCH");
						$this->excel->getActiveSheet()->setCellValue("F{$row}", "Productcode");
						$this->excel->getActiveSheet()->setCellValue("G{$row}", "PRODUCT NAME");
						$this->excel->getActiveSheet()->setCellValue("H{$row}", "AMOUNT");
						$this->excel->getActiveSheet()->setCellValue("I{$row}", "UNIT");
						$this->excel->getActiveSheet()->getStyle("A{$row}:I{$row}")->getFont()->setName('Tahoma')->setSize(10);
						$this->excel->getActiveSheet()->getStyle("A{$row}:I{$row}")->getFont()->getColor()->setARGB("203764");
						$this->excel->getActiveSheet()->getStyle("A{$row}:I{$row}")->getFont()->setBold(TRUE);
						$this->excel->getActiveSheet()->getStyle("A{$row}:I{$row}")->getAlignment()->setVertical("center");
						$this->excel->getActiveSheet()->getStyle("A{$row}:I{$row}")->getAlignment()->setHorizontal("center");

						$this->excel
						->getActiveSheet()
						->getStyle("A{$row}:I{$row}")
						->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()->setARGB('D9E1F2');

						$this->excel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
						$this->excel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
						$this->excel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
						$this->excel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
						$this->excel->getActiveSheet()->getColumnDimension("E")->setWidth(12);
						$this->excel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
						$this->excel->getActiveSheet()->getColumnDimension("G")->setWidth(30);
						$this->excel->getActiveSheet()->getColumnDimension("H")->setWidth(12);
						$this->excel->getActiveSheet()->getColumnDimension("I")->setWidth(5);


						foreach($details as $detail)
						{
							if($detail->is_count == 1)
							{
								$row++;
								$time = gmmktime(0,0,0, date('m'), date('d'), date('Y'));
								$this->excel->getActiveSheet()->setCellValue("A{$row}", $detail->order_code);
								$this->excel->getActiveSheet()->setCellValue("B{$row}", PHPExcel_Shared_Date::PHPToExcel($time));
								$this->excel->getActiveSheet()->setCellValue("C{$row}", (empty($adr->code) ? $order->customer_code : $adr->code));
								$this->excel->getActiveSheet()->setCellValue("D{$row}", $order->customer_name);
								$this->excel->getActiveSheet()->setCellValueExplicit("F{$row}", $detail->product_code, PHPExcel_Cell_DataType::TYPE_STRING);
								$this->excel->getActiveSheet()->setCellValue("G{$row}", $detail->product_name);
								$this->excel->getActiveSheet()->setCellValue("H{$row}", $detail->qty);
								$this->excel->getActiveSheet()->setCellValue("I{$row}", $detail->unit_code);
								$this->excel->getActiveSheet()->getStyle("B{$row}")->getNumberFormat()->setFormatCode("dd/m/yyyy");
								$this->excel->getActiveSheet()->getStyle("A{$row}:I{$row}")->getFont()->setName('Arial')->setSize(10);
								$this->excel
								->getActiveSheet()
								->getStyle("A{$row}:I{$row}")
								->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
								->getStartColor()->setARGB('F2F2F2');
							}
						}

						$border_style = array(
							"borders" => array(
								"allborders" => array(
									"style" => PHPExcel_Style_Border::BORDER_THIN,
									"color" => array('rgb' => '000000')
								)
							)
						);
						$this->excel->getActiveSheet()->getStyle("A1:I{$row}")->applyFromArray($border_style);

						$index++;
						$i++;
					}
				}
			}

			$this->excel->setActiveSheetIndex(0);
		}


	setToken($token);

		$file_name = "Delivery_slip.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }



} //--- end class








 ?>
