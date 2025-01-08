<style>
	td {
		border:none !important;
	}
</style>
<div class="tab-pane fade" id="document">
	<form id="documentForm" class="form-horizontal" method="post" action="<?php echo $this->home; ?>/update_config">
    <div class="row">
			<div class="col-sm-6 col-xs-12 table-responsive">
				<table class="table">
					<tbody>
						<tr>
							<td class="width-30 text-right middle">ใบเสนอราคา</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_QUOTATION" required value="<?php echo $PREFIX_QUOTATION; ?>" />
							</td>
							<td class="width-30 middle text-right">Run digit</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_QUOTATION" value="<?php echo $RUN_DIGIT_QUOTATION; ?>" />
							</td>
						</tr>

						<tr>
							<td class="width-30 text-right middle">ออเดอร์[ขายสินค้า]</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ORDER" required value="<?php echo $PREFIX_ORDER; ?>" />
							</td>
							<td class="width-30 middle text-right">Run digit</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_ORDER" value="<?php echo $RUN_DIGIT_ORDER; ?>" />
							</td>
						</tr>

						<tr>
							<td class="width-30 text-right middle">ใบส่งของ/ใบกำกับ</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_INVOICE" required value="<?php echo $PREFIX_INVOICE; ?>" />
							</td>
							<td class="width-30 middle text-right">Run digit</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_INVOICE" required value="<?php echo $RUN_DIGIT_INVOICE; ?>" />
							</td>
						</tr>

						<tr>
							<td class="width-30 text-right middle">รับชำระหนี้[ตัดหนี้]</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ORDER_REPAY" required value="<?php echo $PREFIX_ORDER_REPAY; ?>" />
							</td>
							<td class="width-30 middle text-right">Run digit</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_ORDER_REPAY" required value="<?php echo $RUN_DIGIT_ORDER_REPAY; ?>" />
							</td>
						</tr>

						<tr>
							<td class="width-30 text-right middle">ฝากขาย[โอนคลัง]</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIG_TR" required value="<?php echo $PREFIX_CONSIGN_TR; ?>" />
							</td>
							<td class="width-30 middle text-right">Run digit</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_CONSIGN_TR" required value="<?php echo $RUN_DIGIT_CONSIGN_TR; ?>" />
							</td>
						</tr>
						<!--
						<tr>
							<td class="width-30 text-right middle">ฝากขาย[ใบกำกับ]</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_SO" required value="<?php echo $PREFIX_CONSIGN_SO; ?>" />
							</td>
							<td class="width-30 middle text-right">Run digit</td>
							<td class="width-20 middle">
								<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_CONSIGN_SO" required value="<?php echo $RUN_DIGIT_CONSIGN_SO; ?>" />
							</td>
						</tr>
					-->

					<tr>
						<td class="width-30 text-right middle">ตัดยอดฝากขาย</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_SOLD" required value="<?php echo $PREFIX_CONSIGN_SOLD; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_CONSIGN_SOLD" required value="<?php echo $RUN_DIGIT_CONSIGN_SOLD; ?>" />
						</td>
					</tr>

					<tr>
						<td class="width-30 text-right middle">รับสินคาเข้าจากการซื้อ</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RECEIVE_PO" required value="<?php echo $PREFIX_RECEIVE_PO; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RECEIVE_PO" required value="<?php echo $RUN_DIGIT_RECEIVE_PO; ?>" />
						</td>
					</tr>

					<tr>
						<td class="width-30 text-right middle">รับสินค้าจากการผลิต</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RECEIVE_TRANSFORM" required value="<?php echo $PREFIX_RECEIVE_TRANSFORM; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RECEIVE_TRANSFORM" required value="<?php echo $RUN_DIGIT_RECEIVE_TRANSFORM; ?>" />
						</td>
					</tr>

					<tr>
						<td class="width-30 text-right middle">เบิกสินค้าไปผลิต</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center" name="PREFIX_TRANSFORM" required value="<?php echo $PREFIX_TRANSFORM; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center" name="RUN_DIGIT_TRANSFORM" required value="<?php echo $RUN_DIGIT_TRANSFORM; ?>" />
						</td>
					</tr>
					<!--
					<tr>
						<td class="width-30 text-right middle">ยืมสินค้า</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_LEND" required value="<?php echo $PREFIX_LEND; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_LEND" required value="<?php echo $RUN_DIGIT_LEND; ?>" />
						</td>
					</tr>


					<tr>
						<td class="width-30 text-right middle">เบิกสปอนเซอร์</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_SPONSOR" required value="<?php echo $PREFIX_SPONSOR; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_SPONSOR" required value="<?php echo $RUN_DIGIT_SPONSOR; ?>" />
						</td>
					</tr>


					<tr>
						<td class="width-30 text-right middle">เบิกอภินันท์</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_SUPPORT" required value="<?php echo $PREFIX_SUPPORT; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_SUPPORT" required value="<?php echo $RUN_DIGIT_SUPPORT; ?>" />
						</td>
					</tr>
					-->
					<tr>
						<td class="width-30 text-right middle">คืนสินค้าจากการขาย</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RETURN_ORDER" required value="<?php echo $PREFIX_RETURN_ORDER; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RETURN_ORDER" required value="<?php echo $RUN_DIGIT_RETURN_ORDER; ?>" />
						</td>
					</tr>

					<!--
					<tr>
						<td class="width-30 text-right middle">คืนสินค้าจากการยืม</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RETURN_LEND" required value="<?php echo $PREFIX_RETURN_LEND; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RETURN_LEND" required value="<?php echo $RUN_DIGIT_RETURN_LEND; ?>" />
						</td>
					</tr>
					
					<tr>
						<td class="width-30 text-right middle">กระทบยอด</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_CHECK" required value="<?php echo $PREFIX_CONSIGN_CHECK; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_CONSIGN_CHECK" required value="<?php echo $RUN_DIGIT_CONSIGN_CHECK; ?>" />
						</td>
					</tr>
				-->

					<tr>
						<td class="width-30 text-right middle">โอนสินค้าระหว่างคลัง</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_TRANSFER" required value="<?php echo $PREFIX_TRANSFER; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_TRANSFER" required value="<?php echo $RUN_DIGIT_TRANSFER; ?>" />
						</td>
					</tr>

					<tr>
						<td class="width-30 text-right middle">ย้ายพื้นที่จัดเก็บ</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_MOVE" required value="<?php echo $PREFIX_MOVE; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_MOVE" required value="<?php echo $RUN_DIGIT_MOVE; ?>" />
						</td>
					</tr>

					<tr>
						<td class="width-30 text-right middle">ปรับยอดสต็อก</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ADJUST" required value="<?php echo $PREFIX_ADJUST; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_ADJUST" required value="<?php echo $RUN_DIGIT_ADJUST; ?>" />
						</td>
					</tr>

					<tr>
						<td class="width-30 text-right middle">นโยบายส่วนลด</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_POLICY" required value="<?php echo $PREFIX_POLICY; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_POLICY" required value="<?php echo $RUN_DIGIT_POLICY; ?>" />
						</td>
					</tr>

					<tr>
						<td class="width-30 text-right middle">เงื่อนไขส่วนลด</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RULE" required value="<?php echo $PREFIX_RULE; ?>" />
						</td>
						<td class="width-30 middle text-right">Run digit</td>
						<td class="width-20 middle">
							<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RULE" required value="<?php echo $RUN_DIGIT_RULE; ?>" />
						</td>
					</tr>
					</tbody>
				</table>
			</div>

			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>
      <div class="col-sm-6 col-xs-12 center">
      	<button type="button" class="btn btn-sm btn-success input-small text-center" onClick="checkDocumentSetting()"><i class="fa fa-save"></i> บันทึก</button>
      </div>


    </div><!--/ row -->
  </form>
</div>
