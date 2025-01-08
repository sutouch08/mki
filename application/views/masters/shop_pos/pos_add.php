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
			<input type="text" name="name" id="name" maxlength="200" class="form-control input-sm" value="" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Prefix</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="prefix" id="prefix" maxlength="3" class="form-control input-sm" onkeyup="validCode(this)" value="" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">POS NO</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="pos_no" id="pos_no" maxlength="32" class="form-control input-sm" value=""  />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">POS CODE</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="pos_code" id="pos_code" maxlength="32" class="form-control input-sm" value=""  />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">จุดขาย</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<select class="form-control input-sm" name="shop" id="shop">
				<option value="">เลือก</option>
				<?php echo select_shop_id(); //--- shop_helper ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ขนาดกระดาษ</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<select class="form-control input-sm" name="paper_size" id="paper_size">
				<option value="57">57 mm.</option>
				<option value="80">80 mm.</option>
			</select>
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

<?php if($this->pm->can_add) : ?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs"></label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<button type="button" class="btn btn-sm btn-success pull-right" id="btn-save" onclick="save()"><i class="fa fa-save"></i> Save</button>
    </div>
  </div>
<?php endif; ?>

</form>

<script src="<?php echo base_url(); ?>scripts/masters/pos.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
