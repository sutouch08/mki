var HOME = BASE_URL + 'report/follow/order_backlogs/';

function toggleAllCustomer(option) {
	$('#allCustomer').val(option);

	if(option == 1) {
		$('#btn-cust-all').addClass('btn-primary');
		$('#btn-cust-range').removeClass('btn-primary');
		$('#fromCustomer').val('').attr('disabled', 'disabled');
		$('#toCustomer').val('').attr('disabled', 'disabled');
		return;
	}

	if(option == 0) {
		$('#btn-cust-all').removeClass('btn-primary');
		$('#btn-cust-range').addClass('btn-primary');
		$('#fromCustomer').val('').removeAttr('disabled');
		$('#toCustomer').val('').removeAttr('disabled');
		$('#fromCustomer').focus();
	}
}



function toggleAllDate(option) {
	$('#allDate').val(option);

	if(option == 1) {
		$('#btn-date-all').addClass('btn-primary');
		$('#btn-date-range').removeClass('btn-primary');
		$('#fromDate').val('').attr('disabled', 'disabled');
		$('#toDate').val('').attr('disabled', 'disabled');
		return;
	}

	if(option == 0) {
		$('#btn-date-all').removeClass('btn-primary');
		$('#btn-date-range').addClass('btn-primary');
		$('#fromDate').removeAttr('disabled');
		$('#toDate').removeAttr('disabled');
	}
}



function toggleAllChannels(option) {
	$('#allChannels').val(option);

	if(option == 1) {
		$('#btn-ch-all').addClass('btn-primary');
		$('#btn-ch-range').removeClass('btn-primary');
		return;
	}

	if(option == 0) {
		$('#btn-ch-all').removeClass('btn-primary');
		$('#btn-ch-range').addClass('btn-primary');
		$('#channels-modal').modal('show');
	}
}


function toggleAllPayment(option) {
	$('#allPayment').val(option);

	if(option == 1) {
		$('#btn-pm-all').addClass('btn-primary');
		$('#btn-pm-range').removeClass('btn-primary');
		return;
	}

	if(option == 0) {
		$('#btn-pm-all').removeClass('btn-primary');
		$('#btn-pm-range').addClass('btn-primary');
		$('#payment-modal').modal('show');
	}
}



$('#fromDate').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd) {
		$('#toDate').datepicker('option', 'minDate', sd);
	}
});

$('#toDate').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd) {
		$('#fromDate').datepicker('option', 'maxDate', sd);
	}
})


$('#fromCustomer').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
			var from = arr[0];
			var to = $('#toCustomer').val();

			if(to.length && from > to) {
				$('#fromCustomer').val(to);
				$('#toCustomer').val(from);
			}
		}
		else {
			$(this).val('');
		}
	}
})


$('#toCustomer').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
			var from = $('#fromCustomer').val();
			var to = arr[0];

			if(from.length && from > to) {
				$('#fromCustomer').val(to);
				$('#toCustomer').val(from);
			}
		}
		else {
			$(this).val('');
		}
	}
})



function getReport() {
	var allCustomer = $('#allCustomer').val();
	var fromCustomer = $('#fromCustomer').val();
	var toCustomer = $('#toCustomer').val();

	var allDate = $('#allDate').val();
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();

	var allChannels = $('#allChannels').val();
	var allPayment = $('#allPayment').val();

	if(allCustomer == 0) {
		if(fromCustomer.length == 0) {
			$('#fromCustomer').addClass('has-error');
			swal('รหัสลูกค้าไม่ถูกต้อง');
			return false;
		}
		else {
			$('#fromCustomer').removeClass('has-error');
		}

		if(toCustomer.length == 0) {
			$('#toCustomer').addClass('has-error');
			swal('รหัสลูกค้าไม่ถูกต้อง');
			return false;
		}
		else {
			$('#toCustomer').removeClass('has-error');
		}

	} //-- end if customer


	if(allDate == 0) {
		if(!isDate(fromDate)) {
			$('#fromDate').addClass('has-error');
			swal('วันที่ไม่ถูกต้อง');
			return false;
		}
		else {
			$('#fromDate').removeClass('has-error');
		}

		if(!isDate(toDate)) {
			$('#toDate').addClass('has-error');
			swal('วันที่ไม่ถูกต้อง');
			return false;
		}
		else {
			$('#toDate').removeClass('has-error');
		}
	}


	if(allChannels == 0){
    var count = $('.chk:checked').length;
    if(count == 0){
      $('#channels-modal').modal('show');
      return false;
    }
  }

	if(allPayment == 0){
    var count = $('.pm:checked').length;
    if(count == 0){
      $('#payment-modal').modal('show');
      return false;
    }
  }

	var data = [
    {'name' : 'allCustomer', 'value' : allCustomer},
		{'name' : 'fromCustomer', 'value' : fromCustomer},
    {'name' : 'toCustomer', 'value' : toCustomer},
    {'name' : 'allDate' , 'value' : allDate},
		{'name' : 'fromDate' , 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
		{'name' : 'allChannels', 'value' : allChannels},
		{'name' : 'allPayment', 'value' : allPayment}
  ];


	if(allChannels == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'channels[]';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }


	if(allPayment == 0){
    $('.pm').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'payment[]';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

	load_in();

	$.ajax({
		url:HOME + 'get_report',
		type:'GET',
		cache:false,
		data:data,
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var data = $.parseJSON(rs);
				var source = $('#template').html();
				var output = $('#rs');

				render(source, data, output);
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


function doExport() {
	var allCustomer = $('#allCustomer').val();
	var fromCustomer = $('#fromCustomer').val();
	var toCustomer = $('#toCustomer').val();

	var allDate = $('#allDate').val();
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();

	var allChannels = $('#allChannels').val();
	var allPayment = $('#allPayment').val();

	if(allCustomer == 0) {
		if(fromCustomer.length == 0) {
			$('#fromCustomer').addClass('has-error');
			swal('รหัสลูกค้าไม่ถูกต้อง');
			return false;
		}
		else {
			$('#fromCustomer').removeClass('has-error');
		}

		if(toCustomer.length == 0) {
			$('#toCustomer').addClass('has-error');
			swal('รหัสลูกค้าไม่ถูกต้อง');
			return false;
		}
		else {
			$('#toCustomer').removeClass('has-error');
		}

	} //-- end if customer


	if(allDate == 0) {
		if(!isDate(fromDate)) {
			$('#fromDate').addClass('has-error');
			swal('วันที่ไม่ถูกต้อง');
			return false;
		}
		else {
			$('#fromDate').removeClass('has-error');
		}

		if(!isDate(toDate)) {
			$('#toDate').addClass('has-error');
			swal('วันที่ไม่ถูกต้อง');
			return false;
		}
		else {
			$('#toDate').removeClass('has-error');
		}
	}


	if(allChannels == 0){
    var count = $('.chk:checked').length;
    if(count == 0){
      $('#channels-modal').modal('show');
      return false;
    }
  }

	if(allPayment == 0){
    var count = $('.pm:checked').length;
    if(count == 0){
      $('#payment-modal').modal('show');
      return false;
    }
  }

	var token = $('#token').val();
  get_download(token);

  $('#reportForm').submit();
}
