<div class="row">
	<div class="col-sm-12" id="move-table">
  	<table class="table table-striped border-1">
    	<thead>
      	<tr>
        	<th colspan="7" class="text-center">รายการโอนย้าย</th>
        </tr>
				<tr>
        	<th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-100">บาร์โค้ด</th>
          <th class="fix-width-100">รหัส</th>
					<th class="min-width-150">สินค้า</th>
          <th class="fix-width-200">ต้นทาง</th>
          <th class="fix-width-200">ปลายทาง</th>
          <th class="fix-width-100 text-right">จำนวน</th>
        </tr>
      </thead>

      <tbody id="move-list">
<?php if(!empty($details)) : ?>
<?php		$no = 1;						?>
<?php   $total_qty = 0; ?>
<?php		foreach($details as $rs) : 	?>
				<tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
	      	<td class="middle text-center">
						<?php echo $no; ?>
					</td>
					<!--- บาร์โค้ดสินค้า --->
	        <td class="middle">
						<?php echo $rs->barcode; ?>
					</td>
					<!--- รหัสสินค้า -->
	        <td class="middle">
						<?php echo $rs->product_code; ?>
					</td>

					<td class="middle">
						<?php echo $rs->product_name; ?>
					</td>
					<!--- โซนต้นทาง --->
	        <td class="middle">
	      		<input type="hidden" class="row-zone-from" id="row-from-<?php echo $rs->id; ?>" value="<?php echo $rs->from_zone; ?>" />
						<?php echo $rs->from_zone_name; ?>
	        </td>
	        <td class="middle" id="row-label-<?php echo $rs->id; ?>">
						<?php 	echo $rs->to_zone_name; 	?>
	        </td>

					<td class="middle text-right" >
						<?php echo number($rs->qty); ?>
					</td>
	      </tr>
<?php			$no++;			?>
<?php     $total_qty += $rs->qty; ?>
<?php		endforeach;			?>
				<tr>
					<td colspan="6" class="middle text-right"><strong>รวม</strong></td>
					<td class="middle text-right"><strong><?php echo number($total_qty); ?></strong></td>
				</tr>
<?php	else : ?>
 				<tr>
        	<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
        </tr>
<?php	endif; ?>
      </tbody>
    </table>
  </div>
</div>
