<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:10px;">
		<button type="button" class="btn btn-white"></button>
		<div class="text-center border-1" style="min-width:100px; height:35px;">
			<a data-toggle="tab" style="display:block; padding:7px 12px 8px" href="#top" aria-expanded="true" onclick="getOrderTabs(0)">
				HOME
			</a>
		</div>
		<?php $tabs = $this->product_tab_model->getChild(0); ?>
		<?php  if(!empty($tabs)) : ?>
			<?php foreach($tabs as $rs) : ?>
				<div class="text-center border-1" style="min-width:100px; height:35px; padding:7px 12px 8px">
					<a data-toggle="tab" href="#cat-<?php echo $rs->id; ?>" aria-expanded="false" onclick="getItemTabs(<?php echo $rs->id; ?>)"><?php echo $rs->name; ?></a>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" >
		<div class="tabbable tabs-left" style="display:flex;">

			<div class="tab-content width-100 margin-bottom-10" style="padding:0px; border:0px; min-height:300px; max-height: 800px; overflow-y:scroll;">
				<div id="top" class="tab-pane active">
		<?php	$qs = $this->product_tab_model->get_item_in_tab(0); ?>
		<?php if(!empty($qs)) : ?>
			<?php foreach($qs as $rs) : ?>
				<div class="col-lg-1-harf col-md-3 col-sm-4 col-xs-6" style="padding:5px;">
					<div class="border-1" style="padding:10px;">
						<div class="image">
							<a href="javascript:void(0)" onclick="getOrderItemGrid('<?php echo $rs->code; ?>')">
								<img class="img-responsive" src="<?php echo get_product_image($rs->code, 'mini'); ?>" />
							</a>
						</div>
						<div class="description" style="overflow: hidden; line-height: 18px; height:42px; font-size:16px; font-weight:400;">
							ssssssssssssssssss<?php echo $rs->name; ?>
						</div>
						<div class="description" style="height:20px; font-size:10px;">
							รหัสสินค้า &nbsp;&nbsp;<?php echo $rs->code; ?>
						</div>
						<div class="price red bold margin-bottom-15" style="font-size:18px;">
							<?php echo number($rs->price, 2); ?> ฿
						</div>
						<div class="row" style="margin-left:-5px; margin-right:-5px;">
							<div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 padding-5 margin-bottom-10">
								<div class="input-group">
									<span class="input-group-btn"><button type="button" class="btn btn-white padding-5"><i class="fa fa-minus"></i></button></span>
									<input type="number" class="form-control text-center" style="font-size:14px;" value="1" id="<?php echo $rs->code; ?>" />
									<span class="input-group-btn"><button type="button" class="btn btn-white padding-5"><i class="fa fa-plus"></i></button></span>
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 padding-5">
								<button type="button" class="btn btn-sm btn-danger btn-block">เพิ่ม</button>
							</div>
						</div>

					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>

				</div>
				<?php if(!empty($tabs)) : ?>
					<?php foreach($tabs as $rs) : ?>
						<div id="cat-<?php echo $rs->id; ?>" class="tab-pane"></div>
				<?php endforeach; ?>
			<?php endif; ?>

			</div>
		</div>

		<!-- /section:elements.tab.position -->
	</div>										<!-- #section:elements.tab.position -->


</div>

<script src="<?php echo base_url(); ?>assets/js/owlcarousel/owl.carousel.min.js"></script>

<script>
$(document).ready(function(){
	$(".owl-carousel").owlCarousel({
		"margin" : 10,
		"autoWidth" : true,
		"dots" : false
	});
});
</script>
