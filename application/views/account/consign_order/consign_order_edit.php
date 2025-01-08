<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right top-p">
		<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
			<button type="button" class="btn btn-sm btn-success" onclick="saveConsign()">
				<i class="fa fa-save"></i> บันทึก
			</button>
		<?php endif; ?>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle" aria-expanded="false">
				ตัวเลือก
				<i class="ace-icon fa fa-angle-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right">
				<li>
					<a href="javascript:getSample()"><i class="fa fa-download"></i> Template</a>
				</li>
				<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
					<li>
						<a href="javascript:getUploadFile()"><i class="fa fa-upload"></i> Import excel</a>
					</li>
					<?php if(empty($doc->ref_code)) : ?>
						<li class="hide">
							<a href="javascript:getActiveCheckList()"><i class="fa fa-flash"></i> โหลดเอกสารกระทบยอด</a>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->code; ?>" disabled />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center e" name="date_add" id="date" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled />
	</div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm e" name="customerCode" id="customerCode" value="<?php echo $doc->customer_code; ?>" disabled/>
	</div>
	<div class="col-lg-3-harf col-md-6 col-sm-6 col-xs-6 padding-5">
		<label>ลูกค้า[ในระบบ]</label>
		<input type="text" class="form-control input-sm e" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>โซน</label>
		<input type="text" class="form-control input-sm e" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled/>
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled/>
	</div>

	<div class="col-lg-10-harf col-md-4-harf col-sm-4-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm e" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
	</div>
	<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">Submit</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"></i class="fa fa-pencil"></i> แก้ไข</button>
	    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="getUpdate()"><i class="fa fa-save"></i> บันทึก</button>
		</div>
	<?php endif; ?>

	<input type="hidden" id="consign_code" value="<?php echo $doc->code; ?>">
	<input type="hidden" id="customer_code" value="<?php echo $doc->customer_code; ?>">
	<input type="hidden" id="auz" value="<?php echo $auz; ?>">
	<input type="hidden" id="prev-customer-code" value="<?php echo $doc->customer_code; ?>" />
	<input type="hidden" id="prev-customer-name" value="<?php echo $doc->customer_name; ?>" />
	<input type="hidden" id="prev-zone-code" value="<?php echo $doc->zone_code; ?>" />
	<input type="hidden" id="prev-zone-name" value="<?php echo $doc->zone_name; ?>" />
</div>

<hr class="margin-top-15">



<?php $this->load->view('account/consign_order/consign_order_control'); ?>
<?php $this->load->view('account/consign_order/consign_order_detail'); ?>


<div class="modal fade" id="check-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:400px;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body" id="check-list-body">

			 </div>
			<div class="modal-footer">
			 <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			</div>
	 </div>
 </div>
</div>

<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:350px;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			 <h4 class="modal-title">นำเข้าไฟล์ Excel</h4>
			</div>
			<div class="modal-body">
				<form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-sm-9 col-xs-9 padding-5">
						<button type="button" class="btn btn-sm btn-primary btn-block" id="show-file-name" onclick="getFile()">กรุณาเลือกไฟล์ Excel</button>
					</div>

					<div class="col-sm-3 col-xs-3 padding-5">
						<button type="button" class="btn btn-sm btn-info btn-block" onclick="uploadfile()"><i class="fa fa-cloud-upload"></i> นำเข้า</button>
					</div>
				</div>
				<input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
				<input type="hidden" name="555" />
				</form>
			 </div>
			<div class="modal-footer">

			</div>
	 </div>
 </div>
</div>


<script id="check-list-template" type="text/x-handlebarsTemplate">
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="width-30 text-center">วันที่</th>
					<th class="width-40 text-center">เอกสาร</th>
					<th></th>
				</tr>
			</thead>
			<tbody id="check-list-table">
		 {{#each this}}
			 {{#if nodata}}
				 <tr>
					 <td colspan="3" class="text-center"><h4>ไม่พบรายการ</h4></td>
				 </tr>
			 {{else}}
					<tr>
						<td class="middle text-center">{{date_add}}</td>
						<td class="middle text-center">{{code}}</td>
						<td class="middle text-center">
							<button type="button" class="btn btn-xs btn-info btn-block" onclick="loadCheckDiff('{{code}}')">นำเข้ายอดต่าง</button>
						</td>
					</tr>
				{{/if}}
		 {{/each}}
			</tbody>
		</table>
	</div>
</div>
</script>

<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_control.js"></script>

<?php $this->load->view('include/footer'); ?>
