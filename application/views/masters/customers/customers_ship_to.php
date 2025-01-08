<div class='row'>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 table-responsive border-1">
    <table class='table table-bordered min-width-900'>
      <thead>
        <tr>
          <td colspan="6" align="center">ที่อยู่สำหรับจัดส่ง
            <p class="pull-right top-p">
              <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
            </p>
          </td>
        </tr>
        <tr style="font-size:12px;">
          <td class="fix-width-80 text-center">ชื่อเรียก</td>
					<td class="fix-width-80 text-center">รหัสสาขา</td>
          <td class="fix-width-150">ผู้รับ</td>
          <td class="min-width-250">ที่อยู่</td>
          <td class="fix-width-150">โทรศัพท์</td>
          <td class="fix-width-120"></td>
        </tr>
      </thead>
      <tbody id="adrs">
<?php if(!empty($addr)) : ?>
<?php 	foreach($addr as $rs) : ?>
  <?php  $tumbon = !empty($rs->sub_district) ? ' ต.'.$rs->sub_district : ''; ?>
  <?php  $aumphor = !empty($rs->district) ? ' อ.'.$rs->district : ''; ?>
  <?php  $province = !empty($rs->province) ? ' จ.'.$rs->province : ''; ?>
  <?php  $postcode = !empty($rs->postcode) ? ' '.$rs->postcode : ''; ?>
        <tr style="font-size:12px;" id="<?php echo $rs->id; ?>">
          <td class="middle text-center"><?php echo $rs->alias; ?></td>
					<td class="middle text-center"><?php echo $rs->code; ?></td>
          <td class="middle"><?php echo $rs->name; ?></td>
          <td class="middle"><?php echo $rs->address . $tumbon . $aumphor . $province . $postcode; ?></td>
          <td class="middle"><?php echo $rs->phone; ?></td>
          <td class="middle text-right">
    <?php if( $rs->is_default == 1 ) : ?>
            <button type="button" class="btn btn-mini btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setDefault(<?php echo $rs->id; ?>)">
              <i class="fa fa-check"></i>
            </button>
    <?php else : ?>
            <button type="button" class="btn btn-mini btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setDefault(<?php echo $rs->id; ?>)">
              <i class="fa fa-check"></i>
            </button>
    <?php endif; ?>
            <button type="button" class="btn btn-mini btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
            <button type="button" class="btn btn-mini btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
          </td>
        </tr>
<?php 	endforeach; ?>
<?php else : ?>
        <tr><td colspan="6" align="center">ไม่พบที่อยู่</td></tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div><!-- /row-->


<!--  Add New Address Modal  --------->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center" >เพิ่ม/แก้ไข ที่อยู่สำหรับจัดส่ง</h4>
            </div>
            <div class="modal-body">
            <form id="addAddressForm"	>
            <input type="hidden" name="id_address" id="id_address" />
            <div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
								<label class="input-label">รหัสสาขา</label>
								<input type="text" class="form-control input-sm" name="address_code" id="address_code" placeholder="รหัสสาขา" />
							</div>
            	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 padding-5">
                	<label class="input-label">ชื่อ</label>
                    <input type="text" class="form-control input-sm" name="Fname" id="Fname" placeholder="ชื่อผู้รับ (จำเป็น)" />
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
                	<label class="input-label">ที่อยู่</label>
                    <input type="text" class="form-control input-sm" name="address" id="address1" placeholder="เลขที่, หมู่บ้าน, ถนน (จำเป็น)" />
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
                	<label class="input-label">ตำบล/แขวง</label>
                    <input type="text" class="form-control input-sm" name="sub_district" id="sub_district" placeholder="ตำบล" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
                	<label class="input-label">อำเภอ/เขต</label>
                    <input type="text" class="form-control input-sm" name="district" id="district" placeholder="อำเภอ (จำเป็น)" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
                	<label class="input-label">จังหวัด</label>
                    <input type="text" class="form-control input-sm" name="province" id="province" placeholder="จังหวัด (จำเป็น)" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
                	<label class="input-label">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control input-sm" name="postcode" id="postcode" placeholder="รหัสไปรษณีย์" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
                	<label class="input-label">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control input-sm" name="phone" id="phone" placeholder="000 000 0000" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
                	<label class="input-label">อีเมล์</label>
                    <input type="text" class="form-control input-sm" name="email" id="email" placeholder="someone@somesite.com" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
                	<label class="input-label">ชื่อเรียก</label>
                    <input type="text" class="form-control input-sm" name="alias" id="alias" placeholder="ใช้เรียกที่อยู่ เช่น บ้าน, ที่ทำงาน (จำเป็น)" />
                </div>
            </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" onClick="saveAddress()" ><i class="fa fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>



<script id="addressTableTemplate" type="text/x-handlebars-template">
{{#each this}}
<tr style="font-size:12px;" id="{{ id }}">
	<td class="text-center">{{ alias }}</td>
	<td class="text-center">{{code}}</td>
	<td>{{ name }}</td>
	<td>{{ address }}</td>
	<td>{{ phone }}</td>
	<td align="right">
	{{#if default}}
		<button type="button" class="btn btn-xs btn-success btn-address" id="btn-{{ id }}" onClick="setDefault({{ id }})"><i class="fa fa-check"></i></button>
	{{else}}
		<button type="button" class="btn btn-xs btn-address" id="btn-{{ id }}" onClick="setDefault({{ id }})"><i class="fa fa-check"></i></button>
	{{/if}}
		<button type="button" class="btn btn-xs btn-warning" onClick="editAddress({{ id }})"><i class="fa fa-pencil"></i></button>
		<button type="button" class="btn btn-xs btn-danger" onClick="removeAddress({{ id }})"><i class="fa fa-trash"></i></button>
	</td>
</tr>
{{/each}}
</script>
