<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Address extends PS_Controller
{
  public $menu_code = 'DBADDR';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'ที่อยู่จัดส่ง';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/address';
    $this->load->model('address/address_model');
    $this->load->model('address/transport_model');
    $this->load->model('masters/customers_model');
  }


  public function get_online_address()
  {
    $this->load->model('orders/orders_model');
    $order_code = $this->input->get('order_code');
    $customer_ref = $this->input->get('customer_ref');
    $order = $this->orders_model->get($order_code);
    if(!empty($order->address_id))
    {
      echo $order->address_id;
    }
    else
    {
      $rs = $this->address_model->get_default_address($customer_ref);
      if(!empty($rs))
      {
        echo $rs->id;
      }
      else
      {
        echo 'noaddress';
      }
    }

  }



  public function print_online_address($id, $code)
  {
    $this->load->model('orders/orders_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/sender_model');
    $this->load->library('printer');
    $adr = $this->address_model->get_shipping_detail($id);
    $order = $this->orders_model->get($code);
    if(!empty($order))
    {
      $order->total_qty = $this->invoice_model->get_total_sold_qty($code);
      $order->payment_role = $this->payment_methods_model->get_role($order->payment_code);
      $order->sender_name = $this->sender_model->get_name($order->sender_id);
    }

    $details = $order->state == 8 ? $this->invoice_model->get_details($code) : FALSE;

    if(!empty($adr))
    {
			$sub_district = empty($adr->sub_district) ? '' : 'ต.'.$adr->sub_district;
			$district = empty($adr->district) ? '' : ' อ. '.$adr->district;
      $ds = array(
        'order' => $order,
        'details' => $details,
        'cusName' => $adr->name,
        'cusAdr1' => $adr->address,
        'cusAdr2' => ($sub_district . $district),
        'cusProv' => empty($adr->province) ? '' : ('จ. '.$adr->province),
        'cusPostCode' => $adr->postcode,
        'cusPhone' => $adr->phone,
        'cusCode' => $order->customer_code,
        'cName' => getConfig('COMPANY_FULL_NAME'),
        'cAddress' => getConfig('COMPANY_ADDRESS1').'<br>'.getConfig('COMPANY_ADDRESS2'),
        'cPhone' => getConfig('COMPANY_PHONE'),
        'cPostCode' => getConfig("COMPANY_POST_CODE")
      );

      $this->load->view('print/print_address_online_sheet', $ds);
    }
  }



  public function print_address_sheet($code, $customer_code, $id_address=NULL, $id_sender=NULL, $box_count = 1)
  {
    $this->load->library('printer');
    $this->load->model('inventory/qc_model');
    $id_address = empty($id_address) ? $this->address_model->get_id($customer_code) : $id_address;
    $id_sender = empty($id_sender) ? $this->transport_model->get_id($customer_code) : $id_sender;
		$boxes = getConfig('USE_QC') == 1 ? $this->qc_model->count_box($code) : $box_count;
    $ds = array(
      'reference' => $code,
      'boxes' => $boxes == 0 ? 1 : $boxes,
      'ad' => $this->address_model->get_shipping_detail($id_address),
      'sd' => $this->transport_model->get_sender($id_sender),
      'cName' => getConfig('COMPANY_FULL_NAME'),
      'cAddress' => getConfig('COMPANY_ADDRESS1').'<br>'.getConfig('COMPANY_ADDRESS2'),
      'cPostCode' => getConfig('COMPANY_POST_CODE'),
      'cPhone' => getConfig('COMPANY_PHONE')
    );

    $this->load->view('print/print_address_sheet', $ds);
  }


  public function get_address_form()
  {
    $this->load->helper('address');
    $customer_code = $this->input->post('customer_code');
    if($customer_code)
    {
      //--- จำนวนที่อยู่
      $adn = $this->address_model->count_address($customer_code);
      //--- จำนวนผู้จัดส่ง
      $sdn = $this->transport_model->count_sender($customer_code);
      //--- ที่อยู่ลูกค้าทั้งหมด
      $adrs = $adn > 0 ? $this->address_model->get_shipping_address($customer_code) : FALSE;
      //--- รายชื่อผู้ให้บริการจัดส่ง
      $senders = $sdn > 0 ? $this->transport_model->get_senders($customer_code) : FALSE;

      if(!empty($senders))
      {
        $senders->main = $this->transport_model->get_name($senders->main_sender);
        $senders->second = $this->transport_model->get_name($senders->second_sender);
        $senders->third = $this->transport_model->get_name($senders->third_sender);
      }

			$use_qc = getConfig('USE_QC');
      echo get_address_form($adn, $sdn, $adrs, $senders, $use_qc);
    }
    else
    {
      echo 'noaddress';
    }
  }



	function get_customer_bill_to_address()
	{
		$this->load->model('address/customer_address_model');
		$customer_code = trim($this->input->get('customer_code'));

		$rs = $this->customer_address_model->get_customer_bill_to_address($customer_code);
		if(!empty($rs))
		{
			$sub_prefix = $rs->province === 'กรุงเทพมหานคร' ? 'แขวง' : 'ต.';
			$prefix = $rs->province === 'กรุงเทพมหานคร' ? 'เขต' : 'อ.';
			$address  = $rs->address.' ';
			$address .= $sub_prefix . $rs->sub_district.' ';
			$address .= $prefix . $rs->district.' ';
			$address .= 'จ.'.$rs->province.' '.$rs->postcode;

			$arr = array(
				'branch_code' => $rs->branch_code,
				'branch_name' => $rs->branch_name,
				'address' => $address,
				'phone' => $rs->phone
			);

			echo json_encode($arr);
		}
		else
		{
			echo "no_address";
		}
	}


	public function save_address()
  {
    $sc = TRUE;
    if($this->input->post('customer_ref'))
    {
      $this->load->model('address/address_model');
      $id = $this->input->post('id_address');

      $arr = array(
        'code' => trim($this->input->post('customer_ref')),
				'customer_code' => get_null($this->input->post('customer_code')),
        'name' => trim($this->input->post('name')),
        'address' => trim($this->input->post('address')),
        'sub_district' => trim($this->input->post('sub_district')),
        'district' => trim($this->input->post('district')),
        'province' => trim($this->input->post('province')),
        'postcode' => trim($this->input->post('postcode')),
        'phone' => trim($this->input->post('phone')),
        'email' => trim($this->input->post('email')),
        'alias' => trim($this->input->post('alias'))
      );

      if(!empty($id))
      {
        $rs = $this->address_model->update_shipping_address($id, $arr);
      }
      else
      {
        $rs = $this->address_model->add_shipping_address($arr);
      }

      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'เพิ่มที่อยู่ไม่สำเร็จ';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไมพบชื่อลูกค้าออนไลน์';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function get_address_table()
  {
    $sc = TRUE;
		$ds = array();

    if($this->input->post('customer_ref'))
    {
      $code = $this->input->post('customer_ref');

      if(! empty($code))
      {
        $adrs = $this->address_model->get_ship_to_address($code);

        if(!empty($adrs))
        {
          foreach($adrs as $rs)
          {
            $arr = array(
              'id' => $rs->id,
							'code' => $rs->code,
							'customer_code' => $rs->customer_code,
              'name' => $rs->name,
              'address' => $rs->address.' '.$rs->sub_district.' '.$rs->district.' '.$rs->province.' '.$rs->postcode,
              'phone' => $rs->phone,
              'email' => $rs->email,
              'alias' => $rs->alias,
              'default' => $rs->is_default
            );

            array_push($ds, $arr);
          }
        }
        else
        {
          $sc = FALSE;
        }
      }
      else
      {
        $sc = FALSE;
      }
    }

    echo $sc === TRUE ? json_encode($ds) : 'noaddress';
  }

} //--- end class

?>
