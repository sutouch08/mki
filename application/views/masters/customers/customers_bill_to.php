<?php
$action = empty($bill) ? $this->home.'/add_bill_to/'.$ds->code : $this->home.'/update_bill_to/'.$ds->code;
$em = empty($bill) ? TRUE : FALSE;

$customer_name = $em ? '' : $bill->customer_name;
$branch_code = $em ? '0000' : $bill->branch_code;
$branch_name = $em ? 'สำนักงานใหญ่' : $bill->branch_name;
$address = $em ? '' : $bill->address;
$sub_district = $em ? '' : $bill->sub_district;
$district = $em ? '' : $bill->district;
$province = $em ? '' : $bill->province;
$postcode = $em ? '' : $bill->postcode;
$country = $em ? 'TH' : $bill->country;
$phone = $em ? '' : $bill->phone;
?>

<form class="form-horizontal" id="billToForm" method="post" action="<?php echo $action; ?>">

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-5 col-md-6 col-sm-7 col-xs-12">
      <input type="text" name="customer_name" id="customer_name" class="form-control input-sm" placeholder="ชื่อสำหรับเปิดบิล" value="<?php echo $customer_name; ?>" required/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">รหัสสาขา</label>
    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6">
      <input type="text" name="branch_code" id="bill_branch_code" class="form-control input-sm code" placeholder="0000" value="<?php echo $branch_code; ?>" />
    </div>
  </div>


  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อสาขา</label>
    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
      <input type="text" name="branch_name" id="bill_branch_name" class="form-control input-sm" placeholder="สำนักงานใหญ่" value="<?php echo $branch_name; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ที่อยู่</label>
    <div class="col-lg-8 col-md-10 col-sm-9 col-xs-12">
      <input type="text" name="address" id="bill_address" class="form-control input-sm" placeholder="อาคาร/หมู่ที่/ถนน **ต้องการ" value="<?php echo $address; ?>" required/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">ตำบล/แขวง</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
      <input type="text" name="sub_district" id="bill_sub_district" class="form-control input-sm" placeholder="ตำบล/แขวง **ต้องการ" value="<?php echo $sub_district; ?>" required/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">อำเภอ/เขต</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
      <input type="text" name="district" id="bill_district" class="form-control input-sm" placeholder="อำเภอ/เขต **ต้องการ" value="<?php echo $district; ?>" required/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">จังหวัด</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
      <input type="text" name="province" id="bill_province" class="form-control input-sm" placeholder="จังหวัด **ต้องการ" value="<?php echo $province; ?>" required/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">รหัสไปรษณีย์</label>
    <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6">
      <input type="text" name="postcode" id="bill_postcode" class="form-control input-sm" placeholder="10110" value="<?php echo $postcode; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">รหัสประเทศ</label>
    <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3">
      <input type="text" name="country" id="bill_country" class="form-control input-sm text-center" placeholder="TH" value="<?php echo $country; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right">โทรศัพท์</label>
    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
      <input type="text" name="phone" id="bill_phone" class="form-control input-sm" placeholder="000 000 0000" value="<?php echo $phone; ?>" />
    </div>
  </div>
  <div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i>&nbsp;&nbsp;Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>

</form>
