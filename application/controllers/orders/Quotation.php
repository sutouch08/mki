<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends PS_Controller
{
  public $menu_code = 'SOODQT';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ใบเสนอราคา';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/quotation';
    $this->load->model('orders/quotation_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');

    $this->load->helper('product_images');
    $this->load->helper('discount');
  }


  public function index()
  {
    $filter = array(
      'code'          => get_filter('code', 'qu_code', ''),
      'customer_code'      => get_filter('customer', 'qu_customer_code', ''),
      'contact' => get_filter('contact', 'qu_contact', ''),
      'user'          => get_filter('user', 'qu_user', ''),
      'reference'     => get_filter('reference', 'qu_reference', ''),
      'from_date'     => get_filter('fromDate', 'qu_fromDate', ''),
      'to_date'       => get_filter('toDate', 'qu_toDate', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->quotation_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$data = $this->quotation_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($data))
    {
      foreach($data as $rs)
      {
        $rs->amount = $this->quotation_model->get_sum_total_amount($rs->code) - $rs->bDiscAmount;
      }
    }


    $filter['data'] = $data;

		$this->pagination->initialize($init);
    $this->load->view('quotation/quotation_list', $filter);
  }



  public function add_new()
  {
    $data['code'] = $this->get_new_code();
    $this->load->view('quotation/quotation_add', $data);
  }



  public function add()
  {
    if($this->pm->can_add)
    {
      $customer_code = $this->input->post('customerCode');
      if($customer_code != '')
      {
        $customer = $this->customers_model->get($customer_code);
        if(!empty($customer))
        {
          $date = db_date($this->input->post('date_add'));
          $code = $this->get_new_code($date);

          $arr = array(
            'code' => $code,
            'customer_code' => $customer->code,
						'customer_name' => $customer->name,
            'contact' => get_null($this->input->post('contact')),
            'is_term' => $this->input->post('is_term'),
            'credit_term' => $this->input->post('credit_term'),
						'valid_days' => intval($this->input->post('valid_days')),
            'user' => get_cookie('uname'),
						'title' => $this->input->post('title'),
            'remark' => $this->input->post('remark'),
            'date_add' => $date
          );

          if($this->quotation_model->add($arr))
          {
            redirect($this->home.'/edit/'.$code);
          }
        }
        else
        {
          set_error("รหัสลูกค้าไม่ถูกต้อง");
          redirect($this->home ."/add_new");
        }
      }
      else
      {
        set_error("ไม่พบรหัสลูกค้า");
        redirect($this->home ."/add_new");
      }
    }
    else
    {
      set_error("คุณไม่มีสิทธิ์เพิ่มเอกสาร");
      redirect($this->home ."/add_new");
    }

  }


  public function edit($code)
  {
    if($this->pm->can_add)
    {
      $this->load->helper('product_tab');
      $data = $this->quotation_model->get($code);
      $data->customer_name = $this->customers_model->get_name($data->customer_code);
      $ds = array(
        'data' => $data,
        'details' => $this->quotation_model->get_details($code)
      );

      $this->load->view('quotation/quotation_edit', $ds);
    }
    else
    {
      $this->load->view('deny_page');
    }
  }


	public function get_details_table()
	{
		$sc = TRUE;
		$code = $this->input->get('code');
		if(empty($code))
		{
			$sc = FALSE;
			$this->error = "ไม่พบค่าตัวแปรของเลขที่เอกสาร";
		}
		else
		{
			$ds = array();
			$details = $this->quotation_model->get_details($code);
			if(!empty($details))
			{
				$no = 1;
				$total_qty = 0;
				$total_amount = 0;
				$total_discount = 0;

				foreach($details as $rs)
				{
					$arr = array(
						'no' => $no,
						'id' => $rs->id,
						'product_code' => $rs->product_code,
						'product_name' => $rs->product_name,
						'price' => $rs->price,
						'qty' => $rs->qty,
						'discount_label' => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
						'discount_amount' => number($rs->discount_amount,2),
						'amount' => number($rs->total_amount, 2),
						'err' => $rs->total_amount < 0 ? 'has-error' : '',
						'hilight' => $rs->total_amount < 0 ? 'red' : '',
						'cando' => $this->pm->can_edit ? true : false,
						'err' => $total_amount < 0 ? 'has-error' : ''
					);

					array_push($ds, $arr);
					$no++;
					$total_qty += $rs->qty;
					$total_discount += $rs->discount_amount;
					$total_amount += $rs->total_amount;
				}

				$arr = array(
					'subtotal' => true,
					'total_qty' => number($total_qty),
					'total_amount' => number($total_amount, 2),
					'total_discount' => number($total_discount, 2),
					'net_amount' => number(($total_amount - $total_discount), 2)
				);

				array_push($ds, $arr);
			}
			else
			{
				$arr = array(
					'nodata' => true
				);

				array_push($ds, $arr);
			}
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}


  public function update()
  {
    $sc = TRUE;
    if(! $this->pm->can_edit)
    {
      $sc = FALSE;
      $this->error = "No Permission";
    }
    else
    {
      if($this->input->post('code'))
      {
        $code = $this->input->post('code');
        $date = db_date($this->input->post('date_add'), TRUE);
        $customer = $this->customers_model->get($this->input->post('customer_code'));
        if(!empty($customer))
        {
          $arr = array(
            'customer_code' => $customer->code,
						'customer_name' => $customer->name,
            'contact' => get_null($this->input->post('contact')),
            'is_term' => $this->input->post('is_term'),
            'credit_term' => $this->input->post('credit_term'),
						'valid_days' => intval($this->input->post('valid_days')),
            'update_user' => get_cookie('uname'),
						'title' => $this->input->post('title'),
            'remark' => $this->input->post('remark'),
            'date_add' => $date
          );

          if(! $this->quotation_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "แก้ไขข้อมูลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "รหัสลูกค้าไม่ถูกต้อง";
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร";
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function save()
  {
    $sc = TRUE;
		$code = $this->input->post('code');

		if(!empty($code))
		{
			$order = $this->quotation_model->get($code);

			if(!empty($order))
			{
				if($order->status == 0)
				{
					$arr = array('status' => 1);
					if(!$this->quotation_model->update($code, $arr))
					{
						$sc = FALSE;
						$this->error = "บันทึกเอกสารไม่สำเร็จ";
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
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		$this->response($sc);
  }



	public function add_detail()
  {
    $sc = TRUE;
		$code = $this->input->post('code');

    if(!empty($code))
    {
      $this->load->helper('discount');
			$product_code = $this->input->post('product_code');
			$price = $this->input->post('price');
      $discLabel = $this->input->post('discountLabel');
			$qty = $this->input->post('qty');

      $doc = $this->quotation_model->get($code);

      if(!empty($doc))
      {
				if($qty > 0)
				{
					$item = $this->products_model->get($product_code);
					if(!empty($item))
					{
						//---- get current item if exists
						$ds = $this->quotation_model->get_detail_by_item_code($code, $product_code);
						//---- get discount
            $disc = parse_discount_text($discLabel, $price);

						if(!empty($ds))
            {
              $new_qty = $ds->qty + $qty;
              $final_price = $price - $disc['discount_amount'];
              $discount_amount = $disc['discount_amount'] * $new_qty;
              $total_amount = $final_price * $new_qty;

              $arr = array(
                'qty' => $new_qty,
                'discount1' => $disc['discount1'],
                'discount2' => $disc['discount2'],
                'discount3' => $disc['discount3'],
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount
              );

              //--- Update
              if(! $this->quotation_model->update_detail($ds->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Update failed";
              }
            }
            else
            {
              $arr = array(
                'quotation_code' => $code,
                'style_code' => $item->style_code,
                'product_code' => $item->code,
                'product_name' => $item->name,
								'cost' => $item->cost,
                'price' => $price,
                'qty' => $qty,
								'unit_code' => $item->unit_code,
                'discount1' => $disc['discount1'],
                'discount2' => $disc['discount2'],
                'discount3' => $disc['discount3'],
                'discount_amount' => $disc['discount_amount'] * $qty,
                'total_amount' => ($price - $disc['discount_amount']) * $qty,
								'count_stock' => $item->count_stock,
                'date_add' => $doc->date_add
              );
              //---- add
              if(! $this->quotation_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Insert failed";
              }
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
					$this->error = "จำนวนไม่ถูกต้อง";
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
      $this->error = "Missing required parameter : code";
    }

    $this->response($sc);
  }



	public function add_details()
  {
    $sc = TRUE;
		$code = $this->input->post('code');
    if(!empty($code))
    {
      $doc = $this->quotation_model->get($code);


      if(!empty($doc) )
      {
				//---- เอกสารต้องไม่ถูกยกเลิก และ เอกสารต้องยังไม่ถูกปิด
				if($doc->status != 2 && $doc->is_closed == 0)
				{
					$this->load->helper('discount');
		      $discLabel = $this->input->post('disc');
					$items = $this->input->post('items');

					if(!empty($items))
					{
						$this->db->trans_begin();

						foreach($items as $rd)
						{
							if($sc === FALSE)
							{
								break;
							}

							$rs = (object) $rd;

							$item = $this->products_model->get($rs->product_code);

							if(!empty($item))
							{
								if($rs->qty > 0)
								{
									//---- get current item if exists
									$ds = $this->quotation_model->get_detail_by_item_code($code, $item->code);
									//---- get discount
			            $disc = parse_discount_text($discLabel, $item->price);

									if(!empty($ds))
			            {
			              $new_qty = $ds->qty + $rs->qty;
			              $final_price = $item->price - $disc['discount_amount'];
			              $discount_amount = $disc['discount_amount'] * $new_qty;
			              $total_amount = $final_price * $new_qty;

			              $arr = array(
			                'qty' => $new_qty,
			                'discount1' => $disc['discount1'],
			                'discount2' => $disc['discount2'],
			                'discount3' => $disc['discount3'],
			                'discount_amount' => $discount_amount,
			                'total_amount' => $total_amount
			              );

			              //--- Update
			              if(! $this->quotation_model->update_detail($ds->id, $arr))
			              {
			                $sc = FALSE;
			                $this->error = "Update failed";
			              }
			            }
			            else
			            {
			              $arr = array(
			                'quotation_code' => $code,
			                'style_code' => $item->style_code,
			                'product_code' => $item->code,
			                'product_name' => $item->name,
											'cost' => $item->cost,
			                'price' => $item->price,
			                'qty' => $rs->qty,
											'unit_code' => $item->unit_code,
			                'discount1' => $disc['discount1'],
			                'discount2' => $disc['discount2'],
			                'discount3' => $disc['discount3'],
			                'discount_amount' => $disc['discount_amount'] * $rs->qty,
			                'total_amount' => ($item->price - $disc['discount_amount']) * $rs->qty,
											'count_stock' => $item->count_stock,
			                'date_add' => $doc->date_add
			              );
			              //---- add
			              if(! $this->quotation_model->add_detail($arr))
			              {
			                $sc = FALSE;
			                $this->error = "Insert failed";
			              }
			            }
								}
								else
								{
									$sc = FALSE;
									$this->error = "จำนวนต้องมากกว่า 0 : {$rs->product_code}";
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "ไม่พบสินค้าในระบบ {$rs->product_code}";
							}

						} //--- end foreach

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
						$this->error = "จำนวนไม่ถูกต้อง";
					}
				}
				else
				{
					$sc = FALSE;
					if($doc->status == 2)
					{
						$this->error = "เอกสารถูกยกเลิกไปแล้ว";
					}

					if($doc->is_closed == 1)
					{
						$this->error = "เอกสารถูกปิดไปแล้ว";
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
      $this->error = "Missing required parameter : quotation code";
    }

    $this->response($sc);
  }




	public function update_bill_discount()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		$bDiscAmount = trim($this->input->post('bDiscAmount'));

		$arr = array(
			'bDiscAmount' => $bDiscAmount
		);

		if(! $this->quotation_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update Bill Discount Failed";
		}

		$this->response($sc);
	}


	public function view_detail($code)
  {
		$data = $this->quotation_model->get($code);
		$data->customer_name = $this->customers_model->get_name($data->customer_code);
		$ds = array(
			'data' => $data,
			'details' => $this->quotation_model->get_details($code)
		);

		$this->load->view('quotation/quotation_view_detail', $ds);

  }

	public function delete_detail()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		if(!empty($id))
		{
			if(! $this->quotation_model->delete_detail($id))
			{
				$sc = FALSE;
				$this->error = "ลบรายการไม่สำเร็จ";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบ Item ID";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_QUOTATION');
    $run_digit = getConfig('RUN_DIGIT_QUOTATION');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->quotation_model->get_max_code($pre);
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



  public function print_quotation($code)
  {

		$this->load->model('address/customer_address_model');
    $this->load->library('printer');

    $order = $this->quotation_model->get($code);
    $customer = $this->customers_model->get($order->customer_code);
		$customer_address = $this->customer_address_model->get_customer_bill_to_address($order->customer_code);
		$order->emp_name = $this->user_model->get_employee_name($order->user);
    $details = $this->quotation_model->get_details($code);

    $ds['order'] = $order;
    $ds['details'] = $details;
		$ds['customer'] = $customer;
		$ds['address'] = $customer_address;
    $ds['title'] = "ใบเสนอราคา";
    $this->load->view('print/print_quotation', $ds);
  }

  public function get_sell_stock($item_code)
  {
    $sell_stock = $this->stock_model->get_sell_stock($item_code);
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code);
    $availableStock = $sell_stock - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
  }




  public function cancle_quotation()
  {
    $sc = TRUE;
    $code = $this->input->get('code');
		if(!empty($code))
		{
			$qt = $this->quotation_model->get($code);
			if(!empty($qt))
			{
				if($qt->status == 0)
				{
					$this->db->trans_begin();

					//---- cancle quotation details
					if($this->quotation_model->cancle_details($code))
					{
						if(! $this->quotation_model->cancle_quotation($code))
						{
							$sc = FALSE;
							$this->error = "ยกเลิกเอกสารไม่สำเร็จ";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ยกเลิกรายการในใบเสนอราคาไม่สำเร็จ";
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
				else if($qt->status == 1)
				{
					$sc = FALSE;
					$this->error = "ใบเสนอราคาถูกปิดแล้วไม่สามารถยกเลิกได้";
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




	public function get_detail_table()
	{
		$sc = TRUE;
		$code = trim($this->input->get('code'));
		$order = $this->quotation_model->get($code);

		$ds = array();

		if(!empty($order))
		{
			$details = $this->quotation_model->get_details($code);
			if(!empty($details))
			{
				$no = 1;
				$total_qty = 0;
				$total_discount = 0;
				$total_amount = 0;
				$cando = ($this->pm->can_add OR $this->pm->can_edit) ? TRUE : FALSE;
				foreach($details as $rs)
				{
					$arr = array(
						'id' => $rs->id,
						'no' => $no,
						'img' => get_product_image($rs->product_code, 'mini'),
						'product_code' => $rs->product_code,
						'product_name' => $rs->product_name,
						'price' => $rs->price,
						'qty' => $rs->qty,
						'discount_label' => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
						'amount' => $rs->total_amount,
						'cando' => $cando
					);

					array_push($ds, $arr);
					$no++;
					$total_qty += $rs->qty;
					$total_discount += $rs->discount_amount;
					$total_amount += $rs->total_amount;
				}

				$arr = array(
					'subtotal' => TRUE,
					'bDiscAmount' => $order->bDiscAmount,
					'total_qty' => number($total_qty, 2),
					'totalAfDisc' => $total_amount,
					'totalBfDisc' => number(($total_amount + $total_discount), 2),
					'total_discount' => number($total_discount + $order->bDiscAmount, 2),
					'net_amount' => number($total_amount - $order->bDiscAmount, 2)
				);

				array_push($ds, $arr);
			}
			else
			{
				$arr = array('nodata' => TRUE);
				array_push($ds, $arr);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid parameter : code";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function update_row()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		if(!empty($id))
		{
			$price = $this->input->post('price');
			$qty = $this->input->post('qty');
			$discountLabel = $this->input->post('discountLabel');

			//--- ได้ Obj มา
			$detail = $this->quotation_model->get_detail($id);

			//--- ถ้ารายการนี้มีอยู่
			if( $detail !== FALSE )
			{
				//------ คำนวณส่วนลดใหม่
				$step = explode('+', $discountLabel);
				$discAmount = 0;
				$discLabel = array(0, 0, 0);
				$pricex = $price;
				$i = 0;
				if(!empty($step[0]))
				{
					foreach($step as $discText)
					{
						if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
						{
							$disc = explode('%', $discText);
							$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
							$discount = count($disc) == 1 ? $disc[0] : $pricex * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
							$discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
							$discAmount += $discount;
							$pricex -= $discount;
						}
						$i++;
					}
				}


				$total_discount = $qty * $discAmount; //---- ส่วนลดรวม
				$total_amount = ( $qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย

				$arr = array(
							"qty" => $qty,
							"price" => $price,
							"discount1" => $discLabel[0],
							"discount2" => $discLabel[1],
							"discount3" => $discLabel[2],
							"discount_amount"	=> $total_discount,
							"total_amount" => $total_amount ,
							"update_user" => get_cookie('uname')
						);


				if(! $this->quotation_model->update_detail($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Id for qoutation detail";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Quotation detail's id was not found";
		}


		$this->response($sc);
	}



	public function unsave_quotation()
	{
		$sc = TRUE;
		$code = $this->input->post('code');

		$arr = array('status' => 0);

		if(! $this->quotation_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update failed";
		}

		$this->response($sc);
	}


  //---- get item price
  public function get_item()
  {
    $sc = TRUE;
		$ds = array();
		$code = $this->input->get('item_code');
		if(!empty($code))
		{
			$rs = $this->products_model->get($code);
			if(!empty($rs))
			{
				$ds = array(
					"product_code" => $rs->code,
					"product_name" => $rs->name,
					"price" => $rs->price
				);
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
			$this->error = "ไม่พบรหัสสินค้า";
		}


		echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


	//------- OLD code

  public function clear_filter()
  {
    $filter = array(
      'qu_code',
      'qu_customer_code',
      'qu_contact',
      'qu_user',
      'qu_reference',
      'qu_fromDate',
      'qu_toDate',
    );

    clear_filter($filter);
  }
}
?>
