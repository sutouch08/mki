<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($data->status != 2 && $data->is_closed == 0 && ($this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-sm btn-yellow" onclick="goEdit('<?php echo $data->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
				<?php endif; ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="printQuantation()"><i class="fa fa-print"></i> พิมพ์</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
  <div class="col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $data->code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo thai_date($data->date_add); ?>"  readonly disabled />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" name="customer" id="customer" value="<?php echo $data->customer_code; ?>"  disabled />
	</div>
  <div class="col-sm-4 col-xs-6 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm" name="customerName" id="customerName" value="<?php echo $data->customer_name; ?>" disabled/>
  </div>

	<div class="col-sm-3 col-xs-6 padding-5">
    <label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm" name="contact" id="contact" value="<?php echo $data->contact; ?>" disabled/>
  </div>

  <div class="col-sm-1 col-xs-6 padding-5">
    <label>เงื่อนไข</label>
		<select class="form-control input-sm" name="is_term" id="is_term" disabled>
			<option value="0" <?php echo is_selected($data->is_term, 0); ?>>เงินสด</option>
      <option value="1" <?php echo is_selected($data->is_term, 1); ?>>เครดิต</option>
    </select>
  </div>

	<div class="col-sm-1 col-xs-6 padding-5">
    <label>เครดิต(วัน)</label>
		<input type="number" class="form-control input-sm text-center" value="<?php echo intval($data->credit_term); ?>" disabled/>
  </div>

	<div class="col-sm-1 col-xs-6 padding-5">
    <label>ยืนราคา(วัน)</label>
		<input type="number" class="form-control input-sm text-center" value="<?php echo intval($data->valid_days); ?>" disabled/>
  </div>

	<div class="col-sm-3 col-xs-12 padding-5">
    <label>ชื่องาน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $data->title; ?>" disabled>
  </div>

  <div class="col-sm-6 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" value="<?php echo $data->remark; ?>" disabled>
  </div>
</div>
<input type="hidden" name="customerCode" id="customerCode" value="<?php echo $data->customer_code; ?>" />
<input type="hidden" name="code" id="code" value="<?php echo $data->code; ?>" />

<?php
if($data->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<hr class="margin-top-15 margin-bottom-15 padding-5">

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5">
		<div class="table-responsive">
			<table class="table table-striped border-1">
				<thead>
					<tr>
						<th class="width-5 middle text-center">#</th>
						<th class="width-5 middle text-center"></th>
						<th class="width-15 middle">รหัสสินค้า</th>
						<th class="middle hidden-xs">ชื่อสินค้า</th>
						<th class="width-8 middle text-right">ราคา</th>
						<th class="width-8 middle text-right">จำนวน</th>
						<th class="width-10 middle text-center">ส่วนลด</th>
						<th class="width-10 middle text-right">มูลค่า</th>
						<th class="width-5"></th>
					</tr>
				</thead>
				<tbody id="detail-table">
			<?php if(!empty($details)) : ?>
			<?php
							$no = 1;
							$total_qty = 0;
							$total_discount = 0;
							$total_amount = 0;
			?>
			<?php 	foreach($details as $rs) : ?>
				<tr id="row-<?php echo $rs->id; ?>">
					<td class="middle text-center"><?php echo $no; ?></td>
					<td class="middle text-center">
						<img src="<?php echo get_product_image($rs->product_code, 'mini'); ?>" width="40px" height="40px"/>
					</td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle hidden-xs"><?php echo $rs->product_name; ?></td>
					<td class="middle text-right"><?php echo number($rs->price,2); ?></td>
					<td class="middle text-right"><?php echo number($rs->qty); ?></td>
					<td class="middle text-center"><?php echo discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?></td>
					<td class="middle text-right"><?php echo number($rs->total_amount, 2); ?></td>
					<td class="middle text-right">THB.</td>
				</tr>
			<?php   $no++; ?>
			<?php   $total_qty += $rs->qty; ?>
			<?php 	$total_discount += $rs->discount_amount; ?>
			<?php 	$total_amount += $rs->total_amount; ?>
			<?php 	endforeach; ?>

			<?php
				$totalBfDisc = $total_amount + $total_discount;
				$total_discount += $data->bDiscAmount;
				$total_amount -= $data->bDiscAmount;
			 ?>


			<tr id="billDisc">
				<td colspan="6" class="middle"></td>
				<td class="middle">ส่วนลดท้ายบิล</td>
				<td class="middle text-right"><?php echo $data->bDiscAmount; ?></td>
				<td class="middle text-center">THB.</td>
			</tr>

				<tr>
					<td colspan="6" rowspan="4" style="border-right:solid 1px #cccc;"></td>
					<td class="">จำนวนรวม</td>
					<td class="text-right"><?php echo number($total_qty); ?></td>
					<td class="text-center">Pcs.</td>
				</tr>
				<tr>
					<td class="">มูลค่ารวม</td>
					<td class="text-right"><?php echo number($totalBfDisc, 2); ?></td>
					<td class="text-center">THB.</td>
				</tr>
				<tr>
					<td class="">ส่วนลดรวม</td>
					<td class="text-right"><?php echo number($total_discount, 2); ?></td>
					<td class="text-center">THB.</td>
				</tr>
				<tr>
					<td class="">สุทธิ</td>
					<td class="text-right"><?php echo number($total_amount, 2); ?></td>
					<td class="text-center">THB.</td>
				</tr>
			<?php else : ?>
				<tr>
					<td colspan="9" class="middle text-center">---- ไม่พบรายการ ----</td>
				</tr>
			<?php endif; ?>
				</tbody>
			</table>
		</div>

	</div>
</div>



<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:250px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <div class="margin-top-10 text-center">
          <label>ส่วนลด</label>
          <input type="text" class="form-control input-sm input-medium text-center inline" id="discountLabel" value="0"/>
        </div>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="insert_item()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>



<script id="detail-template" type="text/x-handlebarsTemplate">
{{#each this}}
	{{#if notata}}
		<tr>
			<td colspan="8" class="middle text-center">---- ไม่พบรายการ ----</td>
		</tr>
	{{else}}
		{{#if subtotal}}
			<tr>
				<td colspan="5" rowspan="4" style="border-right:solid 1px #cccc;"></td>
				<td class="">จำนวนรวม</td>
				<td class="text-right">{{total_qty}}</td>
				<td class="text-center">Pcs.</td>
			</tr>
			<tr>
				<td class="">มูลค่ารวม</td>
				<td class="text-right">{{total_amount}}</td>
				<td class="text-center">THB.</td>
			</tr>
			<tr>
				<td class="">ส่วนลดรวม</td>
				<td class="text-right">{{total_discount}}</td>
				<td class="text-center">THB.</td>
			</tr>
			<tr>
				<td class="">สุทธิ</td>
				<td class="text-right">{{net_amount}}</td>
				<td class="text-center">THB.</td>
			</tr>
		{{else}}
			<tr id="row-{{id}}">
				<td class="middle text-center">{{no}}</td>
				<td class="middle">{{product_code}}</td>
				<td class="middle hidden-xs">{{product_name}}</td>
				<td class="middle text-right">{{price}}</td>
				<td class="middle text-right">{{qty}}</td>
				<td class="middle text-center">{{discount_label}}</td>
				<td class="middle text-right">{{amount}}</td>
				<td class="middle text-right">
					<button class="btn btn-minier btn-danger" onclick="removeRow({{id}})"><i class="fa fa-trash"></i></button>
				</td>
			</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>




<script src="<?php echo base_url(); ?>scripts/quotation/quotation.js"></script>
<script src="<?php echo base_url(); ?>scripts/quotation/quotation_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_quotation.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js"></script>


<?php $this->load->view('include/footer'); ?>
