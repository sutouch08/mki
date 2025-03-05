// function addCustomer() {
// 	$('#customerModal').modal('show');
// }
//--- properties for print
var center    = ($(document).width() -800)/2;
var prop 			= "width=800, height=900. left="+center+", scrollbars=yes";


function addCustomer() {
	var target = BASE_URL + 'masters/customers/add_new';
	window.open(target, '_blank', prop);
}

function addChannels() {
	var target = BASE_URL + 'masters/channels/add_new';
	window.open(target, '_blank', prop);
}

function addPayment() {
	var target = BASE_URL + 'masters/payment_methods/add_new';
	window.open(target, '_blank', prop);
}

// $('#customerModal').on('shown.bs.modal', function(){
// 	$('#customer_code').focus();
// })
//
// $('#customer_code').focusout(function() {
//   setTimeout(() => {
//     let val = $(this).val();
//     let res  = val.toUpperCase();
//     $(this).val(res);
//   }, 100);
// })
//
// function saveCustomer() {
// 	var code = $('#customer_code').val();
// 	var name = $('#customer_name').val();
// 	var tax_id = $('#tax_id').val();
// 	var customer_group = $('#customer_group').val();
// 	var customer_kind = $('#customer_kind').val();
// 	var customer_type = $('#customer_type').val();
// 	var customer_class = $('#customer_class').val();
// 	var customer_area = $('#customer_area').val();
// 	var sale = $("#customer_sale").val();
// 	var credit_term = $('#credit_term').val();
// 	var credit_amount = $('#credit_amount').val();
//
// 	if(code.length == 0) {
// 		$('#customer_code').addClass('has-error');
// 		$('#customer_code').focus();
// 		return false;
// 	}
// 	else {
// 		$('#customer_code').removeClass('has-error');
// 	}
//
// 	if(name.length == 0) {
// 		$('#customer_name').addClass('has-error');
// 		$('#customer_name').focus();
// 		return false;
// 	}
// 	else {
// 		$('#customer_name').removeClass('has-error');
// 	}
//
// 	$('#customerModal').modal('hide');
//
// 	load_in();
// 	$.ajax({
// 		url:BASE_URL + 'masters/customers/add',
// 		type:'POST',
// 		cache:false,
// 		data:{
// 			'code' : code,
// 			'name' : name,
// 			'Tax_Id' : tax_id,
// 			'group' : customer_group,
// 			'kind' : customer_kind,
// 			'type' : customer_type,
// 			'class' : customer_class,
// 			'area' : customer_area,
// 			'sale' : sale,
// 			'credit_term' : credit_term,
// 			'CreditLine' : credit_amount
// 		},
// 		success:function(rs) {
// 			load_out();
// 			var rs = $.trim(rs);
// 			if(rs === 'success') {
// 				swal({
// 					title:'Success',
// 					type:'success',
// 					timer:1000
// 				});
//
// 				$('#customer').val(code);
// 				$('#customerName').val(name);
//
// 				$('#customer_code').val('');
// 				$('#customer_name').val('');
// 				$('#tax_id').val('');
// 				$('#customer_group').val('');
// 				$('#customer_kine').val('');
// 				$('#customer_type').val('');
// 				$('#customer_class').val('');
// 				$('#customer_area').val('');
// 				$('#customer_sale').val('');
// 				$('#credit_term').val(0);
// 				$('#cretit_amount').val(0.00);
// 			}
// 			else {
// 				swal({
// 					title:'Error!',
// 					text:rs,
// 					type:'error'
// 				});
// 			}
// 		},
// 		error:function(xhr, status, error) {
// 			load_out();
// 			swal({
// 				title:'Error!',
// 				text:'Error-' + xhr.status + ': '+xhr.statusText,
// 				type:'error'
// 			});
// 		}
// 	})
// }
//
// //
// // function addChannels() {
// // 	$('#channelsModal').modal('show');
// // }
//
// $('#channelsModal').on('shown.bs.modal', function(){
// 	$('#channels_code').focus();
// })
//
// function saveChannels() {
// 	var code = $('#channels_code').val();
// 	var name = $('#channels_name').val();
//
// 	if(code.length == 0) {
// 		$('#channels_code').addClass('has-error');
// 		$('#channels_code').focus();
// 		return false;
// 	}
// 	else {
// 		$('#channels_code').removeClass('has-error');
// 	}
//
// 	if(name.length == 0) {
// 		$('#channels_name').addClass('has-error');
// 		$('#channels_name').focus();
// 		return false;
// 	}
// 	else {
// 		$('#channels_name').removeClass('has-error');
// 	}
//
// 	$('#channelsModal').modal('hide');
//
// 	load_in();
//
// 	$.ajax({
// 		url:BASE_URL + 'masters/channels/add',
// 		type:'POST',
// 		cache:false,
// 		data:{
// 			'code' : code,
// 			'name' : name
// 		},
// 		success:function(rs) {
// 			load_out();
// 			var rs = $.trim(rs);
// 			if(rs === 'success') {
// 				swal({
// 					title:'Success',
// 					type:'success',
// 					timer:1000
// 				});
//
// 				$('#channels').append($('<option>', {
// 					value:code,
// 					text:name
// 				}));
//
// 				$('#channels').val(code);
// 			}
// 			else {
// 				swal({
// 					title:'Error',
// 					text:rs,
// 					type:'error'
// 				})
// 			}
//
// 		},
// 		error:function(xhr, status, err) {
// 			load_out();
// 			swal({
// 				title:'Error!',
// 				text:'Error-' + xhr.status + ': '+xhr.statusText,
// 				type:'error'
// 			});
// 		}
// 	})
// }
//
//
//
//
// // function addPayment() {
// // 	$('#paymentModal').modal('show');
// // }
//
// $('#paymentModal').on('shown.bs.modal', function(){
// 	$('#payment_code').focus();
// })
//
// function savePayment() {
// 	var code = $('#payment_code').val();
// 	var name = $('#payment_name').val();
// 	var role = $('#role').val();
// 	var term = $('#term').is(':checked') ? 1 : 0;
//
// 	if(code.length == 0) {
// 		$('#payment_code').addClass('has-error');
// 		$('#payment_code').focus();
// 		return false;
// 	}
// 	else {
// 		$('#payment_code').removeClass('has-error');
// 	}
//
// 	if(name.length == 0) {
// 		$('#payment_name').addClass('has-error');
// 		$('#payment_name').focus();
// 		return false;
// 	}
// 	else {
// 		$('#payment_name').removeClass('has-error');
// 	}
//
//
// 	if(role == '') {
// 		$('#role').addClass('has-error');
// 		return false;
// 	}
// 	else {
// 		$('#role').removeClass('has-error');
// 	}
//
// 	$('#paymentModal').modal('hide');
//
// 	load_in();
//
// 	$.ajax({
// 		url:BASE_URL + 'masters/payment_methods/add',
// 		type:'POST',
// 		cache:false,
// 		data:{
// 			'code' : code,
// 			'name' : name,
// 			'role' : role,
// 			'term' : term
// 		},
// 		success:function(rs) {
// 			load_out();
// 			var rs = $.trim(rs);
// 			if(rs === 'success') {
// 				swal({
// 					title:'Success',
// 					type:'success',
// 					timer:1000
// 				});
//
// 				$('#payment').append($('<option>', {
// 					value:code,
// 					text:name
// 				}));
//
// 				$('#payment').val(code);
// 			}
// 			else {
// 				swal({
// 					title:'Error',
// 					text:rs,
// 					type:'error'
// 				})
// 			}
//
// 		},
// 		error:function(xhr, status, err) {
// 			load_out();
// 			swal({
// 				title:'Error!',
// 				text:'Error-' + xhr.status + ': '+xhr.statusText,
// 				type:'error'
// 			});
// 		}
// 	})
// }


function newTags() {
	$('#tags-name').val('');
	$('#tagsModal').modal('show');
}

$('#tagsModal').on('shown.bs.modal', function() {
	$('#tags-name').focus();
});

function addTags() {
	$('#tags-name').clearError();
	let name = $('#tags-name').val();

	if(name.length == 0) {
		$('#tags-name').hasError();
		return false;
	}

	$('#tagsModal').modal('hide');

	$.ajax({
		url:BASE_URL + 'orders/orders/add_tags',
		type:'POST',
		cache:false,
		data:{
			'name' : name
		},
		success:function(rs) {
			if(rs.trim() === 'success') {
				$('#tags').append($('<option>', {
					value:name,
					text:name
				}));

				$('#tags').val(name);
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	})
}
