<?php $this->load->view('include/header'); ?>
<?php if($doc->status == 0) : ?>
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
			<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
    <?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="save()"><i class="fa fa-save"></i> <?php label('save'); ?></button>
    <?php	endif; ?>
    </p>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4">
  	<label><?php label('doc_num'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
		<label><?php label('date'); ?></label>
		<input type="text" class="form-control input-sm text-center edit" id="date-add" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
		<label>วันที่รับ</label>
		<input type="text" class="form-control input-sm text-center edit" name="post_date" id="post-date" value="<?php echo thai_date($doc->posting_date); ?>" readonly disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4">
			<label><?php label('vender_code'); ?></label>
				<input type="text" class="form-control input-sm text-center edit" id="venderCode" value="<?php echo $doc->vender_code; ?>" placeholder="ค้นหารหัสผู้ผลิต" disabled/>
	 </div>
	 <div class="col-lg-4 col-md-5 col-sm-5 col-xs-8">
			<label><?php label('vender_name'); ?></label>
				<input type="text" class="form-control input-sm edit" id="venderName" value="<?php echo $doc->vender_name; ?>" placeholder="ค้นหาชื่อผู้ผลิต" disabled/>
	 </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6">
			<label>ใบสั่งผลิต</label>
			<input type="text" class="form-control input-sm text-center edit" id="poCode" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งผลิต" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6">
		<label><?php label('inv'); ?></label>
		<input type="text" class="form-control input-sm text-center edit" id="invoice" value="<?php echo $doc->invoice_code; ?>" placeholder="อ้างอิงใบส่งสินค้า" disabled/>
	</div>
	<div class="col-lg-3 col-md-3-harf col-sm-4 col-xs-6">
		<label>คลัง</label>
		<select class="form-control input-sm edit" id="warehouse" disabled>
			<option value="">เลือก</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>
	<div class="col-lg-8 col-md-10-harf col-sm-10-harf col-xs-9">
		<label><?php label('remark'); ?></label>
		<input type="text" class="form-control input-sm edit" id="remark" value="<?php echo $doc->remark; ?>" placeholder="ระบุหมายเตุ(ถ้ามี)" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3">
<?php if($this->pm->can_edit && $doc->status == 0) : ?>
		<label class="display-block not-show">edit</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editHeader()">
			<i class="fa fa-pencil"></i> <?php label('edit'); ?>
		</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">
			<i class="fa fa-save"></i> <?php label('update'); ?>
		</button>
<?php endif; ?>
	</div>
	<input type="hidden" name="code" id="code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" name="approver" id="approver" value="" />
</div>

<hr class="margin-top-15"/>
<?php $this->load->view('inventory/receive_po/receive_po_control'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
    	<table class="table table-striped table-bordered" style="min-width:800px;">
        	<thead>
            	<tr class="font-size-12">
              	<th class="fix-width-40 text-center">#</th>
                <th class="fix-width-150 text-center">รหัสสินค้า</th>
                <th class="min-width-200">ชื่อสินค้า</th>
								<th class="fix-width-150">โซน</th>
								<th class="fix-width-100 text-center">Lot.</th>
								<th class="fix-width-100 text-right">ราคา</th>
                <th class="fix-width-100 text-right">จำนวน</th>
								<th class="fix-width-120 text-right">มูลค่า</th>
								<th class="fix-width-40"></th>
              </tr>
            </thead>
            <tbody id="receiveTable">
						<?php if(!empty($details)) : ?>
							<?php $no = 1; ?>
							<?php $total_qty = 0; ?>
							<?php $total_amount = 0; ?>
							<?php foreach($details as $rs) : ?>
								<tr>
									<td class="text-center no"><?php echo $no; ?></td>
									<td class=""><?php echo $rs->product_code; ?></td>
									<td class=""><?php echo $rs->product_name; ?></td>
									<td class=""><?php echo $rs->zone_name; ?></td>
									<td class="text-center"><?php echo empty($rs->receive_date) ? "" : thai_date($rs->receive_date); ?></td>
									<td class="text-right"><?php echo number($rs->price,2); ?></td>
									<td class="text-right"><?php echo number($rs->qty); ?></td>
									<td class="text-right"><?php echo number($rs->amount, 2); ?></td>
									<td class="text-center">
										<?php if($rs->status === 'N') : ?>
											<button type="button" class="btn btn-minier btn-danger" onclick="removeRow(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
												<i class="fa fa-trash"></i>
											</button>
										<?php endif; ?>
									</td>
								</tr>
								<?php $no++; ?>
								<?php $total_qty += $rs->qty; ?>
								<?php $total_amount += $rs->amount; ?>
							<?php endforeach; ?>
							<tr>
								<td colspan="6" class="text-right"><strong><?php label('total'); ?></strong></td>
								<td class="text-right"><strong><?php echo number($total_qty); ?></strong></td>
								<td class="text-right"><strong><?php echo number($total_amount, 2); ?></strong></td>
								<td></td>
							</tr>
						<?php else : ?>
							<tr id="pre_label">
								<td align='center' colspan='9'><h4>-----  ไม่พบรายการ  -----</h4></td>
							</tr>
						<?php endif; ?>
			      </tbody>
        </table>
    </div>
</div>
</form>

<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog input-xlarge">
    <div class="modal-content">
      <div class="modal-header">
      	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
		    <h4 class='modal-title-site text-center' > <?php label('approver'); ?> </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
          	<input type="password" class="form-control input-sm text-center" id="sKey" />
            <span class="help-block red text-center" id="approvError">&nbsp;</span>
          </div>
          <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="doApprove()"><?php label('approve'); ?></button>
          </div>
        </div>
    	 </div>
      </div>
    </div>
</div>


<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>
<?php else : ?>
  <?php redirect($this->home.'/view_detail/'.$doc->code); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_edit.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
