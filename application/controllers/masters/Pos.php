<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pos extends PS_Controller
{
  public $menu_code = 'DBPOSM';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'POS';
	public $title = 'เพิ่ม/แก้ไข เครื่อง POS';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/pos';
    $this->load->model('masters/pos_model');
		$this->load->helper('shop');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pos_code', ''),
			'name' => get_filter('name', 'pos_name', ''),
			'pos_no' => get_filter('pos_no', 'pos_no', ''),
			'pos_code' => get_filter('pos_code', 'pos_poscode', ''),
			'shop' => get_filter('shop', 'pos_shop', ''),
			'status' => get_filter('status', 'pos_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->pos_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->pos_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/shop_pos/pos_list', $filter);
  }



	public function add_new()
	{
		$this->load->view('masters/shop_pos/pos_add');

	}


	public function is_restrict_prefix($prefix)
	{
		$rs = $this->db
		->where('group_code', 'Document')
		->where('value', $prefix)
		->count_all_results('config');

		if($rs == 1)
		{
			return TRUE;
		}

		return FALSE;
	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			if($this->input->post('code'))
			{
				if($this->input->post('name') && $this->input->post('shop_id') && $this->input->post('prefix'))
				{
					if($this->pos_model->is_exists_code(trim($this->input->post('code'))))
					{
						$sc = FALSE;
						$this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
					}

					if($sc === TRUE && $this->pos_model->is_exists_name(trim($this->input->post('name'))))
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
					}

					if($sc === TRUE && $this->pos_model->is_exists_prefix(trim($this->input->post('prefix'))))
					{
						$sc = FALSE;
						$this->error = "Prefix ซ้ำ กรุณากำหนดใหม่";
					}

					if($sc === TRUE && $this->is_restrict_prefix(trim($this->input->post('prefix'))))
					{
						$sc = FALSE;
						$this->error = "ไม่สามารถใช้ Prefix นี้ได้เนื่องจากซ้ำกับเอกสารอื่น สามารถตรวจสอบ Prefix เอกสารอื่นๆ ได้ที่เมนู การกำหนดค่า => การกำหนดค่า => เอกสาร ";
					}

					if($sc === TRUE)
					{
						$arr = array(
							'code' => trim($this->input->post('code')),
							'name' => trim($this->input->post('name')),
							'prefix' => trim($this->input->post('prefix')),
							'pos_no' => get_null(trim($this->input->post('pos_no'))),
							'pos_code' => get_null(trim($this->input->post('pos_code'))),
							'shop_id' => $this->input->post('shop_id'),
							'active' => $this->input->post('active'),
							'paper_size' => $this->input->post('paper_size'),
							'uname' => $this->_user->uname
						);

						if(! $this->pos_model->add($arr))
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
					$this->error = "Missing Parameter";
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
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}




	public function edit($code)
	{
		$pos = $this->pos_model->get_by_code($code);

		if(!empty($pos))
		{
			$this->load->view('masters/shop_pos/pos_edit', $pos);
		}
		else
		{
			$this->page_error();
		}
	}


	public function update()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			if($this->input->post('code'))
			{
				$code = trim($this->input->post('code'));
				$old_name = trim($this->input->post('old_name'));

				if($this->input->post('name') && $this->input->post('shop_id') && $this->input->post('prefix'))
				{

					if($this->pos_model->is_exists_name(trim($this->input->post('name')), $code))
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
					}

					if($sc === TRUE && $this->pos_model->is_exists_prefix(trim($this->input->post('prefix')), $code))
					{
						$sc = FALSE;
						$this->error = "Prefix ซ้ำ กรุณากำหนดใหม่";
					}

					if($sc === TRUE && $this->is_restrict_prefix(trim($this->input->post('prefix'))))
					{
						$sc = FALSE;
						$this->error = "ไม่สามารถใช้ Prefix นี้ได้เนื่องจากซ้ำกับเอกสารอื่น สามารถตรวจสอบ Prefix เอกสารอื่นๆ ได้ที่เมนู การกำหนดค่า => การกำหนดค่า => เอกสาร ";
					}

					if($sc === TRUE)
					{
						$arr = array(
							'name' => trim($this->input->post('name')),
							'prefix' => trim($this->input->post('prefix')),
							'pos_no' => get_null(trim($this->input->post('pos_no'))),
							'pos_code' => get_null(trim($this->input->post('pos_code'))),
							'shop_id' => $this->input->post('shop_id'),
							'active' => $this->input->post('active'),
							'paper_size' => $this->input->post('paper_size'),
							'uname' => $this->_user->uname
						);

						if(! $this->pos_model->update($code, $arr))
						{
							$sc = FALSE;
							$error = $this->db->error();
							$this->error = "Update Failed : ".$error['message'];
						}
					}

				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing Parameter";
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
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}



	public function delete()
	{
		$sc = TRUE;

		$code = $this->input->post('code');
		if(! is_null($code))
		{
			if($this->pm->can_delete)
			{
				//---- check transection
				$transection = $this->pos_model->has_transection($code);

				if(! $transection)
				{
					if( ! $this->pos_model->delete($code))
					{
						$sc = FALSE;
						$error = $this->db->error();
						$this->error = "Delete Failed : ".$error['message'];
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Delete Failed : Transection exists";
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
			$this->error = "Missing Parameter : code";
		}


		$this->response($sc);
	}



	public function get_zone_code_and_name()
	{
		$txt = trim($this->input->get('term'));
		$ds = array();
		if(! is_null($txt))
		{
			if($txt !== '*')
			{
				$this->db->group_start();
				$this->db->like('code', $txt);
				$this->db->or_like('name', $txt);
				$this->db->group_end();
			}

			$rs = $this->db->limit(20)->get('zone');

			if($rs->num_rows() > 0)
			{
				foreach($rs->result() as $zone)
				{
					$ds[] = $zone->code.' | '.$zone->name;
				}
			}
			else
			{
				$ds[] = 'not found';
			}

		}

		echo json_encode($ds);

	}




	public function get_customer_code_and_name()
	{
		$txt = trim($this->input->get('term'));
		$ds = array();
		if(! is_null($txt))
		{
			if($txt !== '*')
			{
				$this->db->group_start();
				$this->db->like('code', $txt);
				$this->db->or_like('name', $txt);
				$this->db->group_end();
			}

			$rs = $this->db->limit(20)->get('customers');

			if($rs->num_rows() > 0)
			{
				foreach($rs->result() as $customer)
				{
					$ds[] = $customer->code.' | '.$customer->name;
				}
			}
			else
			{
				$ds[] = 'not found';
			}

		}

		echo json_encode($ds);

	}



  public function clear_filter()
  {
    $filter = array(
			'post_code',
			'pos_name',
			'pos_shop',
			'pos_no',
			'pos_poscode',
			'pos_status'
		);


    clear_filter($filter);
  }

} //--- end class

 ?>
