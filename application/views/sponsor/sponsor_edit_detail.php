<?php $this->load->view('include/header'); ?>
<?php
$add = $this->pm->can_add;
$edit = $this->pm->can_edit;
$delete = $this->pm->can_delete;
$hide = $order->status == 1 ? 'hide' : '';
 ?>
 <div class="row">
   <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5" style="padding-top:5px">
     <h3 class="title"><?php echo $this->title; ?></h3>
   </div>
   <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right top-p">
     <button type="button" class="btn btn-white btn-warning top-btn" onClick="editOrder('<?php echo $order->code; ?>')"><i class="fa fa-arrow-left"></i> กลับ</button>
     <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
       <button type="button" class="btn btn-white btn-success top-btn <?php echo $hide; ?>" id="btn-save-order" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
     <?php endif; ?>
   </div>
 </div>
<hr class="margin-bottom-15" />
<?php $this->load->view('sponsor/sponsor_edit_header'); ?>

<!--  Search Product -->
<div class="row">
	<div class="divider padding-5 visible-xs"></div>
	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-8 padding-5 margin-bottom-10">
    <label>รุ่นสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="ค้นรหัสสินค้า" />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
    <label class="not-show">รุ่นสินค้า</label>
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">แสดงสินค้า</button>
  </div>

	<div class="divider padding-5 visible-xs"></div>

  <div class="col-lg-2-harf col-lg-offset-1 col-md-3 col-md-offset-1 col-sm-3 col-sm-offset-1 col-xs-12 padding-5 margin-bottom-10">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="ค้นหารหัสสินค้า">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
    <label>คงเหลือ</label>
    <input type="number" class="form-control input-sm text-center" id="stock-qty" disabled>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="input-qty">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5 margin-bottom-10">
    <label class="not-show">add</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">เพิ่ม</button>
  </div>

  <input type="hidden" id="auz" value="<?php echo getConfig('ALLOW_UNDER_ZERO'); ?>">
</div>
<hr class="margin-top-15 margin-bottom-0 padding-5" />

<?php
  if(getConfig('USE_PRODUCT_TAB'))
  {
    $this->load->view('orders/order_tab_menu');
  }
?>

<?php $this->load->view('sponsor/sponsor_detail');  ?>


<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToOrder()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>

<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
