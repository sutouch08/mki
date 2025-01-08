<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5" >
		<div class="tabbable tabs-left" style="display:flex;">
			<ul class="nav nav-tabs" id="myTab3" style="width:100px; border-bottom:0;">
				<li class="active">
					<a data-toggle="tab" href="#top" aria-expanded="true" onclick="getOrderTabs(0)">
						TOP LEVEL
					</a>
				</li>

		<?php $tabs = $this->product_tab_model->getChild(0); ?>
		<?php  if(!empty($tabs)) : ?>
			<?php foreach($tabs as $rs) : ?>
				<li class="">
					<a data-toggle="tab" href="#cat-<?php echo $rs->id; ?>" aria-expanded="false" onclick="getOrderTabs(<?php echo $rs->id; ?>)"><?php echo $rs->name; ?></a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
			</ul>

			<div class="tab-content width-100 margin-bottom-10" style="max-height:300px; overflow-y:auto;">
				<div id="top" class="tab-pane active">
		<?php	$qs = $this->product_tab_model->get_style_in_tab(0); ?>
		<?php if(!empty($qs)) : ?>
			<?php foreach($qs as $rs) : ?>
				<div class="item-card padding-0 center">
					<div class="product padding-5">
						<div class="image">
							<a href="javascript:void(0)" onclick="getOrderGrid('<?php echo $rs->code; ?>')">
								<img class="img-responsive border-1" src="<?php echo get_cover_image($rs->code, 'mini'); ?>" />
							</a>
						</div>
						<div class="discription" style="font-size:10px; min-height:50px;">
							<a href="javascript:void(0)" onclick="getOrderGrid('<?php echo $rs->code; ?>')">
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
		</div>

		<!-- /section:elements.tab.position -->
	</div>										<!-- #section:elements.tab.position -->


</div>
