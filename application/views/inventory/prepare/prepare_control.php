
<div class="row">
  <div class="col-lg-4 col-md-4 col-sm-4-harf col-xs-12 padding-5">
    <label>โซน</label>
    <select class="width-100" id="zone_code" onchange="set_focus()">
      <option value="">ระบุโซน</option>
      <?php echo select_sell_zone(); ?>
    </select>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="qty" value="1" />
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm" id="barcode-item"/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-default btn-block" id="btn-submit" onclick="doPrepare()">ตกลง</button>
  </div>
  <div class="col-lg-1 col-lg-offset-3 col-md-1-harf col-md-offset-1 col-sm-1 col-sm-offset-1 hidden-xs padding-5">
    <label class="display-block not-show">refresh</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="window.location.reload()">Refresh</button>
  </div>
</div>
