<?php
class Suspended extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    //--- get permission for user
  }


  public function index()
  {
		$system_start_date = getConfig('SYSTEM_START_DATE');
		$system_end_date = getConfig('SYSTEM_END_DATE');
		$stop_days = getConfig('SYSTEM_END_AFTER_DAYS');

		$ssd = db_date($system_start_date);
		$sed = db_date($system_end_date);
		$today = today();
		$stop_date = date('Y-m-d', strtotime("+{$stop_days} day", strtotime($sed)));

		if($stop_date >= $today && $ssd <= $today)
		{
			redirect(base_url().'main');
			exit;
		}

		if($ssd > $today)
		{
			$ds['text'] = "บัญชีของคุณยังไม่เปิดใช้งาน คุณสามารถใช้งานได้ในวันที่ ".thai_date($ssd);
		}

		if($sed < $today)
		{
			$ds['text'] = "บัญชีของคุณถูกระงับการใช้งาน เนื่องจากสัญญาการให้บริการสิ้นสุดลง โปรดติดต่อตัวแทนผู้ให้บริการเพื่อชำระค่าบริการและเปิดใช้งานระบบอีกครั้ง";
		}

    $this->load->view('suspended', $ds);
  }

}


 ?>
