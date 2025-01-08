<?php

	function select_shop_id($id = NULL)
	{
		$sc = '';
	  $CI =& get_instance();
	  $CI->load->model('masters/shop_model');
	  $options = $CI->shop_model->get_all();

	  if(!empty($options))
	  {
	    foreach($options as $rs)
	    {
	      $sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
	    }
	  }

	  return $sc;
	}
 ?>
