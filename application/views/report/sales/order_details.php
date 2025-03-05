<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-6 col-md-6 col-sm-7 col-xs-12 padding-5">
		<h3 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-5 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-4 padding-5">
    <label class="display-block">เลขที่</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-code-all" onclick="toggleAllCode(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-code-range" onclick="toggleAllCode(0)">ระบุ</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-4 padding-5">
    <label class="display-block">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="codeFrom" name="codeFrom" disabled>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-4 padding-5">
    <label class="display-block">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="codeTo" name="codeTo" disabled>
  </div>

  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm text-center width-50 from-date" name="fromDate" id="fromDate" value="<?php echo date('01-m-Y'); ?>" />
      <input type="text" class="form-control input-sm text-center width-50" name="toDate" id="toDate" value="<?php echo date('t-m-Y'); ?>" />
    </div>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" id="channels" name="channels">
			<option value="all">ทั้งหมด</option>
			<?php echo select_channels(); ?>
		</select>
  </div>

  <input type="hidden" id="allCode" name="allCode" value="1">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</div>
</form>
<hr class="padding-5">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-40 text-center">Date</th>
					<th class="fix-width-40 text-center">Order No.</th>
					<th class="fix-width-40 text-center">Item Code</th>
					<th class="fix-width-40 text-center">Item Name</th>
					<th class="fix-width-40 text-center">Item Group</th>
					<th class="fix-width-40 text-center">Item Category</th>
					<th class="fix-width-40 text-center">Item Kind</th>
					<th class="fix-width-40 text-center">Item Type</th>
					<th class="fix-width-40 text-center"></th>
				</tr>
			</thead>
		</table>
	</div>
</div>


<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered table-striped">
    <tr>
      <th colspan="6" class="text-center">รายงานยอดขาย แยกตามสินค้า </th>
    </tr>
    <tr>
      <th colspan="6" class="text-center">วันที่ {{ reportDate }} (วันที่เปิดบิล) </th>
    </tr>
    <tr>
      <th colspan="6" class="text-center"> สินค้า : {{ pdList }} </th>
    </tr>
    <tr class="font-size-12">
      <th class="width-5 middle text-center">ลำดับ</th>
      <th class="width-10 middle">รหัส</th>
      <th class="middle">สินค้า</th>
      <th class="width-15 middle text-center">ราคา(vat exclude)</th>
      <th class="width-15 middle text-right">จำนวน</th>
      <th class="width-15 text-right middle">มูลค่า(vat exclude)</th>
    </tr>
{{#each bs}}
  {{#if nodata}}
    <tr>
      <td colspan="6" align="center"><h4>-----  ไม่พบสินค้าคงเหลือตามเงื่อนไขที่กำหนด  -----</h4></td>
    </tr>
  {{else}}
    {{#if @last}}
    <tr class="font-size-14">
      <td colspan="4" class="text-right">รวม</td>
      <td class="text-right">{{ totalQty }}</td>
      <td class="text-right">{{ totalAmount }}</td>
    </tr>
    {{else}}
    <tr class="font-size-12">
      <td class="middle text-center">{{no}}</td>
      <td class="middle">{{ pdCode }}</td>
      <td class="middle">{{ pdName }}</td>
      <td class="middle text-right">{{ price }}</td>
      <td class="middle text-right">{{ qty }}</td>
      <td class="middle text-right">{{ amount }}</td>
    </tr>
    {{/if}}
  {{/if}}
{{/each}}
  </table>
</script>

<script type="text/javascript">
	function toggleAllCode(option) {
		$('#allCode').val(option);

		if(option == 1) {
			$('#codeFrom').val('').attr('disabled', 'disabled');
			$('#codeTo').val('').attr('disabled', 'disabled');
			$('#btn-code-all').addClass('btn-primary');
			$('#btn-code-range').removeClass('btn-primary');
		}

		if(option == 0) {
			$('#codeFrom').val('').removeAttr('disabled');
			$('#codeTo').val('').removeAttr('disabled');
			$('#btn-code-all').removeClass('btn-primary');
			$('#btn-code-range').addClass('btn-primary');
			$('#codeFrom').focus();
		}
	}

	$('#codeFrom').autocomplete({
		source:BASE_URL + 'auto_complete/get_order_code',
		autoFocus:true,
		close:function() {
			let from = $('#codeFrom').val();
			let to = $('#codeTo').val();

			if(to != "" && to < from) {
				$('#codeFrom').val(to);
				$('#codeTo').val(from);
			}

			$('#codeTo').focus();
		}
	});

	$('#codeTo').autocomplete({
		source:BASE_URL + 'auto_complete/get_order_code',
		autoFocus:true,
		close:function() {
			let from = $('#codeFrom').val();
			let to = $('#codeTo').val();

			if(to != "" && to < from) {
				$('#codeFrom').val(to);
				$('#codeTo').val(from);
			}
		}
	});
</script>
<?php $this->load->view('include/footer'); ?>
