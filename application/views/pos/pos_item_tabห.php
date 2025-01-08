<div id="accordion" class="accordion-style1 panel-group accordion-style2">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true">
															<i class="bigger-110 ace-icon fa fa-angle-down" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
															&nbsp;Group Item #1
														</a>
													</h4>
												</div>

												<div class="panel-collapse collapse in" id="collapseOne" aria-expanded="true" style="">
													<div class="panel-body">
														Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
													</div>
												</div>
											</div>

											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false">
															<i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
															&nbsp;Group Item #2
														</a>
													</h4>
												</div>

												<div class="panel-collapse collapse" id="collapseTwo" aria-expanded="false" style="height: 0px;">
													<div class="panel-body">
														Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
													</div>
												</div>
											</div>

											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false">
															<i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
															&nbsp;Group Item #3
														</a>
													</h4>
												</div>

												<div class="panel-collapse collapse" id="collapseThree" aria-expanded="false" style="height: 0px;">
													<div class="panel-body">
														Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
													</div>
												</div>
											</div>
										</div>v


<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5" >
		<div class="tabbable tabs-right" style="display:flex;">


			<div class="tab-content width-100 margin-bottom-10" style="min-height:300px; overflow-y:scroll;">
				<div id="top" class="tab-pane active">
		<?php	$qs = $this->product_tab_model->get_item_in_tab(0); ?>
		<?php if(!empty($qs)) : ?>
			<?php foreach($qs as $rs) : ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-0 center">
					<div class="product padding-5">
						<div class="image">
							<a href="javascript:void(0)" onclick="getOrderItemGrid('<?php echo $rs->code; ?>')">
								<img class="img-responsive border-1" src="<?php echo get_product_image($rs->code, 'default'); ?>" />
							</a>
						</div>
						<div class="discription" style="font-size:10px; min-height:50px;">
							<a href="javascript:void(0)" onclick="getOrderItemGrid('<?php echo $rs->code; ?>')">
								<span class="display-block"><?php echo $rs->name; ?></span>
								<span><?php echo number($rs->price, 2); ?></span>
							</a>
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
			<ul class="nav nav-tabs" id="myTab3" style="width:100px; min-height:100%; border:0;">
				<li class="active">
					<a data-toggle="tab" href="#top" aria-expanded="true" onclick="getOrderTabs(0)">
						HOME
					</a>
				</li>

		<?php $tabs = $this->product_tab_model->getChild(0); ?>
		<?php  if(!empty($tabs)) : ?>
			<?php foreach($tabs as $rs) : ?>
				<li class="">
					<a data-toggle="tab" href="#cat-<?php echo $rs->id; ?>" aria-expanded="false" onclick="getItemTabs(<?php echo $rs->id; ?>)"><?php echo $rs->name; ?></a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
			</ul>
		</div>

		<!-- /section:elements.tab.position -->
	</div>										<!-- #section:elements.tab.position -->


</div>
