<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title">
			<?php echo $this->title; ?>
		</h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->status != 2 && $this->pm->can_delete) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="goCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
			<?php endif; ?>
			<?php if($doc->status == 1 && $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="rollback('<?php echo $doc->code; ?>')">ย้อนสถานะมาแก้ไข</button>
			<?php endif; ?>
			<?php if($doc->status == 1) : ?>
			<button type="button" class="btn btn-sm btn-info" onclick="printConsignOrder()"><i class="fa fa-print"></i> พิมพ์</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center e" name="date_add" id="date" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled />
	</div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm e" name="customerCode" id="customerCode" value="<?php echo $doc->customer_code; ?>" disabled/>
	</div>
	<div class="col-lg-3-harf col-md-6 col-sm-6 col-xs-6 padding-5">
		<label>ลูกค้า[ในระบบ]</label>
		<input type="text" class="form-control input-sm e" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>โซน</label>
		<input type="text" class="form-control input-sm e" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled/>
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled/>
	</div>

	<div class="col-lg-10 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm e" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
	</div>

	<?php $statusLabel = $doc->status == 1 ? 'บันทึกแล้ว' : ($doc->status == 2 ? 'ยกเลิก' : 'ยังไม่บันทึก'); ?>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $statusLabel; ?>" disabled />
	</div>

	<input type="hidden" name="consign_code" id="consign_code" value="<?php echo $doc->code; ?>">
	<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
	<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" >
</div>

<hr class="margin-top-15"/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
    <table class="table table-striped border-1" style="min-width:930px;">
      <thead>
        <tr class="font-size-12">
          <th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-150">รหัส</th>
          <th class="min-width-200">สินค้า</th>
          <th class="fix-width-100 text-right">ราคา</th>
          <th class="fix-width-120 text-right">ส่วนลด</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-120 text-right">มูลค่า</th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  $totalQty = 0; ?>
<?php  $totalAmount = 0; ?>
<?php  foreach($details as $rs) : ?>
        <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle"><?php echo $rs->product_name; ?></td>
          <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
          <td class="middle text-right"><?php echo $rs->discount; ?></td>
          <td class="middle text-right"><?php echo number($rs->qty); ?></td>
          <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
          </td>
        </tr>

<?php  $no++; ?>
<?php  $totalQty += $rs->qty; ?>
<?php  $totalAmount += $rs->amount; ?>
<?php endforeach; ?>

      <tr id="total-row">
        <td colspan="5" class="middle text-right"><strong>รวม</strong></td>
        <td id="total-qty" class="middle text-right"><?php echo number($totalQty); ?></td>
        <td id="total-amount" class="middle text-right"><?php echo number($totalAmount,2); ?></td>
      </tr>
<?php else : ?>
  <tr id="total-row">
    <td colspan="5" class="middle text-right"><strong>รวม</strong></td>
    <td id="total-qty" class="middle text-right">0</td>
    <td id="total-amount" class="middle text-right">0</td>
  </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancel_modal'); ?>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
