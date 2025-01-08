<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30"/>
<style>
.lbl::before {
	margin-right:10px !important;
}
</style>

<form id="addForm" class="form-horizontal">
  <div class="row">
  	<div class="col-sm-12 col-xs-12 padding-5">
      <div class="form-group">
    		<label class="col-sm-2 col-xs-12 control-label no-padding-right">ชื่อแถบ</label>
    		<div class="col-sm-4 col-xs-8">
    			<input type="text" class="form-control input-sm" name="tab_name" id="tab_name" value="<?php echo $name; ?>">
    		</div>
				<div class="col-sm-1 col-1-harf col-xs-4 padding-5">
					<?php if($this->pm->can_edit) : ?>
		        <button type="button" class="btn btn-xs btn-success btn-block" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
		      <?php endif; ?>
				</div>
    	</div>
    </div>
  </div>
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
	<input type="text" class="hidden">
</form>



<script src="<?php echo base_url(); ?>scripts/masters/product_tab.js"></script>

<?php $this->load->view('include/footer'); ?>
