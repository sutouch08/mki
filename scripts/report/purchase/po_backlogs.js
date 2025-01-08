var HOME = BASE_URL + 'report/purchase/po_backlogs/';

function toggleAllVendor(option){
  $('#allVendor').val(option);
  if(option == 1){
    $('#btn-vendor-all').addClass('btn-primary');
    $('#btn-vendor-range').removeClass('btn-primary');
    $('#vendorFrom').val('');
    $('#vendorFrom').attr('disabled', 'disabled');
    $('#vendorTo').val('');
    $('#vendorTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-vendor-all').removeClass('btn-primary');
    $('#btn-vendor-range').addClass('btn-primary');
    $('#vendorFrom').removeAttr('disabled');
    $('#vendorTo').removeAttr('disabled');
    $('#vendorFrom').focus();
  }
}


function toggleAllPO(option){
  $('#allPO').val(option);
  if(option == 1){
    $('#btn-po-all').addClass('btn-primary');
    $('#btn-po-range').removeClass('btn-primary');
    $('#poFrom').val('');
    $('#poFrom').attr('disabled', 'disabled');
    $('#poTo').val('');
    $('#poTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-po-all').removeClass('btn-primary');
    $('#btn-po-range').addClass('btn-primary');
    $('#poFrom').removeAttr('disabled');
    $('#poTo').removeAttr('disabled');
    $('#poFrom').focus();
  }
}


function toggleItem(option){
  $('#isItem').val(option);
  if(option == 1){
    $('#btn-item').addClass('btn-primary');
    $('#btn-style').removeClass('btn-primary');
    $('#styleFrom').addClass('hide');
    $('#itemFrom').removeClass('hide');
    $('#styleTo').addClass('hide');
    $('#itemTo').removeClass('hide');
    return
  }

  if(option == 0){
    $('#btn-style').addClass('btn-primary');
    $('#btn-item').removeClass('btn-primary');
    $('#itemFrom').addClass('hide');
    $('#styleFrom').removeClass('hide');
    $('#itemTo').addClass('hide');
    $('#styleTo').removeClass('hide');
    return
  }
}


function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary')
    $('#btn-pd-range').removeClass('btn-primary')
    $('.pd').val('');
    $('.pd').attr('disabled', 'disabled')
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary')
    $('#btn-pd-range').addClass('btn-primary')
    $('.pd').removeAttr('disabled');
  }

}



$('#vendorFrom').autocomplete({
  source:BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
    var vendor = arr[0];
    $(this).val(vendor);
    if(vendor.length){
      var vendorTo = $('#vendorTo').val();
      if(vendorTo.length > 0){
        if(vendor > vendorTo){
          $('#vendorTo').val(vendor);
          $(this).val(vendorTo);
        }
      }
    }

    $('#vendorTo').focus();
  }
});



$('#itemFrom').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    var pdFrom = $.trim($(this).val());
    if(pdFrom.length && pdFrom != 'not found'){
      var pdTo = $('#itemTo').val();
      if(pdTo.length){
        if(pdFrom > pdTo){
          $('#itemTo').val(pdFrom);
          $('#itemFrom').val(pdTo);
        }
      }
    }
  }
})



$('#itemTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    var pdTo = $.trim($(this).val());
    if(pdTo.length && pdTo != 'not found'){
      var pdFrom = $('#itemFrom').val();
      if(pdFrom.length){
        if(pdFrom > pdTo){
          $('#itemTo').val(pdFrom);
          $('#itemFrom').val(pdTo);
        }
      }
    }
  }
})



$('#styleFrom').autocomplete({
  source:BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function(){
    var pdFrom = $.trim($(this).val());
    if(pdFrom.length && pdFrom != 'not found'){
      var pdTo = $('#styleTo').val();
      if(pdTo.length){
        if(pdFrom > pdTo){
          $('#styleTo').val(pdFrom);
          $('#styleFrom').val(pdTo);
        }
      }
    }
  }
})



$('#styleTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function(){
    var pdTo = $.trim($(this).val());
    if(pdTo.length && pdTo != 'not found'){
      var pdFrom = $('#styleFrom').val();
      if(pdFrom.length){
        if(pdFrom > pdTo){
          $('#styleTo').val(pdFrom);
          $('#styleFrom').val(pdTo);
        }
      }
    }
  }
})


$('#vendorTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
    var vendor = arr[0];
    $(this).val(vendor);
    if(vendor.length){
      var vendorFrom = $('#vendorFrom').val();
      if(vendorFrom.length > 0){
        if(vendor < vendorFrom){
          $('#vendorFrom').val(vendor);
          $(this).val(vendorFrom);
        }
      }
    }
  }
});


$('#poFrom').autocomplete({
  source:BASE_URL + 'auto_complete/get_all_po_code',
  autoFocus:true,
  close:function(){
    var poFrom = $.trim($(this).val());
    if(poFrom.length && poFrom != 'no_content'){
      var poTo = $('#poTo').val();
      if(poTo.length){
        if(poFrom > poTo){
          $('#poFrom').val(poTo)
          $('#poTo').val(poFrom)
        }
      }
    }
  }
})



$('#poTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_all_po_code',
  autoFocus:true,
  close:function(){
    var poTo = $.trim($(this).val());
    if(poTo.length && poTo != 'no_content'){
      var poFrom = $('#poFrom').val();
      if(poFrom.length){
        if(poFrom > poTo){
          $('#poFrom').val(poTo)
          $('#poTo').val(poFrom)
        }
      }
    }
  }
})




//--- Date picker
$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option','maxDate', sd);
  }
});


function getReport(){
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  var allVendor = $('#allVendor').val();
  var vendorFrom = $('#vendorFrom').val();
  var vendorTo = $('#vendorTo').val();

  var allPO = $('#allPO').val();
  var poFrom = $('#poFrom').val();
  var poTo = $('#poTo').val();

  var status = $('#status').val();

  var isItem = $('#isItem').val();
  var allProduct = $('#allProduct').val();
  var itemFrom = $('#itemFrom').val();
  var itemTo = $('#itemTo').val();
  var styleFrom = $('#styleFrom').val();
  var styleTo = $('#styleTo').val();

  if(allPO == 0){
    if(poFrom.length == 0){
      $('#poFrom').addClass('has-error');
      swal('Error!', 'ใบสั่งซื้อไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#poFrom').removeClass('has-error');
    }

    if(poTo.length == 0){
      $('#poTo').addClass('has-error');
      swal('Error!', 'ใบสั่งซื้อไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#poTo').removeClass('has-error');
    }
  }else{
    $('#poFrom').removeClass('has-error');
    $('#poTo').removeClass('has-error');
  }


  if(allVendor == 0){
    if(vendorFrom.length == 0){
      $('#vendorFrom').addClass('has-error');
      swal('Error!', 'ผู้ขายไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#vendorFrom').removeClass('has-error');
    }

    if(vendorTo.length == 0){
      $('#vendorTo').addClass('has-error');
      swal('Error!', 'ผู้ขายไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#vendorTo').removeClass('has-error');
    }
  }else{
    $('#vendorFrom').removeClass('has-error');
    $('#vendorTo').removeClass('has-error');
  }


  if(allProduct == 0){
    if(isItem == 1){
      if(itemFrom.length == 0 || itemFrom === 'not found'){
        $('#itemFrom').addClass('has-error');
        swal('Error!', 'สินค้าไม่ถูกต้อง', 'error')
        return false;
      }else{
        $('#itemFrom').removeClass('has-error')
      }

      if(itemTo.length == 0 || itemTo === 'not found'){
        $('#itemTo').addClass('has-error');
        swal('Error!', 'สินค้าไม่ถูกต้อง', 'error')
        return false;
      }else{
        $('#itemTo').removeClass('has-error')
      }
    }


    if(isItem == 0){
      if(styleFrom.length == 0 || styleFrom === 'not found'){
        $('#styleFrom').addClass('has-error');
        swal('Error!', 'สินค้าไม่ถูกต้อง', 'error')
        return false;
      }else{
        $('#styleFrom').removeClass('has-error')
      }

      if(styleTo.length == 0 || styleTo === 'not found'){
        $('#styleTo').addClass('has-error')
        swal('Error!', 'สินค้าไม่ถูกต้อง', 'error')
        return false;
      }else{
        $('#styleTo').removeClass('has-error')
      }
    }
  }

  var data = [
    {'name' : 'fromDate', 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
    {'name' : 'allVendor', 'value' : allVendor},
    {'name' : 'vendorFrom', 'value' : vendorFrom},
    {'name' : 'vendorTo', 'value' : vendorTo},
    {'name' : 'allPO', 'value' : allPO},
    {'name' : 'poFrom', 'value' : poFrom},
    {'name' : 'poTo', 'value' : poTo},
    {'name' : 'status', 'value' : status},
    {'name' : 'isItem', 'value' : isItem},
    {'name' : 'allProduct', 'value' : allProduct},
    {'name' : 'itemFrom', 'value' : itemFrom},
    {'name' : 'itemTo', 'value' : itemTo},
    {'name' : 'styleFrom' , 'value' : styleFrom},
    {'name' : 'styleTo', 'value' : styleTo}
  ];

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:'false',
    data:data,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#rs');
        render(source,  data, output);
      }
    }
  });

}


function doExport(){
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  var allVendor = $('#allVendor').val();
  var vendorFrom = $('#vendorFrom').val();
  var vendorTo = $('#vendorTo').val();

  var allPO = $('#allPO').val();
  var poFrom = $('#poFrom').val();
  var poTo = $('#poTo').val();

  var status = $('#status').val();

  var isItem = $('#isItem').val();
  var allProduct = $('#allProduct').val();
  var itemFrom = $('#itemFrom').val();
  var itemTo = $('#itemTo').val();
  var styleFrom = $('#styleFrom').val();
  var styleTo = $('#styleTo').val();

  if(allPO == 0){
    if(poFrom.length == 0){
      $('#poFrom').addClass('has-error');
      swal('Error!', 'ใบสั่งซื้อไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#poFrom').removeClass('has-error');
    }

    if(poTo.length == 0){
      $('#poTo').addClass('has-error');
      swal('Error!', 'ใบสั่งซื้อไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#poTo').removeClass('has-error');
    }
  }else{
    $('#poFrom').removeClass('has-error');
    $('#poTo').removeClass('has-error');
  }


  if(allVendor == 0){
    if(vendorFrom.length == 0){
      $('#vendorFrom').addClass('has-error');
      swal('Error!', 'ผู้ขายไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#vendorFrom').removeClass('has-error');
    }

    if(vendorTo.length == 0){
      $('#vendorTo').addClass('has-error');
      swal('Error!', 'ผู้ขายไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#vendorTo').removeClass('has-error');
    }
  }else{
    $('#vendorFrom').removeClass('has-error');
    $('#vendorTo').removeClass('has-error');
  }


  if(allProduct == 0){
    if(isItem == 1){
      if(itemFrom.length == 0 || itemFrom === 'not found'){
        $('#itemFrom').addClass('has-error');
        swal('Error!', 'สินค้าไม่ถูกต้อง', 'error')
        return false;
      }else{
        $('#itemFrom').removeClass('has-error')
      }

      if(itemTo.length == 0 || itemTo === 'not found'){
        $('#itemTo').addClass('has-error');
        swal('Error!', 'สินค้าไม่ถูกต้อง', 'error')
        return false;
      }else{
        $('#itemTo').removeClass('has-error')
      }
    }


    if(isItem == 0){
      if(styleFrom.length == 0 || styleFrom === 'not found'){
        $('#styleFrom').addClass('has-error');
        swal('Error!', 'สินค้าไม่ถูกต้อง', 'error')
        return false;
      }else{
        $('#styleFrom').removeClass('has-error')
      }

      if(styleTo.length == 0 || styleTo === 'not found'){
        $('#styleTo').addClass('has-error')
        swal('Error!', 'สินค้าไม่ถูกต้อง', 'error')
        return false;
      }else{
        $('#styleTo').removeClass('has-error')
      }
    }
  }


  var token = $('#token').val();
  get_download(token);

  $('#reportForm').submit();

}
