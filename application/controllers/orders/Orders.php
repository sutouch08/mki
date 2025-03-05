<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends PS_Controller
{
  public $menu_code = 'SOODSO';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ออเดอร์';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/orders';
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('orders/discount_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');

    $this->load->helper('order');
    $this->load->helper('channels');
    $this->load->helper('payment_method');
    $this->load->helper('sender');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('saleman');
    $this->load->helper('state');
    $this->load->helper('product_images');
    $this->load->helper('discount');

    $this->filter = getConfig('STOCK_FILTER');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'order_code', ''),
      'customer' => get_filter('customer', 'order_customer', ''),
      'sale_code' => get_filter('sale_code', 'sale_code', 'all'),
      'type_code' => get_filter('type_code', 'type_code', 'all'),
      'user' => get_filter('user', 'order_user', 'all'),
      'reference' => get_filter('reference', 'order_reference', ''),
      'reference2' => get_filter('reference2', 'reference2', ''),
      'ship_code' => get_filter('shipCode', 'order_shipCode', ''),
      'channels' => get_filter('channels', 'order_channels', ''),
      'payment' => get_filter('payment', 'order_payment', ''),
      'from_date' => get_filter('fromDate', 'order_fromDate', ''),
      'to_date' => get_filter('toDate', 'order_toDate', ''),
      'is_paid' => get_filter('is_paid', 'is_paid', 'all'),
			'notSave' => get_filter('notSave', 'notSave', NULL),
      'onlyMe' => get_filter('onlyMe', 'onlyMe', NULL),
      'isExpire' => get_filter('isExpire', 'isExpire', NULL),
      'order_by' => get_filter('order_by', 'order_by', 'code'),
      'sort_by' => get_filter('sort_by', 'sort_by', 'DESC')
    );

    $state = array(
      '1' => get_filter('state_1', 'state_1', 'N'),
      '2' => get_filter('state_2', 'state_2', 'N'),
      '3' => get_filter('state_3', 'state_3', 'N'),
      '4' => get_filter('state_4', 'state_4', 'N'),
      '5' => get_filter('state_5', 'state_5', 'N'),
      '6' => get_filter('state_6', 'state_6', 'N'),
      '7' => get_filter('state_7', 'state_7', 'N'),
      '8' => get_filter('state_8', 'state_8', 'N'),
      '9' => get_filter('state_9', 'state_9', 'N')
    );

    $state_list = array();

    $button = array();

    for($i =1; $i <= 9; $i++)
    {
    	if($state[$i] === 'Y')
    	{
    		$state_list[] = $i;
    	}

      $btn = 'state_'.$i;
      $button[$btn] = $state[$i] === 'Y' ? 'btn-info' : '';
    }

    $button['not_save'] = empty($filter['notSave']) ? '' : 'btn-info';
    $button['only_me'] = empty($filter['onlyMe']) ? '' : 'btn-info';
    $button['is_expire'] = empty($filter['isExpire']) ? '' : 'btn-info';


    $filter['state_list'] = empty($state_list) ? NULL : $state_list;

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->orders_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->orders_model->get_data($filter, $perpage, $this->uri->segment($segment));
    $ds       = array();
    if(!empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->channels_name = $this->channels_model->get_name($rs->channels_code);
        $rs->payment_name  = $this->payment_methods_model->get_name($rs->payment_code);
        $rs->payment_role  = $this->payment_methods_model->get_role($rs->payment_code);
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
        $rs->total_amount  = $rs->total_amount + $rs->shipping_fee + $rs->service_fee;
        $rs->state_name    = get_state_name($rs->state);
        $ds[] = $rs;
      }
    }

    $filter['orders'] = $ds;
    $filter['state'] = $state;
    $filter['btn'] = $button;

		$this->pagination->initialize($init);
    $this->load->view('orders/orders_list', $filter);
  }


	public function update_detail()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$qty = $this->input->post('qty');
		$price = $this->input->post('price');
		$disc = $this->input->post('discount');
		$total_amount = $this->input->post('total_amount');

		$auz = get_auz(); //--- Allow under zero stock : return TRUE or FALSE;
		$ds = $this->orders_model->get_detail($id);

		if(!empty($ds))
		{
			//---- หายอดต่างว่าต้องเพิ่มหรือลดจากเดิมเท่าไร
			//---- ถ้ายอดเป็นบวก คือต้องเพิ่ม ต้องตรวจสอบว่ายอดพอให้เพิ่มมั้ย
			//---- ถ้ายอดเป็นลบ ต้องเอาออก ไม่ต้องตรวจสอบ เอาออกได้เลย
			//---- ถ้ายอดเป็น 0 แสดงว่ายอดเดิม ไม่ต้องทำอะไร  update ราคากับส่วนลดอย่างเดียว
			$diff = $qty - $ds->qty;

			if($diff > 0)
			{
				$stock = $this->get_sell_stock($ds->product_code);

				if($stock >= $diff OR $ds->is_count == 0 OR $auz === TRUE)
				{
					//---- ถ้ายอดคงเหลือมากกว่ายอดที่เพิ่ม หรือ สินค้าไม่นับสต็อก หรือ อนุญาติให้ติดลบได้

					$arr = array(
						'qty' => $qty,
						'price' => $price,
						'discount1' => number($disc['discLabel1'],2) . $disc['discUnit1'],
						'discount2' => number($disc['discLabel2'],2) . $disc['discUnit2'],
						'discount3' => number($disc['discLabel3'],2) . $disc['discUnit3'],
						'discount_amount' => $disc['discountAmount'] * $qty,
						'total_amount' => $total_amount,
						'id_rule' => NULL,
						'update_user' => get_cookie('uname'),
            'valid' => 0
					);

					if(!$this->orders_model->update_detail($id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update item failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "ยอดคงเหลือไม่เพียงพอ คงเหลือ : {$stock}";
				}
			}
			else
			{
				$arr = array(
					'qty' => $qty,
					'price' => $price,
					'discount1' => number($disc['discLabel1'],2) . $disc['discUnit1'],
					'discount2' => number($disc['discLabel2'],2) . $disc['discUnit2'],
					'discount3' => number($disc['discLabel3'],2) . $disc['discUnit3'],
					'discount_amount' => $disc['discountAmount'] * $qty,
					'total_amount' => $total_amount,
					'id_rule' => NULL,
					'update_user' => get_cookie('uname')
				);

				if(!$this->orders_model->update_detail($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update item failed";
				}
			}

			if($sc === TRUE)
			{
				$this->orders_model->set_status($ds->order_code, 0);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Order line id : {$id}";
		}

		$this->response($sc);
	}


	public function update_remark()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
    $reference = get_null(trim($this->input->post('reference')));
    $shipping_code = get_null(trim($this->input->post('shipping_code')));
		$sender_id = $this->input->post('sender_id');
		$remark = get_null(trim($this->input->post('remark')));

		$arr = array(
      'reference' => $reference,
      'shipping_code' => $shipping_code,
			'sender_id' => $sender_id,
			'remark' => $remark
		);

		if(!$this->orders_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update failed";
		}

		$this->response($sc);
	}


	public function update_shipping_fee()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$fee = $this->input->post('fee');

		$arr = array(
			'shipping_fee' => $fee
		);

		if(!$this->orders_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update Shipping fee failed";
		}
		else
		{
			update_order_total_amount($code);
		}

		$this->response($sc);
	}


	public function update_service_fee()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$fee = $this->input->post('fee');

		$arr = array(
			'service_fee' => $fee
		);

		if(!$this->orders_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update Service fee failed";
		}
		else
		{
			update_order_total_amount($code);
		}

		$this->response($sc);
	}


	public function update_bill_discount()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$bDiscAmount = $this->input->post('bDiscAmount');

		$arr = array(
			'bDiscAmount' => $bDiscAmount
		);

		if(! $this->orders_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update Bill discount failed";
		}
		else
		{
			update_order_total_amount($code);
		}

		$this->response($sc);
	}


  public function add_new()
  {
    $role = 'S';
    $limit = get_zero(getConfig('SYSTEM_ORDER_LIMIT'));

    if(! $this->orders_model->is_limit($role, $limit))
    {
      $this->load->view('orders/orders_add');
    }
    else
    {
      set_error("ไม่สามารถเพิ่มเอกสารได้เนื่องจากจำนวนเอกสารเกินจำนวนที่จำกัด");
      redirect($this->home);
    }
  }


  public function add_tags()
  {
    $sc = TRUE;

    $name = trim($this->input->post('name'));

    if( ! empty($name))
    {
      if( ! $this->db->insert('order_tags', ['name' => $name]))
      {
        $sc = FALSE;
        $this->error = "เพิ่ม tags ไม่สำเร็จ";
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $role = 'S'; //--- S = ขาย
  			$limit = get_zero(getConfig('SYSTEM_ORDER_LIMIT'));

        if(! $this->orders_model->is_limit($role, $limit))
        {
          $this->load->model('inventory/invoice_model');
  				$this->load->model('address/address_model');

          $book_code = getConfig('BOOK_CODE_ORDER');
          $date_add = db_date($ds->date_add);
          $code = $this->get_new_code($date_add);

          $has_term = $this->payment_methods_model->has_term($ds->payment_code);
          $sale_code = get_null($this->customers_model->get_sale_code($ds->customer_code));
          $sender_id = get_null($ds->sender_id);
  				$id_address = empty($ds->customer_ref) ? $this->address_model->get_shipping_address_id($ds->customer_code) : $this->address_model->get_shipping_address_id($ds->customer_ref);

          //--- check over due
          $is_strict = getConfig('STRICT_OVER_DUE') == 1 ? TRUE : FALSE;
          $overDue = $is_strict ? $this->invoice_model->is_over_due($ds->customer_code) : FALSE;

          //--- ถ้ามียอดค้างชำระ และ เป็นออเดอร์แบบเครดิต
          //--- ไม่ให้เพิ่มออเดอร์
          if($overDue && $has_term)
          {
            $sc = FALSE;
            $this->error = "มียอดค้างชำระเกินกำหนดไม่อนุญาติให้ขาย";
          }

          if($sc === TRUE)
          {
            $arr = array(
              'date_add' => $date_add,
              'code' => $code,
              'role' => $role,
              'bookcode' => $book_code,
              'reference' => get_null($ds->reference),
              'reference2' => get_null($ds->reference2),
              'customer_code' => $ds->customer_code,
              'customer_name' => $ds->customer_name,
              'customer_ref' => $ds->customer_ref,
              'channels_code' => $ds->channels_code,
              'payment_code' => $ds->payment_code,
              'sale_code' => $sale_code,
              'type_code' => get_null($ds->type_code),
              'is_term' => ($has_term === TRUE ? 1 : 0),
              'user' => $this->_user->uname,
  						'address_id' => $id_address,
              'sender_id' => $sender_id,
              'tags' => get_null($ds->tags),
              'remark' => get_null($ds->remark)
            );

            if( ! $this->orders_model->add($arr))
            {
              $sc = FALSE;
              $this->error = get_error_message('insert', "order");
            }

            if($sc === TRUE)
            {
              $arr = array(
                'order_code' => $code,
                'state' => 1,
                'update_user' => $this->_user->uname
              );

              $this->order_state_model->add_state($arr);
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่สามารถเพิ่มเอกสารได้เนื่องจากจำนวนเอกสารเกินจำนวนที่จำกัด";
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


  public function get_customer_unpaid_amount()
  {
    $sc = TRUE;
    $customer_code = $this->input->get('customer_code');
    $order_code = $this->input->get('order_code');
    $balance = $this->orders_model->get_order_balance_by_customer($customer_code, $order_code);

    echo empty($balance) ? 0 : $balance;
  }


  public function get_csr_code()
  {
    $csr_code = "not_found";
    $customer_code = $this->input->post('customer_code');

    if( ! empty($customer_code))
    {
      $customer = $this->customers_model->get($customer_code);

      if( ! empty($customer))
      {
        $csr_code = $customer->type_code;
      }
    }

    echo $csr_code;
  }


  public function get_customer()
  {
    $txt = trim($_REQUEST['term']);
    $sc = [];

    $this->db
    ->select('c.code, c.name, c.type_code, c.sale_code, c.channels_code, s.name as sale_name')
    ->from('customers AS c')
    ->join('saleman AS s', 'c.sale_code = s.code', 'left');

		if($txt != '*')
		{
			$this->db
			->group_start()
			->like('c.code', $txt)
			->or_like('c.name', $txt)
			->group_end();
		}

		$rs = $this->db->order_by('c.code', 'ASC')->limit(50)->get();

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rs)
      {
        $sc[] = array(
          'label' => $rs->code.' | '.$rs->name,
          'code' => $rs->code,
          'name' => $rs->name,
          'type_code' => $rs->type_code,
          'channels_code' => $rs->channels_code,
          'sale_code' => $rs->sale_code,
          'sale_name' => $rs->sale_name
        );
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }


	//---- load quotation
	public function load_quotation($order_code, $quotation_no)
	{
		if(!empty($quotation_no))
		{
			//--- load model
			$this->load->model('orders/quotation_model');

			$quotation = $this->quotation_model->get_details($quotation_no);
			if(!empty($quotation))
			{
				foreach($quotation as $qt)
				{
					$arr = array(
						'order_code' => $order_code,
						'style_code' => $qt->style_code,
						'product_code' => $qt->product_code,
						'product_name' => $qt->product_name,
						'cost' => $qt->cost,
						'price' => $qt->price,
						'qty' => $qt->qty,
						'unit_code' => $qt->unit_code,
						'discount1' => $qt->discount1,
						'discount2' => $qt->discount2,
						'discount3' => $qt->discount3,
						'discount_amount' => $qt->discount_amount,
						'total_amount' => $qt->total_amount,
						'is_count' => $qt->count_stock
					);

					$this->orders_model->add_detail($arr);
				}
			}

			//--- update reference
			$arr = array(
				'reference' => $order_code
			);

			$this->quotation_model->update($quotation_no, $arr);

			return TRUE;
		}

		return FALSE;
	}



	public function reload_quotation()
	{
		$sc = TRUE;

		$code = $this->input->get('order_code');
		$qt_no = $this->input->get('qt_no');

		if(!empty($code))
		{
			//--- load model
			$this->load->model('orders/quotation_model');
			$order = $this->orders_model->get($code);
			if(!empty($order))
			{
				//---- order state ต้องยังไม่ถูกดึงไปจัด
				if($order->state <= 3)
				{

					//---- start transection
					$this->db->trans_begin();
					//--- มีอยู่แต่ต้องการเอาออก
					if(empty($qt_no) && !empty($order->qt_no))
					{
						//--- 2. ลบรายการที่มีในออเดอร์แก่า
						if($this->orders_model->clear_order_detail($code))
						{
              update_order_total_amount($code);

							//---- update qt no on order
							$arr = array(
								'qt_no' => NULL,
								'status' => 0
							);

							if(! $this->orders_model->update($code, $arr))
							{
								$sc = FALSE;
								$this->error = "ลบเลขที่ใบเสนอราคาไม่สำเร็จ";
							}
							else
							{
								//--- update reference quotation
								$arr = array(
									'reference' => NULL,
									'is_closed' => 0
								);

								if(! $this->quotation_model->update($order->qt_no, $arr))
								{
									$sc = FALSE;
									$this->error = "แก้ไขเลขที่อ้างอิงในใบเสนอราคาไม่สำเร็จ";
								}
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "ลบรายการไม่สำเร็จ";
						}
					}
					else
					{
						if(!empty($qt_no))
						{
							//--- ยังไม่มี หรือ มีแล้วต้องการเปลี่ยน
							$qt = $this->quotation_model->get($qt_no);

							if(! empty($qt))
							{
								//---- 1. ดึงรายการในใบเสนอราคามาเช็คก่อนว่ามีรายการหรือไม่
								$is_exists = $this->quotation_model->is_exists_details($qt_no);

								if($is_exists === TRUE)
								{
									//--- 2. ลบรายการที่มีในออเดอร์แก่า
									if($this->orders_model->clear_order_detail($code))
									{
										//--- 3. เพิ่มรายการใหม่
										$rs = $this->load_quotation($code, $qt_no);
										if($rs)
										{
                      update_order_total_amount($code);

											//---4. เปลี่ยนเลขที่ qt_no ใน ตาราง orders
											$arr = array(
												'qt_no' => $qt_no,
												'status' => 0
											);

											if($this->orders_model->update($code, $arr))
											{
												//--- 5. update order no ในตาราง order_quotation
												$arr = array(
													'reference' => $code
												);

												if(! $this->quotation_model->update($qt_no, $arr))
												{
													$sc = FALSE;
													$this->error = "Update order no failed";
												}
											}
											else
											{
												$sc = FALSE;
												$this->error = "Update Quotation no failed";
											}
										}
										else
										{
											$sc = FALSE;
											$this->error = "โหลดรายการใหม่ไม่สำเร็จ";
										}
									}
									else
									{
										$sc = FALSE;
										$this->error = "ลบรายการเก่าไม่สำเร็จ";
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "ไม่พบรายการในใบเสนอราคา";
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "ใบเสนอราคาไม่ถูกต้อง";
							} //--- end if empty qt
						}

					} //--- end if empty qt_no


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
					$this->error = "ออเดอร์อยุ๋ในสถานะที่ไม่สามารถแก้ไขรายการได้";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่พบข้อมูลออเดอร์";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบเลขที่เอกสาร";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}





  public function add_detail($order_code)
  {
		$this->load->model('masters/vat_model');
    $result = TRUE;
    $err = "";
    $auz = get_auz(); //--- Allow under zero stock : return TRUE or FALSE;
    $err_qty = 0;
    $data = $this->input->post('data');
    $order = $this->orders_model->get($order_code);
    if(!empty($data))
    {
      foreach($data as $rs)
      {
        $code = $rs['code']; //-- รหัสสินค้า
        $qty = $rs['qty'];
        $item = $this->products_model->get($code);

        if( $qty > 0 )
        {
          //$qty = ceil($qty);

          //---- ยอดสินค้าที่่สั่งได้
          $sumStock = $this->get_sell_stock($code);


          //--- ถ้ามีสต็อกมากว่าที่สั่ง หรือ เป็นสินค้าไม่นับสต็อก หรือ ติดลบได้
          if( $sumStock >= $qty OR $item->count_stock == 0 OR $auz)
          {

            //---- ถ้ายังไม่มีรายการในออเดอร์
            if( $this->orders_model->is_exists_detail($order_code, $code) === FALSE )
            {
              //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
              $discount = array(
                'amount' => 0,
                'id_rule' => NULL,
                'discLabel1' => 0,
                'discLabel2' => 0,
                'discLabel3' => 0
              );

              if($order->role == 'S')
              {
                $discount = $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);
              }

              if($order->role == 'C' OR $order->role == 'N')
              {
                $gp = $order->gp;
                //------ คำนวณส่วนลดใหม่
      					$step = explode('+', $gp);
      					$discAmount = 0;
      					$discLabel = array(0, 0, 0);
      					$price = $item->price;
      					$i = 0;
      					foreach($step as $discText)
      					{
      						if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
      						{
      							$disc = explode('%', $discText);
      							$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
      							$amount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
      							$discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
      							$discAmount += $amount;
      							$price -= $amount;
      						}

      						$i++;
      					}

                $total_discount = $qty * $discAmount; //---- ส่วนลดรวม
      					//$total_amount = ( $qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย
                $discount['amount'] = $total_discount;
                $discount['discLabel1'] = $discLabel[0];
                $discount['discLabel2'] = $discLabel[1];
                $discount['discLabel3'] = $discLabel[2];
              }

              $arr = array(
                "order_code"	=> $order_code,
                "style_code"		=> $item->style_code,
                "product_code"	=> $item->code,
                "product_name"	=> addslashes($item->name),
                "cost"  => $item->cost,
                "price"	=> $item->price,
                "qty"		=> $qty,
                "unit_code" => $item->unit_code,
                "vat_code" => $item->vat_code,
                "vat_rate" => $this->vat_model->get_rate($item->vat_code),
                "discount1"	=> $discount['discLabel1'],
                "discount2" => $discount['discLabel2'],
                "discount3" => $discount['discLabel3'],
                "discount_amount" => $discount['amount'],
                "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                "id_rule"	=> $discount['id_rule'],
                "is_count" => $item->count_stock
              );

              if( $this->orders_model->add_detail($arr) === FALSE )
              {
                $result = FALSE;
                $error = "Error : Insert fail";
                $err_qty++;
              }
            }
            else  //--- ถ้ามีรายการในออเดอร์อยู่แล้ว
            {
              $detail 	= $this->orders_model->get_order_detail($order_code, $item->code);
              $qty			= $qty + $detail->qty;

              $discount = array(
                'amount' => 0,
                'id_rule' => NULL,
                'discLabel1' => 0,
                'discLabel2' => 0,
                'discLabel3' => 0
              );

              //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
              if($order->role == 'S')
              {
                $discount 	= $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);
              }


              $arr = array(
                "qty"		=> $qty,
                "discount1"	=> $discount['discLabel1'],
                "discount2" => $discount['discLabel2'],
                "discount3" => $discount['discLabel3'],
                "discount_amount" => $discount['amount'],
                "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                "id_rule"	=> $discount['id_rule'],
                "valid" => 0
              );

              if( $this->orders_model->update_detail($detail->id, $arr) === FALSE )
              {
                $result = FALSE;
                $error = "Error : Update Fail";
                $err_qty++;
              }
            }	//--- end if isExistsDetail
          }
          else 	// if getStock
          {
            $result = FALSE;
            $error = "Error : สินค้าไม่เพียงพอ";
          } 	//--- if getStock
        }	//--- if qty > 0
      }

      if($result === TRUE)
      {
        $this->orders_model->set_status($order_code, 0);
      }
    }

    echo $result === TRUE ? 'success' : ( $err_qty > 0 ? $error.' : '.$err_qty.' item(s)' : $error);
  }




  public function remove_detail($id)
  {
    $detail = $this->orders_model->get_detail($id);
    $item = $this->products_model->get($detail->product_code);
    $rs = $this->orders_model->remove_detail($id);
    echo $rs === TRUE ? 'success' : 'Can not delete please try again';
  }



  public function edit_order($code)
  {
    $this->load->model('address/address_model');
    $this->load->model('masters/bank_model');
    $this->load->model('orders/order_payment_model');
    $this->load->helper('bank');
    $ds = array();
    $rs = $this->orders_model->get($code);

    if( ! empty($rs))
    {
      $customer = $this->customers_model->get($rs->customer_code);
      $rs->channels_name = $this->channels_model->get_name($rs->channels_code);
      $rs->payment_name = $this->payment_methods_model->get_name($rs->payment_code);
      $rs->payment_role = $this->payment_methods_model->get_role($rs->payment_code);
      $rs->sale_name = (empty($customer) ? NULL : $customer->sale_name);
      $rs->total_amount = $this->orders_model->get_order_total_amount($rs->code);
      $rs->user = $this->user_model->get_name($rs->user);
      $rs->state_name = get_state_name($rs->state);
      $rs->has_payment = $this->order_payment_model->is_exists($code);


			$state = $this->order_state_model->get_order_state($code);
	    $ost = array();
	    if(!empty($state))
	    {
	      foreach($state as $st)
	      {
	        $ost[] = $st;
	      }
	    }

	    $details = $this->orders_model->get_order_details($code);
	    //$ship_to = $this->address_model->get_shipping_address($rs->customer_ref, $rs->customer_code);
			$ship_to = empty($rs->customer_ref) ? $this->address_model->get_ship_to_address($rs->customer_code) : $this->address_model->get_shipping_address($rs->customer_ref);
	    $banks = $this->bank_model->get_active_bank();
	    $ds['state'] = $ost;
	    $ds['order'] = $rs;
	    $ds['details'] = $details;
	    $ds['addr']  = $ship_to;
	    $ds['banks'] = $banks;
	    $ds['payments'] = $this->order_payment_model->get_payments($code);
	    $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
	    $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
	    $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
	    $this->load->view('orders/order_edit', $ds);

    }
		else
		{
			$this->page_error();
		}
  }



  public function update_order()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $this->load->model('inventory/invoice_model');
			$this->load->model('address/address_model');

			$customer_code = trim($this->input->post('customer_code'));
      $customer_name = trim($this->input->post('customer_name'));
			$customer_ref = trim($this->input->post('customer_ref'));
      $code = $this->input->post('order_code');
      $recal = $this->input->post('recal');
      $has_term = $this->payment_methods_model->has_term($this->input->post('payment_code'));
      $sale_code = $this->customers_model->get_sale_code($customer_code);
			$id_address = empty($customer_ref) ? $this->address_model->get_shipping_address_id($customer_code) : $this->address_model->get_shipping_address_id($customer_ref);
      $tags = get_null($this->input->post('tags'));

      //--- check over due
      $is_strict = is_true(getConfig('STRICT_OVER_DUE'));
      $overDue = $is_strict ? $this->invoice_model->is_over_due($customer_code) : FALSE;


      //--- ถ้ามียอดค้างชำระ และ เป็นออเดอร์แบบเครดิต
      //--- ไม่ให้เพิ่มออเดอร์
      if($overDue && $has_term)
      {
        $sc = FALSE;
        $message = 'มียอดค้างชำระเกินกำหนดไม่อนุญาติให้แก้ไขการชำระเงิน';
      }
      else
      {
        $ds = array(
          'reference' => get_null(trim($this->input->post('reference'))),
          'reference2' => get_null(trim($this->input->post('reference2'))),
          'customer_code' => $customer_code,
          'customer_name' => $customer_name,
          'customer_ref' => $customer_ref,
          'channels_code' => $this->input->post('channels_code'),
          'payment_code' => $this->input->post('payment_code'),
          'sale_code' => $sale_code,
          'type_code' => get_null($this->input->post('type_code')),
          'is_term' => $has_term,
					'address_id' => $id_address,
          'sender_id' => get_null($this->input->post('sender_id')),
          'date_add' => db_date($this->input->post('date_add')),
          'remark' => trim($this->input->post('remark')),
          'tags' => $tags,
					'qt_no' => $this->input->post('qt_no'),
          'status' => 0
        );

        $rs = $this->orders_model->update($code, $ds);

        if($rs === TRUE)
        {
          if($recal == 1)
          {
            $order = $this->orders_model->get($code);

            //---- Recal discount
            $details = $this->orders_model->get_order_details($code);
            if(!empty($details))
            {
              foreach($details as $detail)
              {
                $qty	= $detail->qty;

                //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
                $discount 	= $this->discount_model->get_item_recal_discount($detail->order_code, $detail->product_code, $detail->price, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);

                $arr = array(
                  "qty"		=> $qty,
                  "discount1"	=> $discount['discLabel1'],
                  "discount2" => $discount['discLabel2'],
                  "discount3" => $discount['discLabel3'],
                  "discount_amount" => $discount['amount'],
                  "total_amount"	=> ($detail->price * $qty) - $discount['amount'],
                  "id_rule"	=> $discount['id_rule']
                );

                $this->orders_model->update_detail($detail->id, $arr);
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $message = 'ปรับปรุงรายการไม่สำเร็จ';
        }
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function edit_detail($code)
  {
    $this->load->helper('product_tab');
    $ds = array();
    $rs = $this->orders_model->get($code);

    if($rs->state <= 3)
    {
      $customer = $this->customers_model->get($rs->customer_code);
      $rs->sale_name = (empty($customer) ? NULL : $customer->sale_name);
      $ds['order'] = $rs;

      $details = $this->orders_model->get_order_details($code);
      $ds['details'] = $details;
      $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('orders/order_edit_detail', $ds);
    }
    else
    {
      $ds['order'] = $rs;
      $this->load->view('orders/invalid_state', $ds);
    }

  }



  public function save($code)
  {
    $sc = TRUE;
    $order = $this->orders_model->get($code);
    //--- ถ้าออเดอร์เป็นแบบเครดิต
    if($order->is_term == 1)
    {
      //---- check credit balance
      $amount = $this->orders_model->get_order_total_amount($code);
			$amount += $order->shipping_fee;
			$amount += $order->service_fee;
			$amount -= $order->bDiscAmount;

      //--- credit balance from sap
      $credit_balance = $this->customers_model->get_credit_balance($order->customer_code);

      if(getConfig('CONTROL_CREDIT'))
      {
        if($amount > $credit_balance)
        {
          $diff = $amount - $credit_balance;
          $sc = FALSE;
          $message = 'เครดิตคงเหลือไม่พอ (ขาด : '.number($diff, 2).')';
        }
      }
    }

    if($sc === TRUE)
    {
      update_order_total_amount($code);
    }

    if($sc === TRUE)
    {
      $rs = $this->orders_model->set_status($code, 1);
      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'บันทึกออเดอร์ไม่สำเร็จ';
      }
			else
			{
				if(! empty($order->qt_no))
				{
					//--- load model
					$this->load->model('orders/quotation_model');
					//--- close quotation
					$ar = array(
						'is_closed' => 1
					);

					$this->quotation_model->update($order->qt_no, $ar);
				}
			}
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function get_product_order_tab($id = NULL)
  {
    $ds = array();
  	$id_tab = $id === NULL ? $this->input->post('id') : $id;

		$qs = $this->product_tab_model->get_style_in_tab($id_tab);

		if(!empty($qs))
		{
			foreach($qs as $rs)
			{
				$arr = array(
					'code' => $rs->code,
					'name' => $rs->name,
					'price' => number($rs->price, 2),
					'image' => get_cover_image($rs->code, 'default')
				);

				array_push($ds, $arr);
			}

			echo json_encode($ds);
		}
		else
		{
			echo 'no_product';
		}

  }



	public function get_order_item_tab($id = NULL)
  {
    $ds = array();
  	$id_tab = $id === NULL ? $this->input->post('id') : $id;

		$qs = $this->product_tab_model->get_item_in_tab($id_tab);

		if(!empty($qs))
		{
			foreach($qs as $rs)
			{
				$arr = array(
					'code' => $rs->code,
					'name' => $rs->name,
					'price' => number($rs->price, 2),
					'image' => get_product_image($rs->code, 'default')
				);

				array_push($ds, $arr);
			}

			echo json_encode($ds);
		}
		else
		{
			echo 'no_product';
		}

  }



  public function get_style_sell_stock($style_code)
  {
    $sell_stock = $this->stock_model->get_style_sell_stock($style_code);
    $reserv_stock = $this->orders_model->get_reserv_stock_by_style($style_code);

    $available = $sell_stock - $reserv_stock;

    return $available >= 0 ? $available : 0;
  }



  public function get_order_grid()
  {
		$use_grid = getConfig('USE_ORDER_GRID');
		$use_product_name = getConfig('USE_PRODUCT_NAME');

    //----- Attribute Grid By Clicking image
    $style_code = $this->input->get('style_code');
    $style = $this->product_style_model->get($style_code);
    $warehouse = get_null($this->input->get('warehouse_code'));
    $zone = get_null($this->input->get('zone_code'));
  	$sc = 'not exists';
    $view = $this->input->get('isView') == '0' ? FALSE : TRUE;

		if($use_grid)
		{
			$sc = $this->getOrderGrid($style_code, $view, $warehouse, $zone);
			$tableWidth	= $this->products_model->countAttribute($style_code) == 1 ? 600 : $this->getOrderTableWidth($style_code);
		}
		else
		{
			$sc = $this->getOrderTable($style_code, $view, $warehouse, $zone);
			$tableWidth	= 350; //$use_product_name == 1 ? 400 : 300;
		}


  	$sc .= ' | '.$tableWidth;
  	$sc .= ' | ' . $style_code.' : '.$style->name;
  	$sc .= ' | ' . $style_code;
    $sc .= ' | ' . get_cover_image($style_code, 'mini');
    $sc .= ' | ' . number($style->price, 2);
  	echo $sc;
  }


	public function get_order_item_grid()
	{
		$code = trim($this->input->get('itemCode'));
		$warehouse = get_null($this->input->get('warehouse_code'));
		$view = $this->input->get('isView') == '1' ? TRUE : FALSE;
		$item = $this->products_model->get($code);

		if(!empty($item))
		{
			$sc = $this->getOrderItemGrid($item, $view);
			$sc .= ' | ';
			$sc .= ' | '.$item->code.' : '.$item->name;
			$sc .= ' | '.$item->code;
			$sc .= ' | '.number($item->price, 2);

			echo $sc;
		}
		else
		{
			echo 'notfound';
		}

	}



  public function getOrderGrid($style_code, $view = FALSE)
	{
		$sc = '';
    $style = $this->product_style_model->get($style_code);
		$isVisual = $style->count_stock == 1 ? FALSE : TRUE;
		$attrs = $this->getAttribute($style->code);

		if( count($attrs) == 1  )
		{
			$sc .= $this->orderGridOneAttribute($style, $attrs[0], $isVisual, $view);
		}
		else if( count( $attrs ) == 2 )
		{
			$sc .= $this->orderGridTwoAttribute($style, $isVisual, $view);
		}
		return $sc;
	}




	public function getOrderItemGrid($item, $view = FALSE)
	{
		$sc  = "";
		if(!empty($item))
		{
			$auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
			$use_product_name = getConfig('USE_PRODUCT_NAME') == 1 ? TRUE : FALSE;
			$isVisual = $item->count_stock == 1 ? FALSE : TRUE;
			$active = $item->active == 0 ? 'Disactive' : ($item->can_sell == 0 ? 'N/S' : ($item->is_deleted == 1 ? 'Deleted' : TRUE));
			$qty = $isVisual === FALSE ? ($active === TRUE ? $this->showStock($this->get_sell_stock($item->code)) : 0) : FALSE; //--- สต็อกที่สั่งซื้อได้
			$disabled = ($isVisual === TRUE OR $auz === TRUE) && $active === TRUE ? '' : (($active !== TRUE OR $qty <= 0) ? 'disabled' : '');

			if($qty <= 0 && $active === TRUE)
			{
				$txt = $auz === TRUE ? '<span class="font-size-12 red">'.$qty.'</span>' : '<span class="font-size-12 red">Sold out</span>';
        $txt = $qty == 0 ? '<span class="font-size-12 red">Sold out</span>' : $txt;
			}
			else
      {
        $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
      }

			$available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $limit = $qty === FALSE ? 1000000 : ($auz === TRUE ? 1000000 : $qty);

      $code = $item->code;

      if( $view === FALSE )
			{
				$sc .= '<center>';
				$sc .= '<input type="number"
				class="form-control input-sm input-medium order-grid input-qty display-block text-center"
				name="qty[0]['.$item->code.']"
				id="'.$item->code.'"
				onkeyup="valid_qty($(this), '.$limit.')" '.$disabled.' />';
				$sc .= '</center>';
			}

      $sc .= 	'<center>';
      $sc .= '<span class="font-size-10">';
      $sc .= $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $sc .= '</span></center>';
		}

		return $sc;
	}



	public function getOrderTable($style_code, $view = FALSE, $warehouse = NULL, $zone = NULL)
	{
		$sc 		= '';

		$items	= $this->products_model->get_items_by_style($style_code);

		$sc 	 .= "<table class='table table-bordered'>";
		$i 		  = 0;
    $auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
		$use_product_name = getConfig('USE_PRODUCT_NAME'); // == 1 ? TRUE : FALSE;

    if(!empty($items))
    {
      foreach($items as $item )
      {
      //  $sc 	.= $i%2 == 0 ? '<tr>' : '';
  		  $sc .= "<tr>";
  			$isVisual = $item->count_stock == 1 ? FALSE : TRUE;
        $active	= $item->active == 0 ? 'Disactive' : ( $item->can_sell == 0 ? 'N/S' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
  			$qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
  			//$disabled  = $isVisual === TRUE  && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');
        $disabled  = ($isVisual === TRUE OR $auz === TRUE) && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');

        if( $qty < 1 && $active === TRUE )
        {
          $txt = $auz === TRUE ? '<span class="font-size-12 red">'.$qty.'</span>' : '<span class="font-size-12 red">Sold out</span>';
          $txt = $qty == 0 ? '<span class="font-size-12 red">Sold out</span>' : $txt;
        }
        else
        {
          $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
        }

        $available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
        $limit = $qty === FALSE ? 1000000 : ($auz === TRUE ? 1000000 : $qty);

        $code = $item->code;
  			$name = $use_product_name === 1 ? $item->name : ($use_product_name == 0 ? $item->code : $item->code.' : '.$item->name);

  			$sc 	.= '<td class="middle text-left" style="border-right:0px; white-space:pre-wrap;">';
        $sc   .= '<p class="margin-bottom-0">'.$item->name.'</p>';
        $sc   .= '<p class="margin-bottom-0 font-size-10">'.$item->code.'</p>';
  			//$sc 	.= '<strong>' .	$name. '</strong>';
  			$sc 	.= '</td>';

  			$sc 	.= '<td class="middle one-attribute" style="width:80px;">';

        if( $view === FALSE )
  			{
  			$sc 	.= '<input type="number" class="form-control input-sm order-grid input-qty display-block text-center" name="qty[0]['.$item->code.']" id="'.$item->code.'" onkeyup="valid_qty($(this), '.$limit.')" '.$disabled.' />';
  			}

        $sc 	.= 	'<center>';
        $sc   .= '<span class="font-size-10">';
        $sc   .= $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
        $sc   .= '</span></center>';
  			$sc 	.= '</td>';

  			//$i++;

  			//$sc 	.= $i%2 == 0 ? '</tr>' : '';
  			$sc .= "</tr>";

      }
    }
    else
    {
      $sc .= "<tr><td class='text-center'>ไม่พบรายการสินค้าในรุ่นนี้</td></tr>";
    }

		$sc	.= "</table>";

		return $sc;
	}


  public function getProductTable($style_code, $view = FALSE)
	{
		$items	= $this->products_model->get_items_by_style($style_code);
    $sc 		= '';
		$sc 	 .= "<table class='table table-bordered'>";
		$i 		  = 0;
		$use_product_name = getConfig('USE_PRODUCT_NAME');// == 1 ? TRUE : FALSE;

    foreach($items as $item )
    {
		  $sc .= "<tr>";
			$isVisual = $item->count_stock == 1 ? FALSE : TRUE;
      $active	= $item->active == 0 ? 'Disactive' : ( $item->can_sell == 0 ? 'N/S' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
			$qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้

      $disabled  = "";

      if( $qty <= 0 && $active === TRUE )
      {
        $txt = $qty == 0 ? '<span class="font-size-12 red">Sold out</span>' : '<span class="font-size-12 red">'.$qty.'</span>';
      }
      else
      {
        $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
      }

      $available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $limit = 1000000;

      $code = $item->code;
			$name = $use_product_name === 1 ? $item->name : ($use_product_name == 0 ? $item->code : $item->code.' : '.$item->name);

			$sc 	.= '<td class="middle text-center" style="border-right:0px; white-space:pre-wrap;">';
			$sc 	.= '<strong>' .	$name. '</strong>';
			$sc 	.= '</td>';

			$sc 	.= '<td class="middle one-attribute" style="width:80px;">';

			if( $view === FALSE )
			{
				$sc 	.= '<input
				type="number"
				min="1"
				max="'.$limit.'"
				class="form-control order-grid input-qty text-center"
				name="qty['.$item->color_code.']['.$item->code.']"
				id="'.$item->code.'"
				data-pdcode="'.$item->code.'"
				data-pdname="'.$item->name.'"
				data-price="'.$item->price.'"
				onkeyup="valid_qty($(this), '.$limit.')" '.$disabled.' />';
			}

      $sc 	.= 	'<center>';
      $sc   .= '<span class="font-size-10">';
      $sc   .= $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $sc   .= '</span></center>';
			$sc 	.= '</td>';
			$sc .= "</tr>";

    }

		$sc	.= "</table>";

		return $sc;
	}



  public function get_item_grid()
  {
    $sc = "";
    $item_code = $this->input->get('itemCode');
    $warehouse_code = get_null($this->input->get('warehouse_code'));
    $filter = getConfig('MAX_SHOW_STOCK');
    $item = $this->products_model->get($item_code);
    if(!empty($item))
    {
      $qty = $item->count_stock == 1 ? ($item->active == 1 ? $this->showStock($this->get_sell_stock($item->code, $warehouse_code)) : 0) : 1000000;
      $sc = "success | {$item_code} | {$qty}";
    }
    else
    {
      $sc = "Error | ไม่พบสินค้า | {$item_code}";
    }

    echo $sc;
  }


	public function get_product_item_grid()
	{
		$code = trim($this->input->get('itemCode'));
		$warehouse = get_null($this->input->get('warehouse_code'));
		$view = $this->input->get('isView') == '1' ? TRUE : FALSE;
		$item = $this->products_model->get($code);

		if(!empty($item))
		{
			$sc = $this->getProductItemGrid($item, $view);
			$sc .= ' | 150';
			$sc .= ' | '.$item->code.' : '.$item->name;
			$sc .= ' | '.$item->code;
			$sc .= ' | '.number($item->price, 2);

			echo $sc;
		}
		else
		{
			echo 'notfound';
		}

	}


	public function getProductItemGrid($item, $view = FALSE)
	{
		$sc  = "";

		if(!empty($item))
		{
			$auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
			$use_product_name = getConfig('USE_PRODUCT_NAME');// == 1 ? TRUE : FALSE;
			$isVisual = $item->count_stock == 1 ? FALSE : TRUE;
			$active = $item->active == 0 ? 'Disactive' : ($item->can_sell == 0 ? 'N/S' : ($item->is_deleted == 1 ? 'Deleted' : TRUE));
			$qty = $isVisual === FALSE ? ($active === TRUE ? $this->showStock($this->get_sell_stock($item->code)) : 0) : FALSE; //--- สต็อกที่สั่งซื้อได้
			$disabled = "";

			if($qty <= 0 && $active === TRUE)
			{
				$txt = $auz === TRUE ? '<span class="font-size-12 red">'.$qty.'</span>' : '<span class="font-size-12 red">Sold out</span>';
        $txt = $qty == 0 ? '<span class="font-size-12 red">Sold out</span>' : $txt;
			}
			else
      {
        $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
      }

			$available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $limit = $qty === FALSE ? 1000000 : ($auz === TRUE ? 1000000 : $qty);

      $code = $item->code;

      if( $view === FALSE )
			{
				$sc .= '<center>';
				$sc 	.= '<input
				type="number"
				min="1"
				max="'.$limit.'"
				class="form-control input-sm input-medium order-grid input-qty text-center"
				name="qty['.$item->color_code.']['.$item->code.']"
				id="'.$item->code.'"
				data-pdcode="'.$item->code.'"
				data-pdname="'.$item->name.'"
				data-price="'.$item->price.'"
				onkeyup="valid_qty($(this), '.$limit.')" '.$disabled.' />';
				$sc .= '</center>';
			}

      $sc .= 	'<center>';
      $sc .= '<span class="font-size-10">';
      $sc .= $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $sc .= '</span></center>';
		}

		return $sc;
	}



  //--- Po
  public function get_product_grid()
  {
    $use_grid = getConfig('USE_ORDER_GRID');
    $style_code = $this->input->get('style_code');
    $sc = "รหัสสินค้าไม่ถูกต้อง";

    $view = FALSE;

    if($this->products_model->is_exists_style($style_code))
    {
      if($use_grid)
      {
        $sc = $this->getProductGrid($style_code);
        $tableWidth	= $this->products_model->countAttribute($style_code) == 1 ? 600 : $this->getOrderTableWidth($style_code);
      }
      else
      {
        $sc = $this->getProductTable($style_code);
        $tableWidth	= 350;
      }

    	$sc .= ' | '.$tableWidth;
    	$sc .= ' | ' . $style_code;
    	$sc .= ' | ' . $style_code;
    }

  	echo $sc;
  }


  //---- PO
  public function getProductGrid($style_code)
	{
		$sc = '';
    $style = $this->product_style_model->get($style_code);
		$isVisual = $style->count_stock == 1 ? FALSE : TRUE;
    $showStock = TRUE;
    $view = FALSE;
		$attrs = $this->getAttribute($style->code);

		if( count($attrs) == 1  )
		{
			$sc .= $this->orderGridOneAttribute($style, $attrs[0], $isVisual, $view, $showStock);
		}
		else if( count( $attrs ) == 2 )
		{
			$sc .= $this->orderGridTwoAttribute($style, $isVisual, $view, $showStock);
		}
		return $sc;
	}


  public function showStock($qty)
	{
		return $this->filter == 0 ? $qty : ($this->filter < $qty ? $this->filter : $qty);
	}



  public function orderGridOneAttribute($style, $attr, $isVisual, $view, $is_po = FALSE)
	{
		$sc 		= '';
		$data 	= $attr == 'color' ? $this->getAllColors($style->code) : $this->getAllSizes($style->code);
		$items	= $this->products_model->get_style_items($style->code);
		$sc 	 .= "<table class='table table-bordered'>";
		$i 		  = 0;
    $auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;

    foreach($items as $item )
    {
      $id_attr	= $item->size_code === NULL OR $item->size_code === '' ? $item->color_code : $item->size_code;
      $sc 	.= $i%2 == 0 ? '<tr>' : '';
      $active	= $item->active == 0 ? 'Disactive' : ( $item->can_sell == 0 ? 'N/S' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
			$qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
      $disabled  = ($isVisual === TRUE OR $auz === TRUE OR $is_po === TRUE) && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');

      if( $qty < 1 && $active === TRUE )
      {
        $txt = $auz === TRUE ? '<span class="font-size-12 red">'.$qty.'</span>' : '<span class="font-size-12 red">Sold out</span>';
        $txt = $qty == 0 ? '<span class="font-size-12 red">Sold out</span>' : $txt;
      }
      else
      {
        $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
      }

      $available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $limit = $qty === FALSE ? 1000000 : (($auz === TRUE OR $is_po === TRUE) ? 1000000 : $qty);
      $code = $attr == 'color' ? $item->color_code : $item->size_code;

			$sc 	.= '<td class="middle text-center width-25" style="border-right:0px; white-space:pre-wrap;">';
			$sc 	.= '<strong>' .	$code. '</strong>';
			$sc 	.= '</td>';

			$sc 	.= '<td class="middle width-25" class="one-attribute">';

			if( $view === FALSE )
			{
				$sc 	.= '<input
				type="number"
				min="1"
				max="'.$limit.'"
				class="form-control order-grid input-qty text-center"
				name="qty['.$item->color_code.']['.$item->code.']"
				id="'.$item->code.'"
				data-pdcode="'.$item->code.'"
				data-pdname="'.$item->name.'"
				data-price="'.$item->price.'"
				onkeyup="valid_qty($(this), '.$limit.')" '.$disabled.' />';
			}


      $sc 	.= 	'<center>';
      $sc   .= '<span class="font-size-10">';
      $sc   .= $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $sc   .= '</span></center>';
			$sc 	.= '</td>';

			$i++;

			$sc 	.= $i%2 == 0 ? '</tr>' : '';

    }

		$sc	.= "</table>";

		return $sc;
	}





  public function orderGridTwoAttribute($style, $isVisual = FALSE, $view = FALSE, $is_po = FALSE)
	{
    $auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
		$colors	= $this->getAllColors($style->code);
		$sizes 	= $this->getAllSizes($style->code);
		$sc 		= '';
		$sc 		.= '<table class="table table-bordered">';
		$sc 		.= $this->gridHeader($colors);

		foreach( $sizes as $size_code => $size )
		{
			$sc 	.= '<tr style="font-size:14px;">';
			$sc 	.= '<td class="text-center middle" style="width:80px; white-space:pre-wrap;"><strong>'.$size_code.'</strong></td>';

			foreach( $colors as $color_code => $color )
			{
        $item = $this->products_model->get_item_by_color_and_size($style->code, $color_code, $size_code);

				if( !empty($item) )
				{
					$isVisual = $item->count_stock == 1 ? FALSE : TRUE;
					$active	= $item->active == 0 ? 'ปิด' : ( $item->can_sell == 0 ? 'ไม่มี' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
					//$stock	= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->stock_model->get_stock($item->code) )  : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
					$qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
					$disabled  = ($isVisual === TRUE OR $auz === TRUE OR $is_po === TRUE) && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');
					if( $qty < 1 && $active === TRUE )
					{
						$txt = $auz === TRUE ? '<span class="font-size-12 red">'.$qty.'</span>' : '<span class="font-size-12 red">หมด</span>';
            $txt = $qty == 0 ? '<span class="font-size-12 red">หมด</span>' : $txt;
					}
					else
					{
						$txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
					}

					$available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
					$limit = $qty === FALSE ? 1000000 : (($auz === TRUE OR $is_po === TRUE) ? 1000000 : $qty);


					$sc 	.= '<td class="order-grid">';

					if( $view === FALSE )
					{
						$sc 	.= '<input
						type="number"
						min="1"
						max="'.$limit.'"
						class="form-control order-grid input-qty text-center"
						name="qty['.$item->color_code.']['.$item->code.']"
						id="'.$item->code.'"
						data-pdcode="'.$item->code.'"
						data-pdname="'.$item->name.'"
						data-price="'.$item->price.'"
						onkeyup="valid_qty($(this), '.$limit.')" '.$disabled.' />';
					}

					$sc 	.= ($isVisual === FALSE OR $is_po === TRUE) ? '<center style="font-weight:bold; color:#0F00FF"><strong>'.$available.'</strong></center>' : '';
					$sc 	.= '</td>';
				}
				else
				{
					$sc .= '<td class="order-grid">N/A</td>';
				}
			} //--- End foreach $colors

			$sc .= '</tr>';
		} //--- end foreach $sizes
	$sc .= '</table>';
	return $sc;
	}







  public function getAttribute($style_code)
  {
    $sc = array();
    $color = $this->products_model->count_color($style_code);
    $size  = $this->products_model->count_size($style_code);
    if( $color > 0 )
    {
      $sc[] = "color";
    }

    if( $size > 0 )
    {
      $sc[] = "size";
    }
    return $sc;
  }





  public function gridHeader(array $colors)
  {
    $sc = '<tr class="font-size-12"><td>&nbsp;</td>';
    foreach( $colors as $code => $name )
    {
      $sc .= '<td class="text-center middle" style="white-space:pre-wrap;"><strong>'.$code.'</strong></td>';
    }
    $sc .= '</tr>';
    return $sc;
  }





  public function getAllColors($style_code)
	{
		$sc = array();
    $colors = $this->products_model->get_all_colors($style_code);
    if($colors !== FALSE)
    {
      foreach($colors as $color)
      {
        $sc[$color->code] = $color->name;
      }
    }

    return $sc;
	}




  public function getAllSizes($style_code)
	{
		$sc = array();
		$sizes = $this->products_model->get_all_sizes($style_code);
		if( $sizes !== FALSE )
		{
      foreach($sizes as $size)
      {
        $sc[$size->code] = $size->name;
      }
		}
		return $sc;
	}



  public function getOrderTableWidth($style_code)
  {
    $sc = 800; //--- ชั้นต่ำ
    $tdWidth = 70;  //----- แต่ละช่อง
    $padding = 100; //----- สำหรับช่องแสดงไซส์
    $color = $this->products_model->count_color($style_code);
    if($color > 0)
    {
      $sc = $color * $tdWidth + $padding;
    }

    return $sc;
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
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



  public function print_order_sheet($code, $barcode = '')
  {
    $this->load->model('masters/products_model');
    $this->load->model('masters/customer_type_model');
		$this->load->model('address/customer_address_model');
    $this->load->library('printer');

    $order = $this->orders_model->get($code);
		$customer = $this->customers_model->get($order->customer_code);
		$customer_address = $this->customer_address_model->get_customer_bill_to_address($order->customer_code);
		$order->emp_name = $this->user_model->get_employee_name($order->user);
    $order->csr = $this->customer_type_model->get_name($customer->type_code);
    $details = $this->orders_model->get_order_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

		$ds['order'] = $order;
    $ds['details'] = $details;
		$ds['customer'] = $customer;
		$ds['address'] = $customer_address;
    $ds['is_barcode'] = $barcode != '' ? TRUE : FALSE;
    $this->load->view('print/print_order_sheet', $ds);
  }

  public function get_sell_stock($item_code, $warehouse_code = NULL)
  {
    $auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse_code);
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code, $warehouse_code);
    $buffer = $this->buffer_model->get_sum_product($item_code, $warehouse_code);
    $cancle = $this->cancle_model->get_sum_product($item_code, $warehouse_code);
    $availableStock = ($sell_stock + $buffer + $cancle) - $reserv_stock;
		return $auz === TRUE ? $availableStock : ($availableStock < 0 ? 0 : $availableStock);
  }




  public function get_detail_table($order_code)
  {
    $sc = "no data found";
    $order = $this->orders_model->get($order_code);
    $details = $this->orders_model->get_order_details($order_code);
    if($details != FALSE )
    {
      $no = 1;
      $total_qty = 0;
      $total_discount = 0;
      $total_amount = 0;
      $total_order = 0;
      $ds = array();
      foreach($details as $rs)
      {
        $arr = array(
          "id"		=> $rs->id,
          "no"	=> $no,
          "imageLink"	=> get_product_image($rs->product_code, 'mini'),
          "productCode"	=> $rs->product_code,
          "productName"	=> $rs->product_name,
          "cost"				=> $rs->cost,
          "price"	=> $rs->price,
          "priceLabel" => number($rs->price, 2),
          "qty"	=> $rs->qty,
          "qtyLabel" => number($rs->qty, 2),
          "discount"	=> discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
          "amount"	=> $rs->total_amount,
          "amountLabel" => number($rs->total_amount, 2)
        );
        array_push($ds, $arr);
        $total_qty += $rs->qty;
        $total_discount += $rs->discount_amount;
        $total_amount += $rs->total_amount;
        $total_order += $rs->qty * $rs->price;
        $no++;
      }

      $netAmount = ( $total_amount - $order->bDiscAmount ) + $order->shipping_fee + $order->service_fee;

      $arr = array(
        "bDiscAmount" => $order->bDiscAmount,
        "bDiscAmountLabel" => number($order->bDiscAmount, 2),
        "total_qty" => number($total_qty, 2),
        "order_amount" => number($total_order, 2),
        "total_discount" => number($total_discount, 2),
        "shipping_fee"	=> number($order->shipping_fee,2),
        "service_fee"	=> number($order->service_fee, 2),
        "total_amount" => number($total_amount, 2),
        "net_amount"	=> number($netAmount,2)
      );
      array_push($ds, $arr);
      $sc = json_encode($ds);
    }
    echo $sc;

  }


  public function get_pay_amount()
  {
    $pay_amount = 0;

    if($this->input->get('order_code'))
    {
      $code = $this->input->get('order_code');

      //--- ยอดรวมหลังหักส่วนลด ตาม item
      // $amount = $this->orders_model->get_order_total_amount($code);
      // //--- ส่วนลดท้ายบิล
      // $bDisc = $this->orders_model->get_bill_discount($code);
      // $pay_amount = $amount - $bDisc;

      $pay_amount = $this->orders_model->get_order_balance($code);
    }

    echo $pay_amount;
  }



  public function get_account_detail($id)
  {
    $sc = 'fail';
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');
    $rs = $this->bank_model->get_account_detail($id);
    if($rs !== FALSE)
    {
      $ds = bankLogoUrl($rs->bank_code).' | '.$rs->bank_name.' สาขา '.$rs->branch.'<br/>เลขที่บัญชี '.$rs->acc_no.'<br/> ชื่อบัญชี '.$rs->acc_name;
      $sc = $ds;
    }

    echo $sc;
  }



  public function confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $this->load->helper('bank');
      $this->load->model('orders/order_payment_model');

      $file = isset( $_FILES['image'] ) ? $_FILES['image'] : FALSE;
      $order_code = $this->input->post('order_code');
      $date = $this->input->post('payDate');
      $h = $this->input->post('payHour');
      $m = $this->input->post('payMin');
      $dhm = $date.' '.$h.':'.$m.':00';
      $pay_date = date('Y-m-d H:i:s', strtotime($dhm));
      $img_name = $order_code.'-'.date('Ymdhis');
      $arr = array(
        'order_code' => $order_code,
        'order_amount' => $this->input->post('orderAmount'),
        'pay_amount' => $this->input->post('payAmount'),
        'pay_date' => $pay_date,
        'id_account' => $this->input->post('id_account'),
        'acc_no' => $this->input->post('acc_no'),
        'user' => get_cookie('uname'),
        'is_deposit' => $this->input->post('is_deposit'),
        'img' => $img_name
      );

      //--- บันทึกรายการ
      if($this->order_payment_model->add($arr))
      {
				$order = $this->orders_model->get($order_code);

				if($order->state < 2)
				{
					if($this->orders_model->change_state($order_code, 2))
					{
						$arr = array(
		          'order_code' => $order_code,
		          'state' => 2,
		          'update_user' => get_cookie('uname')
		        );
		        $this->order_state_model->add_state($arr);
					}
				}

      }
      else
      {
        $sc = FALSE;
        $message = 'บันทึกรายการไม่สำเร็จ';
      }

      if($file !== FALSE)
      {
        $rs = $this->do_upload($file, $img_name);
        if($rs !== TRUE)
        {
          $sc = FALSE;
          $message = $sc;
        }
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function do_upload($file, $img_name)
	{
    $this->load->library('upload');
    $sc = TRUE;

		$image_path = $this->config->item('image_path').'payments/';
    $image 	= new upload($file);
    if( $image->uploaded )
    {
      $image->file_new_name_body = $img_name; 		//--- เปลี่ยนชือ่ไฟล์ตาม order_code
      $image->image_resize			 = TRUE;		//--- อนุญาติให้ปรับขนาด
      $image->image_retio_fill	 = TRUE;		//--- เติกสีให้เต็มขนาดหากรูปภาพไม่ได้สัดส่วน
      $image->file_overwrite		 = TRUE;		//--- เขียนทับไฟล์เดิมได้เลย
      $image->auto_create_dir		 = TRUE;		//--- สร้างโฟลเดอร์อัตโนมัติ กรณีที่ไม่มีโฟลเดอร์
      $image->image_x					   = 500;		//--- ปรับขนาดแนวนอน
      //$image->image_y					   = 800;		//--- ปรับขนาดแนวตั้ง
      $image->image_ratio_y      = TRUE;  //--- ให้คงสัดส่วนเดิมไว้
      $image->image_background_color	= "#FFFFFF";		//---  เติมสีให้ตามี่กำหนดหากรูปภาพไม่ได้สัดส่วน
      $image->image_convert			= 'jpg';		//--- แปลงไฟล์

      $image->process($image_path);						//--- ดำเนินการตามที่ได้ตั้งค่าไว้ข้างบน

      if( ! $image->processed )	//--- ถ้าไม่สำเร็จ
      {
        $sc 	= $image->error;
      }
    } //--- end if

    $image->clean();	//--- เคลียร์รูปภาพออกจากหน่วยความจำ

		return $sc;
	}




  public function view_payment_detail($id)
  {
    $this->load->model('orders/order_payment_model');
    $this->load->model('masters/bank_model');
    $sc = TRUE;
    $code = $this->input->post('order_code');
    $rs = $this->order_payment_model->get($id);

    if(!empty($rs))
    {
      $bank = $this->bank_model->get_account_detail($rs->id_account);
      $img  = payment_image_url($rs->img); //--- order_helper
      $ds   = array(
        'order_code' => $code,
        'orderAmount' => number($rs->order_amount, 2),
        'payAmount' => number($rs->pay_amount, 2),
        'payDate' => thai_date($rs->pay_date, TRUE, '/'),
        'bankName' => $bank->bank_name,
        'branch' => $bank->branch,
        'accNo' => $bank->acc_no,
        'accName' => $bank->acc_name,
        'date_add' => thai_date($rs->date_upd, TRUE, '/'),
        'imageUrl' => $img === FALSE ? '' : $img,
        'valid' => "no"
      );
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'fail';
  }


  public function update_shipping_code()
  {
    $order_code = $this->input->post('order_code');
    $ship_code  = $this->input->post('shipping_code');
    if($order_code && $ship_code)
    {
      $rs = $this->orders_model->update_shipping_code($order_code, $ship_code);
      echo $rs === TRUE ? 'success' : 'fail';
    }
  }


  public function save_address()
  {
    $sc = TRUE;
    if($this->input->post('customer_ref'))
    {
      $this->load->model('address/address_model');
      $id = $this->input->post('id_address');
      $arr = array(
        'code' => trim($this->input->post('customer_ref')),
				'customer_code' => get_null($this->input->post('customer_code')),
        'name' => trim($this->input->post('name')),
        'address' => trim($this->input->post('address')),
        'sub_district' => trim($this->input->post('sub_district')),
        'district' => trim($this->input->post('district')),
        'province' => trim($this->input->post('province')),
        'postcode' => trim($this->input->post('postcode')),
        'phone' => trim($this->input->post('phone')),
        'email' => trim($this->input->post('email')),
        'alias' => trim($this->input->post('alias'))
      );

      if(!empty($id))
      {
        $rs = $this->address_model->update_shipping_address($id, $arr);
      }
      else
      {
        $rs = $this->address_model->add_shipping_address($arr);
      }

      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'เพิ่มที่อยู่ไม่สำเร็จ';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไมพบชื่อลูกค้าออนไลน์';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function get_address_table()
  {
    $sc = TRUE;
    $ds = array();
    $code = $this->input->post('customer_ref');
    $order_code = $this->input->post('order_code');

    if($order_code || $code)
    {
			$order = empty($order_code) ? NULL : $this->orders_model->get($order_code);

      if(! empty($code) OR ! empty($order))
      {
        $this->load->model('address/address_model');

        $adrs = empty($code) ? $this->address_model->get_ship_to_address($order->customer_code) : $this->address_model->get_shipping_address($code);

        if(!empty($adrs))
        {
          foreach($adrs as $rs)
          {
            $arr = array(
              'id' => $rs->id,
							'code' => $rs->code,
              'order_code' => $order_code,
              'name' => $rs->name,
              'address' => $rs->address.' '.$rs->sub_district.' '.$rs->district.' '.$rs->province.' '.$rs->postcode,
              'phone' => $rs->phone,
              'email' => $rs->email,
              'alias' => $rs->alias,
              'default' => empty($order_code) ? ($rs->is_default == 1? 1 : 0) : (($rs->id == $order->address_id) ? 1 : '')
            );

            $ds[] = $arr;
          }
        }
        else
        {
          $sc = FALSE;
        }
      }
      else
      {
        $sc = FALSE;
      }
    }

    echo $sc === TRUE ? json_encode($ds) : 'noaddress';
  }



  public function set_order_address()
  {
    $id = $this->input->post('id_address');
    $code = $this->input->post('order_code');
    //--- set new default
    $rs = $this->orders_model->set_address_id($code, $id);
    echo $rs === TRUE ? 'success' :'fail';
  }



  public function set_default_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $code = $this->input->post('customer_ref');
    //--- drop current
    $this->address_model->unset_default_shipping_address($code);

    //--- set new default
    $rs = $this->address_model->set_default_shipping_address($id);
    echo $rs === TRUE ? 'success' :'fail';
  }


  public function get_shipping_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $rs = $this->address_model->get_shipping_detail($id);
    if(!empty($rs))
    {
      $arr = array(
        'id' => $rs->id,
        'code' => $rs->code,
        'name' => $rs->name,
        'address' => $rs->address,
        'sub_district' => $rs->sub_district,
        'district' => $rs->district,
        'province' => $rs->province,
        'postcode' => $rs->postcode,
        'phone' => $rs->phone,
        'email' => $rs->email,
        'alias' => $rs->alias,
        'is_default' => $rs->is_default
      );

      echo json_encode($rs);
    }
    else
    {
      echo 'nodata';
    }
  }



  public function delete_shipping_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $rs = $this->address_model->delete_shipping_address($id);
    echo $rs === TRUE ? 'success' : 'fail';
  }



  public function set_never_expire()
  {
    $code = $this->input->post('order_code');
    $option = $this->input->post('option');
    $rs = $this->orders_model->set_never_expire($code, $option);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }


  public function un_expired()
  {
    $code = $this->input->post('order_code');
    $rs = $this->orders_model->un_expired($code);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }


  public function do_approve($code)
  {
    $sc = TRUE;
    $this->load->model('approve_logs_model');

    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      if($order->state == 1)
      {
        $user = $this->_user->uname;

        $rs = $this->orders_model->update_approver($code, $user);

        if(! $rs)
        {
          $sc = FALSE;
          $this->error = "อนุมัติไม่สำเร็จ";
        }
        else
        {
          $this->approve_logs_model->add($code, 1, $user);
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
      $this->error = "ไม่พบเลขที่เอกสาร";
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function un_approve($code)
  {
    $sc = TRUE;
    $this->load->model('approve_logs_model');
    $order = $this->orders_model->get($code);
    if(!empty($order))
    {
      if($order->state == 1 )
      {
        $user = $this->_user->uname;
        $rs = $this->orders_model->un_approver($code, $user);
        if(! $rs)
        {
          $sc = FALSE;
          $this->error = "อนุมัติไม่สำเร็จ";
        }
        else
        {
          $this->approve_logs_model->add($code, 0, $user);
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
      $this->error = "ไม่พบเลขที่เอกสาร";
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function order_state_change()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $code = $this->input->post('order_code');
      $state = $this->input->post('state');
      $order = $this->orders_model->get($code);
      $details = $this->orders_model->get_order_details($code);

      if(!empty($order))
      {
        //--- ถ้าเป็นเบิกแปรสภาพ จะมีการผูกสินค้าไว้
        if($order->role == 'T')
        {
          $this->load->model('inventory/transform_model');
          //--- หากมีการรับสินค้าที่ผูกไว้แล้วจะไม่อนุญาติให้เปลี่ยนสถานะใดๆ
          $is_received = $this->transform_model->is_received($code);

          if($is_received === TRUE)
          {
            $sc = FALSE;
            $this->error = 'ใบเบิกมีการรับสินค้าแล้วไม่อนุญาติให้ย้อนสถานะ';
          }
        }

        //--- ถ้าเป็นยืมสินค้า
        if($order->role == 'L')
        {
          $this->load->model('inventory/lend_model');
          //--- หากมีการรับสินค้าที่ผูกไว้แล้วจะไม่อนุญาติให้เปลี่ยนสถานะใดๆ
          $is_received = $this->lend_model->is_received($code);

          if($is_received === TRUE)
          {
            $sc = FALSE;
            $this->error = 'ใบเบิกมีการรับคืนสินค้าแล้วไม่อนุญาติให้ย้อนสถานะ';
          }
        }

        if($order->role == 'P')
        {
          $this->load->model('masters/sponsor_budget_model');
          $this->load->model('inventory/invoice_model');
          $sold_amount = $this->invoice_model->get_total_sold_amount($order->code);
        }

        //--- เปิดไปกำกับไปแล้ว
        if(! empty($order->invoice_code))
        {
          $sc = FALSE;
          $this->error = "ออเดอร์ถูกเปิดใบกำกับภาษีแล้ว กรุณายกเลิกใบกำกับก่อนย้อนสถานะ";
        }


        if($sc === TRUE)
        {
          $this->db->trans_begin();

          //--- ถ้าเปิดบิลแล้ว
          if($sc === TRUE && $order->state == 8)
          {
            $this->load->model('account/order_credit_model');

            if($state < 8)
            {

              if( ! $this->roll_back_action($order))
							{
								$sc = FALSE;
							}
							else
							{
                if($order->role == 'S')
                {
                  //--- ลบรายการตั้งหนี้ออก
                  $this->order_credit_model->delete($code);
                }

                if($order->role == 'P')
                {
                  $this->sponsor_budget_model->rollback_used($order->budget_id, $sold_amount);
                }
							}
            }

            if($state == 9)
            {
							if( ! $this->roll_back_action($order))
							{
								$sc = FALSE;
							}
							else
							{
								//--- ยกเลิกออเดอร์
								if(! $this->cancle_order($code, $order->role))
								{
									$sc = FALSE;
								}
								else
								{
                  if($order->role == 'S')
                  {
                    //--- ลบรายการตั้งหนี้ออก
                    $this->order_credit_model->delete($code);
                  }

                  if($order->role == 'P')
                  {
                    $this->sponsor_budget_model->rollback_used($order->budget_id, $sold_amount);
                  }
								}
							}
            }
          }

          else if($sc === TRUE && $order->state != 8)
          {
            if($state == 9)
            {
							if( ! $this->cancle_order($code, $order->role))
							{
								$sc = FALSE;
							}
            }
          }

          if($sc === TRUE)
          {
            $rs = $this->orders_model->change_state($code, $state);

            if($rs)
            {
              $arr = array(
                'order_code' => $code,
                'state' => $state,
                'update_user' => get_cookie('uname')
              );

              $this->order_state_model->add_state($arr);
            }
          }

          $this->rollback_unvalid_details($code);

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
    }
    else
    {
      $sc = FALSE;
			$this->error = 'ไม่พบข้อมูลออเดอร์';
    }

		echo $sc === TRUE ? 'success' : $this->error;
  }


  public function rollback_unvalid_details($code)
  {
    $this->load->model('inventory/prepare_model');

    $details = $this->orders_model->get_order_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $prepared = $this->prepare_model->get_prepared($code, $rs->product_code);
        if($prepared < $rs->qty)
        {
          $this->orders_model->unvalid_detail($rs->id);
        }
      }
    }
  }

  public function roll_back_action($order)
  {
		$sc = TRUE;

    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('inventory/lend_model');
    $this->load->model('stock/stock_model');

		$use_prepare = $order->picked == 1 ? TRUE : FALSE;

		$default_zone = getConfig('DEFAULT_ZONE');

		if(! $use_prepare && ($default_zone == "" OR $default_zone == NULL))
		{
			$sc = FALSE;
			$this->error = "ไม่สามารถปรับสต็อกคืนได้ เนื่องจากไม่ได้ตั้งค่าโซนขายเริ่มต้นไว้";
			return FALSE;
		}

		//---- set is_complete = 0
    if( ! $this->orders_model->un_complete($order->code))
		{
			$sc = FALSE;
			$this->error = "Set order complete status failed";
			return FALSE;
		}


		if($use_prepare)
		{
			//---- move cancle product back to  buffer
			if($sc === TRUE && ! $this->cancle_model->restore_buffer($order->code))
			{
				$sc = FALSE;
				$this->error = "Move cancle to buffer failed";
				return FALSE;
			}
		}


    //--- remove movement
    if($sc === TRUE && ! $this->movement_model->drop_movement($order->code))
		{
			$sc = FALSE;
			$this->error = "Drop Movement failed";
			return FALSE;
		}

		if($sc === TRUE)
		{
			//--- restore sold product back to buffer
			$sold = $this->invoice_model->get_details($order->code);

			if(!empty($sold))
			{
				foreach($sold as $rs)
				{
					if($sc === FALSE)
					{
						break;
					}

					if($rs->is_count == 1)
					{
						if($use_prepare)
						{
							//---- restore_buffer
							if($this->buffer_model->is_exists($rs->reference, $rs->product_code, $rs->zone_code) === TRUE)
							{
								if( ! $this->buffer_model->update($rs->reference, $rs->product_code, $rs->zone_code, $rs->qty))
								{
									$sc = FALSE;
									$this->error = "Update buffer failed";
								}
							}
							else
							{
								$ds = array(
									'order_code' => $rs->reference,
									'product_code' => $rs->product_code,
									'warehouse_code' => $rs->warehouse_code,
									'zone_code' => $rs->zone_code,
									'qty' => $rs->qty,
									'user' => $rs->user
								);

								if( ! $this->buffer_model->add($ds))
								{
									$sc = FALSE;
									$this->error = "Insert buffer failed";
								}
							}
						}

						if($order->role === 'N' OR $order->role === 'L')
						{
							//--- remove stock from zone
							if( !	$this->stock_model->update_stock_zone($order->zone_code, $rs->product_code, (-1) * $rs->qty))
							{
								$sc = FALSE;
								$this->error = "ลบสต็อกปลายทางไม่สำเร็จ";
							}
						}

						if(! $use_prepare)
						{
							//--- คืนสต็อกกลับเข้าโซน
							if( ! $this->stock_model->update_stock_zone($default_zone, $rs->product_code, $rs->qty))
							{
								$sc = FALSE;
								$this->error = "คืนสต็อกกลับเข้าโซน {$default} ไม่สำเร็จ";
							}
						}
					}

					if($sc === TRUE && ! $this->invoice_model->drop_sold($rs->id))
					{
						$sc = FALSE;
						$this->error = "ลบรายการขายไม่สำเร็จ";
					}

					//------ หากเป็นออเดอร์เบิกแปรสภาพ
					if($order->role == 'T')
					{
						$this->transform_model->reset_sold_qty($order->code);
					}

					//-- หากเป็นออเดอร์ยืม
					if($order->role == 'L')
					{
						$this->lend_model->drop_backlogs_list($order->code);
					}
				} //--- end foreach
			} //---- end sold
		}

		return $sc;
  }


  public function cancle_order($code, $role)
  {
		$sc = TRUE;

    $this->load->model('inventory/prepare_model');
    $this->load->model('inventory/qc_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('orders/order_payment_model');

    $order = $this->orders_model->get($code);

    //---- เมื่อมีการยกเลิกออเดอร์
    //--- 1. เคลียร์ buffer เข้า cancle
    if($sc === TRUE && ! $this->clear_buffer($code))
		{
			$sc = FALSE;
			$this->error = "Drop buffer failed (function : cancle_order)";
		}

    //--- 2. ลบประวัติการจัดสินค้า
    if($sc === TRUE && !$this->prepare_model->clear_prepare($code))
		{
			$sc = FALSE;
			$this->error = "Drop prepare failed (function : cancle_order)";
		}

    //--- 3. ลบประวัติการตรวจสินค้า
		if($sc === TRUE && ! $this->qc_model->clear_qc($code))
		{
			$sc = FALSE;
			$this->error = "Drop QC failed (function : cancle_order)";
		}

    //--- 4. mark orderdetail as cancle
    if($sc === TRUE && ! $this->orders_model->cancle_details($code))
		{
			$sc = FALSE;
			$this->error = "ยกเลิกรายการสินค้าไม่สำเร็จ (function : cancle_order)";
		}

    //--- 5. ยกเลิกออเดอร์
    if($sc === TRUE && ! $this->orders_model->set_status($code, 2))
		{
			$sc = FALSE;
			$this->error = "ยกเลิกออเดอร์ไม่สำเร็จ";
		}

		if($sc === TRUE)
		{
			if($role == 'S')
			{

				if($order->is_term == 1)
				{
					//--- clear order_credit
					$this->clear_credit_payment($order);
				}
				else
				{
					//---- clear payment
					$this->order_payment_model->clear_payment($code);
				}
			}


			//--- 6. ลบรายการที่ผู้ไว้ใน order_transform_detail (กรณีเบิกแปรสภาพ)
			if($role == 'T')
			{
				$this->transform_model->clear_transform_detail($code);
				$this->transform_model->close_transform($code);
			}
		}

		return $sc;
  }

  //--- รับ obj
  public function clear_credit_payment($order)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('account/order_credit_model');
    //--- ดึงยอดที่เคยตั้งหนี้ไว้ แล้วทำให้เป็นค่าลบเพื่อบวกกลับเข้ายอดใช้ไป
    $credit = $this->order_credit_model->get($order->code);

    if( ! empty($credit))
    {
      $amount = $credit->amount * (-1);
      //--- คืนยอดใช้ไป
      if($this->customers_model->update_used($order->customer_code, $amount))
      {
        //--- ลบรายการตั้งหนี้
        return $this->order_credit_model->delete($order->code);
      }

      return FALSE;
    }

    return TRUE;
  }

  //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
  public function clear_buffer($code)
  {
    $sc = TRUE;

    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');

    $buffer = $this->buffer_model->get_all_details($code);
    //--- ถ้ายังมีรายการที่ค้างอยู่ใน buffer เคลียร์เข้า cancle
    if(!empty($buffer))
    {
      foreach($buffer as $rs)
      {
        if($rs->qty != 0)
        {
          $arr = array(
            'order_code' => $rs->order_code,
            'product_code' => $rs->product_code,
            'warehouse_code' => $rs->warehouse_code,
            'zone_code' => $rs->zone_code,
            'qty' => $rs->qty,
            'user' => get_cookie('uname')
          );
          //--- move buffer to cancle
          if($sc === TRUE && ! $this->cancle_model->add($arr))
          {
            $sc = FALSE;
          }
        }
        //--- delete cancle
        if($sc === TRUE && ! $this->buffer_model->delete($rs->id))
        {
          $sc = FALSE;
        }
      }
    }

    return $sc;
  }


  public function update_discount()
  {
    $code = $this->input->post('order_code');
    $discount = $this->input->post('discount');
    $approver = $this->input->post('approver');
    $order = $this->orders_model->get($code);
    $user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');
  	if(!empty($discount))
  	{
  		foreach( $discount as $id => $value )
  		{
  			//----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
  			if( $value != "")
  			{
  				//--- ได้ Obj มา
  				$detail = $this->orders_model->get_detail($id);

  				//--- ถ้ารายการนี้มีอยู่
  				if( $detail !== FALSE )
  				{
  					//------ คำนวณส่วนลดใหม่
  					$step = explode('+', $value);
  					$discAmount = 0;
  					$discLabel = array(0, 0, 0);
  					$price = $detail->price;
  					$i = 0;
  					foreach($step as $discText)
  					{
  						if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
  						{
  							$disc = explode('%', $discText);
  							$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
  							$discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
  							$discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
  							$discAmount += $discount;
  							$price -= $discount;
  						}
  						$i++;
  					}

  					$total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
  					$total_amount = ( $detail->qty * $detail->price ) - $total_discount; //--- ยอดรวมสุดท้าย

  					$arr = array(
  								"discount1" => $discLabel[0],
  								"discount2" => $discLabel[1],
  								"discount3" => $discLabel[2],
  								"discount_amount"	=> $total_discount,
  								"total_amount" => $total_amount ,
  								"id_rule"	=> NULL,
                  "update_user" => $user
  							);

  					$cs = $this->orders_model->update_detail($id, $arr);
            if($cs)
            {
              $log_data = array(
    												"order_code"		=> $code,
    												"product_code"	=> $detail->product_code,
    												"old_discount"	=> discountLabel($detail->discount1, $detail->discount2, $detail->discount3),
    												"new_discount"	=> discountLabel($discLabel[0], $discLabel[1], $discLabel[2]),
    												"user"	=> $user,
    												"approver"		=> $approver
    												);
    					$this->discount_logs_model->logs_discount($log_data);
            }

  				}	//--- end if detail
  			} //--- End if value
  		}	//--- end foreach

      $this->orders_model->set_status($code, 0);
  	}
    echo 'success';
  }


  public function update_non_count_price()
  {
    $sc = TRUE;

    $code = $this->input->post('order_code');
    $id = $this->input->post('id_order_detail');
    $price = empty($this->input->post('price')) ? 0 : $this->input->post('price');
    $user = $this->_user->uname;

    $order = $this->orders_model->get($code);

    if($order->state == 8) //--- ถ้าเปิดบิลแล้ว
    {
      $sc = FALSE;
      $this->error = 'ไม่สามารถแก้ไขราคาได้ เนื่องจากออเดอร์ถูกเปิดบิลไปแล้ว';
    }
    else
    {
      //--- ได้ Obj มา
      $detail = $this->orders_model->get_detail($id);

      //--- ถ้ารายการนี้มีอยู่
      if( ! empty($detail))
      {
        //------ คำนวณส่วนลดใหม่
        $price_c = $price;

        $discAmount = 0;

        $step = array($detail->discount1, $detail->discount2, $detail->discount3);

        foreach($step as $discount)
        {
          $disc 	= explode('%', $discount);
          $disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
          $discount = count($disc) == 1 ? $disc[0] : $price_c * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
          $discAmount += $discount;
          $price_c -= $discount;
        }

        $total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
        $total_amount = ( $detail->qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย

        $arr = array(
          "price"	=> $price,
          "discount_amount"	=> $total_discount,
          "total_amount" => $total_amount,
          "update_user" => $user
        );

        if( ! $this->orders_model->update_detail($id, $arr))
        {
          $sc = FALSE;
          $this->error = "แก้ไขราคาไม่สำเร็จ";
        }
        else
        {
          update_order_total_amount($code);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการสินค้า";
      }
    }

    $this->response($sc);
  }



  public function update_price()
  {
    $code = $this->input->post('order_code');
    $ds = $this->input->post('price');
  	$approver	= $this->input->post('approver');
  	$user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');
  	foreach( $ds as $id => $value )
  	{
  		//----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
  		if( $value != "" )
  		{
  			//--- ได้ Obj มา
  			$detail = $this->orders_model->get_detail($id);

  			//--- ถ้ารายการนี้มีอยู่
  			if( $detail !== FALSE )
  			{
          if($detail->price != $value)
          {
            //------ คำนวณส่วนลดใหม่
    				$price 	= $value;
            $discAmount = 0;
            $step = array($detail->discount1, $detail->discount2, $detail->discount3);
            foreach($step as $discount_text)
            {
              $disc 	= explode('%', $discount_text);
              $disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
              $discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
              $discAmount += $discount;
              $price -= $discount;
            }

            $total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
  					$total_amount = ( $detail->qty * $value ) - $total_discount; //--- ยอดรวมสุดท้าย

            $arr = array(
              'price' => $value,
              'discount_amount' => $total_discount,
              'total_amount' => $total_amount,
              'update_user' => $user
            );

            $cs = $this->orders_model->update_detail($id, $arr);
            if($cs)
            {
              $log_data = array(
                "order_code"		=> $code,
                "product_code"	=> $detail->product_code,
                "old_price"	=> $detail->price,
                "new_price"	=> $value,
                "user"	=> $user,
                "approver"		=> $approver
              );
              $this->discount_logs_model->logs_price($log_data);
            }
          }

  			}	//--- end if detail
  		} //--- End if value
  	}	//--- end foreach

    update_order_total_amount($code);
    $this->orders_model->set_status($code, 0);

  	echo 'success';
  }


  public function paid_order($code)
  {
    $sc = TRUE;
    $this->load->model('account/payment_receive_model');
    $order = $this->orders_model->get($code);
    if($order->is_paid == 0)
    {
      //--- บันทึกรับเงิน
      //--- เพิ่มรายการเข้า payment_receive
      $payment = array(
        'reference' => $order->code,
        'customer_code' => $order->customer_code,
        'pay_date' => now(),
        'amount' => $order->balance,
        'payment_type' => 'TR',
        'valid' => 1
      );

      $this->db->trans_begin();

      if(! $this->payment_receive_model->add($payment) )
      {
        $sc = FALSE;
        $this->error = 'เพิ่มรายการเงินเข้าไม่สำเร็จ';
      }

      if( ! $this->orders_model->paid($code, TRUE))
      {
        $sc = FALSE;
        $this->error = 'เปลี่ยนสถานะออเดอร์เป็นชำระแล้วไม่สำเร็จ';
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

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function unpaid_order($code)
  {
    $sc = TRUE;
    $this->load->model('account/payment_receive_model');
    $order = $this->orders_model->get($code);
    if($order->is_paid == 1)
    {
      //--- บันทึกรับเงิน
      //--- เพิ่มรายการเข้า payment_receive
      $payment = array(
        'reference' => $order->code,
        'customer_code' => $order->customer_code,
        'pay_date' => now(),
        'amount' => (-1) * $order->balance,
        'payment_type' => 'TR',
        'valid' => 1
      );

      $this->db->trans_begin();

      if(! $this->payment_receive_model->add($payment) )
      {
        $sc = FALSE;
        $this->error = 'เพิ่มรายการเงินเข้าไม่สำเร็จ';
      }

      if( ! $this->orders_model->paid($code, FALSE))
      {
        $sc = FALSE;
        $this->error = 'ยกเลิกสถานะออเดอร์ไม่สำเร็จ';
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

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function get_summary()
  {
    $this->load->model('masters/bank_model');
    $code = $this->input->post('order_code');
    $order = $this->orders_model->get($code);
    $details = $this->orders_model->get_order_details($code);
    $bank = $this->bank_model->get_active_bank();
    if(!empty($details))
    {
      echo get_summary($order, $details, $bank); //--- order_helper;
    }
  }


  public function get_template_file()
  {
    $path = $this->config->item('upload_path').'orders/';
    $file_name = $path."import_order_template.xlsx";

    if(file_exists($file_name))
    {
      header('Content-Description: File Transfer');
      header('Content-Type:Application/octet-stream');
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
      header('Content-Length: '.filesize($file_name));
      header('Pragma: public');

      flush();
      readfile($file_name);
      die();
    }
    else
    {
      echo "File Not Found";
    }
  }


  public function clear_filter()
  {
    $filter = array(
      'order_code',
      'order_customer',
      'order_user',
      'order_reference',
      'reference2',
      'type_code',
      'sale_code',
      'order_shipCode',
      'order_channels',
      'order_payment',
      'order_fromDate',
      'order_toDate',
      'order_warehouse',
      'notSave',
      'onlyMe',
      'isExpire',
      'order_order_by',
      'order_sort_by',
			'from_date',
			'to_date',
      'state_1',
      'state_2',
      'state_3',
      'state_4',
      'state_5',
      'state_6',
      'state_7',
      'state_8',
      'state_9',
      'stated',
      'startTime',
      'endTime'
    );

    clear_filter($filter);
  }


}
?>
