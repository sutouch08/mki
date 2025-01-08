<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm" id="item-code" autofocus/>
  </div>
  <div class="col-lg-2-harf col-md-3-harf col-sm-4 col-xs-8 padding-5">
    <label>สินค้า</label>
    <input type="text" class="form-control input-sm" id="item-name" readonly/>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
    <label>ราคา</label>
    <input type="number" class="form-control input-sm text-center" id="txt-price" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
    <label>ส่วนลด</label>
    <input type="text" class="form-control input-sm text-center" id="txt-disc" />
  </div>

  <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 padding-5">
    <label>ในโซน</label>
    <label class="form-control input-sm text-center blue" style="margin-bottom:0px;" id="stock-qty">0</label>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="txt-qty" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
    <label>มูลค่า</label>
    <span class="form-control input-sm text-center" id="txt-amount">0</span>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">submit</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addToDetail()">เพิ่ม</button>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Reset</label>
    <button type="button" class="btn btn-xs btn-default btn-block" onclick="clearFields()">เคลียร์</button>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Reset</label>
    <button type="button" class="btn btn-xs btn-danger btn-block" id="btn-del" onclick="deleteChecked()">ลบรายการ</button>
  </div>
</div>
<input type="hidden" id="product_code" />
<input type="hidden" id="count_stock" value="1" />
<hr class="margin-top-15 margin-bottom-15" />
