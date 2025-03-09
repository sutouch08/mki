<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare extends PS_Controller
{
  public $menu_code = 'ICODPR';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'จัดสินค้า';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/prepare';
    $this->load->model('inventory/prepare_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/buffer_model');
    $this->load->helper('order_helper');
  }


  public function index()
  {
    $this->load->helper('channels');
    $this->load->helper('saleman');

    $filter = array(
      'code' => get_filter('code', 'pp_code', ''),
      'customer' => get_filter('customer', 'pp_customer', ''),
      'user' => get_filter('user', 'pp_user', 'all'),
      'channels' => get_filter('channels', 'pp_channels', 'all'),
      'order_round' => get_filter('order_round', 'pp_order_round', 'all'),
      'shipping_round' => get_filter('shipping_round', 'pp_shipping_round', 'all'),
      'from_date' => get_filter('from_date', 'pp_from_date', ''),
      'to_date' => get_filter('to_date', 'pp_to_date', ''),
      'ship_from_date' => get_filter('ship_from_date', 'pp_ship_from_date', ''),
      'ship_to_date' => get_filter('ship_to_date', 'pp_ship_to_date', '')
    );

		$this->title = "ออเดอร์รอจัด";

		$perpage = get_rows();

		$segment  = 4; //-- url segment
		$rows = $this->prepare_model->count_rows($filter, 3);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders = $this->prepare_model->get_list($filter, $perpage, $this->uri->segment($segment), 3);

    $filter['orders'] = $orders;
		$filter['use_prepare'] = is_true(getConfig('USE_PREPARE'));

		$this->pagination->initialize($init);
    $this->load->view('inventory/prepare/prepare_list', $filter);
  }


  public function view_process()
  {
    $this->load->helper('channels');
    $this->load->helper('saleman');
    
    $filter = array(
      'code' => get_filter('code', 'pp_code', ''),
      'customer' => get_filter('customer', 'pp_customer', ''),
      'user' => get_filter('user', 'pp_user', 'all'),
      'channels' => get_filter('channels', 'pp_channels', 'all'),
      'order_round' => get_filter('order_round', 'pp_order_round', 'all'),
      'shipping_round' => get_filter('shipping_round', 'pp_shipping_round', 'all'),
      'from_date' => get_filter('from_date', 'pp_from_date', ''),
      'to_date' => get_filter('to_date', 'pp_to_date', ''),
      'ship_from_date' => get_filter('ship_from_date', 'pp_ship_from_date', ''),
      'ship_to_date' => get_filter('ship_to_date', 'pp_ship_to_date', '')
    );

		$this->title = "ออเดอร์กำลังจัด";
		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		$segment  = 4; //-- url segment
    $rows = $this->prepare_model->count_rows($filter, 4);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/view_process/', $rows, $perpage, $segment);
		$orders = $this->prepare_model->get_list($filter, $perpage, $this->uri->segment($segment), 4);

    $filter['orders'] = $orders;

    $this->pagination->initialize($init);
    $this->load->view('inventory/prepare/prepare_view_process', $filter);
  }



  public function process($code)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('masters/channels_model');
    $state = $this->orders_model->get_state($code);

    if($state == 3)
    {
      $arr = array(
        'state' => 4,
        'picked' => 2,
        'update_user' => $this->_user->uname
      );

      if($this->orders_model->update($code, $arr))
      {
        $arr = array(
          'order_code' => $code,
          'state' => 4,
          'update_user' => $this->_user->uname
        );

        $this->order_state_model->add_state($arr);
      }
    }

    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $order->channels_name = $this->channels_model->get_name($order->channels_code);

    $uncomplete = $this->orders_model->get_unvalid_details($code);

    if(!empty($uncomplete))
    {
      foreach($uncomplete as $rs)
      {
        $rs->barcode = $this->get_barcode($rs->product_code);
        $rs->prepared = $this->get_prepared($rs->order_code, $rs->product_code);
        $rs->stock_in_zone = $this->get_stock_in_zone($rs->product_code);
      }
    }

    $complete = $this->orders_model->get_valid_details($code);

    if(!empty($complete))
    {
      foreach($complete as $rs)
      {
        $rs->barcode = $this->get_barcode($rs->product_code);
        $rs->prepared = $rs->is_count == 1 ? $this->get_prepared($rs->order_code, $rs->product_code) : $rs->qty;
        $rs->from_zone = $this->get_prepared_from_zone($rs->order_code, $rs->product_code, $rs->is_count);
      }
    }

    $ds = array(
      'order' => $order,
      'uncomplete_details' => $uncomplete,
      'complete_details' => $complete
    );

    $this->load->view('inventory/prepare/prepare_process', $ds);
  }




  public function do_prepare()
  {
    $sc = TRUE;
    $valid = 0;
    if($this->input->post('order_code'))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/warehouse_model');
      $this->load->model('masters/zone_model');

      $order_code = $this->input->post('order_code');
      $zone_code  = $this->input->post('zone_code');
      $barcode    = $this->input->post('barcode');
      $qty        = $this->input->post('qty');

      $zone = $this->zone_model->get($zone_code);

      $state = $this->orders_model->get_state($order_code);
      //--- ตรวจสอบสถานะออเดอร์ 4 == กำลังจัดสินค้า
      if($state == 4)
      {
        $item = $this->products_model->get_product_by_barcode($barcode);
        //--- ตรวจสอบบาร์โค้ดที่ยิงมา
        if(!empty($item))
        {
          if($item->count_stock == 1)
          {
            $ds = $this->orders_model->get_order_detail($order_code, $item->code);
            if(!empty($ds))
            {
              //--- ดึงยอดที่จัดแล้ว
              $prepared = $this->get_prepared($ds->order_code, $ds->product_code);

              //--- ยอดคงเหลือค้างจัด
              $bQty = $ds->qty - $prepared;

              //---- ตรวจสอบยอดที่ยังไม่ครบว่าจัดเกินหรือเปล่า
              if( $bQty < $qty)
              {
                $sc = FALSE;
                $this->error = "สินค้าเกิน กรุณาคืนสินค้าแล้วจัดสินค้าใหม่อีกครั้ง";
              }
              else
              {
                $is_enough = $this->stock_model->is_enough($zone_code, $ds->product_code, $qty);
                $auz = getConfig('AlLOW_UNDER_ZERO') == 1 ? TRUE : $this->warehouse_model->is_auz($zone->warehouse_code);

                if( ! $is_enough && ! $auz)
                {
                  $sc = FALSE;
                  $this->error = "สินค้าไม่เพียงพอ กรุณากำหนดจำนวนสินค้าใหม่";
                }
                else
                {
                  $this->db->trans_begin();

                  if( ! $this->stock_model->update_stock_zone($zone_code, $ds->product_code, $qty * -1))
                  {
                    $sc = FALSE;
                    $this->error = "ตัดสต็อกไม่สำเร็จ";
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->prepare_model->update_buffer($ds->order_code, $ds->product_code, $zone_code, $qty))
                    {
                      $sc = FALSE;
                      $this->error = "เพิ่ม buffer ไม่สำเร็จ";
                    }
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->prepare_model->update_prepare($ds->order_code, $ds->product_code, $zone_code, $qty))
                    {
                      $sc = FALSE;
                      $this->error = "เพิ่ม prepare history ไม่สำเร็จ";
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

                  if($sc === TRUE)
                  {
                    $preparedQty = $this->get_prepared($ds->order_code, $ds->product_code);

                    if($preparedQty == $ds->qty)
                    {
                      $this->orders_model->valid_detail($ds->id);
                      $valid = 1;
                    }
                  }
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = 'สินค้าไม่ตรงกับออเดอร์';
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = 'สินค้าไม่นับสต็อก ไม่จำเป็นต้องจัดสินค้านี้';
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = 'บาร์โค้ดไม่ถูกต้อง กรุณาตรวจสอบ';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'สถานะออเดอร์ถูกเปลี่ยน ไม่สามารถจัดสินค้าต่อได้';
      }
    }

    echo $sc === TRUE ? json_encode(array("id" => $ds->id, "qty" => $qty, "valid" => $valid)) : $this->error;
  }


  public function get_barcode($item_code)
  {
    $this->load->model('masters/products_model');
    return $this->products_model->get_barcode($item_code);
  }


  public function get_prepared($order_code, $item_code)
  {
    return $this->prepare_model->get_prepared($order_code, $item_code);
  }


  public function get_prepared_from_zone($order_code, $item_code, $is_count)
  {
    if($is_count == 1)
    {
      $sc = 'ไม่พบข้อมูล';

      $buffer = $this->prepare_model->get_prepared_from_zone($order_code, $item_code);

      if(!empty($buffer))
      {
        $sc = '';
        foreach($buffer as $rs)
        {
          $sc .= '<span class="display-block font-size-12">';
          $sc .= $rs->name.' : '.number($rs->qty);
          $sc .= '<a href="#" id="buffer-'.$rs->id.'" onclick="removeBuffer('.$rs->id.')" ';
          $sc .= ' data-order="'.$order_code.'" data-item="'.$item_code.'" ';
          $sc .= ' data-zonecode="'.$rs->zone_code.'" data-zonename="'.$rs->name.'" data-qty="'.number($rs->qty).'">';
          $sc .= '<i class="fa fa-trash fa-lg red margin-left-10"></i></a>';
          $sc .= '</span>';
        }
      }
    }
    else
    {
      $sc = 'ไม่นับสต็อก';
    }

  	return $sc;
  }




  public function get_stock_in_zone($item_code)
  {
    $sc = "ไม่มีสินค้า";
    $stock = $this->stock_model->get_stock_in_zone($item_code);
    if(!empty($stock))
    {
      $sc = "";
      foreach($stock as $rs)
      {
        $sc .= $rs->name.' : '.number($rs->qty).'<br/>';
      }
    }

    return $sc;
  }



  public function set_zone_label($value)
  {
    $this->input->set_cookie(array('name' => 'showZone', 'value' => $value, 'expire' => 3600 , 'path' => '/'));
  }

  public function finish_prepare()
  {
    $sc = TRUE;
    $this->load->helper('order');
    $code = $this->input->post('order_code');
    $use_qc = is_true(getConfig('USE_QC'));

    $state = $this->orders_model->get_state($code);

    //---	ถ้าสถานะเป็นกำลังจัด (บางทีอาจมีการเปลี่ยนสถานะตอนเรากำลังจัดสินค้าอยู่)
    if( $state == 4)
    {
      $new_state = $use_qc ? 5 : 7;

      $this->db->trans_begin();

      //--- mark all detail as valid
      if( ! $this->orders_model->valid_all_details($code))
      {
        $sc = FALSE;
        $this->error = "Failed to set valid for order rows";
      }

      if($sc === TRUE)
      {
        $arr = array(
          'state' => $new_state,
          'picked' => 1,
          'update_user' => $this->_user->uname
        );

        if( ! $this->orders_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to change order state";
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'order_code' => $code,
          'state' => $new_state,
          'update_user' => $this->_user->uname
        );

        if( ! $this->order_state_model->add_state($arr))
        {
          $sc = FALSE;
          $this->error = "Failed to add state logs";
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

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function check_state()
  {
    $code = $this->input->get('order_code');
    $rs = $this->orders_model->get_state($code);
    echo $rs;
  }


  public function pull_order_back()
  {
    $code = $this->input->post('order_code');
    $state = $this->orders_model->get_state($code);
    if($state == 4)
    {
      $arr = array(
        'order_code' => $code,
        'state' => 3,
        'update_user' => get_cookie('uname')
      );

      $this->orders_model->change_state($code, 3);
      $this->order_state_model->add_state($arr);
    }

    echo 'success';
  }

  function remove_buffer()
  {
    $sc = TRUE;

    $order_code = $this->input->post('order_code');
    $item_code = $this->input->post('product_code');
    $zone_code = $this->input->post('zone_code');
    $buffer_id = $this->input->post('buffer_id');
    $buffer = $this->buffer_model->get($buffer_id);

    $detail_id = $this->orders_model->get_order_detail_id($order_code, $item_code);

    if( ! empty($detail_id))
    {
      $this->db->trans_begin();

      if( ! empty($buffer))
      {
        if( ! $this->buffer_model->remove_buffer($order_code, $item_code, $zone_code))
        {
          $sc = FALSE;
          $this->error = "Failed to delete buffer";
        }
        else
        {
          //--- roll back stock
          if( ! $this->stock_model->update_stock_zone($buffer->zone_code, $buffer->product_code, $buffer->qty))
          {
            $sc = FALSE;
            $this->error = "Failed to rollback stock zone";
          }
        }
      }

      if( $sc === TRUE)
      {
        if( ! $this->prepare_model->remove_prepare($order_code, $item_code, $zone_code))
        {
          $sc = FALSE;
          $this->error = "Failed to delete prepare logs";
        }
      }



      if($sc === TRUE)
      {
        if( ! $this->orders_model->unvalid_detail($detail_id) )
        {
          $sc = FALSE;
          $this->error = "Failed to rollback item status (unvalid)";
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
      $this->error = "ไม่พบรายการสินค้า";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


	public function sold_order()
	{
		$orders = $this->input->post('orders');

		if(!empty($orders))
		{
			foreach($orders as $order)
			{
				echo $order.'<br/>';
			}
		}
	}



  public function clear_filter()
  {
    $filter = array(
      'pp_code',
      'pp_customer',
      'pp_user',
      'pp_channels',
      'pp_order_round',
      'pp_shipping_round',
      'pp_from_date',
      'pp_to_date',
      'pp_ship_from_date',
      'pp_ship_to_date'
    );

    clear_filter($filter);
  }
} //--- end class
?>
