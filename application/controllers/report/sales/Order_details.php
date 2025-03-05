<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_details extends PS_Controller
{
  public $menu_code = 'RSSODE';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงาน ใบสั่งขาย แสดงรายการสินค้า';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/order_details';
    $this->load->model('report/sales/sales_report_model');
    $this->load->helper('channels');
  }

  public function index()
  {
    $this->load->view('report/sales/order_details');
  }


  public function get_report()
  {
    $allProduct = $this->input->get('allProduct');
    $pdFrom = $this->input->get('pdFrom');
    $pdTo = $this->input->get('pdTo');

    $fromDate = $this->input->get('fromDate');
    $toDate = $this->input->get('toDate');

		$orderBy = empty($this->input->get('orderBy')) ? 'amount' : $this->input->get('orderBy');

    //---  Report title
    $sc['reportDate'] = thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $sc['pdList']   = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';


    $ds = array(
      'allProduct' => is_true($allProduct),
      'pdFrom' => $pdFrom,
      'pdTo' => $pdTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate),
			'orderBy' => $orderBy
    );

    $result = $this->sales_report_model->get_sum_item_sales_by_date_upd($ds);

    $bs = array();

    if(!empty($result))
    {
      $no = 1;
      $totalQty = 0;
      $totalAmount = 0;
      foreach($result as $rs)
      {
        $arr = array(
          'no' => number($no),
          'pdName' => $rs->product_name,
          'pdCode' => $rs->product_code,
          'price' => number($rs->price, 2),
          'qty' => number($rs->qty),
          'amount' => number($rs->amount, 2)
        );

        array_push($bs, $arr);
        $no++;

        $totalQty += $rs->qty;
        $totalAmount += $rs->amount;
      }

      $arr = array(
        'totalQty' => number($totalQty),
        'totalAmount' => number($totalAmount, 2)
      );

      array_push($bs, $arr);

      $bs;
    }
    else
    {
      $arr = array('nodata' => 'nodata');
      array_push($bs, $arr);
    }

    $sc['bs'] = $bs;

    echo json_encode($sc);
  }


  public function do_export()
  {
    $allProduct = $this->input->post('allProduct');
    $pdFrom = $this->input->post('pdFrom');
    $pdTo = $this->input->post('pdTo');

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

		$orderBy = empty($this->input->post('orderBy')) ? 'amount' : $this->input->post('orderBy');

		$token = $this->input->post('token');

    //---  Report title
    $report_title = 'รายงานยอดขาย แยกตามสินค้า';
    $date_title = 'วันที่ : '.thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $pd_title = 'สินค้า :  '. ($allProduct == 1 ? 'ทั้งหมด' : $pdFrom.' - '.$pdTo);


    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales By Items');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:I1');
    $this->excel->getActiveSheet()->setCellValue('A2', $date_title);
    $this->excel->getActiveSheet()->mergeCells('A2:I2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:I3');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B4', 'รหัส');
    $this->excel->getActiveSheet()->setCellValue('C4', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('D4', 'ราคา(Vat exclude)');
    $this->excel->getActiveSheet()->setCellValue('E4', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('F4', 'มูลค่า(Vat exclude)');

    $row = 5;

		$ds = array(
      'allProduct' => is_true($allProduct),
      'pdFrom' => $pdFrom,
      'pdTo' => $pdTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate),
			'orderBy' => $orderBy
    );

    $result = $this->sales_report_model->get_sum_item_sales_by_date_upd($ds);

    if(!empty($result))
    {
      $no = 1;
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->product_name);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->price);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->amount);

        $no++;
        $row++;
      }

      $res = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
      $this->excel->getActiveSheet()->setCellValue('E'.$row, '=SUM(E5:E'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F5:F'.$res.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('E5:E'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('E5:E'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }

		setToken($token);
    $file_name = "Report Sales by product.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
