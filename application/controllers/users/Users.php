<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends PS_Controller{
	public $menu_code = 'DBUSER'; //--- Add/Edit Users
	public $menu_group_code = 'DB'; //--- System security
	public $title = 'Users';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'users/users';
		$this->load->helper('profile');
  }



  public function index()
  {
		$filter = array(
			'uname' => get_filter('user', 'user', ''),
			'dname' => get_filter('dname', 'dname', ''),
			'profile' => get_filter('profile', 'profile', 'all'),
			'status' => get_filter('status', 'status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->user_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$data = $this->user_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $data;


		$this->pagination->initialize($init);
    $this->load->view('users/users_view', $filter);
  }





  public function add_user()
  {
		$this->load->helper('saleman');
    $this->load->view('users/user_add_view');
  }


	public function edit_user($id)
	{
		$this->load->helper('saleman');
		$ds['data'] = $this->user_model->get_user($id);
		$this->load->view('users/user_edit_view', $ds);
	}


	public function reset_password($id)
	{
			$this->title = 'Reset Password';
			$data['data'] = $this->user_model->get_user($id);
			$this->load->view('users/user_reset_pwd_view', $data);
	}



	public function change_password()
	{
		if($this->input->post('user_id'))
		{
			$id = $this->input->post('user_id');
			$pwd = password_hash($this->input->post('pwd'), PASSWORD_DEFAULT);
			$rs = $this->user_model->change_password($id, $pwd);

			if($rs === TRUE)
			{
				$this->session->set_flashdata('success', 'Password changed');
			}
			else
			{
				$this->session->set_flashdata('error', 'Change password not successfull, please try again');
			}
		}

		redirect($this->home);
	}



	public function delete_user($id)
	{
		$rs = $this->user_model->delete_user($id);

		echo $rs === TRUE ? 'success' : 'fail';
	}



	public function update_user()
	{
		$sc = TRUE;

		if($this->input->post('user_id'))
		{
			$id = $this->input->post('user_id');
			$uname = trim($this->input->post('uname'));
			$dname = trim($this->input->post('dname'));
			$id_profile = get_null($this->input->post('profile'));
			$sale_code = get_null($this->input->post('sale_code'));
			$status = $this->input->post('status');

			$ds = array(
				'uname' => $uname,
				'name' => $dname,
				'id_profile' => $id_profile,
				'sale_code' => $sale_code,
				'active' => $status
			);

			if(! $this->user_model->update_user($id, $ds))
			{
				$sc = FALSE;
				$this->error = "Update user failed";
			}			
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$this->response($sc);

	}




	public function new_user()
	{
		$sc = TRUE;

		if($this->input->post('uname'))
		{
			$uname = trim($this->input->post('uname'));
			$dname = trim($this->input->post('dname'));
			$pwd = password_hash($this->input->post('pwd'), PASSWORD_DEFAULT);
			$uid = md5(uniqid());
			$id_profile = get_null($this->input->post('profile'));
			$sale_code = get_null($this->input->post('sale_code'));
			$status = $this->input->post('status');

			$ds = array(
				'uname' => $uname,
				'pwd' => $pwd,
				'name' => $dname,
				'uid' => $uid,
				'id_profile' => $id_profile,
				'sale_code' => $sale_code,
				'active' => $status
			);

			if(! $this->user_model->add($ds))
			{
				$sc = FALSE;
				$this->error = "Create user failed";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$this->response($sc);
	}




	public function valid_dname()
	{
		$dname = $this->input->get('dname');
		$id = $this->input->get('id');

		$rs = $this->user_model->is_exists_display_name($dname, $id);

		if($rs === TRUE)
		{
			echo 'exists';
		}
		else
		{
			echo 'not exists';
		}
	}



	public function valid_uname()
	{
		$uname = $this->input->get('uname');
		$id = $this->input->get('id');

		$rs = $this->user_model->is_exists_uname($uname, $id);
		if($rs === TRUE)
		{
			echo 'exists';
		}
		else
		{
			echo 'not exists';
		}
	}




	//--- Activeate suspend user by id;
	public function active_user($id)
	{
		$rs = $this->user_model->active_user($id);
		echo $rs === TRUE ? 'success' : json_encode($rs);
	}






	//--- Suspend activated user by id
	public function disactive_user($id)
	{
		$rs = $this->user_model->disactive_user($id);

		echo $rs === TRUE ? 'success' : $rs;
	}





	public function clear_filter()
	{
		$filter = array('user', 'dname', 'profile', 'status');
		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
