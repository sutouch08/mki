<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_invoice extends PS_Controller
{
	public $menu_code = 'SOODIV';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ใบส่งสินค้า/ใบกำกับภาษี';
  public $filter;

	public function __construct()
	{
		parent::__construct();
		$this->home = base_url().'orders/order_invoice';
		$this->load->model('orders/order_invoice_model');
		$this->load->model('masters/customers_model');
		$this->load->helper('address');
		$this->load->helper('vat');
		$this->title = getConfig('USE_VAT') == 1 ? 'ใบกำกับภาษี' : 'ใบส่งสินค้า';
	}


	public function index()
	{
		$filter = array(
			'code' => get_filter('code', 'invoice_code', ''),
			'order_code' => get_filter('order_code', 'invoice_reference', ''),
			'customer' => get_filter('customer', 'invoice_customer', ''),
			'from_date' => get_filter('from_date', 'invoice_from_date', ''),
			'to_date' => get_filter('to_date', 'invoice_to_date', ''),
			'status' => get_filter('status', 'invoice_status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->order_invoice_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->order_invoice_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['order'] = $orders;

		$this->pagination->initialize($init);

    $this->load->view('order_invoice/order_invoice_list', $filter);
	}


	public function add_new()
	{
		$this->load->view('order_invoice/order_invoice_add');
	}


	public function add()
	{
		$sc = TRUE;
		$customer_code = $this->input->post('customer_code');
		$customer = $this->customers_model->get($customer_code);
		$customer_name = $this->customers_model->get_bill_name($customer_code);
		$doc_date = db_date($this->input->post('doc_date'));

		if(!empty($customer))
		{
			$this->load->model('address/customer_address_model');

			$code = $this->get_new_code($doc_date);

			$arr = array(
				'code' => $code,
				'doc_date' => $doc_date,
				'vat_type' => $this->input->post('vat_type'),
				'customer_code' => $customer->code,
				'customer_name' => empty($customer_name) ? $customer->name : $customer_name,
				'tax_id' => get_null($customer->Tax_Id),
				'remark' => get_null(trim($this->input->post('remark'))),
				'uname' => $this->_user->uname
			);

			$address = $this->customer_address_model->get_customer_bill_to_address($customer->code);
			if(!empty($address))
			{
				$arr['branch_code'] = get_null($address->branch_code);
				$arr['branch_name'] = get_null($address->branch_name);
				$arr['address'] = get_null($address->address);
				$arr['sub_district'] = get_null($address->sub_district);
				$arr['district'] = get_null($address->district);
				$arr['province'] = get_null($address->province);
				$arr['postcode'] = get_null($address->postcode);
				$arr['phone'] = get_null($address->phone);
			}

			if(! $this->order_invoice_model->add($arr))
			{
				$sc = FALSE;
				$this->error = "เพิ่มเอกสารไม่สำเร็จ";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "รหัสลูกค้าไม่ถูกต้อง {$customer_code}";
		}

		if($sc === TRUE)
		{
			$ds = array(
				'status' => 'success',
				'code' => $code
			);
		}
		else
		{
			$ds = array(
				'status' => 'error',
				'message' => $this->error
			);
		}

		echo json_encode($ds);
	}


	public function edit($code)
	{
		$order = $this->order_invoice_model->get($code);

		if(!empty($order))
		{
			$details = $this->order_invoice_model->get_details($code);
			$reference = $this->order_invoice_model->get_all_reference($code);

			$ds = array(
				'order' => $order,
				'details' => $details,
				'reference' => $reference,
				'use_vat' => getConfig('USE_VAT') == 1 ? TRUE : FALSE
			);

			$this->load->view('order_invoice/order_invoice_edit', $ds);
		}
		else
		{
			$this->load->view('page_error');
		}
	}



	public function update()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$customer_code = $this->input->post('customer_code');

		if(!empty($code))
		{
			$customer = $this->customers_model->get($customer_code);
			if(!empty($customer))
			{
				$this->load->model('address/customer_address_model');

				$arr = array(
					'doc_date' => db_date($this->input->post('doc_date')),
					'vat_type' => trim($this->input->post('vat_type')),
					'customer_code' => $customer->code,
					'customer_name' => $customer->name,
					'tax_id' => $customer->Tax_Id,
					'remark' => get_null(trim($this->input->post('remark'))),
					'upd_user' => $this->_user->uname
				);

				$address = $this->customer_address_model->get_customer_bill_to_address($customer->code);
				if(!empty($address))
				{
					$arr['branch_code'] = get_null($address->branch_code);
					$arr['branch_name'] = get_null($address->branch_name);
					$arr['address'] = get_null($address->address);
					$arr['sub_district'] = get_null($address->sub_district);
					$arr['district'] = get_null($address->district);
					$arr['province'] = get_null($address->province);
					$arr['postcode'] = get_null($address->postcode);
					$arr['phone'] = get_null($address->phone);
				}

				if(!$this->order_invoice_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid customer code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		$this->response($sc);
	}



	public function cancle_invoice()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		if($this->pm->can_delete)
		{
			$order = $this->order_invoice_model->get($code);

			if(!empty($order))
			{

				$this->db->trans_begin();

				$arr = array(
					'status' => 2 //--- cancle
				);

				if(! $this->order_invoice_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "Update document status failed";
				}

				if(! $this->order_invoice_model->update_details_status($code, 2))
				{
					$sc = FALSE;
					$this->error = "Update details status failed";
				}

				if(!empty($order->reference))
				{
					$reference = explode(',', $order->reference);

					if(!empty($reference))
					{
						$this->load->model('orders/orders_model');

						foreach($reference as $order_code)
						{
							$arr = array(
								'invoice_code' => NULL
							);

							if(! $this->orders_model->update($order_code, $arr))
							{
								$sc = FALSE;
								$this->error = "Update order invoice reference failed";
							}

							if(! $this->order_invoice_model->update_order_sold($order_code, array('inv_code' => NULL)))
							{
								$sc = FALSE;
								$this->error = "Update order sold invoice reference failed";
							}
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
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document No";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing permission";
		}

		$this->response($sc);
	}


	public function view_detail($code)
	{
		$order = $this->order_invoice_model->get($code);

		if(!empty($order))
		{
			$details = $this->order_invoice_model->get_details($code);
			$reference = $this->order_invoice_model->get_all_reference($code);
			$address = array(
				'address' => $order->address,
				'sub_district' => $order->sub_district,
				'district' => $order->district,
				'province' => $order->province,
				'postcode' => $order->postcode,
				'phone' => $order->phone
			);

			$ds = array(
				'order' => $order,
				'details' => $details,
				'reference' => $reference,
				'address' => parse_address($address), //--- address_helper
				'use_vat' => getConfig('USE_VAT') == 1 ? TRUE : FALSE
			);

			$this->load->view('order_invoice/order_invoice_view_detail', $ds);
		}
		else
		{
			$this->load->view('page_error');
		}
	}

	public function print_selected_tax_invoice()
	{
		$invoice = $this->input->post('data');

		$invoices = explode(',', $invoice);

		if(!empty($invoices))
		{
			$this->load->library('printer');
			$this->title = "ใบแจ้งหนี้/ใบกำกับภาษี";
			$use_vat = getConfig('USE_VAT') ? TRUE : FALSE;

			$data = $this->order_invoice_model->get_invoice_list($invoices);

			if(!empty($data))
			{
				$ds = array();
				foreach($data as $order)
				{
					$details = $this->order_invoice_model->get_collapse_details($order->code);

					$sale = $this->customers_model->get_saleman($order->customer_code);

					$bill_name = $this->customers_model->get_bill_name($order->customer_code);

					if(!empty($bill_name))
					{
						$order->customer_name = $bill_name;
					}

					$address = array(
						'address' => $order->address,
						'sub_district' => $order->sub_district,
						'district' => $order->district,
						'province' => $order->province,
						'postcode' => $order->postcode
					);

					$inv = new stdClass();
					$inv->title = $this->title;
					$inv->order = $order;
					$inv->address = parse_address($address);
					$inv->details = $details;
					$inv->saleman = $sale;
					$inv->use_vat = $use_vat;

					$ds[] = $inv;
				} //--- end foreach

				$arr = array(
					'data' => $ds
				);
				$this->load->view('print/print_multiple_tax_invoice', $arr);
			}
		}
		else
		{
			$this->error_page();
		}
	}



	public function print_multiple_tax_invoice($gen_id)
	{
		$this->load->library('printer');
		$this->title = "ใบแจ้งหนี้/ใบกำกับภาษี";
		$use_vat = getConfig('USE_VAT') ? TRUE : FALSE;

		$data = $this->order_invoice_model->get_gen_invoice_list($gen_id);
		if(!empty($data))
		{
			$ds = array();
			foreach($data as $order)
			{
				$details = $this->order_invoice_model->get_collapse_details($order->code);

				$sale = $this->customers_model->get_saleman($order->customer_code);

				$bill_name = $this->customers_model->get_bill_name($order->customer_code);

				if(!empty($bill_name))
				{
					$order->customer_name = $bill_name;
				}

				$address = array(
					'address' => $order->address,
					'sub_district' => $order->sub_district,
					'district' => $order->district,
					'province' => $order->province,
					'postcode' => $order->postcode
				);

				$inv = new stdClass();
				$inv->title = $this->title;
				$inv->order = $order;
				$inv->address = parse_address($address);
				$inv->details = $details;
				$inv->saleman = $sale;
				$inv->use_vat = $use_vat;

				$ds[] = $inv;
			} //--- end foreach

			$arr = array(
				'data' => $ds
			);
			$this->load->view('print/print_multiple_tax_invoice', $arr);
		}
	}



	public function print_selected_do_invoice()
	{
		$do = $this->input->post('data');

		$do = explode(',', $do);

		if(!empty($do))
		{
			$this->load->library('printer');
			$this->title = "ใบส่งสินค้า/ใบแจ้งหนี้";
			$use_vat = getConfig('USE_VAT') ? TRUE : FALSE;

			$data = $this->order_invoice_model->get_invoice_list($do);

			if(!empty($data))
			{
				$ds = array();
				foreach($data as $order)
				{
					$details = $this->order_invoice_model->get_collapse_details($order->code);

					$sale = $this->customers_model->get_saleman($order->customer_code);
					$bill_name = $this->customers_model->get_bill_name($order->customer_code);

					if(!empty($bill_name))
					{
						$order->customer_name = $bill_name;
					}


					$address = array(
						'address' => $order->address,
						'sub_district' => $order->sub_district,
						'district' => $order->district,
						'province' => $order->province,
						'postcode' => $order->postcode
					);

					$inv = new stdClass();
					$inv->title = $this->title;
					$inv->order = $order;
					$inv->address = parse_address($address);
					$inv->details = $details;
					$inv->saleman = $sale;
					$inv->use_vat = $use_vat;

					$ds[] = $inv;
				} //--- end foreach

				$arr = array(
					'data' => $ds
				);
				$this->load->view('print/print_multiple_tax_invoice', $arr);
			}
		}
	}


	public function print_multiple_do_invoice($gen_id)
	{
		$this->load->library('printer');
		$this->title = "ใบส่งสินค้า/ใบแจ้งหนี้";
		$use_vat = getConfig('USE_VAT') ? TRUE : FALSE;

		$data = $this->order_invoice_model->get_gen_invoice_list($gen_id);

		if(!empty($data))
		{
			$ds = array();
			foreach($data as $order)
			{
				$details = $this->order_invoice_model->get_collapse_details($order->code);

				$sale = $this->customers_model->get_saleman($order->customer_code);

				$bill_name = $this->customers_model->get_bill_name($order->customer_code);

				if(!empty($bill_name))
				{
					$order->customer_name = $bill_name;
				}

				$address = array(
					'address' => $order->address,
					'sub_district' => $order->sub_district,
					'district' => $order->district,
					'province' => $order->province,
					'postcode' => $order->postcode
				);

				$inv = new stdClass();
				$inv->title = $this->title;
				$inv->order = $order;
				$inv->address = parse_address($address);
				$inv->details = $details;
				$inv->saleman = $sale;
				$inv->use_vat = $use_vat;

				$ds[] = $inv;
			} //--- end foreach

			$arr = array(
				'data' => $ds
			);
			$this->load->view('print/print_multiple_tax_invoice', $arr);
		}
	}



	public function print_tax_receipt($code)
	{
		$this->title = "ใบเสร็จ/ใบกำกับภาษี";
		$this->print_receipt($code);
	}


	public function print_tax_invoice($code)
	{
		$this->title = "ใบแจ้งหนี้/ใบกำกับภาษี";
		$this->print_invoice($code);
	}

	public function print_tax_billing_note($code)
	{
		$this->title = "ใบวางบิล/ใบแจ้งหนี้";
		$this->print_billing_note($code);
	}

	public function print_do_receipt($code)
	{
		$this->title = "ใบส่งสินค้า/ใบเสร็จรับเงิน";
		$this->print_receipt($code);
	}

	public function print_do_invoice($code)
	{
		$this->title = "ใบส่งสินค้า/ใบแจ้งหนี้";
		$this->print_invoice($code);
	}



	public function print_invoice($code)
	{
		$order = $this->order_invoice_model->get($code);
		if(!empty($order))
		{
			$this->load->library('printer');

			//$details = $this->order_invoice_model->get_details($code);
			$details = $this->order_invoice_model->get_collapse_details($code); //--- ดึงราย โดยรวมยอดรายการที่ รหัสสินค้าเดียวกัน ราคาเท่ากัน ส่วนลดเท่ากันให้เป็นรายการเดียว
			$sale = $this->customers_model->get_saleman($order->customer_code);
			$bill_name = $this->customers_model->get_bill_name($order->customer_code);

			if(!empty($bill_name))
			{
				$order->customer_name = $bill_name;
			}

			$address = array(
				'address' => $order->address,
				'sub_district' => $order->sub_district,
				'district' => $order->district,
				'province' => $order->province,
				'postcode' => $order->postcode
			);

			$ds = array(
				'title' => $this->title,
				'order' => $order,
				'address' => parse_address($address), //--- address_helper
				'details' => $details,
				'saleman' => $sale,
				'use_vat' => getConfig('USE_VAT') ? TRUE : FALSE
			);

			$this->load->view('print/print_tax_invoice', $ds);
		}
	}


	public function print_receipt($code)
	{
		$order = $this->order_invoice_model->get($code);
		if(!empty($order))
		{
			$this->load->library('printer');

			//$details = $this->order_invoice_model->get_details($code);
			$details = $this->order_invoice_model->get_collapse_details($code); //--- ดึงราย โดยรวมยอดรายการที่ รหัสสินค้าเดียวกัน ราคาเท่ากัน ส่วนลดเท่ากันให้เป็นรายการเดียว
			$sale = $this->customers_model->get_saleman($order->customer_code);
			$bill_name = $this->customers_model->get_bill_name($order->customer_code);

			if(!empty($bill_name))
			{
				$order->customer_name = $bill_name;
			}

			$address = array(
				'address' => $order->address,
				'sub_district' => $order->sub_district,
				'district' => $order->district,
				'province' => $order->province,
				'postcode' => $order->postcode
			);

			$ds = array(
				'title' => $this->title,
				'order' => $order,
				'address' => parse_address($address), //--- address_helper
				'details' => $details,
				'saleman' => $sale,
				'use_vat' => getConfig('USE_VAT') ? TRUE : FALSE
			);

			$this->load->view('print/print_tax_receipt', $ds);
		}
	}


	public function print_billing_note($code)
	{
		$order = $this->order_invoice_model->get($code);

		if(!empty($order))
		{
			$this->load->library('printer');

			//$details = $this->order_invoice_model->get_details($code);
			$details = $this->order_invoice_model->get_collapse_details($code); //--- ดึงราย โดยรวมยอดรายการที่ รหัสสินค้าเดียวกัน ราคาเท่ากัน ส่วนลดเท่ากันให้เป็นรายการเดียว
			$sale = $this->customers_model->get_saleman($order->customer_code);
			$bill_name = $this->customers_model->get_bill_name($order->customer_code);

			if(!empty($bill_name))
			{
				$order->customer_name = $bill_name;
			}

			$address = array(
				'address' => $order->address,
				'sub_district' => $order->sub_district,
				'district' => $order->district,
				'province' => $order->province,
				'postcode' => $order->postcode
			);

			$ds = array(
				'title' => $this->title,
				'order' => $order,
				'address' => parse_address($address), //--- address_helper
				'details' => $details,
				'saleman' => $sale,
				'use_vat' => getConfig('USE_VAT') ? TRUE : FALSE
			);

			$this->load->view('print/print_billing_note', $ds);
		}
	}



	public function save()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		if(!empty($code))
		{
			$order = $this->order_invoice_model->get($code);
			if(!empty($order))
			{
				$reference = $this->order_invoice_model->get_all_reference($code);
				$order_code = "";

				if(!empty($reference))
				{
					$i = 1;
					foreach($reference as $rs)
					{
						$order_code .= $i === 1 ? $rs->order_code : ", {$rs->order_code}";
						$i++;
					}
				}

				$ds = $this->order_invoice_model->get_total_amount_and_vat_amount($code);


				$arr = array(
					'reference' => get_null($order_code),
					'total_amount' => (!empty($ds) ? $ds->total_amount : 0.00),
					'vat_amount' => (!empty($ds) ? $ds->total_vat_amount : 0.00),
					'status' => 1
				);

				$this->db->trans_begin();

				if(! $this->order_invoice_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "Update Failed";
				}

				//---- set details status to 1 (saved)
				if(! $this->order_invoice_model->update_details_status($code, 1))
				{
					$sc = FALSE;
					$this->error = "Update details status failed";
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
				$this->error = "Invalid code : {$code}";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		$this->response($sc);
	}




	public function add_to_order()
	{
		$this->load->model('orders/orders_model');

		$sc = TRUE;

		$code = trim($this->input->post('code'));
		$list = $this->input->post('order_list');

		$order = $this->order_invoice_model->get($code);
		if(!empty($order))
		{
			if(!empty($list))
			{
				foreach($list as $order_code)
				{
					$details = $this->order_invoice_model->get_billed_details($order_code);
					if(!empty($details))
					{
						foreach($details as $rs)
						{
							//--- use id from order_sold to check duplicate item
							$is_exists = $this->order_invoice_model->is_exists_detail($rs->reference, $rs->product_code);

							if(! $is_exists)
							{
								$discount_label = $rs->discount_label;
								//--- recal discount
								if($rs->avgBillDiscAmount > 0 && $rs->discount_amount > 0)
								{
									$price = $rs->price * $rs->qty;
									$discount = $price == 0 ? 0 : ($rs->discount_amount/$price) * 100;
									$discount_label = $discount === 0 ? 0 : round($discount,2).'%';
								}


								$arr = array(
									'invoice_code' => $code,
									'order_code' => $rs->reference,
									'product_code' => $rs->product_code,
									'product_name' => $rs->product_name,
									'qty' => $rs->qty,
									'price' => $rs->price,
									'unit_code' => $rs->unit_code,
									'unit_name' => $rs->unit_name,
									'vat_code' => $rs->vat_code,
									'vat_rate' => $rs->vat_rate,
									'discount_label' => $discount_label,
									'discount_amount' => $rs->discount_amount,
									'amount' => $rs->total_amount,
									'vat_amount' => $rs->vat_amount
								);

								$this->order_invoice_model->add_detail($arr);
							}
						}
					}

					$ds = array(
						'invoice_code' => $code
					);

					$this->orders_model->update($order_code, $ds);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : order list";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เลขที่เอกสารไม่ถูกต้อง";
		}

		$this->response($sc);
	}



	public function remove_reference_detail()
	{
		$this->load->model('orders/orders_model');

		$sc = TRUE;

		$code = trim($this->input->post('code'));
		$reference = trim($this->input->post('reference'));

		if(!empty($code) && !empty($reference))
		{
			if(! $this->order_invoice_model->remove_reference_detail($code, $reference))
			{
				$sc = FALSE;
				$this->error = "ลบรายการไม่สำเร็จ";
			}
			else
			{
				$arr = array(
					'invoice_code' => NULL
				);

				$this->orders_model->update($reference, $arr);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$this->response($sc);
	}




	public function get_order_list()
	{
		$customer_code = trim($this->input->get('customer_code'));
		$orders = $this->order_invoice_model->get_non_invoice_list_by_customer($customer_code);

		$ds = array();
		if(!empty($orders))
		{
			foreach($orders as $rs)
			{
				$arr = array(
					'orderCode' => $rs->code,
					'amount' => number($rs->total_amount, 2)
				);

				array_push($ds, $arr);
			}
		}
		else
		{
			$arr = array(
				"nodata" => "no data"
			);

			array_push($ds, $arr);
		}

		echo json_encode($ds);
	}




	public function create_invoice($order_code)
	{
		$this->load->model('orders/orders_model');
		$this->load->model('address/customer_address_model');
		$sc = TRUE;

		if(!empty($order_code))
		{
			$order = $this->orders_model->get($order_code);

			if(!empty($order))
			{
				if($order->state == 8)
				{
					if(empty($order->invoice_code))
					{
						$customer = $this->customers_model->get($order->customer_code);
						$doc_date = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : date('Y-m-d');
						$code = $this->get_new_code($doc_date);
						$total_amount = 0;
						$total_vat = 0;

						$arr = array(
							'code' => $code,
							'doc_date' => $doc_date,
							'vat_type' => 'I',
							'customer_code' => $customer->code,
							'customer_name' => $customer->name,
							'tax_id' => get_null($customer->Tax_Id),
							'remark' => NULL,
							'uname' => $this->_user->uname,
							'reference' => $order_code,
							'status' => 1
						);

						$address = $this->customer_address_model->get_customer_bill_to_address($customer->code);

						if(!empty($address))
						{
							$arr['branch_code'] = get_null($address->branch_code);
							$arr['branch_name'] = get_null($address->branch_name);
							$arr['address'] = get_null($address->address);
							$arr['sub_district'] = get_null($address->sub_district);
							$arr['district'] = get_null($address->district);
							$arr['province'] = get_null($address->province);
							$arr['postcode'] = get_null($address->postcode);
							$arr['phone'] = get_null($address->phone);
						}

						$this->db->trans_begin();

						if($this->order_invoice_model->add($arr))
						{
							//----- get details
							$details = $this->order_invoice_model->get_billed_details($order_code);

							if(!empty($details))
							{
								foreach($details as $rs)
								{
									if($sc === FALSE)
									{
										break;
									}

									//--- check duplicate item
									$is_exists = $this->order_invoice_model->is_exists_detail($rs->reference, $rs->product_code);

									if(! $is_exists)
									{
										$discount_label = $rs->discount_label;
										//--- recal discount
										if($rs->avgBillDiscAmount > 0 && $rs->discount_amount > 0)
										{
											$price = $rs->price * $rs->qty;
											$discount = $price == 0 ? 0 : ($rs->discount_amount/$price) * 100;
											$discount_label = $discount === 0 ? 0 : round($discount,2).'%';
										}


										$arr = array(
											'invoice_code' => $code,
											'order_code' => $rs->reference,
											'product_code' => $rs->product_code,
											'product_name' => $rs->product_name,
											'qty' => $rs->qty,
											'price' => $rs->price,
											'unit_code' => $rs->unit_code,
											'unit_name' => $rs->unit_name,
											'vat_code' => $rs->vat_code,
											'vat_rate' => $rs->vat_rate,
											'discount_label' => $discount_label,
											'discount_amount' => $rs->discount_amount,
											'amount' => $rs->total_amount,
											'vat_amount' => $rs->vat_amount,
											'status' => 1
										);

										if(! $this->order_invoice_model->add_detail($arr))
										{
											$sc = FALSE;
											$this->error = "Insert detail failed";
										}

										if($sc === TRUE)
										{
											$total_amount += $rs->total_amount;
											$total_vat += $rs->vat_amount;
										}
									} //--- end if exists
								} //--- end foreach
							} //--- end if empty details

							$arr = array(
								'total_amount' => $total_amount,
								'vat_amount' => $total_vat
							);

							if(! $this->order_invoice_model->update($code, $arr))
							{
								$sc = FALSE;
								$this->error = "Update DocTotal failed";
							}


							if(! $this->orders_model->update($order_code, array('invoice_code' => $code)))
							{
								$sc = FALSE;
								$this->error = "Update order invoice reference failed";
							}

							if(! $this->order_invoice_model->update_order_sold($order_code, array('inv_code' => $code)))
							{
								$sc = FALSE;
								$this->error = "Update order sold invoice reference failed";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Create Document failed";
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
					$this->error = "Invalid order state";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Order code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: code";
		}

		if($sc === TRUE)
		{
			$result = array(
				'status' => 'success',
				'code' => empty($code) ? '' : $code
			);
		}
		else
		{
			$result = array(
				'status' => 'failed',
				'error' => $this->error
			);
		}

		return $result;
	}



	public function gen_new_invoice()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			$orders = $this->input->post('orders');

			if(!empty($orders))
			{
				$count = 0;
				$success = array();
				$failed = array();

				foreach($orders as $order_code)
				{
					$rs = $this->create_invoice($order_code);

					if(!empty($rs))
					{
						$count++;

						if($rs['status'] === 'success')
						{
							$success[] = $rs['code'];
						}
						else
						{
							$arr = array(
								'code' => $order_code,
								'error_message' => $rs['error']
							);

							array_push($failed, $arr);
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Server Error";
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		if($sc === TRUE)
		{
			$arr = array(
				'success' => array(
					'count' => count($success),
					'code' => $success
				),
				'failed' => array(
					'count' => count($failed),
					'order' => $failed
				)
			);

			echo json_encode($arr);
		}
		else
		{
			echo $this->error;
		}
	}




	//--- create invoice multi order group by customer code
	public function create_multi_order_invoice()
	{
		$this->load->model('orders/orders_model');
		$this->load->model('address/customer_address_model');

		$sc = TRUE;

		$data = $this->input->post('data');

		$ds = array();

		$gen_id = uniqid();

		if(!empty($data))
		{
			foreach($data as $customer_code => $order)
			{

				//--- get sold data by customer_code
				$details = $this->order_invoice_model->get_sold_data_by_order($customer_code, $order);

				if(!empty($details))
				{
					$reference = "";

					//--- create new invoice
					$customer = $this->customers_model->get($customer_code);
					$doc_date = date('Y-m-d');
					$code = $this->get_new_code($doc_date);
					$total_amount = 0;
					$total_vat = 0;

					if(!empty($order))
					{
						$i = 1;
						foreach($order as $ref)
						{
							if($sc === FALSE)
							{
								break;
							}

							if( ! $this->order_invoice_model->is_exists_order($ref))
							{
								$reference .= $i == 1 ? $ref : ", ".$ref;
								$i++;
							}
							else
							{
								$sc = FALSE;
								$this->error = "{$ref} เคยเปิดใบกำกับไปแล้ว";
							}
						}
					}

					if($sc === TRUE)
					{
						$arr = array(
							'code' => $code,
							'doc_date' => $doc_date,
							'vat_type' => 'I',
							'customer_code' => $customer->code,
							'customer_name' => $customer->name,
							'tax_id' => get_null($customer->Tax_Id),
							'remark' => NULL,
							'uname' => $this->_user->uname,
							'reference' => $reference,
							'status' => 1,
							'gen_id' => $gen_id
						);

						$address = $this->customer_address_model->get_customer_bill_to_address($customer->code);

						if(!empty($address))
						{
							$arr['branch_code'] = get_null($address->branch_code);
							$arr['branch_name'] = get_null($address->branch_name);
							$arr['address'] = get_null($address->address);
							$arr['sub_district'] = get_null($address->sub_district);
							$arr['district'] = get_null($address->district);
							$arr['province'] = get_null($address->province);
							$arr['postcode'] = get_null($address->postcode);
							$arr['phone'] = get_null($address->phone);
						}

						$this->db->trans_begin();

						if($this->order_invoice_model->add($arr))
						{
							foreach($details as $rs)
							{
								//--- check duplicate item
								$is_exists = $this->order_invoice_model->is_exists_detail($rs->reference, $rs->product_code);

								if(! $is_exists)
								{
									$discount_label = $rs->discount_label;
									//--- recal discount for bill discount
									if($rs->avgBillDiscAmount > 0 && $rs->discount_amount > 0)
									{
										$price = $rs->price * $rs->qty;
										$discount = $price == 0 ? 0 : ($rs->discount_amount/$price) * 100;
										$discount_label = $discount === 0 ? 0 : round($discount,2).'%';
									}


									$arr = array(
									'invoice_code' => $code,
									'order_code' => $rs->reference,
									'product_code' => $rs->product_code,
									'product_name' => $rs->product_name,
									'qty' => $rs->qty,
									'price' => $rs->price,
									'unit_code' => $rs->unit_code,
									'unit_name' => $rs->unit_name,
									'vat_code' => $rs->vat_code,
									'vat_rate' => $rs->vat_rate,
									'discount_label' => $discount_label,
									'discount_amount' => $rs->discount_amount,
									'amount' => $rs->total_amount,
									'vat_amount' => $rs->vat_amount,
									'status' => 1
									);

									if(! $this->order_invoice_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Insert detail failed";
									}

									if($sc === TRUE)
									{
										$total_amount += $rs->total_amount;
										$total_vat += $rs->vat_amount;
									}
								} //--- end if exists

							} //--- end foreach details

							$arr = array(
							'total_amount' => $total_amount,
							'vat_amount' => $total_vat
							);

							if($sc === TRUE)
							{
								if(! $this->order_invoice_model->update($code, $arr))
								{
									$sc = FALSE;
									$this->error = "Update DocTotal failed";
								}
							}


							if($sc === TRUE)
							{
								foreach($order as $order_code)
								{
									if(! $this->orders_model->update($order_code, array('invoice_code' => $code)))
									{
										$sc = FALSE;
										$this->error = "Update order invoice reference failed";
									}

									if(! $this->order_invoice_model->update_order_sold($order_code, array('inv_code' => $code)))
									{
										$sc = FALSE;
										$this->error = "Update order sold invoice reference failed";
									}
								}
							}

						}
						else
						{
							$sc = FALSE;
							$this->error = "Create Document failed";
						}


						if($sc === TRUE)
						{
							$this->db->trans_commit();

							$ds[] = $code;
						}
						else
						{
							$this->db->trans_rollback();
						}
					}
				} //--- !empty sold data
			} //--- end foreach
		}
		else
		{
			$sc = FALSE;
			$this->error = "No order found";
		}

		if($sc === TRUE)
		{
			$arr = array(
				'status' => 'success',
				'gen_id' => $gen_id
			);

			echo json_encode($arr);
		}
		else
		{
			echo $this->error;
		}
	}



	//--- create invoice multi order group by customer code
	public function create_each_order_invoice()
	{
		$this->load->model('orders/orders_model');
		$this->load->model('address/customer_address_model');

		$sc = TRUE;

		$data = $this->input->post('data');

		$ds = array();

		$gen_id = uniqid();

		if(!empty($data))
		{
			$this->db->trans_begin();

			foreach($data as $order_code)
			{
				$exists = $this->order_invoice_model->is_exists_order($order_code);

				if(! $exists)
				{
					$order = $this->orders_model->get($order_code);

					if(!empty($order))
					{
						if($order->state == 8) //--- เปิดบิลแล้ว/จัดส่งแล้ว
						{
							$details = $this->order_invoice_model->get_billed_details($order->code);

							if(!empty($details))
							{
								$customer = $this->customers_model->get($order->customer_code);
								$doc_date = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : date('Y-m-d');
								$code = $this->get_new_code($doc_date);
								$total_amount = 0;
								$total_vat = 0;

								$arr = array(
									'code' => $code,
									'doc_date' => $doc_date,
									'vat_type' => 'I',
									'customer_code' => $customer->code,
									'customer_name' => $customer->name,
									'tax_id' => get_null($customer->Tax_Id),
									'remark' => NULL,
									'uname' => $this->_user->uname,
									'reference' => $order->code,
									'status' => 1,
									'gen_id' => $gen_id
								);

								$address = $this->customer_address_model->get_customer_bill_to_address($customer->code);

								if(!empty($address))
								{
									$arr['branch_code'] = get_null($address->branch_code);
									$arr['branch_name'] = get_null($address->branch_name);
									$arr['address'] = get_null($address->address);
									$arr['sub_district'] = get_null($address->sub_district);
									$arr['district'] = get_null($address->district);
									$arr['province'] = get_null($address->province);
									$arr['postcode'] = get_null($address->postcode);
									$arr['phone'] = get_null($address->phone);
								}



								if($this->order_invoice_model->add($arr))
								{
									foreach($details as $rs)
									{
										//--- check duplicate item
										$is_exists = $this->order_invoice_model->is_exists_detail($rs->reference, $rs->product_code);

										if(! $is_exists)
										{
											$discount_label = $rs->discount_label;
											//--- recal discount for bill discount
											if($rs->avgBillDiscAmount > 0 && $rs->discount_amount > 0)
											{
												$price = $rs->price * $rs->qty;
												$discount = $price == 0 ? 0 : ($rs->discount_amount/$price) * 100;
												$discount_label = $discount === 0 ? 0 : round($discount,2).'%';
											}


											$arr = array(
											'invoice_code' => $code,
											'order_code' => $rs->reference,
											'product_code' => $rs->product_code,
											'product_name' => $rs->product_name,
											'qty' => $rs->qty,
											'price' => $rs->price,
											'unit_code' => $rs->unit_code,
											'unit_name' => $rs->unit_name,
											'vat_code' => $rs->vat_code,
											'vat_rate' => $rs->vat_rate,
											'discount_label' => $discount_label,
											'discount_amount' => $rs->discount_amount,
											'amount' => $rs->total_amount,
											'vat_amount' => $rs->vat_amount,
											'status' => 1
											);

											if(! $this->order_invoice_model->add_detail($arr))
											{
												$sc = FALSE;
												$this->error = "Insert detail failed";
											}

											if($sc === TRUE)
											{
												$total_amount += $rs->total_amount;
												$total_vat += $rs->vat_amount;
											}
										} //--- end if exists

									} //--- end foreach details

									$arr = array(
									'total_amount' => $total_amount,
									'vat_amount' => $total_vat
									);

									if($sc === TRUE)
									{
										if(! $this->order_invoice_model->update($code, $arr))
										{
											$sc = FALSE;
											$this->error = "Update DocTotal failed : {$code}";
										}
									}


									if($sc === TRUE)
									{
										if(! $this->orders_model->update($order_code, array('invoice_code' => $code)))
										{
											$sc = FALSE;
											$this->error = "Update order invoice reference failed : {$code}";
										}


										if(! $this->order_invoice_model->update_order_sold($order_code, array('inv_code' => $code)))
										{
											$sc = FALSE;
											$this->error = "Update order sold invoice reference failed";
										}
									}

								}
								else
								{
									$sc = FALSE;
									$this->error = "Create Document failed : {$order_code}";
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
								$this->error = "ไม่พบข้อมูลการขาย";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Invalid Order Status";
						}
					}
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
			$this->error = "No order found";
		}

		if($sc === TRUE)
		{
			$arr = array(
				'status' => 'success',
				'gen_id' => $gen_id
			);

			echo json_encode($arr);
		}
		else
		{
			echo $this->error;
		}
	}




	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_INVOICE');
    $run_digit = getConfig('RUN_DIGIT_INVOICE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_invoice_model->get_max_code($pre);
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
			'invoice_code',
			'invoice_reference',
			'invoice_customer',
			'invoice_from_date',
			'invoice_to_date',
			'invoice_status'
		);

		clear_filter($filter);

		echo 'done';
	}


} //--- end class

?>
