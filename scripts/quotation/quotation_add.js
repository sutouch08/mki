
function save() {

	if($('.row-qty').length > 0) {
		var error = 0;
		var message = "";
		var code = $('#code').val();

		$('.row-qty').each(function() {
			var id = $(this).data('id');
			var qty = $('#qty-'+id).val();
			var price = $('#price-'+id).val();
			var amount = $('#amount-'+id).val();

			if(qty <= 0) {
				error++;

				$('#qty-'+id).addClass('has-error');
			}
			else {
				$('#qty-'+id).removeClass('has-error');
			}

			if(price < 0) {
				error++;

				$('#price-'+id).addClass('has-error');
			}
			else {
				$('#price-'+id).removeClass('has-error');
			}

			if(amount < 0) {
				error++;
				$('#amount-'+id).addClass('has-error');
			}
			else {
				$('#amount-'+id).removeClass('has-error');
			}

		}); //--- end each


		if(error > 0) {
			swal({
				title:"Error!",
				text:"กรุณาแก้ไขข้อผิดพลาด",
				type:"error"
			});

			return false;
		}



		load_in();

		$.ajax({
	    url:HOME + 'save',
			type:'POST',
			cache:false,
			data:{
				'code' : code
			},
			success:function(rs) {
				load_out();
				if(rs == 'success') {
					swal({
						title:'Success',
						text:'บันทึกเอกสารเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						goDetail(code);
					},1500);
				}
				else {
						swal({
							title:'Error!',
							text:rs,
							type:'error'
						});
					}
				}
	  	});
		} //-- end if
		else {
			swal({
				title:'Error!',
				text:"ไม่พบรายการสินค้า",
				type:'error'
			});
		}
}



function get_edit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}

function update(){
  let code = $('#code').val();
  let date = $('#date_add').val();
  let customer_code = $('#customerCode').val();
  let contact = $('#contact').val();
  let is_term = $('#is_term').val();
  let credit_term = $('#credit_term').val();
	let valid_days = $('#valid_days').val();
	let title = $('#title').val();
  let remark = $('#remark').val();

  if(!isDate(date)){
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(customer_code.length == 0){
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
      'date_add' : date,
      'customer_code' : customer_code,
      'contact' : contact,
      'is_term' : is_term,
      'credit_term' : credit_term,
			'valid_days' : valid_days,
			'title' : title,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      rs = $.trim(rs);
      if(rs === 'success'){
        $('.edit').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');

        swal({
          title:'Updated',
          type:'success',
          timer: 1000
        });
      }else{
        swal({
          title:'Error!',
          text: rs,
          type:'error'
        });
      }
    }

  })
}



function getOrderGrid(pdCode) {
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_product_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();
				var rs = rs.split(' | ');
				if( rs.length == 4 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];
					$("#modal-content").css("width", width +"px");
					$("#modalTitle").html(pdCode);
					$("#id_style").val(style);
					$("#modalBody").html(grid);
					$("#orderGrid").modal('show');
				}else{
					swal(rs[0]);
				}
			}
		});
	}
}


function getProductGrid(){
	var pdCode = $('#pd-box').val();
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_product_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();
				var rs = rs.split(' | ');
				if( rs.length == 4 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];
					$("#modal").css("width", width +"px");
					$("#modal-content").css("width", width +"px");
					$("#modalTitle").html(pdCode);
					$("#id_style").val(style);
					$("#modalBody").html(grid);
					$("#orderGrid").modal('show');
				}else{
					swal(rs[0]);
				}
			}
		});
	}
}



//----
function getOrderItemGrid(code) {
	var isView = $('#view').length;

	if(code.length) {
		$.ajax({
			url:BASE_URL + 'orders/orders/get_product_item_grid',
			type:'GET',
			cache:false,
			data:{
				'itemCode' : code,
				'isView' : isView
			},
			success:function(rs) {
				var rs = rs.split(' | ');
				if( rs.length > 3 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];

					if(rs.length === 5){
						var price = rs[4];
						pdCode = pdCode + '<span style="color:red;"> : ' + price + ' ฿</span>';
					}

					if(grid == 'notfound'){
						swal("ไม่พบสินค้า");
						return false;
					}

					//$("#modal").css("width", width +"px");
					//$("#modal-content").css("width", width +"px");
					$("#modalItemTitle").html(pdCode);
					//$("#id_style").val(style);
					$("#modalItemBody").html(grid);
					$("#orderItemGrid").modal('show');
					$('#orderItemGrid').on('shown.bs.modal', function(){
						$('#'+code).focus();
					})

				}else{
					swal("สินค้าไม่ถูกต้อง");
				}
			}
		})
	}
}


function addToOrder() {
	var code = $('#code').val();
	var item_code = $('#item-code').val();
	var price = parseFloat($('#price').val());
	var disc = $('#disc').val();
	var qty = parseFloat($('#qty').val());

	if(item_code.length === 0){
		$('#item-code').addClass('has-error');
		return false;
	} else {
		$('#item-code').removeClass('has-error');
	}

	if(isNaN(price)){
		$('#price').addClass('has-error');
		return false;
	} else {
		$('#price').removeClass('has-error');
	}

	if(isNaN(qty)){
		$('#qty').addClass('has-error');
		return false;
	} else {
		$('#qty').removeClass('has-error');
	}

	load_in();

	$.ajax({
		url:HOME + 'add_detail',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'product_code' : item_code,
			'price' : price,
			'discountLabel' : disc,
			'qty' : qty
		},
		success:function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				clearFields();
				get_detail_table();
				$('#item-code').focus();
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		},
		error:function(rs) {
			load_out();
			var errorMessage = "Error-"+rs.status+": "+rs.statusText;
			swal({
				title:"Error!",
				text:errorMessage,
				type:"error"
			});
		}
	});
}


function valid_qty(){
  return true;
}


function insert_item()
{
	$('#orderGrid').modal('hide');
	$("#orderItemGrid").modal('hide');
	var code = $('#code').val();
	var disc = $('#discountLabel').val();
	var ds = [];
	var items = [];

  $('.input-qty').each(function(){

    let item_code = $(this).data('pdcode');
    let qty = parseDefault(parseFloat($(this).val()), 0);

    if(qty > 0){
			let item = {
				"product_code" : item_code,
				"qty" : qty
			}

			items.push(item);
    }
  });

	if(items.length > 0) {
		$.ajax({
			url:HOME + 'add_details',
			type:'POST',
			cache:false,
			data:{
				"code" : code,
				"disc" :disc,
				"items" : items
			},
			success:function(rs) {
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					clearFields();
					get_detail_table();
					update_status();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			},
			error: function(xhr, status, error) {
				swal({
					title:'Error!',
					text:'Error-'+xhr.status+': '+xhr.statusText,
					type:'error'
				})
			}
		})
	}
}



function addItem(){
	var code = $('#code').val();
	var item_code = $('#item-code').val();
	var price = parseFloat($('#price').val());
	var disc = $('#disc').val();
	var qty = parseFloat($('#qty').val());

	if(item_code.length === 0){
		$('#item-code').addClass('has-error');
		return false;
	} else {
		$('#item-code').removeClass('has-error');
	}

	if(isNaN(price)){
		$('#price').addClass('has-error');
		return false;
	} else {
		$('#price').removeClass('has-error');
	}

	if(isNaN(qty)){
		$('#qty').addClass('has-error');
		return false;
	} else {
		$('#qty').removeClass('has-error');
	}

	load_in();

	$.ajax({
		url:HOME + 'add_detail',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'product_code' : item_code,
			'price' : price,
			'discountLabel' : disc,
			'qty' : qty
		},
		success:function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				clearFields();
				get_detail_table();
				$('#item-code').focus();
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		},
		error:function(rs) {
			load_out();
			var errorMessage = "Error-"+rs.status+": "+rs.statusText;
			swal({
				title:"Error!",
				text:errorMessage,
				type:"error"
			});
		}
	});
}


function get_detail_table() {
	var code = $('#code').val();

	$.ajax({
		url:HOME + 'get_detail_table',
		type:'GET',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var source = $('#detail-template').html();
				var data = $.parseJSON(rs);
				var output = $('#detail-table');

				render(source, data, output);

				percent_init();
				digit_init();
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		},
		error: function(rs) {
			var errorMessage = "Error-"+rs.status+": "+rs.statusText;
			swal({
				title:"Error!",
				text:errorMessage,
				type:"error"
			});
		}
	})
}



function removeRow(id, pd_code) {
	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการลบ '+ pd_code + ' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonText:'ใช่ ต้องการลบ',
		cancelButtonText:'ยกเลิก',
		confirmButtonColor:'#DD6B55',
		closeOnConfirm:false
	},
	function() {
		$.ajax({
			url:HOME + 'delete_detail',
			type:'POST',
			cache:false,
			data:{
				'id' : id
			},
			success:function(rs) {
				if(rs === 'success') {
					swal({
						title:'Deleted',
						type:'success',
						timer:1000
					});

					$('#row-'+id).remove();

					reIndex();
					recalTotal();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			},
			error: function(rs) {
				swal({
					title:'Error!',
					text:"Error-" + rs.status + ": "+rs.statusText,
					type:'error'
				})
			}
		})
	});
}


function percent_init() {
	$('.row-disc').keyup(function(e) {
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

			recal($(this).data('id'));
		}
	})
}


function digit_init() {
	$('.digit').focusout(function(){
		var value = parseDefaultValue($(this).val(), 0, 'float');
		$(this).val(value.toFixed(2));
	});

	$('.digit').focus(function(){
		$(this).select();
	})
}


function clearFields() {
	$('#pd-box').val('');
	$('#item-code').val('');
	$('#item-name').val('');
	$('#price').val('');
	$('#disc').val('');
	$('#qty').val('');
}

function updateBillDiscAmount() {
	var code = $('#code').val();
	var billDiscAmount = parseDefaultValue($('#billDiscAmount').val(), 0, 'float');
	var c_bDisc = $('#current_bill_disc_amount').val();

	$.ajax({
		url:HOME + 'update_bill_discount',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'bDiscAmount' : billDiscAmount
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'success') {
				$('#current_bill_disc_amount').val(billDiscAmount);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});

				$('#billDiscAmount').val(c_bDisc);
			}

			recalTotal();
		}
	})

}



function update_detail(id) {
	var qty = parseDefaultValue($('#qty-'+id).val(), 0, 'float');
	var price = parseDefaultValue($('#price-'+id).val(), 0, 'float');
	var discount = $('#disc-'+id).val();
	var total_amount = parseDefaultValue($('#amount-'+id).val(), 0, 'float');

	$.ajax({
		url:HOME + 'update_row',
		type:'POST',
		cache:false,
		data:{
			'id' : id,
			'qty' : qty,
			'price' : price,
			'discountLabel' : discount,
			'total_amount' : total_amount
		},
		success:function(rs) {
			var rs = $.trim(rs)
			if(rs !== 'success') {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
			else {
				update_status();
			}
		},
		error: function(rs) {
			swal({
				title:'Error!',
				text:'Error-'+rs.status+': '+rs.statusText,
				type:'error'
			})
		}
	})
}


//--- convert line total to discount

function recalDiscount(id) {
	var qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
	var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
	var amount = parseDefault(parseFloat($('#amount-'+id).val()), 0);

	var disc = (1- (amount/qty)/price) * 100;

	$('#disc-'+id).val(disc.toFixed(2)+"%");

	recalTotal();
}


function recal(id) {
	var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
	var qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
	var disc = $('#disc-'+id).val();
	var discAmount = parseDiscountAmount(disc, price);
	var lineTotal = (price * qty) - (discAmount * qty);
	$('#amount-'+id).val(lineTotal.toFixed(2));
	recalTotal();
}


function recalTotal() {
	var total_order = 0;
	var totalAfDisc = 0;
	var total_qty = 0;
	var total_disc = 0;
	var net_amount = 0;

	$('.line-total').each(function(){
		let id = $(this).data('id');
		let price = parseDefault(parseFloat($('#price-'+id).val()), 0);
		let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
		let amount = parseDefault(parseFloat($('#amount-'+id).val()), 0);
		let order_amount = qty * price;
		let disc_amount = order_amount - amount;

		total_order += order_amount;
		total_qty += qty;
		total_disc += disc_amount;
	});

	totalAfDisc = total_order - total_disc;

	$('#totalAfDisc').val(totalAfDisc);


	var bill_disc = parseDefault(parseFloat($('#billDiscAmount').val()), 0);

	total_disc += bill_disc;
	net_amount = (total_order - total_disc);

	$('#total-qty').text(addCommas(total_qty.toFixed(2)));
	$('#total-amount').text(addCommas(total_order.toFixed(2)));
	$('#total-discount').text(addCommas(total_disc.toFixed(2)));
	$('#net-amount').text(addCommas(net_amount.toFixed(2)));
}


function update_status() {
	var status = $('#status').val();
	if(status == 1) {
		var code = $('#code').val();

		$.ajax({
			url:HOME + 'unsave_quotation',
			type:'POST',
			cache:false,
			data:{
				'code' : code
			},
			success:function(rs) {
				if(rs === 'success') {
					$('#status').val(0);
					$('#btn-save').removeClass('hidden');
					$('#btn-back').addClass('hidden');
					$('#btn-leave').removeClass('hidden');
				}
			}
		})
	}
}



function get_item(){
	var code = $('#item-code').val();
	if(code.length > 0) {
		$.ajax({
			url:HOME + 'get_item',
			type:'GET',
			cache:false,
			data:{
				'item_code' : code
			},
			success:function(rs){
				var rs = $.trim(rs);
				if(isJson(rs)){
					var ds = $.parseJSON(rs);
					$('#item-name').val(ds.product_name);
					$('#price').val(ds.price);
					$('#price').focus().select();
				} else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
		})
	}
}




$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(code);
			$('#customerName').val(name);
			$('#contact').focus();
		}else{
			$("#customerCode").val('');
			$(this).val('');
			$('#customerName').val('');
		}
	}
});


$('#is_term').change(function(){
  if($(this).val() == 1){
    $('#credit_term').removeAttr('readonly').focus();
  }else{
    $('#credit_term').val(0).attr('readonly', 'readonly');
  }
})



$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
})

$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code_and_name',
	autoFocus: true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length === 2) {
			$(this).val(arr[0]);
		}
	}
});




$('#pd-box').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getProductGrid();
			}, 300);

		}
	}

});



$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_item_code_and_name',
	autoFocus:true,
	close:function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
			setTimeout(function(){
				get_item();
			},500)

		}
		else {
			$(this).val('');
		}
	}
});



$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		var price = $('#price').val();
		if(code.length > 4){
			setTimeout(function(){
				get_item();
			}, 200);
		}
	}
});



$('#price').keyup(function(e){
	if(e.keyCode === 13){
		$('#disc').focus();
	}
})


$('#disc').keyup(function(e){
	if(e.keyCode === 13){
		$('#qty').focus();
	}

	if(e.keyCode == 32) {
		var value = $.trim($('#disc').val());
		if(value.length) {
			var last = value.slice(-1);

			if(isNaN(last)) {
				value = value.slice(0, -1);
			}

			value = value+'%';
			$('#disc').val(value);
		}
		else {
			$('#disc').val('');
		}

	}
});


$('#qty').keyup(function(e){
	if(e.keyCode === 13){
		addItem();
	}
})


$(document).ready(function(){
	percent_init();
	digit_init();
})
