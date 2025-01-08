<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_sold_by_customer_and_payment extends PS_Controller
{
  public $menu_code = 'RSCSBL';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานยอดขาย แยกตามลูกค้า แสดงยอดค้างรับ';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/order_sold_by_customer_and_payment';
    $this->load->model('report/sales/sales_report_model');
    $this->load->helper('channels');
    $this->load->helper('payment_method');
  }

  public function index()
  {
    $this->load->view('report/sales/order_sold_by_customer_and_payment');
  }


  public function get_report()
  {
    $allCustomer = $this->input->get('allCustomer');
    $cusFrom = $this->input->get('cusFrom');
    $cusTo = $this->input->get('cusTo');

    $fromDate = $this->input->get('fromDate');
    $toDate = $this->input->get('toDate');

    $channels = $this->input->get('channels');
    $payments = $this->input->get('payments');
    $options = $this->input->get('options');

    //---  Report title
    $sc['reportDate'] = thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $sc['cusList']   = $allCustomer == 1 ? 'ทั้งหมด' : '('.$cusFrom.') - ('.$cusTo.')';
    // $sc['productList']   = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';


    $ds = array(
      'allCustomer' => is_true($allCustomer),
      'cusFrom' => $cusFrom,
      'cusTo' => $cusTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate),
      'channels' => $channels,
      'payments' => $payments,
      'options' => $options
    );

    $result = $this->sales_report_model->get_order_sold_by_customer_and_payment($ds);

    $bs = array();

    if(!empty($result))
    {
      $no = 1;
      $totalAmount = 0;
      $totalPaid = 0;
      $totalBalance = 0;
      foreach($result as $rs)
      {
        $paid = ($rs->paid === NULL && $rs->balance === NULL) ? $rs->total_amount : $rs->paid;
        $balance = $rs->total_amount - $paid;
        $balance = $balance > 0 ? $balance : 0;
        $cusName = empty($rs->customer_ref) ? $rs->customer_name : $rs->customer_name . "({$rs->customer_ref})";
        $arr = array(
          'no' => number($no),
          'date_upd' => thai_date($rs->date_upd, FALSE, '/'),
          'reference' => $rs->reference,
          'cusName' => $cusName,
          'channels' => $rs->channels,
          'payments' => $rs->payment,
          'amount' => number($rs->total_amount, 2),
          'paid' =>  number($paid, 2),
          'balance' => number($balance, 2)
        );

        array_push($bs, $arr);
        $no++;

        $totalAmount += $rs->total_amount;
        $totalPaid += $paid;
        $totalBalance += $balance;
      }

      $arr = array(
        'totalAmount' => number($totalAmount, 2),
        'totalPaid' => number($totalPaid, 2),
        'totalBalance' => number($totalBalance, 2)
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
    $channels = $this->input->get('channels');
    $payments = $this->input->get('payments');
    $options = $this->input->get('options');

    //---  Report title
    $report_title = 'รายงานยอดขาย แยกตามลูกค้า แสดงยอดค้างรับ';
    $date_title = 'วันที่ : '.thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $cus_title = 'ลูกค้า :  '. ($allCustomer == 1 ? 'ทั้งหมด' : $cusFrom.' - '.$cusTo);


    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales By Customer Show Payments');

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
    $this->excel->getActiveSheet()->setCellValue('E4', 'ช่องทาง');
    $this->excel->getActiveSheet()->setCellValue('F4', 'การชำระเงิน');
    $this->excel->getActiveSheet()->setCellValue('G4', 'มูลค่า');
    $this->excel->getActiveSheet()->setCellValue('H4', 'รับแล้ว');
    $this->excel->getActiveSheet()->setCellValue('I4', 'ค้างรับ');

    $row = 5;

    $ds = array(
      'allCustomer' => is_true($allCustomer),
      'cusFrom' => $cusFrom,
      'cusTo' => $cusTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate),
      'channels' => $channels,
      'payments' => $payments,
      'options' => $options
    );

    $result = $this->sales_report_model->get_order_sold_by_date_upd($ds);

    if(!empty($result))
    {
      $no = 1;
      foreach($result as $rs)
      {
        $paid = ($rs->paid === NULL && $rs->balance === NULL) ? $rs->total_amount : $rs->paid;
        $balance = $rs->total_amount - $paid;
        $balance = $balance > 0 ? $balance : 0;
        $cusName = empty($rs->customer_ref) ? $rs->customer_name : $rs->customer_name . "({$rs->customer_ref})";

        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_upd, FALSE, '/'));
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $cusName);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->channels);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->payment);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->total_amount);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $paid);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $balance);
        $no++;
        $row++;
      }

      $res = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
      $this->excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G5:G'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('H'.$row, '=SUM(H5:H'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('I'.$row, '=SUM(I5:I'.$res.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('G5:I'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('G5:I'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }


    $file_name = "Report Sales by customer show items.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
