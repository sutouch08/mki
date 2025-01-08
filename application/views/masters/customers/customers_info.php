<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update"; ?>">

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
      <input type="text" class="form-control input-sm code" value="<?php echo $ds->code; ?>" disabled />
			<input type="hidden" name="code" id="code" value="<?php echo $ds->code; ?>"/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-5 col-md-6 col-sm-7 col-xs-12">
			<input type="text" name="name" id="name" class="form-control input-sm" maxlength="200" value="<?php echo $ds->name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ID/Tax ID</label>
    <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-12">
			<input type="text" name="Tax_Id" id="Tax_Id" class="form-control input-sm" value="<?php echo $ds->Tax_Id; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มลูกค้า</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-9 padding-right-5">
			<select name="group" id="group" class="form-control">
				<option value="">เลือก</option>
				<?php echo select_customer_group($ds->group_code); ?>
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
				<?php echo select_customer_kind($ds->kind_code); ?>
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
				<?php echo select_customer_type($ds->type_code); ?>
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
				<?php echo select_customer_class($ds->class_code); ?>
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
				<?php echo select_customer_area($ds->area_code); ?>
			</select>
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-left-5">
			<button type="button" class="btn btn-sm btn-success btn-block" onclick="addAttribute('area')"><i class="fa fa-plus"></i></button>
		</div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>


	<div class="form-group">
	 <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">พนักงานขาย</label>
	 <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
		 <select name="sale" id="sale" class="form-control">
			 <?php echo select_sale($ds->sale_code); ?>
		 </select>
	 </div>
	</div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">เครดิตเทอม</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
			<div class="input-group width-100">
				<input type="number" name="credit_term" id="credit_term" class="form-control input-sm" value="<?php echo $ds->credit_term; ?>" />
				<span class="input-group-addon">วัน</span>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">วงเงินเครติด</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
			<input type="text" name="CreditLine" id="CreditLine" class="form-control input-sm" value="<?php echo number($ds->amount, 2); ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">วงเงินใช้ไป</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
			<input type="text" class="form-control input-sm" value="<?php echo number($ds->used,2); ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">วงเงินคงเหลือ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" class="form-control input-sm" value="<?php echo number($ds->balance, 2); ?>" disabled/>
    </div>
  </div>


  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">หมายเหตุ</label>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
      <textarea class="form-control input-sm" name="note" id="note" rows="5"><?php echo $ds->note; ?></textarea>

    </div>
  </div>


	<div class="divider-hidden"></div>

  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<div class="divider-hidden"></div>

	<input type="hidden" name="old_name" id="old_name" value="<?php echo $ds->name; ?>" />
	<input type="hidden" name="customers_code" id="customers_code" value="<?php echo $ds->code; ?>" />
</form>
<?php $this->load->view('masters/customers/customer_modal'); ?>
