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
<?php if($this->pm->can_edit): ?>
<hr class="padding-5"/>
<form class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3 padding-5">
			<input type="text" class="form-control input-sm" value="<?php echo $code; ?>" disabled />
    </div>
  </div>

	<input type="hidden" name="code" id="code" value="<?php echo $code; ?>" />

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3 padding-5">
			<select class="form-control input-sm" name="box_size" id="box-size">
				<option value="">กรุณาเลือก</option>
				<?php echo select_box_size($size_code);  ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs"></label>
    <div class="col-sm-3 col-xs-12 padding-5">
			<label class="display-block not-show visible-xs">save</label>
			<button type="button" class="btn btn-sm btn-success btn-block" id="btn-save" onclick="update()"><i class="fa fa-save"></i> Update</button>
    </div>
  </div>
</form>

<?php endif; ?>

<script src="<?php echo base_url(); ?>scripts/masters/box_code.js"></script>
<?php $this->load->view('include/footer'); ?>
