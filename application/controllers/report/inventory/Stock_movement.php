<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_movement extends PS_Controller
{
  public $menu_code = 'RICSTM';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานความเคลื่อนไหวสินค้า';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/stock_movement';
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('report/inventory/stock_movement_report_model');
  }

  public function index()
  {
    $whList = $this->warehouse_model->get_all_warehouse();
    $ds['whList'] = $whList;
    $this->load->view('report/inventory/stock_movement_report', $ds);
  }


  public function get_report()
  {
    $sc = TRUE;

    $filter = json_decode($this->input->post('filter'));
    $ds = array();

    if( ! empty($filter))
    {
      $date = db_date($filter->date);

      $items = $filter->allProduct == 1 ? $this->stock_movement_report_model->get_all_items() : $this->stock_movement_report_model->get_items_by_range($filter->pdFrom, $filter->pdTo);

      $whs = $filter->warehouse; //--- array of warehouse
      $date_title = "{$filter->date}";
      $item_title = $filter->allProduct == 1 ? "ทั้งหมด" : "{$filter->pdFrom}  ถึง  {$filter->pdTo}";
      $wh_title = $filter->allWarehouse == 1 ? "ทั้งหมด" : $this->warehouse_name_list($whs);
      $group_title = $filter->groupWarehouse == 1 ? "รวมคลัง" : "แยกคลัง";

      $Whs = $this->get_warehouse_list_array();

      if( ! empty($items))
      {
        $no = 1;

        foreach($items as $item)
        {
          $mv = NULL;

          if($filter->groupWarehouse == 0)
          {
            $mv = $this->stock_movement_report_model->get_movement_by_item_each_warehouse($item->code, $whs, $date);
          }
          else
          {
            $mv = $this->stock_movement_report_model->get_movement_by_item_group_warehouse($item->code, $whs, $date);
          }

          if( ! empty($mv))
          {
            foreach($mv as $rs)
            {
              $last_in = NULL;
              $last_out = NULL;

              if( empty($rs->warehouse_code))
              {
                $last_in = $this->stock_movement_report_model->get_last_move_by_item($item->code, 'in');
                $last_out = $this->stock_movement_report_model->get_last_move_by_item($item->code, 'out');
              }
              else
              {
                $last_in = $this->stock_movement_report_model->get_last_move_by_warehouse($item->code, $rs->warehouse_code, 'in');
                $last_out = $this->stock_movement_report_model->get_last_move_by_warehouse($item->code, $rs->warehouse_code, 'out');
              }

              $row = array(
                'no' => number($no),
                'product_code' => $item->code,
                'product_name' => $item->name,
                'warehouse_code' => empty($rs->warehouse_code) ? NULL : $Whs[$rs->warehouse_code],
                'move_in' => number($rs->move_in),
                'move_out' => number($rs->move_out),
                'balance' => number($rs->move_in - $rs->move_out),
                'last_in' => empty($last_in) ? NULL : thai_date($last_in, FALSE),
                'last_out' => empty($last_out) ? NULL : thai_date($last_out, FALSE)
              );

              array_push($ds, $row);
              $no++;
            }
          }
          else
          {
            if($filter->groupWarehouse == 0)
            {
              foreach($whs as $warehouse_code)
              {
                $row = array(
                  'no' => number($no),
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'warehouse_code' => $Whs[$warehouse_code],
                  'move_in' => 0,
                  'move_out' => 0,
                  'balance' => 0,
                  'last_in' => NULL,
                  'last_out' => NULL
                );

                array_push($ds, $row);
                $no++;
              }
            }
            else
            {
              $row = array(
                'no' => number($no),
                'product_code' => $item->code,
                'product_name' => $item->name,
                'warehouse_code' => NULL,
                'move_in' => 0,
                'move_out' => 0,
                'balance' => 0,
                'last_in' => NULL,
                'last_out' => NULL
              );

              array_push($ds, $row);
              $no++;
            }
          }
        }
      }
      else
      {
        $row = array(
          'nodata' => 'nodata'
        );

        array_push($ds, $row);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'date_title' => $sc === TRUE ? $date_title : NULL,
      'item_title' => $sc === TRUE ? $item_title : NULL,
      'wh_title' => $sc === TRUE ? $wh_title : NULL,
      'group_title' => $sc === TRUE ? $group_title : NULL,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }



  public function get_warehouse_list_array()
  {
    $Whs = [];

    $rs = $this->db->select('code, name')->get('warehouse');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $Whs[$ro->code] = $ro->name;
      }
    }

    return $Whs;
  }


  public function warehouse_name_list(array $ds = array())
  {
    $name = "";

    if( ! empty($ds))
    {
      $rs = $this->db->select('name')->where_in('code', $ds)->get('warehouse');

      if($rs->num_rows() > 0)
      {
        $i = 0;

        foreach($rs->result() as $ro)
        {
          $name .= $i === 0 ? $ro->name : ", {$ro->name}";
          $i++;
        }
      }
    }

    return $name;
  }


  public function do_export()
  {
    $token = $this->input->post('token');
    $filter = json_decode($this->input->post('filter'));

    if( ! empty($filter))
    {
      $date = db_date($filter->date);

      $items = $filter->allProduct == 1 ? $this->stock_movement_report_model->get_all_items() : $this->stock_movement_report_model->get_items_by_range($filter->pdFrom, $filter->pdTo);

      $whs = $filter->warehouse; //--- array of warehouse
      $report_title = "รายงานความเคลื่อนไหวสินค้า ณ วันที่  {$filter->date}";
      $item_title = "สินค้า : ".($filter->allProduct == 1 ? "ทั้งหมด" : "{$filter->pdFrom}  ถึง  {$filter->pdTo}");
      $wh_title = "คลัง : ".($filter->allWarehouse == 1 ? "ทั้งหมด" : $this->warehouse_name_list($whs));
      $group_title = "การแสดงผล : ".($filter->groupWarehouse == 1 ? "รวมคลัง" : "แยกคลัง");

      $Whs = $this->get_warehouse_list_array();

      //--- load excel library
      $this->load->library('excel');

      $this->excel->setActiveSheetIndex(0);
      $this->excel->getActiveSheet()->setTitle('Stock Movement Report');

      $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
      $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
      $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
      $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
      $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
      $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
      $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

      //--- set report title header
      $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
      $this->excel->getActiveSheet()->mergeCells('A1:I1');
      $this->excel->getActiveSheet()->setCellValue('A2', $item_title);
      $this->excel->getActiveSheet()->mergeCells('A2:I2');
      $this->excel->getActiveSheet()->setCellValue('A3', $wh_title);
      $this->excel->getActiveSheet()->mergeCells('A3:G3');
      $this->excel->getActiveSheet()->setCellValue('H3', $group_title);
      $this->excel->getActiveSheet()->mergeCells('H3:I3');

      $row = 5;

      //--- set Table header
      $this->excel->getActiveSheet()->setCellValue("A{$row}", '#');
      $this->excel->getActiveSheet()->setCellValue("B{$row}", 'รหัส');
      $this->excel->getActiveSheet()->setCellValue("C{$row}", 'สินค้า');
      $this->excel->getActiveSheet()->setCellValue("D{$row}", 'คลัง');
      $this->excel->getActiveSheet()->setCellValue("E{$row}", 'เข้า');
      $this->excel->getActiveSheet()->setCellValue("F{$row}", 'ออก');
      $this->excel->getActiveSheet()->setCellValue("G{$row}", 'คงเหลือ');
      $this->excel->getActiveSheet()->setCellValue("H{$row}", 'เข้าล่าสุด');
      $this->excel->getActiveSheet()->setCellValue("I{$row}", 'ออกล่าสุด');

      $this->excel->getActiveSheet()->getStyle("A{$row}:I{$row}")->getAlignment()->setHorizontal('center');

      $row++;

      if( ! empty($items))
      {
        $no = 1;

        foreach($items as $item)
        {
          $mv = NULL;

          if($filter->groupWarehouse == 0)
          {
            $mv = $this->stock_movement_report_model->get_movement_by_item_each_warehouse($item->code, $whs, $date);
          }
          else
          {
            $mv = $this->stock_movement_report_model->get_movement_by_item_group_warehouse($item->code, $whs, $date);
          }

          if( ! empty($mv))
          {
            foreach($mv as $rs)
            {
              $last_in = NULL;
              $last_out = NULL;

              if( empty($rs->warehouse_code))
              {
                $last_in = $this->stock_movement_report_model->get_last_move_by_item($item->code, 'in');
                $last_out = $this->stock_movement_report_model->get_last_move_by_item($item->code, 'out');
              }
              else
              {
                $last_in = $this->stock_movement_report_model->get_last_move_by_warehouse($item->code, $rs->warehouse_code, 'in');
                $last_out = $this->stock_movement_report_model->get_last_move_by_warehouse($item->code, $rs->warehouse_code, 'out');
              }

              $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
              $this->excel->getActiveSheet()->setCellValue("B{$row}", $item->code);
              $this->excel->getActiveSheet()->setCellValue("C{$row}", $item->name);
              $this->excel->getActiveSheet()->setCellValue("D{$row}", empty($rs->warehouse_code) ? "ทั้งหมด" : $Whs[$rs->warehouse_code]);
              $this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->move_in);
              $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->move_out);
              $this->excel->getActiveSheet()->setCellValue("G{$row}", ($rs->move_in - $rs->move_out));
              $this->excel->getActiveSheet()->setCellValue("H{$row}", empty($last_in) ? "" : thai_date($last_in, FALSE));
              $this->excel->getActiveSheet()->setCellValue("I{$row}", empty($last_out) ? "" : thai_date($last_out, FALSE));

              $row++;
              $no++;
            } //--- end foreach
          }
          else
          {
            if($filter->groupWarehouse == 0)
            {
              foreach($whs as $warehouse_code)
              {
                $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
                $this->excel->getActiveSheet()->setCellValue("B{$row}", $item->code);
                $this->excel->getActiveSheet()->setCellValue("C{$row}", $item->name);
                $this->excel->getActiveSheet()->setCellValue("D{$row}", $Whs[$warehouse_code]);
                $this->excel->getActiveSheet()->setCellValue("E{$row}", 0);
                $this->excel->getActiveSheet()->setCellValue("F{$row}", 0);
                $this->excel->getActiveSheet()->setCellValue("G{$row}", 0);
                $this->excel->getActiveSheet()->setCellValue("H{$row}", "");
                $this->excel->getActiveSheet()->setCellValue("I{$row}", "");

                $row++;
                $no++;
              }
            }
            else
            {
              $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
              $this->excel->getActiveSheet()->setCellValue("B{$row}", $item->code);
              $this->excel->getActiveSheet()->setCellValue("C{$row}", $item->name);
              $this->excel->getActiveSheet()->setCellValue("D{$row}", "ทั้งหมด");
              $this->excel->getActiveSheet()->setCellValue("E{$row}", 0);
              $this->excel->getActiveSheet()->setCellValue("F{$row}", 0);
              $this->excel->getActiveSheet()->setCellValue("G{$row}", 0);
              $this->excel->getActiveSheet()->setCellValue("H{$row}", "");
              $this->excel->getActiveSheet()->setCellValue("I{$row}", "");

              $row++;
              $no++;
            }
          }
        }
      }

      $this->excel->getActiveSheet()->getStyle("E6:G{$row}")->getNumberFormat()->setFormatCode('#,##0');
    }

    setToken($token);

    $file_name = "Report Stock Movement.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }

} //--- end class








 ?>
