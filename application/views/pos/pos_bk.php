<?php $this->load->view('include/pos/pos_header'); ?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<form class="form-horizontal" role="form">
			<div class="form-group">
				<div class="col-sm-10 col-xs-8 padding-right-5">
					<select class="form-control input-sm" id="customer" name="customer">
						<?php if(!empty($customer_list)) : ?>
							<?php foreach($customer_list as $list) : ?>
								<option value="<?php echo $list->code; ?>" <?php echo is_selected($customer_code, $list->code); ?>><?php echo $list->name; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="col-sm-2 col-xs-4 padding-left-5">
					<button type="button" class="btn btn-xs btn-primary btn-block" onclick="newCustomer(<?php echo $shop_id; ?>)"><i class="fa fa-plus"></i></button>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-12 col-xs-12">
					<input type="text" class="form-control input-sm" name="pd-box" id="pd-box" placeholder="ค้นหาสินค้าด้วย รหัสหรือชื่อสินค้า" />
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-12 col-xs-12">
					<input type="text" class="form-control input-sm" name="barcode-box" id="barcode-box" placeholder="บาร์โค้ดสินค้า" autofocus>
				</div>
			</div>
		</form>

		<table class="table">
			<thead>
				<tr style="background-color:#f9c4be; font-weight:bold;">
					<td class="width-40 text-center">Product</td>
					<td class="width-15 text-center">Price</td>
					<td class="width-15 text-center">Qty</td>
					<td class="width-20 text-center">Subtotal</td>
					<td class="width-10 text-center"><i class="fa fa-trash"></i></td>
				</tr>
			</thead>
			<tbody id="item-table">

			</tbody>
		</table>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5" style="min-height:100%;">
		<?php

		if(getConfig('USE_PRODUCT_TAB') == 1)
		{
			$this->load->model('masters/product_tab_model');
			$this->load->helper('product_images');
			$this->load->helper('product_tab');

			if(getConfig('PRODUCT_TAB_TYPE') === 'item')
			{
				$this->load->view('pos/pos_item_tab');
			}
			else
			{
				$this->load->view('orders/order_tab_menu');
			}
		}

		?>
	</div>

</div>


<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/pos/pos_footer'); ?>
