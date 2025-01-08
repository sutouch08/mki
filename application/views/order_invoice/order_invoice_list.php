<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
			<?php if($this->_use_vat) : ?>
			<button type="button" class="btn btn-sm btn-info top-btn" onclick="printSelectedInvoice('tax')"><i class="fa fa-print"></i> พิมพ์ใบกำกับภาษี</button>
		<?php else : ?>
			<button type="button" class="btn btn-sm btn-info top-btn" onclick="printSelectedInvoice('do')"><i class="fa fa-print"></i> พิมพ์ใบส่งสินค้า</button>
		<?php endif; ?>
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
    <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label for="code">เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm search-box" name="code" id="code" value="<?php echo $code; ?>" />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label for="order_code">เลขที่อ้างอิง</label>
		<input type="text" class="form-control input-sm search-box" name="order_code" id="order_code" value="<?php echo $order_code; ?>" />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label for="customer">ลูกค้า</label>
		<input type="text" class="form-control input-sm search-box" name="customer" id="customer" value="<?php echo $customer; ?>" />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label for="status">สถานะ</label>
		<select class="form-control input-sm" name="status" id="status" onchange="getSearch()">
			<option value="all" <?php echo is_selected('all', $status); ?>>ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
			<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label for="customer">วันที่</label>
		<div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 from-date" name="from_date" id="from_date" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50" name="to_date" id="to_date" value="<?php echo $to_date; ?>" />
    </div>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1 col-xs-3 padding-5">
		<label class="display-block not-show">search</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">
			<i class="fa fa-search"></i> ค้นหา
		</button>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1 col-xs-3 padding-5">
		<label class="display-block not-show">reset</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">
			<i class="fa fa-retweet"></i> Reset
		</button>
	</div>
</div>
</form>

<hr class="padding-5 margin-top-15"/>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <span>ว่าง</span><span class="margin-right-15"> = ปกติ</span>
      <span class="blue">NC</span><span class="margin-right-15"> = ยังไม่บันทึก</span>
      <span class="red">CN</span><span class=""> = ยกเลิก</span>
    </p>
  </div>
</div>

<div class="row">
	<div class="col-lg-12 co-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1100px;">
			<thead>
				<tr>
					<th class="fix-width-50 text-center">#</th>
					<th class="fix-width-20 text-center"></th>
					<th class="fix-width-100 text-center">วันที่</th>
					<th class="fix-width-120">เลขที่</th>
					<th class="fix-width-120">อ้างอิง</th>
					<th class="min-width-250">ลูกค้า</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
					<th class="fix-width-60 text-center">สถานะ</th>
					<th class="fix-width-100 text-right"></th>
				</tr>
			</thead>
			<tbody>
	<?php if(!empty($order)) : ?>
		<?php $no = $this->uri->segment(4) + 1; ?>
		<?php foreach($order as $rs) : ?>
				<tr>
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle text-center" style="padding-left:0px; padding-right:0px;">
						<?php if($rs->status == 1) : ?>
						<label>
							<input type="checkbox" class="ace chk" value="<?php echo $rs->code; ?>" />
							<span class="lbl"></span>
						</label>
						<?php endif; ?>
					</td>
					<td class="middle text-center"><?php echo thai_date($rs->doc_date); ?></td>
					<td class="middle"><?php echo $rs->code; ?></td>
					<td class="middle" style="overflow:hidden;"><?php echo $rs->reference; ?></td>
					<td class="middle"><?php echo $rs->customer_code." : ".$rs->customer_name; ?></td>
					<td class="middle text-right"><?php echo number($rs->total_amount, 2); ?></td>
					<td class="middle text-center">
						<?php if($rs->status == 2) : ?>
							<span class="red">CN</span>
						<?php elseif($rs->status == 0) : ?>
							<span class="blue">NC</span>
						<?php endif; ?>
					</td>
					<td class="middle text-right">
						<button type="button" class="btn btn-minier btn-info" onclick="view_detail('<?php echo $rs->code; ?>')">
							<i class="fa fa-eye"></i>
						</button>

		<?php if($rs->status == 0) : ?>
			<?php if($this->pm->can_edit) : ?>
						<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')">
							<i class="fa fa-pencil"></i>
						</button>
			<?php endif; ?>
			<?php if($this->pm->can_delete) : ?>
						<button type="button" class="btn btn-minier btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')">
							<i class="fa fa-trash"></i>
						</button>
			<?php endif; ?>
		<?php endif; ?>
					</td>
				</tr>
			<?php $no++; ?>
		<?php endforeach; ?>
	<?php else : ?>
		<tr><td colspan="8" class="text-center"> --- ไม่พบรายการตามเงื่อนไขที่กำหนด --- </td></tr>
	<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
