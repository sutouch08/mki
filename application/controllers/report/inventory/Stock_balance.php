<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_balance extends PS_Controller
{
  public $menu_code = 'RICSTB';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือ';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/stock_balance';
    $this->load->model('report/inventory/stock_balance_model');
    $this->load->model('masters/warehouse_model');
  }

  public function index()
  {
    $whList = $this->warehouse_model->get_all_warehouse();
    $ds['whList'] = $whList;
    $this->load->view('report/inventory/stock_balance_report', $ds);
  }


  public function get_report()
  {
    $allProduct = $this->input->get('allProduct');
    $pdFrom = $this->input->get('pdFrom');
    $pdTo = $this->input->get('pdTo');

    $allWhouse = $this->input->get('allWhouse');
    $warehouse = $this->input->get('warehouse');

    $date = $this->input->get('date') ? from_date($this->input->get('date')) : from_date(now());
    $today = from_date(now());
    $currentDate = $date == $today ? 1 : 0;

    $wh_list = '';

    if(!empty($warehouse))
    {
      $i = 1;
      foreach($warehouse as $wh)
      {
        $whsName = $this->warehouse_model->get_name($wh);

        $wh_list .= $i === 1 ? $wh.' : '.$whsName : ', '.$wh.' : '.$whsName;
        $i++;
      }
    }

    //---  Report title
    $sc['reportDate'] = $currentDate == 0 ? thai_date($date,FALSE, '/') : thai_date(date('Y-m-d'),FALSE, '/');
    $sc['whList']   = $allWhouse == 1 ? 'ทั้งหมด' : $wh_list;
    $sc['productList']   = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';


    if($currentDate == 1)
    {
      $result = $this->stock_balance_model->get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse);
    }
    else
    {
      $result = $this->stock_balance_model->get_stock_balance_prev_date($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $date);
    }

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
          'barcode' => $rs->barcode,
          'pdCode' => $rs->code,
          'pdName' => $rs->name,
          'cost' => number($rs->cost, 2),
          'qty' => number($rs->qty),
          'unit' => $rs->unit_name,
          'amount' => number($rs->cost * $rs->qty, 2)
        );

        array_push($bs, $arr);
        $no++;

        $totalQty += $rs->qty;
        $totalAmount += ($rs->qty * $rs->cost);
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
    $token = $this->input->post('token');
    $allProduct = $this->input->post('allProduct');
    $pdFrom = $this->input->post('pdFrom');
    $pdTo = $this->input->post('pdTo');

    $allWhouse = $this->input->post('allWhouse');
    $warehouse = $this->input->post('warehouse');

    $date = $this->input->post('date') ? from_date($this->input->post('date')) : from_date(now());
    $today = from_date(now());
    $currentDate = $date == $today ? 1 : 0;

    $wh_list = '';

    if(!empty($warehouse))
    {
      $i = 1;
      foreach($warehouse as $wh)
      {
        $whsName = $this->warehouse_model->get_name($wh);

        $wh_list .= $i === 1 ? $wh.' : '.$whsName : ', '.$wh.' : '.$whsName;
        $i++;
      }
    }


    //---  Report title
    $report_title = 'รายงานสินค้าคงเหลือ ณ วันที่  '.thai_date(date('Y-m-d'), '/').'      (  วันที่พิมพ์รายงาน : '.date('d/m/Y').'  เวลา : '.date('H:i:s').' )';
    $wh_title     = 'คลัง :  '. ($allWhouse == 1 ? 'ทั้งหมด' : $wh_list);
    $pd_title     = 'สินค้า :  '. ($allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')');

    if($currentDate == 1)
    {
      $result = $this->stock_balance_model->get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse);
    }
    else
    {
      $result = $this->stock_balance_model->get_stock_balance_prev_date($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $date);
    }

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Stock Balance Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:H1');
    $this->excel->getActiveSheet()->setCellValue('A2', $wh_title);
    $this->excel->getActiveSheet()->mergeCells('A2:H2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:H3');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B4', 'บาร์โค้ด');
    $this->excel->getActiveSheet()->setCellValue('C4', 'รหัส');
    $this->excel->getActiveSheet()->setCellValue('D4', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('E4', 'หน่วยนับ');
    $this->excel->getActiveSheet()->setCellValue('F4', 'ทุน');
    $this->excel->getActiveSheet()->setCellValue('G4', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('H4', 'มูลค่า');

    $row = 5;
    if(!empty($result))
    {
      $no = 1;
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->barcode);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->name);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->unit_name);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->cost);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, '=F'.$row.'*G'.$row);
        $no++;
        $row++;
      }

      $res = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
      $this->excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G5:G'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('H'.$row, '=SUM(H5:H'.$res.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('B5:B'.$res)->getNumberFormat()->setFormatCode('0');
      $this->excel->getActiveSheet()->getStyle('G5:H'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('G5:H'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('H5:H'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    setToken($token);

    $file_name = "Report Stock Balance.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
