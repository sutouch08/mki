
<div class="tab-pane fade" id="pos">
<form id="posForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">

		<div class="col-sm-3"><span class="form-control left-label">POS Channels</span></div>
		<div class="col-sm-9">
			<select class="form-control input-medium" name="POS_CHANNELS" >
				<option value="">ไม่ระบุ</option>
				<?php echo select_channels($POS_CHANNELS); ?>
			</select>
			<span class="help-block">กำหนดช่องทางขายที่ใช้สำหรับการขาย POS (จำเป็นต้องระบุหากมีการขายแบบ POS มีผลต่อเงื่อนไขส่วนลด)</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">Default Payment Method</span></div>
		<div class="col-sm-9">
			<select class="form-control input-medium" name="POS_DEFAULT_PAYMENT" >
				<?php echo select_payment_method($POS_DEFAULT_PAYMENT); ?>
			</select>
			<span class="help-block">กำหนดช่องทางการชำระเงินเริ่มต้นที่ใช้สำหรับการขาย POS (จำเป็นต้องระบุหากมีการขายแบบ POS มีผลต่อเงื่อนไขส่วนลด)</span>
		</div>
		<div class="divider-hidden"></div>



    <div class="col-sm-9 col-sm-offset-3">
			<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('posForm')"><i class="fa fa-save"></i> บันทึก</button>
		</div>
		<div class="divider-hidden"></div>

  </div>
</form>
</div><!--- Tab-pane --->
