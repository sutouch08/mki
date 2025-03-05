<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal margin-top-30" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
      <input type="text" class="form-control input-sm" id="code" data-runno="<?php echo $run_no; ?>" maxlength="15" value="<?php echo $code; ?>" disabled />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>


  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-5 col-md-6 col-sm-7 col-xs-12">
			<input type="text" name="name" id="name" class="form-control input-sm" maxlength="200" value="" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ID/Tax ID</label>
    <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-12">
			<input type="text" name="Tax_Id" id="Tax_Id" class="form-control input-sm" value="" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มลูกค้า</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-9 padding-right-5">
			<select name="group" id="group" class="form-control">
				<option value="">เลือก</option>
				<?php echo select_customer_group(); ?>
			</select>
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-left-5">
			<button type="button" class="btn btn-sm btn-success btn-block" onclick="addAttribute('group')"><i class="fa fa-plus"></i></button>
		</div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
  </div>


	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ประเภทลูกค้า</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-9 padding-right-5">
			<select name="kind" id="kind" class="form-control">
				<option value="">เลือก</option>
				<?php echo select_customer_kind(); ?>
			</select>
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-left-5">
			<button type="button" class="btn btn-sm btn-success btn-block" onclick="addAttribute('kind')"><i class="fa fa-plus"></i></button>
		</div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
  </div>


	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ชนิดลูกค้า</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-9 padding-right-5">
			<select name="type" id="type" class="form-control">
				<option value="">เลือก</option>
				<?php echo select_customer_type(); ?>
			</select>
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-left-5">
			<button type="button" class="btn btn-sm btn-success btn-block" onclick="addAttribute('type')"><i class="fa fa-plus"></i></button>
		</div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
  </div>



	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">เกรดลูกค้า</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-9 padding-right-5">
			<select name="class" id="class" class="form-control">
				<option value="">เลือก</option>
				<?php echo select_customer_class(); ?>
			</select>
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-left-5">
			<button type="button" class="btn btn-sm btn-success btn-block" onclick="addAttribute('class')"><i class="fa fa-plus"></i></button>
		</div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="class-error"></div>
  </div>


	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">พื้นที่ขาย</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-9 padding-right-5">
			<select name="area" id="area" class="form-control">
				<option value="">เลือก</option>
				<?php echo select_customer_area(); ?>
			</select>
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-left-5">
			<button type="button" class="btn btn-sm btn-success btn-block" onclick="addAttribute('area')"><i class="fa fa-plus"></i></button>
		</div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>

	<div class="form-group">
	 <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ช่องทางขาย</label>
	 <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
		 <select name="channels" id="channels" class="form-control">
			 <option value="">เลือก</option>
			 <?php echo select_channels(); ?>
		 </select>
	 </div>
	</div>


	<div class="form-group">
	 <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">พนักงานขาย</label>
	 <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
		 <select name="sale" id="sale" class="form-control">
			 <option value="">เลือก</option>
			 <?php echo select_sale(); ?>
		 </select>
	 </div>
	</div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">เครดิตเทอม</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
			<div class="input-group width-100">
				<input type="number" name="credit_term" id="credit_term" class="form-control input-sm" value="" />
				<span class="input-group-addon">วัน</span>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">วงเงินเครติด</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
			<input type="text" name="CreditLine" id="CreditLine" class="form-control input-sm" value="" />
    </div>
  </div>


	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">หมายเหตุ</label>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
      <textarea class="form-control input-sm" name="note" id="note" rows="5"></textarea>
    </div>
  </div>




	<div class="divider-hidden"></div>
	<?php if($this->pm->can_add) : ?>
  <div class="form-group">
		<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="saveAdd()"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<?php endif; ?>
</form>
<?php $this->load->view('masters/customers/customer_modal'); ?>
<script src="<?php echo base_url(); ?>scripts/masters/customers.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
