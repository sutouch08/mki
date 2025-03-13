<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row padding-top-20">
	<div class="col-lg-3 col-md-3 col-sm-3 control-label text-right hidden-xs">ชื่อ</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="visible-xs">ชื่อ</label>
		<input type="text" name="name" id="name" class="width-100" maxlength="100" value="<?php echo $ds->name; ?>" autofocus/>
	</div>
	<div class="divider-hidden hidden-xs"></div>

	<div class="col-lg-3 col-md-3 col-sm-3 control-label text-right hidden-xs">ลำดับ</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="visible-xs">ลำดับ</label>
		<select class="width-100" name="postition" id="position">
			<option value="10" <?php echo is_selected('10', $ds->position); ?>>10</option>
			<option value="9" <?php echo is_selected('9', $ds->position); ?>>9</option>
			<option value="8" <?php echo is_selected('8', $ds->position); ?>>8</option>
			<option value="7" <?php echo is_selected('7', $ds->position); ?>>7</option>
			<option value="6" <?php echo is_selected('6', $ds->position); ?>>6</option>
			<option value="5" <?php echo is_selected('5', $ds->position); ?>>5</option>
			<option value="4" <?php echo is_selected('4', $ds->position); ?>>4</option>
			<option value="3" <?php echo is_selected('3', $ds->position); ?>>3</option>
			<option value="2" <?php echo is_selected('2', $ds->position); ?>>2</option>
			<option value="1" <?php echo is_selected('1', $ds->position); ?>>1</option>
		</select>
	</div>
	<div class="divider-hidden hidden-xs"></div>

	<div class="col-lg-3 col-md-3 col-sm-3 control-label text-right hidden-xs"></div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="visible-xs">&nbsp;</label>
		<label style="padding-top:5px;">
			<input name="active" id="active" class="ace" type="checkbox" value="1" <?php echo is_checked('1', $ds->active); ?> />
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
	<input type="hidden" id="id" value="<?php echo $ds->id; ?>" />
</div>


<script src="<?php echo base_url(); ?>scripts/masters/order_round.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
