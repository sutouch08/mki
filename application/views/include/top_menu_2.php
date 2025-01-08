<?php
$topMenuGroups = $this->menu->get_active_menu_groups('top');
$topSubMenuGroups = isset($this->menu_sub_group_code) ? $this->menu_sub_group_code : NULL;
?>

<?php if(!empty($topMenuGroups)) : ?>
	<ul class="nav nav-list" style="top:0px;">
		<?php foreach($topMenuGroups as $topMenu) : ?>
			<?php $subGroups = $this->menu->get_menus_sub_group($topMenu->code); ?>
				<?php if(!empty($subGroups)) : ?>
					<?php foreach($subGroups as $subGroup) : ?>
							<li class="hover">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									<?php echo $subGroup->name; ?> &nbsp;
									<i class="ace-icon fa fa-angle-down bigger-110"></i>
								</a>
						<?php $menus = $this->menu->get_menus_by_sub_group($subGroup->code, $topMenu->code); ?>
						<?php if(!empty($menus)) : ?>
							<ul class="submenu can-scroll ace-scroll scroll-disabled">
								<?php foreach($menus as $menu) : ?>
									<li class="hover">
										<a href="<?php echo base_url().$menu->url; ?>">
											<i class="ace-icon fa fa-bar-chart bigger-110 blue"></i>
											<?php echo $menu->name; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
							<div class="scroll-track scroll-detached no-track">
								<div class="scrollbars">

								</div>
							</div>
						<?php endif; ?>
						</li>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
