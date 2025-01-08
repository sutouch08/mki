<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="newUser()"><i class="fa fa-plus"></i> เพิมใหม่</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>User name</label>
    <input type="text" class="form-control input-sm" name="user" value="<?php echo $uname; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Display name</label>
    <input type="text" class="form-control input-sm" name="dname" value="<?php echo $dname; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Profile</label>
		<select class="form-control input-sm" name="profile">
			<option value="all">ทั้งหมด</option>
			<?php echo select_profile($profile); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Status</label>
		<select class="form-control input-sm" name="status">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>Inactive</option>
		</select>
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th style="width:50px;" class="middle text-center">ลำดับ</th>
					<th style="min-width:100px;" class="middle">User name</th>
					<th style="min-width:150px;" class="middle">Display name</th>
					<th style="min-width:100px;" class="middle">Profile</th>
					<th style="width:120px;" class="middle text-center">Create at</th>
					<th style="width:80px;"  class="middle text-center">Status</th>
					<th style="min-width:150px;"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<?php $id_profile = $this->_user->id_profile; //--- current user profile ?>
					<?php if( ($rs->id_profile != -987654321) OR $id_profile == -987654321) : ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle"><?php echo $rs->dname; ?></td>
						<td class="middle"><?php echo $rs->pname; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '.'); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="text-right">
							<?php if(($this->pm->can_edit && $rs->id_profile != -987654321) OR (get_cookie('id_profile') == -987654321)) : ?>
								<button type="button" class="btn btn-mini btn-info" title="Reset password" onclick="getReset(<?php echo $rs->id; ?>)">
									<i class="fa fa-key"></i>
								</button>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if(($this->pm->can_delete && $rs->id_profile != -987654321) OR (get_cookie('id_profile') == -987654321)) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->uname; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/users/users.js"></script>

<?php $this->load->view('include/footer'); ?>
