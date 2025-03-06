<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"> <?php echo $this->title; ?> </h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    	<p class="pull-right top-p">
				<?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-xs btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-5 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="active" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
      <option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
    </select>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-2 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i><span class="hidden-xs">Search</span></button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-2 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i><span class="hidden-xs">Reset</span></button>
  </div>
</div>
<hr class="padding-5 margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-60 middle text-center">Status</th>
					<th class="min-width-200 middle">Name</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="<?php echo $rs->id; ?>">
						<td class="middle">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->id; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete('<?php echo $rs->id; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center">
							<?php echo is_active($rs->active); ?>
						</td>
						<td class="middle"><?php echo $rs->name; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/shipping_round.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
