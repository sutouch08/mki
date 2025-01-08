<?php $this->load->view('include/header'); ?>
<?php $use_vat = getConfig('USE_VAT'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-8 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-4 padding-5">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5">
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="code">เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" name="code" disabled />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="doc_date">วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="doc_date" id="doc_date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="customer_code">รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" name="customer_code" id="customer_code" value="" />
	</div>
	<?php if($use_vat) : ?>
	<div class="col-sm-6 col-xs-6 padding-5">
		<label for="customer_name">ลูกค้า</label>
		<input type="text" class="form-control input-sm" name="customer_name" id="customer_name" value="" disabled />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="vat_type" class="not-show">ภาษี</label>
		<select class="form-control input-sm edit" id="vat_type" name="vat_type">
			<option value="I">ราขายรวม VAT</option>
			<option value="E">ราคาขายไม่รวม VAT</option>
		</select>
	</div>
	<?php else : ?>
		<div class="col-sm-6 col-xs-6 padding-5">
			<label for="customer_name">ลูกค้า</label>
			<input type="text" class="form-control input-sm" name="customer_name" id="customer_name" value="" disabled />
		</div>
	<?php endif; ?>
	<div class="col-sm-10 col-10-harf col-xs-12 padding-5">
		<label for="remark">หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
	</div>

	<input type="hidden" name="customerCode" id="customerCode" value="" />
</div>
<hr class="padding-5 margin-top-15" />






<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
