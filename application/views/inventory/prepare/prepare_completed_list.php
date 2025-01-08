<?php $showZone = get_cookie('showZone') ? '' : 'hide'; ?>
<?php $showBtn  = get_cookie('showZone') ? 'hide' : '';  ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:870px;">
      <thead>
        <tr><td colspan="6" align="center">รายการที่ครบแล้ว</td></tr>
        <tr>
          <th class="fix-width-120 middle hidden-xs">บาร์โค้ด</th>
          <th class="min-width-300 middle">สินค้า</th>
          <th class="fix-width-100 middle text-center">จำนวน</th>
          <th class="fix-width-100 middle text-center">จัดแล้ว</th>
          <th class="fix-width-100 middle text-center">คงเหลือ</th>
          <th class="fix-width-150 middle text-right"><span class="hidden-xs">จัดจากโซน</span></th>
        </tr>
      </thead>
      <tbody id="complete-table">
        <?php  if(!empty($complete_details)) : ?>
          <?php   foreach($complete_details as $rs) : ?>
            <tr class="font-size-12">
              <td class="middle hidden-xs"><?php echo $rs->barcode; ?></td>
              <td class="middle">
                <span class="hidden-xs"><?php echo $rs->product_code .' : '.$rs->product_name; ?></span>
                <span class="visible-xs"><?php echo $rs->product_code; ?></span>
              </td>
              <td class="middle text-center"><?php echo number($rs->qty); ?></td>
              <td class="middle text-center"><?php echo number($rs->prepared); ?></td>
              <td class="middle text-center"><?php echo number($rs->qty - $rs->prepared); ?></td>
              <td class="middle text-right"><?php echo $rs->from_zone; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
