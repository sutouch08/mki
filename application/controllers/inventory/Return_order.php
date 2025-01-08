<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_order extends PS_Controller
{
  public $menu_code = 'ICRTOR';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'คืนสินค้า(ลดหนี้ขาย)';
  public $filter;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_order';
    $this->load->model('inventory/return_order_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');

  }


  public function index()
  {
    $filter = array(
      'code'    => get_filter('code', 'code', ''),
      'invoice' => get_filter('invoice', 'invoice', ''),
      'customer_code' => get_filter('customer_code', 'customer_code', ''),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', ''),
      'status' => get_filter('status', 'status', 'all'),
      'approve' => get_filter('approve', 'approve', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->return_order_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->return_order_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_order_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_order_model->get_sum_amount($rs->code);
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      }
    }

    $filter['docs'] = $document;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_order/return_order_list', $filter);
  }




  public function add_details($code)
  {

		//---1. add details to table
		//---2. บันทึกขาย (ยอดติดลบ)
		//---3. เพิ่มยอดสต็อก
		//---4. บันทึก movement
		//---5.

    $sc = TRUE;

    if($this->input->post('qty'))
    {
			$this->load->model('orders/orders_model');
			$this->load->model('account/order_credit_model');
      $this->load->model('inventory/movement_model');
			$this->load->model('inventory/delivery_order_model');
			$this->load->model('stock/stock_model');
			$this->load->model('masters/channels_model');
			$this->load->model('masters/payment_methods_model');
			$this->load->helper('discount');

      //--- start transection
      $this->db->trans_begin();

      $doc = $this->return_order_model->get($code);
      if(!empty($doc))
      {
				$customer = $this->customers_model->get_attribute($doc->customer_code);

        $qtys = $this->input->post('qty');
        $prices = $this->input->post('price');
        $sold = $this->input->post('sold_qty');
        $discount = $this->input->post('discount');

        //--- drop old detail
        $this->return_order_model->drop_details($code);

        if(!empty($qtys))
        {

          foreach($qtys as $item => $invoice)
          {
						if($sc === FALSE)
						{
							break;
						}


						$pd = $this->products_model->get_attribute($item);

            foreach($invoice as $inv => $qty)
            {
							if($sc === FALSE)
							{
								break;
							}

							$order = $this->orders_model->get($inv);
							$is_term = $order->is_term;

							$price = $prices[$item][$inv];
							$discText = $discount[$item][$inv];
							$disc = parse_discount_text($discText, $price);

              $amount = $qty * ($price - $disc['discount_amount']);
							$vat_amount = $amount * ($pd->vat_rate * 0.01);

              $arr = array(
                'return_code' => $code,
                'invoice_code' => $inv,
                'product_code' => $item,
                'product_name' => $pd->name,
								'unit_code' => $pd->unit_code,
								'unit_name' => $pd->unit_name,
                'sold_qty' => $sold[$item][$inv],
                'qty' => $qty,
                'price' => $price,
                'discount_percent' => $discText,
                'amount' => $amount,
								'vat_code' => $pd->vat_code,
								'vat_rate' => get_zero($pd->vat_rate),
                'vat_amount' => $vat_amount,
								'valid' => 1
              );


              if(! $this->return_order_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = 'บันทึกรายการไม่สำเร็จ';
              }

							//--- update stock
							if($pd->count_stock)
							{
								if(! $this->stock_model->update_stock_zone($doc->zone_code, $item, $qty))
								{
									$sc = FALSE;
									$this->error = "ปรับยอดสต็อกไม่สำเร็จ : {$item}";
								}

								//--- บันทึก movement
								$ds = array(
									'reference' => $code,
									'warehouse_code' => $doc->warehouse_code,
									'zone_code' => $doc->zone_code,
									'product_code' => $item,
									'move_in' => $qty,
									'date_add' => $doc->date_add
								);

								if(!$this->movement_model->add($ds))
								{
									$sc = FALSE;
									$this->error = 'บันทึก movement ไม่สำเร็จ';
								}
							}

							$disc = parse_discount_text($discText, $price);

							//--- ข้อมูลสำหรับบันทึกยอดขาย
							$arr = array(
											'reference' => $doc->code,
											'role'   => 'S',
											'role_name' => 'ขาย',
											'channels_code' => $order->channels_code,
											'channels_name' => $this->channels_model->get_name($order->channels_code),
											'payment_code' => $order->payment_code,
											'payment_name' => $this->payment_methods_model->get_name($order->payment_code),
											'product_code'  => $pd->code,
											'product_name'  => $pd->name,
											'product_style' => $pd->style_code,
											'color_code' => $pd->color_code,
											'color_name' => $pd->color_name,
											'size_code' => $pd->size_code,
											'size_name' => $pd->size_name,
											'product_group_code' => $pd->group_code,
											'product_group_name' => $pd->group_name,
											'product_sub_group_code' => $pd->sub_group_code,
											'product_sub_group_name' => $pd->sub_group_name,
											'product_category_code' => $pd->category_code,
											'product_category_name' => $pd->category_name,
											'product_kind_code' => $pd->kind_code,
											'product_kind_name' => $pd->kind_name,
											'product_type_code' => $pd->type_code,
											'product_type_name' => $pd->type_name,
											'product_brand_code' => $pd->brand_code,
											'product_brand_name' => $pd->brand_name,
											'product_year' => $pd->year,
											'cost'  => $pd->cost,
											'price'  => $price,
											'price_ex' => remove_vat($price),
											'sell'  => $price - $disc['discount_amount'],
											'qty'   => $qty,
											'unit_code' => $pd->unit_code,
											'unit_name' => $pd->unit_name,
											'vat_code' => $pd->vat_code,
											'vat_rate' => get_zero($pd->vat_rate),
											'discount_label'  => $discText,
											'avgBillDiscAmount' => 0, //--- average per single item count
											'discount_amount' => $qty * $disc['discount_amount'],
											'total_amount'   => ($amount * (-1)),
											'total_amount_ex' => remove_vat($amount) * (-1),
											'total_cost'   => ($pd->cost * $qty) * (-1),
											'margin'  =>  (round(remove_vat($amount),2) - ($pd->cost * $qty)) * (-1),
											'id_policy'   => NULL,
											'id_rule'     => NULL,
											'customer_code' => $doc->customer_code,
											'customer_name' => $customer->name,
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
											'update_user' => get_cookie('uname'),
											'budget_code' => NULL
							);

							if(! $this->delivery_order_model->sold($arr))
							{
								$sc = FALSE;
								$this->error = "บันทึกยอดขายไม่สำเร็จ";
							}

							if($sc === TRUE && $is_term)
							{
								if($this->order_credit_model->is_exists($doc->code))
								{
									$this->order_credit_model->update_amount($doc->code, $amount * (-1));
									$this->order_credit_model->recal_balance($doc->code);
								}
								else
								{
									$arr = array(
										'order_code' => $doc->code,
										'customer_code' => $doc->customer_code,
										'delivery_date' => $doc->date_add,
										'due_date' => $doc->date_add,
										'over_due_date' => $doc->date_add
									);

									$this->order_credit_model->add($arr);
								}

							}

            }

          } //--- endforeach

          $this->return_order_model->set_status($code, 1);

        }
        else
        {
          $sc = FALSE;
          set_error('ไม่พบจำนวนในการรับคืน');
        } //--- end if empty qty


        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
          set_error($this->db->error());
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
        //--- empty document
        $sc = FALSE;
        set_error('ไม่พบเลขที่เอกสาร');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบข้อมูลในฟอร์ม');
    }

    if($sc === TRUE)
    {
      set_message('Success');
      redirect($this->home.'/view_detail/'.$code);
    }
    else
    {
      redirect($this->home.'/edit/'.$code);
    }

  }




  public function delete_detail($id)
  {
    $rs = $this->return_order_model->delete_detail($id);
    echo $rs === TRUE ? 'success' : 'ลบรายการไม่สำเร็จ';
  }


  public function unsave($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
		$this->load->model('account/order_credit_model');
		$this->load->model('inventory/delivery_order_model');
		$this->load->model('stock/stock_model');
		$this->load->helper('discount');
    if($this->pm->can_edit)
    {
			$doc = $this->return_order_model->get($code);

			if(!empty($doc))
			{
				$this->db->trans_begin();

	      if($this->return_order_model->set_status($code, 0) === FALSE)
	      {
	        $sc = FALSE;
	        $message = 'เปลี่ยนสถานะเอกสารไม่สำเร็จ';
	      }

				if(! $this->return_order_model->unvalid_details($code))
				{
					$sc = FALSE;
					$this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
				}

				if($sc === TRUE)
				{
					if($this->movement_model->drop_movement($code) === FALSE)
	        {
	          $sc = FALSE;
	          $message = 'ลบ movement ไม่สำเร็จ';
	        }
				}

				$details = $this->return_order_model->get_details($code);

				//----- update stock
				if($sc === TRUE)
				{
					if(!empty($details))
					{
						foreach($details as $rs)
						{
							$item = $this->products_model->get($rs->product_code);
							if(!empty($item))
							{
								if($item->count_stock)
								{
									if(! $this->stock_model->update_stock_zone($doc->zone_code, $item->code, (-1)*$rs->qty ))
									{
										$sc = FALSE;
										$this->error = "ปรับยอดสต็อกไม่สำเร็จ : {$item->code}";
									}
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "ไม่พบรหัสสินค้าในระบบ : {$rs->product_code}";
							}
						} //--- end foreach
					}
				}

				//--- drop sold data
				if($sc === TRUE)
				{
					if(! $this->delivery_order_model->drop_sold_data($code))
					{
						$sc = FALSE;
						$this->error = "ลบรายการบันทึกขายไม่สำเร็จ";
					}
				}

				//--- drop order credit
				if($sc === TRUE)
				{
					if(! $this->order_credit_model->delete($code))
					{
						$sc = FALSE;
						$this->error = "ลบรายการตั้งหนี้ไม่สำเร็จ";
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
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}

    }
    else
    {
      $sc = FALSE;
      $message = 'คุณไม่มีสิทธิ์ในการยกเลิกการบันทึก';
    }


		$this->response($sc);
  }





  public function add_new()
  {
    $this->load->view('inventory/return_order/return_order_add');
  }


  public function add()
  {
    if($this->input->post('date_add'))
    {
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = trim($this->input->post('invoice'));
      $customer_code = trim($this->input->post('customer_code'));
      $zone = $this->zone_model->get($this->input->post('zone_code'));
      $remark = trim($this->input->post('remark'));

      $code = $this->get_new_code($date_add);
      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RETURN_ORDER'),
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'user' => get_cookie('uname'),
        'date_add' => $date_add,
        'remark' => $remark
      );

      $rs = $this->return_order_model->add($arr);
      if($rs === TRUE)
      {
        redirect($this->home.'/edit/'.$code);
      }
      else
      {
        set_error("เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง");
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error("ไม่พบข้อมูลเอกสารหรือฟอร์มว่างเปล่า กรุณาตรวจสอบ");
      redirect($this->home.'/add_new');
    }
  }


  public function edit($code)
  {
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
		$doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);

    $details = $this->return_order_model->get_details($code);

    $detail = array();
      //--- ถ้าไม่มีรายละเอียดให้ไปดึงจากใบกำกับมา
    if(empty($details))
    {
      $details = $this->return_order_model->get_invoice_details($doc->invoice);
      if(!empty($details))
      {

        foreach($details as $rs)
        {
          $returned_qty = $this->return_order_model->get_returned_qty($doc->invoice, $rs->product_code);
          $qty = $rs->qty - $returned_qty;
          if($qty > 0)
          {
            $dt = new stdClass();
            $dt->id = 0;
            $dt->invoice_code = $doc->invoice;
            $dt->barcode = $this->products_model->get_barcode($rs->product_code);
            $dt->product_code = $rs->product_code;
            $dt->product_name = $rs->product_name;
            $dt->sold_qty = $qty;
            $dt->discount_percent = $rs->discount;
            $dt->qty = 0;
            $dt->price = $rs->price;
            $dt->amount = 0;

            $detail[] = $dt;
          }
        }
      }
    }
    else
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_order_model->get_returned_qty($doc->invoice, $rs->product_code);
        $qty = $rs->sold_qty - ($returned_qty - $rs->qty);
        if($qty > 0)
        {
          $dt = new stdClass();
          $dt->id = $rs->id;
          $dt->invoice_code = $doc->invoice;
          $dt->barcode = $this->products_model->get_barcode($rs->product_code);
          $dt->product_code = $rs->product_code;
          $dt->product_name = $rs->product_name;
          $dt->sold_qty = $qty;
          $dt->discount_percent = $rs->discount_percent;
          $dt->qty = $rs->qty;
          $dt->price = $rs->price;
          $dt->amount = $rs->qty * ($rs->price * (100 - ($rs->discount_percent * 0.01)));

          $detail[] = $dt;
        }
      }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $detail
    );

    if($doc->status == 0)
    {
      $this->load->view('inventory/return_order/return_order_edit', $ds);
    }
    else
    {
      $this->load->view('inventory/return_order/return_order_view_detail', $ds);
    }

  }



  public function update()
  {
    $sc = TRUE;
    if($this->input->post('return_code'))
    {
      $code = $this->input->post('return_code');
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = trim($this->input->post('invoice'));
      $customer_code = $this->input->post('customer_code');
      $zone = $this->zone_model->get($this->input->post('zone_code'));
      $remark = $this->input->post('remark');

      $arr = array(
        'date_add' => $date_add,
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'remark' => $remark,
        'update_user' => get_cookie('uname')
      );

      if($this->return_order_model->update($code, $arr) === FALSE)
      {
        $sc = FALSE;
        $message = 'ปรับปรุงข้อมูลไม่สำเร็จ';
      }

    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function view_detail($code)
  {
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);

    $return_details = $this->return_order_model->get_details($code);
    $details = array();

    if(!empty($return_details))
    {
      foreach($return_details as $rs)
      {
        $dt = new stdClass();
        $dt->id = $rs->id;
        $dt->invoice_code = $rs->invoice_code;
        $dt->barcode = $this->products_model->get_barcode($rs->product_code);
        $dt->product_code = $rs->product_code;
        $dt->product_name = $rs->product_name;
        $dt->price = $rs->price;
        $dt->discount_percent = $rs->discount_percent;
        $dt->sold_qty = $rs->sold_qty;
        $dt->qty = $rs->qty;
        $dt->amount = $rs->amount;
        $details[] = $dt;
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('inventory/return_order/return_order_view_detail', $ds);
  }


	//--- get customer code name by invoice code
	public function get_customer_by_invoice()
	{
		$invoice = trim($this->input->get('invoice_code'));

		if(!empty($invoice))
		{
			$customer = $this->return_order_model->get_customer_by_invoice($invoice);
			if(!empty($customer))
			{
				$arr = array(
					'code' => $customer->customer_code,
					'name' => $customer->customer_name
				);

				echo json_encode($arr);
			}
			else
			{
				echo "not found";
			}
		}
		else
		{
			echo "not found";
		}
	}


	//---- check invoice code is valid
	public function check_invoice()
	{
		$sc = TRUE;
		$invoice = trim($this->input->get('invoice_code'));
		if(!empty($invoice))
		{
			$valid = $this->return_order_model->get_valid_invoice($invoice);
			if(empty($valid))
			{
				$sc = FALSE;
			}
		}
		else
		{
			$sc = FALSE;
		}

		echo $sc === TRUE ? 'success' : "Not found";
	}




  public function get_invoice($invoice)
  {
    $sc = TRUE;
    $details = $this->return_order_model->get_invoice_details($invoice);
    $ds = array();
    if(empty($details))
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_order_model->get_returned_qty($invoice, $rs->product_code);
        $qty = $rs->qty - $returned_qty;
        $row = new stdClass();
        if($qty > 0)
        {
          $row->barcode = $this->products_model->get_barcode($rs->product_code);
          $row->invoice = $invoice;
          $row->code = $rs->product_code;
          $row->name = $rs->product_name;
          $row->price = round($rs->price, 2);
          $row->discount = round($rs->discount, 2);
          $row->qty = round($qty, 2);
          $row->amount = 0;
          $ds[] = $row;
        }
      }
    }

    echo $sc === TRUE ? json_encode($ds) : $message;
  }




  public function print_detail($code)
  {
    $this->load->library('printer');
		$this->load->model('address/customer_address_model');
		$this->load->helper('address');

    $doc = $this->return_order_model->get($code);
		$adr = $this->customer_address_model->get_customer_bill_to_address($doc->customer_code);
		$sale = $this->customers_model->get_saleman($doc->customer_code);
		$customer = $this->customers_model->get($doc->customer_code);

		if(!empty($adr))
		{
			$address = array(
				'address' => $adr->address,
				'sub_district' => $adr->sub_district,
				'district' => $adr->district,
				'province' => $adr->province,
				'postcode' => $adr->postcode
			);
		}
		else
		{
			$address = array(
				'address' => NULL,
				'sub_district' => NULL,
				'district' => NULL,
				'province' => NULL,
				'postcode' => NULL
			);
		}


    $details = $this->return_order_model->get_details($code);
		$use_vat = getConfig('USE_VAT') == 1 ? TRUE : FALSE;

		$ds = array(
			'title' => ($use_vat ? 'ใบลดหนี้/ใบกำกับภาษี' : 'ใบคืนสินค้า'),
			'order' => $doc,
			'adr' => $adr,
			'address' => parse_address($address), //--- address_helper
			'details' => $details,
			'saleman' => $sale,
			'customer' => $customer,
			'use_vat' => $use_vat
		);

    $this->load->view('print/print_return', $ds);
  }



  public function cancle_return($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
		$this->load->model('account/order_credit_model');
		$this->load->model('inventory/delivery_order_model');
		$this->load->model('stock/stock_model');
		$this->load->helper('discount');

    if($this->pm->can_delete)
    {
			$doc = $this->return_order_model->get($code);

			if(!empty($doc) && $doc->status != 2)
			{
				$this->db->trans_begin();

	      if($this->return_order_model->set_status($code, 2) === FALSE)
	      {
	        $sc = FALSE;
	        $message = 'ยกเลิกเอกสารไม่สำเร็จ';
	      }

				if($sc === TRUE)
				{
					if($this->movement_model->drop_movement($code) === FALSE)
	        {
	          $sc = FALSE;
	          $message = 'ลบ movement ไม่สำเร็จ';
	        }
				}

				$details = $this->return_order_model->get_details($code);

				//----- update stock
				if($sc === TRUE)
				{
					if(!empty($details))
					{
						foreach($details as $rs)
						{
							$item = $this->products_model->get($rs->product_code);
							if(!empty($item))
							{
								if($item->count_stock)
								{
									if(! $this->stock_model->update_stock_zone($doc->zone_code, $item->code, (-1)*$rs->qty ))
									{
										$sc = FALSE;
										$this->error = "ปรับยอดสต็อกไม่สำเร็จ : {$item->code}";
									}
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "ไม่พบรหัสสินค้าในระบบ : {$rs->product_code}";
							}
						} //--- end foreach
					}
				}

				//--- drop sold data
				if($sc === TRUE)
				{
					if(! $this->delivery_order_model->drop_sold_data($code))
					{
						$sc = FALSE;
						$this->error = "ลบรายการบันทึกขายไม่สำเร็จ";
					}
				}

				//--- drop order credit
				if($sc === TRUE)
				{
					if(! $this->order_credit_model->delete($code))
					{
						$sc = FALSE;
						$this->error = "ลบรายการตั้งหนี้ไม่สำเร็จ";
					}
				}


        if($sc === TRUE)
        {
          if(! $this->return_order_model->cancle_details($code))
          {
            $sc = FALSE;
            $this->error = "ยกเลิกรายการไม่สำเร็จ";
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
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}

    }
    else
    {
      $sc = FALSE;
      $message = 'Missing Permission';
    }


		$this->response($sc);
  }




  public function get_item()
  {
    if($this->input->post('barcode'))
    {
      $barcode = trim($this->input->post('barcode'));
      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        echo json_encode($item);
      }
      else
      {
        echo 'not-found';
      }
    }
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_ORDER');
    $run_digit = getConfig('RUN_DIGIT_RETURN_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_order_model->get_max_code($pre);
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
    $filter = array('code', 'invoice', 'customer_code', 'from_date', 'to_date');
    clear_filter($filter);
  }


} //--- end class
?>
