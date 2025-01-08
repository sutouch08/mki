<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><i class="fa fa-cubes"></i> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 padding-top-20"/>

<form class="form-horizontal">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="code" id="code" maxlength="20" class="form-control input-sm" value="" onkeyup="validCode(this)" required autofocus />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="name" id="name" maxlength="250" class="form-control input-sm" value="" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">โซน</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="zone" id="zone" maxlength="250" class="form-control input-sm" value=""  />
			<input type="hidden" name="zone_code" id="zone_code" value="" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ลูกค้า</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="customer" id="customer" maxlength="250" class="form-control input-sm" value=""  />
			<input type="hidden" name="customer_code" id="customer_code" value="" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ข้อความหัวบิล 1</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="bill_logo" id="bill_logo" maxlength="50" class="form-control input-sm" value=""  />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ข้อความหัวบิล 2</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="bill_header" id="bill_header" maxlength="100" class="form-control input-sm" value=""  />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ข้อความหัวบิล 3</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="bill_text" id="bill_text" maxlength="100" class="form-control input-sm" value=""  />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ข้อความท้ายบิล</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="bill_footer" id="bill_footer" maxlength="250" class="form-control input-sm" value=""  />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เลขประจำตัวผู้เสียภาษี</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="tax_id" id="tax_id" maxlength="20" class="form-control input-sm" value=""  />
    </div>
  </div>


	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">VAT</label>
 	 <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 btn-success" id="btn-vat-yes" onclick="toggleVat(1)">มี</button>
			<button type="button" class="btn btn-sm width-50" id="btn-vat-no" onclick="toggleVat(0)">ไม่มี</button>
 		</div>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">เปิดใช้งาน</label>
 	 <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 btn-success" id="btn-active-yes" onclick="toggleActive(1)">ใช่</button>
			<button type="button" class="btn btn-sm width-50" id="btn-active-no" onclick="toggleActive(0)">ไม่ใช่</button>
 		</div>
 	 </div>
  </div>

	<input type="hidden" id="active" name="active" value="1" />
	<input type="hidden" id="use_vat" name="use_vat" value="1" />

<?php if($this->pm->can_add) : ?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs"></label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<button type="button" class="btn btn-sm btn-success pull-right" id="btn-save" onclick="save()"><i class="fa fa-save"></i> Save</button>
    </div>
  </div>
<?php endif; ?>

</form>

<script src="<?php echo base_url(); ?>scripts/masters/shop.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
