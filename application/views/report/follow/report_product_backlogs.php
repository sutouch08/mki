<?php $this->load->view("include/header"); ?>
<div class="row hidden-print">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
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
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>			
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-5 padding-5">
		<label class="display-block">สินค้า</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">เลือก</button>
    </div>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3-harf padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="fromProduct" name="fromProduct" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3-harf padding-5">
    <label class="display-block not-show">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="toProduct" name="toProduct" placeholder="สิ้นสุด" disabled>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-5 padding-5">
		<label class="display-block">ลูกค้า</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-cust-all" onclick="toggleAllCustomer(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-cust-range" onclick="toggleAllCustomer(0)">เลือก</button>
    </div>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3-harf padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="fromCustomer" name="fromCustomer" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3-harf padding-5">
    <label class="display-block not-show">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="toCustomer" name="toCustomer" placeholder="สิ้นสุด" disabled>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-5 padding-5">
		<label class="display-block">วันที่</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-date-all" onclick="toggleAllDate(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-date-range" onclick="toggleAllDate(0)">เลือก</button>
    </div>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-7 padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
		<div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" disabled />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" disabled/>
    </div>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label class="display-block">ช่องทางขาย</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-ch-all" onclick="toggleAllChannels(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-ch-range" onclick="toggleAllChannels(0)">เลือก</button>
    </div>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label class="display-block">การชำระเงิน</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pm-all" onclick="toggleAllPayment(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pm-range" onclick="toggleAllPayment(0)">เลือก</button>
    </div>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label class="display-block">สินค้าไม่นับสต็อก</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-count-yes" onclick="toggleIsCount(1)">รวม</button>
      <button type="button" class="btn btn-sm width-50" id="btn-count-no" onclick="toggleIsCount(0)">ไม่รวม</button>
    </div>
	</div>

</div>
<hr class="padding-5">
	<input type="hidden" id="allProduct" name="allProduct" value="1" />
	<input type="hidden" id="allCustomer" name="allCustomer" value="1" />
	<input type="hidden" id="allDate" name="allDate" value="1" />
	<input type="hidden" id="allChannels" name="allChannels" value="1">
	<input type="hidden" id="allPayment" name="allPayment" value="1">
	<input type="hidden" id="isCount" name="isCount" value="1" />
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>" />


	<div class="modal fade" id="channels-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" id="modal" style="width:500px; max-width:95vw;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="title">ช่องทางการขาย</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<?php if(!empty($channelsList)) : ?>
							<?php foreach($channelsList as $rs) : ?>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<label>
										<input type="checkbox" class="chk" id="<?php echo $rs->code; ?>" name="channels[]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
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



	<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" id="pm-modal" style="width:500px; max-width:95vw;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="title">ช่องทางการชำระเงิน</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<?php if(!empty($paymentList)) : ?>
							<?php foreach($paymentList as $rs) : ?>
								<div class="col-sm-12">
									<label>
										<input type="checkbox" class="pm" id="<?php echo $rs->code; ?>" name="payment[]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
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


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="rs">

	</div>
</div>



<script id="template" type="text/x-handlebars-template">
  <table class="table border-1 visible-print">
		<tr>
			<td colspan="3" class="text-center">รายงานสินค้าค้างส่ง วันที่ {{reportDate}}</td>
		</tr>
		<tr>
			<td>สินค้า : {{custList}} ({{isCount}})</td>
			<td>ลูกค้า : {{custList}}</td>
			<td>วันที่ : {{dateList}}</td>
		</tr>
		<tr>
			<td>ช่องทางขาย : {{channelsList}}</td>
			<td>ช่องทางการชำระเงิน : {{paymentList}}</td>
			<td></td>
		</tr>
	</table>
	<table class="table table-bordered table-striped">
		<thead>
			<tr class="font-size-12">
	      <th class="fix-width-40 middle text-center">#</th>
	      <th class="width-20 middle">สินค้า</th>
				<th class="width-10 middle">ออเดอร์</th>
	      <th class="width-15">ลูกค้า</th>
				<th class="width-10 middle">ช่องทาง</th>
				<th class="width-10 middle">การชำระเงิน</th>
				<th class="width-10 middle">สถานะ</th>
	      <th class="width-10 middle text-right">จำนวน</th>
				<th class="width-10 middle text-right">มูลค่า</th>
	    </tr>
		</thead>
	<tbody>
	{{#each data}}
	  {{#if nodata}}
	    <tr>
	      <td colspan="9" align="center"><h4>-----  ไม่พบรายการตามเงื่อนไขที่กำหนด  -----</h4></td>
	    </tr>
	  {{else}}
	    {{#if @last}}
	    <tr class="font-size-14">
	      <td colspan="7" class="text-right"><b>รวม</b></td>
				<td class="text-right"><b>{{ totalQty }}</b></td>
	      <td class="text-right"><b>{{ totalAmount }}</b></td>
	    </tr>
	    {{else}}
	    <tr class="font-size-12">
	      <td class="middle text-center">{{no}}</td>
	      <td class="middle">{{ item }}</td>
	      <td class="middle">{{ order }}</td>
				<td class="middle">{{ customer }}</td>
	      <td class="middle">{{ channels }}</td>
				<td class="middle">{{ payment }}</td>
				<td class="middle">{{status}}</td>
				<td class="middle text-right">{{ qty }}</td>
				<td class="middle text-right">{{ amount }}</td>
	    </tr>
	    {{/if}}
	  {{/if}}
	{{/each}}
	</tbody>
</table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/follow/product_backlogs.js?v=<?php echo date("YmdH"); ?>"></script>
<?php $this->load->view("include/footer"); ?>
