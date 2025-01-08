<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-6 col-md-6 col-sm-8 hidden-xs padding-5">
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
	<div class="col-lg-6 col-md-6 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-4 padding-5">
    <label class="display-block">ลูกค้า</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-cus-all" onclick="toggleAllCustomer(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-cus-range" onclick="toggleAllCustomer(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-4 padding-5">
    <label class="display-block">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="cusFrom" name="cusFrom" disabled>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-4 padding-5">
    <label class="display-block">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="cusTo" name="cusTo" disabled>
  </div>

  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
			<input type="text" class="form-control input-sm text-center width-50 from-date" name="fromDate" id="fromDate" value="<?php echo date('01-m-Y'); ?>" />
      <input type="text" class="form-control input-sm text-center width-50" name="toDate" id="toDate" value="<?php echo date('t-m-Y'); ?>" />
    </div>
  </div>

  <input type="hidden" id="allCustomer" name="allCustomer" value="1">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>" />
</div>
<hr>
</form>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="rs">

	</div>
</div>




<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered table-striped" style="min-width:1150px;">
    <tr>
      <th colspan="10" class="text-center">รายงานยอดขาย แยกตามลูกค้า แสดงรายการสินค้า </th>
    </tr>
    <tr>
      <th colspan="10" class="text-center">วันที่ {{ reportDate }} (วันที่เปิดบิล) </th>
    </tr>
    <tr>
      <th colspan="10" class="text-center"> ลูกค้า : {{ cusList }} </th>
    </tr>
    <tr class="font-size-12">
      <th class="fix-width-40 middle text-center">#</th>
      <th class="fix-width-100 middle text-center">วันที่</th>
      <th class="min-width-200 middle">ลูกค้า</th>
      <th class="fix-width-120 middle">เลขที่เอกสาร</th>
      <th class="fix-width-150 middle">รหัส</th>
			<th class="fix-width-200 middle">สินค้า</th>
      <th class="fix-width-80 middle text-center">ราคา</th>
      <th class="fix-width-80 middle text-center">ส่วนลด</th>
      <th class="fix-width-80 middle text-right">จำนวน</th>
      <th class="fix-width-100 text-right middle">มูลค่า</th>
    </tr>
{{#each bs}}
  {{#if nodata}}
    <tr>
      <td colspan="10" align="center"><h4>-----  ไม่พบสินค้าคงเหลือตามเงื่อนไขที่กำหนด  -----</h4></td>
    </tr>
  {{else}}
    {{#if @last}}
    <tr class="font-size-14">
      <td colspan="8" class="text-right">รวม</td>
      <td class="text-right">{{ totalQty }}</td>
      <td class="text-right">{{ totalAmount }}</td>
    </tr>
    {{else}}
    <tr class="font-size-12">
      <td class="middle text-center">{{no}}</td>
      <td class="middle text-center">{{ date_upd }}</td>
      <td class="middle">{{ cusName }}</td>
      <td class="middle">{{ reference }}</td>
      <td class="middle">{{ pdCode }}</td>
			<td class="middle">{{ pdName }}</td>
      <td class="middle text-right">{{ price }}</td>
      <td class="middle text-right">{{ discount }}</td>
      <td class="middle text-right">{{ qty }}</td>
      <td class="middle text-right">{{ amount }}</td>
    </tr>
    {{/if}}
  {{/if}}
{{/each}}
  </table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/sales/order_sold_by_customer_and_product.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
