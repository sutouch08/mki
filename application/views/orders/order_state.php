<?php
$pm = get_permission('SOREST', $this->_user->uid, $this->_user->id_profile); //--- ย้อนสถานะออเดอร์ได้หรือไม่
$px	= get_permission('SORECT', $this->_user->uid, $this->_user->id_profile); //--- ย้อนสถานะออเดอร์ที่เปิดบิลแล้วได้หรือไม่
$pc = get_permission('SOREUP', $this->_user->uid, $this->_user->id_profile); //--- ปล่อยออเดอร์ที่ยังไม่ชำระเงิน (เงินสด)
$canChange	= ($pm->can_add + $pm->can_edit + $pm->can_delete) > 0 ? TRUE : FALSE;
$canUnbill	= ($px->can_add + $px->can_edit + $px->can_delete) > 0 ? TRUE : FALSE;
$canSkip = ($pc->can_add + $pc->can_edit + $pc->can_delete) > 0 ? TRUE : FALSE;
?>

<div class="row" style="padding:15px;">
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 padding-5">
		<table class="table" style="margin-bottom:5px;">
			<?php if( $this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete ) : ?>
				<tr>
					<td class="width-25 middle text-right" style="border:0px; padding:5px;">สถานะ : </td>
					<td class="width-50" style="border:0px; padding:0px;">
						<select class="form-control input-sm" style="padding-top:0px; padding-bottom:0px;" id="stateList">

							<?php if($order->status == 0) : ?>
								<option value="0">กรุณาบันทึกออเดอร์</option>
							<?php else : ?>
								<?php if($order->role == 'P' && $order->is_approved == 0) : ?>
									<option value="0">รออนุมัติ</option>
								<?php else : ?>
									<option value="0">เลือกสถานะ</option>
								<?php endif; ?>
							<?php endif; ?>

							<?php if( $order->state != 9 && $order->is_expired == 0 && $order->status == 1) : ?>
								<?php if( $order->state <=3 && $canChange) : ?>
										<?php if($order->state != 1): ?>
													<option value="1">รอดำเนินการ</option>
										<?php endif; ?>
										<?php if($order->role == 'S' && $order->state != 2 && $order->is_term == 0) : ?>
													<option value="2">รอชำระเงิน</option>
										<?php endif; ?>

										<?php if($order->state != 3) : ?>
											<?php if($order->role == 'S' && ($order->is_paid == 1 OR $order->is_term == 1 OR $order->payment_role == 4 OR $canSkip)) : ?>
												<option value="3">รอจัดสินค้า</option>
											<?php endif; ?>
											<?php if($order->role != 'S' && ($order->role != 'P' OR $order->is_approved == 1)) : ?>
												<option value="3">รอจัดสินค้า</option>
											<?php endif; ?>
										<?php endif; ?>

									<?php if(getConfig('DEFAULT_ZONE') != "" && getConfig('USE_PREPARE') == 0) : ?>
										<option value="7">รอเปิดบิล</option>
									<?php endif; ?>

								<?php elseif($order->state > 3 && $order->state < 8 && $canChange ) : ?>
									<option value="1">รอดำเนินการ</option>
									<?php if($order->role == 'S') : ?>
										<option value="2">รอชำระเงิน</option>
									<?php endif; ?>
									<option value="3">รอจัดสินค้า</option>
								<?php elseif($order->state > 3 && $order->state >= 8 && $canUnbill ) : ?>
									<option value="1">รอดำเนินการ</option>
									<?php if($order->role == 'S') : ?>
									<option value="2">รอชำระเงิน</option>
									<?php endif; ?>
									<option value="3">รอจัดสินค้า</option>
									<option value="7">รอเปิดบิล</option>
								<?php endif; ?>

								<?php if( $order->state < 8 && $this->pm->can_delete ) : ?>
									<option value="9">ยกเลิก</option>
								<?php elseif( $order->state >= 8 && $canUnbill) : ?>
									<option value="9">ยกเลิก</option>
								<?php endif; ?>

							<?php elseif($order->is_expired == 1 && $this->pm->can_delete) : ?>
								<option value="9">ยกเลิก</option>
							<?php elseif($order->state == 9 && $this->pm->can_edit) : ?>
								<option value="1">รอดำเนินการ</option>
							<?php endif; ?>
						</select>
					</td>
					<td class="width-25" style="border:0px; padding:0px; padding-left:5px;">
						<?php if( $order->status == 1 && $order->is_expired == 0 ) : ?>
							<button class="btn btn-xs btn-primary btn-block" onclick="changeState()">เปลี่ยนสถานะ</button>
						<?php elseif($order->is_expired == 1 && $$this->pm->can_delete) : ?>
							<button class="btn btn-xs btn-primary btn-block" onclick="changeState()">เปลี่ยนสถานะ</button>
						<?php elseif($order->state == 9 && $this->pm->can_delete) : ?>
							<button class="btn btn-xs btn-primary btn-block" onclick="changeState()">เปลี่ยนสถานะ</button>
						<?php endif; ?>
					</td>
				</tr>
			<?php else : ?>
				<tr>
					<td class="width-30 text-center" style="border:0px;">สถานะ</td>
					<td class="width-40 text-center" style="border:0px;">พนักงาน</td>
					<td class="width-30 text-center" style="border:0px;">เวลา</td>
				</tr>
			<?php endif; ?>
		</table>
	</div>


	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5 font-size-14"	style="border:solid 2px red; <?php echo state_color($order->state); ?>"	>
		<center>สถานปัจจุบัน</center>
		<center><?php echo get_state_name($order->state); ?></center>
	</div>

	<?php if( !empty($state) ) : ?>
		<?php foreach($state as $rs) : ?>
			<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5 font-size-10" style="border:solid 2px white; <?php echo state_color($rs->state); ?>" >
				<center><?php echo get_state_name($rs->state); ?></center>
				<center><?php echo $this->user_model->get_name($rs->update_user); ?></center>
				<center><?php echo thai_date($rs->date_upd,TRUE, '/'); ?></center>
			</div>
		<?php	endforeach; ?>
	<?php endif; ?>
</div>
