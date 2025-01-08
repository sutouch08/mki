<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-8 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 text-center">#</th>
					<th class="width-20">เครื่อง POS</th>
					<th class="width-20">จุดขาย</th>
					<th class="width-20 text-center">โซน</th>
					<th class=""></th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($list)) : ?>
					<?php $no = 1; ?>
				<?php foreach($list as $pos) : ?>

					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $pos->name; ?></td>
						<td class="middle"><?php echo $pos->shop_name; ?></td>
						<td class="middle text-center"><?php echo $pos->zone_name; ?></td>
						<td class="middle text-right">
							<button type="button" class="btn btn-lg btn-primary" onclick="goToPOS(<?php echo $pos->id; ?>)">เลือก</button>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="5" class="text-center"><blockquote>ไม่พบเครื่อง POS</blockquote></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

</div>

<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
