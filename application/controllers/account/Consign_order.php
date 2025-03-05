<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Consign_order extends PS_Controller
{
  public $menu_code = 'ACCSOD';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = '';
	public $title = 'ตัดยอดขาย';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'account/consign_order';
    $this->load->model('account/consign_order_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');
    $this->load->helper('discount');
    $this->load->helper('zone');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'customer' => get_filter('customer', 'customer', ''),
      'zone' => get_filter('zone', 'zone', 'all'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', ''),
      'status' => get_filter('status', 'status', 'all'),
      'ref_code' => get_filter('ref_code', 'ref_code', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->consign_order_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs = $this->consign_order_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if( ! empty($docs))
    {
      foreach($docs as $rs)
      {
        $rs->amount = $this->consign_order_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $docs;

		$this->pagination->initialize($init);
    $this->load->view('account/consign_order/consign_order_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('account/consign_order/consign_order_add');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $code = NULL;
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $date_add = db_date($ds->date_add, TRUE);
        $zone = $this->zone_model->get($ds->zone_code);
        $customer = $this->customers_model->get($ds->customer_code);

        if(empty($customer))
        {
          $sc = FALSE;
          $this->error = "Invalid customer code";
        }

        if(empty($zone))
        {
          $sc = FALSE;
          $this->error = "Invalid zone code";
        }

        if($sc === TRUE)
        {
          $code = $this->get_new_code($date_add);

          if( ! empty($code))
          {
            $arr = array(
              'code' => $code,
              'customer_code' => $customer->code,
              'customer_name' => $customer->name,
              'zone_code' => $zone->code,
              'zone_name' => $zone->name,
              'warehouse_code' => $zone->warehouse_code,
              'remark' => get_null($ds->remark),
              'date_add' => $date_add,
              'user' => $this->_user->uname
            );

            if( ! $this->consign_order_model->add($arr))
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
      'code' => $code
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $this->load->helper('print');
    $doc = $this->consign_order_model->get($code);
    $details = $this->consign_order_model->get_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    // $auz = $this->warehouse_model->is_auz($doc->warehouse_code);

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'auz' => getConfig('ALLOW_UNDER_ZERO') == 1 ? 1 : 0
    );

    $this->load->view('account/consign_order/consign_order_edit', $ds);
  }


  public function update()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      if( ! empty($ds))
      {
        $doc = $this->consign_order_model->get($ds->code);

        if( ! empty($doc))
        {
          if($doc->status == 0)
          {
            $this->db->trans_begin();

            //---- ถ้ามีการเปลี่ยน โซน ต้องทำการลบรายการก่อนหน้านี้ออก
            if($doc->zone_code != $ds->zone_code)
            {
              //--- delete all details
              if( ! $this->consign_order_model->drop_details($ds->code))
              {
                $sc = FALSE;
                $this->error = "Failed to delete previous document line";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'date_add' => db_date($ds->date_add, TRUE),
                'customer_code' => $ds->customer_code,
                'customer_name' => $ds->customer_name,
                'zone_code' => $ds->zone_code,
                'zone_name' => $ds->zone_name,
                'remark' => get_null($ds->remark)
              );

              if( ! $this->consign_order_model->update($ds->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to change document header";
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
            $sc = FALSE;
            $this->error = "Invalid document status";
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

    $this->response($sc);
  }


  public function cancel()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = $this->input->post('reason');

    if($this->pm->can_delete)
    {
      $doc = $this->consign_order_model->get($code);

      if($doc->status != 2)
      {

        $this->load->model('inventory/movement_model');
        $this->load->model('inventory/invoice_model');
        $this->load->model('account/order_credit_model');
        $this->load->model('account/order_repay_model');

        $this->db->trans_begin();

        if($doc->status == 1)
        {
          //-- check payment
          if( $this->order_repay_model->is_exists_reference($code))
          {
            $sc = FALSE;
            $this->error = "เอกสารนี้มีการรับชำระเงินแล้ว ไม่สามารถยกเลิกได้";
          }

          //--- ถ้ายังไม่รับชำระ ทำการ drop payment ที่ค้างไว้
          if($sc === TRUE)
          {
            if( ! $this->order_credit_model->delete($code))
            {
              $sc = FALSE;
              $this->error = "ลบยอดตั้งหนี้ของเอกสารนี้ไม่สำเร็จ";
            }
          }

          //--- roll back stock
          if($sc === TRUE)
          {
            $details = $this->consign_order_model->get_details($code);

            if( ! empty($details))
            {
              foreach($details as $rs)
              {
                if($rs->count_stock == 1)
                {
                  //--- 1. คืนสต็อกกลับเข้าโซน
                  if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, $rs->qty))
                  {
                    $sc = FALSE;
                    $this->error = "คืนสต็อกกลับโซนไม่สำเร็จ : {$rs->product_code}";
                  }
                }
              }
            }
          }

          //--- drop order sold
          if($sc === TRUE)
          {
            if( ! $this->invoice_model->drop_order_sold($code))
            {
              $sc = FALSE;
              $this->error = "ลบรายการบันทึกขายไม่สำเร็จ";
            }
          }

          //---- drop movement
          if($sc === TRUE)
          {
            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "ลบรายการเคลื่อนไหวสินค้าไม่สำเร็จ";
            }
          }
        } //--- end if status == 1


        //--- change line status to 2 (cancel)
        if($sc === TRUE)
        {
          if( ! $this->consign_order_model->update_details($code, array('status' => 2)))
          {
            $sc = FALSE;
            $this->error = "Failed to change line status";
          }
        }

        //--- change document status = 2 (cancel)
        if($sc === TRUE)
        {
          $arr = array(
            'status' => 2,
            'cancel_user' => $this->_user->uname,
            'cancel_date' => now(),
            'cancel_reason' => $reason
          );

          if( ! $this->consign_order_model->update($code, $arr))
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
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('permission');
    }

    $this->response($sc);
  }


  public function view_detail($code)
  {
    $this->load->helper('print');

    $doc = $this->consign_order_model->get($code);

    $details = $this->consign_order_model->get_details($code);

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

    $this->load->view('account/consign_order/consign_order_view_detail', $ds);
  }


  //---- add or update detail row by key in
  public function add_detail($code)
  {
    $sc = TRUE;
    if($this->input->post('product_code'))
    {
      $doc = $this->consign_order_model->get($code);

      if( ! empty($doc))
      {
        $this->load->model('stock/stock_model');

        $product_code = $this->input->post('product_code');
        $price = $this->input->post('price');
        $qty = $this->input->post('qty');
        $discLabel = $this->input->post('disc');
        $disc = parse_discount_text($discLabel, $price);
        $discount = $disc['discount_amount'];
        $amount = ($price - $discount) * $qty;
        $auz = is_true(getConfig('ALLOW_UNDER_ZERO')); //$this->warehouse_model->is_auz($doc->warehouse_code);
        $item = $this->products_model->get($product_code);
        $input_type = 1;  //--- 1 = key in , 2 = load diff, 3 = excel
        $stock = $item->count_stock == 1 ? $this->stock_model->get_stock_zone($doc->zone_code, $item->code) : 10000000;
        $c_qty = $item->count_stock == 1 ? $this->consign_order_model->get_unsave_qty($code, $item->code) : 0;
        $detail = $this->consign_order_model->get_exists_detail($code, $product_code, $price, $discLabel, $input_type);

        $id = NULL;

        if(empty($detail))
        {
          //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
          if(($qty + $c_qty) <= $stock OR $auz === TRUE)
          {
            //--- add new row
            $arr = array(
              'consign_code' => $code,
              'style_code' => $item->style_code,
              'product_code' => $item->code,
              'product_name' => $item->name,
              'cost' => $item->cost,
              'price' => $price,
              'qty' => $qty,
              'discount' => discountLabel($disc['discount1'], $disc['discount2'], $disc['discount3']),
              'discount_amount' => $discount * $qty,
              'amount' => $amount,
              'ref_code' => $doc->ref_code,
              'input_type' => $input_type,
              'count_stock' => $item->count_stock
            );

            $id = $this->consign_order_model->add_detail($arr); //-- return id if success

            if(empty($id))
            {
              $sc = FALSE;
              $this->error = "เพิ่มรายการไม่สำเร็จ";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ยอดในโซนไม่พอตัด";
          }
        }
        else
        {
          //-- update new rows
          //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
          $id = $detail->id;
          $new_qty = $qty + $detail->qty;

          if($new_qty <= $stock OR $auz === TRUE)
          {
            //--- add new row
            $arr = array(
              'qty' => $new_qty,
              'discount_amount' => $discount * $new_qty,
              'amount' => ($price - $discount) * $new_qty
            );

            if(! $this->consign_order_model->update_detail($id, $arr))
            {
              $sc = FALSE;
              $this->error = "ปรับปรุงรายการไม่สำเร็จ";
            }

          }
          else
          {
            $sc = FALSE;
            $this->error = "ยอดในโซนไม่พอตัด";
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
      $this->error = "รหัสสินค้าไม่ถูกต้อง";
    }

    if($sc === TRUE)
    {
      $rs = $this->consign_order_model->get_detail($id);

      $ds = array(
        'id' => $rs->id,
        'barcode' => $item->barcode,
        'product_code' => $rs->product_code,
        'product_name' => $rs->product_name,
        'product' => $rs->product_code.' : '.$rs->product_name,
        'price' => round($rs->price,2),
        'qty' => $rs->qty,
        'discount' => $rs->discount,
        'amount' => $rs->amount,
        'amountLabel' => number($rs->amount, 2)
      );
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function save_consign()
  {
    $sc = TRUE;

    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('account/payment_receive_model');
    $this->load->model("masters/warehouse_model");
    $this->load->model('masters/customers_model');
    $this->load->model('account/order_credit_model');

    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->consign_order_model->get($code);

      if( ! empty($doc))
      {
        $auz = is_true(getConfig('ALLOW_UNDER_ZERO'));

        //--- ยอดเงินรวมสำหรับบันทึกรับเงิน
        $pay_amount = 0;

        //--- customer data
        $customer = $this->customers_model->get_attribute($doc->customer_code);

        $this->db->trans_begin();

        //---- ถ้ายังไม่ได้บันทึก
        if( $doc->status == 0 )
        {
          $details = $this->consign_order_model->get_details($code);

          if( ! empty($details))
          {
            //--- check stock and update status each row
            foreach($details as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              //--- get item info
              $item = $this->products_model->get_attribute($rs->product_code);

              if( ! empty($item))
              {
                if($item->count_stock == 1)
                {
                  $stock = $this->stock_model->get_stock_zone($doc->zone_code, $rs->product_code);

                  if($rs->qty <= $stock OR $auz)
                  {
                    //--- 1. ตัดสต็อกออกจากโซน
                    if(! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, (-1) * $rs->qty))
                    {
                      $sc = FALSE;
                      $this->error = "ตัดสต็อกไม่สำเร็จ : {$rs->product_code}";
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "{$rs->product_code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$rs->qty}";
                  }

                  //--- 2. บันทึก movement
                  if($sc === TRUE)
                  {
                    if(! $this->movement_model->move_out($doc->code, $rs->product_code, $doc->warehouse_code, $doc->zone_code, $rs->qty, $doc->date_add))
                    {
                      $sc = FALSE;
                      $this->error = "บันทึก movement ไม่สำเร็จ : {$rs->product_code}";
                    }
                  }
                }


                //--- 3. บันทึกขาย
                //--- ข้อมูลสำหรับบันทึกยอดขาย
                if($sc === TRUE)
                {
                  $arr = array(
                    'reference' => $doc->code,
                    'role'   => 'M', //-- ตัดยอดฝากขาย
                    'role_name' => 'ตัดยอดขาย',
                    'payment_code'   => NULL,
                    'channels_code'  => NULL,
                    'product_code'  => $rs->product_code,
                    'product_name'  => $rs->product_name,
                    'product_style' => $rs->style_code,
                    'color_code' => $item->color_code,
                    'color_name' => $item->color_name,
                    'size_code' => $item->size_code,
                    'size_name' => $item->size_name,
                    'product_group_code' => $item->group_code,
                    'product_group_name' => $item->group_name,
                    'product_sub_group_code' => $item->sub_group_code,
                    'product_sub_group_name' => $item->sub_group_name,
                    'product_category_code' => $item->category_code,
                    'product_category_name' => $item->category_name,
                    'product_kind_code' => $item->kind_code,
                    'product_kind_name' => $item->kind_name,
                    'product_type_code' => $item->type_code,
                    'product_type_name' => $item->type_name,
                    'product_brand_code' => $item->brand_code,
                    'product_brand_name' => $item->brand_name,
                    'product_year' => $item->year,
                    'cost'  => $rs->cost,
                    'price'  => $rs->price,
                    'price_ex' => remove_vat($rs->price),
                    'sell'  => ($rs->amount/$rs->qty),
                    'qty'   => $rs->qty,
                    'unit_code' => $item->unit_code,
                    'unit_name' => $item->unit_name,
                    'discount_label'  => $rs->discount,
                    'discount_amount' => $rs->discount_amount,
                    'total_amount'   => $rs->amount,
                    'total_amount_ex' => remove_vat($rs->amount),
                    'total_cost'   => $rs->cost * $rs->qty,
                    'margin'  =>  remove_vat($rs->amount) - ($rs->cost * $rs->qty),
                    'id_policy'   => NULL,
                    'id_rule'     => NULL,
                    'customer_code' => $doc->customer_code,
                    'customer_name' => $doc->customer_name,
                    'customer_ref' => NULL,
                    'customer_group_code' => $customer->group_code,
                    'customer_group_name' => $customer->group_name,
                    'customer_kind_code' => $customer->kind_code,
                    'customer_kind_name' => $customer->kind_name,
                    'customer_type_code' => $customer->type_code,
                    'customer_type_name' => $customer->type_name,
                    'customer_class_code' => $customer->class_code,
                    'customer_class_name' => $customer->class_name,
                    'customer_area_code' => $customer->area_code,
                    'customer_area_name' => $customer->area_name,
                    'sale_code'   => $customer->sale_code,
                    'sale_name' => $customer->sale_name,
                    'user' => $doc->user,
                    'date_add'  => $doc->date_add,
                    'zone_code' => $doc->zone_code,
                    'warehouse_code'  => $doc->warehouse_code,
                    'update_user' => $this->_user->uname,
                    'is_count' => $item->count_stock
                  );

                  //--- 3. บันทึกยอดขาย
                  if( ! $this->delivery_order_model->sold($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'บันทึกขายไม่สำเร็จ';
                  }
                }

                if($sc === TRUE)
                {
                  $pay_amount += $rs->amount;

                  if( ! $this->consign_order_model->change_detail_status($rs->id, 1))
                  {
                    $sc = FALSE;
                    $this->error = "บันทึกรายการไม่สำเร็จ : {$rs->product_code}";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า : {$rs->product_code}";
              }
            } //-- end foreach
          }//--- end if details

          if($sc === TRUE)
          {
            if($pay_amount != 0)
            {
              //--- 4. ตั้งหนี้
              $arr = array(
                'order_code' => $doc->code,
                'customer_code' => $doc->customer_code,
                'delivery_date' => date('Y-m-d'),
                'due_date' => added_date(date('Y-m-d'), $customer->credit_term),
                'over_due_date' => added_date(date('Y-m-d'), $customer->credit_term + getConfig('OVER_DUE_DATE')),
                'amount' => $pay_amount,
                'paid' => 0,
                'balance' => $pay_amount
              );

              if($this->order_credit_model->is_exists($doc->code))
              {
                $this->order_credit_model->update($doc->code, $arr);
              }
              else
              {
                $this->order_credit_model->add($arr);
              }
            }
          }

          //--- if no error
          if($sc === TRUE)
          {
            if( ! $this->consign_order_model->change_status($code, 1))
            {
              $sc = FALSE;
              $this->error = "บันทึกสถานะเอกสารไม่สำเร็จ";
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
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
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

    $this->response($sc);
  }


  public function delete_detail($id)
  {
    $sc = TRUE;
    $ds = $this->consign_order_model->get_detail($id);
    if(!empty($ds))
    {
      if($ds->status == 1)
      {
        $sc = FALSE;
        $this->error = "รายการถูกบันทึกแล้วไม่สามารถลบได้";
      }
      else
      {
        if(! $this->consign_order_model->delete_detail($id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการที่ต้องการลบ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function delete_details()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $doc = $this->consign_order_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          if( ! empty($ds->rows))
          {
            if( ! $this->consign_order_model->delete_details_by_ids($ds->rows))
            {
              $sc = FALSE;
              $this->error = "Failed to delete selected rows";
            }
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
        $this->error = get_error_message('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
    }

    $this->response($sc);
  }


  public function import_pos_file($code, $target = 0)
  {
    $sc = TRUE;
    $this->load->library('excel');
    $this->load->model('stock/stock_model');

    $file = isset( $_FILES['excel'] ) ? $_FILES['excel'] : FALSE;

    if($file !== FALSE)
    {
      $file	= 'excel';

      $config = array(   // initial config for upload class
        "allowed_types" => "xlsx",
        "upload_path" => $this->config->item('consign_file_path'),
        "file_name"	=> $code.'-'.date('YmdHis'),
        "max_size" => 5120,
        "overwrite" => TRUE
      );

      $this->load->library("upload", $config);

      if(! $this->upload->do_upload($file))
      {
        $sc = FALSE;
        $this->error = $this->upload->display_errors();
      }
      else
      {
        $info = $this->upload->data();
        /// read file
        $excel = PHPExcel_IOFactory::load($info['full_path']);
        //get only the Cell Collection
        $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

        $i = 1;

        $doc = $this->consign_order_model->get($code);
        $auz = is_true(getConfig('ALLOW_UNDER_ZERO'));
        $rows = [];
        $billNo = "";
        $billDiscPercent = 0;
        $isCancel = FALSE;

        foreach($collection as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          if($i == 1)
          {
            if( empty($rs['I']))
            {
              $sc = FALSE;
              $this->error = "Template ไม่ถูกต้อง";
            }
          }

          if($i > 1)
          {

            $bill = trim($rs['C']);

            if( ! empty($bill))
            {
              $isCancel = trim($rs['B']) == "-" ? FALSE : TRUE;

              if( ! $isCancel)
              {
                $billNo = $bill;
                $bDisc = floatval($rs['M']);
                $bTotal = floatval($rs['Q']);
                $sTotal = $bDisc + $bTotal;
                $billDiscPercent = ($bTotal > 0 && $bDisc > 0) ? $bDisc / $sTotal : 0;
              }
              else
              {
                $billDiscPercent = 0;
              }
            }

            if( ! empty(trim($rs['D'])) && ! $isCancel)
            {
              $item = $this->products_model->get(trim($rs['D']));

              if( ! empty($item))
              {
                $price = trim($rs['H']);
                $disc = trim($rs['J']);
                $qty = trim($rs['I']);
                $disc = $disc > 0 ? $disc/$qty : 0;
                $total = trim($rs['L']);
                $priceAfDisc = $price - $disc;
                $exDisc = round($priceAfDisc * $billDiscPercent, 2);
                $finalItemDisc = $disc + $exDisc;
                $finalPrice = $priceAfDisc - $exDisc;
                $lineTotal = $finalPrice * $qty;

                $uniqueRow = $item->code.$price.$finalItemDisc;

                if(empty($rows[$uniqueRow]))
                {
                  $rows[$uniqueRow] = (object) array(
                  'consign_code' => $code,
                  'style_code' => $item->style_code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  'discount' => $finalItemDisc, //$disc,
                  'discount_amount' => $finalItemDisc * $qty,
                  'amount' => $lineTotal,
                  'ref_code' => $doc->ref_code,
                  'input_type' => 3,
                  'count_stock' => $item->count_stock
                  );
                }
                else
                {
                  $row = $rows[$uniqueRow];
                  $nQty = $row->qty + $qty;
                  $nDisc = $row->discount_amount + ($row->discount * $qty);
                  $nTotal = $row->amount + $lineTotal;

                  $rows[$uniqueRow]->qty = $nQty;
                  $rows[$uniqueRow]->discount_amount = $nDisc;
                  $rows[$uniqueRow]->amount = $nTotal;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "รหัสสินค้าไม่ถูกต้อง : {$rs['D']} @line {$i}";
              }
            } //-- endif ! $isCancel
          } //-- endif $i > 1;

          $i++;
        } //--- end foreach


        if($sc === TRUE && ! empty($rows))
        {
          $this->db->trans_begin();

          foreach($rows as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            if( ! $this->consign_order_model->add_detail($rs))
            {
              $sc = FALSE;
              $this->error = "Failed to insert item row @ {$rs->product_code}";
            }
          }

          if($sc === FALSE)
          {
            $this->db->trans_rollback();
          }
          else
          {
            $this->db->trans_commit();
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Upload file not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function import_excel_file($code, $target = 0)
  {
    $sc = TRUE;
    $this->load->library('excel');
    $this->load->model('stock/stock_model');

    $file = isset( $_FILES['excel'] ) ? $_FILES['excel'] : FALSE;

    if($file !== FALSE)
    {
      $file	= 'excel';

  		$config = array(   // initial config for upload class
  			"allowed_types" => "xlsx",
  			"upload_path" => $this->config->item('consign_file_path'),
  			"file_name"	=> $code.'-'.date('YmdHis'),
  			"max_size" => 5120,
  			"overwrite" => TRUE
  			);

  			$this->load->library("upload", $config);

  			if(! $this->upload->do_upload($file))
        {
          $sc = FALSE;
  				$this->error = $this->upload->display_errors();
  			}
        else
        {
          $info = $this->upload->data();
          /// read file
  				$excel = PHPExcel_IOFactory::load($info['full_path']);
  				//get only the Cell Collection
          $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

          $i = 1;

          $doc = $this->consign_order_model->get($code);
          $auz = is_true(getConfig('ALLOW_UNDER_ZERO'));
          $rows = [];

          $this->db->trans_begin();

          foreach($collection as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            if($i == 1)
            {
              if( ! empty($rs['I']))
              {
                $sc = FALSE;
                $this->error = "Template ไม่ถูกต้อง";
              }
            }

            if($i > 1)
            {
              //--- skip hrader row
              $product_code = $rs['A'];
              $price = $rs['B'];
              $qty = $rs['C'];
              $discLabel = $rs['D'];
              $item = $this->products_model->get($product_code);

              if( ! empty($item))
              {
                $disc = parse_discount_text($discLabel, $price);
                $discount = $disc['discount_amount'];
                $amount = ($price - $discount) * $qty;
                $input_type = 3;  //--- 1 = key in , 2 = load diff, 3 = excel
                $stock = $item->count_stock == 1 ? $this->stock_model->get_stock_zone($doc->zone_code, $item->code) : 10000000;
                $c_qty = $item->count_stock == 1 ? $this->consign_order_model->get_unsave_qty($code, $item->code) : 0;
                $detail = $this->consign_order_model->get_exists_detail($code, $product_code, $price, $discLabel, $input_type);

                if( empty($detail) )
                {
                  //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
                  if(($qty + $c_qty) <= $stock OR $auz)
                  {
                    //--- add new row
                    $arr = array(
                      'consign_code' => $code,
                      'style_code' => $item->style_code,
                      'product_code' => $item->code,
                      'product_name' => $item->name,
                      'cost' => $item->cost,
                      'price' => $price,
                      'qty' => $qty,
                      'discount' => discountLabel($disc['discount1'], $disc['discount2'], $disc['discount3']),
                      'discount_amount' => $discount * $qty,
                      'amount' => $amount,
                      'ref_code' => $doc->ref_code,
                      'input_type' => $input_type,
                      'count_stock' => $item->count_stock
                    );

                    if( ! $this->consign_order_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "เพิ่มรายการไม่สำเร็จ";
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "ยอดในโซนไม่พอตัด";
                  }
                }
                else
                {
                  //-- update new rows
                  //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
                  $new_qty = $qty + $detail->qty;

                  if($new_qty <= $stock OR $auz === TRUE)
                  {
                    //--- add new row
                    $arr = array(
                      'qty' => $new_qty,
                      'discount_amount' => $discount * $new_qty,
                      'amount' => ($price - $discount) * $new_qty
                    );

                    if(! $this->consign_order_model->update_detail($detail->id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "ปรับปรุงรายการไม่สำเร็จ";
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "ยอดในโซนไม่พอตัด";
                  }
                } //--- end if empty detail
              }
              else
              {
                $sc = FALSE;
                $this->error = "รหัสสินค้าไม่ถูกต้อง : {$product_code}";
              } //--- end if $item

            } //--- end if $i

            $i++;
          } //--- endforeach

          if($sc === FALSE)
          {
            $this->db->trans_rollback();
          }
          else
          {
            $this->db->trans_commit();
          }
        }
    }
  	else
    {
      $sc = FALSE;
      $this->error = "Upload file not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_active_check_list($zone_code)
  {
    $ds = array();
    $this->load->model('inventory/consign_check_model');
    $list = $this->consign_check_model->get_active_check_list($zone_code); //--- saved and not valid

    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          'code' => $rs->code,
          'date_add' => thai_date($rs->date_add)
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }


  function load_check_diff($code)
  {
    $sc = TRUE;
    if($this->input->post('check_code'))
    {
      $this->load->model('inventory/consign_check_model');
      $doc = $this->consign_order_model->get($code);
      $check_code = $this->input->post('check_code');
      $input_type = 2; //---- load diff
      $details = $this->consign_check_model->get_diff_details($check_code);
      if(!empty($details))
      {
        $this->db->trans_start();
        $this->consign_order_model->update_ref_code($code, $check_code);
        foreach($details as $rs)
        {
          $item = $this->products_model->get($rs->product_code);
          $discLabel = $this->consign_order_model->get_item_gp($item->code, $doc->zone_code);
          $disc = parse_discount_text($discLabel, $item->price);
          $discount = $disc['discount_amount'];
          $amount = ($item->price - $discount) * $rs->diff;
          $detail = $this->consign_order_model->get_exists_detail($code, $item->code, $item->price, $discLabel, $input_type);
          if(empty($detail))
          {
            //--- add new row
            $arr = array(
              'consign_code' => $code,
              'style_code' => $item->style_code,
              'product_code' => $item->code,
              'product_name' => $item->name,
              'cost' => $item->cost,
              'price' => $item->price,
              'qty' => $rs->diff,
              'discount' => $discLabel,
              'discount_amount' => $discount * $rs->diff,
              'amount' => $amount,
              'ref_code' => $check_code,
              'input_type' => $input_type
            );

            $this->consign_order_model->add_detail($arr);
          }
          else
          {

            //-- update new rows
            //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
            $new_qty = $rs->diff + $detail->qty;
            //--- add new row
            $arr = array(
              'qty' => $new_qty,
              'discount_amount' => $discount * $new_qty,
              'amount' => ($item->price - $discount) * $new_qty
            );

            $this->consign_order_model->update_detail($detail->id, $arr);
          }
        }
      }

      $this->consign_check_model->update_ref_code($check_code, $code, 1);

      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $this->error = "เพิ่มรายการไม่สำเร็จ";
        $sc = FALSE;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสารกระทบยอด";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function remove_import_details($code)
  {
    $sc = TRUE;
    if($this->input->post('check_code'))
    {
      $this->load->model('inventory/consign_check_model');
      $doc = $this->consign_order_model->get($code);
      $check_code = $this->input->post('check_code');
      $input_type = 2; //---- load diff

      $saved = $this->consign_order_model->has_saved_imported($code, $check_code);

      if($saved === FALSE)
      {
        $this->db->trans_start();

        //--- delete details
        $this->consign_order_model->drop_import_details($code, $check_code);

        //--- update ref_code
        $this->consign_order_model->update_ref_code($code, NULL);

        //-- unlink consign_check
        $this->consign_check_model->update_ref_code($check_code, NULL, 0);

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถลบได้เนื่องจากรายการถูกบันทึกแล้ว";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสารกระทบยอด";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update_row()
  {
    $sc = TRUE;

    $id = $this->input->post('id');
    $code = $this->input->post('code');
    $zone_code = $this->input->post('zone_code');

    if( ! empty($id))
    {
      $row = $this->consign_order_model->get_detail($id);

      if( ! empty($row))
      {
        $qty = $this->input->post('qty');
        $prevQty = $this->input->post('prevQty');
        $price = $this->input->post('price');
        $discLabel = $this->input->post('disc');
        $disc = parse_discount_text($discLabel, $price);
        $discount = $disc['discount_amount'];
        $amount = ($price - $discount) * $qty;

        $auz = is_true(getConfig('ALLOW_UNDER_ZERO')); //$this->warehouse_model->is_auz($doc->warehouse_code);
        $item = $this->products_model->get($row->product_code);
        $stock = $item->count_stock == 1 ? $this->stock_model->get_stock_zone($zone_code, $item->code) : 10000000;
        $c_qty = $item->count_stock == 1 ? $this->consign_order_model->get_unsave_qty($code, $item->code) : 0;
        $c_qty = $c_qty > 0 ? $c_qty - $prevQty : 0;
        $c_qty = $c_qty > 0 ? $c_qty : 0;
        $new_qty = $qty + $c_qty;

        if($new_qty <= $stock OR $auz == TRUE)
        {
          $arr = array(
            'price' => $price,
            'qty' => $qty,
            'discount' => discountLabel($disc['discount1'], $disc['discount2'], $disc['discount3']),
            'discount_amount' => $discount * $qty,
            'amount' => $amount
          );

          if( ! $this->consign_order_model->update_detail($row->id, $arr))
          {
            $sc = FALSE;
            $this->error = "แก้ไขรายการไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สต็อกคงเหลือไม่เพียงพอ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการ";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
    }

    $this->response($sc);
  }


  public function get_item_by_code()
  {
    if($this->input->get('code'))
    {
      $this->load->model('stock/stock_model');

      $product_code = $this->input->get('code');
      $zone_code = $this->input->get('zone_code');
      $item = $this->products_model->get($product_code);
      if(!empty($item))
      {
        $gp  = $this->consign_order_model->get_item_gp($item->code, $zone_code);
        $stock = $item->count_stock == 1 ? $this->stock_model->get_stock_zone($zone_code, $item->code) : 0;

        $arr = array(
          'pdCode' => $item->code,
          'barcode' => $item->barcode,
          'product' => $item->code,
          'price' => round($item->price, 2),
          'disc' => $gp,
          'stock' => $stock,
          'count_stock' => $item->count_stock
        );

        $sc = json_encode($arr);
      }
      else
      {
        $sc = 'สินค้าไม่ถูกต้อง';
      }

      echo $sc;
    }
    else
    {
      echo "สินค้าไม่ถูกต้อง";
    }
  }


  public function get_item_by_barcode()
  {
    if($this->input->get('barcode'))
    {
      $this->load->model('stock/stock_model');

      $barcode = $this->input->get('barcode');
      $zone_code = $this->input->get('zone_code');
      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        $gp  = $this->consign_order_model->get_item_gp($item->code, $zone_code);
        $stock = $item->count_stock == 1 ? $this->stock_model->get_stock_zone($zone_code, $item->code) : 0;

        $arr = array(
          'pdCode' => $item->code,
          'barcode' => $item->barcode,
          'product' => $item->code,
          'price' => round($item->price, 2),
          'disc' => $gp,
          'stock' => $stock,
          'count_stock' => $item->count_stock
        );

        $sc = json_encode($arr);
      }
      else
      {
        $sc = 'สินค้าไม่ถูกต้อง';
      }

      echo $sc;
    }
    else
    {
      echo "สินค้าไม่ถูกต้อง";
    }
  }


  public function get_sample_file($token)
  {
    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sample');

    //--- header
    $this->excel->getActiveSheet()->setCellValue('A1', 'Items');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Price');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Qty');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Discount');

    //--- sample data
    $this->excel->getActiveSheet()->setCellValue('A2', 'WA-1234-AA-L');
    $this->excel->getActiveSheet()->setCellValue('B2', '399');
    $this->excel->getActiveSheet()->setCellValue('C2', '2');
    $this->excel->getActiveSheet()->setCellValue('D2', '20%+5%');


    setToken($token);

    $file_name = "Consign_sample.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  public function print_consign($code)
  {
    $this->load->model('address/customer_address_model');
    $this->load->library('printer');

    $doc = $this->consign_order_model->get($code);

    if( ! empty($doc))
    {
      $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
      $details = $this->consign_order_model->get_details($code);
      $doc->emp_name = $this->user_model->get_name($doc->user);

      $ds = array(
        'doc' => $doc,
        'customer' => $this->customers_model->get($doc->customer_code),
        'address' => $this->customer_address_model->get_customer_bill_to_address($doc->customer_code),
        'details' => $details
      );

      $this->load->view('print/print_consign_sold', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGN_SOLD');
    $run_digit = getConfig('RUN_DIGIT_CONSIGN_SOLD');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->consign_order_model->get_max_code($pre);
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
    $filter = array('code', 'customer', 'zone', 'from_date', 'to_date', 'status', 'ref_code');
    clear_filter($filter);
  }


} //---- end class
 ?>
