<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_pos extends PS_Controller
{
  public $menu_code = 'SOPOS';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = '';

  public function __construct()
  {
    parent::__construct();
		$this->home = base_url().'pos/order_pos';
		$this->load->model('masters/shop_model');
		$this->load->model('masters/pos_model');
		$this->load->model('orders/order_pos_model');
		$this->load->model('orders/discount_model');
    $this->load->helper('bank');
		$this->load->helper('discount');

  }


  public function index()
  {
		$this->title = "เลือกจุดขาย";

		$pos = $this->pos_model->get_active_pos_list();

		$ds = array(
			'list' => $pos
		);

		$this->load->view('pos/pos_list', $ds);
  }


	public function main($pos_id)
	{
		//---
		$hold_bills = $this->order_pos_model->count_hold_bills($pos_id); //---- hold by pos id

		$ds = array(
			'pos_id' => $pos_id,
			'hold_bills' => $hold_bills
		);

		$this->load->view('pos/pos_main', $ds);
	}



	public function add($id)
	{
		if($this->pm->can_add)
		{
			$this->load->model('masters/payment_methods_model');
			$this->load->helper('payment_method');
			$this->title = "POS";
			$pos = $this->pos_model->get_pos($id);

			if(!empty($pos))
			{
				//---- get current not save order code
				//--- return order_code if exists
				//--- return NULL if not exists
				$order_code = $this->order_pos_model->get_not_save_order($id);

				if(empty($order_code))
				{
					$code = $this->get_new_code($pos->prefix);
					$payment_code = getConfig('POS_DEFAULT_PAYMENT');
					$channels_code = getConfig('POS_CHANNELS');
					$payment = $this->payment_methods_model->get($payment_code);

					$arr = array(
						'code' => $code,
						'customer_code' => $pos->customer_code,
						'customer_name' => $pos->customer_name,
						'channels_code' => $channels_code,
						'payment_code' => $payment_code,
						'payment_role' => empty($payment) ? 2 : $payment->role,
						'acc_no' => empty($payment) ? NULL : $payment->acc_id,
						'shop_id' => $pos->shop_id,
						'pos_id' => $pos->id,
						'pos_code' => $pos->pos_code,
						'date_add' => date('Y-m-d H:i:s'),
						'uname' => $this->_user->uname
					);

					if(! $this->order_pos_model->add($arr))
					{
						$error = $this->db->error();
						$this->page_error($error['message']);
						exit();
					}
					else
					{
						$order_code = $code;
					}
				}

				$order = $this->order_pos_model->get($order_code);

				if(!empty($order))
				{
					$details = $this->order_pos_model->get_details($order->code);
					$this->title = $pos->name;
					$pos->order = $order;
					$pos->details = $details;
					$pos->customer_list = $this->pos_model->get_customer_shop_list($pos->shop_id);
					$this->load->view('pos/pos', $pos);
				}
				else
				{
					$this->page_error();
				}

			}
			else
			{
				$this->page_error();
			}
		}
		else
		{
			$this->deny_page();
		}
	}


	public function edit($pos_id, $order_code)
	{
		if($this->pm->can_add OR $this->pm->can_edit)
		{
			$this->load->model('masters/payment_methods_model');
			$this->load->helper('payment_method');
			$this->title = "POS";
			$pos = $this->pos_model->get_pos($pos_id);

			if(!empty($pos))
			{
				$order = $this->order_pos_model->get($order_code);

				if(!empty($order))
				{
					if($order->status != 2)
					{
						$this->error = "Invalid Order Status";
						$this->page_error($this->error);
					}
					else if($order->pos_id != $pos_id)
					{
						$this->error = "Invalid pos_id";
						$this->page_error;
					}
					else
					{
						$details = $this->order_pos_model->get_details($order->code);
						$this->title = $pos->name;
						$pos->order = $order;
						$pos->details = $details;
						$pos->customer_list = $this->pos_model->get_customer_shop_list($pos->shop_id);
						$this->load->view('pos/pos', $pos);
					}
				}
				else
				{
					$this->page_error();
				}

			}
			else
			{
				$this->page_error();
			}
		}
		else
		{
			$this->deny_page();
		}
	}



	public function hold_bill()
	{
		$sc = TRUE;

		$order_code = trim($this->input->post('order_code'));
		$ref_note = trim($this->input->post('reference_note'));

		if(!empty($order_code) && $ref_note != '')
		{
			$status = $this->order_pos_model->get_status($order_code);
			if($status === FALSE)
			{
				$sc = FALSE;
				$this->error = "Invalid Order Number";
			}
			else
			{
				if($status == 3)
				{
					$sc = FALSE;
					$this->error = "Hold Failed : Order already Canceled";
				}

				if($status == 1)
				{
					$sc = FALSE;
					$this->error = "Hold failed : Order already Closed";
				}

				if($status == 0)
				{
					$this->db->trans_begin();
					//---- hole details
					if(! $this->order_pos_model->hold_details($order_code))
					{
						$sc = FALSE;
						$this->error = "Hold detail failed";
					}

					if($sc === TRUE)
					{
						if(! $this->order_pos_model->hold_order($order_code, $ref_note))
						{
							$sc = FALSE;
							$this->error = "Hold failed : Update status failed";
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
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$this->response($sc);
	}




	public function get_hold_bills($pos_id)
	{
		$bills = $this->order_pos_model->get_hold_orders($pos_id);

		if(!empty($bills))
		{
			$ds = array();

			foreach($bills as $rs)
			{
				$arr = array(
					'order_code' => $rs->code,
					'pos_id' => $rs->pos_id,
					'ref_note' => $rs->reference_note
				);

				array_push($ds, $arr);
			}

			echo json_encode($ds);
		}
		else {
			echo "ไม่พบรายการ";
		}
	}



	public function add_to_order()
	{
		$sc = TRUE;

		$this->load->model('masters/products_model');
		$this->load->model('orders/discount_model');

		$order_code = trim($this->input->get('order_code'));
		$product_code = trim($this->input->get('product_code'));
		$customer_code = trim($this->input->get('customer_code'));
		$payment_code = trim($this->input->get('payment_code'));
		$zone_code = trim($this->input->get('zone_code'));
		$channels_code = trim($this->input->get('channels_code'));

		$item = $this->products_model->get_product_by_barcode($product_code); //--- barcode or code

		if(!empty($item))
		{
			$order = $this->order_pos_model->get($order_code);

			if(!empty($order))
			{
				$detail = $this->order_pos_model->get_order_detail_by_product($order_code, $product_code);

				if(empty($detail))
				{
					$qty = 1;
					$discount = $this->discount_model->get_item_discount($item->code, $customer_code, $qty, $payment_code, $channels_code, date('Y-m-d'));
					$vat_rate = $this->products_model->get_vat_rate($item->code);
					$item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $qty, 2);
					$sell_price = $item->price - $item_disc_amount;
					$total_amount = round($sell_price * $qty, 2);

					$arr = array(
						'item_type' => $item->count_stock ? 'I' : 'S',
						'order_code' => $order_code,
						'product_code' => $item->code,
						'product_name' => $item->name,
						'unit_code' => $item->unit_code,
						'qty' => $qty,
						'std_price' => $item->price,
						'price' => $item->price,
						'discount_label' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
						'discount_amount' => $discount['amount'],
						'final_price' => $sell_price,
						'total_amount' => $total_amount,
						'vat_rate' => $vat_rate,
						'vat_amount' => $total_amount * ($vat_rate * 0.01),
						'is_count' => $item->count_stock,
						'zone_code' => $zone_code,
						'status' => 0
					);

					$id = $this->order_pos_model->add_detail($arr);

					if(!empty($id))
					{
						$arr['id'] = $id;

						echo json_encode($arr);
					}
					else
					{
						$error = $this->db->error();
						echo "Insert Item failed : ".$error['message'];
					}
				}
				else
				{
					//---- update detail
					$qty = $detail->qty + 1;
					$discount = $this->discount_model->get_item_discount($item->code, $customer_code, $qty, $payment_code, $channels_code, date('Y-m-d'));
					$vat_rate = $this->products_model->get_vat_rate($item->code);
					$item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $qty, 2);
					$sell_price = $item->price - $item_disc_amount;
					$total_amount = round($sell_price * $qty, 2);

					$arr = array(
						'qty' => $qty,
						'discount_label' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
						'discount_amount' => $discount['amount'],
						'final_price' => $sell_price,
						'total_amount' => $total_amount,
						'vat_rate' => $vat_rate,
						'vat_amount' => $total_amount * ($vat_rate * 0.01)
					);

					if($this->order_pos_model->update_detail($detail->id, $arr))
					{
						$arr = array(
							'id' => $detail->id,
							'item_type' => $item->count_stock ? 'I' : 'S',
							'order_code' => $order_code,
							'product_code' => $item->code,
							'product_name' => $item->name,
							'unit_code' => $item->unit_code,
							'qty' => $qty,
							'std_price' => $item->price,
							'price' => $item->price,
							'discount_label' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
							'discount_amount' => $discount['amount'],
							'final_price' => $sell_price,
							'total_amount' => $total_amount,
							'vat_rate' => $vat_rate,
							'vat_amount' => $total_amount * ($vat_rate * 0.01),
							'is_count' => $item->count_stock,
							'zone_code' => $zone_code,
							'status' => 0
						);

						echo json_encode($arr);
					}
					else
					{
						$error = $this->db->error();
						echo "Update item failed : ".$error['message'];
					}
				}
			}
			else
			{
				echo "Invalid Order code : {$order_code}";
			}
		}
		else
		{
			echo "No item found";
		}
	}



	public function update_item()
	{
		$sc = TRUE;
		$result = array();

		$this->load->helper('discount');

		$id = $this->input->post('id');
		$qty = $this->input->post('qty');
		$price = $this->input->post('price');
		$discount_label = trim($this->input->post('discount_label'));

		if(!empty($id))
		{
			$detail = $this->order_pos_model->get_detail($id);
			if(!empty($detail))
			{
				//-- discount_helper
				//-- return discount array per 1 item
				$discount = parse_discount_text($discount_label, $price);
				$sell_price = $price - $discount['discount_amount'];
				$total_amount = $sell_price * $qty;

				$arr = array(
					'qty' => $qty,
					'price' => $price,
					'discount_label' => $discount_label,
					'discount_amount' => $discount['discount_amount'] * $qty,
					'final_price' => $sell_price,
					'total_amount' => $total_amount,
					'vat_amount' => $total_amount * ($detail->vat_rate * 0.01)
				);

				if(! $this->order_pos_model->update_detail($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Item Not found";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Row id not found";
		}

		$this->response($sc);
	}



	public function update_order()
	{
		$sc = TRUE;

		$order_code = trim($this->input->post('order_code'));
		$customer_code = trim($this->input->post('customer_code'));
		$customer_name = trim($this->input->post('customer_name'));
		$recal = $this->input->post('recal_discount');

		$arr = array(
			'customer_code' => $customer_code,
			'customer_name' => $customer_name
		);

		if($this->order_pos_model->update($order_code, $arr))
		{
			if(!empty($recal))
			{
				$this->load->model('masters/products_model');
				$this->load->model('orders/discount_model');
				$this->load->helper('discount');
				$payment_code = $this->input->post('payment_code');
				$channels_code = $this->input->post('channels_code');

				$details = $this->order_pos_model->get_details($order_code);

				if(!empty($details))
				{
					foreach($details as $rs)
					{
						$qty = $rs->qty;
						$discount = $this->discount_model->get_item_discount($rs->product_code, $customer_code, $qty, $payment_code, $channels_code, date('Y-m-d'));
						$vat_rate = $this->products_model->get_vat_rate($rs->product_code);
						$item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $qty, 2);
						$sell_price = $rs->price - $item_disc_amount;
						$total_amount = round($sell_price * $qty, 2);

						$arr = array(
							'qty' => $qty,
							'discount_label' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
							'discount_amount' => $discount['amount'],
							'final_price' => $sell_price,
							'total_amount' => $total_amount,
							'vat_rate' => $vat_rate,
							'vat_amount' => $total_amount * ($vat_rate * 0.01)
						);
					}
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Update Order failed";
		}

		$this->response($sc);
	}


	public function remove_item()
	{
		$sc = TRUE;
		$id = $this->input->post('id');

		if(! $this->order_pos_model->delete_detail($id))
		{
			$sc = FALSE;
			$this->error = "Delete failed";
		}

		$this->response($sc);
	}




  public function save_order()
	{
		$sc = TRUE;

		$this->load->model('inventory/movement_model');
		$this->load->model('inventory/delivery_order_model');
		$this->load->model('stock/stock_model');
		$this->load->model('masters/products_model');
		$this->load->model('masters/payment_methods_model');
		$this->load->model('masters/channels_model');
		$this->load->model('masters/customers_model');

		$order_code = trim($this->input->post('order_code'));

		if(!empty($order_code))
		{
			$payment_code = trim($this->input->post('payment_code'));
			$payment_role = trim($this->input->post('payment_role'));
			$acc_no = trim($this->input->post('acc_no'));
			$warehouse_code = trim($this->input->post('warehouse_code'));
			$amount = $this->input->post('amount');
			$received = $this->input->post('received');
			$changed = $this->input->post('changed');
			$is_paid = 0;

			if($payment_role == 2 OR $payment_role == 3 OR $payment_role == 5)
			{
				if($received >= $amount)
				{
					$is_paid = 1;
				}
			}

			if( $is_paid = 1)
			{
				$order = $this->order_pos_model->get($order_code);

				if(!empty($order))
				{
					if($order->status == 0 OR $order->status == 2) //--- 0 = open, 1 = close, 2 = hold , 3 = cancle
					{
						$details = $this->order_pos_model->get_details($order_code);
						if(!empty($details))
						{
							$this->db->trans_begin();

							$payment_name = $this->payment_methods_model->get_name($order->payment_code);
							$channels_name = $this->channels_model->get_name($order->channels_code);

							foreach($details as $rs)
							{
								if($sc === FALSE)
								{
									break;
								}
								//--- บันทึกขาย
								$item = $this->products_model->get_attribute($rs->product_code);
								$customer = $this->customers_model->get_attribute($order->customer_code);

								//--- ข้อมูลสำหรับบันทึกยอดขาย
								$total_amount_ex = remove_vat($rs->total_amount, $rs->vat_rate);
								$total_cost = $item->cost * $rs->qty;
								$arr = array(

									'reference' => $order->code,
									'role'   => 'O', //--- POS
									'role_name' => 'POS',
									'payment_code'   => $order->payment_code,
									'payment_name' => $payment_name,
									'channels_code'  => $order->channels_code,
									'channels_name' => $channels_name,
									'product_code'  => $rs->product_code,
									'product_name'  => $rs->product_name,
									'product_style' => $item->style_code,
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
									'cost'  => $item->cost,
									'price'  => $rs->price,
									'price_ex' => remove_vat($rs->price, get_zero($rs->vat_rate)),
									'sell'  => $rs->final_price,
									'qty'   => $rs->qty,
									'unit_code' => $item->unit_code,
									'unit_name' => $item->unit_name,
									'vat_code' => $item->vat_code,
									'vat_rate' => get_zero($item->vat_rate),
									'discount_label'  => $rs->discount_label,
									'avgBillDiscAmount' => 0.00, //--- average per single item count
									'discount_amount' => $rs->discount_amount,
									'total_amount'   => $rs->total_amount,
									'total_amount_ex' => $total_amount_ex,
									'vat_amount' => $rs->vat_amount,
									'total_cost'   => $total_cost,
									'margin'  =>  $total_amount_ex - $total_cost,
									'id_policy'   => $rs->id_policy,
									'id_rule'     => $rs->id_rule,
									'customer_code' => $order->customer_code,
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
									'user' => $order->uname,
									'date_add'  => now(),
									'zone_code' => $rs->zone_code,
									'warehouse_code'  => $warehouse_code,
									'update_user' => get_cookie('uname'),
									'budget_code' => NULL,
									'is_count' => $rs->is_count
								);

		            //--- 3. บันทึกยอดขาย
		            if($this->delivery_order_model->sold($arr) !== TRUE)
		            {
		              $sc = FALSE;
		              $message = 'บันทึกขายไม่สำเร็จ';
		              break;
		            }

								//--- ตัดสต็อก
								if($sc === TRUE)
								{
									if(! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, ($rs->qty * -1)))
									{
										$sc = FALSE;
										$error =  $this->db->error();
										$this->error = "Update Stock Failed : {$rs->zone_code} => {$rs->product_code} : ".$error['message'];
									}
								}

								//--- insert movement
								if($sc === TRUE)
								{
									$movement = array(
										'reference' => $order->code,
										'warehouse_code' => $warehouse_code,
										'zone_code' => $rs->zone_code,
										'product_code' => $rs->product_code,
										'move_in' => 0,
										'move_out' => $rs->qty,
										'date_add' => now()
									);

									if(! $this->movement_model->add($movement))
									{
										$sc = FALSE;
										$error =  $this->db->error();
										$this->error = "Update Movement Failed : {$rs->zone_code} => {$rs->product_code} : ".$error['message'];
									}
								}

								if($sc === TRUE)
								{
									$arr = array('status' => 1);
									if(! $this->order_pos_model->update_detail($rs->id, $arr)) //--- saved
									{
										$sc = FALSE;
										$error =  $this->db->error();
										$this->error = "Update item status failed : {$rs->product_code} : ".$error['message'];
									}
								}
							} //--- end foreach

							//---- update order status
							if($sc === TRUE)
							{
								$payment_code = trim($this->input->post('payment_code'));
								$payment_role = trim($this->input->post('payment_role'));
								$acc_no = trim($this->input->post('acc_no'));
								$warehouse_code = trim($this->input->post('warehouse_code'));
								$amount = $this->input->post('amount');
								$received = $this->input->post('received');
								$changed = $this->input->post('changed');
								$arr = array(
									'payment_code' => $payment_code,
									'payment_role' => $payment_role,
									'acc_no' => $payment_role == 3 ? $acc_no : NULL,
									'amount' => $amount,
									'received' => $received,
									'changed' => $changed,
									'status' => 1,
									'is_paid' => $is_paid
								);


								if(! $this->order_pos_model->update($order->code, $arr))
								{
									$sc = FALSE;
									$error =  $this->db->error();
									$this->error = "Update order status failed : ".$error['message'];
								}
							}


							//--- conmmit
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
							$this->error = "No item found";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid Order Status";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid order code : {$order_code}";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ชำระเงินไม่ครบ";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Required parater: order code";
		}

		$this->response($sc);
	}



	public function bill($order_code)
	{
		if(!empty($order_code))
		{
			$this->load->model('masters/payment_methods_model');
			$this->title = "Invoice#{$order_code}";
			$order = $this->order_pos_model->get($order_code);
			$details = $this->order_pos_model->get_details($order_code);
			$shop = $this->shop_model->get($order->shop_id);
			$pos = $this->pos_model->get($order->pos_id);
			$payment = $this->payment_methods_model->get_name($order->payment_code);

			$ds = array(
				'shop' => $shop,
				'pos' => $pos,
				'order' => $order,
				'details' => $details,
				'pay_by' => $payment
			);

			$this->load->view('print/print_pos_bill', $ds);
		}
		else
		{
			$this->page_error();
		}

	}



	public function get_product_data()
	{
		$code = trim($this->input->get('product_code'));
		$zone_code = trim($this->input->get('zone_code'));

		if(! is_null($code) && $code != '')
		{
			$this->load->model('masters/products_model');
			$this->load->model('stock/stock_model');
			$this->load->helper('product_images');


			$item = $this->products_model->get_product_by_barcode($code);

			if(!empty($item))
			{
				$stock = $this->stock_model->get_stock_zone($zone_code, $item->code);
				$image = get_product_image($item->code, 'default');

				$arr = array(
					'item_type' => $item->count_stock ? 'Item' : 'Service',
					'item_code' => $item->code,
					'item_name' => $item->name,
					'cost' => number($item->cost),
					'price' => number($item->price),
					'vat_rate' => $item->vat_rate,
					'qty' => number($stock),
					'img' => $image
				);

				echo json_encode($arr);
			}
			else
			{
				echo "Product Not Found";
			}
		}
		else
		{
			echo "Missing required parameter : product code";
		}
	}

	public function get_new_code($prefix, $date = NULL)
	{
		$date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('Y', strtotime($date));
    $M = date('m', strtotime($date));
    $run_digit = 5;
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_pos_model->get_max_code($pre);

    if(! empty($code))
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



}

//--- End class
?>
