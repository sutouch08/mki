<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs">
		<h3 class="title">
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-xs-12 visible-xs">
		<h3 class="title-xs">
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <button type="button" class="btn btn-sm btn-info" onclick="printReceipt()"><i class="fa fa-print"></i> พิมพ์ใบเสร็จ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="editForm" method="post" action="<?php echo $this->home; ?>/update">
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>ชำระโดย</label>
		<select class="form-control input-sm edit" name="pay_type" id="pay_type" required disabled>
			<option value="">โปรดเลือก</option>
			<?php echo select_payment_type($doc->pay_type); ?>
		</select>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-6-harf col-xs-6 padding-5">
		<label>ลูกค้า[ในระบบ]</label>
		<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
	</div>

	<div class="col-lg-4-harf col-md-4-harf col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
	</div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="repay_code" id="repay_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
</form>

<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>

<?php $this->load->view('account/order_repay/order_repay_control'); ?>


<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay_control.js"></script>

<?php $this->load->view('include/footer'); ?>
