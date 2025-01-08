<!--  Add New Address Modal  --------->
<div class="modal fade" id="attributeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center" id="title"></h4>
								<input type="hidden" id="attribute" value=""/>
            </div>
            <div class="modal-body">
            <div class="row">
							<div class="col-sm-12 col-xs-12">
								<label>รหัส</label>
								<input type="text" class="form-control input-sm margin-bottom-10" id="a_code" maxlength="20" value="" onkeyup="validCode(this)" />
							</div>
							<div class="col-sm-12 col-xs-12">
								<label>ชื่อ</label>
								<input type="text" class="form-control input-sm" id="a_name" maxlength="100" value=""  />
							</div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" onClick="saveAttribute()" ><i class="fa fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>
