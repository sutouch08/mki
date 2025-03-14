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



function getReport() {
  clearErrorByClass('e');

  let h = {
    'allChannels' : $('#allChannels').val(),
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'sold_date' : $('#sold_date').val(),
    'order_by' : $('#orderBy').val(),
    'wm_channels' : $('#wm-channels').is(':checked') ? 1 : 0,
    'channels' : []
  };

	if(h.allChannels == 0){
		var count = $('.chk:checked').length;
		if(count == 0){
			$('#channels-modal').modal('show');
			return false;
		}
	}

  if(! isDate(h.fromDate) || ! isDate(h.toDate)){
    swal('วันที่ไม่ถูกต้อง');
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    return false;
  }

  if(h.allChannels == 0) {
    $('.chk:checked').each(function(index, el) {
      let code = $(this).val();
      let name = $(this).data('name');
      h.channels.push({'code':code, 'name':name});
    });
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:'false',
    data: {
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          var source = $('#template').html();
          var output = $('#rs');
          render(source,  ds.data, output);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });

}


function doExport() {
  clearErrorByClass('e');

  let h = {
    'allChannels' : $('#allChannels').val(),
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'sold_date' : $('#sold_date').val(),
    'order_by' : $('#orderBy').val(),
    'wm_channels' : $('#wm-channels').is(':checked') ? 1 : 0,
    'channels' : []
  };

	if(h.allChannels == 0){
		var count = $('.chk:checked').length;
		if(count == 0){
			$('#channels-modal').modal('show');
			return false;
		}
	}

  if(! isDate(h.fromDate) || ! isDate(h.toDate)){
    swal('วันที่ไม่ถูกต้อง');
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    return false;
  }

  if(h.allChannels == 0) {
    $('.chk:checked').each(function(index, el) {
      let code = $(this).val();
      let name = $(this).data('name');
      h.channels.push({'code':code, 'name':name});
    });
  }

  $('#data').val(JSON.stringify(h));
  let token = generateUID();
  $('#token').val(token);
	get_download(token);
  $('#reportForm').submit();
}
