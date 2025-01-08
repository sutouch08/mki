<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit extends PS_Controller
{
  public $menu_code = 'DBUNIT';
	public $menu_group_code = 'DB';
	public $menu_sub_group_code = 'PRODUCT';
	public $title = 'หน่วยนับ';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/unit';
    $this->load->model('masters/unit_model');
  }


  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'unit_code', ''),
			'name' => get_filter('name', 'unit_name', '')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->unit_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$rs = $this->unit_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/unit/unit_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/unit/unit_add');
  }


  public function add()
  {
		$sc = TRUE;

		$code = trim($this->input->post('code'));
		$name = trim($this->input->post('name'));

		if(!empty($code))
		{
			if($this->pm->can_add)
			{
				//--- check duplicate code;

				$is_exists = $this->unit_model->is_exists_code($code);
				if(! $is_exists)
				{
					$is_exists = $this->unit_model->is_exists_name($name);
					if(! $is_exists)
					{
						$arr = array(
							'code' => $code,
							'name' => $name
						);

						if(! $this->unit_model->add($arr))
						{
							$sc = FALSE;
							$this->error = "เพิ่มหน่วยนับไม่สำเร็จ";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อหน่วยนับใหม่";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "รหัสซ้ำ กรุณากำหนดรหัสหน่วยนับใหม่";
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing permission";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
  }



  public function edit($code)
  {
    $data['data'] = $this->unit_model->get($code);
    $this->load->view('masters/unit/unit_edit', $data);
  }



  public function update()
  {
    $sc = TRUE;
		$code = trim($this->input->post('code'));
		$name = trim($this->input->post('name'));
		$old_name = trim($this->input->post('old_name'));

		if(!empty($code))
		{
			if($this->pm->can_edit)
			{
				if(!empty($name))
				{
					$is_exists = $this->unit_model->is_exists_name($name, $old_name);

					if(! $is_exists)
					{
						$arr = array(
							'name' => $name
						);

						if(! $this->unit_model->update($code, $arr))
						{
							$sc = FALSE;
							$this->error = "Update failed";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing required parameter : name";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Permission";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete()
  {
		$sc = TRUE;
    $code = $this->input->post('code');

		if(!empty($code))
		{
			if($this->pm->can_delete)
			{
				//--- check transection used
				$has_trans = $this->unit_model->has_transection($code);

				if(! $has_trans)
				{
					if(!$this->unit_model->delete($code))
					{
						$sc = FALSE;
						$this->error = "Delete failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "หน่วยนับ {$code} มีการใช้งานแล้ว ไม่อนุญาติให้ลบ";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing permission";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
  }



	function set_default()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		if($code !== NULL)
		{
			//----
			$this->db->trans_begin();

			//--- remove current default
			if(!$this->unit_model->clear_default_state())
			{
				$sc = FALSE;
				$this->error = "Clear current default state failed";
			}
			else
			{
				if(! $this->unit_model->set_default_state($code))
				{
					$sc = FALSE;
					$this->error = "Set default state failed : {$code}";
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
			$this->error = "Missing required parameter : Code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function clear_filter()
	{
		clear_filter(array('unit_code', 'unit_name'));
		echo 'done';
	}

}//--- end class
 ?>
