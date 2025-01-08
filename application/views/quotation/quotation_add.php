<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-sm-2 hidden-xs padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="" disabled />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" name="customer" id="customer" value="" required />
	</div>

  <div class="col-sm-4 col-xs-12 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm" name="customerName" id="customerName" value="" disabled />
  </div>

	<div class="col-sm-3 col-xs-12 padding-5">
    <label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm" name="contact" id="contact" value="" />
  </div>

  <div class="col-sm-1 col-xs-12 padding-5">
    <label>เงื่อนไข</label>
		<select class="form-control input-sm" name="is_term" id="is_term">
			<option value="0">เงินสด</option>
      <option value="1">เครดิต</option>
    </select>
  </div>

	<div class="col-sm-1 col-xs-12 padding-5">
    <label>เครดิต(วัน)</label>
		<input type="number" class="form-control input-sm text-center" name="credit_term" id="credit_term" value="0" readonly/>
  </div>

	<div class="col-sm-1 col-xs-12 padding-5">
    <label>ยืนราคา(วัน)</label>
		<input type="number" class="form-control input-sm text-center" name="valid_days" id="valid_days" value="30"/>
  </div>

	<div class="col-sm-3 col-xs-12 padding-5">
    <label>ชื่องาน</label>
    <input type="text" class="form-control input-sm" name="title" id="title" value="">
  </div>

  <div class="col-sm-5 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>

  <div class="col-sm-1 padding-5 col-xs-12">
    <label class="display-block not-show">Submit</label>
    <button type="submit" class="btn btn-xs btn-success btn-block"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
<input type="hidden" name="customerCode" id="customerCode" value="" />
</form>

<script src="<?php echo base_url(); ?>scripts/quotation/quotation.js"></script>
<script src="<?php echo base_url(); ?>scripts/quotation/quotation_add.js"></script>

<?php $this->load->view('include/footer'); ?>
