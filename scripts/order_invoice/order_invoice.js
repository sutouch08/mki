var HOME = BASE_URL + 'orders/order_invoice/';

function goBack() {
	window.location.href = HOME;
}

function addNew() {
	window.location.href = HOME + 'add_new';
}


function goEdit(code) {
	window.location.href = HOME + 'edit/'+code;
}


function view_detail(code) {
	window.location.href = HOME + 'view_detail/'+code;
}


function getDelete(code) {
	if(code === undefined){
		var code = $('#code').val();
	}

	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการยกเลิก '+code+' หรือไม่?',
		type:'warning',
		showCancelButton:true,
		confirmButtonColor:'#DD6B55',
		confirmButtonText:'ใช่ ต้องการยกเลิก',
		cancelButtonText:'ไม่ใช่',
		closeOnConfirm:false
	},
	function() {
		load_in();
		$.ajax({
			url:HOME + 'cancle_invoice',
			type:'POST',
			cache:false,
			data:{
				'code' : code
			},
			success:function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Deleted',
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						window.location.reload();
					},1200);
				}
				else {
					load_out();
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			},
			error:function(xhr, status, error) {
				load_out();
				var errorMessage = xhr.status + ': '+xhr.statusText;
				swal({
					title:'Error!',
					text:'Error-'+errorMessage,
					type:'error'
				});
			}
		})
	});
}




function printSelectedInvoice(option) {
	var data = [];
	var url = '';

	$('.chk').each(function() {
		if($(this).is(':checked')) {
			data.push($(this).val());
		}
	});

	if(option == 'tax') {
		url = BASE_URL + "orders/order_invoice/print_selected_tax_invoice";
	}
	else {
		url = BASE_URL + "orders/order_invoice/print_selected_do_invoice";
	}

	if(data.length) {

			var mapForm = document.createElement("form");
		 mapForm.target = "Map";
		 mapForm.method = "POST";
		 mapForm.action = url;

		 var mapInput = document.createElement("input");
		 mapInput.type = "text";
		 mapInput.name = "data";
		 mapInput.value = data;
		 mapForm.appendChild(mapInput);

		 document.body.appendChild(mapForm);

		 var center = ($(document).width() - 800)/2;
		 var prop = "width=800, height=900, left="+center+", scrollbars=yes";

		 map = window.open("", "Map", prop);

			if (map) {

				 mapForm.submit();
			}
			else {
				 alert('You must allow popups for this map to work.');
			}
	}
}


function print_invoice() {
	//--- properties for print
	var center = ($(document).width() - 800)/2;
	var prop = "width=800, height=900, left="+center+", scrollbars=yes";
	var code = $('#code').val();
	var target  = HOME + 'print_invoice/'+code;
  window.open(target, '_blank', prop);
}

function clearFilter() {
	$.get(HOME+'clear_filter', function() {
		goBack();
	});
}


function print_do_invoice() {
	//--- properties for print
	var center = ($(document).width() - 800)/2;
	var prop = "width=800, height=900, left="+center+", scrollbars=yes";
	var code = $('#code').val();
	var target  = HOME + 'print_do_invoice/'+code;
  window.open(target, '_blank', prop);
}

function print_do_receipt() {
	//--- properties for print
	var center = ($(document).width() - 800)/2;
	var prop = "width=800, height=900, left="+center+", scrollbars=yes";
	var code = $('#code').val();
	var target  = HOME + 'print_do_receipt/'+code;
  window.open(target, '_blank', prop);
}

function print_tax_receipt() {
	//--- properties for print
	var center = ($(document).width() - 800)/2;
	var prop = "width=800, height=900, left="+center+", scrollbars=yes";
	var code = $('#code').val();
	var target  = HOME + 'print_tax_receipt/'+code;
  window.open(target, '_blank', prop);
}


function print_tax_invoice() {
	//--- properties for print
	var center = ($(document).width() - 800)/2;
	var prop = "width=800, height=900, left="+center+", scrollbars=yes";
	var code = $('#code').val();
	var target  = HOME + 'print_tax_invoice/'+code;
  window.open(target, '_blank', prop);
}


function print_tax_billing_note() {
	//--- properties for print
	var center = ($(document).width() - 800)/2;
	var prop = "width=800, height=900, left="+center+", scrollbars=yes";
	var code = $('#code').val();
	var target  = HOME + 'print_tax_billing_note/'+code;
  window.open(target, '_blank', prop);
}

$('#from_date').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd) {
		$('#to_date').datepicker('option', 'minDate', sd);
	}
})

$('#to_date').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd) {
		$('#from_date').datepicker('option', 'maxDate', sd);
	}
})


$('#doc_date').datepicker({
	dateFormat:'dd-mm-yy'
});


$('#customer_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$('#customerCode').val(arr[0]); //--- for check with customer
			$('#customer_code').val(arr[0]);
			$('#customer_name').val(arr[1]);
		}
		else {
			$('#customerCode').val('');
			$('#customer_code').val('');
			$('#customer_name').val('');
		}
	}
})


function add() {
	var doc_date = $('#doc_date').val();
	var customerCode = $.trim($('#customerCode').val());
	var customer_code = $.trim($('#customer_code').val());
	var customer_name = $.trim($('#customer_name').val());
	var vat_type = $('#vat_type').val();
	var remark = $.trim($('#remark').val());

	if(customer_code.length === 0 || (customer_code != customerCode)) {
		swal("รหัสลูกค้าไม่ถูกต้อง");
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'doc_date' : doc_date,
			'customer_code' : customer_code,
			'vat_type' : vat_type,
			'remark' : remark
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				if(ds.status === 'success') {
					goEdit(ds.code);
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					});
				}
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


function getEdit(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}

function updateHeader(){
	var code = $('#code').val();
	var doc_date = $('#doc_date').val();
	var customerCode = $.trim($('#customerCode').val());
	var customer_code = $.trim($('#customer_code').val());
	var customer_name = $.trim($('#customer_name').val());
	var vat_type = $('#vat_type').val();
	var remark = $.trim($('#remark').val());

	if(customer_code.length === 0 || (customer_code != customerCode)) {
		swal("รหัสลูกค้าไม่ถูกต้อง");
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'doc_date' : doc_date,
			'customer_code' : customer_code,
			'vat_type' : vat_type,
			'remark' : remark
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Updated',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1200);
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


function getSearch() {
	$('#searchForm').submit();
}
