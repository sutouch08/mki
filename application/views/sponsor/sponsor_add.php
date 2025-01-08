<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?> </h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>		
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center e" id="date-add" value="<?php echo date('d-m-Y'); ?>" />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm e" id="customer-code" value="" />
	</div>

	<div class="col-lg-4 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm e" id="customer-name" value="" readonly/>
	</div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
		<label>งบคงเหลือ</label>
		<input type="text" class="form-control input-sm text-center e" data-amount="" id="budget-amount" value="" disabled />
	</div>

	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ผู้เบิก[พนักงาน/คนสั่ง]</label>
		<input type="text" class="form-control input-sm e" id="emp-name" value="" />
	</div>

	<div class="col-lg-11 col-md-7-harf col-sm-7-harf col-xs-9">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" id="remark" value="">
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">Submit</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
	</div>
</div>
<input type="hidden" id="budget-id" value="" />
<hr class="margin-top-15">

<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_add.js?v=<?php echo date('Ymd');?>"></script>

<?php $this->load->view('include/footer'); ?>
