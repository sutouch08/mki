<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5" style="padding-top:5px;">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right top-p">
		<button type="button" class="btn btn-default btn-white top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-white btn-info top-btn" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>

	<?php if($order->state < 4 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<button type="button" class="btn btn-white btn-yellow top-btn" onclick="editDetail()"><i class="fa fa-pencil"></i> แก้ไขรายการ</button>
		<?php if($order->status == 0) : ?>
	 		<button type="button" class="btn btn-white btn-success top-btn" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
		<?php endif; ?>
	<?php endif; ?>

	<?php if($order->state == 1 && $order->status == 1 && $order->is_approved == 0 && $order->is_expired == 0 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
	<?php endif; ?>
	<?php if($this->pm->can_delete && $order->is_expired == 1) : ?>
		<li><a href="javascript:unExpired()"><i class="fa fa-exclamation"></i> ทำให้ไม่หมดอายุ</a></li>
	<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr/>
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<?php $this->load->view('sponsor/sponsor_edit_header'); ?>
<div class="row" style="margin-left:-7px; margin-right:-7px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1 padding-5">
		<?php $this->load->view('orders/order_state'); ?>
	</div>
</div>
<hr/>

<?php $this->load->view('sponsor/sponsor_view_detail'); ?>

<?php if(!empty($approve_logs)) : ?>
	<div class="row">
		<?php foreach($approve_logs as $logs) : ?>
		<div class="col-sm-12 padding-5 first last">
			<?php if($logs->approve == 1) : ?>
			  <span class="green">
					อนุมัติโดย :
					<?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?>
				</span>
			<?php else : ?>
				<span class="red">
				ยกเลิกโดย :
				<?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?>
			  </span>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
	</div>
<?php endif; ?>

<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
