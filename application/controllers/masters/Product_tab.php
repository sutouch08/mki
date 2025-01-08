<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_tab extends PS_Controller
{
  public $menu_code = 'DBPTAB';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'แถบแสดงสินค้า';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_tab';
    //--- load model
    $this->load->model('masters/product_tab_model');
    $this->load->helper('product_tab');
  }


  public function index()
  {
    $filter = array(
      'tab_name' => get_filter('tab_name', 'tab_name', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->product_tab_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$tabs = $this->product_tab_model->get_list($filter, $perpage, $this->uri->segment($segment));
    if(!empty($tabs))
    {
      foreach($tabs as $rs)
      {
        $rs->members = $this->product_tab_model->countMember($rs->id);
      }
    }

    $filter['tabs'] = $tabs;
		$this->pagination->initialize($init);
    $this->load->view('masters/product_tab/product_tab_view', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/product_tab/product_tab_add');
  }


  public function add()
  {
		$sc = TRUE;
		$name = trim($this->input->post('name'));
		if($name !== NULL)
		{
			if($this->product_tab_model->isExists('name', $name))
			{
				$sc = FALSE;
				$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
			}
			else
			{
				$arr = array(
					'name' => $name,
					'id_parent' => 0
				);

				if(! $this->product_tab_model->add($arr))
				{
					$sc = FALSE;
					$this->error = "เพิ่มรายการไม่สำเร็จ";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : name";
		}

		$this->response($sc);
  }


  public function edit($id)
  {
    $ds = $this->product_tab_model->get($id);
		if(!empty($ds))
		{
			$this->load->view('masters/product_tab/product_tab_edit', $ds);
		}
		else
		{
			$this->error_page();
		}
  }


  public function update()
  {
		$sc = TRUE;
		$id = $this->input->post('id');
		$name = trim($this->input->post('name'));

		if(!empty($id) && $name !== NULL)
		{
			if($this->product_tab_model->isExists('name', $name, $id))
			{
				$sc = FALSE;
				$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
			}
			else
			{
				$arr = array('name' => $name);

				if(!$this->product_tab_model->update($id, $arr))
				{
					$sc = FALSE;
					$this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parater";
		}

		$this->response($sc);
  }


  public function delete($id)
  {
    $sc = TRUE;

    $this->db->trans_begin();
		if(! $this->product_tab_model->delete_tab_style($id))
		{
			$sc = FALSE;
			$this->error = "ลบรุ่นสินค้าในแถบแสดงสินค้าไม่สำเร็จ";
		}

		if($sc === TRUE)
		{
			if(!$this->product_tab_model->delete_tab_item($id))
			{
				$sc = FALSE;
				$this->error = "ลบสินค้าในแถบแสดงสินค้าไม่สำเร็จ";
			}
		}

		if($sc === TRUE)
		{
			if(!$this->product_tab_model->delete($id))
			{
				$sc = FALSE;
				$this->error = "ลบแถบแสดงสินค้าไม่สำเร็จ";
			}
		}

		if($sc === TRUE)
		{
			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}

		$this->response($sc);
  }


  public function clear_filter()
  {
    $filter = array('tab_name');
    clear_filter($filter);
  }


}//--- end class
?>
