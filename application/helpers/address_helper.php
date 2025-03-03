<?php
function get_address_form($adds, $sds, $ds, $dd, $use_qc)
{
  $sc = 'no_address';
    //--- มีที่อยู่เดียว และผู้จัดส่งเดียว
    if($adds == 1 && $sds == 1 )
    {
      $sc = 1;
    }
		//
    // //--- มีที่อยู่ แต่ ไม่มีผู้จัดส่ง
    // else if( $adds >= 1 && $sds < 1 )
    // {
		//
    //   $sc  = 'no_sender';
		//
    // }
    //--- มีที่อยู่มากกว่า 1 หรือ ผู้จัดส่งมากกว่า 1
    else
    {
      //--- มีที่อยู่มากกว่า 1 ที่
      if( $adds >= 1 )
      {
        $add  = '<tr>';
        $add .=   '<td colspan="2">';
        $add .=     '<strong>เลือกที่อยู่สำหรับจัดส่ง</strong>';
        $add .=   '</td>';
        $add .= '<tr>';

        $n    = 1;
        if(!empty($ds))
        {
          foreach($ds as $rs)
          {
            $se = $n == 1 ? 'checked' : '';
            $add .= '<tr>';
            $add .=   '<td class="width-35 middle">';
            $add .=     '<label>';
            $add .=       '<input type="radio" class="ace" name="id_address" value="'.$rs->id.'" '.$se.' />';
            $add .=       '<span class="lbl">&nbsp;&nbsp;'.$rs->alias.'</span>';
            $add .=     '</label>';
            $add .=   '</td>';
            $add .=   '<td style="white-space:normal;">';
            $add .=     $rs->address.'  ต. '.$rs->sub_district.' อ. '.$rs->district.' จ. '.$rs->province;
            $add .=   '</td>';
            $add .= '</tr>';
            $n++;
          }
        }
      }

      $dds = '';
      //--- มีผู้จัดส่งมากกว่า 1
      if( $sds >= 1 )
      {
        $dds  = '<tr>';
        $dds .=   '<td colspan="2">';
        $dds .=     '<strong>เลือกผู้ให้บริการจัดส่ง</strong>';
        $dds .=   '</td>';
        $dds .= '</tr>';


        //--- กำหนดให้มีผู้จัดส่งได้ไม่เกิน 3 รายเท่านั้น
        if(!empty($dd))
        {
          //--- ผู้จัดส่งรายหลัก
          $dds .= '<tr >';
          $dds .=   '<td colspan="2" style="white-space:normal;">';
          $dds .=     '<label>';
          $dds .=       '<input type="radio" class="ace" name="id_sender" value="'.$dd->main_sender.'" checked />';
          $dds .=       '<span class="lbl">&nbsp;&nbsp; '.$dd->main.'</span>'; //---  transport_helper
          $dds .=     '</label>';
          $dds .=   '</td>';
          $dds .= '</tr>';


          //--- รายที่ 2
          if(!empty($dd->second_sender))
          {
            $dds .= '<tr>';
            $dds .=   '<td colspan="2">';
            $dds .=     '<label>';
            $dds .=       '<input type="radio" class="ace" name="id_sender" value="'.$dd->second_sender.'" />';
            $dds .=       '<span class="lbl">&nbsp;&nbsp; '.$dd->second.'</span>'; //---  transport_helper
            $dds .=     '</label>';
            $dds .=   '</td>';
            $dds .= '</tr>';
          }


          //--- รายที่ 3
          if(!empty($dd->third_sender))
          {
            $dds .= '<tr>';
            $dds .=   '<td colspan="2">';
            $dds .=     '<label>';
            $dds .=       '<input type="radio" class="ace" name="id_sender" value="'.$dd->third_sender.'" />';
            $dds .=       '<span class="lbl">&nbsp;&nbsp; '.$dd->third.'</span>'; //---  transport_helper
            $dds .=     '</label>';
            $dds .=   '</td>';
            $dds .= '</tr>';
          }

        } //--- end if $ds
      }

			if(! $use_qc)
			{
				$dds .= '<tr>';
	      $dds .=   '<td colspan="2">';
	      $dds .=     '<label>';
	      $dds .=       '<span class="lbl">จำนวนกล่อง </span>'; //---  transport_helper
	      $dds .=       '<input type="number" class="form-control input-sm input-mini" name="print_qty" value="1" />';
	      $dds .=     '</label>';
	      $dds .=   '</td>';
	      $dds .= '</tr>';
			}


      //--- ประกอบร่าง
      if( $adds >= 1 )//&& $sds >= 1 )
      {
        $sc = '<table class="table table-bordered">';
        $sc .= $add;
        $sc .= $dds;
        $sc .= '</table>';
      }
    }


  return $sc;
}


function parse_address(array $ds = array())
{
	$adr = "";

	if( ! empty($ds))
	{
    $province = parseProvince($ds['province']);
    $district = parseDistrict($ds['district'], $province);
    $subDistrict = parseSubDistrict($ds['sub_district'], $province);

    $adr .= $ds['address'];
    $adr .= (empty($subDistrict) ? "" : " {$subDistrict}") . (empty($district) ? "" : " {$district}") . (empty($province) ? "" : " {$province}");
    $adr .= (empty($ds['postcode']) ? "" : " {$ds['postcode']}");
	}

	return $adr;
}


function parsePhoneNumber($phone, $length = 10)
{
	$find = [" ", "-", "+"];
  $rep = ["", "", ""];
	$length = $length * -1;

  if($phone != "")
  {
    $phone = trim($phone);
    $phone = str_replace($find, $rep, $phone);
    $phone = substr($phone, $length);

    return $phone;
  }

  return NULL;
}

function parseSubDistrict($ad, $province)
{
	if(! empty($ad))
	{
		if(isBangkok($province))
		{
			$find = [' ', 'แขวง'];
			$rep = ['', ''];
			$ad = str_replace($find, $rep, $ad);
			return substr_replace($ad, 'แขวง', 0, 0);
		}
		else
		{
			$find = [' ', 'ต.', 'ตำบล'];
			$rep = ['', '', ''];
			$ad = str_replace($find, $rep, $ad);
			return substr_replace($ad, 'ตำบล', 0, 0);
		}

	}

	return NULL;
}


function parseDistrict($ad, $province)
{
	if(! empty($ad))
	{
		if(isBangkok($province))
		{
			$find = [' ', 'เขต'];
			$rep = ['', ''];
			$ad = str_replace($find, $rep, $ad);
			return substr_replace($ad, 'เขต', 0, 0);
		}
		else
		{
			$find = [' ', 'อ.', 'อำเภอ'];
			$rep = ['', '', ''];
			$ad = str_replace($find, $rep, $ad);
			return substr_replace($ad, 'อำเภอ', 0, 0);
		}
	}

	return NULL;
}


function parseProvince($ad)
{
	if(! empty($ad))
	{
		$find = [' ', 'จ.', 'จังหวัด', '.'];
		$rep = ['', '', '', ''];
		$ad = str_replace($find, $rep, $ad);
		$ad = substr_replace($ad, 'จังหวัด', 0, 0);

		if(isBangkok($ad))
		{
			$ad = 'จังหวัดกรุงเทพมหานคร';
		}

		return $ad;
	}

	return NULL;
}


function isBangkok($province)
{
	$list = array(
		'จังหวัดกรุงเทพมหานคร',
		'จังหวัดกรุงเทพ',
		'จังหวัดกรุงเทพฯ',
		'จ.กรุงเทพมหานคร',
		'จ.กรุงเทพ',
		'จ.กรุงเทพฯ',
		'กรุงเทพ',
		'กรุงเทพฯ',
		'กรุงเทพมหานคร',
		'กทม',
		'กทม.',
		'ก.ท.ม.'
	);

	if( ! empty($province))
	{
		foreach($list as $val)
		{
			if($province == $val)
			{
				return TRUE;
			}
		}
	}

	return FALSE;
}

 ?>
