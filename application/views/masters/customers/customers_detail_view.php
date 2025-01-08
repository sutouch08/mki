<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-0"/>
<?php
$tab1 = $tab == 'infoTab' ? 'active in' : '';
$tab2 = $tab == 'billTab' ? 'active in' : '';
$tab3 = $tab == 'shipTab' ? 'active in' : '';
?>


<style>

@media (min-width: 768px){

	#content-block {
		 border-left:solid 1px #ccc;
	}

}
</style>


<div class="row">
<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 padding-top-15 hidden-xs">
	<ul id="myTab1" class="setting-tabs width-100" style="margin-left:0px;">
	  <li class="li-block <?php echo $tab1; ?>" onclick="changeView('<?php echo $ds->code; ?>','infoTab')" >
			<a href="#infoTab" data-toggle="tab" style="text-decoration:none;">ข้อมูลลูกค้า</a>
		</li>
		<li class="li-block <?php echo $tab2; ?>" onclick="changeView('<?php echo $ds->code; ?>','billTab')" >
			<a href="#billTab" data-toggle="tab" style="text-decoration:none;">ที่อยู่เปิดบิล</a>
		</li>
		<li class="li-block <?php echo $tab3; ?>" onclick="changeView('<?php echo $ds->code; ?>','shipTab')" >
			<a href="#shipTab" data-toggle="tab" style="text-decoration:none;" >ที่อยู่จัดส่ง</a>
		</li>
	</ul>
</div>

<div class="col-xs-12 padding-5 visible-xs">
	<ul id="myTab1" class="setting-tabs width-100" style="margin-left:0px;">
	  <li class="li-block inline border-1 <?php echo $tab1; ?>" onclick="changeView('<?php echo $ds->code; ?>','infoTab')" >
			<a href="#infoTab" data-toggle="tab" style="text-decoration:none;">ข้อมูลลูกค้า</a>
		</li>
		<li class="li-block inline border-1 <?php echo $tab2; ?>" onclick="changeView('<?php echo $ds->code; ?>','billTab')" >
			<a href="#billTab" data-toggle="tab" style="text-decoration:none;">ที่อยู่เปิดบิล</a>
		</li>
		<li class="li-block inline border-1 <?php echo $tab3; ?>" onclick="changeView('<?php echo $ds->code; ?>','shipTab')" >
			<a href="#shipTab" data-toggle="tab" style="text-decoration:none;" >ที่อยู่จัดส่ง</a>
		</li>
	</ul>
</div>

<div class="divider visible-xs" style="margin-bottom:0px;"></div>

<div class="col-lg-10-harf col-md-10 col-sm-10 col-xs-12 padding-5" id="content-block" style="min-height:600px; ">
<div class="tab-content" style="border:0">
	<div class="tab-pane fade <?php echo $tab1; ?>" id="infoTab">
		<?php $this->load->view('masters/customers/customers_view_info'); ?>
	</div>
	<div class="tab-pane fade <?php echo $tab2; ?>" id="billTab">
		<?php $this->load->view('masters/customers/customers_view_bill_to'); ?>
	</div>
	<div class="tab-pane fade <?php echo $tab3; ?>" id="shipTab">
		<?php $this->load->view('masters/customers/customers_view_ship_to'); ?>
	</div>
</div>
</div><!--/ col-sm-9  -->
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/customers.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/masters/address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/masters/customer_address.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
