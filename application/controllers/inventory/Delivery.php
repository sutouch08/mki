<?php
class Delivery extends PS_Controller
{
	public $menu_code = 'ICODDO';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'รายการรอเปิดบิล';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
  }


	//--- skip pick pack
	public function confirm_order($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
		$this->load->model('inventory/qc_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('masters/customers_model');
		$this->load->model('masters/channels_model');
		$this->load->model('masters/payment_methods_model');
		$this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');
		$this->load->model('masters/zone_model');
		$this->load->model('masters/vat_model');

    $this->load->helper('discount');
		$this->load->helper('order');
		$use_credit = getConfig('CONTROL_CREDIT') == 1 ? TRUE : FALSE;
		$auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;

		$default_zone = getConfig('DEFAULT_ZONE');
		$warehouse_code = $this->zone_model->get_warehouse_code($default_zone);

    if( ! empty($code))
    {
      $order = $this->orders_model->get($code);

			if( ! empty($order))
			{
				if($order->role == 'T')
				{
					$this->load->model('inventory/transform_model');
				}

				//--- กรณียืมสินค้า
				if($order->role == 'L')
				{
					$this->load->model('inventory/lend_model');
				}

				//---- กรณีฝากขาย (โอนคลัง)
				if($order->role == 'N' OR $order->role == 'C')
				{
					$this->load->model('orders/consign_model');
					$this->load->model('masters/zone_model');
					$warehouse_code = $this->zone_model->get_warehouse_code($order->zone_code);
				}

				//-- กรณี สปอนเซอร์
        if($order->role == 'P')
        {
          $this->load->model('masters/sponsor_budget_model');
        }

				if($order->state == 3)
				{
					if($order->picked == 0)
					{
						$this->db->trans_begin();

						//--- change state
						$arr = array(
							'state' => 8,
							'picked' => 0,
							'packed' => 0,
							'update_user' => $this->_user->uname
						);

						if( ! $this->orders_model->update($code, $arr))
						{
							$sc = FALSE;
							$this->error = "Failed to change order state";
						}

						if($sc === TRUE)
						{
							//--- add state event
							$arr = array(
								'order_code' => $code,
								'state' => 8,
								'update_user' => $this->_user->uname
							);

							if( ! $this->order_state_model->add_state($arr))
							{
								$sc = FALSE;
								$this->error = "Failed to add state history";
							}
						}

						if($sc === TRUE)
						{
							//--- วันที่บันทึกขาย ตามการตั้งค่า D = ใช้วันที่ตามเอกสาร B = ใช้วันที่กดเปิดบิล
							$sold_date = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : now();

							//---- รายการทีรอการเปิดบิล
							$bill = $this->delivery_order_model->get_order_details($code);

							$customer = $this->customers_model->get_attribute($order->customer_code);

							$avgBillDiscAmount = 0;
							$docTotal = 0;

							if($order->bDiscAmount > 0)
							{
								$total = $order->total_amount + $order->bDiscAmount;
		            $avgBillDiscAmount = $total > 0 ? round(($order->bDiscAmount/$total), 6) : 0;
							}

							if( ! empty($bill))
							{
								foreach($bill as $rs)
								{
									//--- ถ้ามีรายการที่ไมสำเร็จ ออกจาก loop ทันที
									if($sc === FALSE)
									{
										break;
									}

									//--- ถ้ายอดตรวจ น้อยกว่า หรือ เท่ากับ ยอดสั่ง ใช้ยอดตรวจในการตัด buffer
									//--- ถ้ายอดตวจ มากกว่า ยอดสั่ง ให้ใช้ยอดสั่งในการตัด buffer (บางทีอาจมีการแก้ไขออเดอร์หลังจากมีการตรวจสินค้าแล้ว)

									$sell_qty = $rs->order_qty;

									if($rs->order_qty > 0)
									{
										$this->stock_model->update_stock_zone($default_zone, $rs->product_code, $rs->order_qty);

										//--- 2. update movement
										$arr = array(
											'reference' => $order->code,
											'warehouse_code' => $warehouse_code,
											'zone_code' => $default_zone,
											'product_code' => $rs->product_code,
											'move_in' => 0,
											'move_out' => $rs->order_qty,
											'date_add' => $sold_date
										);

										if($this->movement_model->add($arr) === FALSE)
										{
											$sc = FALSE;
											$this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
											break;
										}

										//--- กรณีฝากขาย
										if($order->role === 'N' OR $order->role === 'C')
										{
											//--- 1. เพิ่มสต็อกเข้าโซนปลายทาง
											if($this->stock_model->update_stock_zone($order->zone_code, $rs->product_code, $rs->order_qty) !== TRUE)
											{
												$sc = FALSE;
												$this->error = 'โอนสินค้าเข้าโซนปลายทางไม่สำเร็จ';
											}

											//--- 2. เพิ่ม movement เข้าปลายทาง
											$arr = array(
												'reference' => $order->code,
												'warehouse_code' => empty($warehouse_code) ? $this->zone_model->get_warehouse_code($order->zone_code) : $warehouse_code,
												'zone_code' => $order->zone_code,
												'product_code' => $rs->product_code,
												'move_in' => $rs->order_qty,
												'move_out' => 0,
												'date_add' => $sold_date
											);

											if($this->movement_model->add($arr) === FALSE)
											{
												$sc = FALSE;
												$this->error = 'บันทึก movement ขาเข้าไม่สำเร็จ';
												break;
											}
										}

										$line_total = $rs->final_price * $rs->order_qty;
										$sumBillDiscAmount = $avgBillDiscAmount > 0 ? round(($line_total * $avgBillDiscAmount), 2) : 0;
										$total_amount = $line_total - $sumBillDiscAmount;
										$docTotal += $total_amount;

										//--- ข้อมูลสำหรับบันทึกยอดขาย
										$item = $this->products_model->get_attribute($rs->product_code);
										$total_amount_ex = remove_vat($total_amount, $item->vat_rate);
										$total_cost = $rs->cost * $rs->order_qty;

										$arr = array(
											'reference' => $order->code,
											'role'   => $order->role,
											'role_name' => role_name($order->role),
											'payment_code'   => $order->payment_code,
											'payment_name' => $this->payment_methods_model->get_name($order->payment_code),
											'channels_code'  => $order->channels_code,
											'channels_name' => $this->channels_model->get_name($order->channels_code),
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
											'price_ex' => remove_vat($rs->price, $item->vat_rate),
											'sell'  => $rs->final_price,
											'qty'   => $rs->order_qty,
											'unit_code' => $item->unit_code,
											'unit_name' => $item->unit_name,
											'vat_code' => $item->vat_code,
											'vat_rate' => get_zero($item->vat_rate),
											'discount_label'  => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
											'avgBillDiscAmount' => $sumBillDiscAmount, //--- average per bath * line_total
											'discount_amount' => ($rs->discount_amount * $rs->order_qty) + $sumBillDiscAmount,
											'total_amount'   => $total_amount,
											'total_amount_ex' => $total_amount_ex,
											'vat_amount' => $total_amount - $total_amount_ex, 
											'total_cost'   => $total_cost,
											'margin'  =>  $total_amount_ex - $total_cost,
											'id_policy'   => $rs->id_policy,
											'id_rule'     => $rs->id_rule,
											'customer_code' => $order->customer_code,
											'customer_name' => $customer->name,
											'customer_ref' => $order->customer_ref,
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
											'user' => $order->user,
											'date_add'  => $sold_date,
											'zone_code' => $default_zone,
											'warehouse_code'  => $warehouse_code,
											'update_user' => get_cookie('uname'),
											'budget_id' => $order->budget_id,
											'budget_code' => $order->budget_code,
											'is_count' => $rs->is_count
										);

										//--- 3. บันทึกยอดขาย
										if($this->delivery_order_model->sold($arr) !== TRUE)
										{
											$sc = FALSE;
											$this->error = 'บันทึกขายไม่สำเร็จ';
											break;
										}
									} //--- end if sell_qty > 0

									//------ ส่วนนี้สำหรับโอนเข้าคลังระหว่างทำ
									//------ หากเป็นออเดอร์เบิกแปรสภาพ
									if($order->role == 'T')
									{
										$sold_qty = $rs->order_qty;

										//--- ยอดสินค้าที่มีการเชื่อมโยงไว้ในตาราง tbl_order_transform_detail (เอาไว้โอนเข้าคลังระหว่างทำ รอรับเข้า)
										//--- ถ้ามีการเชื่อมโยงไว้ ยอดต้องมากกว่า 0 ถ้ายอดเป็น 0 แสดงว่าไม่ได้เชื่อมโยงไว้
										$trans_list = $this->transform_model->get_transform_product($rs->id);

										if( ! empty($trans_list))
										{
											//--- ถ้าไม่มีการเชื่อมโยงไว้
											foreach($trans_list as $ts)
											{
												//--- ถ้าจำนวนที่เชื่อมโยงไว้ น้อยกว่า หรือ เท่ากับ จำนวนที่ตรวจได้ (ไม่เกินที่สั่งไป)
												//--- แสดงว่าได้ของครบตามที่ผูกไว้ ให้ใช้ตัวเลขที่ผูกไว้ได้เลย
												//--- แต่ถ้าได้จำนวนที่ผูกไว้มากกว่าที่ตรวจได้ แสดงว่า ได้สินค้าไม่ครบ ให้ใช้จำนวนที่ตรวจได้แทน
												$move_qty = $ts->order_qty <= $sold_qty ? $ts->order_qty : $sold_qty;

												if( $move_qty > 0)
												{
													//--- update ยอดเปิดบิลใน tbl_order_transform_detail field sold_qty
													if($this->transform_model->update_sold_qty($ts->id, $move_qty) === TRUE )
													{
														$sold_qty -= $move_qty;
													}
													else
													{
														$sc = FALSE;
														$this->error = 'ปรับปรุงยอดรายการค้างรับไม่สำเร็จ';
													}
												}
											}
										}
									}

									//--- if lend
									if($order->role == 'L')
									{
										$sold_qty = $rs->order_qty;

										$arr = array(
											'order_code' => $code,
											'product_code' => $rs->product_code,
											'product_name' => $rs->product_name,
											'qty' => $sold_qty,
											'customer_code' => $order->customer_code
										);

										if($this->lend_model->add_detail($arr) === FALSE)
										{
											$sc = FALSE;
											$this->error = 'เพิ่มรายการค้างรับไม่สำเร็จ';
										}
									}
								} //--- end foreach $bill
							} //--- end if empty($bill)

							//--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
							$buffer = $this->buffer_model->get_all_details($code);

							//--- ถ้ายังมีรายการที่ค้างอยู่ใน buffer เคลียร์เข้า cancle
							if( ! empty($buffer))
							{
								foreach($buffer as $rs)
								{
									if($this->buffer_model->delete($rs->id) === FALSE)
									{
										$sc = FALSE;
										$this->error = 'ลบ Buffer ที่ค้างอยู่ไม่สำเร็จ';
										break;
									}
								}
							}

							//--- update credit used
		          if($sc === TRUE && $order->role == 'S' && $order->is_term == 1)
		          {
		            $credit_balance = $this->customers_model->get_credit_balance($order->customer_code);

		            if( $use_credit && ($credit_balance < $docTotal))
		            {
		              $sc = FALSE;
		              $this->error = 'เครดิตคงเหลือไม่เพียงพอ';
		            }

		            if($sc === TRUE && $this->customers_model->update_used($order->customer_code, $docTotal))
		            {
		              $this->customers_model->update_balance($order->customer_code);
		            }
		          }


		          if($sc === TRUE && $order->role == 'P')
		          {
		            $bd = $this->sponsor_budget_model->get_valid_budget($order->budget_id);

		            if( ! empty($bd))
		            {
		              if($bd->balance < $docTotal)
		              {
		                $sc = FALSE;
		                $this->error = "งบประมาณคงเหลือไม่พอ <br/>คงเหลือ : ".number($bd->balance, 2);
		              }

		              if($sc === TRUE)
		              {
		                if( ! $this->sponsor_budget_model->update_used($bd->id, $docTotal))
		                {
		                  $sc = FALSE;
		                  $this->error = "Failed to update outstanding budget";
		                }
		              }
		            }
		            else
		            {
		              $sc = FALSE;
		              $this->error = "ไม่พบงบประมาณที่ใช้";
		            }
		          }


							if($sc === TRUE)
							{
								//--- set is_complete
								$this->orders_model->set_completed($code);

								//--- ถ้าเป็นออเดอร์แบบขาย และ เป็นเครดิต หรือ เป็น COD ให้ตั้งหนี้
								if($order->role === 'S')
								{
									$this->load->model('account/order_credit_model');

									$sold_amount = $this->invoice_model->get_total_sold_amount($code);

									if(!empty($sold_amount))
									{
										$dept_amount = $sold_amount;

										$customer = $this->customers_model->get($order->customer_code);

										$arr = array(
											'order_code' => $code,
											'customer_code' => $order->customer_code,
											'delivery_date' => date('Y-m-d'),
											'due_date' => added_date(date('Y-m-d'), $customer->credit_term),
											'over_due_date' => added_date(date('Y-m-d'), $customer->credit_term + getConfig('OVER_DUE_DATE')),
											'amount' => $dept_amount,
											'paid' => $order->deposit,
											'balance' => $dept_amount - $order->deposit
										);

										if($this->order_credit_model->is_exists($code))
										{
											$this->order_credit_model->update($code, $arr);
										}
										else
										{
											$this->order_credit_model->add($arr);
										}

										//--- recal balance
										$this->order_credit_model->recal_balance($code);
									}
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
						} //--- $sc == TRUE
					}
					else
					{
						$sc = FALSE;
						$this->error = "ออเดอร์เคยผ่านการจัดมาแล้วต้องจัดสินค้าต่อเท่านั้น";
					}
				} //--- end if state == 3
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่พบเลขที่เอกสาร {$code}";
			}
    }
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบเลขที่เอกสาร {$code}";
		}

    return $sc;
  }


	public function sold_order()
	{
		$sc = TRUE;

		$error = 0;
		$message = "";

		$orders = $this->input->post('orders');

		if( ! empty($orders))
		{
			if(empty(getConfig('DEFAULT_ZONE')))
			{
				$sc = FALSE;
				$this->error = "ไม่สามารถทำรายการได้เนื่องจากไม่พบการกำหนดค่า  'โซนขายเริ่มต้น' กรุณาตรวจสอบ";
			}
			else
			{
				foreach($orders as $code)
				{
					if( ! $this->confirm_order($code))
					{
						$error++;
						$message .= $this->error.' //';
					}
				}

				if($error > 0)
				{
					$sc = FALSE;
					$this->error = $message;
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}

}//--- end class


 ?>
