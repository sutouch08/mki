<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_sold_details extends PS_Controller
{
  public $menu_code = 'RSDEPA';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานวิเคราะห์ขายแบบละเอีด';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/order_sold_details';
    $this->load->model('report/sales/sales_report_model');
  }

  public function index()
  {
    $this->load->view('report/sales/order_sold_details');
  }



  public function do_export()
  {
    $role = $this->input->post('role');

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

		$token = $this->input->post('token');

    //---  Report title
    $report_title = 'รายงานวิเคราะห์ขายแบบละเอียด';
    $date_title = 'วันที่ : '.thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $role_title = 'ประเภท : '.($role == 'all' ? 'ขาย, ฝากขาย' : ($role == 'S' ? 'เฉพาะขาย' : 'เฉพาะฝากขาย'));


    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Order sales details');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->setCellValue('A2', $date_title);
    $this->excel->getActiveSheet()->setCellValue('A3', $role_title);

		$row = 4;
		//--------- Report Table header
		$this->excel->getActiveSheet()->setCellValue("A{$row}", 'date');
		$this->excel->getActiveSheet()->setCellValue("B{$row}", 'reference');
		$this->excel->getActiveSheet()->setCellValue("C{$row}", 'invoice');
		$this->excel->getActiveSheet()->setCellValue("D{$row}", 'item code');
		$this->excel->getActiveSheet()->setCellValue("E{$row}", 'item name');
		$this->excel->getActiveSheet()->setCellValue("F{$row}", 'color');
		$this->excel->getActiveSheet()->setCellValue("G{$row}", 'size');
		$this->excel->getActiveSheet()->setCellValue("H{$row}", 'model');
		$this->excel->getActiveSheet()->setCellValue("I{$row}", 'item group');
    $this->excel->getActiveSheet()->setCellValue("J{$row}", 'item sub group');
		$this->excel->getActiveSheet()->setCellValue("K{$row}", 'item category');
		$this->excel->getActiveSheet()->setCellValue("L{$row}", 'item kind');
		$this->excel->getActiveSheet()->setCellValue("M{$row}", 'item type');
		$this->excel->getActiveSheet()->setCellValue("N{$row}", 'item brand');
		$this->excel->getActiveSheet()->setCellValue("O{$row}", 'item year');
		$this->excel->getActiveSheet()->setCellValue("P{$row}", 'channels');
		$this->excel->getActiveSheet()->setCellValue("Q{$row}", 'payment');
		$this->excel->getActiveSheet()->setCellValue("R{$row}", 'cost (ex)');
		$this->excel->getActiveSheet()->setCellValue("S{$row}", 'price (inc)');
		$this->excel->getActiveSheet()->setCellValue("T{$row}", 'price (ex)');
		$this->excel->getActiveSheet()->setCellValue("U{$row}", 'qty');
		$this->excel->getActiveSheet()->setCellValue("V{$row}", 'item discount');
		$this->excel->getActiveSheet()->setCellValue("W{$row}", 'bill discount');
		$this->excel->getActiveSheet()->setCellValue("X{$row}", 'total discount');
		$this->excel->getActiveSheet()->setCellValue("Y{$row}", 'total amount (inc)');
		$this->excel->getActiveSheet()->setCellValue("Z{$row}", 'total amount (ex)');
		$this->excel->getActiveSheet()->setCellValue("AA{$row}", 'total vat');
		$this->excel->getActiveSheet()->setCellValue("AB{$row}", 'total cost (ex)');
		$this->excel->getActiveSheet()->setCellValue("AC{$row}", 'margin');
		$this->excel->getActiveSheet()->setCellValue("AD{$row}", 'customer name');
		$this->excel->getActiveSheet()->setCellValue("AE{$row}", 'customer group');
		$this->excel->getActiveSheet()->setCellValue("AF{$row}", 'customer kind');
		$this->excel->getActiveSheet()->setCellValue("AG{$row}", 'customer type');
		$this->excel->getActiveSheet()->setCellValue("AH{$row}", 'customer grade');
		$this->excel->getActiveSheet()->setCellValue("AI{$row}", 'customer area');
		$this->excel->getActiveSheet()->setCellValue("AJ{$row}", 'customer saleman');
		$this->excel->getActiveSheet()->setCellValue("AK{$row}", 'Warehouse');
		$this->excel->getActiveSheet()->setCellValue("AL{$row}", 'Zone');



    $row = 5;

    $ds = array(
			'role' => $role,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate)
    );

    $result = $this->sales_report_model->get_sold_details($ds);

    if(!empty($result))
    {
      foreach($result as $rs)
      {
				$this->excel->getActiveSheet()->setCellValue("A{$row}", thai_date($rs->date_add, FALSE, '/'));
				$this->excel->getActiveSheet()->setCellValue("B{$row}", $rs->reference);
				$this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->inv_code);
        $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->product_code);
				$this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->product_name);
				$this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->color_name);
				$this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->size_name);
				$this->excel->getActiveSheet()->setCellValue("H{$row}", $rs->product_style);
				$this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->product_group_name);
        $this->excel->getActiveSheet()->setCellValue("J{$row}", $rs->product_sub_group_name);
				$this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->product_category_name);
				$this->excel->getActiveSheet()->setCellValue("L{$row}", $rs->product_kind_name);
				$this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->product_type_name);
				$this->excel->getActiveSheet()->setCellValue("N{$row}", $rs->product_brand_name);
				$this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->product_year);
				$this->excel->getActiveSheet()->setCellValue("P{$row}", $rs->channels_name);
				$this->excel->getActiveSheet()->setCellValue("Q{$row}", $rs->payment_name);
				$this->excel->getActiveSheet()->setCellValue("R{$row}", $rs->cost);
				$this->excel->getActiveSheet()->setCellValue("S{$row}", $rs->price);
				$this->excel->getActiveSheet()->setCellValue("T{$row}", $rs->price_ex);
				$this->excel->getActiveSheet()->setCellValue("U{$row}", $rs->qty);
				$this->excel->getActiveSheet()->setCellValue("V{$row}", $rs->discount_label);
				$this->excel->getActiveSheet()->setCellValue("W{$row}", $rs->avgBillDiscAmount);
				$this->excel->getActiveSheet()->setCellValue("X{$row}", $rs->discount_amount);
				$this->excel->getActiveSheet()->setCellValue("Y{$row}", $rs->total_amount);
				$this->excel->getActiveSheet()->setCellValue("Z{$row}", $rs->total_amount_ex);
				$this->excel->getActiveSheet()->setCellValue("AA{$row}", $rs->vat_amount);
				$this->excel->getActiveSheet()->setCellValue("AB{$row}", $rs->total_cost);
				$this->excel->getActiveSheet()->setCellValue("AC{$row}", $rs->margin);
				$this->excel->getActiveSheet()->setCellValue("AD{$row}", $rs->customer_name);
				$this->excel->getActiveSheet()->setCellValue("AE{$row}", $rs->customer_group_name);
				$this->excel->getActiveSheet()->setCellValue("AF{$row}", $rs->customer_kind_name);
				$this->excel->getActiveSheet()->setCellValue("AG{$row}", $rs->customer_type_name);
				$this->excel->getActiveSheet()->setCellValue("AH{$row}", $rs->customer_class_name);
				$this->excel->getActiveSheet()->setCellValue("AI{$row}", $rs->customer_area_name);
				$this->excel->getActiveSheet()->setCellValue("AJ{$row}", $rs->sale_name);
				$this->excel->getActiveSheet()->setCellValue("AK{$row}", $rs->warehouse_code);
				$this->excel->getActiveSheet()->setCellValue("AL{$row}", $rs->zone_code);
        $row++;
      }

    }

		setToken($token);

    $file_name = "Report Sales details.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
