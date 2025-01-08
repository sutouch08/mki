<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
		<?php if($doc->status == 'O' && ($this->pm->can_edit OR $this->pm->can_add)) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn" onclick="goChecking(<?php echo $doc->id; ?>)">ตรวจนับสินค้า</button>
			<button type="button" class="btn btn-white btn-info top-btn" onclick="viewDetail(<?php echo $doc->id; ?>)">ตรวจสอบรายการ</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center e" name="date_add" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" disabled readonly/>
  </div>

  <div class="col-lg-3 col-md-3 col-sm-8 col-xs-6 padding-5">
    <label>หัวข้อ</label>
    <input type="text" class="form-control input-sm e" name="subject" id="subject" value="<?php echo $doc->subject; ?>" disabled/>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm e" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled/>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-9 col-xs-12 padding-5">
    <label class="not-show">โซน</label>
    <input type="text" class="form-control input-sm" name="zone_name" id="zone_name" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>คีย์จำนวน</label>
    <select class="form-control input-sm e" name="allow_input_qty" id="allow_input_qty" disabled>
			<option value="0" <?php echo is_selected('0', $doc->allow_input_qty); ?>>ไม่ได้</option>
			<option value="1" <?php echo is_selected('1', $doc->allow_input_qty); ?>>ได้</option>
		</select>
  </div>

  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm e" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>
	<?php if($this->pm->can_edit) : ?>
	  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
	    <label class="display-block not-show">btn</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-h-edit" onclick="getEdit()"> Edit</button>
	    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-h-update" onclick="update()"> Update</button>
	  </div>
	<?php endif; ?>

	<input type="hidden" id="check_id" value="<?php echo $doc->id; ?>" />
</div>
<hr class="margin-top-15">
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
			<label>บาร์โค้ด</label>
			<input type="text" class="form-control input-sm text-center" name="barcode" value="<?php echo $barcode; ?>" />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
			<label>สินค้า</label>
			<input type="text" class="form-control input-sm text-center" name="pd_code" value="<?php echo $pd_code; ?>" />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
			<label>User</label>
			<input type="text" class="form-control input-sm text-center" name="user" value="<?php echo $user; ?>" />
		</div>

		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
	    <label class="display-block not-show">buton</label>
	    <button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
	  </div>
		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
	    <label class="display-block not-show">buton</label>
	    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearResult()">Reset</button>
	  </div>

		<div class="col-lg-4 col-md-4 col-3 col-xs-4 text-right">
			<label class="display-block not-show">Del</label>
	    <button type="button" class="btn btn-xs btn-danger" onclick="removeSelected()">ลบรายการ</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
		<table class="table table-striped border-1 tableFixHead" style="min-width:1050px;">
			<thead>
				<tr>
					<th class="fix-width-40"></th>
					<th class="fix-width-40">#</th>
					<th class="fix-width-120">บาร์โค้ด</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-300">ชื่อสินค้า</th>
					<th class="fix-width-100 text-right">จำนวน</th>
					<th class="fix-width-100 text-center">User</th>
					<th class="fix-width-150 text-center">เวลา</th>
				</tr>
			</thead>
			<tbody>
				<?php if( ! empty($details)) : ?>
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php foreach($details as $rs) : ?>
					<tr id="row-<?php echo $rs->id; ?>">
						<td class="text-center">
							<input type="checkbox" class="chk" value="<?php echo $rs->id; ?>">
						</td>
						<td class="text-center no"><?php echo number($no); ?></td>
						<td><?php echo $rs->barcode; ?></td>
						<td><?php echo $rs->code; ?></td>
						<td><?php echo $rs->name; ?></td>
						<td class="text-right"><?php echo number($rs->qty); ?></td>
						<td class="text-center"><?php echo $rs->uname; ?></td>
						<td class="text-center"><?php echo thai_date($rs->date_add, TRUE); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/check/check.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	function clearResult() {
		$.ajax({
			url:HOME + 'clear_result',
			type:'GET',
			cache:false,
			success:function() {
				goEdit(<?php echo $doc->id; ?>);
			}
		});
	}

	function removeSelected() {
		let rows = [];

		$('.chk:checked').each(function() {
			rows.push($(this).val());
		});

		if(rows.length > 0) {
			swal({
				title:'คุณแน่ใจ ?',
				text:'ต้องการลบรายการตรวจนับหรือไม่ ?',
				type:'warning',
				showCancelButton:true,
				confirmButtonColor:'#DD6B55',
				confirmButtonText:'ใช่',
				cancelButtonText:'ไม่ใช่',
				closeOnConfirm:true
			},
			function() {
				load_in();
				setTimeout(() => {
					$.ajax({
						url:HOME + 'delete_checked_details',
						type:'POST',
						cache:false,
						data:{
							'rows' : JSON.stringify(rows)
						},
						success:function(rs) {
							load_out();

							if(rs === 'success') {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								});

								rows.forEach((id) => {
									$('#row-'+id).remove();
								});

								reIndex();
							}
							else {
								swal({
									title:'Error!',
									text:rs,
									type:'error'
								});
							}
						}
					})
				}, 200);
			});
		}
	}
</script>

<?php $this->load->view('include/footer'); ?>
