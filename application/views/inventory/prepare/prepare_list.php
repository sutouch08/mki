<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-purple btn-100 top-btn" onclick="getPickList()">Download Picklist</button>
			<button type="button" class="btn btn-sm btn-success btn-100 top-btn" onclick="soldOrder()">เปิดบิล</button>
			<button type="button" class="btn btn-sm btn-primary btn-100 top-btn" onclick="goProcess()">กำลังจัด</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search-box" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search-box" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>พนักงาน</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="width-100 filter" name="channels" id="channels">
      <option value="all">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ตัดรอบออเดอร์</label>
		<select class="width-100 filter" name="order_round">
      <option value="all">ทั้งหมด</option>
      <?php echo select_order_round($order_round); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รอบจัดส่ง</label>
		<select class="width-100 filter" name="shipping_round">
      <option value="all">ทั้งหมด</option>
      <?php echo select_shipping_round($shipping_round); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>วันที่ออเดอร์</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>วันที่จัดส่ง</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="ship_from_date" id="shipFromDate" value="<?php echo $ship_from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="ship_to_date" id="shipToDate" value="<?php echo $ship_to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>
	<div class="col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
</form>
<hr class="margin-top-15 padding-5">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1200px;">
			<thead>
				<tr>
					<th class="fix-width-60 middle text-center">
						<label>
							<input type="checkbox" class="ace" onchange="checkAll($(this))" />
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-120 middle">เลขที่</th>
					<th class="min-width-200 middle">ลูกค้า</th>
          <th class="fix-width-150 middle">ช่องทางขาย</th>
					<th class="fix-width-120 middle">ตัดรอบออเดอร์</th>
					<th class="fix-width-120 middle">รอบจัดส่ง</th>
					<th class="fix-width-100 middle text-center">วันที่จัดส่ง</th>
					<th class="fix-width-150 middle">พนักงาน</th>
					<th class="fix-width-100 middle"></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
            <tr class="font-size-11" id="row-<?php echo $rs->code; ?>">
							<td class="middle text-center">
								<?php if($rs->picked == 0) : ?>
									<label>
										<input type="checkbox" class="ace chk" value="<?php echo $rs->code; ?>" />
										<span class="lbl"></span>
									</label>
								<?php endif; ?>
							</td>
              <td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE,'/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $customer_name; ?></td>
              <td class="middle"><?php echo $rs->channels_name; ?></td>
							<td class="middle"><?php echo $rs->order_round; ?></td>
							<td class="middle"><?php echo $rs->shipping_round; ?></td>
              <td class="middle text-center"><?php echo empty($rs->shipping_date) ? NULL : thai_date($rs->shipping_date, FALSE,'/'); ?></td>
							<td class="middle"><?php echo $rs->user; ?></td>
              <td class="middle text-right">
          <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
                <button type="button" class="btn btn-xs btn-info" onClick="goPrepare('<?php echo $rs->code; ?>')">จัดสินค้า</button>
          <?php endif; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="11" class="text-center">--- No content ---</td>
          </tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	$('#user').select2();
</script>

<?php $this->load->view('include/footer'); ?>
