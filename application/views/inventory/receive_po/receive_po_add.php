<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4">
		<label><?php label('doc_num'); ?></label>
		<input type="text" class="form-control input-sm text-center" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
		<label><?php label('date'); ?></label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="date-add" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
		<label>วันที่รับ</label>
		<input type="text" class="form-control input-sm text-center" name="post_date" id="post-date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4">
		<label><?php label('vender_code'); ?></label>
		<input type="text" class="form-control input-sm text-center" name="venderCode" id="venderCode" placeholder="ค้นหารหัสผู้ขาย"/>
	</div>
	<div class="col-lg-4 col-md-5 col-sm-5 col-xs-8">
		<label><?php label('vender_name'); ?></label>
		<input type="text" class="form-control input-sm" name="venderName" id="venderName" placeholder="ค้นหาชื่อผู้ขาย"/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6">
		<label>ใบสั่งผลิต</label>
		<input type="text" class="form-control input-sm text-center" name="poCode" id="poCode" placeholder="ค้นหาใบสั่งผลิต" />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6">
		<label><?php label('inv'); ?></label>
		<input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" placeholder="อ้างอิงใบส่งสินค้า" />		
	</div>
	<div class="col-lg-2 col-md-3-harf col-sm-4 col-xs-4">
		<label>คลัง</label>
		<select class="form-control input-sm" id="warehouse">
			<option value="">เลือก</option>
			<?php echo select_warehouse(); ?>
		</select>
	</div>
	<div class="col-lg-9 col-md-10-harf col-sm-10-harf col-xs-9">
		<label><?php label('remark'); ?></label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเตุ(ถ้ามี)" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3">
		<label class="display-block not-show"><?php label('save'); ?></label>
		<?php 	if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()">เพิ่ม</button>
		<?php	endif; ?>
	</div>
</div>

<hr class="margin-top-15"/>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
