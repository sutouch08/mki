<?php
//---- อนุญาติให้แก้ไขราคาในออเดอร์หรือไม่
$btn_price_yes = $ALLOW_EDIT_PRICE == 1 ? 'btn-success' : '';
$btn_price_no = $ALLOW_EDIT_PRICE == 0 ? 'btn-danger' : '';

//--- อนุญาติให้แก้ไขส่วนลดในออเดอร์หรือไม่
$btn_disc_yes = $ALLOW_EDIT_DISCOUNT == 1 ? 'btn-success' : '';
$btn_disc_no  = $ALLOW_EDIT_DISCOUNT == 0 ? 'btn-danger' : '';

//--- ไม่อนุญาติให้ขายสินค้ากับลูกค้าที่มียอดค้างชำระเกินกำหนด
$btn_strict_yes = $STRICT_OVER_DUE == 1 ? 'btn-success' : '';
$btn_strict_no  = $STRICT_OVER_DUE == 0 ? 'btn-danger' : '';

$btn_credit_yes = $CONTROL_CREDIT == 1 ? 'btn-success' : '';
$btn_credit_no  = $CONTROL_CREDIT == 0 ? 'btn-danger' : '';

$btn_doc_date = $ORDER_SOLD_DATE == 'D' ? 'btn-success' : '';
$btn_bill_date = $ORDER_SOLD_DATE == 'B' ? 'btn-success' : '';

$btn_grid = $USE_ORDER_GRID == 1 ? 'btn-success' : '';
$btn_table = $USE_ORDER_GRID == 0 ? 'btn-success' : '';

$btn_tab_yes = $USE_PRODUCT_TAB == 1 ? 'btn-success' : '';
$btn_tab_no = $USE_PRODUCT_TAB == 0 ? 'btn-success' : '';

$btn_tab_style = $PRODUCT_TAB_TYPE == 'style' ? 'btn-success' : '';
$btn_tab_item = $PRODUCT_TAB_TYPE == 'item' ? 'btn-success' : '';
?>
<div class="tab-pane fade" id="order">
<form id="orderForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<!--
		<div class="col-sm-3"><span class="form-control left-label">อายุของออเดอร์ ( วัน )</span></div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-small text-center" name="ORDER_EXPIRATION" required value="<?php echo $ORDER_EXPIRATION; ?>" />
      <span class="help-block">กำหนดวันหมดอายุของออเดอร์ หากออเดอร์อยู่ในสถานะ รอการชำระเงิน, รอจัดสินค้า หรือ ไม่บันทึก เกินกว่าจำนวนวันที่กำหนด</span>
    </div>
    <div class="divider-hidden"></div>
	-->
		
		<div class="col-sm-3"><span class="form-control left-label">การจำกัดการแสดงผลสต็อก</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="STOCK_FILTER" required value="<?php echo $STOCK_FILTER; ?>" />
			<span class="help-block">กำหนดจำนวนสินค้าคงเหลือสูงสุดที่จะแสดงใหเห็น ถ้าไม่ต้องการใช้กำหนดเป็น 0 </span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">อัพโหลดออเดอร์(รายการ)/ครั้ง</span></div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-small text-center" name="IMPORT_ROWS_LIMIT" value="<?php echo $IMPORT_ROWS_LIMIT; ?>" />
      <span class="help-block">จำกัดจำนวนรายการที่ออเดอร์ที่สามารถนำเข้าระบบได้ครั้งละไม่เกินรายการที่กำหนด เพื่อไม่ให้ระบบเกิดข้อผิดพลาด</span>
    </div>

		<div class="col-sm-3"><span class="form-control left-label">รหัสลูกค้าเริ่มต้น</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="DEFAULT_CUSTOMER" required value="<?php echo $DEFAULT_CUSTOMER; ?>" />
			<span class="help-block">ลูกค้าเริ่มต้นหากไม่มีการกำหนดรหัสลูกค้า</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">ควบคุมเครดิต</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_credit_yes; ?>" style="width:50%;" id="btn-credit-yes" onClick="toggleControlCredit(1)">คุม</button>
				<button type="button" class="btn btn-sm <?php echo $btn_credit_no; ?>" style="width:50%;" id="btn-credit-no" onClick="toggleControlCredit(0)">ไม่คุม</button>
			</div>
			<input type="hidden" name="CONTROL_CREDIT" id="control-credit" value="<?php echo $CONTROL_CREDIT; ?>" />
			<span class="help-block">ใช้การควบคุมเครดิตหรือไม่ หากควบคุมจะไม่สามารถเปิดออเดอร์ได้ถ้าเครดิตคงเหลือไม่เพียงพอ</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">วันเพิ่มในการคุมเครดิต</span></div>
		<div class="col-sm-9">
			<input type="number" class="form-control input-sm input-small text-center" name="OVER_DUE_DATE" required value="<?php echo $OVER_DUE_DATE; ?>" />
			<span class="help-block">จำนวนวันเพิ่มจากวันครบกำหนดชำระ เช่น เครดติ 30 วัน เพิ่มอีก 30 วัน</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">มียอดค้างชำระเกินกำหนด</span></div>
    <div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_strict_yes; ?>" style="width:50%;" id="btn-strict-yes" onClick="toggleStrictDue(1)">ไม่ขาย</button>
				<button type="button" class="btn btn-sm <?php echo $btn_strict_no; ?>" style="width:50%;" id="btn-strict-no" onClick="toggleStrictDue(0)">ขาย</button>
			</div>
      <span class="help-block">ไม่อนุญาติให้ขายสินค้าให้ลูกค้าที่มียอดค้างชำระเกินวันที่กำหนดในการคุมเครดิต</span>
			<input type="hidden" name="STRICT_OVER_DUE" id="strict-over-due" value="<?php echo $STRICT_OVER_DUE; ?>" />
    </div>
    <div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">วันที่ในการบันทึกขาย</span></div>
    <div class="col-sm-9">
			<div class="btn-group input-large">
				<button type="button" class="btn btn-sm <?php echo $btn_doc_date; ?>" style="width:50%;" id="btn-doc-date" onClick="toggleSoldDate('D')">วันที่เอกสาร</button>
				<button type="button" class="btn btn-sm <?php echo $btn_bill_date; ?>" style="width:50%;" id="btn-bill-date" onClick="toggleSoldDate('B')">วันที่เปิดบิล</button>
			</div>
      <span class="help-block">กำหนดวันที่ในการบันทึกขายว่าจะบันทึกขายโดยใช้วันที่ตามเอกสารหรือว่าจะใช้วันที่ตามวันที่เปิดบิล</span>
			<input type="hidden" name="ORDER_SOLD_DATE" id="order-sold-date" value="<?php echo $ORDER_SOLD_DATE; ?>" />
    </div>
    <div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">Order Grid</span></div>
    <div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_grid; ?>" style="width:50%;" id="btn-grid" onClick="toggleOrderGrid(1)">ใช้ Grid</button>
				<button type="button" class="btn btn-sm <?php echo $btn_table; ?>" style="width:50%;" id="btn-table" onClick="toggleOrderGrid(0)">ใช้ Table</button>
			</div>
      <span class="help-block">ตารางสั่งสินค้าในออเดอร์ เลือก Grid หากสินค้ามี สี และ ไซส์ เลือก Table หากสินค้าไม่มี สี และ ไซส์</span>
			<input type="hidden" name="USE_ORDER_GRID" id="use-order-grid" value="<?php echo $USE_ORDER_GRID; ?>" />
    </div>
    <div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">แถบแสดงสินค้า</span></div>
    <div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_tab_yes; ?>" style="width:50%;" id="btn-tab-yes" onClick="toggleProductTab(1)">ใช้</button>
				<button type="button" class="btn btn-sm <?php echo $btn_tab_no; ?>" style="width:50%;" id="btn-tab-no" onClick="toggleProductTab(0)">ไม่ใช้</button>
			</div>
      <span class="help-block">ตารางสั่งสินค้าในออเดอร์ เลือก Grid หากสินค้ามี สี และ ไซส์ เลือก Table หากสินค้าไม่มี สี และ ไซส์</span>
			<input type="hidden" name="USE_PRODUCT_TAB" id="use-product-tab" value="<?php echo $USE_PRODUCT_TAB; ?>" />
    </div>
    <div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">ชนิดของแถบแสดงสินค้า</span></div>
    <div class="col-sm-9">
			<div class="btn-group input-large">
				<button type="button" class="btn btn-sm width-50 <?php echo $btn_tab_style; ?>" id="btn-tab-style" onClick="toggleProductTabType('style')">รุ่นสินค้า</button>
				<button type="button" class="btn btn-sm width-50 <?php echo $btn_tab_item; ?>" id="btn-tab-item" onClick="toggleProductTabType('item')">รายการสินค้า</button>
			</div>
      <span class="help-block">กำหนดรูปแบบการใช้แถบแสดงสินค้า แสดงเป็น 'รุ่นสินค้า' หรือ 'รายการสินค้า'</span>
			<input type="hidden" name="PRODUCT_TAB_TYPE" id="product-tab-type" value="<?php echo $PRODUCT_TAB_TYPE; ?>" />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-9 col-sm-offset-3">
			<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('orderForm')"><i class="fa fa-save"></i> บันทึก</button>
		</div>
		<div class="divider-hidden"></div>

  </div>
</form>
</div><!--- Tab-pane --->
