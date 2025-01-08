<?php if( $this->_SuperAdmin ): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	?>
<?php
	$menus = $this->menu->get_valid_menu_groups();
 ?>

<div class="tab-pane fade" id="menu">
	<form id="menuForm" method="post" action="<?php echo $this->home; ?>/update_menu_config">
  	<div class="row">
    	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr class="hide">
							<th class="width-5 text-center">Active</th>
							<th class="">Menu</th>
						</tr>
					</thead>
					<tbody>
		<?php if(!empty($menus)) : ?>
			<?php foreach($menus as $gs) : ?>
						<tr class="font-size-14" style="background-color:#428bca73;">
							<td colspan="2" class="middle">
								<label>
								<input type="checkbox" class="ace" onchange="toggleGroup($(this), '<?php echo $gs->code; ?>')" <?php echo is_checked('1',$gs->active); ?> />
								<span class="lbl"><?php echo $gs->name; ?></span>
								</label>
								<input type="hidden" name="group[<?php echo $gs->code; ?>]" id="group-<?php echo $gs->code; ?>" value="<?php echo $gs->active; ?>">
							</td>
						</tr>
						</tr>
						<?php $menu = $this->menu->get_menus_list_by_group($gs->code); ?>
						<?php if(!empty($menu)) : ?>
							<?php foreach($menu as $ms) : ?>
								<tr>
									<td class="middle"></td>
									<td class="middle">
										<label>
										<input type="checkbox" class="ace" onchange="toggleMenu($(this), '<?php echo $ms->code; ?>')" <?php echo is_checked('1', $ms->active); ?> />
										<span class="lbl"><?php echo $ms->name; ?></span>
										</label>
										<input type="hidden" name="menu[<?php echo $ms->code; ?>]" id="menu-<?php echo $ms->code; ?>" value="<?php echo $ms->active; ?>">
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
		<?php endif; ?>
					</tbody>
				</table>
    	</div>
      <div class="divider-hidden"></div>

      <div class="col-sm-8 col-sm-offset-4">
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('menuForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
</div>
<?php endif; ?>
