<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" class="form-control input-sm code" name="code" id="code" maxlength="20" value="" onkeyup="validCode(this)" required/>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" class="form-control input-sm" name="name" id="name" maxlength="100" value="" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">ประเภทคลัง</label>
 	 <div class="col-xs-12 col-sm-3">
 		 <select class="form-control input-sm" name="role" id="role" required>
 		 	<option value="">เลือก</option>
			<?php echo select_warehouse_role(); ?>
 		 </select>
 	 </div>
	 <div class="help-block col-xs-12 col-sm-reset inline red" id="role-error"></div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">อนุญาติให้ขาย</label>
 	 <div class="col-xs-12 col-sm-2">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 btn-success" id="btn-sell-yes" onclick="toggleSell(1)">ใช่</button>
			<button type="button" class="btn btn-sm width-50" id="btn-sell-no" onclick="toggleSell(0)">ไม่ใช่</button>
 		</div>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">อนุญาติให้จัด</label>
 	 <div class="col-xs-12 col-sm-2">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 btn-success" id="btn-prepare-yes" onclick="togglePrepare(1)">ใช่</button>
			<button type="button" class="btn btn-sm width-50" id="btn-prepare-no" onclick="togglePrepare(0)">ไม่ใช่</button>
 		</div>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">อนุญาติให้ติดลบ</label>
 	 <div class="col-xs-12 col-sm-2">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50" id="btn-auz-yes" onclick="toggleAuz(1)">ใช่</button>
			<button type="button" class="btn btn-sm width-50 btn-danger" id="btn-auz-no" onclick="toggleAuz(0)">ไม่ใช่</button>
 		</div>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">เปิดใช้งาน</label>
 	 <div class="col-xs-12 col-sm-2">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 btn-success" id="btn-active-yes" onclick="toggleActive(1)">ใช่</button>
			<button type="button" class="btn btn-sm width-50" id="btn-active-no" onclick="toggleActive(0)">ไม่ใช่</button>
 		</div>
 	 </div>
  </div>



	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="checkAdd()"><i class="fa fa-save"></i> บันทึก</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="sell" id="sell" value="1">
	<input type="hidden" name="prepare" id="prepare" value="1">
	<input type="hidden" name="auz" id="auz" value="0">
	<input type="hidden" name="active" id="active" value="1">
</form>

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js"></script>
<?php $this->load->view('include/footer'); ?>
