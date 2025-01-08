<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PS_Controller extends CI_Controller
{
  public $pm;
  public $home;
  public $close_system;
  public $isViewer;
	public $_SuperAdmin = FALSE;
	public $_user;
	public $error;
	public $_use_vat;
	public $show_warning = FALSE;

  public function __construct()
  {
    parent::__construct();

    //--- check is user has logged in ?
    _check_login();

    $this->close_system   = getConfig('CLOSE_SYSTEM'); //--- ปิดระบบทั้งหมดหรือไม่

    if($this->close_system == 1)
    {
      redirect('setting/maintenance');
    }

		$this->_use_vat = getConfig('USE_VAT') == 1 ? TRUE : FALSE;
    $uid = get_cookie('uid');
		$this->_user = $this->user_model->get_user_by_uid($uid);

    $this->isViewer = $this->_user->is_viewer == 1 ? TRUE : FALSE;
		$this->_SuperAdmin = $this->_user->id_profile == -987654321 ? TRUE : FALSE;

		$system_start_date = getConfig('SYSTEM_START_DATE');
		$system_end_date = getConfig('SYSTEM_END_DATE');
		$this->system_end_date = $system_end_date;

		if(!empty($system_start_date) && !empty($system_end_date))
		{
			$ssd = db_date($system_start_date, FALSE);
			$sed = db_date($system_end_date, FALSE);
			$warning_days = getConfig('SYSTEM_WARNING_DAYS');
			$warning_date = date('Y-m-d', strtotime("-{$warning_days} day", strtotime($sed)));
			$today = today();
			if($warning_date <= $today)
			{
				$this->show_warning = TRUE;
			}


			if(($ssd > $today) OR ($sed < $today))
			{
				if($sed < $today)
				{

					$stop_days = getConfig('SYSTEM_END_AFTER_DAYS');

					$stop_date = date('Y-m-d', strtotime("+{$stop_days} day", strtotime($sed)));

					if($stop_date < $today) //--- ถ้าเกินวันที่ปล่อยให้เลยได้
					{
						if(! $this->_SuperAdmin)
						{
							redirect('setting/suspended');
						}
					}
				}

				if($ssd > $today)
				{
					if(! $this->_SuperAdmin)
					{
						redirect('setting/suspended');
					}
				}
			}
		}

    //--- get permission for user
    $this->pm = get_permission($this->menu_code, $uid, get_cookie('id_profile'));

    $language = getConfig('LANGUAGE');
    $display_lang = get_cookie('display_lang');
    $this->language = empty($display_lang) ? 'thai' : $display_lang;
    $this->lang->load($this->language, $this->language);

  }


	public function response($sc = TRUE)
	{
		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function deny_page()
  {
    return $this->load->view('deny_page');
  }


  public function error_page($err = NULL)
  {
		$error = array('error_message' => $err);
    return $this->load->view('page_error', $error);
  }

	public function page_error($err = NULL)
  {
		$error = array('error_message' => $err);
    return $this->load->view('page_error', $error);
  }
}

?>
