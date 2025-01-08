var HOME = BASE_URL + 'account/consign_order/';

function goBack(){
  window.location.href = HOME;
}


function goAdd(){
  window.location.href = HOME + 'add_new';
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


function goEdit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function goCancel(code) {
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
		}, function() {
      $('#cancel-code').val(code);
      $('#cancel-reason').val('');

      setTimeout(() => {
        cancelReason();
      }, 200);
	});
}


function cancelReason() {
	$('#cancel-modal').modal('show');
}


$('#cancel-modal').on('shown.bs.modal', function() {
	$('#cancel-reason').focus();
});


function doCancel() {
	$('#cancel-reason').clearError();

	let code = $('#cancel-code').val();
	let reason = $('#cancel-reason').val().trim();

	if(reason.length == 0) {
		$('#cancel-reason').hasError().focus();
		return false;
	}

	$('#cancel-modal').modal('hide');

	load_in();

	setTimeout(() => {
		$.ajax({
			url:HOME + 'cancel',
			type:'POST',
			cache:false,
			data:{
				'code' : code,
				'reason' : reason
			},
			success:function(rs) {
				load_out();

        if(rs.trim() == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        }
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			},
			error:function(xhr) {
				load_out();
				swal({
					title:'Error!',
					text:xhr.responseText,
					type:'error',
					html:true
				});
			}
		});
	}, 200);
}


function getSearch(){
	$("#searchForm").submit();
}


$(".search").keyup(function(e){
	if( e.keyCode == 13 ){
		getSearch();
	}
});



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


function printConsignOrder() {
  let code = $('#consign_code').val();
  let width = 800;
  let height = 900;
	let center = ($(document).width() - width)/2;
  let target = HOME + 'print_consign/'+ code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });
}
