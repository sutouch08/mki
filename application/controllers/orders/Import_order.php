<?php
class Import_order extends CI_Controller
{
  public $_user;
  public $error;
  public $message;

  public function __construct()
  {
    parent::__construct();

    $uid = get_cookie('uid');

		$this->_user = $this->user_model->get_user_by_uid($uid);

    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/products_model');
    $this->load->model('address/address_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/order_import_logs_model');

    $this->load->library('excel');
  }


  public function index()
  {
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $sc = TRUE;

    $import = 0;
    $success = 0;
    $failed = 0;
    $skip = 0;
          
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path').'orders/';
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $this->config->item('upload_path').'orders/',
      "file_name"	=> "import_order",
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      echo $this->upload->display_errors();
    }
    else
    {
      $info = $this->upload->data();
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      //get only the Cell Collection
      $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      $i = 1;
      $count = count($collection);
      $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

      if( $count > $limit )
      {
        $sc = FALSE;
        $this->error = "ไฟล์มีจำนวนรายการเกิน {$limit} บรรทัด";
      }

      if($sc === TRUE)
      {
        $ds = $this->parse_order_data($collection);

        if( ! empty($ds))
        {
          $shipping_item_code = getConfig('SHIPPING_ITEM_CODE');
          $service_item_code = getConfig('SERVICE_ITEM_CODE');
          $shipping_item = ! empty($shipping_item_code) ? $this->products_model->get($shipping_item_code) : NULL;
          $service_item = ! empty($service_item_code) ? $this->products_model->get($service_item_code) : NULL;

          foreach($ds as $order)
          {
            $import++;

            $res = TRUE;
            $message = "";
            //---- เช็คว่ามีออเดอร์ที่สร้างด้วย reference แล้วหรือยัง
            //---- ถ้ายังไม่มีให้สร้างใหม่
            //---- ถ้ามีแล้วและยังไม่ได้ยกเลิก ไม่สามารถเพิ่มใหม่ได้
            $order_code = $this->orders_model->get_active_order_code_by_reference($order->reference);
            $total_amount = 0;

            if( empty($order_code) )
            {
              $this->db->trans_begin();

              $order_code = $this->get_new_code($order->date_add);

              $arr = array(
                'code' => $order_code,
                'role' => $order->role,
                'bookcode' => $order->bookcode,
                'reference' => $order->reference,
                'customer_code' => $order->customer_code,
                'customer_name' => $order->customer_name,
                'channels_code' => $order->channels_code,
                'payment_code' => $order->payment_code,
                'sale_code' => $order->sale_code,
                'state' => $order->state,
                'is_term' => $order->is_term,
                'status' => $order->status,
                'date_add' => $order->date_add,
                'user' => $order->user,
                'is_import' => $order->is_import
              );

              //--- add order
              if( ! $this->orders_model->add($arr))
              {
                $res = FALSE;
                $message = "Failed to create order for orderNumber {$order->reference}";
              }

              if($res === TRUE)
              {
                if( ! empty($order->items))
                {
                  foreach($order->items as $row)
                  {
                    $arr = array(
                      'order_code' => $order_code,
                      'product_code' => $row->product_code,
                      'product_name' => $row->product_name,
                      'cost' => $row->cost,
                      'price' => $row->price,
                      'qty' => $row->qty,
                      'discount1' => $row->discount1,
                      'discount2' => $row->discount2,
                      'discount3' => $row->discount3,
                      'discount_amount' => $row->discount_amount,
                      'total_amount' => $row->total_amount,
                      'id_rule' => $row->id_rule,
                      'is_count' => $row->is_count,
                      'is_import' => $row->is_import
                    );

                    if( ! $this->orders_model->add_detail($arr))
                    {
                      $res = FALSE;
                      $message = "Failed to add order row of {$order->reference} : {$row->product_code}";
                    }
                    else
                    {
                      $total_amount += $row->total_amount;
                    }

                    if($res == FALSE)
                    {
                      break;
                    }
                  } //--- end foreach

                  //---- if has shipping fee  add shipping sku to order
                  if($res === TRUE && $order->shipping_fee > 0 && ! empty($shipping_item))
                  {
                    $arr = array(
                      "order_code" => $order_code,
                      "product_code" => $shipping_item->code,
                      "product_name" => $shipping_item->name,
                      "cost" => $shipping_item->cost,
                      "price"	=> $order->shipping_fee,
                      "qty"	=> 1,
                      "discount1"	=> 0,
                      "discount2" => 0,
                      "discount3" => 0,
                      "discount_amount" => 0,
                      "total_amount"	=> $order->shipping_fee,
                      "id_rule"	=> NULL,
                      "is_count" => $shipping_item->count_stock,
                      "is_import" => 1
                    );

                    if( ! $this->orders_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $message = "Failed to insert shipping item row of {$order->reference}";
                    }
                    else
                    {
                      $total_amount += $order->shipping_fee;
                    }
                  } //--- end if($order->shipping_fee)

                  //---- if has shipping fee  add shipping sku to order
                  if($res === TRUE && $order->service_fee > 0 && ! empty($service_item))
                  {
                    $arr = array(
                      "order_code" => $order_code,
                      "product_code" => $service_item->code,
                      "product_name" => $service_item->name,
                      "cost" => $service_item->cost,
                      "price"	=> $order->service_fee,
                      "qty"	=> 1,
                      "discount1"	=> 0,
                      "discount2" => 0,
                      "discount3" => 0,
                      "discount_amount" => 0,
                      "total_amount"	=> $order->service_fee,
                      "id_rule"	=> NULL,
                      "is_count" => $service_item->count_stock,
                      "is_import" => 1
                    );

                    if( ! $this->orders_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $message = "Failed to insert shipping item row of {$order->reference}";
                    }
                    else
                    {
                      $total_amount += $order->service_fee;
                    }
                  } //--- end if($order->shipping_fee)
                } //--- end if ! empty($order->items)
              } //--- $sc === TRUE

              //-- add state
              if($res === TRUE)
              {
                $arr = array(
                  'total_amount' => $total_amount,
                  'deposit' => 0,
                  'balance' => $total_amount
                );

                $this->orders_model->update($order_code, $arr);

                $arr = array(
                  'order_code' => $order_code,
                  'state' => $order->state,
                  'update_user' => $this->_user->uname
                );

                //--- add state event
                $this->order_state_model->add_state($arr);
              }

              if($res === TRUE)
              {
                $this->db->trans_commit();
                $success++;
              }
              else
              {
                $this->db->trans_rollback();
                $failed++;
              }

              //--- add logs
              $logs = array(
                'reference' => $order->reference,
                'order_code' => $order_code,
                'action' => 'A', //-- A = add , U = update
                'status' => $res === TRUE ? 'S' : 'E', //-- S = success, E = error, D = duplication
                'message' => $res === TRUE ? NULL : $message,
                'user' => $this->_user->uname
              );

              $this->order_import_logs_model->add($logs);
            }
            else
            {
              if($order->force_update)
              {
                $doc = $this->orders_model->get($order_code);

                if( ! empty($doc) && $doc->state <= 3)
                {
                  $this->db->trans_begin();

                  $arr = array(
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'channels_code' => $order->channels_code,
                    'payment_code' => $order->payment_code,
                    'sale_code' => $order->sale_code,
                    'state' => $order->state,
                    'is_term' => $order->is_term,
                    'status' => $order->status,
                    'date_add' => $order->date_add,
                    'user' => $order->user,
                    'is_import' => $order->is_import
                  );

                  if( ! $this->orders_model->update($order_code, $arr))
                  {
                    $res = FALSE;
                    $message = "Failed to update order {$order_code} for {$order->reference}";
                  }

                  if($res === TRUE)
                  {
                    //---- drop previous order rows
                    if( ! $this->orders_model->remove_all_details($order_code))
                    {
                      $res = FALSE;
                      $message = "Failed to remove previous order rows";
                    }
                    else
                    {
                      if( ! empty($order->items))
                      {
                        foreach($order->items as $row)
                        {
                          $arr = array(
                            'order_code' => $order_code,
                            'product_code' => $row->product_code,
                            'product_name' => $row->product_name,
                            'cost' => $row->cost,
                            'price' => $row->price,
                            'qty' => $row->qty,
                            'discount1' => $row->discount1,
                            'discount2' => $row->discount2,
                            'discount3' => $row->discount3,
                            'discount_amount' => $row->discount_amount,
                            'total_amount' => $row->total_amount,
                            'id_rule' => $row->id_rule,
                            'is_count' => $row->is_count,
                            'is_import' => $row->is_import
                          );

                          if( ! $this->orders_model->add_detail($arr))
                          {
                            $res = FALSE;
                            $message = "Failed to add order row of {$order->reference} : {$row->product_code}";
                          }
                          else
                          {
                            $total_amount += $row->total_amount;
                          }

                          if($res == FALSE)
                          {
                            break;
                          }
                        } //--- end foreach

                        //---- if has shipping fee  add shipping sku to order
                        if($res === TRUE && $order->shipping_fee > 0 && ! empty($shipping_item))
                        {
                          $arr = array(
                            "order_code" => $order_code,
                            "product_code" => $shipping_item->code,
                            "product_name" => $shipping_item->name,
                            "cost" => $shipping_item->cost,
                            "price"	=> $order->shipping_fee,
                            "qty"	=> 1,
                            "discount1"	=> 0,
                            "discount2" => 0,
                            "discount3" => 0,
                            "discount_amount" => 0,
                            "total_amount"	=> $order->shipping_fee,
                            "id_rule"	=> NULL,
                            "is_count" => $shipping_item->count_stock,
                            "is_import" => 1
                          );

                          if( ! $this->orders_model->add_detail($arr))
                          {
                            $sc = FALSE;
                            $message = "Failed to insert shipping item row of {$order->reference}";
                          }
                          else
                          {
                            $total_amount += $order->shipping_fee;
                          }
                        } //--- end if($order->shipping_fee)

                        //---- if has shipping fee  add shipping sku to order
                        if($res === TRUE && $order->service_fee > 0 && ! empty($service_item))
                        {
                          $arr = array(
                            "order_code" => $order_code,
                            "product_code" => $service_item->code,
                            "product_name" => $service_item->name,
                            "cost" => $service_item->cost,
                            "price"	=> $order->service_fee,
                            "qty"	=> 1,
                            "discount1"	=> 0,
                            "discount2" => 0,
                            "discount3" => 0,
                            "discount_amount" => 0,
                            "total_amount"	=> $order->service_fee,
                            "id_rule"	=> NULL,
                            "is_count" => $service_item->count_stock,
                            "is_import" => 1
                          );

                          if( ! $this->orders_model->add_detail($arr))
                          {
                            $sc = FALSE;
                            $message = "Failed to insert shipping item row of {$order->reference}";
                          }
                          else
                          {
                            $total_amount += $order->service_fee;
                          }
                        } //--- end if($order->shipping_fee)
                      } //--- end if ! empty($order->items)
                    } //--- end if remove all detail
                  } //--- if($res === TRUE)

                  //-- add state
                  if($res === TRUE)
                  {
                    $arr = array(
                      'total_amount' => $total_amount,
                      'deposit' => 0,
                      'balance' => $total_amount
                    );

                    $this->orders_model->update($order_code, $arr);

                    $arr = array(
                      'order_code' => $order_code,
                      'state' => $order->state,
                      'update_user' => $this->_user->uname
                    );
                    //--- add state event
                    $this->order_state_model->add_state($arr);
                  }

                  if($res === TRUE)
                  {
                    $this->db->trans_commit();
                    $success++;
                  }
                  else
                  {
                    $this->db->trans_rollback();
                    $failed++;
                  }

                  //--- add logs
                  $logs = array(
                    'reference' => $order->reference,
                    'order_code' => $order_code,
                    'action' => 'U', //-- A = add , U = update
                    'status' => $res === TRUE ? 'S' : 'E', //-- S = success, E = error, D = duplication
                    'message' => $message,
                    'user' => $this->_user->uname
                  );

                  $this->order_import_logs_model->add($logs);
                }
                else
                {
                  $failed++;
                  //--- add logs
                  $logs = array(
                    'reference' => $order->reference,
                    'order_code' => $order_code,
                    'action' => 'U', //-- A = add , U = update
                    'status' => 'E', //-- S = success, E = error, D = Skip (duplicated and not force to update)
                    'message' => "Invalid order state",
                    'user' => $this->_user->uname
                  );

                  $this->order_import_logs_model->add($logs);
                }
              }
              else
              {
                $skip++;
                //--- add logs
                $logs = array(
                  'reference' => $order->reference,
                  'order_code' => $order_code,
                  'action' => 'A', //-- A = add , U = update
                  'status' => 'D', //-- S = success, E = error, D = Skip (duplicated and not force to update)
                  'message' => "{$order->reference} already exists",
                  'user' => $this->_user->uname
                );

                $this->order_import_logs_model->add($logs);
              }
            } //--- end if order exists
          } //--- end foreach
        }
        else
        {
          $sc = FALSE;
        }
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  private function parse_order_data($collection)
  {
    $sc = TRUE;

    if( ! empty($collection))
    {
      $bookcode = getConfig('BOOK_CODE_ORDER');
      $role = 'S';

      $ds = array(); //---- ได้เก็บข้อมูล orders

      $whsCache = array(); //--- ไว้เก็บ  warehouse cache

      //--- เก็บ channels cache
      $channelsCache = array();

      //--- เก็บ payment cache
      $paymentsCache = array();

      //--- เก็บ customer cache
      $customerCache = array();

      $itemsCache = array(); //--- เก็บ item cache

      $headCol = array(
        'A' => 'OrderID',
        'B' => 'CustomerCode',
        'C' => 'Date',
        'D' => 'Channels',
        'E' => 'Payments',
        'F' => 'SKU',
        'G' => 'Qty',
        'H' => 'Price',
        'I' => 'Discount',
        'J' => 'ShippingFee',
        'K' => 'ServiceFee'
      );

      $i = 1;
      //---- รวมข้อมูลให้เป็น array ก่อนนำไปใช้สร้างออเดอร์
      foreach($collection as $rs)
      {
        if($sc === FALSE)
        {
          break;
        }

        if($i == 1)
        {
          $i++;

          foreach($headCol as $col => $field)
          {
            if( ! isset($rs[$col]) OR $rs[$col] !== $field)
            {
              $sc = FALSE;
              $this->error .= 'Column '.$col.' Should be '.$field.'<br/>';
            }
          }

          if($sc === FALSE)
          {
            $this->error .= "<br/><br/>You should download new template !";
            break;
          }
        }
        else
        {
          //--- OrderID / CustomerCode / SKU ต้องไม่เป็นค่าว่าง
          if($sc === TRUE && ! empty($rs['A']) && ! empty($rs['B']) && ! empty($rs['F']))
          {
            //--- ใช้ orderNumber เป็น key array
            $ref_code = trim($rs['A']);

            //--- เช็คว่ามี key อยู่แล้วหรือไม่
            //--- ถ้ายังไม่มีให้สร้างใหม่ ถ้ามีแล้ว ให้เพิ่ม รายการสินค้าเข้าไป
            if( ! isset($ds[$ref_code]))
            {
              $date_add = db_date(trim($rs['C']));

              //--- check date format only check not convert
              if( ! is_valid_date($date_add))
              {
                $sc = FALSE;
                $this->error = "Invalid Date format at Line {$i}";
              }

              $customer_code = trim($rs['B']);

              if( ! empty($customer_code))
              {
                if( ! isset($customerCache[$customer_code]))
                {
                  $customer = $this->customers_model->get($customer_code);

                  if( ! empty($customer))
                  {
                    $customerCache[$customer_code] = $customer;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Invalid customer code at Line {$i} <br/>";
                  }
                }

                $customer = $customerCache[$customer_code];
              }
              else
              {
                $sc = FALSE;
                $this->error .= "CustomerCode is required at Line {$i} <br/>";
              }

              //---- กำหนดช่องทางการขายเป็นรหัส
              $channels_code = trim($rs['D']);

              if( ! empty($channels_code))
              {
                //--- check channels cache
                if( ! isset($channelsCache[$channels_code]))
                {
                  $channels = $this->channels_model->get($channels_code);

                  if( ! empty($channels))
                  {
                    $channelsCache[$channels_code] = $channels;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Invalid channels at Line {$i} <br/>";
                  }
                }

                $channels = $channelsCache[$channels_code];
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Channels is required at Line {$i} <br/>";
              }

              //--- กำหนดช่องทางการชำระเงิน
              $payment_code = trim($rs['E']);

              if( ! empty($payment_code))
              {
                if( ! isset($paymentsCache[$payment_code]))
                {
                  $payment = $this->payment_methods_model->get($payment_code);

                  if( ! empty($payment))
                  {
                    $paymentsCache[$payment_code] = $payment;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Invalid Payment method at Line {$i} <br/>";
                  }
                }

                $payment = $paymentsCache[$payment_code];
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Payment Method is required at Line {$i} <br/>";
              }

              //--- check item cache
              $item_code = trim($rs['F']);

              if( ! isset($itemsCache[$item_code]))
              {
                $item = $this->products_model->get($item_code);

                if( ! empty($item))
                {
                  $itemsCache[$item->code] = $item;
                  $item_code = $item->code;
                }
                else
                {
                  $sc = FALSE;
                  $this->error .= "Invalid SKU '{$item_code}' at Line {$i} <br/>";
                }
              }

              $item = empty($itemsCache[$item_code]) ? NULL : $itemsCache[$item_code];

              if($sc === TRUE)
              {
                //---	ถ้าเป็นออเดอร์ขาย จะมี id_sale
                $sale_code = empty($customer) ? NULL : $customer->sale_code;

                //--- ค่าจัดส่ง
                $shipping_fee = empty($rs['J']) ? 0.00 : trim($rs['J']);
                $shipping_fee = is_numeric($shipping_fee) ? $shipping_fee : 0.00;

                //--- ค่าบริการอื่นๆ
                $service_fee = empty($rs['K']) ? 0.00 : trim($rs['K']);
                $service_fee = is_numeric($service_fee) ? $service_fee : 0.00;

                $qty = empty(trim($rs['G'])) ? 1 : str_replace(',', '', $rs['G']);
                $qty = is_numeric($qty) ? $qty : 1;

                //--- ราคา ต่อหน่วย
                $price = empty($rs['H']) ? 0.00 : str_replace(",", "", $rs['H']); //--- ราคารวมไม่หักส่วนลด
                $price = is_numeric($price) ? $price : 0;

                //--- ส่วนลด (รวม)
                $discount_amount = empty(trim($rs['I'])) ? 0.00 : str_replace(',', '', trim($rs['I']));
                $discount_amount = is_numeric($discount_amount) ? $discount_amount : 0;

                //--- ส่วนลด (ต่อชิ้น)
                $discount = $discount_amount > 0 ? ($discount_amount / $qty) : 0;

                //--- total_amount
                $total_amount = ($price * $qty) - $discount_amount;

                //---- now create order data
                $ds[$ref_code] = (object) array(
                  'role' => $role,
                  'bookcode' => $bookcode,
                  'reference' => $ref_code,
                  'customer_code' => $customer_code,
                  'customer_name' => $customer->name,
                  'channels_code' => $channels_code,
                  'payment_code' => $payment_code,
                  'sale_code' => $sale_code,
                  'state' => 3,
                  'is_paid' => 0,
                  'is_term' => $payment->has_term,
                  'shipping_fee' => $shipping_fee,
                  'service_fee' => $service_fee,
                  'status' => 1,
                  'date_add' => $date_add,
                  'user' => $this->_user->uname,
                  'is_import' => 1,
                  'force_update' => empty(trim($rs['L'])) ? FALSE : TRUE,
                  'hold' => FALSE,
                  'items' => array()
                );

                $row = (object) array(
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  "discount1"	=> $discount,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $discount_amount,
                  "total_amount"	=> round($total_amount,2),
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock,
                  "is_import" => 1
                );

                $ds[$ref_code]->items[$item->code] = $row;
              } //--- end if $sc == TRUE
            }
            else
            {
              //--- check item cache
              $item_code = trim($rs['F']);

              if( ! isset($itemsCache[$item_code]))
              {
                $item = $this->products_model->get($item_code);

                if( ! empty($item))
                {
                  $itemsCache[$item->code] = $item;
                  $item_code = $item->code;
                }
                else
                {
                  $sc = FALSE;
                  $this->error .= "Invalid SKU '{$item_code}' at Line {$i} <br/>";
                }
              }

              $item = $itemsCache[$item_code];

              $qty = empty(trim($rs['G'])) ? 1 : str_replace(',', '', $rs['G']);
              $qty = is_numeric($qty) ? $qty : 1;

              //--- ราคา ต่อหน่วย
              $price = empty($rs['H']) ? 0.00 : str_replace(",", "", $rs['H']); //--- ราคารวมไม่หักส่วนลด
              $price = is_numeric($price) ? $price : 0;

              //--- ส่วนลด (รวม)
              $discount_amount = empty(trim($rs['I'])) ? 0.00 : str_replace(',', '', trim($rs['I']));
              $discount_amount = is_numeric($discount_amount) ? $discount_amount : 0;

              //--- ส่วนลด (ต่อชิ้น)
              $discount = $discount_amount > 0 ? ($discount_amount / $qty) : 0;

              //--- total_amount
              $total_amount = ($price * $qty) - $discount_amount;

              if(isset($ds[$ref_code]->items[$item->code]))
              {
                $row = $ds[$ref_code]->items[$item->code];
                $newPrice = ($row->price + $price) / 2; //--- เฉลี่ยราคา
                $newQty = $row->qty + $qty;
                $newDisc = $row->discount1 + $discount;
                $newDiscAmount = $row->discount_amount + $discount_amount;
                $newTotal = $row->total_amount + $total_amount;

                $ds[$ref_code]->items[$item->code]->qty = $newQty;
                $ds[$ref_code]->items[$item->code]->price = $newPrice;
                $ds[$ref_code]->items[$item->code]->discount1 = $newDisc;
                $ds[$ref_code]->items[$item->code]->discount_amount = $newDiscAmount;
                $ds[$ref_code]->items[$item->code]->total_amount = $newTotal;
              }
              else
              {
                $row = (object) array(
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  "discount1"	=> $discount,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $discount_amount,
                  "total_amount"	=> round($total_amount,2),
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock,
                  "is_import" => 1
                );

                $ds[$ref_code]->items[$item->code] = $row;
              }
            } //--- end if( ! isset($ds[$ref_code]));
          } //--- end if($sc === TRUE && ! empty($rs['A']) && ! empty($rs['I']));

          $i++;
        } //--- end if $i == 1
      } //---- end foreach collection
    }
    else
    {
      $sc = FALSE;
      $this->error = "Empty data collection";
    }

    return $sc === TRUE ? $ds : FALSE;
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }
}

 ?>
