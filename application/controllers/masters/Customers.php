<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Customers extends PS_Controller
{
  public $menu_code = 'DBCUST';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'เพิ่ม/แก้ไข รายชื่อลูกค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/customers';
    $this->load->model('masters/customers_model');
    $this->load->model('masters/customer_group_model');
    $this->load->model('masters/customer_kind_model');
    $this->load->model('masters/customer_type_model');
    $this->load->model('masters/customer_class_model');
    $this->load->model('masters/customer_area_model');
		$this->load->model('masters/saleman_model');
    $this->load->model('masters/channels_model');
    $this->load->helper('customer');
    $this->load->helper('channels');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'name' => get_filter('name', 'name', ''),
      'group' => get_filter('group', 'group', 'all'),
      'kind' => get_filter('kind', 'kind', 'all'),
      'type' => get_filter('type', 'type', 'all'),
      'class' => get_filter('class', 'class', 'all'),
      'area' => get_filter('area', 'area', 'all'),
      'channels' => get_filter('channels', 'channels', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment = 4; //-- url segment
		$rows = $this->customers_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$filter['data'] = $this->customers_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if( ! empty($filter['data']))
    {
      foreach($filter['data'] as $rs)
      {
        $rs->group  = $this->customer_group_model->get_name($rs->group_code);
        $rs->kind   = $this->customer_kind_model->get_name($rs->kind_code);
        $rs->type   = $this->customer_type_model->get_name($rs->type_code);
        $rs->channels  = $this->channels_model->get_name($rs->channels_code);
        $rs->class  = $this->customer_class_model->get_name($rs->class_code);
        $rs->area  = $this->customer_area_model->get_name($rs->area_code);
      }
    }

		$this->pagination->initialize($init);
    $this->load->view('masters/customers/customers_view', $filter);
  }




  public function add_new()
  {
    $no = $this->customers_model->get_max_no();
    $code = $this->get_new_code($no);
    $this->load->view('masters/customers/customers_add_view', ['code' => $code, 'run_no' => $no]);
  }


  public function get_new_code($no)
  {
    $prefix = getConfig('PREFIX_CUSTOMER_CODE');
    $run_digit = getConfig('RUN_DIGIT_CUSTOMER_CODE');

    if( ! is_null($no))
    {
      $run_no = intval($no) + 1;
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function add()
  {
		$sc = TRUE;
		$code = $this->input->post('code');
    $run_no = intval($this->input->post('run_no')) + 1;
		$name = $this->input->post('name');
		$credit = $this->input->post('CreditLine');
		$credit_term = $this->input->post('credit_term');

    if(! is_null($code) && !empty($name))
    {
      $ds = array(
        'code' => $code,
        'name' => $name,
        'Tax_Id' => get_null(trim($this->input->post('Tax_id'))),
        'group_code' => get_null(trim($this->input->post('group'))),
        'kind_code' => get_null(trim($this->input->post('kind'))),
        'type_code' => get_null(trim($this->input->post('type'))),
        'class_code' => get_null(trim($this->input->post('class'))),
        'area_code' => get_null(trim($this->input->post('area'))),
        'channels_code' => get_null($this->input->post('channels')),
        'sale_code' => get_null(trim($this->input->post('sale'))),
        'credit_term' => empty($credit_term) ? 0 : $credit_term,
        'amount' => empty($credit) ? 0 : $credit,
        'note' => get_null($this->input->post('note')),
        'run_no' => $run_no
      );

      if($this->customers_model->is_exists($code))
      {
        $sc = FALSE;
        $this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
      }

      if($this->customers_model->is_exists_name($name))
      {
        $sc = FALSE;
        $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
      }

      if($sc === TRUE)
      {
        if($this->customers_model->add($ds))
        {
          $this->customers_model->update_balance($code);
        }
        else
        {
          $sc = FALSE;
          $error = $this->db->error();
					$this->error = "Insert Failed : ".$error['message'];
        }
      }
    }
    else
    {
      $sc = FALSE;
			$this->error = "Missing Required parameter: Code";
    }

    $this->response($sc);
  }



  public function edit($code, $tab='infoTab')
  {
    $this->load->model('address/customer_address_model');
    $this->load->model('address/address_model');
    $rs = $this->customers_model->get($code);
    $bill_to = $this->customer_address_model->get_customer_bill_to_address($code);
    $ship_to = $this->address_model->get_ship_to_address($code);

    $data['ds'] = $rs;
    $data['tab'] = $tab;
    $data['disabled'] = ''; //--- ไม่ต้องปิดการแก้ไข
    $data['bill'] = $bill_to;
    $data['addr'] = $ship_to;

    $this->load->view('masters/customers/customers_edit_view', $data);
  }



  public function view_detail($code, $tab='infoTab')
  {
    $this->load->model('address/customer_address_model');
    $this->load->model('address/address_model');
    $rs = $this->customers_model->get($code);
    $bill_to = $this->customer_address_model->get_customer_bill_to_address($code);
    $ship_to = $this->address_model->get_shipping_address($code);

    $data['ds'] = $rs;
    $data['tab'] = $tab;
    $data['disabled'] = 'disabled';
    $data['bill'] = $bill_to;
    $data['addr'] = $ship_to;

    $this->load->view('masters/customers/customers_detail_view', $data);
  }


  public function add_bill_to($code)
  {
    if($this->input->post('address'))
    {
      $this->load->model('address/customer_address_model');
      $branch_code = $this->input->post('branch_code');
      $branch_name = $this->input->post('branch_name');
      $country = $this->input->post('country');
      $ds = array(
        'customer_code' => $code,
				'customer_name' => trim($this->input->post('customer_name')),
        'branch_code' => empty($branch_code) ? '000' : $branch_code,
        'branch_name' => empty($branch_name) ? 'สำนักงานใหญ่' : $branch_name,
        'address' => $this->input->post('address'),
        'sub_district' => $this->input->post('sub_district'),
        'district' => $this->input->post('district'),
        'province' => $this->input->post('province'),
        'postcode' => $this->input->post('postcode'),
        'country' => empty($country) ? 'TH' : $country,
        'phone' => $this->input->post('phone')
      );

      $rs = $this->customer_address_model->add_bill_to($ds);
      if($rs === TRUE)
      {
        set_message("เพิ่มที่อยู่เปิดบิลเรียบร้อยแล้ว");
      }
      else
      {
        set_error("เพิ่มที่อยู่ไม่สำเร็จ");
      }
    }
    else
    {
      set_error("ที่อยู่ต้องไม่ว่างเปล่า");
    }

    redirect($this->home.'/edit/'.$code.'/billTab');
  }



  public function update_bill_to($code)
  {
    if($this->input->post('address'))
    {
      $this->load->model('address/customer_address_model');
      $branch_code = $this->input->post('branch_code');
      $branch_name = $this->input->post('branch_name');
      $country = $this->input->post('country');
      $ds = array(
				'customer_name' => trim($this->input->post('customer_name')),
        'branch_code' => empty($branch_code) ? '000' : $branch_code,
        'branch_name' => empty($branch_name) ? 'สำนักงานใหญ่' : $branch_name,
        'address' => $this->input->post('address'),
        'sub_district' => $this->input->post('sub_district'),
        'district' => $this->input->post('district'),
        'province' => $this->input->post('province'),
        'postcode' => $this->input->post('postcode'),
        'country' => empty($country) ? 'TH' : $country,
        'phone' => $this->input->post('phone')
      );

      $rs = $this->customer_address_model->update_bill_to($code, $ds);
      if($rs === TRUE)
      {
        set_message("ปรับปรุงที่อยู่เปิดบิลเรียบร้อยแล้ว");
      }
      else
      {
        set_error("ปรับปรุงที่อยู่ไม่สำเร็จ");
      }
    }
    else
    {
      set_error("ที่อยู่ต้องไม่ว่างเปล่า");
    }

    redirect($this->home.'/edit/'.$code.'/billTab');
  }



	public function update()
  {
		$sc = TRUE;
		$code = $this->input->post('code');
		$name = $this->input->post('name');
		$old_name = trim($this->input->post('old_name'));
		$credit = $this->input->post('CreditLine');
		$credit_term = $this->input->post('credit_term');
    if(! is_null($code) && !empty($name))
    {
      $ds = array(
        'code' => $code,
        'name' => $name,
        'Tax_Id' => get_null(trim($this->input->post('Tax_id'))),
        'group_code' => get_null(trim($this->input->post('group'))),
        'kind_code' => get_null(trim($this->input->post('kind'))),
        'type_code' => get_null(trim($this->input->post('type'))),
        'class_code' => get_null(trim($this->input->post('class'))),
        'area_code' => get_null(trim($this->input->post('area'))),
        'channels_code' => get_null($this->input->post('channels')),
        'sale_code' => get_null(trim($this->input->post('sale'))),
        'credit_term' => empty($credit_term) ? 0 : $credit_term,
        'amount' => empty($credit) ? 0 : $credit,
        'note' => get_null($this->input->post('note'))
      );


      if($this->customers_model->is_exists_name($name, $old_name))
      {
        $sc = FALSE;
        $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
      }

      if($sc === TRUE)
      {
        if($this->customers_model->update($code, $ds))
        {
          $this->customers_model->update_balance($code);
        }
        else
        {
          $sc = FALSE;
          $error = $this->db->error();
					$this->error = "Insert Failed : ".$error['message'];
        }
      }
    }
    else
    {
      $sc = FALSE;
			$this->error = "Missing Required parameter: Code";
    }

    $this->response($sc);
  }



  public function delete()
  {
		$code = trim($this->input->post('code'));
    $sc = TRUE;

    if($code != "" && $code != NULL)
    {
			//--- check transcetion
			if($this->customers_model->is_exists_transection($code))
			{
				$sc = FALSE;
				$this->error = "ไม่สามารถลบสินค้าได้เนื่องจากมีทรานเซ็คชั่นเกิดขึ้นแล้ว";
			}

			if($sc === TRUE)
			{
				//--- delete customer address
	      if(! $this->customers_model->delete_address($code))
	      {
	        $sc = FALSE;
					$this->error = "ลบที่อยู่ไม่สำเร็จ";
	      }
				else
				{
					if(! $this->customers_model->delete($code))
					{
						$sc = FALSE;
						$this->error = "ลบข้อมูลไม่สำเร็จ";
					}
				}
			}
    }
    else
    {
			$sc = FALSE;
			$this->error = "Missing required parameter: Code";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




	public function download_template($token)
	{
		//--- load excel library
		$this->load->library('excel');

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Items Master Template');

		//--- set report title header
		$this->excel->getActiveSheet()->setCellValue('A1', 'Customer Code');
		$this->excel->getActiveSheet()->setCellValue('B1', 'Customer Name');
		$this->excel->getActiveSheet()->setCellValue('C1', 'Tax Id');
		$this->excel->getActiveSheet()->setCellValue('D1', 'Customer Group');
		$this->excel->getActiveSheet()->setCellValue('E1', 'Customer Kind');
		$this->excel->getActiveSheet()->setCellValue('F1', 'Customer Type');
		$this->excel->getActiveSheet()->setCellValue('G1', 'Customer Grade');
		$this->excel->getActiveSheet()->setCellValue('H1', 'Sales Area');
		$this->excel->getActiveSheet()->setCellValue('I1', 'Sale Code');
		$this->excel->getActiveSheet()->setCellValue('J1', 'Credit Term');
		$this->excel->getActiveSheet()->setCellValue('K1', 'Credit Amount');
		$this->excel->getActiveSheet()->setCellValue('L1', 'Notes');



		setToken($token);

		$file_name = "Customers_master_template.xlsx";
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
		header('Content-Disposition: attachment;filename="'.$file_name.'"');
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		$writer->save('php://output');
	}



	public function import_customers()
	{
		$sc = TRUE;
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path');
    $file	= 'uploadFile';
		$config = array(   // initial config for upload class
			"allowed_types" => "xlsx",
			"upload_path" => $path,
			"file_name"	=> "import_customers",
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
			$this->load->library('excel');

			$info = $this->upload->data();
			/// read file
			$excel = PHPExcel_IOFactory::load($info['full_path']);
			//get only the Cell Collection
			$collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

			$i = 1;
			$count = count($collection);
			$limit = intval(getConfig('IMPORT_ROWS_LIMIT'))+1;

			if($count <= $limit)
			{
				foreach($collection as $rs)
				{
					if($i == 1)
					{
						$i++;

						$headCol = array(
							'A' => 'Customer Code',
							'B' => 'Customer Name',
							'C' => 'Tax Id',
							'D' => 'Customer Group',
							'E' => 'Customer Kind',
							'F' => 'Customer Type',
							'G' => 'Customer Grade',
							'H' => 'Sales Area',
							'I' => 'Sale Code',
							'J' => 'Credit Term',
							'K' => 'Credit Amount',
							'L' => 'Notes'
						);

						foreach($headCol as $col => $field)
						{
							if($rs[$col] !== $field)
							{
								$sc = FALSE;
								$this->error = 'Column '.$col.' Should be '.$field;
								break;
							}
						}

						if($sc === FALSE)
						{
							break;
						}

					}
					else if(!empty($rs['A']))
					{
						if($sc === FALSE)
						{
							break;
						}

						$code_pattern = '/[^a-zA-Z0-9_-]/';

						$code = preg_replace($code_pattern, '', trim($rs['A']));
						$name = get_null(trim($rs['B']));
						$taxId = get_null(trim($rs['C']));
						$group = get_null(trim($rs['D']));
						$kind = get_null(trim($rs['E']));
						$type = get_null(trim($rs['F']));
						$class = get_null(trim($rs['G']));
						$area = get_null(trim($rs['H']));
						$sale = get_null(trim($rs['I']));
						$term = get_zero(trim($rs['J']));
						$amount = get_zero(trim($rs['K']));
						$notes = get_null(trim($rs['L']));

						if(!empty($group) && ! $this->customer_group_model->is_exists($group))
						{
							$this->addGroup($group);
						}

						if(!empty($kind) && !$this->customer_kind_model->is_exists($kind))
						{
							$this->addKind($kind);
						}

						if(!empty($type) && !$this->customer_type_model->is_exists($type))
						{
							$this->addType($type);
						}

						if(!empty($class) && !$this->customer_class_model->is_exists($class))
						{
							$this->addClass($class);
						}

						if(!empty($area) && !$this->customer_area_model->is_exists($area))
						{
							$this->addArea($area);
						}

						if(!empty($sale) && !$this->saleman_model->is_exists($sale))
						{
							$this->addSale($sale, NULL, $area);
						}

						$arr = array(
							'code' => $code,
							'name' => $name,
							'Tax_Id' => $taxId,
							'group_code' => $group,
							'kind_code' => $kind,
							'type_code' => $type,
							'class_code' => $class,
							'area_code' => $area,
							'sale_code' => $sale,
							'credit_term' => $term,
							'amount' => $amount
						);

						if($this->customers_model->is_exists($code))
						{
							$is_done = $this->customers_model->update($code, $arr);
						}
						else
						{
							$is_done = $this->customers_model->add($arr);
						}

					}
				} //-- end foreach
			}
			else
			{
				$sc = FALSE;
				$this->error = "จำนวนนำเข้าสูงสุดได้ไม่เกิน {$limit} บรรทัด";
			} //-- end if count limit
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function add_attribute()
	{
		$sc = TRUE;
		$attr = $this->input->post('attribute');
		$code = $this->input->post('code');
		$name = $this->input->post('name');

		if(!empty($attr))
		{
			switch ($attr)
			{
				case 'group':
					$rs = $this->addGroup($code, $name);
				break;
				case 'kind':
					$rs = $this->addKind($code, $name);
				break;
				case 'type':
					$rs = $this->addType($code, $name);
				break;
				case 'class':
					$rs = $this->addClass($code, $name);
				break;
				case 'area':
					$rs = $this->addArea($code, $name);
				break;
				default:
					$rs = $this->addGroup($code, $name);
				break;
			}

			if($rs === FALSE)
			{
				$sc = FALSE;
				$error = $this->db->error();
				$this->error = "Insert failed : ".$error['message'];
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Attribute";
		}

		$this->response($sc);
	}


	private function addGroup($code, $name = NULL)
	{
		$arr = array(
			'code' => $code,
			'name' => empty($name) ? $code : $name
		);

		return $this->customer_group_model->add($arr);
	}

	private function addKind($code, $name = NULL)
	{
		$arr = array(
			'code' => $code,
			'name' => empty($name) ? $code : $name
		);

		return $this->customer_kind_model->add($arr);
	}

	private function addType($code, $name = NULL)
	{
		$arr = array(
			'code' => $code,
			'name' => empty($name) ? $code : $name
		);

		return $this->customer_type_model->add($arr);
	}

	private function addClass($code, $name = NULL)
	{
		$arr = array(
			'code' => $code,
			'name' => empty($name) ? $code : $name
		);

		return $this->customer_class_model->add($arr);
	}


	private function addArea($code, $name = NULL)
	{
		$arr = array(
			'code' => $code,
			'name' => empty($name) ? $code : $name
		);

		return $this->customer_area_model->add($arr);
	}


	private function addSale($code, $name = NULL, $area = NULL)
	{
		$arr = array(
			'code' => $code,
			'name' => empty($name) ? $code : $name,
			'area_code' => empty($area) ? NULL : $area
		);

		return $this->saleman_model->add($arr);
	}



  public function clear_filter()
	{
    $filter = array( 'code', 'name','group','kind','type', 'class','area');
    clear_filter($filter);
	}
}

?>
