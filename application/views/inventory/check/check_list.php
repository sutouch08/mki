<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> &nbsp; เพิ่มใหม่</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
    <label>หัวข้อ</label>
    <input type="text" class="form-control input-sm" name="subject"  value="<?php echo $subject; ?>" />
  </div>

  <div class="col-lg-2 col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm" name="zone_code"  value="<?php echo $zone_code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm" name="user" value="<?php echo $user; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
		<select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">All</option>
			<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
			<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
      <option value="D" <?php echo is_selected('D', $status); ?>>Cancelled</option>
		</select>
  </div>

  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label>วันที่</label>
		<div class="input-daterange input-group width-100">
			<input type="text" class="width-50 text-center from-date" name="from_date" id="from_date" value="<?php echo $from_date; ?>" />
			<input type="text" class="width-50 text-center" name="to_date" id="to_date" value="<?php echo $to_date; ?>" />
		</div>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>

<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
		<table class="table table-striped border-1" style="min-width:1180px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150"></th>
          <th class="fix-width-80 middle">วันที่</th>
					<th class="fix-width-100 middle">เลขที่</th>
					<th class="fix-width-250 middle">หัวข้อ</th>
					<th class="fix-width-60 middle">สถานะ</th>
          <th class="fix-width-150 middle">วันที่ปิด</th>
          <th class="fix-width-100 middle">user</th>
					<th class="min-width-250 middle">โซน</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $no; ?>" class="font-size-12">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle">
							<?php if($rs->status == 'O') : ?>
								<button type="button" class="btn btn-minier btn-primary" onclick="goChecking(<?php echo $rs->id; ?>)">ตรวจนับ</button>
							<?php endif; ?>

							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>

							<?php if($rs->status == 'O' && $this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->id; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($rs->status != 'D' && $this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="cancelCheck(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')">
									<i class="fa fa-times"></i>
								</button>
							<?php endif; ?>
						</td>
            <td class="middle"><?php echo thai_date($rs->date_add); ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->subject; ?></td>
						<td class="middle text-center">
							<?php if($rs->status == 'O') : ?>
								<span class="blue">Open</span>
							<?php elseif($rs->status == 'C') : ?>
								<span class="green">Closed</span>
							<?php elseif($rs->status == 'D') : ?>
								<span class="red">Cancelled</span>
							<?php endif; ?>
						</td>
            <td class="middle text-center"><?php echo (empty($rs->end_date) ? "" : thai_date($rs->end_date, TRUE)); ?></td>
            <td class="middle"><?php echo $rs->user; ?></td>
						<td class="middle"><?php echo $rs->zone_code.' : '.$rs->zone_name; ?></td>

					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/check/check.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
