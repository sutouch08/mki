var HOME = BASE_URL + 'report/sales/sales_by_product/';

function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').val('');
    $('#pdTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary');
    $('#btn-pd-range').addClass('btn-primary');
    $('#pdFrom').removeAttr('disabled');
    $('#pdTo').removeAttr('disabled');
    $('#pdFrom').focus();
  }
}


function toggleOrderBy(option){
  $('#orderBy').val(option);
  if(option == 'amount'){
    $('#btn-amount').addClass('btn-primary');
    $('#btn-qty').removeClass('btn-primary');
    return
  }

  if(option == 'qty'){
    $('#btn-amount').removeClass('btn-primary');
    $('#btn-qty').addClass('btn-primary');
  }
}


$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length === 2){
      var pdFrom = arr[0];
      $(this).val(pdFrom);
      var pdTo = $('#pdTo').val();
      if(pdTo.length > 0){
        if(pdFrom > pdTo){
          $('#pdTo').val(pdFrom);
          $('#pdFrom').val(pdTo);
        }
      }else{
        $('#pdTo').focus();
      }
    }else{
      $(this).val('');
    }
  }
});


$('#pdTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length === 2){
      var pdTo = arr[0];
      $(this).val(pdTo);
      var pdFrom = $('#pdFrom').val();
      if(pdFrom.length > 0){
        if(pdFrom > pdTo){
          $('#pdTo').val(pdFrom);
          $('#pdFrom').val(pdTo);
        }
      }else{
        $('#pdFrom').focus();
      }
    }else{
      $(this).val('');
    }
  }
})


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});



function getReport(){
  var allProduct = $('#allProduct').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();
	var order_by = $('#orderBy').val();

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(! isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    return false;
  }else{
    $('#fromDate').removeClass('has-error');
    $('#toDate').removeClass('has-error');
  }

  var data = [
    {'name' : 'allProduct' , 'value' : allProduct},
    {'name' : 'fromDate' , 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
    {'name' : 'pdFrom', 'value' : pdFrom},
    {'name' : 'pdTo', 'value' : pdTo},
		{'name' : 'orderBy', 'value' : order_by}
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
	var allProduct = $('#allProduct').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(! isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    return false;
  }else{
    $('#fromDate').removeClass('has-error');
    $('#toDate').removeClass('has-error');
  }

	var token = $('#token').val();
	get_download(token);
  $('#reportForm').submit();

}
