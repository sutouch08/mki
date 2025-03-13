<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" />
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm edit text-center" id="customer-code" value="" autofocus />
	</div>
	<div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 padding-5">
		<label>ลูกค้า[ในระบบ]</label>
		<input type="text" class="form-control input-sm edit" id="customer-name" name="customer" value="" />
	</div>
	<div class="col-lg-4 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
		<label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="" />
	</div>
	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ตัดรอบออเดอร์</label>
    <select class="width-100 e" id="order-round">
      <option value="">กรุณาเลือก</option>
      <?php echo select_order_round(); ?>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>รอบจัดส่ง</label>
    <select class="width-100 e" id="shipping-round">
      <option value="">กรุณาเลือก</option>
      <?php echo select_shipping_round(); ?>
    </select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่จัดส่ง</label>
    <input type="text" class="width-100 e text-center" id="ship-date" value="<?php echo date('d-m-Y'); ?>" readonly />
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5 hide">
		<label>GP[%]</label>
		<input type="text" class="form-control input-sm text-center edit" name="gp" id="gp" value="0" />
	</div>
	<div class="col-lg-7 col-md-11 col-sm-10-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()">เพิ่ม</button>
	</div>

	<input type="hidden" id="zone_code" value="" />
	<input type="hidden" id="customerCode" value="" />
</div>
<hr class="margin-top-15"/>


<?php if($this->menu_code == 'SOCCSO') : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign.js?v=<?php echo date('Ymd'); ?>"></script>
<?php else : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_tr.js?v=<?php echo date('Ymd'); ?>"></script>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
