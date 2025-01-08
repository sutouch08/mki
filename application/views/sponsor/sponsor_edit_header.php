<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $order->code; ?>" disabled />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center e" id="date-add" value="<?php echo thai_date($order->date_add); ?>" disabled/>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm e" id="customer-code" value="<?php echo $order->customer_code; ?>" disabled/>
	</div>

	<div class="col-lg-6 col-md-4-harf col-sm-6 col-xs-6 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm" id="customer-name" value="<?php echo $order->customer_name; ?>" disabled/>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ผู้เบิก/คนสั่ง</label>
		<input type="text" class="form-control input-sm e" id="emp-name" value="<?php echo $order->user_ref; ?>" disabled/>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ผูทำรายการ</label>
		<input type="text" class="form-control input-sm" id="user" value="<?php echo $order->user; ?>" disabled/>
	</div>

	<div class="col-lg-9 col-md-8-harf col-sm-6-harf col-xs-9">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm e" id="remark" value="<?php echo $order->remark; ?>" disabled>
	</div>

<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">แก้ไข</label>
		<?php if($order->state < 3) : ?>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</i></button>
		<?php endif; ?>
	</div>
<?php endif; ?>
	<input type="hidden" id="budget-id" value="<?php echo $order->budget_id; ?>" />
	<input type="hidden" id="budget-amount" value="" data-amount=""/>
	<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
</div>

<hr class="margin-bottom-15"/>
