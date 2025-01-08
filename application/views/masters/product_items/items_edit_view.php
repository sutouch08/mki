<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
			<button type="button" class="btn btn-sm btn-success" onclick="checkEdit()"><i class="fa fa-save"></i> บันทึก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-15 padding-5"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update/{$code}"; ?>">
<div class="row">
	<!-- left column -->
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
			<div class="col-xs-8 col-sm-7">
				<input type="text" class="width-100" value="<?php echo $code; ?>" disabled />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
			<div class="col-xs-8 col-sm-7">
				<input type="text" name="name" id="name" class="width-100" value="<?php echo $name; ?>" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">รุ่น</label>
			<div class="col-xs-8 col-sm-7">
				<input type="text" name="style" id="style" class="width-100" value="<?php echo $style_code; ?>" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="style-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">สี</label>
			<div class="col-xs-8 col-sm-7">
				<input type="text" name="color" id="color" class="width-100" value="<?php echo $color_code; ?>" />
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('color')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="color-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ไซส์</label>
			<div class="col-xs-8 col-sm-7">
				<input type="text" name="size" id="size" class="width-100" value="<?php echo $size_code; ?>" />
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('size')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="size-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">บาร์โค้ด</label>
			<div class="col-xs-8 col-sm-7">
				<input type="text" name="barcode" id="barcode" class="width-100" value="<?php echo $barcode; ?>" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="barcode-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ราคาทุน</label>
			<div class="col-xs-8 col-sm-7">
				<input type="number" step="any" name="cost" id="cost" class="width-100" value="<?php echo $cost; ?>" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="cost-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ราคาขาย</label>
			<div class="col-xs-8 col-sm-7">
				<input type="number" step="any" name="price" id="price" class="width-100" value="<?php echo $price; ?>" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="price-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">หน่วยนับ</label>
			<div class="col-xs-8 col-sm-7">
				<select class="form-control input-sm" name="unit_code" id="unit_code">
					<option value="">โปรดเลือก</option>
					<?php echo select_unit($unit_code); ?>
				</select>
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('unit_code')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="unit-error"></div>
		</div>


		<?php if(getConfig('USE_VAT')) : ?>
		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">VAT</label>
			<div class="col-xs-8 col-sm-7">
				<select class="form-control input-sm" name="vat_code" id="vat_code">
					<?php echo select_vat_group($vat_code); ?>
				</select>
			</div>
		</div>
		<?php else : ?>
			<input type="hidden" name="vat_code" id="vat_code" value="" />
		<?php endif;?>


		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">แถบแสดงสินค้า</label>
			<div class="col-xs-8 col-sm-7">
				<?php echo itemTabsTree($code); ?>
			</div>
		</div>

	</div>

	<!-- right column -->
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ยี่ห้อ</label>
			<div class="col-xs-8 col-sm-7">
				<select name="brand_code" id="brand" class="form-control">
					<option value="">โปรดเลือก</option>
				<?php echo select_product_brand($brand_code); ?>
				</select>
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('brand')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="brand-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มสินค้า</label>
			<div class="col-xs-8 col-sm-7">
				<select name="group_code" id="group" class="form-control input-sm" >
					<option value="">โปรดเลือก</option>
				<?php echo select_product_group($group_code); ?>
				</select>
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('group')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มย่อย</label>
			<div class="col-xs-8 col-sm-7">
				<select name="sub_group_code" id="subGroup" class="form-control">
					<option value="">โปรดเลือก</option>
				<?php echo select_product_sub_group($sub_group_code); ?>
				</select>
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('subGroup')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="subGroup-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">หมวดหมู่</label>
			<div class="col-xs-8 col-sm-7">
				<select name="category_code" id="category" class="form-control" >
					<option value="">โปรดเลือก</option>
				<?php echo select_product_category($category_code); ?>
				</select>
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('category')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="category-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ประเภท</label>
			<div class="col-xs-8 col-sm-7">
				<select name="kind_code" id="kind" class="form-control" >
					<option value="">โปรดเลือก</option>
				<?php echo select_product_kind($kind_code); ?>
				</select>
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('kind')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ชนิด</label>
			<div class="col-xs-8 col-sm-7">
				<select name="type_code" id="type" class="form-control" >
					<option value="">โปรดเลือก</option>
				<?php echo select_product_type($type_code); ?>
				</select>
			</div>
			<div class="col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addAttribute('type')"><i class="fa fa-plus"></i></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ปี<?php echo $year; ?></label>
			<div class="col-xs-8 col-sm-7">
				<select name="year" id="year" class="form-control">
					<option value="">โปรดเลือก</option>
					<?php echo select_years($year); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="year-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">นับสต็อก</label>
			<div class="col-xs-8 col-sm-7">
				<label style="padding-top:5px;">
					<input name="count_stock" class="ace ace-switch ace-switch-5" type="checkbox" value="1" <?php echo is_checked($count_stock,1); ?> />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">อนุญาติให้ขาย</label>
			<div class="col-xs-8 col-sm-7">
				<label style="padding-top:5px;">
					<input name="can_sell" class="ace ace-switch ace-switch-5" type="checkbox" value="1" <?php echo is_checked($can_sell,1); ?> />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>


		<div class="form-group hide">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">API</label>
			<div class="col-xs-8 col-sm-7">
				<label style="padding-top:5px;">
					<input name="is_api" class="ace ace-switch ace-switch-5" type="checkbox" value="1" <?php echo is_checked($is_api,1); ?>/>
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 col-xs-12 control-label no-padding-right">ใช้งาน</label>
			<div class="col-xs-8 col-sm-7">
				<label style="padding-top:5px;">
					<input name="active" class="ace ace-switch ace-switch-5" type="checkbox" value="1" <?php echo is_checked($active,1); ?> />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>
	</div> <!-- end right column-->

	<!-- image column -->
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		<div class="col-sm-12 col-xs-12 center">
			<span class="profile-picture">
				<img class="editable img-responsive" src="<?php echo get_product_image($code, 'medium'); ?>">
			</span>
		</div>
		<div class="divider-hidden"></div>


		<div class="col-sm-12 col-xs-12 center">
			<button type="button" class="btn btn-sm btn-success" onclick="changeImage()">Upload image</button>
			<?php if(!empty($image)) : ?>
			<button type="button" class="btn btn-sm btn-danger" onclick="deleteImage()">Delete image</button>
			<?php endif; ?>
		</div>
	</div>

	</div><!--/ row  -->
	<input type="hidden" name="code" id="code" value="<?php echo $code; ?>"/>
</form>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h4 class="blue">Change Image</h4>
			</div>
			<form class="no-margin" id="imageForm">
				<div class="modal-body">
					<div style="width:75%;margin-left:12%;">
						<label id="btn-select-file" class="ace-file-input ace-file-multiple">
							<input type="file" name="image" id="image" accept="image/*" style="display:none;" />
							<span class="ace-file-container" data-title="Click to choose new Image">
								<span class="ace-file-name" data-title="No File ...">
									<i class=" ace-icon ace-icon fa fa-picture-o"></i>
								</span>
							</span>
						</label>
						<div id="block-image" style="opacity:0;">
							<div id="previewImg" class="width-100 center"></div>
							<span onClick="removeFile()" style="position:absolute; left:385px; top:1px; cursor:pointer; color:red;">
								<i class="fa fa-times fa-2x"></i>
							</span>
						</div>
					</div>
				</div>
				<div class="modal-footer center">
					<button type="button" class="btn btn-sm btn-success" onclick="doUpload()"><i class="ace-icon fa fa-check"></i> Submit</button>
					<button type="button" class="btn btn-sm" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('masters/product_items/items_modal'); ?>
<script src="<?php echo base_url(); ?>scripts/masters/items.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
