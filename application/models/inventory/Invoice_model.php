<?php
class Invoice_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_billed_detail($code, $picked = 1, $use_qc = TRUE)
  {
    //--- picked = 1 ผ่านการจัด, picked = 0 ไม่ผ่านการจัด

    $rs = $this->db
    ->select("id, product_code, product_name, qty AS order_qty, is_count")
    ->select("price, discount1, discount2, discount3")
    ->select("(discount_amount / qty) AS discount_amount", FALSE)
    ->select("(total_amount/qty) AS final_price")
    ->where("order_code", $code)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      $details = $rs->result();

      foreach($details as $rs)
      {
        $rs->prepared = $picked == 0 ? 0 : ($rs->is_count == 0 ? $rs->order_qty : $this->get_sum_prepared($code, $rs->product_code));
        $rs->qc = $picked == 0 ? 0 : ($use_qc ? ($rs->is_count == 0 ? $rs->order_qty : ($this->get_sum_prepared($code, $rs->product_code))) : $rs->prepared);
        $rs->sold = $this->get_sum_sold($code, $rs->product_code);
      }

      return $details;
    }

    return NULL;
  }

  public function get_sum_prepared($order_code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->get('prepare');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_qc($order_code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->get('qc');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }

  public function get_sum_sold($order_code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('reference', $order_code)
    ->where('product_code', $product_code)
    ->get('order_sold');


    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('reference', $code)->get('order_sold');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_total_sold_qty($code)
  {
    $rs = $this->db->select_sum('qty')->where('reference', $code)->get('order_sold');
    return intval($rs->row()->qty);
  }


  public function get_total_sold_amount($code)
  {
    $rs = $this->db->select_sum('total_amount')->where('reference', $code)->get('order_sold');
    return $rs->row()->total_amount;
  }


  public function drop_sold($id)
  {
    return $this->db->where('id', $id)->delete('order_sold');
  }


  public function drop_order_sold($code)
  {
    return $this->db->where('reference', $code)->delete('order_sold');
  }


  public function is_over_due($customer_code)
  {
    $today = date('Y-m-d');
    $control_day = getConfig('OVER_DUE_DATE');
    $rs = $this->db
    ->select('id')
    ->where('valid', 0, FALSE)
    ->where('balance >', 0, FALSE)
    ->where('customer_code', $customer_code)
    ->where('over_due_date <', $today)
    ->get('order_credit');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  //--- get sold id
  public function get_sold_id($reference, $customer_code, $product_code, $cost, $price, $discount_label)
  {
    $rs = $this->db
    ->select('id')
    ->where('reference', $reference)
    ->where('customer_code', $customer_code)
    ->where('product_code', $product_code)
    ->where('cost', $cost, FALSE)
    ->where('price', $price, FALSE)
    ->where('discount_label')
    ->get('order_sold');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }

} //--- end class

 ?>
