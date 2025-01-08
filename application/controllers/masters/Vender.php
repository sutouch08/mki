<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Vender extends PS_Controller
{
  public $menu_code = 'DBVEND';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = '';
	public $title = 'เพิ่ม/แก้ไข ผู้จำหน่าย';

  public function __construct()
  {
    parent::__construct();
    $this->title = label_value('DBVEND');
    $this->home = base_url().'masters/vender';
    $this->load->model('masters/vender_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'vender_code', ''),
      'name' => get_filter('name', 'vender_name', ''),
      'active' => get_filter('active', 'vender_active', 2)
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment = 4; //-- url segment
		$rows = $this->vender_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$emps = $this->vender_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $emps;

		$this->pagination->initialize($init);
    $this->load->view('masters/vender/vender_view', $filter);
  }


  public function add_new()
  {
		if($this->pm->can_add)
		{
			$this->load->view('masters/vender/vender_add');
		}
		else
		{
			$this->deny_page();
		}
  }


  public function add()
  {
		$sc = TRUE;

		if($this->pm->can_add)
		{
			if($this->input->post('code') && $this->input->post('name'))
			{
				$code = trim($this->input->post('code'));
				$name = trim($this->input->post('name'));

				if(!$this->vender_model->is_exists($code))
				{
					if(! $this->vender_model->is_exists_name($name))
					{
						$arr = array(
							'code' => $code,
							'name' => $name,
							'credit_term' => get_zero($this->input->post('term')),
							'tax_id' => get_null(trim($this->input->post('tax_id'))),
							'branch_name' => get_null(trim($this->input->post('branch'))),
							'address' => get_null(trim($this->input->post('address'))),
							'phone' => get_null(trim($this->input->post('phone'))),
							'active' => $this->input->post('active')
						);

						if(! $this->vender_model->add($arr))
						{
							$sc = FALSE;
							$this->error = "เพิ่มรายการไม่สำเร็จ";
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
					$this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing permission";
		}


		$this->response($sc);
  }



  public function edit($code)
  {
		if($this->pm->can_edit)
		{
			$data = $this->vender_model->get($code);

			if(!empty($data))
			{
	    	$this->load->view('masters/vender/vender_edit', $data);
			}
			else
			{
				$this->error_page();
			}
		}
		else
		{
			$this->deny_page();
		}
  }



  public function update()
  {
    $sc = TRUE;
		if($this->pm->can_edit)
		{
			if($this->input->post('code') && $this->input->post('name'))
			{
				$code = $this->input->post('code');
				$name = trim($this->input->post('name'));
				$old_name = $this->input->post('old_name');
				if(! $this->vender_model->is_exists_name($name, $old_name))
				{
					$arr = array(
						'name' => $name,
						'credit_term' => get_zero($this->input->post('term')),
						'tax_id' => get_null(trim($this->input->post('tax_id'))),
						'branch_name' => get_null(trim($this->input->post('branch'))),
						'address' => get_null(trim($this->input->post('address'))),
						'phone' => get_null(trim($this->input->post('phone'))),
						'active' => $this->input->post('active')
					);

					if(! $this->vender_model->update($code, $arr))
					{
						$sc = FALSE;
						$this->error = "Update รายการไม่สำเร็จ";
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
				$this->error = "Missing required parameter";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->response($sc);
  }



	public function view_detail($code)
  {
		$data = $this->vender_model->get($code);

		if(!empty($data))
		{
			$this->load->view('masters/vender/vender_edit', $data);
		}
		else
		{
			$this->error_page();
		}
		
  }


  public function delete()
	{
		$sc = TRUE;

		if($this->pm->can_delete)
		{
			$code = $this->input->post('code');

			if(!empty($code))
			{
				//--- check po transection
				$po = $this->vender_model->has_po($code);

				//--- check receive transection
				$received = $this->vender_model->has_received($code);

				if($po > 0 OR $received > 0)
				{
					$sc = FALSE;
					$this->error = "ไม่สามารถลบรายการได้ เนื่องจากมี Transection เกิดขึ้นแล้ว";
				}
				else
				{
					if(! $this->vender_model->delete($code))
					{
						$sc = FALSE;
						$this->error = "ลบรายการไม่สำเร็จ";
					}
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing permission";
		}

		$this->response($sc);
	}




	public function is_exists_code()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$old_code = $this->input->post('old_code');

		if($this->vender_model->is_exists($code, $old_code))
		{
			$sc = FALSE;
			$this->error = "duplicated";
		}

		$this->response($sc);
	}


  public function clear_filter()
	{
		$filter = array('emp_code', 'emp_name', 'emp_active');
    clear_filter($filter);
		echo 'done';
	}
}

?>
