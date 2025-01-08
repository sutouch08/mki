<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-primary btn-100" onclick="goBack()"><i class="fa fa-arrow-left"></i> รอจัด</button>
      <button type="button" class="btn btn-xs btn-info btn-100" onclick="goProcess()"><i class="fa fa-arrow-left"></i> กำลังจัด</button>
      <button type="button" class="btn btn-xs btn-purple btn-100" onclick="viewBuffer()">Buffer</button>
    </p>
  </div>
</div>

<hr class="margin-bottom-10" />
<?php if($order->state != 4) : ?>
  <?php   $this->load->view('inventory/prepare/invalid_state'); ?>
<?php else : ?>
  <div class="row">
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($order->date_add); ?>" disabled/>
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>รหัสลูกค้า</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->customer_code; ?>" disabled />
    </div>
    <div class="col-lg-6 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
      <label>ลูกค้า/ผู้เบิก/ผู้ยืม</label>
      <input type="text" class="form-control input-sm"
      value="<?php echo ($order->customer_ref == '' ? $order->customer_name : str_replace('"', '&quot;',$order->customer_ref));  ?>" disabled />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ช่องทาง</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->channels_name; ?>" disabled/>
    </div>

    <div class="col-sm-12 col-xs-12 padding-5 margin-top-10">
      <label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->remark; ?>" disabled />
    </div>

    <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
  </div>

  <form id="buffer-form" method="post" action="<?php echo base_url(); ?>inventory/buffer" target="_blank">
    <input type="hidden" name="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="pd_code" value="" />
    <input type="hidden" name="zone_code" value="" />
  </form>

  <hr class="margin-top-10 margin-bottom-10"/>

  <?php $this->load->view('inventory/prepare/prepare_control'); ?>

  <hr class="margin-top-10 margin-bottom-10"/>

  <?php $this->load->view('inventory/prepare/prepare_incomplete_list');  ?>

  <hr class="margin-top-10 margin-bottom-10 visible-xs"/>

  <?php $this->load->view('inventory/prepare/prepare_completed_list'); ?>

<?php endif; //--- endif order->state ?>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_process.js?"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
