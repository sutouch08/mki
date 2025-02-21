<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends PS_Controller
{
	public $title = 'Welcome';
	public $menu_code = '';
	public $menu_group_code = '';
	public $error;

	public function __construct()
	{
		parent::__construct();
		_check_login();
		$this->pm = new stdClass();
		$this->pm->can_view = 1;
    $this->load->model('main_model');
		$this->load->helper('warehouse');
		$this->load->helper('product_color');
	}


	public function index()
	{
		if($this->isViewer)
		{
			redirect('view_stock');
		}
		else
		{
			$this->load->view('main_view');
		}

	}


  public function find_order()
  {
    $sc = array();
    $txt = $this->input->post('search_text');
		$warehouse = get_null($this->input->post('warehouse_code'));

    if(!empty($txt))
    {

      $limit = 1000; //--- limit result
      $list = $this->main_model->get_search_order($txt, $warehouse, $limit);

      if( ! empty($list))
      {
				$no = 1;
				$total = 0;

        foreach($list as $rs)
        {
          $arr = array(
						'no' => number($no),
            'pdCode' => $rs->product_code,
            'reference' => $rs->code,
            'qty' => number($rs->qty),
            'state' => $rs->state,
            'cusName' => $rs->customer_name,
            'empName' => $rs->user
          );

          array_push($sc, $arr);
					$total += $rs->qty;
					$no++;
        }

				$arr = array(
					'total' => number($total)
				);

				array_push($sc, $arr);
      }
      else
      {
        $arr = array('nodata' => 'nocontent');
    		array_push($sc, $arr);
      }
    }

    echo json_encode($sc);
  }



  public function get_sell_items_stock()
  {
    $sc = array();
    $txt = trim($this->input->post('search_text'));
		$warehouse = get_null($this->input->post('warehouse_code'));
    if(!empty($txt))
    {
      $list = $this->main_model->search_items_list($txt);
      if(!empty($list))
      {
        $this->load->model('stock/stock_model');
        $this->load->model('orders/orders_model');
        $this->load->model('inventory/buffer_model');
        $this->load->model('inventory/cancle_model');
        $this->load->model('inventory/transfer_model');
        $this->load->model('inventory/move_model');
        $this->load->helper('product_images');

        $useSize = 'mini';

        foreach($list as $rs)
        {
          //---	stock in zone
    			$stockLabel = '';

    			//--- จำนวนคงเหลือทั้งหมด
    			$qty = 0; //$bfQty + $cnQty + $mvQty + $trQty;

    			//---- get data from database
    			$stock_in_zone = $this->stock_model->get_stock_in_zone($rs->code, $warehouse);

    			if(!empty($stock_in_zone))
    			{
            foreach($stock_in_zone as $zone)
            {
              if($zone->qty != 0)
              {
                $name = empty($zone->name) ? $zone->code : $zone->name;
                $stockLabel .= $name.' ='.number($zone->qty).' <br/>';
                $qty += $zone->qty;
              }
            }
    			}


    			if($qty > 0)
    			{
    				$arr = array(
    					'img' => '<img src="'.get_product_image($rs->code, $useSize).'" />',
    					'pdCode' => $rs->code,
    					'pdName' => $rs->name,
    					'qty' => number($qty),
    					'stockInZone' => $stockLabel
    				);

    				array_push($sc, $arr);
    			}
        }
      }
    }

    echo json_encode($sc);
  }


	public function get_po_backlogs()
	{
		$this->load->model('purchase/po_model');
		$sc = TRUE;

		$qty = 0;

		$item_code = $this->input->post('item_code');

		$rs = $this->db
		->select_sum('pd.qty')
		->select_sum('pd.received')
		->from('po_detail AS pd')
		->join('po AS po', 'pd.po_code = po.code', 'left')
		->where('product_code', $item_code)
		->where_in('po.status', [1, 2])
		->where('pd.valid', 0)
		->where('pd.qty >', 'pd.received', FALSE)
		->get();

		if($rs->num_rows() === 1)
		{
			$qty = $rs->row()->qty - $rs->row()->received;
		}

		echo $qty;
	}


	public function get_do_backlogs()
	{
		$qty = 0;

		$item_code = $this->input->post('item_code');

		$this->load->model('orders/orders_model');

		$qty = $this->orders_model->get_reserv_stock($item_code);

		echo round($qty);
	}
} //--- end class
