<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsor extends PS_Controller
{
  public $menu_code = 'SOODSP';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'สปอนเซอร์';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/sponsor';
    $this->load->model('orders/orders_model');
    $this->load->model('masters/sponsors_model');
    $this->load->model('masters/sponsor_budget_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');

    $this->load->helper('order');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('state');
    $this->load->helper('product_images');
    $this->load->helper('discount');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'sp_code', ''),
      'customer' => get_filter('customer', 'sp_customer', ''),
      'user' => get_filter('user', 'sp_user', 'all'),
      'from_date' => get_filter('fromDate', 'sp_fromDate', ''),
      'to_date' => get_filter('toDate', 'sp_toDate', ''),
      'notSave' => get_filter('notSave', 'notSave', NULL),
      'onlyMe' => get_filter('onlyMe', 'onlyMe', NULL),
      'isApprove' => get_filter('isApprove', 'sp_isApprove', 'all')
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

    $role     = 'P'; //--- P = sponsor;
		$segment  = 4; //-- url segment
		$rows     = $this->orders_model->count_rows($filter, $role);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->orders_model->get_data($filter, $perpage, $this->uri->segment($segment), $role);
    $ds       = array();
    if(!empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
        $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
        $rs->state_name    = get_state_name($rs->state);
        $ds[] = $rs;
      }
    }

    $filter['orders'] = $ds;
    $filter['state'] = $state;
    $filter['btn'] = $button;

		$this->pagination->initialize($init);
    $this->load->view('sponsor/sponsor_list', $filter);
  }


  public function get_budget()
  {
    $arr = array(
      'budget_id' => NULL,
      'amount_label' => 0.00,
      'amount' => 0.00
    );

    $sp = $this->sponsors_model->get_by_customer_code($this->input->get('code'));

    if( ! empty($sp))
    {
      if( ! empty($sp->budget_id))
      {
        $bd = $this->sponsor_budget_model->get_valid_budget($sp->budget_id);

        if( ! empty($bd))
        {
          $balance = $bd->balance;
          $commit = $this->sponsor_budget_model->get_commit_amount($bd->id);
          $amount = $balance - $commit;

          $arr['budget_id'] = $bd->id;
          $arr['amount'] = $amount > 0 ? $amount : 0;
          $arr['amount_label'] = $amount > 0 ? number($amount, 2) : 0;
        }
      }
    }

    echo json_encode($arr);
  }


  public function add_new()
  {
    $this->load->view('sponsor/sponsor_add');
  }


  public function add()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      if($this->pm->can_add)
      {
        $sp = $this->sponsors_model->get_by_customer_code($ds->customer_code);

        if( ! empty($sp) && $sp->active == 1)
        {
          $bd = $this->sponsor_budget_model->get_valid_budget($sp->budget_id);

          if( ! empty($bd))
          {
            $book_code = getConfig('BOOK_CODE_SPONSOR');
            $date_add = db_date($ds->date_add);
            $code = $this->get_new_code($date_add);
            $role = 'P'; //--- P = Sponsor

            if( ! empty($code))
            {
              $arr = array(
                'code' => $code,
                'role' => $role,
                'bookcode' => $book_code,
                'customer_code' => $ds->customer_code,
                'customer_name' => $ds->customer_name,
                'user' => $this->_user->uname,
                'budget_id' => $bd->id,
                'budget_code' => $bd->code,
                'remark' => get_null($ds->remark),
                'user_ref' => get_null($ds->emp_name)
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
                $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to generate document number";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบงบประมาณที่ใช้ได้ กรุณาตรวจสอบ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรายชื่ออภินันท์ {$ds->customer_code} กรุณาตรวจสอบ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('permission');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
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
    $this->load->model('approve_logs_model');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if(!empty($rs))
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);
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
    $ds['approve_logs'] = $this->approve_logs_model->get($code);
    $ds['details'] = $details;
    $ds['allowEditDisc'] = FALSE; //getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
    $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
    $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
    $this->load->view('sponsor/sponsor_edit', $ds);
  }



  public function update_order()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      if($this->pm->can_edit)
      {
        $sp = $this->sponsors_model->get_by_customer_code($ds->customer_code);

        if( ! empty($sp) && $sp->active == 1)
        {
          $bd = $this->sponsor_budget_model->get_valid_budget($sp->budget_id);

          if( ! empty($bd))
          {
            $order = $this->orders_model->get($ds->code);

            if($order->state == 1)
            {
              $arr = array(
                'customer_code' => $ds->customer_code,
                'customer_name' => $ds->customer_name,
                'user_ref' => $ds->emp_name,
                'remark' => get_null($ds->remark),
                'budget_id' => $bd->id,
                'budget_code' => $bd->code,
                'update_user' => $this->_user->uname
              );

              if( ! $this->orders_model->update($ds->code, $arr))
              {
                $sc = FALSE;
                $this->error = get_error_message('update');
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
            $this->error = "ไม่พบงบประมาณที่ใช้ได้";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "รายชื่อผู้รับอภินันท์ไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('permission');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
    }

    $this->response($sc);
  }



  public function edit_detail($code)
  {
    $this->load->helper('product_tab');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if($rs->state <= 3)
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $details = $this->orders_model->get_order_details($code);
      $ds['order'] = $rs;
      $ds['details'] = $details;
      $ds['allowEditDisc'] = FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('sponsor/sponsor_edit_detail', $ds);
    }
  }



  public function save($code)
  {
    $sc = TRUE;

    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      //---- check budget balance
      $amount = $this->orders_model->get_order_total_amount($code);

      $bd = $this->sponsor_budget_model->get_valid_budget($order->budget_id);

      if( ! empty($bd))
      {
        $commit = $this->sponsor_budget_model->get_commit_amount($order->budget_id, $order->code);

        $available = $bd->balance - $commit;

        if($available >= $amount)
        {
          $arr = array(
            'status' => 1,
            'is_approved' => 0
          );

          if( ! $this->orders_model->update($order->code, $arr))
          {
            $sc = FALSE;
            $this->error = "บันทึกออเดอร์ไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "งบคงเหลือไม่เพียงพอ <br/>Balance : ".number($bd->balance, 2)."<br/>Commited : ".number($commit, 2)."<br/>Available : ".number($available, 2);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบงบประมาณที่ใช้ได้";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document number";
    }

    $this->response($sc);
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_SPONSOR');
    $run_digit = getConfig('RUN_DIGIT_SPONSOR');
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

  public function clear_filter()
  {
    $filter = array(
      'sp_code',
      'sp_customer',
      'sp_user',
      'sp_fromDate',
      'sp_toDate',
      'notSave',
      'onlyMe',
      'sp_isApprove',
      'state_1',
      'state_2',
      'state_3',
      'state_4',
      'state_5',
      'state_6',
      'state_7',
      'state_8',
      'state_9',
    );

    clear_filter($filter);
  }
}
?>
