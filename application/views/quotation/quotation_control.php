<div class="row">
	<div class="col-sm-2 col-xs-8 padding-5">
		<label>รุ่นสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="ค้นรุ่นสินค้า" />
  </div>
  <div class="col-sm-1 col-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">OK</label>
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
  </div>

	<div class="divider visible-xs"></div>

  <div class="col-sm-2 col-xs-8 padding-5 margin-bottom-10">
		<label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="ค้นหารหัสสินค้า" autofocus>
  </div>

	<div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label>ราคา</label>
    <input type="number" class="form-control input-sm text-center" id="price">
  </div>

	<div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label>ส่วนลด</label>
    <input type="text" class="form-control input-sm text-center" id="disc">
  </div>

  <div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="qty">
  </div>

  <div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label class="display-block not-show">OK</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItem()">เพิ่ม</button>
  </div>

</div>

<input type="hidden" id="item-name" />
<hr class="margin-top-10 padding-5">
<?php

	if(getConfig('USE_PRODUCT_TAB') == 1)
	{
		if(getConfig('PRODUCT_TAB_TYPE') === 'item')
		{
				$this->load->view('orders/item_tab_menu');
		}
		else
		{
			$this->load->view('orders/order_tab_menu');
		}
	}

?>

<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:250px; max-width:1000px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle" >title</h4>
				<div class="margin-top-10 text-center">
          <label>ส่วนลด</label>
          <input type="text" class="form-control input-sm input-medium text-center inline" id="discountLabel" value="0"/>
        </div>
			 </div>
			 <div class="modal-body text-center" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="insert_item()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>

<form id="orderItemForm">
<div class="modal fade" id="orderItemGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" >
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalItemTitle" >title</h4>
			 </div>
			 <div class="modal-body text-center" id="modalItemBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="insert_item()" >เพิ่มในรายการ</button>
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
