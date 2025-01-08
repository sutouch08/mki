<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Box_type extends PS_Controller
{
  public $menu_code = 'DBBOTY';
	public $menu_group_code = 'DB';
	public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข ชนิดกล่อง';
	public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/box_type';
    $this->load->model('masters/box_type_model');
  }


  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'type_code', ''),
			'name' => get_filter('name', 'type_name', '')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->box_type_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$rs = $this->box_type_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/box_type/box_type_list', $filter);
  }


	public function add_new()
	{
		$this->load->view('masters/box_type/box_type_add');
	}


	public function add()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$name = $this->input->post('name');
		if(!empty($code))
		{
			if(!empty($name))
			{
				//--- check duplicate code
				$is_exists = $this->box_type_model->is_exists($code);
				if(! $is_exists)
				{
					$arr = array(
						'code' => $code,
						'name' => $name
					);

					if(! $this->box_type_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "เพิ่มรายการไม่สำเร็จ";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "รหัสซ้ำ มีรหัสนี้อยู่ในระบบแล้ว";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Name not found !";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Code not found !";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function edit($code)
	{
		$rs = $this->box_type_model->get($code);
		if(!empty($rs))
		{
			$ds = array(
				'code' => $rs->code,
				'name' => $rs->name
			);
		}
		else
		{
			$ds = array(
				'code' => "",
				'name' => ""
			);
		}

		$this->load->view('masters/box_type/box_type_edit', $ds);
	}



	public function update()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$name = $this->input->post('name');

		if(!empty($code))
		{
			if(!empty($name))
			{
				$arr = array('name' => $name);

				if(! $this->box_type_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "ปรับปรุงรายการไมสำเร็จ";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่พบชื่อ";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบรหัส";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function delete()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		if(!empty($code))
		{
			//--- check transection
			if($this->box_type_model->has_transection($code))
			{
				$sc = FALSE;
				$this->error = "ไม่สามารถลบได้เนื่องจากมี Transection แล้ว";
			}
			else
			{
				if(! $this->box_type_model->delete($code))
				{
					$sc = FALSE;
					$this->error = "ลบรายการไม่สำเร็จ";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบรหัส";
		}

		$this->response($sc);
	}

  public function clear_filter()
	{
		$filter = array('type_code', 'type_name');
		clear_filter($filter);
		echo 'done';
	}

}//--- end class
 ?>
