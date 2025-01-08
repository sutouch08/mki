<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Box_size extends PS_Controller
{
  public $menu_code = 'DBBXSI';
	public $menu_group_code = 'DB';
	public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข ขนาดกล่อง';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/box_size';
    $this->load->model('masters/box_size_model');
		$this->load->helper('box');
  }


  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'box_code', ''),
			'box_name' => get_filter('box_name', 'box_name', ''),
			'box_width' => get_filter('box_width', 'box_width', ''),
			'box_length' => get_filter('box_length', 'box_length', ''),
			'box_height' => get_filter('box_height', 'box_height', ''),
			'box_type' => get_filter('box_type', 'box_type', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->box_size_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$rs = $this->box_size_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/box_size/box_size_list', $filter);
  }


	public function add_new()
	{
		$this->load->view('masters/box_size/box_size_add');
	}


	public function add()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$name = $this->input->post('name');
		$type = $this->input->post('box_type');
		$width = $this->input->post('box_width');
		$length = $this->input->post('box_length');
		$height = $this->input->post('box_height');


		if(!empty($code) && !empty($name))
		{
			$arr = array(
				'code' => $code,
				'name' => $name,
				'box_type' => $type,
				'box_width' => get_zero($width),
				'box_length' => get_zero($length),
				'box_height' => get_zero($height)
			);

			if(! $this->box_size_model->add($arr))
			{
				$sc = FALSE;
				$this->error = "เพิ่มรายการไม่สำเร็จ";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Name not found !";
		}

		$this->response($sc);
	}


  public function edit($code)
  {
    $ds = array(
      'code' => NULL,
      'name' => NULL,
      'box_type' => NULL,
      'box_width' => 0.00,
      'box_length' => 0.00,
      'box_height' => 0.00
    );

    $rs = $this->box_size_model->get($code);

    if(! empty($rs))
    {
      $ds = array(
        'code' => $rs->code,
        'name' => $rs->name,
        'box_type' => $rs->box_type,
        'box_width' => $rs->box_width,
        'box_length' => $rs->box_length,
        'box_height' => $rs->box_height
      );
    }

    $this->load->view('masters/box_size/box_size_edit', $ds);

  }



	public function update()
	{
		$sc = TRUE;
		$code = $this->input->post('code');

		if(!empty($code))
		{
			if(! empty($this->input->post('name')))
			{
				$arr = array(
					'name' => trim($this->input->post('name')),
					'box_type' => $this->input->post('box_type'),
					'box_width' => $this->input->post('box_width'),
					'box_length' => $this->input->post('box_length'),
					'box_height' => $this->input->post('box_height')
				);

				if( ! $this->box_size_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid name";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบ รหัส";
		}

		$this->response($sc);
	}


	public function delete()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		if(!empty($code))
		{
			//--- check transection
			if( $this->box_size_model->has_transection($code))
			{
				$sc = FALSE;
				$this->error = "ไม่สามารถลบได้เนื่องจากมี Transection แล้ว";
			}
			else
			{
				if( ! $this->box_size_model->delete($code))
				{
					$sc = FALSE;
					$this->error = "Delete failed";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Code notfound";
		}

		$this->response($sc);
	}

  public function clear_filter()
	{
		$filter = array(
			'box_code',
			'box_name',
			'box_width',
			'box_length',
			'box_height',
			'box_type'
		);

		clear_filter($filter);

		echo 'done';
	}

}//--- end class
 ?>
