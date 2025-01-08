<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Shop extends PS_Controller
{
  public $menu_code = 'DBPOSS';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'POS';
	public $title = 'เพิ่ม/แก้ไข จุดขาย';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/shop';
    $this->load->model('masters/shop_model');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'shop_code', ''),
			'name' => get_filter('name', 'shop_name', ''),
			'zone' => get_filter('zone', 'shop_zone', ''),
			'status' => get_filter('status', 'shop_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->shop_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->shop_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/shop/shop_list', $filter);
  }



	public function add_new()
	{
		$this->load->view('masters/shop/shop_add');

	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			if($this->input->post('code'))
			{
				if($this->input->post('name') && $this->input->post('zone_code'))
				{
					if($this->shop_model->is_exists_code(trim($this->input->post('code'))))
					{
						$sc = FALSE;
						$this->error = "รหัสซ้ำ กรุณากำหนดรหัสจุดขายใหม่";
					}

					if($sc === TRUE && $this->shop_model->is_exists_name(trim($this->input->post('name'))))
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อจุดขายใหม่";
					}

					if($sc === TRUE && $this->shop_model->is_exists_zone(trim($this->input->post('zone_code'))))
					{
						$sc = FALSE;
						$this->error = "โซนซ้ำ โซนนี้ถูกใช้งานแล้ว กรุณากำหนดโซนอื่น";
					}

					$customer_code = trim($this->input->post('customer_code'));

					if($sc === TRUE)
					{
						$arr = array(
							'code' => trim($this->input->post('code')),
							'name' => trim($this->input->post('name')),
							'zone_code' => trim($this->input->post('zone_code')),
							'customer_code' => $customer_code,
							'bill_logo' => get_null(trim($this->input->post('bill_logo'))),
							'bill_header' => get_null(trim($this->input->post('bill_header'))),
							'bill_text' => get_null(trim($this->input->post('bill_text'))),
							'bill_footer' => get_null(trim($this->input->post('bill_footer'))),
							'tax_id' => get_null(trim($this->input->post('tax_id'))),
							'use_vat' => $this->input->post('use_vat'),
							'active' => $this->input->post('active')
						);

						$shop_id = $this->shop_model->add($arr);

						if($shop_id === FALSE)
						{
							$sc = FALSE;
							$error = $this->db->error();
							$this->error = $error['message'];
						}
						else
						{
							$arr = array(
								'customer_code' => $customer_code,
								'shop_id' => $shop_id
							);

							$this->shop_model->add_customer($arr);
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
		if($this->pm->can_edit)
		{
			$shop = $this->shop_model->get_by_code($code);

			if(!empty($shop))
			{
				$users = $this->shop_model->get_shop_user($shop->id);

				$ds = array(
					'shop' => $shop,
					'users' => $users
				);

				$this->load->view('masters/shop/shop_edit', $ds);
			}
			else
			{
				$this->page_error();
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

		if($this->pm->can_add)
		{
			if($this->input->post('code'))
			{
				$code = trim($this->input->post('code'));
				$old_name = trim($this->input->post('old_name'));

				if($this->input->post('name') && $this->input->post('zone_code'))
				{

					if($sc === TRUE && $this->shop_model->is_exists_name(trim($this->input->post('name')), $old_name))
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อจุดขายใหม่";
					}

					if($sc === TRUE && $this->shop_model->is_exists_zone(trim($this->input->post('zone_code')), $code))
					{
						$sc = FALSE;
						$this->error = "โซนซ้ำ โซนนี้ถูกใช้งานแล้ว กรุณากำหนดโซนอื่น";
					}

					$customer_code = trim($this->input->post('customer_code'));

					$shop = $this->shop_model->get_by_code($code);

					if($sc === TRUE)
					{
						$arr = array(
							'name' => trim($this->input->post('name')),
							'zone_code' => trim($this->input->post('zone_code')),
							'customer_code' => $customer_code,
							'bill_logo' => get_null(trim($this->input->post('bill_logo'))),
							'bill_header' => get_null(trim($this->input->post('bill_header'))),
							'bill_text' => get_null(trim($this->input->post('bill_text'))),
							'bill_footer' => get_null(trim($this->input->post('bill_footer'))),
							'use_vat' => $this->input->post('use_vat'),
							'tax_id' => get_null(trim($this->input->post('tax_id'))),
							'active' => $this->input->post('active')
						);

						if(! $this->shop_model->update($code, $arr))
						{
							$sc = FALSE;
							$error = $this->db->error();
							$this->error = "Update Failed : ".$error['message'];
						}
						else
						{
							if(! $this->shop_model->is_exists_customer($shop->id, $customer_code))
							{
								$arr = array(
									'customer_code' => $customer_code,
									'shop_id' => $shop->id
								);

								$this->shop_model->add_customer($arr);
							}
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
				$transection = $this->shop_model->has_transection($code);

				if(! $transection)
				{
					if( ! $this->shop_model->delete($code))
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



	public function add_user()
	{
		$sc = TRUE;
		if($this->pm->can_edit)
		{
			$shop_id = $this->input->post('shop_id');
			$uname  = $this->input->post('uname');

			if(! is_null($shop_id))
			{
				if( ! is_null($uname))
				{
					$user = $this->user_model->get($uname);

					if(!empty($user))
					{
						$exists = $this->shop_model->is_exists_user($shop_id, $user->uname);

						if(!$exists)
						{
							$date_add = date('Y-m-d');
							$arr = array(
								'shop_id' => $shop_id,
								'uname' => $user->uname,
								'date_add' => $date_add
							);

							if($this->shop_model->add_user($arr))
							{
								$id = $this->db->insert_id();

								$data = array(
									'id' => $id,
									'uname' => $user->uname,
									'name' => $user->name,
									'date_add' => $date_add
								);

								echo json_encode($data);
							}
							else
							{
								$sc = FALSE;
								$error = $this->db->error();
								$this->error = "Insert Error : ".$error['message'];
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "User already exists";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid User Name";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing required parameter: User Name";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter: Shop ID";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		if($sc === FALSE)
		{
			echo $this->error;
		}
	}



	public function remove_user()
	{
		$sc = TRUE;
		if($this->pm->can_edit)
		{
			$id = $this->input->post('id');
			if(! is_null($id))
			{
				if(! $this->shop_model->delete_shop_user($id))
				{
					$sc = FALSE;
					$error = $this->db->error();
					$this->error = "Delete failed : ".$error['message'];
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter: ID";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
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



	public function get_user_and_name()
	{
		$txt = trim($this->input->get('term'));
		$ds = array();
		if(! is_null($txt))
		{
			if($txt !== '*')
			{
				$this->db->group_start();
				$this->db->like('uname', $txt);
				$this->db->or_like('name', $txt);
				$this->db->group_end();
			}

			$rs = $this->db->limit(20)->get('user');

			if($rs->num_rows() > 0)
			{
				foreach($rs->result() as $user)
				{
					$ds[] = $user->uname.' | '.$user->name;
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
			'shop_code',
			'shop_name',
			'shop_zone',
			'shop_customer',
			'shop_status'
		);


    clear_filter($filter);
  }

} //--- end class

 ?>
