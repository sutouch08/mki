<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_payment_method($payment); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>รูปแบบ</label>
		<select class="form-control input-sm" name="role" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <?php echo select_order_role($role); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1230px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">ลำดับ</th>
					<th class="fix-width-100 text-center">วันที่จัดส่ง</th>
					<th class="fix-width-100 text-center">ตัดรอบออเดอร์</th>
					<th class="fix-width-100 text-center">รอบจัดส่ง</th>
          <th class="fix-width-100 text-center">วันที่</th>
          <th class="fix-width-120">เลขที่เอกสาร</th>
          <th class="min-width-200">ลูกค้า/ผู้รับ/ผู้เบิก</th>
          <th class="fix-width-100 text-center">ยอดเงิน</th>
          <th class="fix-width-100 text-center">การชำระเงิน</th>
          <th class="fix-width-120 text-center">ผู้้ดำเนินการ</th>
					<th class="fix-width-150">CSR</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php $sa = saleman_array(); //-- saleman_helper ?>
<?php $user = user_array(); //-- user_helper ?>
<?php   foreach($orders as $rs)  : ?>
<?php $csr = empty($sa[$rs->sale_code]) ? NULL : $sa[$rs->sale_code]; ?>
<?php $dname = empty($user[$rs->user]) ? NULL : $user[$rs->user]; ?>
        <tr class="font-size-12">

          <td class="text-center pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $no; ?>
          </td>

					<td class="text-center" >
						<?php echo empty($rs->shipping_date) ? "" : thai_date($rs->shipping_date, FALSE); ?>
					</td>

					<td class="text-center" >
						<?php echo $rs->order_round; ?>
					</td>

					<td class="text-center" >
						<?php echo $rs->shipping_round; ?>
					</td>

          <td class="pointer text-center" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo thai_date($rs->date_add); ?>
          </td>

          <td class="pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->code; ?>
            <?php echo ($rs->reference != '' ? ' ['.$rs->reference.']' : ''); ?>
          </td>

          <td class="pointer hide-text" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->customer_name; ?>
          </td>

          <td class="pointer text-center" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo number($rs->total_amount,2); ?>
          </td>

          <td class="pointer text-center" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->payment_name ?>
          </td>

          <td class="pointer text-center hide-text" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $dname; ?>
          </td>

					<td class="hide-text" >
            <?php echo $csr; ?>
          </td>

        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
