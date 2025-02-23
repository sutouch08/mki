<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center">เพิ่มฐานข้อมูลลูกค้า</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-4 col-xs-6 padding-5">
            <label>รหัส</label>
            <input type="text" class="form-control input-sm" id="customer_code" maxlength="15" value="" onkeyup="validCode(this)" />
          </div>
          <div class="col-sm-8 col-xs-6 padding-5">
            <label>ชื่อ</label>
            <input type="text" class="form-control input-sm" id="customer_name" value=""  />
          </div>
          <div class="col-sm-6 col-xs-6 padding-5">
            <label>Tax ID</label>
            <input type="text" class="form-control input-sm" id="customer_tax_id" maxlength="32" value="" />
          </div>

          <div class="col-sm-6 col-xs-6 padding-5">
            <label>กลุ่มลูกค้า</label>
            <select id="customer_group" class="form-control input-sm">
              <option value="">เลือก</option>
              <?php echo select_customer_group(); ?>
            </select>
          </div>
          <div class="col-sm-6 col-xs-6 padding-5">
            <label>ประเภทลูกค้า</label>
            <select id="customer_kind" class="form-control input-sm">
              <option value="">เลือก</option>
              <?php echo select_customer_kind(); ?>
            </select>
          </div>

          <div class="col-sm-6 col-xs-6 padding-5">
            <label>ชนิดลูกค้า</label>
            <select id="customer_type" class="form-control input-sm">
              <option value="">เลือก</option>
              <?php echo select_customer_type(); ?>
            </select>
          </div>

          <div class="col-sm-6 col-xs-6 padding-5">
            <label>เกรดลูกค้า</label>
            <select id="customer_class" class="form-control input-sm">
              <option value="">เลือก</option>
              <?php echo select_customer_class(); ?>
            </select>
          </div>

          <div class="col-sm-6 col-xs-6 padding-5">
            <label>พื้นที่ขาย</label>
            <select id="customer_area" class="form-control input-sm">
              <option value="">เลือก</option>
              <?php echo select_customer_area(); ?>
            </select>
          </div>

          <div class="col-sm-6 col-xs-6 padding-5">
            <label>พนักงานขาย</label>
            <select id="customer_sale" class="form-control input-sm">
              <?php echo select_sale(); ?>
            </select>
          </div>

          <div class="col-sm-6 col-xs-6 padding-5">
            <label>เครดิตเทอม(วัน)</label>
            <input type="number" id="credit_term" class="form-control input-sm" value="0" />
          </div>

          <div class="col-sm-6 col-xs-6 padding-5">
            <label>วงเงินเครดิต</label>
            <input type="number" id="credit_amount" class="form-control input-sm" value="0.00" />
          </div>

        </div><!-- row -->

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onClick="saveCustomer()" ><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="channelsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center">เพิ่มช่องทางการขาย</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-6 col-xs-6 padding-5">
            <label>รหัส</label>
            <input type="text" class="form-control input-sm" id="channels_code" maxlength="15" value="" onkeyup="validCode(this)" />
          </div>
          <div class="col-sm-6 col-xs-6 padding-5">
            <label>ชื่อ</label>
            <input type="text" class="form-control input-sm" id="channels_name" value=""  />
          </div>

        </div><!-- row -->

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onClick="saveChannels()" ><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center">เพิ่มช่องทางการชำระเงิน</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-6 col-xs-6 padding-5">
            <label>รหัส</label>
            <input type="text" class="form-control input-sm" id="payment_code" maxlength="15" value="" onkeyup="validCode(this)" />
          </div>
          <div class="col-sm-6 col-xs-6 padding-5">
            <label>ชื่อ</label>
            <input type="text" class="form-control input-sm" id="payment_name" value=""  />
          </div>
          <div class="col-sm-6 col-xs-6 padding-5">
            <label>ประเภท</label>
            <select name="role" id="role" class="form-control input-sm">
              <option value="">โปรดเลือก</option>
              <?php echo select_payment_role(); ?>
            </select>
          </div>
          <div class="col-sm-6 col-xs-6 padding-5 text-center">
            <label class="display-block not-show">&nbsp;</label>
            <label>
              <input type="checkbox" class="ace" name="term" id="term" value="1" />
              <span class="lbl">&nbsp; &nbsp;เครติดเทอม</span>
            </label>
          </div>
        </div><!-- row -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onClick="savePayment()" ><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="tagsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:300px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center">Order tags</h4>
      </div>
      <div class="modal-body">
        <div class="width-100 padding-5">
          <label>Tags</label>
          <input type="text" class="form-control input-sm e" maxlength="50" id="tags-name" value="" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success btn-100" onClick="addTags()" >Add</button>
      </div>
    </div>
  </div>
</div>
