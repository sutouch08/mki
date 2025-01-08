<script id="row-template" type="text/x-handlebarsTemplate">
<tr id="row-{{id}}">
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="hidden" class="sell-item" data-id="{{id}}" id="pdCode-{{id}}" value="{{product_code}}">
		<input type="hidden" id="pdName-{{id}}" value="{{product_name}}">
		<input type="hidden" id="taxRate-{{id}}" value="{{vat_rate}}">
		<input type="hidden" id="taxAmount-{{id}}" value="{{vat_amount}}">
		<input type="hidden" id="stdPrice-{{id}}" value="{{std_price}}">
		<input type="hidden" id="sellPrice-{{id}}" value="{{final_price}}">
		<input type="hidden" id="discAmount-{{id}}" value="{{discount_amount}}">
		<input type="hidden" id="unitCode-{{id}}" value="{{unit_code}}">
		<input type="hidden" id="itemType-{{id}}" value="{{item_type}}">
		<input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
		<input type="hidden" id="currentPrice-{{id}}" value="{{price}}">
		<input type="hidden" id="currentDisc-{{id}}" value="{{discount_label}}">

		<input type="text" class="form-control input-xs no-border" value="{{product_name}} ({{product_code}})" />
	</td>
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="number" class="form-control input-xs text-center no-border" id="price-{{id}}" value="{{price}}" onchange="updateItem('{{id}}')" onclick="$(this).select();" />
	</td>
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="text" class="form-control input-xs text-center input-disc no-border" data-id="{{id}}" id="disc-{{id}}" value="{{discount_label}}" onchange="updateItem('{{id}}')" onclick="$(this).select();" />
	</td>
	<td class="middle padding-5" style="padding-left:5px; padding-right:5px;">
		<input type="number" class="form-control input-xs text-center input-qty no-border" data-id="{{id}}" id="qty-{{id}}" value="{{qty}}" onchange="updateItem('{{id}}')" onclick="$(this).select();"/>
	</td>
	<td id="total-{{id}}" class="middle text-right row-total" data-id="{{id}}" style="padding-left:5px; padding-right:5px;">{{total}}</td>
	<td class="middle text-center" style="padding-left:5px; padding-right:5px;">
		<span class="pointer" onclick="removeItem('{{id}}')"><i class="fa fa-trash red"></i></span>
	</td>
</tr>
</script>

<script id="update-template" type="text/x-handlebarsTemplate">
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="hidden" class="sell-item" data-id="{{id}}" id="pdCode-{{id}}" value="{{product_code}}">
		<input type="hidden" id="pdName-{{id}}" value="{{product_name}}">
		<input type="hidden" id="taxRate-{{id}}" value="{{vat_rate}}">
		<input type="hidden" id="taxAmount-{{id}}" value="{{vat_amount}}">
		<input type="hidden" id="stdPrice-{{id}}" value="{{std_price}}">
		<input type="hidden" id="sellPrice-{{id}}" value="{{final_price}}">
		<input type="hidden" id="discAmount-{{id}}" value="{{discount_amount}}">
		<input type="hidden" id="unitCode-{{id}}" value="{{unit_code}}">
		<input type="hidden" id="itemType-{{id}}" value="{{item_type}}">
		<input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
		<input type="hidden" id="currentPrice-{{id}}" value="{{price}}">
		<input type="hidden" id="currentDisc-{{id}}" value="{{discount_label}}">

		<input type="text" class="form-control input-xs no-border" value="{{product_name}} ({{product_code}})" />
	</td>
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="number" class="form-control input-xs text-center no-border" id="price-{{id}}" value="{{price}}" onchange="updateItem('{{id}}')" onclick="$(this).select();" />
	</td>
	<td class="middle" style="padding-left:5px; padding-right:5px;">
		<input type="text" class="form-control input-xs text-center input-disc no-border" data-id="{{id}}" id="disc-{{id}}" value="{{discount_label}}" onchange="updateItem('{{id}}')" onclick="$(this).select();" />
	</td>
	<td class="middle padding-5" style="padding-left:5px; padding-right:5px;">
		<input type="number" class="form-control input-xs text-center input-qty no-border" data-id="{{id}}" id="qty-{{id}}" value="{{qty}}" onchange="updateItem('{{id}}')" onclick="$(this).select();"/>
	</td>
	<td id="total-{{id}}" class="middle text-right row-total" data-id="{{id}}" style="padding-left:5px; padding-right:5px;">{{total}}</td>
	<td class="middle text-center" style="padding-left:5px; padding-right:5px;">
		<span class="pointer" onclick="removeItem('{{id}}')"><i class="fa fa-trash red"></i></span>
	</td>
</script>



<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title-site" >ชำระเงิน</h4>
            </div>
            <div class="modal-body">
							<div class="row">
								<div class="col-sm-12 col-xs-12 text-center">
									<span id="payAmountLabel" style="font-size:25px; color:#75ce66;"></span>
									<input type="hidden" id="payableAmount" />
	            	</div>



								<div class="col-sm-12 col-xs-12">
									<label>รับเงิน</label>
									<div class="input-group">
							      <input type="number" class="form-control input-lg text-center" id="receiveAmount" value="" placeholder="รับเงิน">
							      <span class="input-group-btn">
							        <button type="button" class="btn btn-primary btn-lg no-radius payment" onclick="justBalance()">รับพอดี</button>
							      </span>
    							</div>

								</div>

								<div class="col-sm-12 col-xs-12">
									<label class="not-show">Change</label>
									<input type="number" class="form-control input-lg text-center" id="changeAmount" placeholder="เงินทอน" disabled>

								</div>

							</div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-lg btn-info" id="btn-submit" onclick="submitPayment()" disabled>Submit</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="holdOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title-site" >พักบิล</h4>
            </div>
            <div class="modal-body">
							<div class="row">
								<div class="col-sm-12 col-xs-12">
									<label>Reference Note</label>
									<input type="text" class="form-control input-lg" id="reference-note" maxlength="50" placeholder="กรุณาระบุข้อความอ้างอิงในการพักบิล">
								</div>
							</div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-lg btn-info" id="btn-submit" onclick="holdBill()">Submit</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title-site" id="item-code-label"></h4>
            </div>
            <div class="modal-body">
							<div class="row">
								<div class="col-sm-12 col-xs-12">
									<table class="table table-striped">
										<tbody id="item-data"></tbody>
									</table>
								</div>
							</div>
            </div>

        </div>
    </div>
</div>

<script id="item-template" type="text/x-handlebarsTemplate">
	<tr>
		<td rowspan="7" class="width-30"><img class="img-responsive border-1" src="{{img}}" /></td>
		<td class="width-30">Product Type</td>
		<td class="width-40">{{item_type}}</td>
	</tr>
	<tr>
		<td class="width-30">Product Name</td>
		<td class="width-40">{{item_name}}</td>
	</tr>
	<tr>
		<td class="width-30">Product Code</td>
		<td class="width-40">{{item_code}}</td>
	</tr>
	<tr>
		<td class="width-30">Cost</td>
		<td class="width-40">{{cost}}</td>
	</tr>
	<tr>
		<td class="width-30">Price</td>
		<td class="width-40">{{price}}</td>
	</tr>
	<tr>
		<td class="width-30">Tax Rate</td>
		<td class="width-40">{{vat_rate}}</td>
	</tr>
	<tr>
		<td class="width-30">Quantity</td>
		<td class="width-40">{{qty}}</td>
	</tr>
</script>
