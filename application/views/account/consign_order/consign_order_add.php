<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center e" name="date_add" id="date" value="<?php echo date('d-m-Y'); ?>" readonly required />
	</div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm e" name="customerCode" id="customerCode" />
	</div>
	<div class="col-lg-3-harf col-md-6 col-sm-6 col-xs-6 padding-5">
		<label>ลูกค้า[ในระบบ]</label>
		<input type="text" class="form-control input-sm e" name="customer" id="customer" value="" required />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>โซน</label>
		<input type="text" class="form-control input-sm e" name="zone_code" id="zone_code" />
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone" id="zone" value="" />
	</div>

	<div class="col-lg-10-harf col-md-4-harf col-sm-4-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" value="">
	</div>
	<?php if($this->pm->can_add) : ?>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">Submit</label>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
		</div>
	<?php endif; ?>
</div>

<hr class="margin-top-15">

<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js?v=<?php echo date('Ymd'); ?>"></script>


<?php $this->load->view('include/footer'); ?>
