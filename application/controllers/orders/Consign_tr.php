<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consign_tr extends PS_Controller
{
  public $menu_code = 'SOCCTR';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ฝากขาย(โอนคลัง)';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/consign_tr';
    $this->load->model('orders/orders_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');

    $this->load->helper('order');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('state');
    $this->load->helper('product_images');
    $this->load->helper('discount');
    $this->load->helper('zone');
    $this->load->helper('saleman');

    $this->filter = getConfig('STOCK_FILTER');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'cs_code', ''),
      'customer' => get_filter('customer', 'cs_customer', ''),
      'user' => get_filter('user', 'cs_user', 'all'),
      'zone_code' => get_filter('zone', 'cs_zone', 'all'),
      'from_date' => get_filter('fromDate', 'cs_fromDate', ''),
      'to_date' => get_filter('toDate', 'cs_toDate', ''),
      'notSave' => get_filter('notSave', 'notSave', NULL),
      'onlyMe' => get_filter('onlyMe', 'onlyMe', NULL),
      'isApprove' => get_filter('isApprove', 'cs_isApprove', 'all')
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


    $filter['state_list'] = empty($state_list) ? NULL : $state_list;

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->orders_model->count_rows($filter, 'N');
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$filter['orders']  = $this->orders_model->get_data($filter, $perpage, $this->uri->segment($segment), 'N');
    $filter['state'] = $state;
    $filter['btn'] = $button;
		$this->pagination->initialize($init);
    $this->load->view('order_consign/consign_list', $filter);
  }

  public function add_new()
  {
    $this->load->view('order_consign/consign_add');
  }

  public function add()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $book_code = getConfig('BOOK_CODE_CONSIGN_TR');
      $role = 'N';
      $date_add = db_date($ds->date);
      $code = $this->get_new_code($date_add);
      $zone = $this->zone_model->get($ds->zone_code);
      $customer = $this->customers_model->get($ds->customer_code);

      if( ! empty($zone))
      {
        $arr = array(
          'code' => $code,
          'role' => $role,
          'bookcode' => $book_code,
          'customer_code' => $ds->customer_code,
          'customer_name' => $ds->customer_name,
          'gp' => empty($ds->gp) ? 0 : $ds->gp,
          'user' => $this->_user->uname,
          'sale_code' => empty($customer) ? NULL : $customer->sale_code,
          'shipping_date' => db_date($ds->shipping_date),
          'order_round' => get_null($ds->order_round),
          'shipping_round' => get_null($ds->shipping_round),
          'remark' => get_null($ds->remark),
          'zone_code' => $zone->code,
          'warehouse_code' => NULL
        );

        if($this->orders_model->add($arr))
        {
          $arr = array(
            'order_code' => $code,
            'state' => 1,
            'update_user' => $this->_user->uname
          );

          $this->order_state_model->add_state($arr);
        }
        else
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบโซนฝากขาย";
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


  public function edit_order($code)
  {
    $ds = array();
    $rs = $this->orders_model->get($code);

    if( ! empty($rs))
    {
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);
      $rs->zone_name = $this->zone_model->get_name($rs->zone_code);
    }

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

    $ds['state'] = $ost;
    $ds['order'] = $rs;
    $ds['details'] = $details;
    $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
    $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
    $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
    $this->load->view('order_consign/consign_edit', $ds);
  }


  public function edit_detail($code)
  {
    $this->load->helper('product_tab');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if($rs->state <= 3)
    {
      $rs->zone_name = $this->zone_model->get_name($rs->zone_code);
      $ds['order'] = $rs;

      $details = $this->orders_model->get_order_details($code);
      $ds['details'] = $details;
      $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('order_consign/consign_edit_detail', $ds);
    }
  }




  public function update_order()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $order = $this->orders_model->get($ds->code);

      if( ! empty($order))
      {
        $zone = $this->zone_model->get($ds->zone_code);
        $customer = $this->customers_model->get($ds->customer_code);

        if( ! empty($zone))
        {
          $arr = array(
            'date_add' => db_date($ds->date),
            'customer_code' => $ds->customer_code,
            'customer_name' => $ds->customer_name,
            'sale_code' => empty($customer) ? NULL : $customer->sale_code,
            'gp' => empty($ds->gp) ? 0 : $ds->gp,
            'zone_code' => $zone->code,
            'shipping_date' => db_date($ds->shipping_date),
            'order_round' => get_null($ds->order_round),
            'shipping_round' => get_null($ds->shipping_round),
            'remark' => get_null($ds->remark)
          );

          if( ! $this->orders_model->update($ds->code, $arr))
          {
            $sc = FALSE;
            $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid zone code";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
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
      //--- creadit used
      $credit_used = $this->orders_model->get_sum_not_complete_amount($order->customer_code);
      //--- credit balance from sap
      $credit_balance = $this->customers_model->get_credit($order->customer_code);

      if($credit_used > $credit_balance)
      {
        $diff = $credit_used - $credit_balance;
        $sc = FALSE;
        $message = 'เครดิตคงเหลือไม่พอ (ขาด : '.number($diff, 2).')';
      }
    }


    if($sc === TRUE)
    {
      $rs = $this->orders_model->set_status($code, 1);
      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'บันทึกออเดอร์ไม่สำเร็จ';
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGN_TR');
    $run_digit = getConfig('RUN_DIGIT_CONSIGN_TR');
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


  public function clear_filter()
  {
    $filter = array(
      'cs_code',
      'cs_customer',
      'cs_user',
      'cs_zone',
      'cs_fromDate',
      'cs_toDate',
      'cs_is_approve',
      'notSave',
      'onlyMe',
      'isExpire',
      'state_1',
      'state_2',
      'state_3',
      'state_4',
      'state_5',
      'state_6',
      'state_7',
      'state_8',
      'state_9'
    );

    clear_filter($filter);
  }
}
?>
