<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-lg-2 col-md-5 col-sm-4 col-xs-6 padding-5">
			<label>พนักงาน</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-5 col-sm-6 col-xs-6 padding-5">
			<label>โซน</label>
			<select class="width-100 filter" name="zone" id="zone">
				<option value="all">ทั้งหมด</option>
				<?php echo select_consign_zone($zone_code); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group">
				<input type="text" class="form-control input-sm width-50 from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<hr class="padding-5 margin-top-10"/>
	<div class="row margin-top-10">
		<div class="col-sm-12 col-xs-12 padding-5">
			<button type="button" id="btn-state-1" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_1']; ?>" onclick="toggleState(1)">รอดำเนินการ</button>
			<button type="button" id="btn-state-2" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_2']; ?>" onclick="toggleState(2)">รอชำระเงิน</button>
			<button type="button" id="btn-state-3" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_3']; ?>" onclick="toggleState(3)">รอจัด</button>
			<button type="button" id="btn-state-4" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_4']; ?>" onclick="toggleState(4)">กำลังจัด</button>
			<button type="button" id="btn-state-5" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_5']; ?>" onclick="toggleState(5)">รอตรวจ</button>
			<button type="button" id="btn-state-6" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_6']; ?>" onclick="toggleState(6)">กำลังตรวจ</button>
			<button type="button" id="btn-state-7" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_7']; ?>" onclick="toggleState(7)">รอเปิดบิล</button>
			<button type="button" id="btn-state-8" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_8']; ?>" onclick="toggleState(8)">เปิดบิลแล้ว</button>
			<button type="button" id="btn-state-9" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['state_9']; ?>" onclick="toggleState(9)">ยกเลิก</button>
			<button type="button" id="btn-not-save" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['not_save']; ?>" onclick="toggleNotSave()">ไม่บันทึก</button>
			<button type="button" id="btn-only-me" class="btn btn-xs btn-100 margin-bottom-5 btn-state <?php echo $btn['only_me']; ?>" onclick="toggleOnlyMe()">เฉพาะฉัน</button>
		</div>
	</div>

	<input type="hidden" name="state_1" id="state_1" value="<?php echo $state[1]; ?>" />
	<input type="hidden" name="state_2" id="state_2" value="<?php echo $state[2]; ?>" />
	<input type="hidden" name="state_3" id="state_3" value="<?php echo $state[3]; ?>" />
	<input type="hidden" name="state_4" id="state_4" value="<?php echo $state[4]; ?>" />
	<input type="hidden" name="state_5" id="state_5" value="<?php echo $state[5]; ?>" />
	<input type="hidden" name="state_6" id="state_6" value="<?php echo $state[6]; ?>" />
	<input type="hidden" name="state_7" id="state_7" value="<?php echo $state[7]; ?>" />
	<input type="hidden" name="state_8" id="state_8" value="<?php echo $state[8]; ?>" />
	<input type="hidden" name="state_9" id="state_9" value="<?php echo $state[9]; ?>" />
	<input type="hidden" name="notSave" id="notSave" value="<?php echo $notSave; ?>" />
	<input type="hidden" name="onlyMe" id="onlyMe" value="<?php echo $onlyMe; ?>" />
</form>
<hr class="margin-top-10">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered table-hover" style="min-width:950px;">
			<thead>
				<tr>
					<th class="fix-width-50 middle text-center">ลำดับ</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-150 middle">เลขที่เอกสาร</th>
					<th class="min-wdith-250 middle">ลูกค้า</th>
					<th class="fix-width-200 middle">โซน</th>
					<th class="fix-width-100 middle">ยอดเงิน</th>
					<th class="fix-width-100 middle">สถานะ</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <tr id="row-<?php echo $rs->code; ?>" class="font-size-12" style="<?php echo state_color($rs->state, $rs->status, $rs->is_expired); ?>">
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $no; ?></td>
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->code; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->customer_name; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->zone_name; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo number($rs->total_amount, 2); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->state_name; ?></td>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php if($this->menu_code == 'SOCCSO') : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign.js"></script>
<?php else : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_tr.js"></script>
<?php endif; ?>
<script>
	$('#user').select2();
	$('#zone').select2();
</script>
<?php $this->load->view('include/footer'); ?>
