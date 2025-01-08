<?php $this->load->view('include/pos/pos_header'); ?>
<div class="row margin-top-30">
	<div class="col-sm-12 col-xs-12 text-center">
		<button type="button" class="btn btn-lg btn-primary" onclick="goAdd(<?php echo $pos_id; ?>)">ขายสินค้า</button>
		<button type="button" class="btn btn-lg btn-info" onclick="showHoldBill(<?php echo $pos_id; ?>)">
			บิลที่พักไว้
			<?php if($hold_bills > 0) : ?>
				<span class="badge"><?php echo $hold_bills; ?></span>
			<?php endif; ?>
			</button>
	</div>
</div>

<div class="modal fade" id="holdListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title-site" >บิลที่พักไว้</h3>
            </div>
            <div class="modal-body">
							<div class="row">
								<div class="col-sm-12 col-xs-12">
									<table class="width-100">
										<tbody id="hold-list"></tbody>
									</table>
								</div>
							</div>
            </div>
        </div>
    </div>
</div>


<script id="list-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr>
			<td class="width-30 middle">{{order_code}}</td>
			<td class="width-50 middle">{{ref_note}}</td>
			<td class="width-20 middle text-right">
				<button type="button" class="btn btn-sm btn-primary" onclick="goToBill({{pos_id}}, '{{order_code}}')">จัดการ</button>
			</td>
		</tr>
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/pos/pos_footer'); ?>
