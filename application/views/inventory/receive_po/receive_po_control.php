
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Lot.</label>
		<input type="text" class="form-control input-sm text-center" id="receive-date" value="<?php echo thai_date($doc->posting_date); ?>" readonly/>
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-8 padding-5">
		<label>โซน</label>
		<input type="text" class="form-control input-sm" id="zone-code" placeholder="รหัสโซน" autofocus />
	</div>
	<!-- <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">get</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add-zone" onclick="getZone()">ตกลง</button>
		<button type="button" class="btn btn-xs btn-info btn-block hide" id="btn-change-zone" onclick="changeZone()">เปลี่ยน</button>
	</div> -->
	<div class="col-lg-7 col-md-6-harf col-sm-6 col-xs-12 padding-5">
		<label class="">ชื่อโซน</label>
		<input type="text" class="form-control input-sm" id="zone-name" value="" readonly/>
	</div>

	<div class="divider"></div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>จำนวน</label>
		<input type="number" class="form-control input-sm text-center" id="item-qty" value="1" />
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label>บาร์โค้ดสินค้า</label>
		<input type="text" class="form-control input-sm text-center"  id="barcode-item" value="" />
	</div>
	<!-- <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">get</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add-item" onclick="addItem()" disabled>เพิ่ม</button>
	</div> -->

	<div class="col-lg-7 col-md-3-harf col-sm-3 col-xs-12">&nbsp;</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">get</label>
		<button type="button" class="btn btn-xs btn-info btn-block" onclick="getData()">ดึงใบสั่งผลิต</button>
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">get</label>
		<button type="button" class="btn btn-xs btn-danger btn-block" onclick="removeAll()">ลบทั้งหมด</button>
  </div>
</div>

<input type="hidden" id="zoneCode"/>

<hr class="margin-top-15">

<form id="orderForm">
	<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" id="modal" style="width:800px; max-width:95vw;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="modalTitle">title</h4>
				</div>
				<div class="modal-body" id="modalBody" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
					<button type="button" class="btn btn-primary" onClick="insert_item()" >เพิ่มในรายการ</button>
				</div>
			</div>
		</div>
	</div>
</form>


<div class="modal fade" id="poGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:800px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="po-title">title</h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="min-width:640px;">
          <thead>
            <th class="fix-width-40 text-center">#</th>
            <th class="fix-width-150 text-center">รหัส</th>
            <th class="fix-width-150 text-center">สินค้า</th>
            <th class="fix-width-100 text-center">ราคา</th>
            <th class="fix-width-100 text-center">ค้างรับ</th>
            <th class="fix-width-100 text-center">จำนวน</th>
          </thead>
          <tbody id="po-body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default top-btn" id="btn_close" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-yellow top-btn" onclick="receiveAll()">รับยอดค้างทั้งหมด</button>
				<button type="button" class="btn btn-purple top-btn" onclick="clearAll()">เคลียร์ตัวเลขทั้งหมด</button>
        <button type="button" class="btn btn-primary top-btn" onclick="insertPoItems()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>



<script id="receiveTableTemplate" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
		<tr>
			<td colspan="9" class="text-center">---- ไม่พบรายการ ----</td>
		</tr>
		{{else}}
			{{#if @last}}
				<tr>
				<td colspan="6" class="text-right"><strong>รวม</strong></td>
				<td class="text-right"><strong>{{total_qty}}</strong></td>
				<td class="text-right"><strong>{{total_amount}}</strong></td>
				<td></td>
				</tr>
			{{else}}
					<tr>
						<td class="text-center no">{{no}}</td>
						<td class="">{{product_code}}</td>
						<td class="">{{product_name}}</td>
						<td class="">{{zone_name}}</td>
						<td class="text-center">{{receive_date}}</td>
						<td class="text-right">{{price}}</td>
						<td class="text-right">{{qty}}</td>
						<td class="text-right">{{amount}}</td>
						<td class="text-center">
							{{#if open}}
								<button type="button" class="btn btn-minier btn-danger" onclick="removeRow({{id}}, '{{product_code}}')">
									<i class="fa fa-trash"></i>
								</button>
							{{/if}}
						</td>
					</tr>
			{{/if}}
		{{/if}}
	{{/each}}
</script>

<script id="row-template" type="text/x-handlebarsTemplate">
{{#each this}}
<tr>
  <td class="text-center middle no">{{no}}</td>
  <td class="middle">{{pdCode}}</td>
  <td class="middle">{{pdName}}</td>
  <td class="middle text-center">
    <input type="number" class="form-control input-sm text-center receive-box" id="receive-{{id_pa}}" value="{{qty}}" />
    <span class="hide" id="label-{{id_pa}}">{{qty}}</span>
    <input type="hidden" id="productId-{{id_pa}}" value="{{id_pd}}" />
  </td>
  <td class="middle text-center">
    <button type="button" class="btn btn-sm btn-danger" id="btn-remove-{{id_pa}}" onclick="deleteRow({{id_pa}})"><i class="fa fa-trash"></i></button>
  </td>
</tr>
{{/each}}
</script>


<script id="po-template" type="text/x-handlebarsTemplate">
{{#each this}}
<tr class="item-row">
  <td class="text-center middle no">{{no}}</td>
  <td class="middle">{{pdCode}}</td>
  <td class="middle">{{pdName}}</td>
  <td class="middle text-center">{{price}}</td>
  <td class="middle text-center" id="backlogs-{{no}}">{{backlogs}}</td>
  <td class="middle text-center">
    <input type="number" class="form-control input-sm text-center receive_qty" data-no="{{no}}" data-pdcode="{{pdCode}}" id="pdCode-{{no}}" value="{{qty}}" />
		<input type="hidden" id="qty-{{no}}" value="{{qty}}" />
  </td>
</tr>
{{/each}}
</script>
