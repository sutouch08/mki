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
    $sc = TRUE;
    $bs = [];

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $ch_list = empty($ds->wm_channels) ? "" : "ฝากขาย";

      if( ! empty($ds->channels))
      {
        $i = empty($ch_list) ? 1 : 2;

        foreach($ds->channels as $ch)
        {
          $ch_list .= $i === 1 ? $ch->name : ', '.$ch->name;
          $i++;
        }
      }

      //---  Report title
      $bs['reportDate'] = thai_date($ds->fromDate, FALSE, '/').' - '.thai_date($ds->toDate, FALSE, '/');
  		$bs['chList']   = $ds->allChannels == 1 ? 'ทั้งหมด' : $ch_list;

      $channels = $ds->allChannels == 1 ? $this->channels_model->get_all() : $ds->channels;

      $res = [];
      $totalQty = 0;
      $totalAmount = 0;
      $no = 1;

      if($ds->allChannels == 1 OR ! empty($ds->wm_channels))
      {
        $sales = $this->sales_report_model->get_sum_wm($ds->fromDate, $ds->toDate);

        if( ! empty($sales))
        {
          $key = $ds->order_by == 'qty' ? $sales->qty : $sales->amount; //---- ไว้ sort array
          $key = empty($key) ? $no : $key;

          $res[$key] = array(
            'code' => 'WM',
            'name' => 'ฝากขาย',
            'qty' => number($sales->qty),
            'amount' => number($sales->amount)
          );

          $no++;
          $totalQty += $sales->qty;
          $totalAmount += $sales->amount;
        }
      }

      if( ! empty($channels))
      {
        foreach($channels as $rs)
        {
          $sales = $this->sales_report_model->get_sum_sales_by_channels($rs->code, $ds->fromDate, $ds->toDate);

          if( ! empty($sales))
          {
            $key = $ds->order_by == 'qty' ? $sales->qty : $sales->amount; //---- ไว้ sort array
            $key = empty($key) ? $no : $key;

            $res[$key] = array(
              'code' => $rs->code,
              'name' => $rs->name,
              'qty' => number($sales->qty),
              'amount' => number($sales->amount)
            );

            $no++;
            $totalQty += $sales->qty;
            $totalAmount += $sales->amount;
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบช่องทางขาย";
      }


      if( ! empty($res))
      {
        krsort($res);
        $no = 1;
        $result = [];

        foreach($res as $rs)
        {
          $result[] = (object) array(
            'no' => $no,
            'code' => $rs['code'],
            'name' => $rs['name'],
            'qty' => $rs['qty'],
            'amount' => $rs['amount']
          );

          $no++;
        }

        $bs['bs'] = $result;
        $bs['totalQty'] = number($totalQty);
        $bs['totalAmount'] = number($totalAmount, 2);
      }
      else
      {
        $bs['bs'] = ['nodata' => 'nodata'];
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = set_error_message('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $bs
    );

    echo json_encode($arr);
  }


  public function do_export()
  {
    //---  Report title
    $report_title = 'รายงานยอดขาย แยกตามช่องทางการขาย';
    $date_title = "";
    $chList = "";

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales By Channels');

    $ds = json_decode($this->input->post('data'));

    $token = $this->input->post('token');

    if( ! empty($ds))
    {
      $ch_list = empty($ds->wm_channels) ? "" : "ฝากขาย";

      if( ! empty($ds->channels))
      {
        $i = empty($ch_list) ? 1 : 2;

        foreach($ds->channels as $ch)
        {
          $ch_list .= $i === 1 ? $ch->name : ', '.$ch->name;
          $i++;
        }
      }

      //---  Report title
      $report_title = 'รายงานยอดขาย แยกตามช่องทางการขาย';
      $date_title = 'วันที่ : '.thai_date($ds->fromDate, FALSE, '/').' - '.thai_date($ds->toDate, FALSE, '/');
      $chList = $ds->allChannels == 1 ? 'ทั้งหมด' : $ch_list;

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
      $channels = $ds->allChannels == 1 ? $this->channels_model->get_all() : $ds->channels;
      $res = [];
      $result = [];
      $no = 1;

      if($ds->allChannels == 1 OR ! empty($ds->wm_channels))
      {
        $sales = $this->sales_report_model->get_sum_wm($ds->fromDate, $ds->toDate);

        if( ! empty($sales))
        {
          $key = $ds->order_by == 'qty' ? $sales->qty : $sales->amount; //---- ไว้ sort array
          $key = empty($key) ? $no : $key;

          $res[$key] = array(
            'code' => 'WM',
            'name' => 'ฝากขาย',
            'qty' => $sales->qty,
            'amount' => $sales->amount
          );

          $no++;
        }
      }

      if( ! empty($channels))
      {
        foreach($channels as $rs)
        {
          $sales = $this->sales_report_model->get_sum_sales_by_channels($rs->code, $ds->fromDate, $ds->toDate);

          if( ! empty($sales))
          {
            $key = $ds->order_by == 'qty' ? $sales->qty : $sales->amount; //---- ไว้ sort array
            $key = empty($key) ? $no : $key;

            $res[$key] = array(
              'code' => $rs->code,
              'name' => $rs->name,
              'qty' => $sales->qty,
              'amount' => $sales->amount
            );

            $no++;
          }
        }
      }

      if( ! empty($res))
      {
        krsort($res);
        $no = 1;

        foreach($res as $rs)
        {
          $result[] = (object) array(
            'no' => $no,
            'code' => $rs['code'],
            'name' => $rs['name'],
            'qty' => $rs['qty'],
            'amount' => $rs['amount']
          );

          $no++;
        }
      }

      if( ! empty($result))
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

        $re = $row -1;

        $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
        $this->excel->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, '=SUM(D5:D'.$re.')');
        $this->excel->getActiveSheet()->setCellValue('E'.$row, '=SUM(E5:E'.$re.')');

        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
        $this->excel->getActiveSheet()->getStyle('D5:D'.$row)->getAlignment()->setHorizontal('right');
        $this->excel->getActiveSheet()->getStyle('D5:D'.$row)->getNumberFormat()->setFormatCode('#,##0');
        $this->excel->getActiveSheet()->getStyle('E5:E'.$row)->getAlignment()->setHorizontal('right');
        $this->excel->getActiveSheet()->getStyle('E5:E'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
      }
    } //---------- $ds

		setToken($token);
    $file_name = "Report Sales by Channels.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }

} //--- end class








 ?>
