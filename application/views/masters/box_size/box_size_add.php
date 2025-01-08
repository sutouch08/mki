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
    <div class="col-xs-12 col-sm-3 padding-5">
			<input type="text" name="code" id="code" class="form-control input-sm" value="" onkeyup="validCode(this)" required autofocus />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3 padding-5">
			<input type="text" name="name" id="name" class="form-control input-sm" value="" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชนิด</label>
    <div class="col-xs-12 col-sm-3 padding-5">
			<select class="form-control input-sm" name="box_type" id="box_type">
				<option value="">โปรดเลือก</option>
				<?php echo select_box_type(); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right margin-top-20 hidden-xs">ขนาด</label>
    <div class="col-xs-4 col-sm-1 padding-5">
			<label>กว้าง(cm)</label>
			<input type="number" name="box_width" id="box_width" class="form-control input-sm" value="" />
    </div>
		<div class="col-xs-4 col-sm-1 padding-5">
			<label>ยาว(cm)</label>
			<input type="number" name="box_length" id="box_length" class="form-control input-sm" value="" />
    </div>
		<div class="col-xs-4 col-sm-1 padding-5">
			<label>สูง(cm)</label>
			<input type="number" name="box_height" id="box_height" class="form-control input-sm" value="" />
    </div>
  </div>

<?php if($this->pm->can_add) : ?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs"></label>
    <div class="col-sm-1 col-sm-offset-2 col-xs-12  padding-5">
			<label class="display-block not-show visible-xs">save</label>
			<button type="button" class="btn btn-sm btn-success btn-block" id="btn-save" onclick="save()"><i class="fa fa-save"></i> Save</button>
    </div>
  </div>
<?php endif; ?>

</form>

<script src="<?php echo base_url(); ?>scripts/masters/box_size.js"></script>
<?php $this->load->view('include/footer'); ?>
