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
});


function toggleRole(option) {
	$('#role').val(option);

	if(option === 'all') {
		$('#btn-all').addClass('btn-primary');
		$('#btn-sale').removeClass('btn-primary');
		$('#btn-consign').removeClass('btn-primary');
	}
	else if(option === 'S') {
		$('#btn-all').removeClass('btn-primary');
		$('#btn-sale').addClass('btn-primary');
		$('#btn-consign').removeClass('btn-primary');
	}
	else if(option === 'M') {
		$('#btn-all').removeClass('btn-primary');
		$('#btn-sale').removeClass('btn-primary');
		$('#btn-consign').addClass('btn-primary');
	}
}


function doExport() {
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();

	if(!isDate(fromDate) || !isDate(toDate)) {
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	
	var token = $('#token').val();
  get_download(token);

  $('#reportForm').submit();
}
