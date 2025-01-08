<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs">
		<h3 class="title">
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-xs-12 visible-xs">
		<h3 class="title-xs">
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
					<button type="button" class="btn btn-sm btn-success" onclick="save()">
		        <i class="fa fa-save"></i> บันทึก
		      </button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="editForm" method="post" action="<?php echo $this->home; ?>/update">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ชำระโดย</label>
    <select class="form-control input-sm edit" name="pay_type" id="pay_type" required disabled>
			<option value="">โปรดเลือก</option>
			<?php echo select_payment_type($doc->pay_type); ?>
		</select>
  </div>

  <div class="col-lg-3 col-md-3 col-sm-6-harf col-xs-6 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
  </div>

	<div class="col-lg-3 col-md-3 col-sm-10-harf col-xs-6 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6">
    <label class="display-block not-show">Submit</label>
  <?php if($this->pm->can_edit && $doc->status == 0) : ?>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"></i class="fa fa-pencil"></i> แก้ไข</button>
    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
  <?php endif; ?>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="repay_code" id="repay_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
</form>
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
    <button type="button" class="btn btn-sm btn-primary btn-block" onclick="getOrderCreditList()">
      แสดรายการ
    </button>
  </div>
</div>
<hr/>
<?php $this->load->view('account/order_repay/order_repay_control'); ?>

<div class="modal fade" id="check-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:600px; max-width:90vw;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body" id="check-list-body">
				<form id="check-form">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="max-height:500px; overflow:auto;">
						<table class="table border-1" style="min-width:580px; margin-bottom:0;">
							<thead>
								<tr>
									<th class="fix-width-40"></th>
									<th class="min-width-120">เลขที่</th>
									<th class="fix-width-100">วันที่</th>
									<th class="fix-width-100">ครบกำหนด</th>
									<th class="fix-width-120 text-right">มูลค่า</th>
									<th class="fix-width-120 text-right">ค้างชำระ</th>
								</tr>
							</thead>
							<tbody id="check-list-table">

							</tbody>
						</table>
					</div>
				</div>
				</form>
			 </div>
			<div class="modal-footer">
			 <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">ปิด</button>
			 <button type="button" class="btn btn-sm btn-primary" onclick="addToList()">เลือก</button>
			</div>
	 </div>
 </div>
</div>


<script id="check-list-template" type="text/x-handlebarsTemplate">
 {{#each this}}
	 {{#if nodata}}
		 <tr>
			 <td colspan="6" class="text-center"><h4>ไม่พบรายการ</h4></td>
		 </tr>
	 {{else}}
			<tr>
				<td class="middle text-center">
					<input type="checkbox" name="order_credit[{{id}}]" id="check-{{id}}" class="ace chk-order" value="{{id}}">
					<span class="lbl"></span>
				</td>
				<td class="middle"><label for="check-{{id}}">{{order_code}}</label></td>
				<td class="middle">{{delivery_date}}</td>
				<td class="middle">{{due_date}}</td>
				<td class="middle text-right">{{amount}}</td>
				<td class="middle text-right">{{balance}}</td>
			</tr>
		{{/if}}
 {{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay_control.js"></script>

<?php $this->load->view('include/footer'); ?>
