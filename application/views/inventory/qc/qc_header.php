
<!-- แสดงข้อมูลเอกสาร  -->
<div class="row">
  <div class="col-sm-2 col-xs-12 padding-5">
    <label>เลขที่ : <?php echo $order->reference; ?></label>
  </div>
  <div class="col-sm-4 col-xs-12 padding-5">
    <label>ลูกค้า : <?php echo customerName($order->id_customer); ?></label>
  </div>
  <div class="col-sm-2 col-xs-12 padding-5">
    <label>วันที่ : <?php echo thaiDate($order->date_add); ?></label>
  </div>
  <div class="col-sm-4 col-xs-12 padding-5 hidden-xs"></div>
  <?php if( $order->remark != "") : ?>
  <div class="col-sm-12 col-xs-12 padding-5">
    <label style="font-weight:normal;">หมายเหตุ : <?php echo $order->remark; ?></label>
  </div>
  <?php endif; ?>
</div>

<input type="hidden" id="id_order" value="<?php echo $order->id; ?>" />
<hr/>
<!-- แสดงข้อมูลเอกสาร  -->
