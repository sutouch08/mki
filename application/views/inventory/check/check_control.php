<?php $disabled = $doc->allow_input_qty == 1 ? '' : 'disabled'; ?>

<div class="row">
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center focus" name="qty" id="qty" value="1" placeholder="Qty" <?php echo $disabled; ?>/>
  </div>

  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-8 padding-5">
    <label>บาร์โค้ด</label>
    <input type="text" class="form-control input-sm text-center focus" name="barcode" id="barcode" placeholder="แสกนบาร์โค้ดเพื่อตรวจนับ"autofocus />
  </div>
  <div class="col-lg-2-harf col-md- col-sm-3 col-xs-8 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center focus" name="sku" id="pd-code" placeholder="รหัสสินค้า" />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-check" onclick="doChecking()">ตรวจนับ</button>
  </div>

  <div class="divider-hidden visible-sm"></div>
  <div class="col-lg-1-harf col-lg-offset-2 col-md-2 col-sm-2 col-xs-6">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="inputView()">รายการล่าสุด (F3)</button>
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="clearSheet()">เคลียร์พื้นที่ (F2)</button>
  </div>
</div>

<hr class="margin-top-15 margin-bottom-15"/>
