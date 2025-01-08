<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 paddig-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
		<button type="button" class="btn btn-white btn-info top-btn" onclick="viewDetail(<?php echo $doc->id; ?>)">ตรวจสอบรายการ</button>
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goEdit(<?php echo $doc->id; ?>)">แก้ไขรายการ</button>		
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
		เลขที่ : <?php echo $doc->code; ?>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
		หัวข้อ : <?php echo $doc->subject; ?>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		สถานที่ : <?php echo $doc->zone_code .'&nbsp;&nbsp; : &nbsp;&nbsp;'.$doc->zone_name; ?>
	</div>
	<input type="hidden" id="check_id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="allow_input_qty" value="<?php echo $doc->allow_input_qty; ?>" />
	<input type="hidden" id="row-no" value="1" />
</div>
<hr class="margin-top-15 margin-bottom-15">

<?php $this->load->view('inventory/check/check_control'); ?>

<?php $this->load->view('inventory/check/check_details'); ?>

<?php $this->load->view('inventory/check/check_template'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/check/check.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/check/check_control.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
