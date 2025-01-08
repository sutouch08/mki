<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <i class="fa fa-credit-card"></i> <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goToPOS(<?php echo $order->pos_id; ?>)"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<table class="table" style="margin-bottom: 0px; min-height:90vh;">
			<tr>
				<td class="width-60" style="height:100px; border:0px; padding-left:15px; padding-right:15px; padding-bottom:0px;">
					<form class="form-horizontal" role="form">
						<div class="form-group">
							<div class="col-sm-4 col-xs-3 padding-5">
								<input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" readonly />
							</div>
							<div class="col-sm-6 col-xs-5 padding-5">
								<select class="form-control input-sm" id="customer" name="customer" onchange="change_customer()">
									<?php if(!empty($customer_list)) : ?>
										<?php foreach($customer_list as $list) : ?>
											<option value="<?php echo $list->code; ?>" <?php echo is_selected($order->customer_code, $list->code); ?>><?php echo $list->name; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							<div class="col-sm-2 col-xs-4 padding-5">
								<button type="button" class="btn btn-xs btn-primary btn-block" onclick="newCustomer(<?php echo $shop_id; ?>)"><i class="fa fa-plus"></i></button>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-sm-6 col-xs-12 padding-5">
								<select class="form-control input-sm" id="payBy" onchange="changePayment()">
									<?php echo select_pos_payment_method($order->payment_code); ?>
								</select>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12 padding-5">
								<select class="form-control input-sm" id="bank_account" <?php if($order->payment_role != 3 ) { echo 'disabled'; } ?>>
									<option value="">เลือกบัญชี</option>
									<?php echo select_bank_account($order->acc_no); ?>
								</select>
							</div>
						</div>


						<div class="form-group">
							<div class="col-md-4 col-sm-4 col-xs-8 padding-5">
								<input type="text" class="form-control input-sm" name="pd-box" id="pd-box" placeholder="ตรวจสอบราคาสินค้า" />
							</div>
							<div class="col-md-2 col-sm-2 col-xs-4 padding-5">
								<button type="button" class="btn btn-xs btn-primary btn-block" onclick="get_product_data()">ตรวจสอบสินค้า</button>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12 padding-5">
								<input type="text" class="form-control input-sm" name="barcode-box" id="barcode-box" placeholder="บาร์โค้ดหรือรหัสสินค้า"  autofocus>
							</div>
						</div>

					</form>
				</td>

			<?php if(getConfig('USE_PRODUCT_TAB') == 1) : ?>
				<td rowspan="3" class="hidden-xs" style="border:0;">
					<?php
						$this->load->model('masters/product_tab_model');
						$this->load->helper('product_images');
						$this->load->helper('product_tab');

						$this->load->view('pos/pos_item_tab');

					?>
				</td>
			<?php endif; ?>
			</tr>

			<tr>
				<td style="border:0px;">
					<table class="table">
						<thead>
							<tr style="background-color:#f9c4be; font-weight:bold;">
								<td class="width-40 text-center">Items</td>
								<td class="width-15 text-center">Price</td>
								<td class="width-15 text-center">Discount</td>
								<td class="width-10 text-center">Qty</td>
								<td class="width-15 text-center">Subtotal</td>
								<td class="width-5 text-center"><i class="fa fa-trash"></i></td>
							</tr>
						</thead>
						<tbody id="item-table">
							<?php $total_item = 0; ?>
							<?php $total_amount = 0; ?>
							<?php $total_discount = 0; ?>
							<?php $total_tax = 0; ?>
							<?php if(!empty($details)) : ?>
								<?php foreach($details as $rs) : ?>
								<tr id="row-<?php echo $rs->id; ?>">
									<td class="middle" style="padding-left:5px; padding-right:5px;">
										<input type="hidden" class="sell-item" data-id="<?php echo $rs->id; ?>" id="pdCode-<?php echo $rs->id; ?>" value="<?php echo $rs->product_code; ?>">
										<input type="hidden" id="pdName-<?php echo $rs->id; ?>" value="<?php echo $rs->product_name; ?>">
										<input type="hidden" id="taxRate-<?php echo $rs->id; ?>" value="<?php echo $rs->vat_rate; ?>">
										<input type="hidden" id="taxAmount-<?php echo $rs->id; ?>" value="<?php echo $rs->vat_amount; ?>">
										<input type="hidden" id="stdPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->std_price; ?>">
										<input type="hidden" id="sellPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->final_price; ?>">
										<input type="hidden" id="discAmount-<?php echo $rs->id; ?>" value="<?php echo $rs->discount_amount; ?>">
										<input type="hidden" id="unitCode-<?php echo $rs->id; ?>" value="<?php echo $rs->unit_code; ?>">
										<input type="hidden" id="itemType-<?php echo $rs->id; ?>" value="<?php echo $rs->item_type; ?>">
										<input type="hidden" id="currentQty-<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>">
										<input type="hidden" id="currentPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>">
										<input type="hidden" id="currentDisc-<?php echo $rs->id; ?>" value="<?php echo $rs->discount_label; ?>">

										<input type="text" class="form-control input-xs no-border" value="<?php echo $rs->product_name; ?> (<?php echo $rs->product_code; ?>)" />
									</td>
									<td class="middle" style="padding-left:5px; padding-right:5px;">
										<input type="number" class="form-control input-xs text-center no-border" id="price-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>" onchange="updateItem('<?php echo $rs->id; ?>')" onclick="$(this).select();" />
									</td>
									<td class="middle" style="padding-left:5px; padding-right:5px;">
										<input type="text" class="form-control input-xs text-center input-disc no-border" data-id="<?php echo $rs->id; ?>" id="disc-<?php echo $rs->id; ?>" value="<?php echo $rs->discount_label; ?>" onchange="updateItem('<?php echo $rs->id; ?>')" onclick="$(this).select();" />
									</td>
									<td class="middle padding-5" style="padding-left:5px; padding-right:5px;">
										<input type="number" class="form-control input-xs text-center input-qty no-border" data-id="<?php echo $rs->id; ?>" id="qty-<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>" onchange="updateItem('<?php echo $rs->id; ?>')" onclick="$(this).select();"/>
									</td>
									<td id="total-<?php echo $rs->id; ?>" class="middle text-right row-total" data-id="<?php echo $rs->id; ?>" style="padding-left:5px; padding-right:5px;">
										<?php echo number($rs->total_amount, 2); ?>
									</td>
									<td class="middle text-center" style="padding-left:5px; padding-right:5px;">
										<span class="pointer" onclick="removeItem('<?php echo $rs->id; ?>')"><i class="fa fa-trash red"></i></span>
									</td>
								</tr>
								<?php
										$total_item += $rs->qty;
										$total_amount += $rs->total_amount;
										$total_discount += $rs->discount_amount;
										$total_tax += $rs->vat_amount;
										?>
									<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="height:100px; border:0;">
					<table class="table" style="margin-bottom:5px;">
						<tr style="background-color:#d9edf7;">
							<td class="width-25">Total Items</td>
							<td class="width-25 text-right" id="total_item"><?php echo number($total_item,2); ?></td>
							<td class="width-25">Total (After Disc.)</td>
							<td class="width-25 text-right" id="total_amount"><?php echo number($total_amount, 2); ?></td>
						</tr>
						<tr style="background-color:#d9edf7; color:#3c8dbc;">
							<td class="width-25">Discount</td>
							<td class="width-25 text-right" id="total_discount"><?php echo number($total_discount, 2); ?></td>
							<td class="width-25">Tax</td>
							<td class="width-25 text-right" id="total_tax"><?php echo number($total_tax, 2); ?></td>
						</tr>
						<tr style="height:60px; font-size:30px; background-color:black; color:lime;">
							<td colspan="2" class="text-center">Total Payable</td>
							<td colspan="2" class="text-right" id="net_amount"><?php echo number($total_amount, 2); ?></td>
						</tr>
					</table>

					<table class="table" style="margin-bottom:0px;">
						<tr>
							<td class="width-30" style="padding:0px; border:0;">
								<button type="button" class="btn btn-warning btn-lagrg btn-block" onclick="showHoldOption()">Hold</button>
							</td>
							<td class="width-30" style="padding:0px; border:0;">
								<button type="button" class="btn btn-purple btn-lagrg btn-block">Print Order</button>
							</td>
							<td rowspan="2" class="width-40" style="padding:0px; border:0;">
								<button type="button" class="btn btn-success btn-block" style="height:85px;" onclick="showPayment()">Payment</button>
							</td>
						</tr>

						<tr>
							<td class="width-30" style="padding:0px; border:0;">
								<button type="button" class="btn btn-danger btn-lagrg btn-block">Cancel</button>
							</td>
							<td class="width-30" style="padding:0px; border:0;">
								<button type="button" class="btn btn-inverse btn-lagrg btn-block">Print Bill</button>
							</td>

						</tr>

					</table>
				</td>
			</tr>
		</table>
	</div>
</div>

<input type="hidden" id="pos_id" value="<?php echo $id; ?>">
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>">

<input type="hidden" id="channels_code" value="<?php echo $order->channels_code; ?>">
<input type="hidden" id="zone_code" value="<?php echo $zone_code; ?>">
<input type="hidden" id="pos_code" value="<?php echo $order->pos_code; ?>">
<input type="hidden" id="prefix" value="<?php echo $prefix; ?>">
<input type="hidden" id="shop_id" value="<?php echo $order->shop_id; ?>">
<input type="hidden" id="warehouse_code" value="<?php echo $warehouse_code; ?>">
<input type="hidden" id="current_customer" value="<?php echo $order->customer_code; ?>">

<?php $this->load->view('pos/pos_template'); ?>

<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
