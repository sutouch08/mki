<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->

				<!--
			<div class="footer hidden-print" style="border-top:0px;">
				<div class="footer-inner" style="border-top:0px;">
					<div class="footer-content" style="border-top:0px;">
						<div class="col-lg-6 col-sm-6 col-md-6">
							<table class="table" style="margin-bottom:5px;">
								<tr style="background-color:#d9edf7;">
									<td class="width-25">Total Items</td>
									<td class="width-25 text-right" id="total_item">0</td>
									<td class="width-25">Total</td>
									<td class="width-25 text-right" id="total_amount">0.00</td>
								</tr>
								<tr style="background-color:#d9edf7; color:#3c8dbc;">
									<td class="width-25">Discount</td>
									<td class="width-25 text-right" id="total_discount">0.00</td>
									<td class="width-25">Tax</td>
									<td class="width-25 text-right" id="total_tax">0.00</td>
								</tr>
								<tr style="height:60px; font-size:30px; background-color:black; color:lime;">
									<td colspan="2" class="text-center">Total Payable</td>
									<td colspan="2" class="text-right" id="net_amount">0.00</td>
								</tr>
							</table>

							<table class="table" style="margin-bottom:0px;">
								<tr>
									<td class="width-30" style="padding:0px; border:0;">
										<button type="button" class="btn btn-warning btn-lagrg btn-block">Hold</button>
									</td>
									<td class="width-30" style="padding:0px; border:0;">
										<button type="button" class="btn btn-purple btn-lagrg btn-block">Print Order</button>
									</td>
									<td rowspan="2" class="width-40" style="padding:0px; border:0;">
										<button type="button" class="btn btn-success btn-block" style="height:85px;">Payment</button>
									</td>
								</tr>

								<tr>
									<td class="width-30" style="padding:0px; border:0;">
										<button type="button" class="btn btn-danger btn-lagrg btn-block">Cancel</button>
									</td>
									<td class="width-30" style="padding:0px; border:0;">
										<button type="button" class="btn btn-inverse btn-lagrg btn-block">Print Bill</button>
									</td>

								</tr>

							</table>
						</div>
					</div>
				</div>
			</div>
		-->

		<!-- page specific plugin scripts -->

		<!-- ace scripts -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='<?php echo base_url(); ?>assets/js/jquery.js'>"+"<"+"/script>");
		</script>

		<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar-scroll-1.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/ace/ace.submenu-hover.js"></script>
		<script src="<?php echo base_url(); ?>scripts/template.js"></script>
		<script>

			function changeUserPwd(uname)
			{
				window.location.href = BASE_URL +'user_pwd/change/'+uname;
			}
		</script>

	</body>

</html>
