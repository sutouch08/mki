<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-6">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row padding-top-20">
	<div class="col-lg-3 col-md-3 col-sm-3 control-label text-right hidden-xs">รหัส</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="visible-xs">รหัส</label>
		<input type="text" name="code" id="code" class="width-100 code" maxlength="15" value="<?php echo $ds->code; ?>" disabled />
	</div>
	<div class="divider-hidden hidden-xs"></div>


	<div class="col-lg-3 col-md-3 col-sm-3 control-label text-right hidden-xs">ชื่อ</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="visible-xs">ชื่อ</label>
		<input type="text" name="name" id="name" class="width-100" maxlength="100" value="<?php echo $ds->name; ?>" required />
	</div>
	<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	<div class="divider-hidden hidden-xs"></div>

	<div class="col-lg-3 col-md-3 col-sm-3 control-label text-right hidden-xs"></div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
		<label style="padding-top:5px;">
			<input name="active" id="active" class="ace" type="checkbox" value="1" <?php echo is_checked($ds->active, 1); ?> />
			<span class="lbl">  Active</span>
		</label>
	</div>
	<div class="divider-hidden hidden-xs"></div>

	<div class="col-lg-3 col-md-3 col-sm-3 control-label text-right hidden-xs"></div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
		<p class="pull-right">
			<button type="button" class="btn btn-sm btn-success" onclick="update()"><i class="fa fa-save"></i> Update</button>
		</p>
	</div>
</div>



<script src="<?php echo base_url(); ?>scripts/masters/saleman.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
