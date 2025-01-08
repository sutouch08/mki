<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Po_backlogs extends PS_Controller
{
  public $menu_code = 'RPUPOB';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REPO';
	public $title = 'รายงาน ใบสั่งซื้อค้างรับ';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/purchase/po_backlogs';
    $this->load->model('report/purchase/po_backlogs_model');
  }

  public function index()
  {
    $this->load->view('report/purchase/report_po_backlogs');
  }


  public function get_report()
  {
    if(!empty($this->input->get()))
    {
      $from_date = $this->input->get('fromDate') ? $this->input->get('fromDate') : '2020-01-01';
      $to_date = $this->input->get('toDate') ? $this->input->get('toDate') : now();
      $arr = array(
        'from_date' => from_date($from_date),
        'to_date' => to_date($to_date),
        'all_po' => $this->input->get('allPo'),
        'po_from' => $this->input->get('poFrom'),
        'po_to' => $this->input->get('poTo'),
        'po_status' => $this->input->get('status'),
        'all_vendor' => $this->input->get('allVendor'),
        'vendor_from' => $this->input->get('vendorFrom'),
        'vendor_to' => $this->input->get('vendorTo'),
        'is_item' => $this->input->get('isItem'),
        'all_product' => $this->input->get('allProduct'),
        'item_from' => $this->input->get('itemFrom'),
        'item_to' => $this->input->get('itemTo'),
        'style_from' => $this->input->get('styleFrom'),
        'style_to' => $this->input->get('styleTo')
      );

      $result = $this->po_backlogs_model->get_data($arr);
      $ds = array();
      if(!empty($result))
      {
        $total_qty = 0;
        $total_received = 0;
        $total_backlogs = 0;
        $no = 1;
        foreach($result as $rs)
        {
          $backlogs = $rs->qty - $rs->received;
          $backlogs = $backlogs > 0 ? $backlogs : 0;
          $arr = array(
            'no' => $no,
            'date' => thai_date($rs->date_add),
            'pdCode' => $rs->product_code,
            'poCode' => $rs->code,
            'vendor' => $rs->vender_name,
            'dueDate' => thai_date($rs->due_date),
            'qty' => number($rs->qty),
            'received' => number($rs->received),
            'backlogs' => number($backlogs),
            'status' => ($rs->status == 2 ? 'part' : ($rs->status == 3 ? 'closed' : ''))
          );

          array_push($ds, $arr);
          $total_qty += $rs->qty;
          $total_received += $rs->received;
          $total_backlogs += $backlogs;
          $no++;

        }

        $arr = array(
          'totalQty' => number($total_qty),
          'totalReceived' => number($total_received),
          'totalBacklogs' => number($total_backlogs)
        );

        array_push($ds, $arr);
      }
      else
      {
        $arr = array('nodata', 'nodata');
        array_push($ds, $arr);
      }

      echo json_encode($ds);
    }
    else
    {
      echo 'invalid request data';
    }
  }




  public function do_export()
  {
    $token = $this->input->post('token');
    $from_date = $this->input->post('fromDate') ? $this->input->post('fromDate') : '2020-01-01';
    $to_date = $this->input->post('toDate') ? $this->input->post('toDate') : now();

    $all_po = $this->input->post('allPo');
    $po_from = $this->input->post('poFrom');
    $po_to = $this->input->post('poTo');
    $po_status = $this->input->post('status');
    $all_vendor = $this->input->post('allVendor');
    $vendor_from = $this->input->post('vendorFrom');
    $vendor_to = $this->input->post('vendorTo');
    $is_item = $this->input->post('isItem');
    $all_product = $this->input->post('allProduct');
    $item_from = $this->input->post('itemFrom');
    $item_to = $this->input->post('itemTo');
    $style_from = $this->input->post('styleFrom');
    $style_to = $this->input->post('styleTo');

    $title = "รายงาน ใบสั่งซื้อค้างรับ วันที่ (".thai_date($from_date, FALSE, '/').") - (".thai_date($to_date, FALSE, '/').") วันที่พิมพ์รายงาน : ".thai_date(date('Y-m-d'));
    $vendor = $all_vendor == 1 ? 'ทั้งหมด' : "{$vendor_from} - {$vendor_to}";
    $po = $all_po == 1 ? 'ทั้งหมด' : "{$po_from} - {$po_to}";
    $status = $po_status == 'C' ? 'ปิดแล้ว' : ($po_status === 'O' ? "ยังไม่ปิด" : "ทั้งหมด" );
    $pd_result = $is_item == 1 ? 'รายการสินค้า' : 'รุ่นสินค้า';
    $product = $all_product == 1 ? 'ทั้งหมด' : ($is_item == 1 ? "({$item_from}) - ({$item_to})" : "({$style_from}) - ({$style_to})");

    $arr = array(
      'from_date' => from_date($from_date),
      'to_date' => to_date($to_date),
      'all_po' => $all_po,
      'po_from' => $po_from,
      'po_to' => $po_to,
      'po_status' => $po_status,
      'all_vendor' => $all_vendor,
      'vendor_from' => $vendor_from,
      'vendor_to' => $vendor_to,
      'is_item' => $is_item,
      'all_product' => $all_product,
      'item_from' => $item_from,
      'item_to' => $item_to,
      'style_from' => $style_from,
      'style_to' => $style_to
    );


    $result = $this->po_backlogs_model->get_data($arr);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('PO Backlogs');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $title);
    $this->excel->getActiveSheet()->mergeCells('A1:I1');
    $this->excel->getActiveSheet()->setCellValue('A2', "ใบสั่งซื้อ : {$po}");
    $this->excel->getActiveSheet()->mergeCells('A2:I2');
    $this->excel->getActiveSheet()->setCellValue('A3', "รหัสผู้ขาย : {$vendor}");
    $this->excel->getActiveSheet()->mergeCells('A3:I3');
    $this->excel->getActiveSheet()->setCellValue('A4', "สถานะใบสั่งซื้อ : {$status}");
    $this->excel->getActiveSheet()->mergeCells('A4:I4');
    $this->excel->getActiveSheet()->setCellValue('A5', "สินค้า : {$product}");
    $this->excel->getActiveSheet()->mergeCells('A5:I5');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A6', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B6', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C6', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('D6', 'ใบสั่งซื้อ');
    $this->excel->getActiveSheet()->setCellValue('E6', 'ผู้ขาย');
    $this->excel->getActiveSheet()->setCellValue('F6', 'กำหนดรับ');
    $this->excel->getActiveSheet()->setCellValue('G6', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('H6', 'รับแล้ว');
    $this->excel->getActiveSheet()->setCellValue('I6', 'ค้างร้บ');
    $this->excel->getActiveSheet()->setCellValue('J6', 'หมายเหตุ');

    $row = 7;
    if(!empty($result))
    {
      $no = 1;
      $total_qty = 0;
      $total_received = 0;
      $total_backlogs = 0;

      foreach($result as $rs)
      {
        $backlogs = $rs->qty - $rs->received;
        $backlogs = $backlogs > 0 ? $backlogs : 0;

        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->code);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->vender_code.' : '.$rs->vender_name);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, thai_date($rs->due_date));
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->received);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $backlogs);
        $this->excel->getActiveSheet()->setCellValue('J'.$row, ($rs->status == 2 ? 'part' : ($rs->status == 3 ? 'closed' : '')));
        $total_qty += $rs->qty;
        $total_received += $rs->received;
        $total_backlogs += $backlogs;
        $no++;
        $row++;
      }

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
      $this->excel->getActiveSheet()->setCellValue('G'.$row, $total_qty);
      $this->excel->getActiveSheet()->setCellValue('H'.$row, $total_received);
      $this->excel->getActiveSheet()->setCellValue('I'.$row, $total_backlogs);

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->mergeCells("A{$row}:F{$row}");
      $this->excel->getActiveSheet()->getStyle('H6:I'.$row)->getAlignment()->setHorizontal('center');
      $this->excel->getActiveSheet()->getStyle('G6:I'.$row)->getNumberFormat()->setFormatCode('#,##0');

    }

    setToken($token);

    $file_name = "รายงานใบสั่งซื้อค้างรับ.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }

} //--- end class

?>
