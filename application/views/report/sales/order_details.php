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

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-4 padding-5">
    <label class="display-block">ช่องทางขาย</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-ch-all" onclick="toggleAllChannels(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-ch-range" onclick="toggleAllChannels(0)">ระบุ</button>
    </div>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-4 padding-5">
    <label class="display-block">การชำระเงิน</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pm-all" onclick="toggleAllPayments(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pm-range" onclick="toggleAllPayments(0)">ระบุ</button>
    </div>
  </div>
  <input type="hidden" id="allCode" value="1">
	<input type="hidden" id="allChannels" value="1">
	<input type="hidden" id="allPayments" value="1">
</div>
<hr class="padding-5">

<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<input type="hidden" id="data" name="data" value="" />
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</form>

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


<div class="modal fade" id="channels-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" id="modal" style="width:500px; max-width:95vw;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title">ช่องทงขาย</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin-left:0;">
					<?php if(!empty($channels)) : ?>
						<?php $no = 0; ?>
						<?php foreach($channels as $rs) : ?>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<label>
									<input type="checkbox" class="ace ch" data-name="<?php echo $rs->name; ?>" value="<?php echo $rs->code; ?>" />
									<span class="lbl">&nbsp;&nbsp;&nbsp;  <?php echo $rs->code; ?> | <?php echo $rs->name; ?></span>
								</label>
							</div>
							<?php $no++; ?>
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

<div class="modal fade" id="payments-modal" tabindex="-1" role="dialog" aria-labelledby="payment" >
	<div class="modal-dialog" style="width:500px; max-width:95vw;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title">การชำระเงิน</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin-left:0;">
					<?php if( ! empty($payments)) : ?>
						<?php $no = 0; ?>
						<?php foreach($payments as $rs) : ?>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<label>
									<input type="checkbox" class="ace pm" data-name="<?php echo $rs->name; ?>" value="<?php echo $rs->code; ?>" />
									<span class="lbl">&nbsp;&nbsp;&nbsp;  <?php echo $rs->code; ?> | <?php echo $rs->name; ?></span>
								</label>
							</div>
							<?php $no++; ?>
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

	function toggleAllChannels(option) {
		$('#allChannels').val(option);

		if(option == 1){
	    $('#btn-ch-all').addClass('btn-primary');
	    $('#btn-ch-range').removeClass('btn-primary');
	    return
	  }

	  if(option == 0){
	    $('#btn-ch-all').removeClass('btn-primary');
	    $('#btn-ch-range').addClass('btn-primary');
	    $('#channels-modal').modal('show');
	  }
	}

	function toggleAllPayments(option) {
		$('#allPayments').val(option);

		if(option == 1){
	    $('#btn-pm-all').addClass('btn-primary');
	    $('#btn-pm-range').removeClass('btn-primary');
	    return
	  }

	  if(option == 0){
	    $('#btn-pm-all').removeClass('btn-primary');
	    $('#btn-pm-range').addClass('btn-primary');
	    $('#payments-modal').modal('show');
	  }
	}

</script>
<?php $this->load->view('include/footer'); ?>
