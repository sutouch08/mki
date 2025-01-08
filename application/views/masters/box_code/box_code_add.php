<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><i class="fa fa-cubes"></i> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
			<button type="button" class="btn btn-sm btn-info" onclick="getTemplate()"><i class="fa fa-download"></i> Download Template</button>
			<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-purple" onclick="getUploadFile()"><i class="fa fa-file-excel-o"></i> Import Excel</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<?php if($this->pm->can_add): ?>
<hr class="padding-5"/>
<div class="row">
	<div class="col-sm-2 col-xs-12 padding-5">
		<label>ขนาดกล่อง</label>
		<select class="form-control input-sm" name="box_size" id="box-size">
			<option value="">กรุณาเลือก</option>
			<?php echo select_box_size();  ?>
		</select>
	</div>
	<div class="col-sm-2 col-xs-12 padding-5">
		<label>รหัสกล่อง</label>
		<input type="text" class="form-control input-sm" name="box_code" id="box-code" onkeyup="validCode(this)"/>
	</div>
	<div class="col-sm-1 col-sx-12 padding-5">
		<label class="display-block not-show">button</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="addBoxCode()"><i class="fa fa-plus"></i> เพิ่ม</button>
	</div>
</div>
<?php endif; ?>
<hr class="padding-5"/>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 text-center">#</th>
					<th class="width-10">รหัส</th>
					<th class="">ขนาดกล่อง</th>
					<th class="width-15 text-center">ชนิด</th>
					<th class="width-10 text-center">กว้าง(cm)</th>
					<th class="width-10 text-center">ยาว(cm)</th>
					<th class="width-10 text-center">สูง(cm)</th>
				</tr>
			</thead>
			<tbody id="added-table">

			</tbody>
		</table>
	</div>
</div>


<script id="added-template" type="text/x-handlebarsTemplate">
<tr>
	<td class="text-center no"></td>
	<td class="">{{code}}</td>
	<td class="text-center">{{size_name}}</td>
	<td class="text-center">{{type_name}}</td>
	<td class="text-center">{{box_width}}</td>
	<td class="text-center">{{box_length}}</td>
	<td class="text-center">{{box_height}}</td>
</tr>
</script>

<?php $this->load->view('masters/box_code/import_box_code'); ?>
<script src="<?php echo base_url(); ?>scripts/masters/box_code.js"></script>
<?php $this->load->view('include/footer'); ?>
