<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr/>


<?php if( $order->state == 8) : ?>
  <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
  <input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />
  <input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
  <div class="row">
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled readonly />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ลูกค้า[ในระบบ]</label>
      <input type="text" class="form-control input-sm" id="customer-code" value="<?php echo $order->customer_code; ?>" disabled />
    </div>
    <div class="col-lg-5 col-md-6-harf col-sm-6-harf col-xs-6 padding-5">
      <label class="not-show">ลูกค้า</label>
      <input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
      <label>ลูกค้า[ออนไลน์]</label>
      <input type="text" class="form-control input-sm edit" id="customer_ref" name="customer_ref" value="<?php echo $order->customer_ref; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
      <label>ช่องทางขาย</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->channels_name; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
      <label>การชำระเงิน</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->payment_name; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>อ้างอิง</label>
      <input type="text" class="form-control input-sm text-center edit" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
      <label>เลขที่จัดส่ง</label>
      <input type="text" class="form-control input-sm text-center edit" name="shipping_code" id="shipping_code" value="<?php echo $order->shipping_code; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
      <label>การจัดส่ง</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->sender_name; ?>" disabled />
    </div>
    <div class="col-lg-6 col-md-12 col-sm-9-harf col-xs-12 padding-5 ">
      <label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
    </div>
  </div>
  <hr/>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
      <button type="button" class="btn btn-sm btn-info top-btn" onclick="printAddress()"><i class="fa fa-print"></i> ใบนำส่ง</button>
			<?php if(!empty($order->invoice_code)) : ?>
				<?php if($this->_use_vat) : ?>
					<button type="button" class="btn btn-sm btn-info top-btn" onclick="print_tax_receipt('<?php echo $order->invoice_code; ?>')"><i class="fa fa-print"></i> พิมพ์ใบเสร็จรับเงิน</button>
					<button type="button" class="btn btn-sm btn-info top-btn" onclick="print_tax_invoice('<?php echo $order->invoice_code; ?>')"><i class="fa fa-print"></i> พิมพ์ใบแจ้งหนี้</button>
				<?php else : ?>
					<button type="button" class="btn btn-sm btn-info top-btn" onclick="print_do_invoice('<?php echo $order->invoice_code; ?>')"><i class="fa fa-print"></i> พิมพ์ใบแจ้งหนี้</button>
					<button type="button" class="btn btn-sm btn-info top-btn" onclick="print_do_receipt('<?php echo $order->invoice_code; ?>')"><i class="fa fa-print"></i> พิมพ์ใบเสร็จ</button>
				<?php endif; ?>
			<?php endif; ?>
			<button type="button" class="btn btn-sm btn-primary top-btn" onclick="printOrder()"><i class="fa fa-print"></i> ใบส่งของ </button>
			<button type="button" class="btn btn-sm btn-primary top-btn" onclick="printOrderNoPrice()"><i class="fa fa-print"></i> ใบส่งของ (ไม่แสดงราคา)</button>
      <button type="button" class="btn btn-sm btn-success top-btn" onclick="printOrderBarcode()"><i class="fa fa-print"></i> ใบส่งของ (barcode)</button>


      <?php if($use_qc) : ?>
      <button type="button" class="btn btn-sm btn-warning top-btn" onclick="showBoxList()"><i class="fa fa-print"></i> Packing List (ปะหน้ากล่อง)</button>
      <?php endif; ?>

    </div>
  </div>
  <hr/>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-sm-12 padding-5 table-responsive">
      <table class="table table-bordered" style="min-width:1050px;">
        <thead>
          <tr class="font-size-12">
            <th class="fix-width-50 text-center">ลำดับ</th>
            <th class="min-width-300 text-center">สินค้า</th>
            <th class="fix-width-100 text-center">ราคา</th>
            <th class="fix-width-100 text-center">ออเดอร์</th>
            <th class="fix-width-100 text-center">จัด</th>
            <?php if($use_qc) : ?>
              <th class="fix-width-100 text-center">ตรวจ</th>
            <?php endif; ?>
            <th class="fix-width-100 text-center">เปิดบิล</th>
            <th class="fix-width-100 text-center">ส่วนลด</th>
            <th class="fix-width-120 text-center">มูลค่า</th>
          </tr>
        </thead>
        <tbody>
  <?php if(!empty($details)) : ?>
  <?php   $no = 1;
          $totalQty = 0;
          $totalPrepared = 0;
          $totalQc = 0;
          $totalSold = 0;
          $totalAmount = 0;
          $totalDiscount = 0;
          $totalPrice = 0;
  ?>
  <?php   foreach($details as $rs) :  ?>
  <?php   $color = "";
          if($order->picked == 0)
          {
            $color = $rs->order_qty != $rs->sold ? 'red' : $color;
          }
          else
          {
            if($use_qc)
            {
              $color = (($rs->qc != $rs->order_qty) OR ($rs->qc != $rs->prepared)) ? 'red' : $color;
            }
            else
            {
              $color = $rs->prepared != $rs->order_qty ? 'red' : $color;
            }
          }
  ?>
            <tr class="font-size-12 <?php echo $color; ?>">
              <td class="text-center"><?php echo $no; ?></td>

              <!--- รายการสินค้า ที่มีการสั่งสินค้า --->
              <td><?php echo $rs->product_code.' : '. $rs->product_name; ?></td>

              <!--- ราคาสินค้า  --->
              <td class="text-center"><?php echo number($rs->price, 2); ?></td>

              <!---   จำนวนที่สั่ง  --->
              <td class="text-center"><?php echo number($rs->order_qty); ?></td>

              <!--- จำนวนที่จัดได้  --->
              <td class="text-center"><?php echo number($rs->prepared); ?></td>

              <!--- จำนวนที่ตรวจได้ --->
              <?php if($use_qc) : ?>
              <td class="text-center"><?php echo number($rs->qc); ?></td>
              <?php endif; ?>

              <!--- จำนวนที่บันทึกขาย --->
              <td class="text-center"><?php echo number($rs->sold); ?></td>

              <!--- ส่วนลด  --->
              <td class="text-center"><?php echo discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?></td>

              <td class="text-right"><?php echo number($rs->line_total, 2); ?></td>

            </tr>
    <?php
            $totalQty += $rs->order_qty;
            $totalPrepared += $rs->prepared;
            $totalQc += $rs->qc;
            $totalSold += $rs->sold;
            $totalDiscount += $rs->line_discount;
            $totalAmount += $rs->line_total;
            $totalPrice += $rs->price_amount;
            $no++;
            ?>
  <?php   endforeach; ?>
          <tr class="font-size-12">
            <td colspan="3" class="text-right font-size-14">
              รวม
            </td>

            <td class="text-center">
              <?php echo number($totalQty); ?>
            </td>
            <td class="text-center">
              <?php echo number($totalPrepared); ?>
            </td>

            <?php if($use_qc) : ?>
              <td class="text-center">
                <?php echo number($totalQc); ?>
              </td>
            <?php endif; ?>

            <td class="text-center">
              <?php echo number($totalSold); ?>
            </td>

            <td class="text-center">
              ส่วนลดท้ายบิล
            </td>

            <td class="text-right">
              <?php echo number($order->bDiscAmount, 2); ?>
            </td>
          </tr>

          <?php $colspan = 3; ?>
          <tr>
            <td colspan="4" rowspan="3">
              หมายเหตุ : <?php echo $order->remark; ?>
            </td>
            <td colspan="<?php echo $colspan; ?>" class="blod">
              ราคารวม
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalPrice, 2); ?>
            </td>
          </tr>

          <tr>
            <td colspan="<?php echo $colspan; ?>">
              ส่วนลดรวม
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalDiscount + $order->bDiscAmount, 2); ?>
            </td>
          </tr>

          <tr>
            <td colspan="<?php echo $colspan; ?>" class="blod">
              ยอดเงินสุทธิ
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalPrice - ($totalDiscount + $order->bDiscAmount), 2); ?>
            </td>
          </tr>

  <?php else : ?>
        <tr><td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td></tr>
  <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>


  <!--************** Address Form Modal ************-->
  <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="addressModal" aria-hidden="true">
    <div class="modal-dialog" style="width:500px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" id="info_body">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-primary" onclick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
        </div>
      </div>
    </div>
  </div>

  <?php if($use_qc) : ?>
  <?php $this->load->view('inventory/order_closed/box_list');  ?>
  <?php endif; ?>

  <script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>
  <script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>

<?php else : ?>
  <?php $this->load->view('inventory/delivery_order/invalid_state'); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_invoice.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
