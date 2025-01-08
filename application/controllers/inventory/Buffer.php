<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buffer extends PS_Controller
{
  public $menu_code = 'ICCKBF';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ BUFFER';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/buffer';
    $this->load->model('inventory/buffer_model');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'pd_code' => get_filter('pd_code', 'pd_code'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->buffer_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->buffer_model->get_data($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('inventory/buffer/buffer_view', $filter);
  }


  public function remove_select_buffer()
  {
    $sc = TRUE;
    $this->error = "";

    $this->load->model('inventory/prepare_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/orders_model');

    $buffer = $this->input->post('buffer');

    if( ! empty($buffer))
    {
      foreach($buffer as $id)
      {
        $row = $this->buffer_model->get($id);

        if( ! empty($row))
        {
          $this->db->trans_begin();

          $cs = TRUE;

          if( $this->buffer_model->delete($id))
          {
            //--- roll back stock
            if( ! $this->stock_model->update_stock_zone($row->zone_code, $row->product_code, $row->qty))
            {
              $cs = FALSE;
              $this->error .= "คืนสต็อกกลับเข้าโซนไม่สำเร็จ - {$row->order_code} : {$row->product_code} : {$row->qty} <br/>";
            }
            else
            {
              if( ! $this->prepare_model->remove_prepare($row->order_code, $row->product_code, $row->zone_code))
              {
                $cs = FALSE;
                $this->error .= "ลบข้อมูลการจัดไม่สำเร็จ - {$row->order_code} : {$row->product_code} : {$row->qty} <br/>";
              }
            }
          }

          if($cs === TRUE)
          {
            $this->db->trans_commit();

            $detail_id = $this->orders_model->get_order_detail_id($row->order_code, $row->product_code);

            if( ! empty($detail_id))
            {
              $this->orders_model->unvalid_detail($detail_id);
            }
          }
          else
          {
            $sc = FALSE;
            $this->db->trans_rollback();
          }
        }
      } //--- end foreach
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $this->response($sc);
  }


  function clear_filter(){
    $filter = array('order_code', 'pd_code', 'zone_code', 'from_date', 'to_date');
    clear_filter($filter);
  }


} //--- end class
?>
