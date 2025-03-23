<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends PS_Controller
{
  public $menu_code = 'ICODIV';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'รายการเปิดบิลแล้ว';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/invoice';
    $this->load->model('inventory/invoice_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/customers_model');
    $this->load->model('inventory/delivery_order_model');
    $this->load->helper('order');
    $this->load->helper('saleman');
  }


  public function index()
  {
    $this->load->helper('channels');
    $this->load->helper('payment_method');
    $filter = array(
      'code'          => get_filter('code', 'code', ''),
			'invoice_code' => get_filter('invoice_code', 'invoice_code', ''),
      'customer'      => get_filter('customer', 'customer', ''),
      'role'          => get_filter('role', 'role', 'all'),
			'is_inv'        => get_filter('is_inv', 'is_inv', 'all'),
      'channels'      => get_filter('channels', 'channels', 'all'),
      'payment'       => get_filter('payment', 'payment', 'all'),
      'user' => get_filter('user', 'user', 'all'),
      'from_date'     => get_filter('from_date', 'from_date', ''),
      'to_date'       => get_filter('to_date', 'to_date', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->delivery_order_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->delivery_order_model->get_list($filter, $perpage, $this->uri->segment($segment), 8);


    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/order_closed/closed_list', $filter);
  }


  function export_filter()
  {
    $this->load->helper('channels');
    $this->load->helper('payment_method');

    $ds = json_decode($this->input->post('data'));
    $token = $this->input->post('token');

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('ออเดอร์เปิดบิลแล้ว');

    $excel = $this->excel->getActiveSheet();

    $row = 1;
    //--- set report title header
    $excel->setCellValue("A{$row}", "ออเดอร์เปิดบิลแล้ว");
    $excel->mergeCells("A{$row}:N{$row}");
    $row++;

    //---- filter
    $excel->setCellValue("A{$row}", "#");
    $excel->setCellValue("B{$row}", "วันที่จัดส่ง");
    $excel->setCellValue("C{$row}", "ตัดรอบออเดอร์");
    $excel->setCellValue("D{$row}", "รอบจัดส่ง");
    $excel->setCellValue("E{$row}", "วันที่");
    $excel->setCellValue("F{$row}", "เลขที่เอกสาร");
    $excel->setCellValue("G{$row}", "อ้างอิง[MKP]");
    $excel->setCellValue("H{$row}", "อ้างอิง[CRM]");
    $excel->setCellValue("I{$row}", "ใบกำกับ");
    $excel->setCellValue("J{$row}", "ลูกค้า/ผู้รับ/ผู้เบิก");
    $excel->setCellValue("K{$row}", "ยอดเงิน");
    $excel->setCellValue("L{$row}", "Tracking No");
    $excel->setCellValue("M{$row}", "ช่องทางการชำระเงิน");
    $excel->setCellValue("N{$row}", "ช่องทางขาย");
    $excel->setCellValue("O{$row}", "ผู้ดำเนินการ");
    $excel->setCellValue("P{$row}", "CSR");
    $row++;

    if( ! empty($ds))
    {
      $perpage = 10000;
      $offset = 0;

      $filter = array(
        'code' => $ds->code,
  			'invoice_code' => $ds->invoice_code,
        'customer' => $ds->customer,
        'role' => $ds->role,
  			'is_inv' => $ds->is_inv,
        'channels' => $ds->channels,
        'payment' => $ds->payment,
        'user' => $ds->user,
        'from_date' => $ds->from_date,
        'to_date' => $ds->to_date
      );

      $data = $this->delivery_order_model->get_list($filter, $perpage, $offset, 8);
    }

    if( ! empty($data))
    {
      $no = 1;
      $sa = saleman_array(); //-- saleman_helper
      $user = user_array(); //-- user_helper
      $payments = payment_array();
      $channels = channels_array();

      foreach($data as $rs)
      {
        $payment_name = empty($payments[$rs->payment_code]) ? NULL : $payments[$rs->payment_code];
        $channels_name = empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code];
      	$csr = empty($sa[$rs->sale_code]) ? NULL : $sa[$rs->sale_code];
      	$dname = empty($user[$rs->user]) ? NULL : $user[$rs->user];
        $customerName = $rs->customer_name . (empty($rs->customer_ref) ? "" : " [{$rs->customer_ref}]");


        $excel->setCellValue("A{$row}", $no);
        $excel->setCellValue("B{$row}", thai_date($rs->shipping_date, FALSE));
        $excel->setCellValue("C{$row}", $rs->order_round);
        $excel->setCellValue("D{$row}", $rs->shipping_round);
        $excel->setCellValue("E{$row}", thai_date($rs->date_add, FALSE));
        $excel->setCellValue("F{$row}", $rs->code);
        $excel->setCellValue("G{$row}", $rs->reference);
        $excel->setCellValue("H{$row}", $rs->reference2);
        $excel->setCellValue("I{$row}", $rs->invoice_code);
        $excel->setCellValue("J{$row}", $customerName);
        $excel->setCellValue("K{$row}", $rs->total_amount);
        $excel->setCellValue("L{$row}", $rs->shipping_code);
        $excel->setCellValue("M{$row}", $payment_name);
        $excel->setCellValue("N{$row}", $channels_name);
        $excel->setCellValue("O{$row}", $dname);
        $excel->setCellValue("P{$row}", $csr);
        $row++;
        $no++;
      }
    }
    else
    {
      $excel->setCellValue("A{$row}", "ไม่พบรายการตามเงื่อนไขที่กำหนด");
    }

    setToken($token);
    $file_name = "ออเดอร์เปิดบิลแล้ว.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }



  public function view_detail($code)
  {
    $this->load->model('inventory/qc_model');
    $this->load->helper('order');
    $this->load->helper('discount');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/sender_model');

    $use_qc = getConfig('USE_QC') == 1 ? TRUE : FALSE;
    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);

    if($order->role == 'C' OR $order->role == 'N')
    {
      $this->load->model('masters/zone_model');
      $order->zone_name = $this->zone_model->get_name($order->zone_code);
    }

    $order->channels_name = $this->channels_model->get_name($order->channels_code);
    $order->payment_name = $this->payment_methods_model->get_name($order->payment_code);
    $order->payment_role = $this->payment_methods_model->get_role($order->payment_code);
    $order->sender_name = $this->sender_model->get_name($order->sender_id);

    $details = $this->invoice_model->get_billed_detail($code, $order->picked, $use_qc);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        if($rs->is_count == 0)
        {
          $rs->line_total = $rs->order_qty * $rs->final_price;
          $rs->line_discount = $rs->order_qty * $rs->discount_amount;
          $rs->price_amount = $rs->order_qty * $rs->price;
        }
        else
        {
          $sell_qty = $rs->sold;
          $rs->line_total = $sell_qty * $rs->final_price;
          $rs->line_discount = $sell_qty * $rs->discount_amount;
          $rs->price_amount = $sell_qty * $rs->price;
        }
      }
    }

    $box_list = $use_qc ? $this->qc_model->get_box_list($code) : FALSE;

    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['box_list'] = $box_list;
    $ds['use_qc'] = $use_qc;
    $this->load->view('inventory/order_closed/closed_detail', $ds);
  }



  public function print_order($code, $barcode = '')
  {
    $this->load->model('masters/products_model');
		$this->load->model('address/customer_address_model');
    $this->load->library('printer');
    $order = $this->orders_model->get($code);
		$customer = $this->customers_model->get($order->customer_code);
		$customer_address = $this->customer_address_model->get_customer_bill_to_address($order->customer_code);
		$order->emp_name = $this->user_model->get_employee_name($order->user);

    $details = $this->invoice_model->get_details($code); //--- รายการที่มีการบันทึกขายไป
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
    $ds['title'] = "ใบส่งสินค้า";
    $ds['is_barcode'] = $barcode != '' ? TRUE : FALSE;
    $this->load->view('print/print_order', $ds);
  }


	public function print_order_no_price($code)
  {
    $this->load->model('masters/products_model');
		$this->load->model('address/customer_address_model');
    $this->load->library('printer');
    $order = $this->orders_model->get($code);
		$customer = $this->customers_model->get($order->customer_code);
		$customer_address = $this->customer_address_model->get_customer_bill_to_address($order->customer_code);
		$order->emp_name = $this->user_model->get_employee_name($order->user);

    $details = $this->invoice_model->get_details($code); //--- รายการที่มีการบันทึกขายไป
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
    $ds['title'] = "ใบส่งสินค้า";
    $this->load->view('print/print_order_no_price', $ds);
  }

  public function clear_filter()
  {
		$filter = array(
      'code' ,
			'invoice_code',
      'customer',
      'role',
      'channels',
      'payment',
      'user',
      'from_date',
      'to_date'
    );

    clear_filter($filter);
  }

} //--- end class
?>
