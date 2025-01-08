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
<hr class="padding-5 margin-bottom-20"/>

<form class="form-horizontal" id="addForm">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3 padding-5">
      <input type="text" name="code" id="code" class="form-control input-sm" value="<?php echo $code; ?>" disabled />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3 padding-5">
			<input type="text" name="name" id="name" class="form-control input-sm" value="<?php echo $name; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs"></label>
    <div class="col-sm-1 col-sm-offset-2 col-xs-12  padding-5">
			<button type="button" class="btn btn-sm btn-success btn-block" onclick="update()"><i class="fa fa-save"></i> Update</button>
    </div>
  </div>

	<input type="hidden" name="old_code" id="old_code" value="<?php echo $code; ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/masters/box_type.js"></script>
<?php $this->load->view('include/footer'); ?>
