<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5">
    	<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div>
<hr class="padding-5" />

<div class="row">
<div class="col-sm-12 col-xs-12 padding-5" style="padding-top:15px;">
	<div class="tabbable">
		<ul id="myTab1" class="nav nav-tabs">
		  <!-- <li class="li-block"><a href="#general" data-toggle="tab">ทั่วไป</a></li>-->
			<li class="active"><a href="#company" data-toggle="tab">บริษัท</a></li>
			<li class=""><a href="#inventory" data-toggle="tab">คลังสินค้า</a></li>
		  <li class=""><a href="#order" data-toggle="tab">ออเดอร์</a></li>
			<li class=""><a href="#document" data-toggle="tab">เอกสาร</a></li>
	<?php if($USE_POS) : ?>
		  <li class=""><a href="#pos" data-toggle="tab">POS</a></li>
	<?php endif; ?>
	<?php if($this->_SuperAdmin) : ?>
			<li class=""><a href="#system" data-toggle="tab">ระบบ</a></li>
			<li class=""><a href="#menu" data-toggle="tab">เมนู</a></li>
	<?php endif; ?>
			<!--
			<li class="li-block"><a href="#bookcode" data-toggle="tab">เล่มเอกสาร</a></li>
		-->
		</ul>

		<div class="tab-content width-100" style="min-height:600px;">
		<!---  ตั้งค่าทั่วไป  ----------------------------------------------------->
		<?php //$this->load->view('setting/setting_general'); ?>

		<!---  ตั้งค่าบริษัท  ------------------------------------------------------>
		<?php $this->load->view('setting/setting_company'); ?>

		<!---  ตั้งค่าระบบ  ----------------------------------------------------->
		<?php
			if($this->_SuperAdmin)
			{
				$this->load->view('setting/setting_system');
				$this->load->view('setting/setting_menu');
			}

			?>

		<!---  ตั้งค่าออเดอร์  --------------------------------------------------->
		<?php $this->load->view('setting/setting_order'); ?>


		<!---  ตั้งค่า POS --------------------------------------------------->
		<?php
			if($USE_POS)
			{
				$this->load->view('setting/setting_pos');
			}
		 ?>

		<!---  ตั้งค่าเอกสาร  --------------------------------------------------->
		<?php $this->load->view('setting/setting_document'); ?>

		<?php // $this->load->view('setting/setting_bookcode'); ?>

		<?php $this->load->view('setting/setting_inventory'); ?>

		</div>
	</div>

</div>
</div><!--/ row  -->


<script src="<?php echo base_url(); ?>scripts/setting/setting.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/setting/setting_document.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
