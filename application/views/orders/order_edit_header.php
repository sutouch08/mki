<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="width-100 text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    	<label>วันที่</label>
			<input type="text" class="width-100 text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled readonly />
    </div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    	<label>รหัสลูกค้า</label>
			<input type="text" class="width-100 text-center edit" id="customer" name="customer" value="<?php echo $order->customer_code; ?>" required disabled />
    </div>

    <div class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="width-100 edit" id="customerName" name="customerName" value="<?php echo $order->customer_name; ?>" disabled />
    </div>

		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
	    <label>CSR</label>
	    <input type="text" class="width-100 text-center e" id="sale-code" value="<?php echo $order->sale_name; ?>" disabled />
	  </div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
	    <label>Sale</label>
	    <input type="text" class="width-100 text-center e" id="cus-type" value="<?php echo $order->type_name; ?>" disabled />
	  </div>

    <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
    	<label>ลูกค้า[ออนไลน์]</label>
      <input type="text" class="width-100 text-center edit" id="customer_ref" name="customer_ref" value="<?php echo str_replace('"', '&quot;',$order->customer_ref); ?>" disabled />
    </div>

		<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
	    <label>ผู้ช่วยขาย</label>
			<select class="width-100 edit" name="tags" id="tags" disabled>
				<option value="">เลือก</option>
				<?php echo select_order_tags($order->tags); ?>
			</select>
	  </div>

		<!-- <div class="col-lg-2 col-md-2 col-sm-2 col-xs-5 padding-5">
    	<label>ใบเสนอราคา</label>
      <input type="text" class="width-100 text-center edit" id="qt_no" name="qt_no" value="<?php echo $order->qt_no; ?>" disabled />
    </div>
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    	<label class="display-block not-show">ดึง</label>
      <button type="button" class="btn btn-xs btn-primary btn-block edit" id="btn_qt" onclick="checkQuotation()" disabled>เพิ่ม</button>
    </div> -->

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>อ้างอิง[MKP]</label>
		  <input type="text" class="width-100 text-center edit" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>อ้างอิง[CRM]</label>
		  <input type="text" class="width-100 text-center edit" name="reference2" id="reference2" value="<?php echo $order->reference2; ?>" disabled />
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    	<label>ช่องทางขาย</label>
			<select class="width-100 edit" name="channels" id="channels" required disabled>
				<option value="">เลือกรายการ</option>
				<?php echo select_channels($order->channels_code); ?>
			</select>
    </div>
    <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    	<label>การชำระเงิน</label>
			<select class="width-100 edit" name="payment" id="payment" required disabled>
				<option value="">เลือกรายการ</option>
				<?php echo select_payment_method($order->payment_code); ?>
			</select>
    </div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>การจัดส่ง</label>
	    <select class="width-100 edit" name="sender_id" id="sender_id" disabled>
	      <option value="">กรุณาเลือก</option>
	      <?php echo select_sender_list($order->sender_id); ?>
	    </select>
	  </div>

		<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ตัดรอบออเดอร์</label>
	    <select class="width-100 edit" id="order-round" disabled>
	      <option value="">กรุณาเลือก</option>
	      <?php echo select_order_round($order->order_round); ?>
	    </select>
	  </div>

		<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>รอบจัดส่ง</label>
	    <select class="width-100 edit" id="shipping-round" disabled>
	      <option value="">กรุณาเลือก</option>
	      <?php echo select_shipping_round($order->shipping_round); ?>
	    </select>
	  </div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
	    <label>วันที่จัดส่ง</label>
	    <input type="text" class="width-100 e edit text-center" id="ship-date" value="<?php echo thai_date($order->shipping_date); ?>" readonly disabled />
	  </div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>เลขที่จัดส่ง</label>
		  <input type="text" class="width-100 text-center edit" name="shipping_code" id="shipping_code" value="<?php echo $order->shipping_code; ?>" disabled />
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    	<label>ผู้ดำเนินการ</label>
			<input type="text" class="width-100" value="<?php echo get_display_name($order->user); ?>" disabled/>
    </div>

		<div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
		 	<label>หมายเหตุ</label>
		  <input type="text" class="width-100 edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
		</div>

		<?php if($this->pm->can_add OR $this->pm->can_edit): ?>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
			<label class="display-block not-show">แก้ไข</label>
			<?php if($order->state < 4) : ?>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="validUpdate()"><i class="fa fa-save"></i> บันทึก</i></button>
			<?php else : ?>
				<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editRemark()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
				<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateRemark()"><i class="fa fa-save"></i> บันทึก</i></button>
			<?php endif; ?>
		</div>
		<?php endif; ?>
    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
		<input type="hidden" name="deposit_amount" id="deposit-amount" value="<?php echo $order->deposit; ?>" />
</div>
