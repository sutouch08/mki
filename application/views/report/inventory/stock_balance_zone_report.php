<?php $this->load->view("include/header"); ?>
<div class="row hidden-print">
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
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-4 padding-5">
    <label class="display-block">สินค้า</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-3 col-sm-2-harf col-xs-4 padding-5">
    <label class="display-block not-show">start</label>
    <input type="text" class="form-control input-sm text-center" id="pdFrom" name="pdFrom" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-2 col-md-3 col-sm-2-harf col-xs-4 padding-5">
    <label class="display-block not-show">End</label>
    <input type="text" class="form-control input-sm text-center" id="pdTo" name="pdTo" placeholder="สิ้นสุด" disabled>
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-4 padding-5">
    <label class="display-block">คลัง</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label class="display-block">ณ วันที่</label>
		<input type="text" class="form-control input-sm text-center" id="date" name="date" readonly value="<?php echo date("d-m-Y"); ?>">
  </div>

	<div class="divider-hidden"></div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-4 padding-5">
		<label>โซน</label>
		<div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-zone-all" onclick="toggleAllZone(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-zone-range" onclick="toggleAllZone(0)">ระบุ</button>
    </div>
	</div>

	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-8 padding-5">
		<label class="display-block not-show">zone</label>
		<input type="text" class="form-control input-sm" id="zone" name="zone" disabled>
	</div>
  <input type="hidden" id="allProduct" name="allProduct" value="1">
  <input type="hidden" id="allWarehouse" name="allWhouse" value="1">
	<input type="hidden" id="allZone" name="allZone" value="1">
	<input type="hidden" id="zoneCode" name="zoneCode" value="">
  <input type="hidden" id="currentDate" name="currentDate" value="1">
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
										<div class="col-sm-12">
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
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="rs">

	</div>
</div>




<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered table-striped">
    <tr>
      <th colspan="7" class="text-center">รายงานสินค้าคงเหลือ ณ วันที่ {{ reportDate }}</th>
    </tr>
    <tr>
      <th colspan="7" class="text-center"> คลัง : {{ whList }} </th>
    </tr>
		<tr>
      <th colspan="7" class="text-center"> โซน : {{ zoneCode }} </th>
    </tr>
    <tr>
      <th colspan="7" class="text-center"> สินค้า : {{ productList }} </th>
    </tr>
    <tr class="font-size-12">
      <th class="fix-width-40 middle text-center">#</th>
			<th class="fix-width-200 middle text-center">โซน</th>
      <th class="fix-width-150 middle text-center">รหัส</th>
      <th class="min-width-200 middle text-center">สินค้า</th>
      <th class="fix-width-100 middle text-right">ทุน</th>
      <th class="fix-width-100 text-right middle">คงเหลือ</th>
      <th class="fix-width-120 text-right middle">มูลค่า</th>
    </tr>
{{#each bs}}
  {{#if nodata}}
    <tr>
      <td colspan="7" align="center"><h4>-----  ไม่พบสินค้าคงเหลือตามเงื่อนไขที่กำหนด  -----</h4></td>
    </tr>
  {{else}}
    {{#if @last}}
    <tr class="font-size-14">
      <td colspan="5" class="text-right">รวม</td>
      <td class="text-right">{{ totalQty }}</td>
      <td class="text-right">{{ totalAmount }}</td>
    </tr>
    {{else}}
    <tr class="font-size-12">
      <td class="middle text-center">{{no}}</td>
      <td class="middle text-center">{{ zone }}</td>
      <td class="middle">{{ pdCode }}</td>
      <td class="middle">{{ pdName }}</td>
      <td class="middle text-right">{{ cost }}</td>
      <td class="middle text-right">{{ qty }}</td>
      <td class="middle text-right">{{ amount }}</td>
    </tr>
    {{/if}}
  {{/if}}
{{/each}}
  </table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/inventory/stock_balance_zone.js"></script>
<?php $this->load->view("include/footer"); ?>
