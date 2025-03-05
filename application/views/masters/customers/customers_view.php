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
  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>กลุ่มลูกค้า</label>
    <select class="form-control input-sm filter" name="group" id="customer_group">
			<option value="all">ทั้งหมด</option>
			<?php echo select_customer_group($group); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ประเภทลูกค้า</label>
    <select class="form-control input-sm filter" name="kind" id="customer_kind">
			<option value="all">ทั้งหมด</option>
			<?php echo select_customer_kind($kind); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ชนิดลูกค้า</label>
    <select class="form-control input-sm filter" name="type" id="customer_type">
			<option value="all">ทั้งหมด</option>
			<?php echo select_customer_type($type); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>เกรดลูกค้า</label>
    <select class="form-control input-sm filter" name="class" id="customer_class">
			<option value="all">ทั้งหมด</option>
			<?php echo select_customer_class($class); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>พื้นที่ขาย</label>
    <select class="form-control input-sm filter" name="area" id="customer_area">
			<option value="all">ทั้งหมด</option>
			<?php echo select_customer_area($area); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
    <select class="form-control input-sm filter" name="channels" id="channels">
			<option value="all">ทั้งหมด</option>
			<?php echo select_channels($channels); ?>
		</select>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="submit" class="btn btn-sm btn-primary btn-block">ค้นหา</button>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()">รีเซ็ต</button>
  </div>

</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive border-1 padding-0">
		<table class="table table-striped" style="min-width:1120px; margin-bottom:0px;">
			<thead>
				<tr>
					<th class="fix-width-120"></th>
					<th class="fix-width-50 middle text-center">No.</th>
					<th class="fix-width-100 middle">รหัส</th>
					<th class="min-width-250 middle">ชื่อ</th>
					<th class="fix-width-100 middle">กลุ่ม</th>
					<th class="fix-width-100 middle">ชนิด</th>
					<th class="fix-width-100 middle">ประเภท</th>
					<th class="fix-width-100 middle">ช่องทางขาย</th>
					<th class="fix-width-100 middle">เกรด</th>
					<th class="fix-width-100 middle">พื้นที่ขาย</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr style="font-size:11px;">
						<td class="middle">
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
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->group; ?></td>
						<td class="middle"><?php echo $rs->type; ?></td>
						<td class="middle"><?php echo $rs->kind; ?></td>
						<td class="middle"><?php echo $rs->channels; ?></td>
						<td class="middle"><?php echo $rs->class; ?></td>
						<td class="middle"><?php echo $rs->area; ?></td>
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
