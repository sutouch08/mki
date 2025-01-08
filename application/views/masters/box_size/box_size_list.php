<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <i class="fa fa-cubes"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-sm-2 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm text-center search-box" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm text-center search-box" name="box_name" id="box_name" value="<?php echo $box_name; ?>" />
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>ประเภท</label>
		<select class="form-control input-sm" name="box_type" id="box_type" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_box_type($box_type); ?>
		</select>
  </div>

	<div class="col-sm-1 col-xs-4 padding-5">
		<label>กว้าง</label>
		<input type="number" class="form-control input-sm text-center search-box" name="box_width" id="box_width" value="<?php echo $box_width; ?>" />
	</div>

	<div class="col-sm-1 col-xs-4 padding-5">
		<label>ยาว</label>
		<input type="number" class="form-control input-sm text-center search-box" name="box_length" id="box_length" value="<?php echo $box_length; ?>" />
	</div>

	<div class="col-sm-1 col-xs-4 padding-5">
		<label>สูง</label>
		<input type="number" class="form-control input-sm text-center search-box" name="box_height" id="box_height" value="<?php echo $box_height; ?>" />
	</div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12 padding-5">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-10 middle">รหัส</th>
					<th class="width-30 middle">ชื่อ</th>
					<th class="width-10 middle text-center">ประเภท</th>
					<th class="width-10 middle text-center">กว้าง(cm)</th>
					<th class="width-10 middle text-center">ยาว(cm)</th>
          <th class="width-10 middle text-center">สูง(cm)</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $no; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo $rs->type_name; ?></td>
						<td class="middle text-center"><?php echo $rs->box_width; ?></td>
						<td class="middle text-center"><?php echo $rs->box_length; ?></td>
						<td class="middle text-center"><?php echo $rs->box_height; ?></td>
						<td class="text-right">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>', '<?php echo $no; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/box_size.js"></script>

<?php $this->load->view('include/footer'); ?>
