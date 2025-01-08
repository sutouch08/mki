<?php $showZone = get_cookie('showZone') ? '' : 'hide'; ?>
<?php $showBtn  = get_cookie('showZone') ? 'hide' : '';  ?>
<?php $checked  = get_cookie('showZone') ? 'checked' : ''; ?>


<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:870px;">
      <thead>
        <tr><td colspan="6" align="center">รายการรอจัด</td></tr>
        <tr>
          <th class="fix-width-120 middle hidden-xs">บาร์โค้ด</th>
          <th class="min-width-300 middle">สินค้า</th>
          <th class="fix-width-100 middle text-center">จำนวน</th>
          <th class="fix-width-100 middle text-center">จัดแล้ว</th>
          <th class="fix-width-100 middle text-center">คงเหลือ</th>
          <th class="fix-width-150 middle text-right">แสดงที่เก็บ</th>
        </tr>
      </thead>
      <tbody id="incomplete-table">

        <?php  if(!empty($uncomplete_details)) : ?>
          <?php   foreach($uncomplete_details as $rs) : ?>
            <tr class="font-size-12 incomplete" id="incomplete-<?php echo $rs->id; ?>">
              <td class="middle b-click hidden-xs"><?php echo $rs->barcode; ?></td>
              <td class="middle">
                <span class="hidden-xs"><?php echo $rs->product_code .' : '.$rs->product_name; ?></span>
                <span class="visible-xs"><?php echo $rs->product_code; ?></span>
              </td>
              <td class="middle text-center" id="order-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></td>
              <td class="middle text-center" id="prepared-qty-<?php echo $rs->id; ?>"><?php echo number($rs->prepared); ?></td>
              <td class="middle text-center" id="balance-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty - $rs->prepared); ?></td>
              <td class="middle text-right"><?php echo $rs->stock_in_zone; ?></td>
            </tr>
          <?php endforeach; ?>
<?php
      $force = (!empty($uncomplete_details) ? '' : 'hide');
      $close = (!empty($uncomplete_details) ? 'hide' : '');
?>

  <tr>
    <td colspan="6" class="text-center">
      <div id="force-bar" class="">
        <button type="button" class="btn btn-sm btn-danger not-show" id="btn-force-close" onclick="forceClose()">
          <i class="fa fa-exclamation-triangle"></i>
          &nbsp; บังคับจบ
        </button>
        <label style="margin-left:15px;">
          <input type="checkbox" id="force-close" class="ace" style="margin-right:5px;" onchange="toggleForceClose()" />
          <span class="lbl">  สินค้าไม่ครบ</span>
        </label>

      </div>

      <div id="close-bar" class="<?php echo $close; ?>">
        <button type="button" class="btn btn-sm btn-success" onclick="finishPrepare()">จัดเสร็จแล้ว</button>
      </div>

    </td>
  </tr>

<?php else : ?>

  <tr>
    <td colspan="6" class="text-center">
      <div id="close-bar">
        <button type="button" class="btn btn-sm btn-success" onclick="finishPrepare()">จัดเสร็จแล้ว</button>
      </div>
    </td>
  </tr>

<?php endif; ?>
      </tbody>
    </table>
  </div><!--/ col -->
</div><!--/ row-->
