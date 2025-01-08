<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-xs-12 visible-xs">
    <h3 class="title-xs">
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
			<?php if($this->pm->can_delete && $doc->status == 1) : ?>
				<button type="button" class="btn btn-sm btn-danger top-btn" onclick="unSave()"><i class="fa fa-refresh"></i> <?php label('unsave'); ?></button>
			<?php endif; ?>
      <button type="button" class="btn btn-sm btn-info top-btn" onclick="printReceived()"><i class="fa fa-print"></i> <?php label('print'); ?></button>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4">
  	<label><?php label('doc_num'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
		<label><?php label('date'); ?></label>
		<input type="text" class="form-control input-sm text-center edit" id="date-add" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
		<label>วันที่รับ</label>
		<input type="text" class="form-control input-sm text-center edit" name="post_date" id="post-date" value="<?php echo thai_date($doc->posting_date); ?>" readonly disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4">
			<label><?php label('vender_code'); ?></label>
				<input type="text" class="form-control input-sm text-center edit" id="venderCode" value="<?php echo $doc->vender_code; ?>" placeholder="ค้นหารหัสผู้ผลิต" disabled/>
	 </div>
	 <div class="col-lg-4 col-md-5 col-sm-5 col-xs-8">
			<label><?php label('vender_name'); ?></label>
				<input type="text" class="form-control input-sm edit" id="venderName" value="<?php echo $doc->vender_name; ?>" placeholder="ค้นหาชื่อผู้ผลิต" disabled/>
	 </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6">
			<label>ใบสั่งผลิต</label>
			<input type="text" class="form-control input-sm text-center edit" id="poCode" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งผลิต" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6">
		<label><?php label('inv'); ?></label>
		<input type="text" class="form-control input-sm text-center edit" id="invoice" value="<?php echo $doc->invoice_code; ?>" placeholder="อ้างอิงใบส่งสินค้า" disabled/>
	</div>
	<div class="col-lg-3 col-md-3-harf col-sm-4 col-xs-6">
		<label>คลัง</label>
		<select class="form-control input-sm edit" id="warehouse" disabled>
			<option value="">เลือก</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>
	<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
		<label><?php label('remark'); ?></label>
		<input type="text" class="form-control input-sm edit" id="remark" value="<?php echo $doc->remark; ?>" placeholder="ระบุหมายเตุ(ถ้ามี)" disabled/>
	</div>
  <input type="hidden" name="code" id="code" value="<?php echo $doc->code; ?>" />
</div>

<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
    <table class="table table-striped border-1" style="min-width:1010px;">
      <thead>
      	<tr class="font-size-12">
        	<th class="fix-width-40 text-center"><?php label('Num'); ?>	</th>
          <th class="fix-width-150"><?php label('item_code'); ?></th>
          <th class="min-width-250"><?php label('item_name'); ?></th>
					<th class="fix-width-150">โซน</th>
					<th class="fix-width-100 text-center">Lot.</th>
					<th class="fix-width-100 text-right"><?php label('price'); ?></th>
          <th class="fix-width-100 text-right"><?php label('qty'); ?></th>
					<th class="fix-width-120 text-right"><?php label('amount'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no =  1; ?>
          <?php $total_qty = 0; ?>
					<?php $total_amount = 0; ?>
          <?php foreach($details as $rs) : ?>
            <tr class="font-size-12">
              <td class="text-center"><?php echo $no; ?></td>
              <td class=""><?php echo $rs->product_code; ?></td>
              <td class=""><?php echo $rs->product_name; ?></td>
							<td class=""><?php echo $rs->zone_name; ?></td>
							<td class="text-center">
								<?php echo (empty($rs->receive_date) ? thai_date($doc->date_add, FALSE, '/') : thai_date($rs->receive_date, FALSE, '/')); ?>
							</td>
							<td class="text-right"><?php echo number($rs->price, 2); ?></td>
              <td class="text-right"><?php echo number($rs->qty); ?></td>
							<td class=" text-right"><?php echo number($rs->amount, 2); ?></td>
            </tr>
            <?php $no++; ?>
            <?php $total_qty += $rs->qty; ?>
						<?php $total_amount += $rs->amount; ?>
          <?php endforeach; ?>
          <tr>
            <td colspan="6" class="text-right"><strong><?php label('total'); ?></strong></td>
            <td class="text-right"><strong><?php echo number($total_qty); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_amount, 2); ?></strong></td>
          </tr>
        <?php endif; ?>
			  </tbody>
      </table>
    </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_edit.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
