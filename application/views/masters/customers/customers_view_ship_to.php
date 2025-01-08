<div class='row'>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 table-responsive border-1">
		<table class='table table-bordered min-width-800'>
      <thead>
        <tr>
          <td colspan="6" align="center">ที่อยู่สำหรับจัดส่ง

          </td>
        </tr>
        <tr style="font-size:12px;">
          <td class="fix-width-100 text-center">ชื่อเรียก</td>
					<td class="fix-width-100 text-center">รหัสสาขา</td>
          <td class="fix-width-120 text-center">ผู้รับ</td>
          <td class="min-width-250">ที่อยู่</td>
          <td class="fix-width-150">โทรศัพท์</td>
        </tr>
      </thead>
      <tbody id="adrs">
<?php if(!empty($addr)) : ?>
<?php 	foreach($addr as $rs) : ?>
  <?php $default = $rs->is_default == 1 ? 'color:green;' : ''; ?>
  <?php  $tumbon = !empty($rs->sub_district) ? ' ต.'.$rs->sub_district : ''; ?>
  <?php  $aumphor = !empty($rs->district) ? ' อ.'.$rs->district : ''; ?>
  <?php  $province = !empty($rs->province) ? ' จ.'.$rs->province : ''; ?>
  <?php  $postcode = !empty($rs->postcode) ? ' '.$rs->postcode : ''; ?>
        <tr style="font-size:12px; <?php echo $default; ?>" id="<?php echo $rs->id; ?>">
          <td align="center"><?php echo $rs->alias; ?></td>
					<td><?php echo $rs->code; ?></td>
          <td><?php echo $rs->name; ?></td>
          <td><?php echo $rs->address . $tumbon . $aumphor . $province . $postcode; ?></td>
          <td><?php echo $rs->phone; ?></td>
<?php 	endforeach; ?>
<?php else : ?>
        <tr><td colspan="5" align="center">ไม่พบที่อยู่</td></tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div><!-- /row-->
