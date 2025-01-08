<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal" id="addForm" method="post">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-2">
      <input type="text" class="width-100 code" maxlength="20" value="<?php echo $code; ?>" readonly />
    </div>

  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="name" id="name" class="width-100" maxlength="250" value="<?php echo $name; ?>" readonly/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เครดิตเทอม(วัน)</label>
    <div class="col-xs-12 col-sm-1">
			<input type="number" name="credit_term" id="credit_term" class="width-100" value="<?php echo $credit_term; ?>" readonly/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เลขที่ประจำตัวผู้เสียภาษี</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="tax_id" id="tax_id" class="width-100" maxlength="20" value="<?php echo $tax_id; ?>" readonly/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อสาขา</label>
    <div class="col-xs-12 col-sm-2">
			<input type="text" name="branch_name" id="branch_name" class="width-100" maxlength="50" value="<?php echo $branch_name; ?>" readonly/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ที่อยู่</label>
    <div class="col-xs-12 col-sm-6">
			<input type="text" name="address" id="address" class="width-100" value="<?php echo $address; ?>" readonly />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="address-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เบอร์โทร</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="phone" id="phone" class="width-100" maxlength="50" value="<?php echo $phone; ?>" readonly/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="phone-error"></div>
  </div>


	<?php $yes = $active == 1 ? 'btn-success' : ''; ?>
	<?php $no = $active == 0 ? 'btn-danger' : ''; ?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">สถานะ</label>
    <div class="col-xs-12 col-sm-3">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $yes; ?> width-50">ใช้งาน</button>
				<button type="button" class="btn btn-sm <?php echo $no; ?> width-50">ไม่ใช้งาน</button>
			</div>
    </div>
  </div>

	<div class="divider-hidden"></div>

</form>

<script src="<?php echo base_url(); ?>scripts/masters/vender.js"></script>
<?php $this->load->view('include/footer'); ?>
