<?php $this->load->view('include/header'); ?>
<?php $pm = get_permission('SOODIV', $this->_user->uid, get_cookie('id_profile')); ?>
<?php $use_vat = getConfig('USE_VAT'); ?>
<script>
	var USE_VAT = <?php echo $use_vat; ?>
</script>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h4 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 text-center visible-xs" style="background-color:#eee;">
		<h4 class="titel-xs"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
			<p class="pull-right top-p">
				<?php if($pm->can_add OR $pm->can_edit) : ?>
					<?php $inv_option = $use_vat ? 'tax_invoice' : 'do_invoice'; ?>
					<button type="button" class="btn btn-xs btn-primary top-btn" onclick="create_each_invoice('<?php echo $inv_option; ?>')">เปิดใบกำกับแยกออเดอร์</button>
					<button type="button" class="btn btn-xs btn-success top-btn" onclick="create_one_invoice('<?php echo $inv_option; ?>')">เปิดใบกำกับรวมออเดอร์</button>
				<?php endif; ?>
			</p>
		</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code" id="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่ใบกำกับ</label>
    <input type="text" class="form-control input-sm search" name="invoice_code" id="invoice-code"  value="<?php echo $invoice_code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" id="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ใบกำกับ</label>
		<select class="form-control input-sm" name="is_inv" id="is-inv" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected($is_inv, '1'); ?>>เปิดแล้ว</option>
			<option value="0" <?php echo is_selected($is_inv, '0'); ?>>ยังไม่เปิด</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" id="payment" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_payment_method($payment); ?>
		</select>
  </div>


	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>รูปแบบ</label>
		<select class="form-control input-sm" name="role" id="role" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
			<?php echo select_order_role($role); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" id="channels" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>

	<div class="col-lg-2-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>พนักงาน</label>
		<select class="width-100" name="user" id="user" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <?php echo select_user($user); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
	<div class="col-lg-5-harf col-md-2 col-sm-6 col-xs-12 padding-5 text-right">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-success btn-100" onclick="exportFilter()"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp; Export Filter</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1700px;">
      <thead>
        <tr class="font-size-11">
					<th class="fix-width-40 text-center"></th>
					<th class="fix-width-60"></th>
          <th class="fix-width-40 text-center">ลำดับ</th>
					<th class="fix-width-100 text-center">วันที่จัดส่ง</th>
					<th class="fix-width-100 text-center">ตัดรอบออเดอร์</th>
					<th class="fix-width-100 text-center">รอบจัดส่ง</th>
          <th class="fix-width-100 text-center">วันที่</th>
          <th class="fix-width-120">เลขที่เอกสาร</th>
					<th class="fix-width-120">ใบกำกับ</th>
          <th class="fix-width-200">ลูกค้า/ผู้รับ/ผู้เบิก</th>
          <th class="fix-width-100 text-right">ยอดเงิน</th>
					<th class="fix-width-150 text-center">Tracking No</th>
          <th class="fix-width-150 text-center">การชำระเงิน</th>
          <th class="fix-width-150">ผู้ดำเนินการ</th>
					<th class="fix-width-150">CSR</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php $sa = saleman_array(); //-- saleman_helper ?>
<?php $user = user_array(); //-- user_helper ?>
<?php $payments = payment_method_array(); ?>
<?php   foreach($orders as $rs)  : ?>
	<?php $payment = empty($payments[$rs->payment_code]) ? NULL : $payments[$rs->payment_code]; ?>
	<?php $csr = empty($sa[$rs->sale_code]) ? NULL : $sa[$rs->sale_code]; ?>
	<?php $dname = empty($user[$rs->user]) ? NULL : $user[$rs->user]; ?>
        <tr class="font-size-11" <?php echo (empty($rs->invoice_code) ? "" : 'style="background-color:#e8f3f1"'); ?>>
					<td class="middle text-center">
						<?php if($rs->role === 'S' && empty($rs->invoice_code)) : ?>
							<label>
								<input type="checkbox" class="ace chk" value="<?php echo $rs->code; ?>" data-no="<?php echo $no; ?>" />
								<span class="lbl"></span>
							</label>
						<?php endif; ?>
					</td>
					<td class="middle text-center">
						<button type="button" class="btn btn-mini btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
					</td>

					<td class="text-center middle" >
            <?php echo $no; ?>
          </td>

					<td class="text-center middle" >
						<?php echo empty($rs->shipping_date) ? "" : thai_date($rs->shipping_date, FALSE); ?>
					</td>

					<td class="text-center middle" >
						<?php echo $rs->order_round; ?>
					</td>

					<td class="text-center middle" >
						<?php echo $rs->shipping_round; ?>
					</td>

          <td class="middle text-center" >
            <?php echo thai_date($rs->date_add); ?>
          </td>

          <td class="middle" >
            <?php echo $rs->code; ?>
            <?php echo ($rs->reference != '' ? ' ['.$rs->reference.']' : ''); ?>
						<?php if( ! empty($payment) && $payment->role == 4 && $rs->is_paid == 0) : ?>
							<span class="label label-danger">รอเงินเข้า</span>
						<?php endif; ?>
						<input type="hidden" id="orderCode-<?php echo $no; ?>" value="<?php echo $rs->code; ?>" />
          </td>

					<td class="middle" >
            <?php echo $rs->invoice_code; ?>
          </td>

          <td class="middle hide-text" >
            <?php echo $rs->customer_name; ?>
						<?php if(!empty($rs->customer_ref)) : ?>
							[<?php echo $rs->customer_ref; ?>]
						<?php endif; ?>
						<input type="hidden" id="customerCode-<?php echo $no; ?>" value="<?php echo $rs->customer_code; ?>" />
          </td>

          <td class="middle text-right" >
            <?php echo number($rs->total_amount,2); ?>
          </td>

					<td class="middle text-center"><?php echo $rs->shipping_code; ?></td>
          <td class="middle text-center" >
            <?php echo ( ! empty($payment) ? $payment->name : ''); ?>
          </td>

          <td class="middle hide-text" >
            <?php echo $dname; ?>
          </td>
					<td class="middle hide-text" >
            <?php echo $csr; ?>
          </td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="9" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<form class="hide" id="exportForm" method="post" action="<?php echo current_url(); ?>/export_filter">
	<input type="hidden" name="data" id="data" />
	<input type="hidden" name="token" id="token" />
</form>

<script>
	function exportFilter() {
		let h = {
			'code' : $('#code').val().trim(),
			'invoice_code' : $('#invoice-code').val().trim(),
			'customer' : $('#customer').val().trim(),
			'is_inv' : $('#is-inv').val(),
			'payment' : $('#payment').val(),
			'role' : $('#role').val(),
			'channels' : $('#channels').val(),
			'user' : $('#user').val(),
			'from_date' : $('#fromDate').val(),
			'to_date' : $('#toDate').val()
		};

		let token = generateUID();

		$('#data').val(JSON.stringify(h));

		$('#token').val(token);

		$('#exportForm').submit();
		
		get_download(token);
	}
</script>

<script>
	$('#user').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
