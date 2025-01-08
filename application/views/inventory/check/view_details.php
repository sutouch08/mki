<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
		<button type="button" class="btn btn-white btn-inverse top-btn" onclick="viewLogs(<?php echo $doc->id; ?>)"><i class="fa fa-history"></i> &nbsp; ประวัติ</button>
	<?php if($doc->status == 'O') : ?>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="goChecking(<?php echo $doc->id; ?>)">ตรวจนับสินค้า</button>
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goEdit(<?php echo $doc->id; ?>)">แก้ไขรายการ</button>
	<?php endif; ?>
	<?php if(($doc->status == 'C' || $doc->status == 'D') && ($this->pm->can_delete)) : ?>
		<button type="button" class="btn btn-white btn-purple top-btn" onclick="reOpenCheck()">ย้อนสถานะ</button>
	<?php endif; ?>
	<?php if($doc->status == 'C' && ($this->pm->can_edit OR $this->pm->can_add)) : ?>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="getStockZone()">ดึงยอดตั้งต้น</button>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="updateCost()">อัพเดตราคา</button>
		<button type="button" class="btn btn-white btn-success top-btn" onclick="exportResult()"><i class="fa fa-file-excel-o"></i> Export</button>
	<?php endif; ?>
	<?php if($doc->status == 'O' && ($this->pm->can_edit OR $this->pm->can_add)) : ?>
		<button type="button" class="btn btn-white btn-purple top-btn" onclick="closeCheck()">ปิดการตรวจนับ</button>
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
    <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->date_add); ?>" disabled/>
  </div>

  <div class="col-lg-3 col-md-3 col-sm-8 col-xs-6 padding-5">
    <label>หัวข้อ</label>
    <input type="text" class="form-control input-sm" name="subject" id="subject" value="<?php echo $doc->subject; ?>" disabled/>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled/>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-9 col-xs-12 padding-5">
    <label class="not-show">โซน</label>
    <input type="text" class="form-control input-sm" name="zone_name" id="zone_name" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>คีย์จำนวน</label>
    <select class="form-control input-sm" name="allow_input_qty" id="allow_input_qty" disabled>
			<option value="0" <?php echo is_selected('0', $doc->allow_input_qty); ?>>ไม่ได้</option>
			<option value="1" <?php echo is_selected('1', $doc->allow_input_qty); ?>>ได้</option>
		</select>
  </div>

  <div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>
	<input type="hidden" id="check_id" value="<?php echo $doc->id; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15">
<?php if($doc->status == 'D') : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 divFixHead" style="height:450px;">
		<table class="table table-striped border-1 tableFixHead" style="min-width:1000px;">
			<thead>
				<tr class="freez">
					<th class="fix-width-80 text-center">#</th>
					<th class="fix-width-120">บาร์โค้ด</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-300">ชื่อสินค้า</th>
					<th class="fix-width-100 text-right">ตั้งต้น (1)</th>
					<th class="fix-width-100 text-right">ตรวจนับ (2)</th>
					<th class="fix-width-100 text-right">ยอดต่าง (2-1)</th>
				</tr>
			</thead>
			<tbody>
				<?php $total_stock = 0; ?>
				<?php $total_check = 0; ?>
				<?php $total_diff = 0; ?>
				<?php $no = 1; ?>

				<?php if( ! empty($details)) : ?>
					<?php foreach($details as $rs) : ?>
						<?php $stock_qty = $doc->status == 'C' ? $rs->stock_qty : 0; ?>
						<?php $check_qty = $doc->status == 'C' ? $rs->check_qty : $rs->qty; ?>
						<?php $diff_qty = $doc->status == 'C' ? $rs->diff_qty : $rs->qty; ?>
						<?php $code = $doc->status == 'C' ? $rs->product_code : $rs->code; ?>
						<?php $name = $doc->status == 'C' ? $rs->product_name : $rs->name; ?>
					<tr>
						<td class="text-center"><?php echo number($no); ?></td>
						<td><?php echo $rs->barcode; ?></td>
						<td><?php echo $code; ?></td>
						<td><?php echo $name; ?></td>
						<td class="text-right"><?php echo number($stock_qty); ?></td>
						<td class="text-right"><?php echo number($check_qty); ?></td>
						<td class="text-right"><?php echo number($diff_qty); ?></td>
					</tr>
					<?php $total_stock += $stock_qty; ?>
					<?php $total_check += $check_qty; ?>
					<?php $total_diff += $diff_qty; ?>
					<?php $no++; ?>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4" class="text-right"><span style="margin-right:10px;">รวม</span>  <?php echo ($no - 1); ?> <span style="margin-left:10px;">รายการ</span></td>
					<td class="text-right"><?php echo number($total_stock); ?></td>
					<td class="text-right"><?php echo number($total_check); ?></td>
					<td class="text-right"><?php echo number($total_diff); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<form id="exportForm" method="post" action="<?php echo $this->home; ?>/export_result">
	<input type="hidden" name="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" name="token" id="token" />
</form>

<?php $this->load->view('inventory/check/logs_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/check/check.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
