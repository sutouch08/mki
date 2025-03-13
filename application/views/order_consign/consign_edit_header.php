<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled />
    </div>
		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm edit text-center" id="customer-code" value="<?php echo $order->customer_code; ?>" disabled />
		</div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="form-control input-sm edit" id="customer-name" name="customer" value="<?php echo $order->customer_name; ?>" disabled />
    </div>
		<div class="col-lg-4 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
	    <label>โซน[ฝากขาย]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $order->zone_name; ?>" required disabled/>
	  </div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ตัดรอบออเดอร์</label>
	    <select class="width-100 e edit" id="order-round" disabled>
	      <option value="">กรุณาเลือก</option>
	      <?php echo select_order_round($order->order_round); ?>
	    </select>
	  </div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>รอบจัดส่ง</label>
	    <select class="width-100 e edit" id="shipping-round" disabled>
	      <option value="">กรุณาเลือก</option>
	      <?php echo select_shipping_round($order->shipping_round); ?>
	    </select>
	  </div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
	    <label>วันที่จัดส่ง</label>
	    <input type="text" class="width-100 e text-center edit" id="ship-date" value="<?php echo empty($order->shipping_date) ? NULL : thai_date($order->shipping_date); ?>" readonly disabled/>
	  </div>

	  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5 hide">
	    <label>GP[%]</label>
			<input type="text" class="form-control input-sm text-center edit" name="gp" id="gp" value="<?php echo $order->gp; ?>" disabled />
	  </div>

		<?php if($order->state < 4 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<div class="col-lg-7 col-md-11 col-sm-10-harf col-xs-9 padding-5">
				<label>หมายเหตุ</label>
				<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
			</div>
			<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
				<label class="display-block not-show">แก้ไข</label>
				<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
				<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateOrder()"><i class="fa fa-save"></i> บันทึก</i></button>
			</div>
		<?php else : ?>
			<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 padding-5">
				<label>หมายเหตุ</label>
				<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
			</div>
		<?php endif; ?>


    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
		<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $order->zone_code; ?>" />
</div>
<hr class="margin-top-15"/>
