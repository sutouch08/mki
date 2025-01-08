<div class="tab-pane fade" id="system">
<?php
    $open     = $CLOSE_SYSTEM == 0 ? 'btn-success' : '';
    $close    = $CLOSE_SYSTEM == 1 ? 'btn-danger' : '';
		$pos_yes = $USE_POS == 1 ? 'btn-primary' : '';
		$pos_no = $USE_POS == 0 ? 'btn-primary' : '';
?>

  <form id="systemForm">
    <div class="row">
  	<?php if( $this->_SuperAdmin ): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	?>
    	<div class="col-sm-3"><span class="form-control left-label">ปิดระบบ</span></div>
      <div class="col-sm-9">
      	<div class="btn-group input-medium">
        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:50%;" id="btn-open" onClick="openSystem()">เปิด</button>
          <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:50%;" id="btn-close" onClick="closeSystem()">ปิด</button>
        </div>
        <span class="help-block">กรณีปิดระบบจะไม่สามารถเข้าใช้งานระบบได้ในทุกส่วน โปรดใช้ความระมัดระวังในการกำหนดค่านี้</span>
      	<input type="hidden" name="CLOSE_SYSTEM" id="closed" value="<?php echo $CLOSE_SYSTEM; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">ระบบ POS</span></div>
      <div class="col-sm-9">
      	<div class="btn-group input-medium">
        	<button type="button" class="btn btn-sm <?php echo $pos_yes; ?>" style="width:50%;" id="btn-pos-yes" onClick="togglePOS(1)">เปิด</button>
          <button type="button" class="btn btn-sm <?php echo $pos_no; ?>" style="width:50%;" id="btn-pos-no" onClick="togglePOS(0)">ปิด</button>
        </div>
        <span class="help-block">เปิด/ปิด การใช้งานระบบ POS</span>
      	<input type="hidden" name="USE_POS" id="use_pos" value="<?php echo $USE_POS; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">วันที่ใช้งานระบบ</span></div>
      <div class="col-sm-4 col-xs-12">
				<div class="input-daterange input-group">
		      <input type="text" class="form-control input-sm width-50 text-center from-date" name="SYSTEM_START_DATE" id="start_date" value="<?php echo (empty($SYSTEM_START_DATE) ? "" : thai_date($SYSTEM_START_DATE)); ?>" />
		      <input type="text" class="form-control input-sm width-50 text-center" name="SYSTEM_END_DATE" id="end_date" value="<?php echo (empty($SYSTEM_END_DATE) ? "" : thai_date($SYSTEM_END_DATE)); ?>" />
		    </div>
				<span class="help-block">ช่วงวันที่ ที่สามารถใช้งานระบบได้ ไม่สามารถใช้ระบบได้หากวันที่ปัจจุบันเกินกว่าที่กำหนดไว้ (หากไม่ระบุจะสามารถใช้ได้ตลอดไป)</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">แจ้งเตือนล่วงหน้า</span></div>
      <div class="col-sm-9 col-xs-12">
				<input type="number" class="form-control input-sm input-mini text-center" name="SYSTEM_WARNING_DAYS" value="<?php echo $SYSTEM_WARNING_DAYS; ?>" />
				<span class="help-block">แสดงการแจ้งเตือนว่าระบบจะถูกระงับ(หลังหมดอายุใช้งาน)จำนวนกี่วัน</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">ให้ใช้ต่อได้อีก(วัน)</span></div>
      <div class="col-sm-9 col-xs-12">
				<input type="number" class="form-control input-sm input-mini text-center" name="SYSTEM_END_AFTER_DAYS" value="<?php echo $SYSTEM_END_AFTER_DAYS; ?>" />
				<span class="help-block">อณุญาติให้ใช้งานได้ต่ออีกกี่วัน หลังจากวันหมดอายุแล้ว</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">จำกัดจำนวนออเดอร์/เดือน</span></div>
      <div class="col-sm-9 col-xs-12">
				<input type="number" class="form-control input-sm input-mini text-center" name="SYSTEM_ORDER_LIMIT" value="<?php echo $SYSTEM_ORDER_LIMIT; ?>" />
				<span class="help-block">จำกัดจำนวนออเดอร์ที่สามารถเพิ่มได้ต่อเดือน ใส่ 0 หากไม่จำกัดจำนวน</span>
      </div>
      <div class="divider-hidden"></div>

    <?php endif; ?>



      <div class="col-sm-9 col-sm-offset-3">
      	<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('systemForm')"><i class="fa fa-save"></i> บันทึก</button>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/row-->
  </form>
</div><!--/ tab pane -->

<script>
	$('#start_date').datepicker({
		dateFormat:'dd-mm-yy',
		onClose:function(sd) {
			$('#end_date').datepicker("option", "minDate", sd);
		}
	})

	$('#end_date').datepicker({
		dateFormat:'dd-mm-yy',
		onClose:function(sd) {
			$('#start_date').datepicker('option', 'maxDate', sd);
		}
	})
</script>
