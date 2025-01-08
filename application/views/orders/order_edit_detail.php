<?php $this->load->view('include/header'); ?>
<?php
$add = $this->pm->can_add;
$edit = $this->pm->can_edit;
$delete = $this->pm->can_delete;
$hide = $order->status == 1 ? 'hide' : '';
 ?>
 <div class="row">
   <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5" style="padding-top:5px;">
     <h3 class="title"><?php echo $this->title; ?></h3>
   </div>
   <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
     <button type="button" class="btn btn-white btn-warning top-btn" onclick="editOrder('<?php echo $order->code; ?>')"><i class="fa fa-arrow-left"></i> กลับ</button>
     <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
       <button type="button" class="btn btn-white btn-success top-btn <?php echo $hide; ?>" id="btn-save-order" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
     <?php endif; ?>
   </div>
 </div>
<hr class="margin-bottom-15 padding-5" />
<?php $this->load->view('orders/order_edit_header'); ?>

<hr class="padding-5 hide"/>
<div class="row hide">
	<div class="col-lg-9 col-md-9 col-sm-9 hidden-xs">&nbsp;</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>ค่าจัดส่ง</label>
		<input type="number"
		class="form-control input-sm text-right"
		id="shipping-box"
		value="<?php echo $order->shipping_fee; ?>"
		onchange="update_shipping_fee()">
		<input type="hidden" id="current_shipping_fee" value="<?php echo $order->shipping_fee; ?>">
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>ค่าบริการ</label>
		<input type="number"
		class="form-control input-sm text-right"
		id="service-box"
		value="<?php echo $order->service_fee; ?>"
		onchange="update_service_fee()">
		<input type="hidden" id="current_service_fee" value="<?php echo $order->service_fee; ?>">
	</div>
</div>

<hr class="padding-5 margin-bottom-10"/>

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

  <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5 margin-bottom-10">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="ค้นหารหัสสินค้า">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <label>คงเหลือ</label>
    <input type="number" class="form-control input-sm text-center" id="stock-qty" disabled>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="input-qty">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <label class="not-show">Add</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">เพิ่ม</button>
  </div>

	<div class="divider padding-5 visible-xs"></div>

  <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-12 col-lg-offset-1 padding-5">
    <label class="not-show">ส่วนลด</label>
    <button type="button" class="btn btn-xs btn-info btn-block" onclick="recal_discount_rule()">คำนวณส่วนลดใหม่</button>
  </div>
</div>
<hr class="margin-top-15 margin-bottom-0 padding-5" />

<?php
if(getConfig('USE_PRODUCT_TAB'))
{
  $this->load->view('orders/order_tab_menu');
}
?>

<?php $this->load->view('orders/order_detail');  ?>


<form id="orderForm">
  <div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" id="modal" style="min-width:250px; max-width:95vw;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="modalTitle" >title</h4>
        </div>
        <div class="modal-body text-center" >
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="max-height:60vh; padding:0; overflow:auto;" id="modalBody">

            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
          <button type="button" class="btn btn-primary" onClick="addToOrder()" >เพิ่มในรายการ</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form id="orderItemForm">
<div class="modal fade" id="orderItemGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal-item" style="min-width:250px; max-width:1000px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalItemTitle" >title</h4>
			 </div>
			 <div class="modal-body text-center" id="modalItemBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToOrder()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>

<script id="productTabs-template" type="text/x-handlebarsTemplate">
{{#each this}}
<div class="col-sm-2 col-xs-6 padding-0 center">
  <div class="product padding-5">
    <div class="image">
      <a href="javascript:void(0)" onclick="getOrderGrid('{{code}}')">
        <img class="img-responsive border-1" src="{{image}}" />
      </a>
    </div>
    <div class="discription" style="font-size:10px; min-height:50px;">
      <a href="javascript:void(0)" onclick="getOrderGrid('{{code}}')">
        <span class="display-block">{{name}}</span>
        <span>{{price}}</span>
      </a>
    </div>
  </div>
</div>
{{/each}}
</script>

<script id="itemTabs-template" type="text/x-handlebarsTemplate">
{{#each this}}
<div class="col-sm-2 col-xs-6 padding-0 center">
  <div class="product padding-5">
    <div class="image">
      <a href="javascript:void(0)" onclick="getOrderItemGrid('{{code}}')">
        <img class="img-responsive border-1" src="{{image}}" />
      </a>
    </div>
    <div class="discription" style="font-size:10px; min-height:50px;">
      <a href="javascript:void(0)" onclick="getOrderItemGrid('{{code}}')">
        <span class="display-block">{{name}}</span>
        <span>{{price}}</span>
      </a>
    </div>
  </div>
</div>
{{/each}}
</script>

<input type="hidden" id="auz" value="<?php echo getConfig('ALLOW_UNDER_ZERO'); ?>">
<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js"></script>

<?php $this->load->view('include/footer'); ?>
