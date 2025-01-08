
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5" >
		<div id="accordion" class="accordion-style1 panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_0" aria-expanded="true">
							<i class="bigger-110 ace-icon fa fa-angle-down" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
								HOME
						</a>
					</h4>
				</div>

				<div class="panel-collapse collapse in" id="collapse_0" aria-expanded="true" style="">
					<div class="panel-body">
						<?php	$qs = $this->product_tab_model->get_item_in_tab(0); ?>
						<?php if(!empty($qs)) : ?>
							<?php foreach($qs as $rs) : ?>
								<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-0 center">
									<div class="product padding-5">
										<div class="image">
											<a href="javascript:void(0)" onclick="add_item('<?php echo $rs->code; ?>')">
												<img class="img-responsive border-1" src="<?php echo get_product_image($rs->code, 'default'); ?>" />
											</a>
										</div>
										<div class="discription" style="font-size:10px; min-height:50px;">
											<a href="javascript:void(0)" onclick="add_item('<?php echo $rs->code; ?>')">
												<span class="display-block" style="white-space:pre-wrap;"><?php echo $rs->name; ?></span>
												<span><?php echo number($rs->price, 2); ?></span>
											</a>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php $tabs = $this->product_tab_model->getChild(0); ?>
			<?php if(!empty($tabs)) : ?>
				<?php foreach($tabs as $rs) : ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $rs->id; ?>" aria-expanded="false">
									<i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
										<?php echo $rs->name; ?>
								</a>
							</h4>
						</div>

						<div class="panel-collapse collapse" id="collapse_<?php echo $rs->id; ?>" aria-expanded="false" style="">
							<div class="panel-body">
								<?php	$qs = $this->product_tab_model->get_item_in_tab($rs->id); ?>
								<?php if(!empty($qs)) : ?>
									<?php foreach($qs as $ds) : ?>
										<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-0 center">
											<div class="product padding-5">
												<div class="image">
													<a href="javascript:void(0)" onclick="get_product_by_code('<?php echo $ds->code; ?>')">
														<img class="img-responsive border-1" src="<?php echo get_product_image($ds->code, 'default'); ?>" />
													</a>
												</div>
												<div class="discription" style="font-size:10px; min-height:50px;">
													<a href="javascript:void(0)" onclick="get_product_by_code('<?php echo $ds->code; ?>')">
														<span class="display-block"><?php echo $ds->name; ?></span>
														<span><?php echo number($ds->price, 2); ?></span>
													</a>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
			<?php endforeach; ?>
		<?php endif; ?>
		</div>
	</div>										<!-- #section:elements.tab.position -->


</div>
