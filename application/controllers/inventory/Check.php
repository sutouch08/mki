<?php
class Check extends PS_Controller
{
  public $menu_code = "ICSTCK";
  public $menu_group_code = "IC";
  public $title = "ตรวจนับสต็อก";
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/check';
    $this->load->model('inventory/check_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'check_code', ''),
      'subject' => get_filter('subject', 'subject', ''),
      'zone_code' => get_filter('zone_code', 'check_zone_code', ''),
      'user' => get_filter('user', 'check_user', ''),
      'from_date' => get_filter('from_date', 'check_from_date', ''),
      'to_date' => get_filter('to_date', 'check_to_date', ''),
      'status' => get_filter('status', 'check_status', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();

      $rows = $this->check_model->count_rows($filter);

      $filter['data'] = $this->check_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

      $this->pagination->initialize($init);

      $this->load->view('inventory/check/check_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('inventory/check/check_add');
    }
    else
    {
      $this->permission_page();
    }
  }


  public function add()
  {
    $sc = TRUE;
    $this->load->model('masters/zone_model');

    $date_add = $this->input->post('date_add');
    $date_add = empty($date_add) ? date('Y-m-d') : db_date($date_add);
    $subject = $this->input->post('subject');
    $zone_code = $this->input->post('zone_code');
    $allow_input_qty = $this->input->post('allow_input_qty');
    $remark = get_null(trim($this->input->post('remark')));

    $zone = $this->zone_model->get($zone_code);

    if( ! empty($zone))
    {
      $arr = array(
        'code' => $this->get_new_code($date_add),
        'subject' => $subject,
        'zone_id' => $zone->id,
        'zone_code' => $zone->code,
        'zone_name' => $zone->name,
        'warehouse_code' => $zone->warehouse_code,
        'warehouse_name' => $zone->warehouse_name,
        'start_date' => now(),
        'status' => 'O',
        'allow_input_qty' => $allow_input_qty,
        'date_add' => $date_add,
        'user' => $this->_user->uname,
        'remark' => $remark
      );

      $id = $this->check_model->add($arr);

      if($id === FALSE)
      {
        $sc = FALSE;
        $this->error = "Failed to add document";
      }
      else
      {
        $arr = array(
          'check_id' => $id,
          'action' => 'add',
          'uname' => $this->_user->uname
        );

        $this->check_model->add_logs($arr);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid Zone Code Or Zone is inactive";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'check_id' => $sc === TRUE ? $id : 0,
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function edit($id, $offset = 0)
  {
    $filter = array(
      'barcode' => get_filter('barcode', 'de_barcode', ''),
      'pd_code' => get_filter('pd_code', 'de_pd_code', ''),
      'user' => get_filter('user', 'de_user', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home."/edit/{$id}/");
    }
    else
    {
      $doc = $this->check_model->get_by_id($id);

      if( ! empty($doc) )
      {
        $this->segment = 5;
        $perpage = get_rows();
        $rows = $this->check_model->count_details_rows($doc->id, $filter);

        $filter['details'] = $this->check_model->get_details_list($doc->id, $filter, $perpage, $this->uri->segment($this->segment));
        $filter['doc'] = $doc;

        $init = pagination_config($this->home."/edit/{$id}/", $rows, $perpage, $this->segment);

        $this->pagination->initialize($init);

        $this->load->view('inventory/check/check_edit', $filter);
      }
      else
      {
        $this->page_error();
      }
    }
  }


  public function update()
  {
    $sc = TRUE;

    $this->load->model('masters/zone_model');

    $id = $this->input->post('id');
    $date_add = $this->input->post('date_add');
    $date_add = empty($date_add) ? date('Y-m-d') : db_date($date_add);
    $subject = $this->input->post('subject');
    $zone_code = $this->input->post('zone_code');
    $allow_input_qty = $this->input->post('allow_input_qty');
    $remark = get_null($this->input->post('remark'));

    $doc = $this->check_model->get_by_id($id);

    if( ! empty($doc) && $doc->status == 'O')
    {
      $zone = $this->zone_model->get($zone_code);

      if( ! empty($zone))
      {
        $arr = array(
          'date_add' => $date_add,
          'subject' => $subject,
          'zone_code' => $zone->code,
          'zone_name' => $zone->name,
          'warehouse_code' => $zone->warehouse_code,
          'warehouse_name' => $zone->warehouse_name,
          'allow_input_qty' => $allow_input_qty,
          'remark' => $remark
        );

        if( ! $this->check_model->update($id, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update document";
        }
        else
        {
          $arr = array(
            'check_id' => $id,
            'action' => 'update',
            'uname' => $this->_user->uname
          );

          $this->check_model->add_logs($arr);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "โซนไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เอกสารอยู่ในสถานะที่ไม่สามารถแก้ไขได้";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }

  public function checking($id)
  {
    $doc = $this->check_model->get_by_id($id);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->check_model->get_details($id)
      );

      if($doc->status == 'O')
      {
        $this->load->view('inventory/check/checking', $ds);
      }
      else
      {
        redirect($this->home."/view_detail/{$id}");
      }
    }
    else
    {
      $this->page_error();
    }
  }


  public function view_details($id)
  {
    $doc = $this->check_model->get_by_id($id);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $doc->status == 'C' ? $this->check_model->get_results($id) : $this->check_model->get_details($id)
      );

      $this->load->view('inventory/check/view_details', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function set_active_time($id)
  {
    $arr = array(
      'last_active' => now(),
      'active_user' => $this->_user->uname
    );

    return $this->check_model->update($id, $arr);
  }


  public function do_checking()
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $barcode = $this->input->post('barcode');
    $qty = $this->input->post('qty');
    $item_code = "not_found";

    $pd = $this->products_model->get_by_barcode($barcode);

    if(empty($pd) && ! empty($barcode))
    {
      $pd = $this->products_model->get_by_code($barcode);
    }

    if( ! empty($pd))
    {
      $item_code = $pd->code;

      $arr = array(
        'check_id' => $this->input->post('check_id'),
        'barcode' => ! empty($pd->barcode) ? $pd->barcode : $barcode,
        'qty' => $qty > 0 ? $qty : 1,
        'user_id' => $this->_user->id
      );

      $id = $this->check_model->add_detail($arr);

      if( ! $id)
      {
        $sc = FALSE;
        $this->error = "เพิ่มรายการไม่สำเร็จ";
      }
      else
      {
        $bc_id = ! empty($pd->barcode) ? md5($pd->barcode) : md5($barcode);
        $arr['id'] = $id;
        $arr['code'] = $pd->code;
        $arr['timestamp'] = date('H:i:s');
        $arr['bc_id'] = $bc_id;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการสินค้า";
    }

    $ds = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'item_code' => $item_code,
      'message' => $sc === TRUE ? 'success' : $this->error,
      'row' => $sc === TRUE ? $arr : NULL,
      'bc_id' => $sc === TRUE ? $bc_id : NULL
    );

    echo json_encode($ds);
  }


  public function check_with_no_item()
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $barcode = $this->input->post('barcode');
    $bc_id = md5($barcode);

    if( ! empty($barcode))
    {
      $arr = array(
        'check_id' => $this->input->post('check_id'),
        'barcode' => $barcode,
        'qty' => $this->input->post('qty'),
        'user_id' => $this->_user->id
      );

      $id = $this->check_model->add_detail($arr);

      if( ! $id)
      {
        $sc = FALSE;
        $this->error = "เพิ่มรายการไม่สำเร็จ";
      }
      else
      {
        $arr['id'] = $id;
        $arr['code'] = "";
        $arr['timestamp'] = date('H:i:s');
        $arr['bc_id'] = $bc_id;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการสินค้า";
    }

    $ds = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'item_code' => "",
      'message' => $sc === TRUE ? 'success' : $this->error,
      'row' => $sc === TRUE ? $arr : NULL,
      'bc_id' => $bc_id
    );

    echo json_encode($ds);
  }


  public function get_checked_row()
  {
    $sc = TRUE;

    $id = $this->input->get('check_id');
    $barcode = $this->input->get('barcode');

    $row = $this->check_model->get_sum_check_rows($id, $barcode);

    if( ! empty($row))
    {
      $row->bc_id = md5($row->barcode);
    }
    else
    {
      $sc = FALSE;
      $this->error = "Checked row not found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'row' => $sc === TRUE ? $row : NULL
    );

    echo json_encode($arr);
  }


  public function get_history()
  {
    $id = $this->input->get('check_id');
    $qty = $this->input->get('qty');

    $rows = $this->check_model->get_history($id, $qty, $this->_user->id);

    if( ! empty($rows))
    {
      foreach($rows as $rs)
      {
        $rs->timestamp = date('H:i:s', strtotime($rs->date_add));
        $rs->bc_id = md5($rs->barcode);
      }
    }

    $arr = array(
      'status' => 'success',
      'count' => empty($rows) ? 0 : count($rows),
      'rows' => $rows
    );

    echo json_encode($arr);
  }


  public function delete_checked_details()
  {
    $sc = TRUE;
    $rows = json_decode($this->input->post('rows'));

    if( ! empty($rows))
    {
      if( ! $this->check_model->delete_checked_rows($rows))
      {
        $sc = FALSE;
        $this->error = "ลบรายการตรวจนับไม่สำเร็จ";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update_cost($id)
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');

    $doc = $this->check_model->get_by_id($id);

    if( ! empty($doc))
    {
      //---- gen check result
      $details = $this->check_model->get_results($doc->id);

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          if( ! empty($rs->product_code))
          {
            $item = $this->products_model->get_by_code($rs->product_code);

            if( ! empty($item))
            {
              $arr = array(
                'cost' => $item->cost,
                'price' => $item->price
              );

              $this->check_model->update_result($rs->id, $arr);
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function close_check($id)
  {
    $sc = TRUE;

    $doc = $this->check_model->get_by_id($id);

    if( ! empty($doc))
    {
      if($doc->status == 'O')
      {
        $last_active = strtotime($doc->last_active) + (5 * 60); //-- เพิ่ม 5 นาที
        $accept_time = date('Y-m-d H:i:s', $last_active);

        if($accept_time <= now())
        {
          $this->db->trans_begin();

          if( ! $this->check_model->update_details($doc->id, array('status' => 'C')))
          {
            $sc = FALSE;
            $this->error = "ปิดรายการตรวจนับไม่สำเร็จ";
          }

          if($sc === TRUE)
          {
            $arr = array(
              'status' => 'C',
              'end_date' => now()
            );

            if( ! $this->check_model->update($doc->id, $arr))
            {
              $sc = FALSE;
              $this->error = "ปิดการตรวจนับไม่สำเร็จ";
            }
          }

          if($sc === TRUE)
          {
            if( ! $this->check_model->drop_result($doc->id))
            {
              $sc = FALSE;
              $this->error = "ลบรายการสรุปไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              //---- gen check result
              $details = $this->check_model->get_details($doc->id);

              if( ! empty($details))
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $arr = array(
                  'check_id' => $doc->id,
                  'barcode' => $rs->barcode,
                  'product_code' => $rs->code,
                  'product_name' => $rs->name,
                  'cost' => get_zero($rs->cost),
                  'price' => get_zero($rs->price),
                  'check_qty' => $rs->qty,
                  'user_id' => $this->_user->id
                  );

                  if( ! $this->check_model->add_result($arr))
                  {
                    $sc = FALSE;
                    $this->error = "สร้างรายการสรุปยอดไม่สำเร็จ";
                  }
                }
              }
            }
          }

          if($sc === TRUE)
          {
            $arr = array(
            'check_id' => $doc->id,
            'action' => 'close',
            'uname' => $this->_user->uname
            );

            $this->check_model->add_logs($arr);
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ปิดการตรวจนับไม่สำเร็จ เนื่องจากยังมีคนตรวจนับอยู่ กรุณารอ 5 นาทีแล้วลองใหม่อีกครับ";
        }
      }
      else
      {
        if($doc->status == 'D')
        {
          $sc = FALSE;
          $this->error = "ไม่สามารถปิดการตรวจนับได้เนื่องจากการตรวจนับถูกยกเลิกแล้ว";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function re_open_check($id)
  {
    $sc = TRUE;

    $doc = $this->check_model->get_by_id($id);

    if( ! empty($doc))
    {
      if($doc->status == 'C' OR $doc->status == 'D')
      {
        $this->db->trans_begin();

        if( ! $this->check_model->drop_result($doc->id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการสรุปไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          if( ! $this->check_model->update_details($doc->id, array('status' => 'O')))
          {
            $sc = FALSE;
            $this->error = "ย้อนสถานะรายการตรวจนับไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'status' => 'O',
            'end_date' => NULL
          );

          if( ! $this->check_model->update($doc->id, $arr))
          {
            $sc = FALSE;
            $this->error = "ย้อนสถานะการตรวจนับไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'check_id' => $doc->id,
            'action' => 'rollback',
            'uname' => $this->_user->uname
          );

          $this->check_model->add_logs($arr);
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function cancel_check($id)
  {
    $sc = TRUE;

    $doc = $this->check_model->get_by_id($id);

    if( ! empty($doc))
    {
      if($doc->status != 'D')
      {
        $this->db->trans_begin();

        if( ! $this->check_model->drop_result($doc->id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการสรุปไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          if( ! $this->check_model->update_details($doc->id, array('status' => 'D')))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะรายการตรวจนับไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'status' => 'D',
            'end_date' => NULL
          );

          if( ! $this->check_model->update($doc->id, $arr))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะการตรวจนับไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'check_id' => $doc->id,
            'action' => 'cancel',
            'uname' => $this->_user->uname
          );

          $this->check_model->add_logs($arr);
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_logs($id)
  {
    $ds = array();

    $logs = $this->check_model->get_logs($id);

    if( ! empty($logs))
    {
      foreach($logs as $rs)
      {
        $ds[] = array('name' => $this->action_name($rs->action), 'uname' => $rs->uname, 'date' => thai_date($rs->date_upd, TRUE));
      }
    }
    else
    {
      $ds[] = array("nodata" => "nodata");
    }

    echo json_encode($ds);
  }


  public function action_name($action)
  {
    $name = "Unknow";

    switch($action)
    {
      case 'add' :
        $name = "สร้าง";
        break;
      case 'update' :
        $name = 'แก้ไข';
        break;
      case 'cancel' :
        $name = 'ยกเลิก';
        break;
      case 'close' :
        $name = 'ปิดการตรวจนับ';
        break;
      case 'rollback' :
        $name = 'ย้อนสถานะ';
        break;
      case 'get_stock' :
        $name = 'ดึงสต็อก';
        break;
      default :
        $name = 'Unknow';
        break;
    }

    return $name;
  }

  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = 'CK'; //getConfig('PREFIX_CHECK');
    $run_digit = 4; //getConfig('RUN_DIGIT_CHECK');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->check_model->get_max_code($pre);

    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter()
  {
    $filter = array(
      'check_code',
      'subject',
      'check_zone_code',
      'check_user',
      'check_from_date',
      'check_to_date',
      'check_status'
    );

    return clear_filter($filter);
  }

  public function clear_result()
  {
    $filter = array('de_barcode', 'de_pd_code', 'de_user');

    return clear_filter($filter);
  }

  public function get_stock_zone()
  {
    $sc = TRUE;

    $id = $this->input->get('check_id');

    $doc = $this->check_model->get_by_id($id);

    if($doc->status == 'C')
    {

      $stock = $this->stock_model->get_all_stock_in_zone($doc->zone_code);

      if( ! empty($stock) )
      {
        if($this->check_model->reset_stock_zone($doc->id))
        {
          foreach($stock as $rs)
          {
            $row = $this->check_model->get_result_row_by_product_code($doc->id, $rs->product_code);

            if( ! empty($row))
            {
              $this->check_model->update_stock_zone($row->id, $rs->qty);
            }
            else
            {
              $arr = array(
                'check_id' => $doc->id,
                'barcode' => $rs->barcode,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'cost' => get_zero($rs->cost),
                'price' => get_zero($rs->price),
                'stock_qty' => get_zero($rs->qty),
                'check_qty' => 0,
                'diff_qty' => get_zero($rs->qty),
                'user_id' => $this->_user->id
              );

              $this->check_model->add_result($arr);
            }
          }

          $this->check_model->update_result_diff($doc->id);

          $arr = array(
            'check_id' => $doc->id,
            'action' => 'get_stock',
            'uname' => $this->_user->uname
          );

          $this->check_model->add_logs($arr);
        }
        else
        {
          $sc = FALSE;
          $this->error = "รีเซ็ตยอดตั้งต้นไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบยอดตั้งต้นในโซนที่กำหนด";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ดึงสต็อกได้เฉพาะสถานะ Close เท่านั้น";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function export_result()
  {
    $id = $this->input->post('id');
    $token = $this->input->post('token');

    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $sheet = $this->excel->getActiveSheet();

    $sheet->setTitle('สรุปยอดตรวจนับ');

    $sheet->getColumnDimension("A")->setAutoSize(true);
    $sheet->getColumnDimension("B")->setAutoSize(true);
    $sheet->getColumnDimension("C")->setAutoSize(true);
    $sheet->getColumnDimension("D")->setAutoSize(true);
    $sheet->getColumnDimension("E")->setAutoSize(true);
    $sheet->getColumnDimension("F")->setAutoSize(true);
    $sheet->getColumnDimension("G")->setAutoSize(true);
    $sheet->getColumnDimension("H")->setAutoSize(true);
    $sheet->getColumnDimension("I")->setAutoSize(true);
    $sheet->getColumnDimension("J")->setAutoSize(true);
    $sheet->getColumnDimension("K")->setAutoSize(true);

    $sheet->setCellValue("A1", "รายงานสรุปผลการตรวจนับสินค้า");
    $sheet->getStyle("A1")->getFont()->setSize(18);
    $sheet->getStyle("A1")->getFont()->setBold(TRUE);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells("A1:K1");

    $doc = $this->check_model->get_by_id($id);

    if( ! empty($doc))
    {
      $sheet->setCellValue("A2", "เลขที่");
      $sheet->setCellValue("B2", $doc->code);
      $sheet->mergeCells("B2:K2");

      $sheet->setCellValue("A3", "หัวข้อ");
      $sheet->setCellValue("B3", $doc->subject);
      $sheet->mergeCells("B3:K3");

      $sheet->setCellValue("A4", "สถานที่");
      $sheet->setCellValue("B4", $doc->zone_code." : ".$doc->zone_name);
      $sheet->mergeCells("B4:K4");

      $sheet->setCellValue("A5", "วันที่");
      $sheet->setCellValue("B5", thai_date($doc->start_date, FALSE, "/")." - ".thai_date($doc->end_date, FALSE, "/"));
      $sheet->mergeCells("B5:K5");

      $sheet->getStyle("A2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
      $sheet->getStyle("A3")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
      $sheet->getStyle("A4")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
      $sheet->getStyle("A5")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);

      $row = 7;

  		$sheet->setCellValue("A{$row}", "ลำดับ");
  		$sheet->setCellValue("B{$row}", "บาร์โค้ด");
  		$sheet->setCellValue("C{$row}", "รหัสสินค้า");
  		$sheet->setCellValue("D{$row}", "สินค้า");
  		$sheet->setCellValue("E{$row}", "ราคาทุน");
  		$sheet->setCellValue("F{$row}", "ราคาขาย");
  		$sheet->setCellValue("G{$row}", "ยอดตั้งต้น (1)");
  		$sheet->setCellValue("H{$row}", "ยอดตรวจนับ (2)");
  		$sheet->setCellValue("I{$row}", "ยอดต่าง (2-1)");
  		$sheet->setCellValue("J{$row}", "มูลค่าต่าง (ทุน)");
      $sheet->setCellValue("K{$row}", "มูลค่าต่าง (ขาย)");
      $sheet->getStyle("A{$row}:K{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

  		$row++;

      $results = $this->check_model->get_results($doc->id);

      if( ! empty($results))
      {
        $no = 1;

        foreach($results as $rs)
        {
          $sheet->setCellValue("A{$row}", $no);
          $sheet->setCellValueExplicit("B{$row}", $rs->barcode, PHPExcel_Cell_DataType::TYPE_STRING);
          $sheet->setCellValue("C{$row}", $rs->product_code);
      		$sheet->setCellValue("D{$row}", $rs->product_name);
      		$sheet->setCellValue("E{$row}", $rs->cost);
      		$sheet->setCellValue("F{$row}", $rs->price);
      		$sheet->setCellValue("G{$row}", $rs->stock_qty);
      		$sheet->setCellValue("H{$row}", $rs->check_qty);
      		$sheet->setCellValue("I{$row}", "=H{$row} - G{$row}");
      		$sheet->setCellValue("J{$row}", "=E{$row} * I{$row}");
          $sheet->setCellValue("K{$row}", "=F{$row} * I{$row}");
          $row++;
          $no++;
        }

        $re = $row - 1;

        $sheet->setCellValue("A{$row}", "รวม");
        $sheet->setCellValue("G{$row}", "=SUM(G8:G{$re})");
        $sheet->setCellValue("H{$row}", "=SUM(H8:H{$re})");
        $sheet->setCellValue("I{$row}", "=SUM(I8:I{$re})");
        $sheet->setCellValue("J{$row}", "=SUM(J8:J{$re})");
        $sheet->setCellValue("K{$row}", "=SUM(K8:K{$re})");

        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle("A{$row}:K{$row}")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);

    		///  กำหนดรูปแบบ column  บาร์โค้ด ให้เป็น number ไม่มีจุดทศนิยม ไม่มีลูกน้ำขั้นหลักพัน
    		$sheet->getStyle("B8:B{$row}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
    		//$sheet->getStyle("B8:B{$re}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    		/// ใส่ คอมม่า ให้หลักพัน และเติมทศนิยม
        $sheet->getStyle("E8:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("F8:F{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("G8:G{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("H8:H{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("I8:I{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("J8:J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("K8:K{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
      }
    }
    else
    {
      $sheet->setCellValue("A2", "Error : ไม่พบเลขที่การตรวจนับ กรุณาตรวจสอบ");
    }

    setToken($token);
    $file_name = "รายงานตรวจนับสินค้า.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }

} //--- end class
 ?>
