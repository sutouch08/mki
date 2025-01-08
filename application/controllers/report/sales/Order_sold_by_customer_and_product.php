<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_sold_by_customer_and_product extends PS_Controller
{
  public $menu_code = 'RSCSPD';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานยอดขาย แยกตามลูกค้า แสดงรายการสินค้า';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/order_sold_by_customer_and_product';
    $this->load->model('report/sales/sales_report_model');
  }

  public function index()
  {
    $this->load->view('report/sales/order_sold_by_customer_and_product');
  }


  public function get_report()
  {
    // $allProduct = $this->input->get('allProduct');
    // $pdFrom = $this->input->get('pdFrom');
    // $pdTo = $this->input->get('pdTo');

    $allCustomer = $this->input->get('allCustomer');
    $cusFrom = $this->input->get('cusFrom');
    $cusTo = $this->input->get('cusTo');

    $fromDate = $this->input->get('fromDate');
    $toDate = $this->input->get('toDate');

    //---  Report title
    $sc['reportDate'] = thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $sc['cusList']   = $allCustomer == 1 ? 'ทั้งหมด' : '('.$cusFrom.') - ('.$cusTo.')';
    // $sc['productList']   = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';


    $ds = array(
      // 'allProduct' => is_true($allProduct),
      // 'pdFrom' => $pdFrom,
      // 'pdTo' => $pdTo,
      'allCustomer' => is_true($allCustomer),
      'cusFrom' => $cusFrom,
      'cusTo' => $cusTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate)
    );

    $result = $this->sales_report_model->get_order_sold_by_date_upd($ds);

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
          'date_upd' => thai_date($rs->date_upd, FALSE, '/'),
          'reference' => $rs->reference,
          'cusName' => $rs->customer_name,
          'pdCode' => $rs->product_code,
          'pdName' => $rs->product_name,
          'price' => number($rs->price, 2),
          'discount' => $rs->discount_label,
          'qty' => number($rs->qty),
          'amount' => number($rs->total_amount, 2)
        );

        array_push($bs, $arr);
        $no++;

        $totalQty += $rs->qty;
        $totalAmount += $rs->total_amount;
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
    $allCustomer = $this->input->post('allCustomer');
    $cusFrom = $this->input->post('cusFrom');
    $cusTo = $this->input->post('cusTo');

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

    //---  Report title
    $report_title = 'รายงานยอดขาย แยกตามลูกค้า แสดงรายการสินค้า';
    $date_title = 'วันที่ : '.thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $cus_title = 'ลูกค้า :  '. ($allCustomer == 1 ? 'ทั้งหมด' : $cusFrom.' - '.$cusTo);


    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales By Customer Show Items');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:I1');
    $this->excel->getActiveSheet()->setCellValue('A2', $date_title);
    $this->excel->getActiveSheet()->mergeCells('A2:I2');
    $this->excel->getActiveSheet()->setCellValue('A3', $cus_title);
    $this->excel->getActiveSheet()->mergeCells('A3:I3');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B4', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C4', 'ลูกค้า');
    $this->excel->getActiveSheet()->setCellValue('D4', 'เลขที่เอกสาร');
    $this->excel->getActiveSheet()->setCellValue('E4', 'รหัส');
    $this->excel->getActiveSheet()->setCellValue('F4', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('G4', 'ราคา');
    $this->excel->getActiveSheet()->setCellValue('H4', 'ส่วนลด');
    $this->excel->getActiveSheet()->setCellValue('I4', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('J4', 'มูลค่า');

    $row = 5;

    $ds = array(
      'allCustomer' => is_true($allCustomer),
      'cusFrom' => $cusFrom,
      'cusTo' => $cusTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate)
    );

    $result = $this->sales_report_model->get_order_sold_by_date_upd($ds);

    if(!empty($result))
    {
      $no = 1;
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_upd, FALSE, '/'));
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->customer_name);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->product_name);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, number($rs->price, 2));
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->discount_label);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, number($rs->qty));
        $this->excel->getActiveSheet()->setCellValue('J'.$row, number($rs->total_amount));
        $no++;
        $row++;
      }

      $res = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
      $this->excel->getActiveSheet()->setCellValue('I'.$row, '=SUM(I5:I'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('J'.$row, '=SUM(J5:J'.$res.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('G5:G'.$row)->getAlignment()->setHorizontal('center');
      $this->excel->getActiveSheet()->getStyle('G5:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
      $this->excel->getActiveSheet()->getStyle('I5:J'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('I5:I'.$row)->getNumberFormat()->setFormatCode('0');
      $this->excel->getActiveSheet()->getStyle('J5:J'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }


    $file_name = "Report Sales by customer show items.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
