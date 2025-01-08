<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-6 col-md-6 col-sm-7 hidden-xs padding-5">
		<h4 class="title">
			<i class="fa fa-bar-chart"></i>
			<?php echo $this->title; ?>
		</h4>
	</div>
	<div class="col-xs-12 visible-xs padding-5">
		<h3 class="title-xs">
			<i class="fa fa-bar-chart"></i>
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-5 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
		<label class="display-block">รูปแบบ</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-33" id="btn-all" onclick="toggleRole('all')">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-33" id="btn-sale" onclick="toggleRole('S')">ขาย</button>
			<button type="button" class="btn btn-sm width-33" id="btn-consign" onclick="toggleRole('M')">ฝากขาย</button>
    </div>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label class="display-block">วันที่</label>
		<div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm text-center width-50 from-date" name="fromDate" id="fromDate" value="<?php echo date('01-m-Y'); ?>" />
      <input type="text" class="form-control input-sm text-center width-50" name="toDate" id="toDate" value="<?php echo date('t-m-Y'); ?>" />
    </div>
  </div>
</div>
<hr class="padding-5">

	<input type="hidden" id="role" name="role" value="all" />
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>" />

</form>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5" id="rs">
		<blockquote>
      <p class="lead" style="color:#CCC;">
        รายงานจะไม่แสดงข้อมูลทางหน้าจอ เนื่องจากข้อมูลมีจำนวนคอลัมภ์ที่ยาวเกินกว่าที่จะแสดงผลทางหน้าจอได้ทั้งหมด กรุณา export ข้อมูลเป็นไฟล์ Excel แทน
      </p>
    </blockquote>
    </div>
</div>

<script src="<?php echo base_url(); ?>scripts/report/sales/order_sold_details.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
