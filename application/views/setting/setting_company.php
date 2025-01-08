<?php
$vat_yes = $USE_VAT == 1 ? 'btn-success' : '' ;
$vat_no = $USE_VAT == 0 ? 'btn-success' : '';
 ?>
<div class="tab-pane fade active in" id="company">
	<form id="companyForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
    	<div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">แบรนด์สินค้า</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-medium" name="COMPANY_NAME" id="brand" value="<?php echo $COMPANY_NAME; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">ชื่อบริษัท</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-xlarge" name="COMPANY_FULL_NAME" id="cName" value="<?php echo $COMPANY_FULL_NAME; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">ที่อยู่บรรทัด 1</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm" name="COMPANY_ADDRESS1" id="cAddress1" placeholder="เลขที่ หมู่ ถนน ตำบล" value="<?php echo $COMPANY_ADDRESS1; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">ที่อยู่บรรทัด 2</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm" name="COMPANY_ADDRESS2" id="cAddress2" placeholder="อำเภอ จังหวัด" value="<?php echo $COMPANY_ADDRESS2; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">รหัสไปรษณีย์</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-small" name="COMPANY_POST_CODE" id="postCode" value="<?php echo $COMPANY_POST_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">โทรศัพท์</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-large" name="COMPANY_PHONE" id="phone" value="<?php echo $COMPANY_PHONE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">แฟกซ์</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-large" name="COMPANY_FAX_NUMBER" id="fax" value="<?php echo $COMPANY_FAX_NUMBER; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">อีเมล์</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-large" name="COMPANY_EMAIL" id="email" value="<?php echo $COMPANY_EMAIL; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">เลขประจำตัวผู้เสียภาษี</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-large" name="COMPANY_TAX_ID" id="taxID" value="<?php echo $COMPANY_TAX_ID; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">ปีที่เริ่มต้นกิจการ (ค.ศ.)</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="number" class="form-control input-sm input-mini text-center" name="START_YEAR" id="startYear" value="<?php echo $START_YEAR; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3 col-xs-12"><span class="form-control left-label">VAT</span></div>
			<div class="col-sm-9 col-xs-12">
				<div class="btn-group input-medium">
					<button type="button" class="btn btn-sm <?php echo $vat_yes; ?>" style="width:50%;" id="btn-vat-yes" onClick="toggleVAT(1)">ใช้</button>
					<button type="button" class="btn btn-sm <?php echo $vat_no; ?>" style="width:50%;" id="btn-vat-no" onClick="toggleVAT(0)">ไม่ใช้</button>
				</div>
				<span class="help-block">เปิด/ปิด ระบบ VAT</span>
				<input type="hidden" name="USE_VAT" id="use_vat" value="<?php echo $USE_VAT; ?>" />
			</div>
			<div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">อัตราภาษีซื้อ</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_RATE" value="<?php echo $PURCHASE_VAT_RATE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3 col-xs-12">
        <span class="form-control left-label">อัตราภาษีขาย</span>
      </div>
      <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control input-sm input-small" name="SALE_VAT_RATE" value="<?php echo $SALE_VAT_RATE; ?>" />
      </div>
      <div class="divider-hidden"></div>
			<div class="divider-hidden"></div>
      <div class="col-sm-9 col-sm-offset-3">
        <button type="button" class="btn btn-sm btn-success" onClick="checkCompanySetting()">
          <i class="fa fa-save"></i> บันทึก
        </button>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
</div>
