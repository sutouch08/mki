<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adjust extends PS_Controller
{
  public $menu_code = 'ICSTAJ';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
	public $title = 'ปรับปรุงสต็อก';
  public $filter;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/adjust';
    $this->load->model('inventory/adjust_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
		$this->load->model('inventory/check_stock_diff_model');
  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'adj_code', ''),
      'reference'  => get_filter('reference', 'adj_reference', ''),
      'user'  => get_filter('user', 'adj_user', 'all'),
      'from_date' => get_filter('from_date', 'adj_from_date', ''),
      'to_date'   => get_filter('from_date', 'adj_to_date', ''),
      'remark' => get_filter('remark', 'adj_remark', ''),
      'status' => get_filter('status', 'adj_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->adjust_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list   = $this->adjust_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('inventory/adjust/adjust_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('inventory/adjust/adjust_add');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      if($this->input->post('date_add'))
      {
        $date_add = db_date($this->input->post('date_add'));

        $code = $this->get_new_code($date_add);

        if( ! empty($code))
        {
          $ds = array(
            'code' => $code,
            'bookcode' => NULL,
            'reference' => get_null($this->input->post('reference')),
            'date_add' => $date_add,
            'user' => get_cookie('uname'),
            'status' => -1,
            'remark' => get_null($this->input->post('remark'))
          );

          if( ! $this->adjust_model->add($ds))
          {
            $sc = FALSE;
            $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Failed to generate document number";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('required');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $ds = array(
      'doc' => $this->adjust_model->get($code),
      'details' => $this->adjust_model->get_details($code)
    );

    $this->load->view('inventory/adjust/adjust_edit', $ds);
  }


  public function add_detail()
  {
    $sc = TRUE;
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $zone_code = $this->input->post('zone_code');
      $product_code = $this->input->post('pd_code');
      $up_qty = $this->input->post('qty_up');
      $down_qty = $this->input->post('qty_down');
      $qty = $up_qty - $down_qty;

      if($qty != 0)
      {
        $doc = $this->adjust_model->get($code);

        if( ! empty($doc) && $doc->status < 1)
        {
          //--- ตรวจสอบรหัสสินค้า
          $item = $this->products_model->get($product_code);
          if(!empty($item))
          {
            //--- ตรวจสอบรหัสโซน
            $zone = $this->zone_model->get($zone_code);
            if(!empty($zone))
            {
              //--- ตรวจสอบว่ามีรายการที่เงื่อนไขเดียวกันแล้วยังไม่ได้บันทึกหรือเปล่า
              //--- ถ้ามีรายการอยู่จะได้ ข้อมูล กลับมา
              $detail = $this->adjust_model->get_exists_detail($code, $product_code, $zone_code);

              if(!empty($detail))
              {
                if($detail->valid == 0)
                {
                  //---- ถ้ามีรายการอยู่แล้ว ทำการ update
                  $qty = $up_qty - $down_qty;
                  if(! $this->adjust_model->update_detail_qty($detail->id, $qty))
                  {
                    $sc = FALSE;
                    $this->error = "ปรับปรุงรายการไม่สำเร็จ";
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "ไม่สามารถปรับปรุงรายการได้เนื่องจากรายการถูกปรับยอดไปแล้ว";
                }

              }
              else
              {
                //---- ถ้ายังไม่มีรายการ เพิ่มใหม่
                $ds = array(
                  'adjust_code' => $code,
                  'warehouse_code' => $zone->warehouse_code,
                  'zone_code' => $zone->code,
                  'product_code' => $item->code,
                  'qty' => $qty
                );

                if(! $this->adjust_model->add_detail($ds))
                {
                  $sc = FALSE;
                  $this->error = "เพิ่มรายการไม่สำเร็จ";
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "รหัสโซนไม่ถูกต้อง";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "รหัสสินค้าไม่ถูกต้อง";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารไม่ถูกต้อง หรือ สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "จำนวนต้องมากกว่า 1";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูล";
    }

    if($sc === TRUE)
    {
      $rs = $this->adjust_model->get_exists_detail($code, $product_code, $zone_code);
      if(!empty($rs))
      {
        $arr = array(
          'id' => $rs->id,
          'pdCode' => $rs->product_code,
          'pdName' => $rs->product_name,
          'zoneCode' => $rs->zone_code,
          'zoneName' => $rs->zone_name,
          'up' => ($rs->qty > 0 ? $rs->qty : 0),
          'down' => ($rs->qty < 0 ? ($rs->qty * -1) : 0),
          'valid' => $rs->valid
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "การบันทึกข้อมูลผิดพลาด";
      }
    }

    echo $sc === TRUE ? json_encode($arr) : $this->error;
  }




  //---- update doc header
  public function update()
  {
    $sc = TRUE;
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $reference = get_null($this->input->post('reference'));
      $remark = get_null($this->input->post('remark'));

      $doc = $this->adjust_model->get($code);
      if(!empty($doc))
      {
        $arr = array(
          'reference' => $reference,
          'remark' => $remark
        );

        //---- ถ้าบันทึกแล้ว จะไม่สามารถเปลี่ยนแปลงวันที่ได้
        //--- เนื่องจากมีการบันทึก movement ไปแล้วตามวันที่เอกสาร
        if($doc->status == 0)
        {
          $arr['date_add'] = $date_add;
        }

        if(! $this->adjust_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




	public function delete_detail()
	{
		$sc = TRUE;
		if($this->pm->can_edit)
		{
			$id = $this->input->post('id');

			if(!empty($id))
			{
				$detail = $this->adjust_model->get_detail($id);

				if(!empty($detail))
				{
					$doc = $this->adjust_model->get($detail->adjust_code);
					if(!empty($doc))
					{
						if($doc->status < 1)
						{
							if( ! $this->adjust_model->delete_detail($id))
							{
								$sc = FALSE;
								$this->error = "ลบรายการไม่สำเร็จ";
							}
							else
							{
								if($detail->id_diff)
								{
									$this->check_stock_diff_model->update($detail->id_diff, array('status' => 0));
								}
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "เอกสารถูกบันทึกไปแล้ว ไม่สามารถแก้ไขรายการได้";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ไม่พบเลขที่เอกสาร";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการที่ต้องการลบ หรือ รายการถูกลบไปแล้ว";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่พบ ID";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "คุณไม่มีสิทธิ์ในการแก้ไขรายการ";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	///----- Just change status to 0
	public function unsave()
	{
		$sc = TRUE;
		if($this->input->post('code'))
		{
			$code = $this->input->post('code');
			$doc = $this->adjust_model->get($code);
			if(!empty($doc))
			{
				if($doc->status == 1)
				{
					$details = $this->adjust_model->get_details($code);
					if(!empty($details))
					{
						$status = 0; //--- 0 = not save, 1 = saved, 2 = cancled
						if( ! $this->adjust_model->change_status($code, $status))
						{
							$sc = FALSE;
							$this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ไม่พบรายการปรับยอดกรุณาตรวจสอบ";
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบเลขที่เอกสาร";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function save()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');

      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status < 1)
        {
          $status = 0; //--- 0 = pending for approve, 1 = saved, 2 = cancled

          if( ! $this->adjust_model->change_status($code, $status))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกอนุมัติไปแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function approve()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');

      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $this->db->trans_begin();

          $details = $this->adjust_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              if($rs->valid == 0)
              {
                //--- 1 ปรับยอดในโซน
                if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, $rs->qty))
                {
                  $sc = FALSE;
                  $this->error = "ปรับยอดในโซนไม่สำเร็จ";
                  break;
                }


                //--- 2. update movement
                $move_in = $rs->qty > 0 ? $rs->qty : 0;
                $move_out = $rs->qty < 0 ? ($rs->qty * -1) : 0;

                $arr = array(
                  'reference' => $rs->adjust_code,
                  'warehouse_code' => $rs->warehouse_code,
                  'zone_code' => $rs->zone_code,
                  'product_code' => $rs->product_code,
                  'move_in' => $move_in,
                  'move_out' => $move_out,
                  'date_add' => $doc->date_add
                );

                if(! $this->movement_model->add($arr))
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ไม่สำเร็จ';
                  break;
                }

                //--- 3 ปรับรายการเป็น บันทึกรายการแล้ว (valid = 1)
                if(! $this->adjust_model->valid_detail($rs->id))
                {
                  $sc = FALSE;
                  $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                  break;
                }
              }
            }
          }

          //--- เปลี่ยนสถานะเอกสารเป็น บันทึกแล้ว
          if($sc === TRUE)
          {
            $arr = array(
              'status' => 1,
              'is_approve' => 1,
              'approver' => $this->_user->uname,
              'approve_date' => now()
            );

            if( ! $this->adjust_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to change document status";
            }
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
          if($doc->status == -1)
          {
            $sc = FALSE;
            $this->error = "เอกสารยังไม่ถูกบันทึก";
          }

          if($doc->status == 2)
          {
            $sc = FALSE;
            $this->error = "เอกสารถูกยกเลิกไปแล้ว";
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }





  public function view_detail($code)
  {
    $ds = array(
      'doc' => $this->adjust_model->get($code),
      'details' => $this->adjust_model->get_details($code)
    );

    $this->load->view('inventory/adjust/adjust_detail', $ds);
  }



  public function cancel()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');
      $reason = $this->input->post('reason');

      if( ! empty($code))
      {
        $doc = $this->adjust_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 2)
          {
            $this->db->trans_begin();

            $arr = array(
              'is_cancle' => 1
            );

            if( ! $this->adjust_model->update_details($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update transection rows";
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 2,
                'cancel_reason' => $reason,
                'cancel_user' => $this->_user->uname,
                'cancel_date' => now()
              );

              if( ! $this->adjust_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }
            }

            if($sc === TRUE)
            {
              $details = $this->adjust_model->get_details($code);

              if( ! empty($details))
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  if($rs->valid == 1)
                  {
                    //--- 1. ปรับสต็อกกลับ
                    if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, ($rs->qty * -1)))
                    {
                      $sc = FALSE;
                      $this->error = "ปรับปรุงยอดในโซนไม่สำเร็จ";
                    }
                  }
                }
              }
            }

            if($sc === TRUE)
            {
              if( ! $this->movement_model->drop_movement($code))
              {
                $sc = FALSE;
                $this->error = "Failed to drop transection movement";
              }
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
          $this->error = get_error_message('notfound');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('required');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('permission');
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }



	public function load_check_diff($code)
  {
    $sc = TRUE;

    $list = $this->input->post('diff');

    if(!empty($list))
    {
      $this->db->trans_begin();
      //---- add diff list to adjust
      foreach($list as $id => $val)
      {
        $diff = $this->check_stock_diff_model->get($id);

        if(!empty($diff))
        {
          if($sc === FALSE)
          {
            break;
          }

          if($diff->status < 1)
          {
            $zone = $this->zone_model->get($diff->zone_code);
            if(!empty($zone))
            {
              $arr = array(
                'adjust_code' => $code,
                'warehouse_code' => $zone->warehouse_code,
                'zone_code' => $zone->code,
                'product_code' => $diff->product_code,
                'qty' => $diff->qty,
                'id_diff' => $diff->id
              );

              $adjust_id = $this->adjust_model->get_not_save_detail($code, $diff->product_code, $diff->zone_code);
              if(!empty($adjust_id))
              {
                if(! $this->adjust_model->update_detail($adjust_id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Update Failed : {$diff->product_code} : {$diff->zone_code}";
                }
              }
              else
              {
                if(! $this->adjust_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Add detail failed : {$diff->product_code} : {$diff->zone_code}";
                }
              }

              if($sc === TRUE)
              {
                $this->check_stock_diff_model->update($diff->id, array('status' => 1));
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "โซนไม่ถูกต้อง";
            }
          }
        }

      } //--- endforeach;

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
      $this->error = "ไม่พบรายการยอดต่าง";
    }

    if($sc === TRUE)
    {
      set_message('Loaded');
    }
    else
    {
      set_error($this->error);
    }

    redirect("{$this->home}/edit/{$code}");
  }



  public function get_stock_zone()
  {
    $zone_code = $this->input->get('zone_code');
    $product_code = $this->input->get('product_code');
    $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);
    echo $stock;
  }



  public function get_new_code($date = '')
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ADJUST');
    $run_digit = getConfig('RUN_DIGIT_ADJUST');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->adjust_model->get_max_code($pre);
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
      'adj_code',
      'adj_reference',
      'adj_user',
      'adj_from_date',
      'adj_to_date',
      'adj_remark',
      'adj_status'
    );

    clear_filter($filter);

    echo 'done';
  }

} //---- End class
?>
