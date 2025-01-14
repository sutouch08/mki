<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Warehouse extends PS_Controller
{
  public $menu_code = 'DBWRHS';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข คลังสินค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/warehouse';
    $this->load->model('masters/warehouse_model');
    $this->load->helper('warehouse');
    $this->title = label_value('DBWRHS');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'wh_code', ''),
      'name' => get_filter('name', 'wh_name', ''),
      'role' => get_filter('role', 'wh_role', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->warehouse_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->warehouse_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $rs->zone_count = $this->warehouse_model->count_zone($rs->code);
      }
    }

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/warehouse/warehouse_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/warehouse/warehouse_add');
  }



  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if($this->pm->can_add)
    {
      if( ! empty($ds))
      {
        if( ! $this->warehouse_model->is_exists_code($ds->code))
        {
          if( ! $this->warehouse_model->is_exists_name($ds->name))
          {
            $arr = array(
              'code' => $ds->code,
              'name' => trim($ds->name),
              'role' => $ds->role,
              'sell' => $ds->sell == 0 ? 0 : 1,
              'prepare' => $ds->prepare == 0 ? 0 : 1,
              'auz' => $ds->auz == 1 ? 1 : 0,
              'active' => $ds->active == 0 ? 0 : 1,
              'update_user' => $this->_user->uname
            );

            if( ! $this->warehouse_model->add($arr))
            {
              $sc = FALSE;
              $this->error = get_error_message('update');
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = get_error_message('exists', $ds->name);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = get_error_message('exists', $ds->code);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('required');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }



  public function edit($code)
  {
    if($this->pm->can_edit)
    {
      $ds['ds'] = $this->warehouse_model->get($code);
      $this->load->view('masters/warehouse/warehouse_edit', $ds);
    }
    else
    {
      set_error(label_value('no_permission'));
      redirect($this->home);
    }
  }



  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if($this->pm->can_edit)
    {
      if( ! empty($ds))
      {
        if( ! $this->warehouse_model->is_exists_name($ds->name, $ds->code))
        {
          $arr = array(
            'name' => trim($ds->name),
            'role' => $ds->role,
            'sell' => $ds->sell == 0 ? 0 : 1,
            'prepare' => $ds->prepare == 0 ? 0 : 1,
            'auz' => $ds->auz == 1 ? 1 : 0,
            'active' => $ds->active == 0 ? 0 : 1,
            'update_user' => $this->_user->uname
          );

          if( ! $this->warehouse_model->update($ds->code, $arr))
          {
            $sc = FALSE;
            $this->error = get_error_message('update');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = get_error_message('exists', $ds->name);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('required');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function delete($code)
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      //---- count member if exists reject action
      if($this->warehouse_model->has_zone($code))
      {
        $sc = FALSE;
        $this->error = label_value('child_inside');
      }
      else
      {
        if($this->warehouse_model->delete($code) === FALSE)
        {
          $sc = FALSE;
          $this->error = label_value('delete_fail');
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = label_value('no_permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function is_exists_code($code, $old_code = NULL)
  {
    $exists = $this->warehouse_model->is_exists_code($code, $old_code);
    if($exists)
    {
      echo label_value('duplicated_code');
    }
    else
    {
      echo 'ok';
    }
  }


  public function is_exists_name($name, $old_name = NULL)
  {
    $exists = $this->warehouse_model->is_exists_name($name, $old_name);
    if($exists)
    {
      echo label_value('duplicated_name');
    }
    else
    {
      echo 'ok';
    }
  }



  public function clear_filter()
  {
    $filter = array('wh_code', 'wh_name', 'wh_role');
    clear_filter($filter);
  }

} //--- end class

 ?>
