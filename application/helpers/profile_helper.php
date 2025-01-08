<?php
function select_profile($id = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('users/profile_model');
  $profile = $CI->profile_model->get_profiles();
	$id_profile = get_cookie('id_profile');

  if(!empty($profile))
  {
    foreach($profile as $rs)
    {
			if($rs->id != '-987654321')
			{
				$sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
			}
			else
			{
				if($id_profile == $rs->id )
				{
					$sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
				}
			}

    }
  }

  return $sc;

}


 ?>
