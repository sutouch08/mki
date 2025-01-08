<!--  Add New Address Modal  --------->
<div class="modal fade" id="logsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center" >ประวัติ</h4>
      </div>
      <div class="modal-body">
        <div class="row" style="margin-left:0; margin-right:0;">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="logs-detail" style="min-height:100px; max-height:300px; overflow:auto;">

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-xs btn-default btn-100" onclick="closeModal('logsModal')">Close</button>
      </div>
    </div>
  </div>
</div>

<script id="logs-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}
      --- Not Found ---
    {{else}}
      <p class="p-logs">
        {{name}}  โดย  {{uname}}  วันที่ {{date}}
      </p>
    {{/if}}
  {{/each}}
</script>

<script>
  function viewLogs(id) {
    load_in();

    $.ajax({
      url:HOME + 'get_logs/'+id,
      type:'GET',
      cache:false,
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          let source = $('#logs-template').html();
          let output = $('#logs-detail');

          render(source, ds, output);

          $('#logsModal').modal('show');
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          })
        }
      }
    })
  }
</script>
