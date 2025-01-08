<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:250px;">
    <div class="modal-content">
      <div class="modal-header text-center">ระบุจำนวนรายการล่าสุด</div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            <input type="number" class="form-control input-sm input-mini text-center focus" id="view-qty" value="10" style="margin-left:auto; margin-right:auto;"/>
          </div>
          <div class="divider-hidden"></div>
          <div class="divider-hidden"></div>
          <div class="divider-hidden"></div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <button type="button" class="btn btn-xs btn-default btn-block" onclick="closeModal('viewModal')">Cancel</button>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <button type="button" class="btn btn-xs btn-info btn-block" onclick="viewHistory()">แสดงรายการ</button>
          </div>
        </div>
      </div><!--- modal-body -->
    </div>
  </div>
</div>



<script id="checked-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{bc_id}}">
    <td>{{barcode}}</td>
    <td>{{code}}</td>
    <td class="text-right" id="{{bc_id}}">{{qty}}</td>
  </tr>
</script>

<script id="check-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{id}}">
    <td>{{barcode}}</td>
    <td>{{code}}</td>
    <td class="text-center" id="qty-{{id}}">{{qty}}</td>
    <td class="text-center">{{timestamp}}</td>
    <td class="text-center">
      <input type="checkbox" class="chk" id="chk-{{id}}" value="{{id}}" data-id="{{id}}" data-bcid="{{bc_id}}" data-qty="{{qty}}" />
    </td>
  </tr>
</script>

<script id="history-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{id}}">
      <td>{{barcode}}</td>
      <td>{{code}}</td>
      <td class="text-center" id="qty-{{id}}">{{qty}}</td>
      <td class="text-center">{{timestamp}}</td>
      <td class="text-center">
        <input type="checkbox" class="chk" id="chk-{{id}}" value="{{id}}" data-id="{{id}}" data-bcid="{{bc_id}}" data-qty="{{qty}}"/>
      </td>
    </tr>
  {{/each}}
</script>
