<?php
function setToken($token)
{
	$CI =& get_instance();
	$cookie = array(
		'name' => 'file_download_token',
		'value' => $token,
		'expire' => 3600,
		'path' => '/'
	);

	return $CI->input->set_cookie($cookie);
}


//---	ตัดข้อความแล้วเติม ... ข้างหลัง
function limitText($str, $length)
{
	$txt = '...';
	if( strlen($str) >= $length)
	{
		return mb_substr($str, 0, $length).$txt;
	}
	else
	{
		return $str;
	}
}




function is_selected($val, $select)
{
  return $val === $select ? 'selected' : '';
}




function is_checked($val1, $val2)
{
  return $val1 == $val2 ? 'checked' : '';
}



function is_active($val)
{
  return ($val == 1 OR $val == '1' OR $val == 'Y' OR $val == 'y') ? '<i class="fa fa-check green"></i>' : '<i class="fa fa-times red"></i>';
}




function get_filter($postName, $cookieName, $defaultValue = "")
{
  $CI =& get_instance();
  $sc = '';

  if($CI->input->post($postName) !== NULL)
  {
    $sc = $CI->input->post($postName);
    $CI->input->set_cookie(array('name' => $cookieName, 'value' => $sc, 'expire' => 3600 , 'path' => '/'));
  }
  else if($CI->input->cookie($cookieName) !== NULL)
  {
    $sc = $CI->input->cookie($cookieName);
  }
  else
  {
    $sc = $defaultValue;
  }

	return $sc;
}




function clear_filter($cookies)
{
  if(is_array($cookies))
  {
    foreach($cookies as $cookie)
    {
      delete_cookie($cookie);
    }
  }
  else
  {
    delete_cookie($cookies);
  }
}




function set_rows($value = 20)
{
  $value = $value > 300 ? 300 : $value;

  $arr = array(
    'name' => 'rows',
    'value' => $value,
    'expire' => 259200,
    'path' => '/'
  );
  $CI =& get_instance();
  return $CI->input->set_cookie($arr);
}





function get_rows()
{
  $CI =& get_instance();
  return $CI->input->cookie('rows') === NULL ? 20 : $CI->input->cookie('rows');
}




function number($val, $digit = 0)
{
  return number_format($val, $digit);
}


function qty_format($val, $digit = 0)
{
	if (floatval(intval($val)) === floatval($val)) {
        // The number is an integer. Remove all the decimals
        return number_format($val, 0);
    }

  return number_format($val, $digit);
}





function ac_format($val, $digit = 0)
{
	return $val == 0 ? '-' : number_format($val, $digit);
}


function getConfig($code)
{
  $CI =& get_instance();
  $rs = $CI->db->select('value')->where('code', $code)->get('config');
  if($rs->num_rows() == 1)
  {
    return $rs->row()->value;
  }

	return NULL;
}



function get_vat_amount($amount, $vat = NULL, $type = NULL)
{
	$vat = $vat === NULL ? getConfig('SALE_VAT_RATE') : $vat;
	$type = $type === NULL ? getConfig('DEFAULT_VAT_TYPE') : $type;

	if($amount > 0 && $vat > 0)
	{
		if($type == 'I')
		{
			$re_vat = ($amount * $vat) / (100+$vat);
			return round($re_vat,2);
		}
		else
		{
			$re_vat = $amount * ($vat * 0.01);
			return round($re_vat, 2);
		}
	}

	return round($amount, 2);
}



function remove_vat($amount, $vat = NULL)
{
	if($vat === NULL)
	{
		$vat = getConfig('SALE_VAT_RATE'); //-- 7
	}

	if( $vat != 0 )
	{
		$re_vat	= ($vat + 100) / 100;
		return round($amount/$re_vat, 2);
	}

	return round($amount, 2);
}

//---- remove discount percent return price after discount
function get_price_after_discount($price, $disc = 0)
{
	$find = array('%', ' ');
	$replace = array('', '');
	$disc = str_replace($find, $replace, $disc);

	if($disc > 0 && $disc <= 100)
	{
		$price = $price - ($price *($disc * 0.01));
	}

	return $price;
}


//--- return discount amount calculate from price and discount percentage
function get_discount_amount($price, $disc = 0)
{
	$find = array('%', ' ');
	$replace = array('', '');
	$disc = str_replace($find, $replace, $disc);

	if($disc > 0 && $disc <= 100)
	{
		$amount = $price * ($disc * 0.01);
	}
	else
	{
		$amount = 0;
	}

	return $amount;
}




function add_vat($amount, $vat = NULL)
{
	if($vat === NULL)
	{
		$vat = getConfig('SALE_VAT_RATE'); //-- 7
	}

	if( $vat != 0 )
	{
		$re_vat = $vat * 0.01;
		return round(($amount * $re_vat) + $amount, 2);
	}

	return round($amount, 2);
}



function set_error($message)
{
  $CI =& get_instance();
  $CI->session->set_flashdata('error', $message);
}


function set_message($message)
{
  $CI =& get_instance();
  $CI->session->set_flashdata('success', $message);
}


function get_error_message($key, $name = "data")
{
	$error = array(
		'insert' => "Insert {$name} failed.",
		'update' => "Update {$name} failed.",
		'delete' => "Delete {$name} failed.",
		'permission' => "You don't have permission to perform this operation.",
		'required' => "Missing required parameter.",
		'exists' => "'{$name}' already exists.",
		'status' => "Invalid document status",
		'notfound' => "Document number or Data not found",
		'transection' => "Unable to delete {$name} because transections exists or link to other module.",
		'duplicated' => "Duplicated {$name} already exists."
	);

	return (!empty($error[$key]) ? $error[$key] : "Unknow error.");
}



function label($content)
{
	$ci =& get_instance();
	echo $ci->lang->line($content);
}

function label_value($content)
{
	$ci =& get_instance();
	return $ci->lang->line($content);
}



//--- return null if blank value
function get_null($value)
{
	return $value === '' ? NULL : $value;
}


//--- return  0 if blank or NULL
function get_zero($value)
{
	return ($value === '' OR $value === NULL) ? 0 : $value;
}

//--- return TRUE if value ==  1 else return FALSE;
function is_true($value)
{
	if($value === 1 OR $value === '1' OR $value === TRUE)
	{
		return TRUE;
	}

	return FALSE;
}


function get_auz()
{
	$auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
	return $auz;
}


function pagination_config( $base_url, $total_rows = 0, $perpage = 20, $segment = 3)
{
    $rows = get_rows();
    $input_rows  = '<p class="pull-right pagination hidden-xs">';
    $input_rows .= 'ทั้งหมด '.number($total_rows).' รายการ | แสดง';
    $input_rows .= '<input type="number" name="set_rows" id="set_rows" class="input-mini text-center margin-left-15 margin-right-10" value="'.$rows.'" />';
    $input_rows .= 'ต่อหน้า ';
    $input_rows .= '<buton class="btn btn-success btn-xs" type="button" onClick="set_rows()">แสดง</button>';
    $input_rows .= '</p>';

		$config['full_tag_open'] 		= '<nav><ul class="pagination">';
		$config['full_tag_close'] 		= '</ul>'.$input_rows.'</nav><hr class="padding-5">';
		$config['first_link'] 				= 'First';
		$config['first_tag_open'] 		= '<li>';
		$config['first_tag_close'] 		= '</li>';
		$config['next_link'] 				= 'Next';
		$config['next_tag_open'] 		= '<li>';
		$config['next_tag_close'] 	= '</li>';
		$config['prev_link'] 			= 'prev';
		$config['prev_tag_open'] 	= '<li>';
		$config['prev_tag_close'] 	= '</li>';
		$config['last_link'] 				= 'Last';
		$config['last_tag_open'] 		= '<li>';
		$config['last_tag_close'] 		= '</li>';
		$config['cur_tag_open'] 		= '<li class="active"><a href="#">';
		$config['cur_tag_close'] 		= '</a></li>';
		$config['num_tag_open'] 		= '<li>';
		$config['num_tag_close'] 		= '</li>';
		$config['uri_segment'] 		= $segment;
		$config['per_page']			= $perpage;
		$config['total_rows']			= $total_rows != false ? $total_rows : 0 ;
		$config['base_url']				= $base_url;
		return $config;
}


function convert($txt)
{
	//return iconv('UTF-8', 'CP850', $txt);
	return $txt;
}

define('BAHT_TEXT_NUMBERS', array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'));
define('BAHT_TEXT_UNITS', array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'));
define('BAHT_TEXT_ONE_IN_TENTH', 'เอ็ด');
define('BAHT_TEXT_TWENTY', 'ยี่');
define('BAHT_TEXT_INTEGER', 'ถ้วน');
define('BAHT_TEXT_BAHT', 'บาท');
define('BAHT_TEXT_SATANG', 'สตางค์');
define('BAHT_TEXT_POINT', 'จุด');

function baht_text($number, $include_unit = true, $display_zero = true)
{


	/**
	 * Convert baht number to Thai text
	 * @param double|int $number
	 * @param bool $include_unit
	 * @param bool $display_zero
	 * @return string|null
	 */


    if (!is_numeric($number)) {
        return null;
    }

    $log = floor(log($number, 10));
    if ($log > 5) {
        $millions = floor($log / 6);
        $million_value = pow(1000000, $millions);
        $normalised_million = floor($number / $million_value);
        $rest = $number - ($normalised_million * $million_value);
        $millions_text = '';
        for ($i = 0; $i < $millions; $i++) {
            $millions_text .= BAHT_TEXT_UNITS[6];
        }
        return baht_text($normalised_million, false) . $millions_text . baht_text($rest, true, false);
    }

    $number_str = (string)floor($number);
    $text = '';
    $unit = 0;

    if ($display_zero && $number_str == '0') {
        $text = BAHT_TEXT_NUMBERS[0];
    } else for ($i = strlen($number_str) - 1; $i > -1; $i--) {
        $current_number = (int)$number_str[$i];

        $unit_text = '';
        if ($unit == 0 && $i > 0) {
            $previous_number = isset($number_str[$i - 1]) ? (int)$number_str[$i - 1] : 0;
            if ($current_number == 1 && $previous_number > 0) {
                $unit_text .= BAHT_TEXT_ONE_IN_TENTH;
            } else if ($current_number > 0) {
                $unit_text .= BAHT_TEXT_NUMBERS[$current_number];
            }
        } else if ($unit == 1 && $current_number == 2) {
            $unit_text .= BAHT_TEXT_TWENTY;
        } else if ($current_number > 0 && ($unit != 1 || $current_number != 1)) {
            $unit_text .= BAHT_TEXT_NUMBERS[$current_number];
        }

        if ($current_number > 0) {
            $unit_text .= BAHT_TEXT_UNITS[$unit];
        }

        $text = $unit_text . $text;
        $unit++;
    }

    if ($include_unit) {
        $text .= BAHT_TEXT_BAHT;

        $satang = explode('.', number_format($number, 2, '.', ''))[1];
        $text .= $satang == 0
            ? BAHT_TEXT_INTEGER
            : baht_text($satang, false) . BAHT_TEXT_SATANG;
    } else {
        $exploded = explode('.', $number);
        if (isset($exploded[1])) {
            $text .= BAHT_TEXT_POINT;
            $decimal = (string)$exploded[1];
            for ($i = 0; $i < strlen($decimal); $i++) {
                $text .= BAHT_TEXT_NUMBERS[$decimal[$i]];
            }
        }
    }

    return $text;
}

 ?>
