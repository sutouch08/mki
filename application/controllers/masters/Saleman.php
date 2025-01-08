<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saleman extends PS_Controller{
	public $menu_code = 'DBSALE'; //--- Add/Edit Users
	public $menu_group_code = 'DB'; //--- System security
	public $title = 'พนักงานขาย';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/saleman';
		$this->load->model('masters/saleman_model');
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
		$rows = $this->saleman_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->saleman_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/saleman/saleman_view', $filter);
  }



	public function add_new()
	{
		$this->load->view('masters/saleman/saleman_add');
	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			$code = trim($this->input->post('code'));
			$name = trim($this->input->post('name'));
			$active = $this->input->post('active') == 1 ? 1 : 0;

			if($code != '' && $name != '')
			{
				if(! $this->saleman_model->is_exists($code))
				{
					if(! $this->saleman_model->is_exists_name($name))
					{
						$arr = array(
							'code' => $code,
							'name' => $name,
							'active' => $active
						);

						if(!$this->saleman_model->add($arr))
						{
							$sc = FALSE;
							$this->error = "เพิ่มข้อมูลไม่สำเร็จ";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ชื่อพนังงานขายซ้ำ";
					}

				}
				else
				{
					$sc = FALSE;
					$this->error = "รหัสซ้ำ";
				}

			}
			else
			{
				$this->error = "ไม่พบข้อมูลในฟอร์ม";
				$sc = FALSE;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "คุณไม่มีสิทธิ์ในการเพิ่มข้อมูล";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function edit($code)
	{
		$ds = $this->saleman_model->get($code);
		$data['ds'] = $ds;
		$this->load->view('masters/saleman/saleman_edit', $data);
	}



	public function update()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$name = trim($this->input->post('name'));
		$active = $this->input->post('active');

		if($code && $name)
		{
			if(! $this->saleman_model->is_exists_name($name, $code))
			{
				$arr = array(
					'name' => $name,
					'active' => $active
				);

				if(!$this->saleman_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ชื่อพนังงานขายซ้ำ";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบข้อมูลในฟอร์ม";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function delete($code)
	{
		if($this->has_transection($code) > 0)
		{
			echo 'ไม่สามารถลบได้เนื่องจากมี Transection';
		}
		else
		{
			if(!$this->saleman_model->delete($code))
			{
				echo "ลบรายการไม่สำเร็จ";
			}
			else
			{
				echo 'success';
			}
		}
	}




	public function is_exists_name()
	{
		$code = trim($this->input->post('code'));
		$name = trim($this->input->post('name'));

		if($this->saleman_model->is_exists_name($name, $code))
		{
			echo "ชื่อพนักงานขายซ้ำ";
		}
		else
		{
			echo "ok";
		}
	}


	public function has_transection($code)
	{
		$order = $this->saleman_model->has_order($code);
		$order_sold = $this->saleman_model->has_sold_order($code);
		return $order + $order_sold;
	}


	public function clear_filter()
	{
		$filter = array('name', 'active');
		clear_filter($filter);
	}

}//--- end class


 ?>
