<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
		<p class="pull-right top-p" >
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<button type="button" class="btn btn-white btn-default top-btn" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
			<?php if($this->pm->can_delete && $order->is_expired == 1) : ?>
				<button type="button" class="btn btn-white btn-warning top-btn" onclick="unExpired()">ทำให้ไม่หมดอายุ</button>
			<?php endif; ?>
			<?php if($order->state < 4 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-white btn-yellow top-btn" onclick="editDetail()"><i class="fa fa-pencil"></i> แก้ไขรายการ</button>
				<?php if($order->status == 0) : ?>
					<button type="button" class="btn btn-white btn-success top-btn" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr/>
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="customerCode" value="<?php echo $order->customer_code; ?>" />
<input type="hidden" id="zone_code" value="<?php echo $order->zone_code; ?>" />

<?php $this->load->view('order_consign/consign_edit_header'); ?>
<div class="row" style="margin-left:-7px; margin-right:-7px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1 padding-5">
		<?php $this->load->view('orders/order_state'); ?>
	</div>
</div>
<hr/>
<?php $this->load->view('order_consign/consign_view_detail'); ?>

<?php if($this->menu_code == 'SOCCSO') : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign.js"></script>
<?php else : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_tr.js"></script>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js"></script>

<?php $this->load->view('include/footer'); ?>
