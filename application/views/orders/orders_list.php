<?php $this->load->view('include/header'); ?>
<?php $can_upload = getConfig('ALLOW_UPLOAD_ORDER'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if($this->pm->can_add) : ?>
				<?php if($can_upload == 1) : ?>
					<button type="button" class="btn btn-white btn-purple" onclick="getUploadFile()">นำเข้าออเดอร์</button>
				<?php endif;?>
				<button type="button" class="btn btn-white btn-purple btn-100" onclick="getTemplate()"><i class="fa fa-download"></i> &nbsp; Template</button>
				<button type="button" class="btn btn-white btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>อ้างอิง[MKP]</label>
		<input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>อ้างอิง[CRM]</label>
		<input type="text" class="form-control input-sm search" name="reference2" value="<?php echo $reference2; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>CSR</label>
		<select class="form-control input-sm filter" name="sale_code">
			<option value="all">ทั้งหมด</option>
			<?php echo select_saleman($sale_code); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>SALE</label>
		<select class="form-control input-sm filter" name="type_code">
			<option value="all">ทั้งหมด</option>
			<?php echo select_customer_type($type_code); ?>
		</select>
  </div>

	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ผู้ดำเนินการ</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่จัดส่ง</label>
		<input type="text" class="form-control input-sm search" name="shipCode" value="<?php echo $ship_code; ?>" />
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ช่องทางการขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_channels($channels); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ช่องทางการชำระเงิน</label>
		<select class="form-control input-sm" name="payment" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_payment_method($payment); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>การชำระเงิน</label>
		<select class="form-control input-sm" name="is_paid" onchange="getSearch()">
			<option value="all" <?php echo is_selected('all', $is_paid); ?>>ทั้งหมด</option>
			<option value="paid" <?php echo is_selected('paid', $is_paid); ?>>จ่ายแล้ว</option>
			<option value="not_paid" <?php echo is_selected('not_paid', $is_paid); ?>>ยังไม่จ่าย</option>
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
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
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
<input type="hidden" name="isExpire" id="isExpire" value="<?php echo $isExpire; ?>" />

<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
<input type="hidden" name="search" value="1" />
<hr class="padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>
<?php $sort_code = $order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered table-hover dataTable" style="min-width:1930px;">
			<thead>
				<tr>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-100 middle text-center sorting <?php echo $sort_date; ?>" id="sort_date_add" onclick="sort('date_add')">วันที่</th>
					<th class="fix-width-150 middle text-center">เวลาที่ดำเนินการ</th>
					<th class="fix-width-120 middle sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่เอกสาร</th>
					<th class="min-width-250 middle text-center">ลูกค้า</th>
					<th class="fix-width-100 middle text-center">ยอดเงิน</th>
					<th class="fix-width-150 middle text-center">ช่องทางขาย</th>
					<th class="fix-width-100 middle text-center">การชำระเงิน</th>
					<th class="fix-width-100 middle text-center">สถานะ</th>
					<th class="fix-width-150 middle text-center">อ้างอิง[MKP]</th>
					<th class="fix-width-120 middle text-center">ผู้ดำเนินการ</th>
					<th class="fix-width-200 middle text-center">CSR</th>
					<th class="fix-width-120 middle text-center">SALE</th>
					<th class="fix-width-150 middle text-center">อ้างอิง[CRM]</th>
					<th class="fix-width-120 middle text-center">เลขที่จัดส่ง</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
					<?php $ch = channels_array(); /// channels_helper ?>
					<?php $pm = payment_method_array(); //-- payment_method_helper ?>
					<?php $sa = saleman_array(); //-- saleman_helper ?>
					<?php $user = user_array(); //-- user_helper ?>
					<?php $type = customer_type_array(); //--- customer_helper ?>
          <?php foreach($orders as $rs) : ?>
						<?php $payment = empty($pm[$rs->payment_code]) ? NULL : $pm[$rs->payment_code]; ?>
						<?php $cod_txt = empty($payment) ? "" : (($payment->role == 4 && $rs->state != 9) ? ($rs->is_paid == 1 ? '' : '<span class="badge badge-danger font-size-10">รอเงินเข้า</span>') : ''); ?>
						<?php $c_ref = empty($rs->customer_ref) ? '' : ' ['.$rs->customer_ref.']'; ?>
						<?php $channels_name = empty($ch[$rs->channels_code]) ? NULL : $ch[$rs->channels_code]; ?>
						<?php $payment_name = empty($payment) ? NULL : $payment->name; ?>
						<?php $csr = empty($sa[$rs->sale_code]) ? NULL : $sa[$rs->sale_code]; ?>
						<?php $dname = empty($user[$rs->user]) ? NULL : $user[$rs->user]; ?>
						<?php $type_name = empty($type[$rs->type_code]) ? NULL : $type[$rs->type_code]; ?>
						<?php $first_state_date = $this->order_state_model->get_first_state_timestamp($rs->code); ?>

            <tr class="font-size-11" id="row-<?php echo $rs->code; ?>" style="<?php echo state_color($rs->state, $rs->status, $rs->is_expired); ?>">
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $no; ?></td>
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
							<td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($first_state_date, TRUE); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->code . $cod_txt; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->customer_name . $c_ref; ?></td>
							<td class="middle pointer text-right" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo number($rs->total_amount, 2); ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $channels_name; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $payment_name; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo get_state_name($rs->state); ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->reference; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $dname; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $csr; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $type_name; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->reference2; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->shipping_code; ?></td>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php
if($can_upload == 1) :
	 $this->load->view('orders/import_order');
endif;
?>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	$('#user').select2();

	function getTemplate(){
	  window.location.href = BASE_URL + 'orders/orders/get_template_file';
	}
</script>

<?php $this->load->view('include/footer'); ?>
