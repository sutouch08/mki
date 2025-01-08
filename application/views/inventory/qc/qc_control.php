<!--  Control -->
<?php $allow_input_qty = getConfig('QC_ALLOW_INPUT_QTY') == 1 ? TRUE : FALSE; ?>

<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-8 padding-5">
    <label>บาร์โค้ดกล่อง</label>
    <input type="text" class="form-control input-sm text-center zone" id="barcode-box" autofocus />
  </div>
  <div class="col-sm-1 col-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">change box</label>
    <button
      type="button"
      class="btn btn-xs btn-info btn-block hide"
      id="btn-change-box"
      onclick="confirmSaveBeforeChangeBox()"
      >
      <i class="fa fa-refresh"></i> เปลี่ยนกล่อง
    </button>
		<button
			type="button"
			class="btn btn-xs btn-info btn-block"
			id="btn-chose-box"
			onclick="getBox()"
			>
			<i class="fa fa-check"></i> ตกลง
		</button>
  </div>


		<div class="col-sm-1 col-xs-3 padding-5">
			<label>จำนวน</label>
			<?php if($allow_input_qty) : ?>
			<input type="number" class="form-control input-sm text-center item" id="qty" value="1" disabled />
			<?php else : ?>
			<input type="number" class="form-control input-sm text-center" value="1" disabled />
			<input type="hidden" id="qty" value="1" />
			<?php endif; ?>
		</div>


  <div class="col-sm-2 col-xs-5 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm text-center item" id="barcode-item" disabled />
  </div>
  <div class="col-sm-1 col-xs-4 padding-5">
    <label class="display-block not-show">submit</label>
    <button type="button" class="btn btn-xs btn-default btn-block item" id="btn-submit" onclick="qcProduct()" disabled>ตกลง</button>
  </div>


  <div class="col-sm-2 col-xs-12 padding-5">
    <label class="display-block not-show">submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block item" onclick="saveQc(0)">
      <i class="fa fa-save"></i> บันทึก
    </button>
  </div>
  <div class="col-sm-1 col-xs-12 padding-5">
    <label class="display-block not-show">print</label>
    <button type="button" class="btn btn-xs btn-primary hide" id="btn-print-address" onclick="printAddress()">พิมพ์ใบปะหน้า</button>
  </div>
  <div class="col-sm-2 col-xs-12 padding-5">
    <div class="title middle text-center" style="height:55px; background-color:black; color:white; padding-top:20px; margin-top:0px;">
      <h4 id="all_qty" style="display:inline;">
        <?php echo number($qc_qty); ?>
      </h4>
      <h4 style="display:inline;"> / <?php echo number($all_qty); ?></h4>
    </div>
  </div>
</div>

<input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />

<hr class="padding-5"/>
<!--  Control -->
