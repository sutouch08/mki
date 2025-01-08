<?php $this->load->view('include/header'); ?>
<?php
$cus_pm = get_permission('DBCUST', $this->_user->uid, $this->_user->id_profile); //--- customer master permission
$chn_pm = get_permission('DBCHAN', $this->_user->uid, $this->_user->id_profile); //--- channels master permission
$pay_pm = get_permission('DBPAYM', $this->_user->uid, $this->_user->id_profile); //--- payments master permission
?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5 hidden-xs">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm" value="" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" autofocus required />
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5">
		<label class="display-block not-show">add</label>
		<?php if($cus_pm->can_add) : ?>
			<button type="button" class="btn btn-success btn-xs btn-block" onclick="addCustomer()"><i class="fa fa-plus"></i></button>
		<?php else : ?>
			<button type="button" class="btn btn-default btn-xs btn-block" disabled><i class="fa fa-plus"></i></button>
		<?php endif; ?>
	</div>

  <div class="col-md-6 col-sm-5-harf col-xs-7 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm" name="customerName" id="customerName" value="" required />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-5 padding-5">
    <label>ลูกค้า[ออนไลน์]</label>
		<input type="text" class="form-control input-sm" name="cust_ref" value="" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>อ้างอิงออเดอร์</label>
		<input type="text" class="form-control input-sm" name="reference" value="" />
  </div>

	<div class="col-lg-3 col-md-3 col-sm-2-harf col-xs-4 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" id="channels" required>
			<option value="">ทั้งหมด</option>
			<?php echo select_channels(); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5">
		<label class="display-block not-show">add</label>
		<?php if($chn_pm->can_add) : ?>
			<button type="button" class="btn btn-success btn-xs btn-block" onclick="addChannels()"><i class="fa fa-plus"></i></button>
		<?php else : ?>
			<button type="button" class="btn btn-default btn-xs btn-block" disabled><i class="fa fa-plus"></i></button>
		<?php endif; ?>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-2-harf col-xs-4 padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" id="payment" required>
			<option value="">ทั้งหมด</option>
			<?php echo select_payment_method(); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5">
		<label class="display-block not-show">add</label>
		<?php if($pay_pm->can_add) : ?>
			<button type="button" class="btn btn-success btn-xs btn-block" onclick="addPayment()"><i class="fa fa-plus"></i></button>
		<?php else : ?>
			<button type="button" class="btn btn-default btn-xs btn-block" disabled><i class="fa fa-plus"></i></button>
		<?php endif; ?>
	</div>



	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>การจัดส่ง</label>
    <select class="form-control input-sm" name="sender_id" id="sender_id">
      <option value="">กรุณาเลือก</option>
      <?php echo select_sender_list(); ?>
    </select>
  </div>

  <div class="col-lg-8-harf col-md-8 col-sm-8 col-xs-10 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-2 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="submit" class="btn btn-xs btn-success btn-block">เพิ่ม</button>
  </div>
</div>
<input type="hidden" name="customerCode" id="customerCode" value="" />
</form>
<hr class="margin-top-15 padding-5">


<?php $this->load->view('orders/masters_add_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/masters_add_modal.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
