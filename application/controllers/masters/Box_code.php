<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Box_code extends PS_Controller
{
  public $menu_code = 'DBBOXC';
	public $menu_group_code = 'DB';
	public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข รหัสกล่อง';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/box_code';
    $this->load->model('masters/box_code_model');
		$this->load->helper('box');
  }


  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'box_code', ''),
			'box_name' => get_filter('box_name', 'box_name', ''),
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
		$rows = $this->box_code_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$rs = $this->box_code_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/box_code/box_code_list', $filter);
  }


	public function add_new()
	{
		$this->load->view('masters/box_code/box_code_add');
	}


	public function add()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$size = $this->input->post('size_code');
		$result = NULL;

		if(!empty($code))
		{
			if(!empty($size))
			{
				if($this->pm->can_add)
				{
					$exists = $this->box_code_model->is_exists($code);
					if(! $exists)
					{
						$arr = array(
							'code' => $code,
							'size_code' => $size
						);

						if(! $this->box_code_model->add($arr))
						{
							$sc = FALSE;
							$this->error = "เพิ่มรายการไม่สำเร็จ";
						}
						else
						{
							$ds = $this->box_code_model->get_box($code);
							if(!empty($ds))
							{
								$arr = array(
									'code' => $ds->code,
									'size_name' => $ds->name,
									'type_name' => $ds->type_name,
									'box_width' => $ds->box_width,
									'box_length' => $ds->box_length,
									'box_height' => $ds->box_height
								);

								$result = json_encode($arr);
							}
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
					$sc = FALSE;
					$this->error = "คุณไม่มีสิทธิ์ในการเพิ่มรหัสกล่อง";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "กรุณาเลือกขนาดกล่อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบรหัสกล่อง";
		}

		echo $sc === TRUE ? $result : $this->error;
	}


  public function edit($code)
  {
    $rs = $this->box_code_model->get($code);

    if(! empty($rs))
    {
      $ds = array(
        'code' => $rs->code,
        'size_code' => $rs->size_code
      );
    }

    $this->load->view('masters/box_code/box_code_edit', $ds);

  }



	public function update()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$size_code = $this->input->post('size_code');

		if(!empty($code))
		{
			if(! empty($this->input->post('size_code')))
			{
				$arr = array(
					"size_code" => $size_code
				);

				if( ! $this->box_code_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Size Code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบ Box Code";
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
			if( $this->box_code_model->has_transection($code))
			{
				$sc = FALSE;
				$this->error = "ไม่สามารถลบได้เนื่องจากมี Transection แล้ว";
			}
			else
			{
				if( ! $this->box_code_model->delete($code))
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



	public function get_sample_file($token)
  {
    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sample');

    //--- header
    $this->excel->getActiveSheet()->setCellValue('A1', 'BoxCode');
    $this->excel->getActiveSheet()->setCellValue('B1', 'SizeCode');

    setToken($token);

    $file_name = "BoxCodeTemplate.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }




	public function import_excel_file()
  {
    $sc = TRUE;
    $this->load->library('excel');

    $file = isset( $_FILES['excel'] ) ? $_FILES['excel'] : FALSE;

    if($file !== FALSE)
    {
      $file	= 'excel';
  		$config = array(   // initial config for upload class
  			"allowed_types" => "xlsx",
  			"upload_path" => $this->config->item('upload_file_path'),
  			"file_name"	=> "box_code",
  			"max_size" => 5120,
  			"overwrite" => TRUE
  			);

  			$this->load->library("upload", $config);

  			if(! $this->upload->do_upload($file))
        {
          $sc = FALSE;
  				$this->error = $this->upload->display_errors();
  			}
        else
        {
          $info = $this->upload->data();
          /// read file
  				$excel = PHPExcel_IOFactory::load($info['full_path']);
  				//get only the Cell Collection
          $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

          $i = 1;

          $this->db->trans_begin();

          foreach($collection as $rs)
          {

						if($sc === FALSE)
						{
							break;
						}

            if($i > 1)
            {
              //--- skip hrader row
              $code = trim($rs['A']);
              $size_code = trim($rs['B']);

              if(!empty($code) && !empty($size_code))
              {
                if( ! $this->box_code_model->is_exists($code))
								{
									$arr = array(
										'code' => $code,
										'size_code' => $size_code
									);

									if(! $this->box_code_model->add($arr))
									{
										$sc = FALSE;
										$this->error = "Import Item Failed @ Row - {$i}";
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "Duplicate Box code '{$code}' @ Row - {$i}";
								}
              }

            } //--- end if $i

            $i++;
          } //--- endforeach

          if($sc === FALSE)
          {
            $this->db->trans_rollback();
          }
          else
          {
            $this->db->trans_commit();
          }
        }
    }
  	else
    {
      $sc = FALSE;
      $this->error = "Upload file not found";
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
