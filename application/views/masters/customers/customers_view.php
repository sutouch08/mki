<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title hidden-xs"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    	<p class="pull-right top-p">
				<?php if($this->_SuperAdmin) : ?>
					<button type="button" class="btn btn-sm btn-primary" onclick="getUploadFile()"><i class="fa fa-upload"></i> Import</button>
					<button type="button" class="btn btn-sm btn-purple" onclick="get_template()"><i class="fa fa-download"></i> Template</button>
				<?php endif; ?>
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>กลุ่มลูกค้า</label>
    <select class="form-control input-sm filter" name="group" id="customer_group">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_group($group); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ประเภทลูกค้า</label>
    <select class="form-control input-sm filter" name="kind" id="customer_kind">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_kind($kind); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ชนิดลูกค้า</label>
    <select class="form-control input-sm filter" name="type" id="customer_type">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_type($type); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>เกรดลูกค้า</label>
    <select class="form-control input-sm filter" name="class" id="customer_class">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_class($class); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>พื้นที่ขาย</label>
    <select class="form-control input-sm filter" name="area" id="customer_area">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_area($area); ?>
		</select>
  </div>

  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
		<div class="btn-group width-100">
			<button type="submit" class="btn btn-sm btn-primary width-50">ค้นหา</button>
			<button type="button" class="btn btn-sm btn-warning width-50" onclick="clearFilter()">รีเซ็ต</button>
		</div>
  </div>

</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive border-1 padding-0">
		<table class="table table-striped table-hover" style="margin-bottom:0px;">
			<thead>
				<tr>
					<th class="width-5 middle text-center">No.</th>
					<th class="width-10 middle">รหัส</th>
					<th class="width-30 middle">ชื่อ</th>
					<th class="width-10 middle  hidden-xs">กลุ่ม</th>
					<th class="width-10 middle hidden-xs">ประเภท</th>
					<th class="width-10 middle hidden-xs">ชนิด</th>
					<th class="width-10 middle hidden-xs">เกรด</th>
					<th class="width-15"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr style="font-size:11px;">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle  hidden-xs"><?php echo $rs->group; ?></td>
						<td class="middle hidden-xs"><?php echo $rs->kind; ?></td>
						<td class="middle hidden-xs"><?php echo $rs->type; ?></td>
						<td class="middle hidden-xs"><?php echo $rs->class; ?></td>
						<td class="text-right">
							<button type="button" class="btn btn-mini btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')">
								<i class="fa fa-eye"></i>
							</button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
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

<?php $this->load->view('masters/customers/import_customers'); ?>
<script src="<?php echo base_url(); ?>scripts/masters/customers.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
