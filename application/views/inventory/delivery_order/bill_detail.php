<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($order->role == 'S' && $order->state == 8 && empty($order->invoice_code)) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="open_invoice('<?php echo $order->code; ?>')">เปิดใบกำกับ</button>
			<?php endif; ?>
    </p>
  </div>
</div>
<hr/>


<?php
  if( $order->state == 7)
  {
    $this->load->view('inventory/delivery_order/bill_confirm_detail');
  }
  else if( $order->state == 8)
  {    
    $this->load->view('inventory/delivery_order/bill_closed_detail');
  }
  else
  {
    $this->load->view('inventory/delivery_order/invalid_state');
  }
?>

<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
