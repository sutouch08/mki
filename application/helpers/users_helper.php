<?php
function _check_login()
{
  $CI =& get_instance();
  $uid = get_cookie('uid');
  if($uid === NULL OR $CI->user_model->verify_uid($uid) === FALSE)
  {
    redirect(base_url().'users/authentication');
  }
}


function get_permission($menu, $uid = NULL, $id_profile = NULL)
{
  $CI =& get_instance();

  $uid = $uid === NULL ? get_cookie('uid') : $uid;
  $user = $CI->user_model->get_user_by_uid($uid);
  if(empty($user))
  {
    return reject_permission();
  }

  //--- If super admin
  if($user->id_profile == -987654321)
  {
    $pm = new stdClass();
    $pm->can_view = 1;
    $pm->can_add = 1;
    $pm->can_edit = 1;
    $pm->can_delete = 1;
    $pm->can_approve = 1;
  }
  else
  {
    $pm = $CI->user_model->get_permission($menu, $uid, $user->id_profile);
    if(empty($pm))
    {
      return reject_permission();
    }
    else
    {
      if(getConfig('CLOSE_SYSTEM') == 2)
      {
        $pm->can_add = 0;
        $pm->can_edit = 0;
        $pm->can_delete = 0;
        $pm->can_approve = 0;
      }
    }
  }

  return $pm;
}


function reject_permission()
{
  $pm = new stdClass();
  $pm->can_view = 0;
  $pm->can_add = 0;
  $pm->can_edit = 0;
  $pm->can_delete = 0;
  $pm->can_approve = 0;

  return $pm;
}


function _can_view_page($can_view)
{
  if( ! $can_view)
  {
    $CI =& get_instance();
    $CI->load->view('deny_page');
    //redirect('deny_page');
  }
}


function profile_name_in($text)
{
  if($text !== '')
  {
    $CI =& get_instance();
    $CI->db->select('id');
  }
}


function select_user($uname = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('users/user_model');

  $list = $ci->user_model->get_all();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->uname.'" '.is_selected($uname, $rs->uname).'>'.$rs->uname.' : '.$rs->name.'</option>';
    }
  }

  return $sc;
}

function user_in($txt)
{
  $sc = array('0');
  $CI =& get_instance();
  $CI->load->model('users/user_model');
  $users = $CI->user_model->search($txt);

  if(!empty($users))
  {
    foreach($users as $rs)
    {
      $sc[] = $rs->uname;
    }
  }

  return $sc;
}


function get_display_name($uname)
{
  $CI =& get_instance();
  $name = $CI->user_model->get_name($uname);
  return $name;
}


function selece_profile($id = NULL)
{
	$sc = "";

	$ci =& get_instance();
	$ci->load->model('users/profile_model');
	$profiles = $ci->profile_model->get_profiles();

	if(!empty($profiles))
	{
		foreach($profiles as $rs)
		{
			$sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
		}
	}

	return $sc;
}


function user_array()
{
  $ds = [];
  $ci =& get_instance();
  $list = $ci->user_model->get_all();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds[$rs->uname] = $rs->name;
    }
  }

  return $ds;
}

 ?>
