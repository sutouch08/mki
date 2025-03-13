<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_round extends PS_Controller{
	public $menu_code = 'DBODRN'; //--- Add/Edit Users
	public $menu_group_code = 'DB'; //--- System security
	public $title = 'รอบตัดออเดอร์';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/order_round';
		$this->load->model('masters/order_round_model');
  }



  public function index()
  {
		$filter = array(
			'name' => get_filter('name', 'name', ''),
			'active' => get_filter('active', 'active', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->order_round_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->order_round_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/order_round/order_round_list', $filter);
  }



	public function add_new()
	{
		$this->load->view('masters/order_round/order_round_add');
	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			$name = trim($this->input->post('name'));
			$pos = $this->input->post('position');
			$active = $this->input->post('active') == 1 ? 1 : 0;

			if(empty($name))
			{
				$sc = FALSE;
				$this->error = set_error_message('required');
			}

			if($sc === TRUE)
			{
				if($this->order_round_model->is_exists($name))
				{
					$sc = FALSE;
					$this->error = set_error_message('exists', $name);
				}

				if($sc === TRUE)
				{
					$arr = array(
						'name' => $name,
						'position' => empty($pos) ? 10 : $pos,
						'active' => $active
					);

					if( ! $this->order_round_model->add($arr))
					{
						$sc = FALSE;
						$this->error = set_error_message('insert');
					}
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = set_error_message('permission');
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function edit($id)
	{
		$ds = $this->order_round_model->get_by_id($id);
		$data['ds'] = $ds;
		$this->load->view('masters/order_round/order_round_edit', $data);
	}



	public function update()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$name = trim($this->input->post('name'));
		$active = $this->input->post('active');
		$pos = $this->input->post('position');

		if(empty($name))
		{
			$sc = FALSE;
			$this->error = set_error_message('required');
		}

		if($sc === TRUE)
		{
			if($this->order_round_model->is_exists($name, $id))
			{
				$sc = FALSE;
				$this->error = set_error_message('exists', $name);
			}

			if($sc === TRUE)
			{
				$arr = array(
					'name' => $name,
					'position' => empty($pos) ? 10 : $pos,
					'active' => $active
				);

				if( ! $this->order_round_model->update($id, $arr))
				{
					$sc = FALSE;
					$this->error = set_error_message('update');
				}
			}
		}


		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function delete($id)
	{
		$sc = TRUE;

		if( ! $this->order_round_model->delete($id))
		{
			$sc = FALSE;
			$this->error = set_error_message('delete');
		}

		$this->response($sc);
	}


	public function clear_filter()
	{
		$filter = array('name', 'active');
		clear_filter($filter);
	}

}//--- end class


 ?>
