<style>
.check-title {
  background-color: #f8f8f8;
  border: solid 1px #CCC;
  padding:8px;
  font-size: 14px;
  text-align: center;
}

.check-table {
  padding-left: 0;
  padding-right: 0;
  background-color: #CCC;
  height:400px;
  max-height: 400px;
  overflow: auto;
}
</style>

<div class="row">
  <div class="col-lg-6-harf col-md-7 col-sm-12 col-xs-12 padding-5">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 check-title" >กำลังตรวจนับ</div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 check-table">
      <table class="table table-striped tableFixHead border-1" style="min-width:440px;">
        <thead>
          <tr class="freez">
            <th class="fix-width-120">บาร์โค้ด</th>
            <th class="min-width-150">รหัสสินค้า</th>
            <th class="fix-width-50 text-center">จำนวน</th>
            <th class="fix-width-80 text-center">เวลา</th>
            <th class="fix-width-40 text-center">
              <a class="pointer red" href="javascript:removeCheck()" title="ลบรายการที่เลือก"><i class="fa fa-trash fa-lg"></i></a>
            </th>
          </tr>
        </thead>
        <tbody id="check-table">

        </tbody>
      </table>
    </div>
  </div>

  <div class="divider-hidden visible-xs"></div>
  <div class="divider-hidden visible-xs"></div>
  <div class="divider-hidden visible-xs"></div>

  <div class="col-lg-5-harf col-md-5 col-sm-12 col-xs-12 padding-5">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 check-title">ตรวจนับแล้ว</div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 check-table">
      <table class="table table-striped border-1 tableFixHead" style="min-width:350px;">
        <thead>
          <tr class="freez">
            <th class="fix-width-120">บาร์โค้ด</th>
            <th class="min-width-150">รหัสสินค้า</th>
            <th class="fix-width-80 text-right">จำนวน</th>
          </tr>
        </thead>
        <tbody id="checked-table">
          <tr id="head" class="hide"><td colspan="3"></td></tr>
          <?php if( ! empty($details)) : ?>
            <?php foreach($details as $rs) : ?>
              <?php $bc_id = md5($rs->barcode); ?>
            <tr id="row-<?php echo $bc_id; ?>">
              <td><?php echo $rs->barcode; ?></td>
              <td><?php echo $rs->code; ?></td>
              <td class="text-right" id="<?php echo $bc_id; ?>"><?php echo number($rs->qty); ?></td>
            </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
