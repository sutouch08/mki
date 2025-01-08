var HOME = BASE_URL + 'pos/order_pos/';

function goToPOS(id) {
	window.location.href = HOME + 'main/'+id;
}


function goAdd(id) {
	window.location.href = HOME + 'add/'+id;
}


function viewBill(code) {
	window.location.href = HOME + 'bill/'+code;
}

function removeItem(id) {
	$.ajax({
		url:HOME + 'remove_item',
		type:'POST',
		cache:false,
		data:{
			'id' : id
		},
		success:function(rs) {
			if(rs === 'success') {
				$('#row-'+id).remove();
				recalTotal();
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr, status, error) {
			swal({
				title:'Error!!',
				type:'error',
				text:'Delete failed - '+ xhr.status+' : '+xhr+statusText
			});
		}
	})

}




$('#pd-box').keyup(function(e){
	if(e.keyCode === 13) {
		get_product_data();
	}
})


function get_product_data() {
	var zone_code = $('#zone_code').val();
	var code = $('#pd-box').val();
	if(code.length) {

		$('#item-code-label').text(code);
		$.ajax({
			url:HOME + 'get_product_data',
			type:'GET',
			cache:false,
			data:{
				'product_code' : code,
				'zone_code' : zone_code
			},
			success:function(rs) {
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var ds = $.parseJSON(rs);
					var source = $('#item-template').html();
					var output = $('#item-data');

					render(source, ds, output);

					$('#productModal').modal('show');
				}
				else {
					swal("Product not found");
				}
			}
		})
	}
}

$('#barcode-box').keyup(function(e){
	if(e.keyCode === 13) {
		var barcode = $.trim($(this).val());
		if(barcode.length) {
			$('#barcode-box').val('');
			add_item(barcode);
		}

	}
})


$('#barcode-box').autocomplete({
	source:BASE_URL + 'auto_complete/get_item_code_and_name',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
})


function add_item(product_code)
{
	var order_code = $('#order_code').val();
	var payment_code =  $('#payBy').val();
	var customer_code = $('#customer').val();
	var zone_code = $('#zone_code').val();
	var channels_code = $('#channels_code').val();

	if(product_code.length > 0) {
		$.ajax({
			url:HOME + 'add_to_order', //--'get_product_by_code',
			type:'GET',
			cache:false,
			data:{
				'order_code' : order_code,
				'product_code' : product_code,
				'payment_code' : payment_code,
				'customer_code' : customer_code,
				'channels_code' : channels_code,
				'zone_code' : zone_code
			},
			success:function(rs) {
				if(isJson(rs)) {

					addToOrder(rs);
				}
				else {
					swal({
						title:'Error',
						text:rs,
						type:'error'
					});
				}
			},
			error:function(xhr, status, error) {
				swal({
					title:'Error!',
					text:'Error-'+xhr.status+': '+xhr.statusText,
					type:'error'
				});
			}
		})
	}
}

function addToOrder(rs) {
	var ds = $.parseJSON(rs);
	var id = ds.id;

	if($('#qty-'+id).length) {

		var source = $('#update-template').html();
		var output = $('#row-'+id);

		render(source, ds, output);

		recalItem(id);
	}
	else {
		var source = $('#row-template').html();
		var output = $('#item-table');

		render_append(source, ds, output);
		percent_init();
		recalItem(id);
	}
}



function updateItem(id) {
	var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
	var qty = $('#qty-'+id).val();
	var qty = isInteger(qty) ? parseDefault(parseInt(qty), 0) : parseDefault(parseFloat(qty), 0);
	var disc = $('#disc-'+id).val();

	if(qty <= 0) {
		$('#qty-'+id).val(1);
		recalItem(id);
	}
	else {
		$.ajax({
			url:HOME + 'update_item',
			type:'POST',
			cache:false,
			data:{
				'id' : id,
				'price' : price,
				'qty' : qty,
				'discount_label' : disc
			},
			success:function(rs) {
				if(rs == 'success') {
					//--- update current
					$('#currentQty-'+id).val(qty);
					$('#currentPrice-'+id).val(price);
					$('#currentDisc-'+id).val(disc);
					recalItem(id);
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'eror'
					});

					//--- roll back data
					var c_qty = $('#currentQty-'+id).val();
					var c_price = $('#currentPrice-'+id).val();
					var c_disc = $('#currentDisc-'+id).val();

					$('#qty-'+id).val(c_qty);
					$('#price-'+id).val(c_price);
					$('#disc-'+id).val(c_disc);

					recalItem(id);
				}
			}
		})
	}
}




function recalItem(id) {
	var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
	var qty = $('#qty-'+id).val();
	var qty = isInteger(qty) ? parseDefault(parseInt(qty), 0) : parseDefault(parseFloat(qty), 0);
	var disc = parseDiscountAmount($('#disc-'+id).val(), price);
	var sell_price = price - disc;
	var tax_rate = parseDefault(parseFloat($('#taxRate-'+id).val()), 0.00) * 0.01;
	var total = qty * sell_price;
	var tax_amount = total * tax_rate;
	var discount_amount = qty * disc;


	$('#total-'+id).text(addCommas(total.toFixed(2)));
	$('#taxAmount-'+id).val(tax_amount);
	$('#sellPrice-'+id).val(sell_price);
	$('#discAmount-'+id).val(discount_amount);

	recalTotal();
}



function recalTotal() {
	var total_qty = 0;
	var total_tax = 0;
	var total_disc = 0;
	var total_amount = 0;

	$('.input-qty').each(function() {
		let id = $(this).data('id');
		let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
		let discAmount = parseDefault(parseFloat($('#discAmount-'+id).val()), 0);
		let tax = parseDefault(parseFloat($('#taxAmount-'+id).val()), 0);
		let total = parseDefault(parseFloat(removeCommas($('#total-'+id).text())), 0);

		total_qty += qty;
		total_tax += tax;
		total_disc += discAmount;
		total_amount += total;
	});

	$('#total_item').text(addCommas(total_qty.toFixed(2)));
	$('#total_amount').text(addCommas(total_amount.toFixed(2)));
	$('#total_discount').text(addCommas(total_disc.toFixed(2)));
	$('#total_tax').text(addCommas(total_tax.toFixed(2)));
	$('#net_amount').text(addCommas(total_amount.toFixed(2)));
}



function percent_init() {
	$('.input-disc').keyup(function(e) {
		if(e.keyCode === 32) {
			//-- press space bar
			var value = $.trim($(this).val());
			if(value.length) {
				var last = value.slice(-1);
				if(isNaN(last)) {
					//--- ถ้าตัวสุดท้ายไม่ใช่ตัวเลข เอาออก
					value = value.slice(0, -1);
				}
				value = value +"%";
				$(this).val(value);
			}
			else {
				$(this).val('');
			}

			recalItem($(this).data('id'));
		}
	})
}


function showPayment() {
	var amountText = $('#net_amount').text();
	var amount = parseDefault(parseFloat(removeCommas(amountText)), 0.00);
	var role = $('#payBy option:selected').data('role');
	var acc_no = $('#bank_account').val();
	if(role == 3 && acc_no == '') {
		swal('กรุณาระบุเลขที่บัญชี');
		return false;
	}

	if($('.sell-item').length == 0) {
		swal('ไม่พบรายการสินค้า');
		return false;
	}

	if(amount > 0) {
		$('#payableAmount').val(amount);
		$('#payAmountLabel').text(amountText);
	}

	$('#paymentModal').modal('show');

}


$('#paymentModal').on('shown.bs.modal', function() {
	$('#receiveAmount').focus();
})



function changePayment() {
	//--- role = 1 ==> เครดิต
	//--- role = 2 ==> เงินสด
	//--- role = 3 ==> โอนเงิน
	//--- role = 4 ==> COD
	//--- role = 5 ==> บัตรเครดิต
	var payment = $('#payBy').val();
	var role = $('#payBy option:selected').data('role');
	var acc_id = $('#payBy option:selected').data('acc');

	//--- reset field
	$('#receiveAmount').val('');
	$('#changeAmount').val('');
	$('#btn-submit').attr('disabled','disabled');
	$('#bank_account').attr('disabled', 'disabled');

	if(role == 1) {
		//---- credit
		$('#receiveAmount').attr('disabled', 'disabled');
		$('#btn-submit').removeAttr('disabled');
		$('#btn-submit').focus();
	}
	else if(role == 2) {
		//--- cash
		$('#receiveAmount').removeAttr('disabled');
		$('#receiveAmount').focus();
	}
	else if(role == 3) {
		//--- bank transfer
		$('#bank_account').removeAttr('disabled');
		if(acc_id > 0) {
			$('#bank_account').val(acc_id);
		}

		$('#receiveAmount').removeAttr('disabled');
		$('#receiveAmount').focus();
	}
	else if(role == 4) {
		//--- cod
		$('#receiveAmount').attr('disabled', 'disabled');
		$('#btn-submit').removeAttr('disabled');
		$('#btn-submit').focus();
	}
	else if(role == 5) {
		//--- Credit card
		var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
		if(amount > 0) {
			$('#receiveAmount').removeAttr('disabled');
			$('#changeAmount').val('');
			$('#receiveAmount').val(amount);
			$('#btn-submit').removeAttr('disabled');
			$('#btn-submit').focus();
		}
	}
}




function justBalance() {
	var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
	if(amount > 0) {
		var role = $('#payBy option:selected').data('role');
		if(role == 2 || role == 3 || role == 5) {
			$('#receiveAmount').val(amount);
			calChange();
			$('#btn-submit').removeAttr('disabled');
		}
	}
}


$('#receiveAmount').keyup(function(e) {
	if(e.keyCode == 13) {
		submitPayment();
	}
	else {
		var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
		var receive = parseDefault(parseFloat($(this).val()), 0);
		calChange();
		if(receive >= amount) {
			$('#btn-submit').removeAttr('disabled');
		}
		else {
			$('#btn-submit').attr('disabled', 'disabled');
		}
	}

})

function calChange() {
	var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
	var receive = parseDefault(parseFloat($('#receiveAmount').val()), 0);
	var change = receive - amount;
	$('#changeAmount').val(change.toFixed(2));
}


function submitPayment() {
	$('#paymentModal').modal('hide');
	var order_code = $('#order_code').val();
	var payment_code = $('#payBy').val();
	var acc_no = $('#bank_account').val();
	var payment_role = $('#payBy option:selected').data('role');
	var warehouse_code = $('#warehouse_code').val();

	var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
	var receive_amount = parseDefault(parseFloat($('#receiveAmount').val()), 0);
	var change = receive_amount - amount;

	if(payment_role == 2 || payment_role == 3 || payment_role == 5) {
		if(amount > receive_amount) {
			swal("ยอดเงินไม่ครบ");
			return false;
		}
	}


	if(payment_code == '') {
		swal('กรุณาระบุช่องทางการชำระเงิน');
		return false;
	}

	if(payment_role == 3 && acc_no.length == 0) {
		swal('กรุณาระบุเลขที่บัญชี');
		return false;
	}

	if($('.sell-item').length == 0) {
		swal('ไม่พบรายการขาย');
		return  false;
	}

	load_in();
	$.ajax({
		url:HOME + 'save_order',
		type:'POST',
		data: {
			'order_code' : order_code,
			'payment_code' : payment_code,
			'acc_no' : acc_no,
			'payment_role' : payment_role,
			'warehouse_code' : warehouse_code,
			'amount' : amount,
			'received' : receive_amount,
			'changed' : change.toFixed(2)
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'success',
					type:'success',
					timer:1000
				})
				setTimeout(function(){
					viewBill(order_code);
				},1200)

			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				})
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text: xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
}



function change_customer() {
	var order_code = $('#order_code').val();
	var customer_code = $('#customer').val();
	var customer_name = $('#customer option:selected').text();
	var c_customer = $('#current_customer').val();
	var payment_code = $('#payBy_code').val();
	var channels_code = $('#channels_code').val();

	var count = $('.sell-item').length;

	if(count > 0) {
		swal({
			title: 'ต้องการคำนวนส่วนลดใหม่หรือไม่',
			type:'warning',
			showCancelButton:true,
			confirmButtonColor: '#6fb3e0',
			confirmButtonText:'คำนวณส่วนลดใหม่',
			cancelButtonColor:'#428bca',
			cancelButtonText:'ไม่คำนวณ',
			closeOnConfirm:false
		}, function(isConfirm){
			if(isConfirm) {

				$.ajax({
					url:HOME + 'update_order',
					type:'POST',
					cache:false,
					data:{
						'order_code' : order_code,
						'customer_code' : customer_code,
						'customer_name' : customer_name,
						'payment_code' : payment_code,
						'channels_code' : channels_code,
						'recal_discount' : 1
					},
					success:function(rs) {

						if(rs === 'success') {
							swal({
								title:'Success',
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
							});

							$('#customer').val(c_customer);
						}
					},
					error:function(xhr, status, error) {

						swal({
							title:'Error!',
							text:'Error-'+xhr.status+' : '+xhr.statusText,
							type:'error'
						})

						$('#customer').val(c_customer);
					}
				})
			}
			else {

				$.ajax({
					url:HOME + 'update_order',
					type:'POST',
					cache:false,
					data:{
						'order_code' : order_code,
						'customer_code' : customer_code,
						'recal_discount' : 0
					},
					success:function(rs) {

						if(rs === 'success') {
							swal({
								title:'Success',
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
							});

							$('#customer').val(c_customer);
						}
					},
					error:function(xhr, status, error) {

						swal({
							title:'Error!',
							text:'Error-'+xhr.status+' : '+xhr.statusText,
							type:'error'
						})

						$('#customer').val(c_customer);
					}
				})
			}
		})
	}
	else {
		$.ajax({
			url:HOME + 'update_order',
			type:'POST',
			cache:false,
			data:{
				'order_code' : order_code,
				'customer_code' : customer_code,
				'recal_discount' : 0
			},
			success:function(rs) {

				if(rs === 'success') {
					swal({
						title:'Success',
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
					});

					$('#customer').val(c_customer);
				}
			},
			error:function(xhr, status, error) {

				swal({
					title:'Error!',
					text:'Error-'+xhr.status+' : '+xhr.statusText,
					type:'error'
				})

				$('#customer').val(c_customer);
			}
		})
	}
}


function showHoldOption() {
	if($('.sell-item').length == 0) {
		swal('ไม่พบรายการสินค้า');
		return false;
	}

	$('#holdOptionModal').modal('show');
}


$('#holdOptionModal').on('shown.bs.modal', function(){
	$('#reference-note').focus();
});


function holdBill() {
	var order_code = $('#order_code').val();
	var ref_note = $.trim($('#reference-note').val());
	var pos_id = $('#pos_id').val();

	if(ref_note.length == 0) {
		$('#reference-note').addClass('has-error');
		return false;
	}
	else {
		$('#reference-note').removeClass('has-error');
	}

	$('#holdOptionModal').modal('hide');

	load_in();
	$.ajax({
		url:HOME + 'hold_bill',
		type:'POST',
		cache:false,
		data:{
			'order_code' : order_code,
			'reference_note' : ref_note
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					goToPOS(pos_id);
				},1200)
			}
			else {
				swal({
					title:'Error',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr, status, err) {
			load_out();
			swal({
				title:'Error!',
				type:'error',
				text: xhr.responseText,
				html:true
			});
		}
	})
}


function showHoldBill(pos_id) {
	$.ajax({
		url:HOME + 'get_hold_bills/'+pos_id,
		type:'GET',
		cache:false,
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				var source = $('#list-template').html();
				var output = $('#hold-list');

				render(source, ds, output);
			}
			else {
				$('#hold-list').html('<tr><td align="center">'+ rs + '</td></tr>');
			}

			$('#holdListModal').modal('show');
		}
	})
}



function goToBill(pos_id, order_code) {
	window.location.href = HOME + 'edit/'+pos_id+'/'+order_code;
}


$(document).ready(function(){
	percent_init();
})
