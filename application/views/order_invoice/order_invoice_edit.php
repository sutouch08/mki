<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-8 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-4 padding-5">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($order->status == 0 && ($this->pm->can_add OR $this->can_edit)) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5">
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="code">เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" name="code" value="<?php echo $order->code; ?>" disabled />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="doc_date">วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" name="doc_date" id="doc_date" value="<?php echo thai_date($order->doc_date); ?>" readonly disabled />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label for="customer_code">รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customer_code" id="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
	</div>
	<?php if($use_vat) : ?>
	<div class="col-sm-6 col-xs-6 padding-5">
		<label for="customer_name">ลูกค้า</label>
		<input type="text" class="form-control input-sm" name="customer_name" id="customer_name" value="<?php echo $order->customer_name; ?>" disabled />
	</div>
	<div class="col-sm-1 col-1-harf col-xs-12 padding-5">
		<label for="phone">ราคาขาย</label>
		<select class="form-control input-sm edit" id="vat_type" name="vat_type" disabled>
			<option value="I" <?php echo is_selected('I', $order->vat_type); ?>>รวม VAT</option>
			<option value="E" <?php echo is_selected('E', $order->vat_type); ?>>ไม่รวม VAT</option>
		</select>
	</div>
<?php else : ?>
	<div class="col-sm-7 col-7-harf col-xs-6 padding-5">
		<label for="customer_name">ลูกค้า</label>
		<input type="text" class="form-control input-sm" name="customer_name" id="customer_name" value="<?php echo $order->customer_name; ?>" disabled />
	</div>
<?php endif; ?>

<div class="col-sm-9 col-xs-9 padding-5">
	<label for="remark">หมายเหตุ</label>
	<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled/>
</div>
<?php if($order->status == 0) : ?>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()">
			<i class="fa fa-pencil"></i> แก้ไข
		</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">
			<i class="fa fa-save"></i> บันทึก
		</button>
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getOrderList()">
			<i class="fa fa-search"></i> ออเดอร์
		</button>
	</div>
<?php endif; ?>
</div>
<input type="hidden" id="customerCode" value="<?php echo $order->customer_code; ?>" />
<?php if($order->status == 0) : ?>
<hr class="padding-5" />
<div class="row">
<div class="col-sm-12 col-xs-12 padding-5">
	<?php if(!empty($reference)) : ?>
		<span>อ้างอิง : </span>
		<?php foreach($reference as $ref) : ?>
			<span class="label label-info label-white label-xlg middle">
				<?php echo $ref->order_code; ?>
				<i class="fa fa-trash red pointer" onclick="confirm_remove('<?php echo $ref->order_code; ?>')"></i>
			</span>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
</div>
<?php endif; ?>
<hr class="padding-5 margin-top-15" />
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table border-1">
			<thead>
				<tr>
					<th class="width-5 text-center">#</th>
					<th class="width-10">รหัส</th>
					<th class="width-25">รายละเอียด</th>
					<th class="width-10">อ้างอิง</th>
					<th class="width-8 text-right">จำนวน</th>
					<th class="width-8">หน่วยนับ</th>
					<th class="width-8 text-right">ราคา</th>
					<th class="width-10 text-right">ส่วนลด</th>
					<th class="width-15 text-right">จำนวนเงิน</th>
				</tr>
			</thead>
			<tbody>
	<?php $no = 1; ?>
	<?php $total_amount_ex = 0; ?>
	<?php $total_amount_inc = 0; ?>
	<?php $total_vat = 0; ?>
	<?php if(!empty($details)) : ?>
		<?php foreach($details as $rs) : ?>
				<?php $price = vat_price($rs->price, $order->vat_type, $rs->vat_rate); //--- vat_helper ?>
				<?php $amount = vat_price($rs->amount, $order->vat_type, $rs->vat_rate); ?>

				<tr id="row-<?php echo $rs->id; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle"><?php echo $rs->order_code; ?></td>
					<td class="middle text-right"><?php echo number($rs->qty,2); ?></td>
					<td class="middle"><?php echo $rs->unit_name; ?></td>
					<td class="middle text-right"><?php echo number($price, 2); ?></td>
					<td class="middle text-right"><?php echo $rs->discount_label; ?></td>
					<td class="middle text-right"><?php echo number($amount, 2); ?></td>
				</tr>
			<?php $no++; ?>
			<?php $total_amount_inc += $rs->amount; ?>
			<?php $total_amount_ex += $amount; ?>
			<?php $total_vat += $rs->vat_amount; ?>
		<?php endforeach; ?>
	<?php else : ?>
		<tr>
			<td colspan="9" class="text-center">&nbsp;</td>
		</tr>
	<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8" class="middle text-right">รวมเป็นเงิน</td>
					<td class="middle text-right"><?php echo number($total_amount_ex, 2); ?></td>
				</tr>
			<?php if($use_vat) : ?>
				<tr>
					<td colspan="8" class="middle text-right">ภาษีมูลค่าเพิ่ม <?php echo getConfig('SALE_VAT_RATE'); ?> %</td>
					<td class="middle text-right"><?php echo number($total_vat, 2); ?></td>
				</tr>
			<?php endif; ?>
				<tr>
					<td colspan="8" class="middle text-right">จำนวนเงินรวมทั้งสิ้น</td>
					<td class="middle text-right"><?php echo number($total_amount_inc, 2); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>



<div class="modal fade" id="order-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="min-width:300px; max-width:90vw;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" >
            <div id="orderList"></div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-sm btn-info btn-block" data-dismiss="modal" onclick="addToOrder()">เพิ่มรายการที่เลือก</button>
            </div>
        </div>
    </div>
</div>

<script type="text/x-handlebarsTemplate" id="orderListTemplate">
	<table class="table table-striped" style="margin-bottom:0px;">
	{{#each this}}
		<tr>
			<td class="width-50">
			<label>
				<input type="checkbox" class="ace chk" value="{{orderCode}}">
				<span class="lbl">&nbsp;&nbsp; {{orderCode}}</span>
			</label>
			</td>
			<td class="width-50 text-right">{{amount}}</td>

		</tr>
	{{/each}}
	</table>
</script>









<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice_control.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
