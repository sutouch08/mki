var HOME = BASE_URL + 'report/sales/sales_by_channels/';

function toggleAllChannels(option){
  $('#allChannels').val(option);
  if(option == 1){
    $('#btn-ch-all').addClass('btn-primary');
    $('#btn-ch-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-ch-all').removeClass('btn-primary');
    $('#btn-ch-range').addClass('btn-primary');
    $('#channels-modal').modal('show');
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
  var allChannels = $('#allChannels').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();
	var order_by = $('#orderBy').val();

	if(allChannels == 0){
		var count = $('.chk:checked').length;
		if(count == 0){
			$('#channels-modal').modal('show');
			return false;
		}
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
    {'name' : 'allChannels' , 'value' : allChannels},
    {'name' : 'fromDate' , 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
		{'name' : 'orderBy', 'value' : order_by}
  ];


  if(allChannels == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'channels['+index+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

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
	var allChannels = $('#allChannels').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

	if(allChannels == 0){
		var count = $('.chk:checked').length;
		if(count == 0){
			$('#channels-modal').modal('show');
			return false;
		}
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
