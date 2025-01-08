<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <i class="fa fa-credit-card"></i> <?php echo $this->title; ?>
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
    <input type="text" class="form-control input-sm text-center search" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm text-center search" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
		<label>ประเภท</label>
		<select class="form-control input-sm" name="role" id="role" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_payment_role($role); ?>
		</select>
	</div>

  <div class="col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>

</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-10 middle">รหัส</th>
					<th class="width-30 middle">ชื่อ</th>
					<th class="width-10 middle text-center">ประเภท</th>
					<th class="width-10 middle text-center">เครติด</th>
					<th class="width-10 middle text-center">Default</th>
          <th class="width-15 middle">ปรับปรุงล่าสุด</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo $rs->role_name; ?></td>
						<td class="middle text-center">
							<?php if($rs->has_term == 1) : ?>
								<i class="fa fa-check green"></i>
							<?php endif; ?>
						</td>
						<td class="middle text-center">
							<?php if($rs->is_default) : ?>
								<i class="fa fa-check green"></i>
							<?php endif; ?>
						</td>
            <td class="middle"><?php echo thai_date($rs->date_upd,TRUE, '/'); ?></td>
						<td class="text-right">
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

<script src="<?php echo base_url(); ?>scripts/masters/payment_methods.js"></script>

<?php $this->load->view('include/footer'); ?>
