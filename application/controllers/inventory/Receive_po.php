<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_po extends PS_Controller
{
  public $menu_code = 'ICPURC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title;
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_po';
    $this->load->model('inventory/receive_po_model');
    $this->load->model('purchase/po_model');

    $this->title = "รับ FG จากใบสั่งผลิต";
  }


  public function index()
  {
    $this->load->helper('channels');

    $filter = array(
      'code'    => get_filter('code', 'code', ''),
      'invoice' => get_filter('invoice', 'invoice', ''),
      'po'      => get_filter('po', 'po', ''),
      'vender'  => get_filter('vender', 'vender', ''),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->receive_po_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->receive_po_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->receive_po_model->get_sum_qty($rs->code);
      }
    }

    $filter['document'] = $document;

		$this->pagination->initialize($init);

    $this->load->view('inventory/receive_po/receive_po_list', $filter);
  }



  public function view_detail($code)
  {
    $this->load->model('masters/products_model');
    $this->load->helper('warehouse');

    $doc = $this->receive_po_model->get($code);

    $details = $this->receive_po_model->get_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('inventory/receive_po/receive_po_detail', $ds);
  }



  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
		$this->load->model('masters/vender_model');

    $doc = $this->receive_po_model->get($code);

    $details = $this->receive_po_model->get_print_details($code);

    $ds = array(
			'title' => "ใบรับสินค้า",
      'doc' => $doc,
			'vender' => $this->vender_model->get($doc->vender_code),
      'details' => $details,
      'form_no' => "FM-WH-011 แก้ไขครั้งที่ : 00 วันที่บังคับใช้ : 21/10/2024"
    );

    $this->load->view('print/print_received', $ds);
  }


	//---- insert single item to document
	public function add_item()
	{
		$sc = TRUE;

    $code = $this->input->post('code');
		$barcode = trim($this->input->post('barcode'));
    $zone_code = $this->input->post('zone_code');
    $qty = $this->input->post('qty');
    $receive_date = db_date($this->input->post('receive_date'));

		if( ! empty($code))
		{
			$doc = $this->receive_po_model->get($code);

			if( ! empty($doc))
			{
				if($doc->status == 0)
				{
					if( ! empty($barcode))
					{
						if($qty > 0)
						{
							//-- check item
							$this->load->model('masters/products_model');
              $this->load->model('masters/zone_model');

							$item = $this->products_model->get_product_by_barcode($barcode);

              $item = empty($item) ? $this->products_model->get($barcode) : $item;

							if( ! empty($item))
							{
                $zone = $this->zone_model->get($zone_code);

                if( ! empty($zone))
                {
                  $detail = $this->receive_po_model->get_detail_by_product_and_zone($code, $item->code, $zone_code);

                  if( ! empty($detail))
                  {
                    $uQty = $detail->qty + $qty;
                    $amount = $uQty * $detail->price;

                    $arr = array(
                      'qty' => $uQty,
                      'amount' => $amount
                    );

                    if(!$this->receive_po_model->update_detail($detail->id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Update failed";
                    }
                  }
                  else
                  {
                    if( ! empty($doc->po_code))
                    {
                      $pod = $this->po_model->get_detail($doc->po_code, $item->code);

                      if( ! empty($pod))
                      {
                        $price = $pod->price;

                        $arr = array(
                          'receive_code' => $code,
                          'po_code' => $doc->po_code,
                          'style_code' => $item->style_code,
                          'product_code' => $item->code,
                          'product_name' => $item->name,
                          'price' => $price,
                          'qty' => $qty,
                          'amount' => $price * $qty,
                          'receive_date' => $receive_date,
                          'zone_code' => $zone->code,
                          'warehouse_code' => $zone->warehouse_code,
                          'status' => 'N'
                        );
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "Item : {$item->code} ไม่อยู่ในรายการสั่งผลิต เลขที่ {$doc->po_code}";
                      }
                    }
                    else
                    {
                      $arr = array(
                      'receive_code' => $code,
                      'po_code' => NULL,
                      'style_code' => $item->style_code,
                      'product_code' => $item->code,
                      'product_name' => $item->name,
                      'price' => $item->cost,
                      'qty' => $qty,
                      'amount' => $item->cost * $qty,
                      'receive_date' => $receive_date,
                      'zone_code' => $zone_code,
                      'status' => 'N'
                      );
                    }

                    if($sc === TRUE)
                    {
                      if( ! $this->receive_po_model->add_detail($arr))
                      {
                        $sc = FALSE;
                        $this->error = "Add Item failed";
                      }
                    }
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Invalid zone";
                }
							}
							else
							{
								$sc = FALSE;
								$this->error = "Item not found";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Item Qty must be greater than 0";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Missing required parameter : barcode";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Document Status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document code";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: Document code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}





  public function add_details($code)
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('purchase/po_model');
    $details = json_decode($this->input->post('details'));

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 0)
      {
        if(!empty($details))
        {
          $this->db->trans_begin();

          foreach($details as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            $row = $this->receive_po_model->get_detail_by_product_and_zone($code, $rs->product_code, $rs->zone_code);

            if( ! empty($row))
            {
              $qty = $row->qty + $rs->qty;

              $arr = array(
                'qty' => $qty,
                'amount' => $qty * $row->price
              );

              if( ! $this->receive_po_model->update_detail($row->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update item {$rs->product_code}";
              }
            }
            else
            {
              $item = $this->products_model->get($rs->product_code);
              $zone = $this->zone_model->get($rs->zone_code);
              $pod = $this->po_model->get_detail($doc->po_code, $item->code);
              $price = empty($pod) ? $item->cost : $pod->price;

              $arr = array(
                'receive_code' => $code,
                'po_code' => (empty($pod) ? NULL : $pod->po_code),
                'style_code' => $item->style_code,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'price' => $price,
                'qty' => $rs->qty,
                'amount' => $price * $rs->qty,
                'receive_date' => db_date($rs->receive_date),
                'zone_code' => $zone->code,
                'warehouse_code' => $zone->warehouse_code,
                'status' => 'N',
                'user' => $this->_user->uname
              );

              if( ! $this->receive_po_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to insert item {$rs->product_code}";
              }
            }
          }

          if( $sc === TRUE)
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
          $this->error = 'no items found';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'Invalid document number';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



	public function update_receive_table()
	{
		$sc = TRUE;
		$ds = array();
		$code = $this->input->get('code');

		if(!empty($code))
		{
      $doc = $this->receive_po_model->get($code);

      if( ! empty($doc))
      {
        $details = $this->receive_po_model->get_details($code);

        if(!empty($details))
        {
          $no = 1;
          $total_qty = 0;
          $total_amount = 0;

          foreach($details as $rs)
          {
            $arr = array(
              'no' => $no,
              'id' => $rs->id,
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'price' => number($rs->price, 2),
              'qty' => number($rs->qty),
              'amount' => number($rs->amount, 2),
              'receive_date' => empty($rs->receive_date) ? NULL : thai_date($rs->receive_date),
              'zone_code' => $rs->zone_code,
              'zone_name' => $rs->zone_name,
              'open' => $rs->status === 'N' ? TRUE : FALSE
            );

            array_push($ds, $arr);
            $no++;
            $total_qty += $rs->qty;
            $total_amount += $rs->amount;
          }

          $arr = array(
            'total_qty' => number($total_qty),
            'total_amount' => number($total_amount, 2)
          );

          array_push($ds, $arr);
        }
        else
        {
          $arr = array("nodata" => "Nodata");
          array_push($ds, $arr);
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Document Number";
      }
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : Document code";
		}


		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



  public function delete_detail($id)
  {
    if($this->receive_po_model->drop_detail($id))
    {
      echo 'success';
    }
    else
    {
      echo 'delete_fail';
    }
  }


  public function delete_details($code)
  {
    if($this->receive_po_model->drop_details($code))
    {
      echo 'success';
    }
    else
    {
      echo 'delete_fail';
    }
  }



  public function save($code)
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('purchase/po_model');
    $this->load->model('stock/stock_model');

    $auto_close = getConfig('AUTO_CLOSE_PO');

    $doc = $this->receive_po_model->get($code);

    if(empty($doc))
    {
      $sc = FALSE;
      $this->error = 'doc_not_found';
    }

    if($sc === TRUE && $doc->status > 0)
    {
      $sc = FALSE;
      $this->error = 'invalid_status';
    }

    if($sc === TRUE)
    {
      $details = $this->receive_po_model->get_unsave_details($code);

      if(!empty($details))
      {
        $this->db->trans_begin();

        foreach($details as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          //---- update stock
          if(! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, $rs->qty))
          {
            $sc = FALSE;
            $this->error = "Update stock failed";
          }


          //--- update stock movement
          $arr = array(
            'reference' => $rs->receive_code,
            'warehouse_code' => $rs->warehouse_code,
            'zone_code' => $rs->zone_code,
            'product_code' => $rs->product_code,
            'move_in' => $rs->qty,
            'move_out' => 0,
            'date_add' => $doc->date_add
          );

          if(! $this->movement_model->move_in($rs->receive_code, $rs->product_code, $rs->warehouse_code, $rs->zone_code, $rs->qty, $doc->date_add))
          {
            $sc = FALSE;
            $this->error = 'Insert Movement failed';
          }

          //--- update po
          if(!empty($rs->po_code))
          {
            if(! $this->po_model->update_received($rs->po_code, $rs->product_code, $rs->qty))
            {
              $sc = FALSE;
              $this->error = 'Update PO receive qty failed : '.$rs->po_code;
            }

            //--- change po to partially received
            if(! $this->po_model->change_status($rs->po_code, 2))
            {
              $sc = FALSE;
              $this->error = 'Change PO status failed : '.$rs->po_code;
            }

            //--- close po if complete
            if($auto_close == 1)
            {
              if($this->po_model->is_all_done($rs->po_code))
              {
                if(! $this->po_model->close_po($rs->po_code))
                {
                  $sc = FALSE;
                  $this->error = 'Close PO failed : '.$rs->po_code;
                }
              }
            }
          }

          //--- change status to 'S'
          if(! $this->receive_po_model->update_detail($rs->id, array('status' => 'S')))
          {
            $sc = FALSE;
            $this->error = 'Change item status failed : '.$rs->product_code;
          }

        } //--- end foreach

        //---- change doc status
        //--- 0 = not save, 1 = saved , 2 = Cancle
        if(! $this->receive_po_model->set_status($code, 1))
        {
          $sc = FALSE;
          $this->error = 'Change document status failed : '.$code;
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
        $this->error = 'no_data_found';
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function unsave($code)
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('purchase/po_model');
    $this->load->model('stock/stock_model');

    $doc = $this->receive_po_model->get($code);

    if(empty($doc))
    {
      $sc = FALSE;
      $this->error = 'doc_not_found';
    }

    if($sc === TRUE && $doc->status != 1)
    {
      $sc = FALSE;
      $this->error = 'invalid_status';
    }

    if($sc === TRUE)
    {
      $details = $this->receive_po_model->get_saved_details($code);
      if(!empty($details))
      {
        $this->db->trans_begin();

        $wh = $this->warehouse_model->get($doc->warehouse_code);
        $gb_auz = getConfig('ALLOW_UNDER_ZERO');
        $auz = $gb_auz == 1 ? TRUE : ($wh->auz == 1 ? TRUE : FALSE);
        foreach($details as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          //---- update stock

          if($auz OR $this->stock_model->is_enough($doc->zone_code, $rs->product_code, $rs->qty))
          {
            if(! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, $rs->qty*-1))
            {
              $sc = FALSE;
              $this->error = "Update stock failed";
            }

            //--- update stock movement
            if(! $this->movement_model->drop_move_in($doc->code, $rs->product_code, $doc->zone_code))
            {
              $sc = FALSE;
              $this->error = 'Insert Movement failed';
            }

            //--- update po
            if(!empty($rs->po_code))
            {
              if(! $this->po_model->update_received($rs->po_code, $rs->product_code, $rs->qty * -1))
              {
                $sc = FALSE;
                $this->error = 'Update PO receive qty failed : '.$rs->po_code;
              }

              //--- update po status
              if(! $this->po_model->change_status($rs->po_code))
              {
                $sc = FALSE;
                $this->error = 'Change PO status failed : '.$rs->po_code;
              }

              //--- unclose po detail
              $this->po_model->unvalid_detail($rs->po_code, $rs->product_code);

            }

            //--- change status to 'N'
            if(! $this->receive_po_model->update_detail($rs->id, array('status' => 'N')))
            {
              $sc = FALSE;
              $this->error = 'Change item status failed : '.$rs->product_code;
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = 'stock_not_enough'.' : '.$doc->zone_code .' : '.$rs->product_code;
          }

        } //--- end foreach

        //---- change doc status
        //--- 0 = not save, 1 = saved , 2 = Cancle
        if(! $this->receive_po_model->set_status($code, 0))
        {
          $sc = FALSE;
          $this->error = 'Change document status failed : '.$code;
        }

        if($this->db->trans_status() === TRUE OR $sc === TRUE)
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
        $this->error = 'no_data_found';
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function un_close_po($code)
  {
    $sc = TRUE;
    $this->load->model('purchase/po_model');

    $received = $this->po_model->get_sum_received($code); //--- รับแล้วเท่าไร
    $status = $received > 0 ? 2 : 1; //--- ถ้าเคยรับมาแล้วสถานะเป็น part
    return $this->po_model->un_close_po($code, $status);
  }

  public function cancle_received()
  {
    if($this->input->post('code'))
    {
      $this->load->model('inventory/movement_model');
      $this->load->model('purchase/po_model');
      $code = $this->input->post('code');
      $rs = $this->receive_po_model->get($code);
      if(!empty($rs->po_code))
      {
        $po = $this->po_model->get($rs->po_code);
      }

      $details = $this->receive_po_model->get_details($code);
			$this->db->trans_start();

      if(!empty($details))
      {

        foreach($details as $ds)
        {
          //--- ลบรายการรับเข้า
          $this->receive_po_model->drop_detail($ds->id);

          //---- ลบ movement
          $this->movement_model->drop_move_in($rs->code, $ds->product_code, $rs->zone_code);

          //--- update po details
          if($rs->status == 1 && !empty($rs->po_code))
          {
            $this->po_model->update_received($rs->po_code, $ds->product_code, ($ds->qty * (-1)));
            $this->po_model->unvalid_detail($rs->po_code, $ds->product_code);
          }
        }

        if(!empty($rs->po_code))
        {
          $po_status = $this->po_model->count_received($rs->po_code) > 0 ? 2 : 1;
          $this->po_model->change_status($rs->po_code, $po_status);
        }
      }


			$this->receive_po_model->set_status($code, 2); //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก


			$this->db->trans_complete();


      if($this->db->trans_status() === FALSE)
      {
        echo 'cancle_fail';
      }
      else
      {
        echo 'success';
      }
    }
    else
    {
      echo 'doc_not_found';
    }

  }



  public function get_po_details()
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $this->load->model('purchase/po_model');
    $po_code = $this->input->get('po_code');
    $po = $this->po_model->get($po_code);
    if(!empty($po))
    {
      if($po->status == 0 OR $po->status == 3 OR $po->status == 4)
      {
        $sc = FALSE;
        $this->error = 'po_error';
      }
      else
      {
        $details = $this->po_model->get_details($po_code);
        //$rate = (getConfig('RECEIVE_OVER_PO') * 0.01);
        $ds = array();

        if(!empty($details))
        {
          $no = 1;
          foreach($details as $rs)
          {
            $backlogs = ($rs->qty - $rs->received) < 0 ? 0 : $rs->qty - $rs->received;
            //$limit = $rs->qty + ($rs->qty* $rate);
            $arr = array(
              'no' => $no,
              //'barcode' => $this->products_model->get_barcode($rs->product_code),
              'pdCode' => $rs->product_code,
              'pdName' => $rs->product_name,
              'price' => $rs->price,
              'qty' => $backlogs <= 0 ? '' :$backlogs,
              //'limit' => ($limit - $rs->received) < 0 ? 0 : $limit - $rs->received,
              'backlogs' => number($backlogs)
            );

            array_push($ds, $arr);
            $no++;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = 'no_content';
        }
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = 'doc_not_found';
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  public function edit($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->helper('warehouse');

    $document = $this->receive_po_model->get($code);

    if(!empty($document))
    {
      $zone = $this->zone_model->get($document->zone_code);

      if(!empty($zone))
      {
        $document->zone_name = $zone->name;
      }
      else
      {
        $document->zone_name = '';
      }
    }

    $details = $this->receive_po_model->get_details($code);

    $ds['doc'] = $document;
    $ds['details'] = $details;
    $this->load->view('inventory/receive_po/receive_po_edit', $ds);
  }



  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $this->load->model('purchase/po_model');
      $this->load->model('masters/vender_model');

      $doc = $this->receive_po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $vender = $this->vender_model->get($ds->venderCode);

          if( ! empty($vender))
          {
            $po = $this->po_model->get($ds->poCode);

            if(!empty($po))
            {
              if($po->status == 0)
              {
                $sc = FALSE;
                $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ยังไม่บันทึกใบสั่งซื้อ';
              }
              else if($po->status == 3)
              {
                $sc = FALSE;
                $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ใบสั่งซื้อถูกปิดแล้ว';
              }
              else if($po->status == 4)
              {
                $sc = FALSE;
                $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ใบสั่งซื้อถูกยกเลิกแล้ว';
              }
            }


            if($sc === TRUE)
            {
              $arr = array(
                'date_add' => db_date($ds->date_add, TRUE),
                'posting_date' => db_date($ds->post_date),
                'vender_code' => $vender->code,
                'vender_name' => $vender->name,
                'po_code' => empty($po) ? NULL : $po->code,
                'invoice_code' => $ds->invoice,
                'warehouse_code' => $ds->warehouse_code,
                'remark' => get_null($ds->remark),
                'update_user' => $this->_user->uname
              );

              if(!$this->receive_po_model->update($ds->code, $arr))
              {
                $sc = FALSE;
                $this->error = 'update_failed';
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "รหัสผู้ขายไม่ถูกต้อง";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $this->response($sc);
  }


  public function add_new()
  {
    $this->load->helper('warehouse');
    $this->load->view('inventory/receive_po/receive_po_add');
  }


  public function add()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $this->load->model('purchase/po_model');
      $this->load->model('masters/vender_model');

      $date_add = db_date($ds->date_add, TRUE);
      $vender = $this->vender_model->get($ds->venderCode);

      if( ! empty($vender))
      {
        if( ! empty($ds->invoice))
        {
          $po = $this->po_model->get($ds->poCode);

          if( ! empty($po))
          {
            if($po->status == 0)
            {
              $sc = FALSE;
              $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ยังไม่บันทึกใบสั่งซื้อ';
            }
            else if($po->status == 3)
            {
              $sc = FALSE;
              $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ใบสั่งซื้อถูกปิดแล้ว';
            }
            else if($po->status == 4)
            {
              $sc = FALSE;
              $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ใบสั่งซื้อถูกยกเลิกแล้ว';
            }
          }

          if($sc === TRUE)
          {
            $code = $this->get_new_code($date_add);

            $arr = array(
              'code' => $code,
              'bookcode' => getConfig('BOOK_CODE_RECEIVE_PO'),
              'vender_code' => $vender->code,
              'vender_name' => $vender->name,
              'po_code' => empty($po) ? NULL : $po->code,
              'invoice_code' => $ds->invoice,
              'warehouse_code' => $ds->warehouse_code,
              'remark' => get_null($ds->remark),
              'date_add' => $date_add,
              'posting_date' => db_date($ds->post_date),
              'user' => $this->_user->uname
            );

            if(!$this->receive_po_model->add($arr))
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }
          }
        }
        else
        {
          $sc === TRUE;
          $this->error = "กรุณาระบุใบส่งสินค้า";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "รหัสผู้จำหน่ายไม่ถูกต้อง";
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
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function get_vender_by_po($po_code)
  {
    $this->load->model('purchase/po_model');
    $po = $this->po_model->get($po_code);
    if(!empty($po))
    {
      echo $po->vender_code.' | '.$po->vender_name;
    }
  }


  public function get_zone()
  {
    $this->load->model('masters/zone_model');
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $zone = $this->zone_model->get($code);

      if(empty($zone))
      {
        $sc = FALSE;
        $this->error = "Not found";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : zone code";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $zone : NULL
    );

    echo json_encode($arr);
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_PO');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_PO');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_po_model->get_max_code($pre);
    if(!empty($code))
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
    $filter = array('code','invoice','po','vender','from_date','to_date');
    clear_filter($filter);
  }

} //--- end class
