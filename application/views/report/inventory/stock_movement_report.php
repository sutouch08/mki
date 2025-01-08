<?php $this->load->view("include/header"); ?>

<div class="row hidden-print" id="header-row">
	<div class="col-lg-6 col-md-6 col-sm-7 hidden-xs padding-5">
		<h3 class="title">
			<i class="fa fa-bar-chart"></i>
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-xs-12 visible-xs padding-5">
		<h3 class="title-xs">
			<i class="fa fa-bar-chart"></i>
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-5 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row" id="filter-row">
  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-4 padding-5">
    <label class="display-block">สินค้า</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-4 padding-5">
    <label class="display-block not-show">start</label>
    <input type="text" class="form-control input-sm text-center h" id="pdFrom" name="pdFrom" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-4 padding-5">
    <label class="display-block not-show">End</label>
    <input type="text" class="form-control input-sm text-center h" id="pdTo" name="pdTo" placeholder="สิ้นสุด" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-4 padding-5">
    <label class="display-block">คลัง</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
    </div>
  </div>
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-5 padding-5">
    <label class="display-block">การแสดงผล</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-group-all" onclick="toggleGroupWarehouse(1)">รวมคลัง</button>
      <button type="button" class="btn btn-sm width-50" id="btn-group-range" onclick="toggleGroupWarehouse(0)">แยกคลัง</button>
    </div>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-3 padding-5">
    <label class="display-block">ณ วันที่</label>
		<input type="text" class="form-control input-sm text-center h" id="date" name="date" readonly value="<?php echo date("d-m-Y"); ?>">
  </div>

  <input type="hidden" id="allProduct" name="allProduct" value="1">
  <input type="hidden" id="allWarehouse" name="allWarehouse" value="1">
	<input type="hidden" id="groupWarehouse" name="groupWarehouse" value="1">
	<input type="hidden" id="filter" name="filter" value="" />
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>" />
</div>

<div class="modal fade" id="wh-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="width:500px; max-width:95vw;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title" id="modal_title">เลือกคลัง</h4>
			</div>
			<div class="modal-body" id="modal_body">
				<div class="row">
					<?php if(!empty($whList)) : ?>
						<?php foreach($whList as $rs) : ?>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<label>
									<input type="checkbox" class="chk" id="<?php echo $rs->code; ?>" name="warehouse[<?php echo $rs->code; ?>]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
									<?php echo $rs->code; ?> | <?php echo $rs->name; ?>
								</label>
							</div>
						<?php endforeach; ?>
					<?php endif;?>
				</div>
				<div class="divider" ></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
			</div>
		</div>
	</div>
</div>
</form>
<hr>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" id="title-row">
		<table class="table table-bordered border-1">
			<tr>
				<td colspan="3" class="text-center font-size-14">รายงานความเคลื่อนไหวสินค้า ณ วันที่ <span id="date-title"><?php echo date('d-m-Y'); ?></span></td>
			</tr>
			<tr>
				<td class="width-40 font-size-14">สินค้า :  <span id="item-title">ทั้งหมด</span></td>
				<td class="width-40 font-size-14">คลัง :  <span id="wh-title">ทั้งหมด</span></td>
				<td class="width-20 font-size-14">การแสดงผล :  <span id="group-title">ทั้งหมด</span></td>
			</tr>
		</table>
	</div>
</div>
<div class="row" style="padding-right:12px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1" id="report-row"
		style="margin-left:7px; padding:0px; background-color:#fff; height:350px; overflow:auto;">
		<table class="table table-striped tableFixHead" style="min-width:1050px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center fix-header-no-border">#</th>
					<th class="fix-width-120  fix-header-no-border">รหัส</th>
					<th class="min-width-200  fix-header-no-border">สินค้า</th>
					<th class="fix-width-150  fix-header-no-border">คลัง</th>
					<th class="fix-width-100 text-right fix-header-no-border">เข้า</th>
					<th class="fix-width-100 text-right fix-header-no-border">ออก</th>
					<th class="fix-width-100 text-right fix-header-no-border">คงเหลือ</th>
					<th class="fix-width-120 text-center fix-header-no-border">เข้าล่าสุด</th>
					<th class="fix-width-120 text-center fix-header-no-border">ออกล่าสุด</th>
				</tr>
			</thead>
			<tbody id="report-table">

			</tbody>
		</table>
	</div>
</div>


<script id="template" type="text/x-handlebars-template">
	{{#each data}}
		{{#if nodata}}
		<tr>
			<td colspan="9" align="center"><h4>-----  ไม่พบสินค้าคงเหลือตามเงื่อนไขที่กำหนด  -----</h4></td>
		</tr>
		{{else}}
		<tr>
			<td class="text-center">{{no}}</td>
			<td class="">{{product_code}}</td>
			<td class="">{{product_name}}</td>
			<td class="">{{warehouse_code}}</td>
			<td class="text-right">{{move_in}}</td>
			<td class="text-right">{{move_out}}</td>
			<td class="text-right">{{balance}}</td>
			<td class="text-center">{{last_in}}</td>
			<td class="text-center">{{last_out}}</td>
		</tr>
		{{/if}}
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/report/inventory/stock_movement.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view("include/footer"); ?>
