<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><i class="fa fa-credit-card"></i> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 code" maxlength="15" value="" onkeyup="validCode(this)" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" maxlength="50" value="" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ประเภท</label>
    <div class="col-xs-12 col-sm-3">
			<select name="role" id="role" class="form-control input-sm" onchange="toggleRole()" required>
				<option value="">โปรดเลือก</option>
				<?php echo select_payment_role(); ?>
			</select>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="role-error"></div>
  </div>

	<div class="form-group hide" id="bank_account">
    <label class="col-sm-3 control-label no-padding-right">เลขที่บัญชี</label>
    <div class="col-xs-12 col-sm-3">
			<select name="acc_no" id="acc_no" class="form-control input-sm">
				<option value="">โปรดเลือก</option>
				<?php echo select_bank_account(); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">&nbsp;</label>
    <div class="col-xs-12 col-sm-3">
			<label>
				<input type="checkbox" class="ace" id="active" value="1" checked/>
				<span class="lbl">&nbsp;&nbsp; Active</span>
			</label>
    </div>
  </div>


	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="save_add()"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="payment_methods_code" id="payment_methods_code" value="0" />
</form>

<script src="<?php echo base_url(); ?>scripts/masters/payment_methods.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
