<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Channels extends PS_Controller
{
  public $menu_code = 'DBCHAN';
	public $menu_group_code = 'DB';
	public $title = 'ช่องทางการขาย';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/channels';
    $this->load->model('masters/channels_model');
  }


  public function index()
  {
		$code = get_filter('code', 'cn_code', '');
		$name = get_filter('name', 'cn_name', '');

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->channels_model->count_rows($code, $name);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$rs = $this->channels_model->get_data($code, $name, $perpage, $this->uri->segment($segment));

    $ds = array(
      'code' => $code,
      'name' => $name,
			'data' => $rs
    );

		$this->pagination->initialize($init);
    $this->load->view('masters/channels/channels_view', $ds);
  }


  public function add_new()
  {
    if($this->pm->can_add)
		{
			$this->load->view('masters/channels/channels_add_view');
		}
		else
		{
			$this->deny_page();
		}

  }


  public function add()
  {
		$sc = TRUE;

    if($this->input->post('code'))
    {

      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $customer_code = $this->input->post('customer_code');
      $customer_name = $this->input->post('customer_name');
			$has_default = $this->channels_model->has_default();

      $ds = array(
        'code' => $code,
        'name' => $name,
        'customer_code' => empty($customer_code) ? NULL : $customer_code,
        'customer_name' => empty($customer_name) ? NULL : $customer_name,
				'is_default' => $has_default ? 0 : 1
      );

      if($this->channels_model->is_exists($code) === TRUE)
      {
        $sc = FALSE;
        $this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
      }

      if($this->channels_model->is_exists_name($name) === TRUE)
      {
        $sc = FALSE;
        $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
      }

      if($sc === TRUE)
      {
				if(! $this->channels_model->add($ds))
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
			$this->error = "Missing required parameter";
    }

    $this->response($sc);
  }



  public function edit($code)
  {
    $data['data'] = $this->channels_model->get_channels($code);
    $this->load->view('masters/channels/channels_edit_view', $data);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $name = $this->input->post('name');
			$old_name = $this->input->post('channels_name');
      $customer_code = $this->input->post('customer_code');
      $customer_name = $this->input->post('customer_name');
			$is_default = $this->input->post('is_default');

			if($this->channels_model->is_exists_name($name, $old_name))
			{
				$sc = FALSE;
				$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
			}

      $ds = array(
        'name' => $name,
        'customer_code' => empty($customer_code) ? NULL : $customer_code,
        'customer_name' => empty($customer_name) ? NULL : $customer_name
      );

			if($sc === TRUE)
			{
				if(! $this->channels_model->update($code, $ds))
				{
					$sc = FALSE;
					$error = $this->db->error();
					$this->error = "Update failed : ".$error['message'];
				}
				else
				{
					//--- set default
					if($is_default)
					{
						$this->channels_model->set_default($code);
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



  public function delete($code)
  {
    if($code != '')
    {
      if($this->channels_model->delete($code))
      {
        set_message('Channels deleted');
      }
      else
      {
        set_error('Cannot delete channels');
      }
    }
    else
    {
      set_error('Channels not found');
    }

    redirect($this->home);
  }



  public function clear_filter()
	{
		clear_filter(array('cn_code', 'cn_name'));
		echo 'done';
	}

}//--- end class
 ?>
