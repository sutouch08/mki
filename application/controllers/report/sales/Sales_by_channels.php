<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_by_channels extends PS_Controller
{
  public $menu_code = 'RSBYCH';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานยอดขาย แยกตามช่องทางการขาย';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/sales_by_channels';
    $this->load->model('report/sales/sales_report_model');
		$this->load->model('masters/channels_model');
  }

  public function index()
  {
		$ds['channels'] = $this->channels_model->get_all();

    $this->load->view('report/sales/sales_by_channels', $ds);
  }


  public function get_report()
  {
    $allChannels = $this->input->get('allChannels');
    $channels = $this->input->get('channels');

    $fromDate = $this->input->get('fromDate');
    $toDate = $this->input->get('toDate');

		$orderBy = empty($this->input->get('orderBy')) ? 'amount' : $this->input->get('orderBy');

		$ch_list = '';
    if(!empty($channels))
    {
      $i = 1;
      foreach($channels as $ch)
      {
        $ch_list .= $i === 1 ? $ch : ', '.$ch;
        $i++;
      }
    }

    //---  Report title
    $sc['reportDate'] = thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
		$sc['chList']   = $allChannels == 1 ? 'ทั้งหมด' : $ch_list;


    $ds = array(
      'allChannels' => is_true($allChannels),
			'channels' => $channels,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate),
			'orderBy' => $orderBy
    );

    $result = $this->sales_report_model->get_sum_channels_sales_by_date_upd($ds);

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
          'code' => $rs->code,
          'name' => $rs->name,
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
    $allChannels = $this->input->post('allChannels');
    $channels = $this->input->post('channels');

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

		$orderBy = empty($this->input->post('orderBy')) ? 'amount' : $this->input->post('orderBy');

		$token = $this->input->post('token');

		$ch_list = '';
    if(!empty($channels))
    {
      $i = 1;
      foreach($channels as $ch)
      {
        $ch_list .= $i === 1 ? $ch : ', '.$ch;
        $i++;
      }
    }

    //---  Report title
    $report_title = 'รายงานยอดขาย แยกตามช่องทางการขาย';
    $date_title = 'วันที่ : '.thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $chList = $allChannels == 1 ? 'ทั้งหมด' : $ch_list;


    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales By Channels');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:I1');
    $this->excel->getActiveSheet()->setCellValue('A2', $date_title);
    $this->excel->getActiveSheet()->mergeCells('A2:I2');
    $this->excel->getActiveSheet()->setCellValue('A3', $chList);
    $this->excel->getActiveSheet()->mergeCells('A3:I3');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B4', 'รหัส');
    $this->excel->getActiveSheet()->setCellValue('C4', 'ช่องทางขาย');
    $this->excel->getActiveSheet()->setCellValue('D4', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('E4', 'มูลค่า(Vat exclude)');

    $row = 5;

		$ds = array(
      'allChannels' => is_true($allChannels),
      'channels' => $channels,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate),
			'orderBy' => $orderBy
    );

    $result = $this->sales_report_model->get_sum_channels_sales_by_date_upd($ds);

    if(!empty($result))
    {
      $no = 1;
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->code);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->name);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->amount);

        $no++;
        $row++;
      }

      $res = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
      $this->excel->getActiveSheet()->setCellValue('D'.$row, '=SUM(D5:D'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('E'.$row, '=SUM(E5:E'.$res.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('D5:D'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('D5:D'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('E5:E'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('E5:E'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }

		setToken($token);
    $file_name = "Report Sales by Channels.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
