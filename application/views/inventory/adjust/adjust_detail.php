<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h3 class="title" >
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($this->pm->can_approve && $doc->status == 0) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="getApprove()"><i class="fa fa-check"></i> อนุมัติ</button>
			<?php endif; ?>
			<?php if($doc->status != 2 && $this->pm->can_delete) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="goCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
			<?php endif; ?>
		</p>
	</div>
</div>
<hr class="padding-5"/>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>อ้างถึง</label>
		<input type="text" class="form-control input-sm edit" id="reference" value="<?php echo $doc->reference; ?>" disabled />
	</div>
	<div class="col-lg-7-harf col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
	</div>

	<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15"/>
<?php if($doc->status == 2) : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <span style="margin-right:30px;"><i class="fa fa-check green"></i> = ปรับยอดแล้ว</span>
      <span><i class="fa fa-times red"></i> = ยังไม่ปรับยอด</span>
    </p>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="min-width:860px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-200">สินค้า</th>
          <th class="fix-width-200 text-center">โซน</th>
          <th class="fix-width-80 text-center">เพิ่ม</th>
          <th class="fix-width-80 text-center">ลด</th>
          <th class="fix-width-60 text-center">สถานะ</th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_name; ?>
        </td>
        <td class="middle text-center">
          <?php echo $rs->zone_name; ?>
        </td>
        <td class="middle text-center" id="qty-up-<?php echo $rs->id; ?>">
          <?php echo $rs->qty > 0 ? $rs->qty : 0 ; ?>
        </td>
        <td class="middle text-center" id="qty-down-<?php echo $rs->id; ?>">
          <?php echo $rs->qty < 0 ? ($rs->qty * -1) : 0 ; ?>
        </td>
        <td class="middle text-center">
          <?php echo is_active($rs->valid); ?>
        </td>
      </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php endif; ?>
      </tbody>
    </table>
  </div>

	<div class="col-lg-6 col-md-5 col-sm-6 col-xs-12 padding-5">
		<?php if($doc->is_approve == 1) : ?>
			<p class="green">อนุมัติโดย : <?php echo $doc->approver; ?> @ <?php echo thai_date($doc->approve_date, TRUE); ?></p>
		<?php endif; ?>
		<?php if($doc->status == 2) : ?>
			<p class="red">ยกเลิกโดย : <?php echo $doc->cancel_user; ?> @ <?php echo thai_date($doc->cancel_date, TRUE); ?></p>
			<p class="red">เหตุผล : <?php echo $doc->cancel_reason; ?> </p>
		<?php endif; ?>
	</div>
</div>

<?php $this->load->view('cancel_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
