<?php
	$add = $this->pm->can_add;
	$edit = $this->pm->can_edit;
	$delete = $this->pm->can_delete;
	?>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
			<table class="table table-striped border-1" style="min-width:1000px;">
				<thead>
					<tr class="font-size-12">
						<th class="fix-width-50 text-center">#</th>
						<th class="fix-width-50 text-center"></th>
						<th class="fix-width-150">รหัสสินค้า</th>
						<th class="min-width-250">ชื่อสินค้า</th>
						<th class="fix-width-100 text-center">ราคา</th>
						<th class="fix-width-100 text-center">จำนวน</th>
						<th class="fix-width-100 text-center">ส่วนลด</th>
						<th class="fix-width-150 text-right">มูลค่า</th>
						<th class="fix-width-50 text-center"></th>
					</tr>
				</thead>
				<tbody id="detail-table">
					<?php   $no = 1;              ?>
					<?php   $total_qty = 0;       ?>
					<?php   $total_discount = 0;  ?>
					<?php   $total_amount = 0;    ?>
					<?php   $order_amount = 0;    ?>
					<?php if(!empty($details)) : ?>
						<?php   foreach($details as $rs) : ?>
							<?php 	$discount = discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
							<?php 	$discLabel = discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
							<tr class="font-size-10" id="row_<?php echo $rs->id; ?>">
								<td class="middle text-center no">
									<?php echo $no; ?>
								</td>

								<td class="middle text-center padding-0">
									<img src="<?php echo get_product_image($rs->product_code, 'mini'); ?>" width="40px" height="40px"  />
								</td>

								<td class="middle pd-code" id="pd-code-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
									<?php echo $rs->product_code; ?>
								</td>

								<td class="middle">
									<?php echo $rs->product_name; ?>
								</td>

								<td class="middle text-center">
									<input type="number"
									class="form-control input-sm text-right price-box digit"
									id="price_<?php echo $rs->id; ?>"
									name="price[<?php echo $rs->id; ?>]"
									data-id="<?php echo $rs->id; ?>"
									value="<?php echo $rs->price; ?>"
									onkeyup="recal(<?php echo $rs->id; ?>)"
									onchange="update_detail(<?php echo $rs->id; ?>)"
									/>
								</td>

								<td class="middle text-center">
									<input type="number"
									class="form-control input-sm text-right qty-box digit"
									id="qty_<?php echo $rs->id; ?>"
									data-id="<?php echo $rs->id; ?>"
									name="qty[<?php echo $rs->id; ?>]"
									data-id="<?php echo $rs->id; ?>"
									value="<?php echo $rs->qty; ?>"
									onkeyup="recal(<?php echo $rs->id; ?>)"
									onchange="update_detail(<?php echo $rs->id; ?>)"
									/>
									<input type="hidden" id="current_qty_<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>" />
								</td>

								<td class="middle text-center">
									<input type="text"
									class="form-control input-sm text-center discount-box row-disc"
									id="disc_<?php echo $rs->id; ?>"
									name="disc[<?php echo $rs->id; ?>]"
									data-id="<?php echo $rs->id; ?>"
									value="<?php echo $discount; ?>"
									onkeyup="recal(<?php echo $rs->id; ?>)"
									onchange="update_detail(<?php echo $rs->id; ?>)"
									/>
								</td>

								<td class="middle text-right">
									<input type="number"
									class="form-control input-sm text-right line-total digit"
									id="line_total_<?php echo $rs->id; ?>"
									data-id="<?php echo $rs->id; ?>"
									name="line_total[<?php echo $rs->id; ?>]"
									data-id="<?php echo $rs->id; ?>"
									value = "<?php echo $rs->total_amount; ?>"
									onkeyup="recalDiscount(<?php echo $rs->id; ?>)"
									onchange="update_detail(<?php echo $rs->id; ?>)"
									/>
								</td>

								<td class="middle text-right">
									<?php if( ( $order->is_paid == 0 && $order->state != 2 && $order->is_expired == 0 ) && ($edit OR $add) && $order->state < 4 ) : ?>
										<button type="button"
										class="btn btn-mini btn-danger"
										onclick="removeDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
										<i class="fa fa-trash"></i></button>
									<?php endif; ?>
								</td>
							</tr>

							<?php			$total_qty += $rs->qty;	?>
							<?php 		$total_discount += $rs->discount_amount; ?>
							<?php 		$order_amount += $rs->qty * $rs->price; ?>
							<?php			$total_amount += $rs->total_amount; ?>
							<?php			$no++; ?>
						<?php   endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="10" class="text-center"><h4>ไม่พบรายการ</h4></td>
						</tr>
					<?php endif; ?>
					<?php  $totalAfDisc = $total_amount; ?>
					<?php 	$netAmount = ( $total_amount - $order->bDiscAmount - $order->deposit ) + $order->shipping_fee + $order->service_fee;	?>

					<tr id="billDisc">
						<td colspan="7" class="middle text-right" style="border-left:solid 1px #CCC;">ส่วนลดท้ายบิล</td>
						<td class="middle">
							<input type="number"
							class="form-control input-sm text-right digit"
							id="billDiscAmount"
							name="billDiscAmount"
							value="<?php echo $order->bDiscAmount; ?>"
							onchange="updateBillDiscAmount()"
							/>
							<!-- total amount after row discount but before bill disc -->
							<input type="hidden" id="totalAfDisc" value="<?php echo $total_amount; ?>" />
							<input type="hidden" id="current_bill_disc_amount" value="<?php $order->bDiscAmount; ?>">
						</td>
						<td class="middle padding-5 text-center"><b>THB.</b></td>
					</tr>

					<tr class="font-size-12">
						<td colspan="6" rowspan="7"></td>
						<td style="border-left:solid 1px #CCC;"><b>จำนวนรวม</b></td>
						<td class="text-right" id="total-qty" style="font-weight:bold;"><b><?php echo number($total_qty,2); ?></b></td>
						<td class="text-center"><b>Pcs.</b></td>
					</tr>
					<tr class="font-size-12">
						<td style="border-left:solid 1px #CCC;"><b>มูลค่ารวม</b></td>
						<td class="text-right" id="total-order" style="font-weight:bold;"><?php echo number($order_amount, 2); ?></td>
						<td class="text-center"><b>THB.</b></td>
					</tr>
					<tr class="font-size-12">
						<td style="border-left:solid 1px #CCC;"><b>ส่วนลดรวม</b></td>
						<td class="text-right" id="total-disc" style="font-weight:bold;">
							- <?php echo number($total_discount + $order->bDiscAmount, 2); ?>
						</td>
						<td class="text-center"><b>THB.</b></td>
					</tr>

					<tr class="font-size-12">
						<td style="border-left:solid 1px #CCC;"><b>ชำระแล้ว</b></td>
						<td class="text-right" id="deposit" style="font-weight:bold;">- <?php echo number($order->deposit, 2); ?></td>
						<td class="text-center"><b>THB.</b></td>
					</tr>
					<tr class="font-size-12">
						<td style="border-left:solid 1px #CCC;"><b>สุทธิ</b></td>
						<td class="text-right" style="font-weight:bold;" id="net-amount"><?php echo number( $netAmount, 2); ?></td>
						<td class="text-center"><b>THB.</b></td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>
<!-- order detail template ------>
<script id="detail-table-template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @last}}
		<tr id="billDisc">
			<td colspan="7" class="middle text-right" style="border-left:solid 1px #CCC;">ส่วนลดท้ายบิล</td>

			<td class="middle">
				<input type="number"
				class="form-control input-sm text-right"
				id="billDiscAmount"
				name="billDiscAmount"
				value="{{bDiscAmount}}"
				onchange="updateBillDiscAmount()"
				/>
				<!-- total amount after row discount but before bill disc -->
				<input type="hidden" id="totalAfDisc" value="{{netAmount}}" />
			</td>
			<td class="middle padding-5 text-center"><b>THB.</b></td>
		</tr>



    <tr class="font-size-12">
    	<td colspan="6" rowspan="7"></td>
      <td style="border-left:solid 1px #CCC;"><b>จำนวนรวม</b></td>
      <td class="text-right" id="total-qty"><b>{{ total_qty }}</b></td>
      <td class="text-center"><b>Pcs.</b></td>
    </tr>

    <tr class="font-size-12">
      <td style="border-left:solid 1px #CCC;"><b>มูลค่ารวม</b></td>
      <td class="text-right" id="total-order"><b>{{ order_amount }}</b></td>
      <td class="text-center"><b>THB.</b></td>
    </tr>

    <tr class="font-size-12">
      <td style="border-left:solid 1px #CCC;"><b>ส่วนลดรวม</b></td>
      <td class="text-right" id="total-disc"><b>{{ total_discount }}</b></td>
      <td class="text-center"><b>THB.</b></td>
    </tr>

		<!-- <tr class="font-size-12">
				<td style="border-left:solid 1px #CCC;"><b>ค่าจัดส่ง</b></td>
				<td class="text-right" id="shipping-fee" style="font-weight:bold;">{{ shipping_fee }}</td>
				<td class="text-center"><b>THB.</b></td>
		</tr>

		<tr class="font-size-12">
				<td style="border-left:solid 1px #CCC;"><b>อื่นๆ</b></td>
				<td class="text-right" id="service-fee" style="font-weight:bold;">{{ service_fee }}</td>
				<td class="text-center"><b>THB.</b></td>
		</tr> -->

		<tr class="font-size-12">
				<td style="border-left:solid 1px #CCC;"><b>ชำระแล้ว</b></td>
				<td class="text-right" id="deposit" style="font-weight:bold;">- {{ deposit }}</td>
				<td class="text-center"><b>THB.</b></td>
		</tr>


    <tr class="font-size-12">
      <td style="border-left:solid 1px #CCC;"><b>สุทธิ</b></td>
      <td class="text-right" id="net-amount"><b>{{ net_amount }}</b></td>
      <td class="text-center"><b>THB.</b></td>
    </tr>
	{{else}}
        <tr class="font-size-10" id="row_{{ id }}">
            <td class="middle text-center">{{ no }}</td>
            <td class="middle text-center padding-0">
            	<img src="{{ imageLink }}" width="40px" height="40px"  />
            </td>
            <td class="middle">{{ productCode }}</td>
            <td class="middle">{{ productName }}</td>
						<td class="middle text-center">
							<input type="number"
							class="form-control input-sm text-right price-box"
							id="price_{{id}}"
							name="price[{{id}}]"
							value="{{price}}"
							onkeyup="recal({{id}})"
							onchange="update_detail({{id}})"
							 />
						</td>
            <td class="middle text-center">
							<input type="number"
							class="form-control input-sm text-right qty-box"
							id="qty_{{id}}"
							name="qty[{{id}}]"
							value="{{qty}}"
							onkeyup="recal({{id}})"
							onchange="update_detail({{id}})"
							 />
						</td>
            <td class="middle text-center">
							<input type="text"
							class="form-control input-sm text-center discount-box"
							id="disc_{{id}}"
							name="disc[{{id}}]"
							value="{{ discount }}"
							onkeyup="recal({{id}})"
							onchange="update_detail({{id}})"
							 />
							</td>
							<td class="middle text-right">
								<input type="number"
								class="form-control input-sm text-right line-total"
								id="line_total_{{id}}"
								data-id="{{id}}"
								name="line_total[{{id}}]"
								value = "{{ amount }}"
								onkeyup="recalDiscount({{id}})"
								onchange="update_detail({{id}})"
								/>
      				</td>

            <td class="middle text-right">
            <?php if( $edit OR $add ) : ?>
            	<button type="button" class="btn btn-xs btn-danger" onclick="removeDetail({{ id }}, '{{ productCode }}')"><i class="fa fa-trash"></i></button>
            <?php endif; ?>
            </td>
        </tr>
	{{/if}}
{{/each}}
</script>

<script id="nodata-template" type="text/x-handlebars-template">
	<tr>
      <td colspan="11" class="text-center"><h4>ไม่พบรายการ</h4></td>
  </tr>
</script>
