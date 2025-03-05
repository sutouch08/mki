<?php $this->load->view('include/header'); ?>
<?php
$cus_pm = get_permission('DBCUST', $this->_user->uid, $this->_user->id_profile); //--- customer master permission
$chn_pm = get_permission('DBCHAN', $this->_user->uid, $this->_user->id_profile); //--- channels master permission
$pay_pm = get_permission('DBPAYM', $this->_user->uid, $this->_user->id_profile); //--- payments master permission
?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="width-100 e text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="width-100 e" name="customer" id="customer" value="" autofocus required />
  </div>

	<div class="col-lg-harf col-md-1 col-sm-1 col-xs-2 padding-5">
		<label class="display-block not-show">add</label>
		<?php if($cus_pm->can_add) : ?>
			<button type="button" class="btn btn-success btn-xs btn-block" onclick="addCustomer()"><i class="fa fa-plus"></i></button>
		<?php else : ?>
			<button type="button" class="btn btn-default btn-xs btn-block" disabled><i class="fa fa-plus"></i></button>
		<?php endif; ?>
	</div>

  <div class="col-lg-4 col-md-5-harf col-sm-5-harf col-xs-12 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="width-100 e" name="customerName" id="customerName" value="" required />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>CSR</label>
    <input type="text" class="width-100 text-center e" id="sale-code" value="" disabled />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Sale</label>
    <input type="text" class="width-100 text-center e" id="cus-type" value="" disabled />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>ลูกค้า[ออนไลน์]</label>
		<input type="text" class="width-100 e" name="cust_ref" id="cust-ref" value="" />
  </div>

	<div class="col-lg-2 col-md-3-harf col-sm-3-harf col-xs-10 padding-5">
    <label>ผู้ช่วยขาย</label>
		<select class="width-100 e" name="tags" id="tags">
			<option value="">เลือก</option>
			<?php echo select_order_tags(); ?>
		</select>
  </div>

	<div class="col-lg-harf col-md-1 col-sm-1 col-xs-2 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-success btn-xs btn-block" onclick="newTags()"><i class="fa fa-plus"></i></button>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>อ้างอิง[MKP]</label>
		<input type="text" class="width-100 e" name="reference" id="reference" value="" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>อ้างอิง[CRM]</label>
		<input type="text" class="width-100 e" name="reference2" id="reference2" value="" />
  </div>

	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-10 padding-5">
    <label>ช่องทางขาย</label>
		<select class="width-100 e" name="channels" id="channels" required>
			<option value="">เลือกช่องทางขาย</option>
			<?php echo select_channels(); ?>
		</select>
  </div>

	<div class="col-lg-harf col-md-1 col-sm-1 col-xs-2 padding-5">
		<label class="display-block not-show">add</label>
		<?php if($chn_pm->can_add) : ?>
			<button type="button" class="btn btn-success btn-xs btn-block" onclick="addChannels()"><i class="fa fa-plus"></i></button>
		<?php else : ?>
			<button type="button" class="btn btn-default btn-xs btn-block" disabled><i class="fa fa-plus"></i></button>
		<?php endif; ?>
	</div>

	<div class="col-lg-2 col-md-3-harf col-sm-3-harf col-xs-10 padding-5">
    <label>การชำระเงิน</label>
		<select class="width-100 e" name="payment" id="payment" required>
			<option value="">เลือกการชำระเงิน</option>
			<?php echo select_payment_method(); ?>
		</select>
  </div>

	<div class="col-lg-harf col-md-1 col-sm-1 col-xs-2 padding-5">
		<label class="display-block not-show">add</label>
		<?php if($pay_pm->can_add) : ?>
			<button type="button" class="btn btn-success btn-xs btn-block" onclick="addPayment()"><i class="fa fa-plus"></i></button>
		<?php else : ?>
			<button type="button" class="btn btn-default btn-xs btn-block" disabled><i class="fa fa-plus"></i></button>
		<?php endif; ?>
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label>การจัดส่ง</label>
    <select class="width-100 e" name="sender_id" id="sender_id">
      <option value="">กรุณาเลือก</option>
      <?php echo select_sender_list(); ?>
    </select>
  </div>

  <div class="col-lg-9 col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="width-100" name="remark" id="remark" value="">
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="checkBalance()">เพิ่ม</button>
  </div>
</div>

<input type="hidden" name="customerCode" id="customerCode" value="" />
<hr class="margin-top-15 padding-5">


<?php $this->load->view('orders/masters_add_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/masters_add_modal.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
