<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_methods extends PS_Controller
{
  public $menu_code = 'DBPAYM';
	public $menu_group_code = 'DB';
	public $title = 'ช่องทางการชำระเงิน';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/payment_methods';
    $this->load->model('masters/payment_methods_model');
		$this->load->helper('payment_method');
  }


  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'payment_code', ''),
			'name' => get_filter('name', 'payment_code', ''),
			'role' => get_filter('role', 'payment_role', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->payment_methods_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$rs = $this->payment_methods_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;


		$this->pagination->initialize($init);
    $this->load->view('masters/payment_methods/payment_methods_view', $filter);
  }


  public function add_new()
  {
    $this->load->helper('payment_method');
		$this->load->helper('bank');
    $this->load->view('masters/payment_methods/payment_methods_add_view');
  }


  public function add()
  {
		$sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $name = $this->input->post('name');
			$role = $this->input->post('role');
			$acc_id = get_null($this->input->post('acc_no'));
      $active = $this->input->post('active') == 0 ? 0 : 1;
      $term = ($role == 1 OR $role == 4) ? 1 : 0;

			$has_default = $this->payment_methods_model->has_default();

      $ds = array(
        'code' => $code,
        'name' => $name,
				'role' => $role,
				'acc_id' => $acc_id,
        'has_term' => $term,
        'active' => $active,
				'is_default' => $has_default ? 0 : 1
      );

      if($this->payment_methods_model->is_exists($code))
      {
        $sc = FALSE;
        $this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
      }

      if($this->payment_methods_model->is_exists_name($name))
      {
        $sc = FALSE;
        $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
      }

			if($sc === TRUE)
			{
				if(! $this->payment_methods_model->add($ds))
				{
					$sc = FALSE;
					$error = $this->db->error();
					$this->error = "Insert failed : ".$error['message'];
				}
			}

    }
    else
    {
      $sc = FALSE;
			$this->error = "Missing Required Parameter";
    }

    $this->response($sc);
  }





  public function edit($code)
  {
    $this->load->helper('payment_method');
		$this->load->helper('bank');
    $ds = $this->payment_methods_model->get($code);


    $this->load->view('masters/payment_methods/payment_methods_edit_view', $ds);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $old_name = $this->input->post('old_name');
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $role = $this->input->post('role');
			$acc_id = get_null($this->input->post('acc_no'));
      $active = $this->input->post('active') == 0 ? 0 : 1;
			$term = ($role == 1 OR $role == 4) ? 1 : 0;
			$is_default = $this->input->post('is_default');

      $ds = array(
        'name' => $name,
        'has_term' => $term,
        'role' => $role,
				'acc_id' => $acc_id,
        'active' => $active
      );


			if($this->payment_methods_model->is_exists_name($name, $old_name))
			{
				$sc = FALSE;
				$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
			}

      if($sc === TRUE)
      {
        if(! $this->payment_methods_model->update($code, $ds))
        {
          $sc = FALSE;
					$error = $this->db->error();
					$this->error = "Update failed : ".$error['message'];
        }
        else
        {
					if($is_default)
					{
						$this->payment_methods_model->set_default($code);
					}

        }
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing Required Parameter";
    }

    $this->response($sc);
  }




  public function delete($code)
  {
    if($code != '')
    {
      if($this->payment_methods_model->delete($code))
      {
        set_message('Payment channels deleted');
      }
      else
      {
        set_error('Cannot delete payment channels');
      }
    }
    else
    {
      set_error('payment channels not found');
    }

    redirect($this->home);
  }



  //--- เช็คว่าการชำระเงินเป็นแบบเครดิตหรือไม่
  public function is_credit_payment($code)
  {
    //---- ตรวจสอบว่าเป็นเครดิตหรือไม่
    $rs = $this->payment_methods_model->has_term($code);
    echo $rs === TRUE ? 1 : 0;
  }


  public function clear_filter()
	{
		$filter = array('payment_code', 'payment_name', 'payment_role');
		clear_filter($filter);
		echo 'done';
	}

}//--- end class
 ?>
