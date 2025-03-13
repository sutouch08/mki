
//---	กำหนดให้สามารถค้นหาโซนได้ก่อนจะค้นหาลูกค้า(กรณี edit header)
window.addEventListener('load', () => {
   var customer_code = $('#customerCode').val();
   zoneInit(customer_code, false);
})


$('#date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#ship-date').datepicker('option', 'minDate', sd)
  }
});


$('#ship-date').datepicker({
  dateFormat:'dd-mm-yy'
});

var click = 0;

function add() {
  if(click === 0) {
    click = 1;
    let h = {
      'date' : $('#date').val(),
      'customer_code' : $('#customer-code').val(),
      'customer_name' : $('#customer-name').val(),
      'zone_code' : $('#zone_code').val(),
      'zone_name' : $('#zone').val(),
      'gp' : parseDefault(parseFloat($('#gp').val()), 0),
      'remark' : $('#remark').val().trim(),
      'order_round' : $('#order-round').val(),
      'shipping_round' : $('#shipping-round').val(),
      'shipping_date' : $('#ship-date').val()
    };

    if( ! isDate(h.date)) {
      swal("วันที่ไม่ถูกต้อง");
      click = 0;
      return false;
    }

    if(h.customer_code.length == 0 || h.customer_name.length == 0) {
      swal("รหัสลูกค้าไม่ถูกต้อง");
      click = 0;
      return false;
    }

    if(h.zone_code.length == 0 || h.zone_name.length == 0) {
      swal("โซนไม่ถูกต้อง");
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            window.location.href = HOME + 'edit_detail/'+ ds.code;
          }
          else {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error',
              html:true
            })

            click = 0;
          }
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error',
            html:true
          })

          click = 0;
        }
      },
      error:function(rs) {
        load_out();

        swal({
          title:'Error!',
          text:rs.responseText,
          type:'error',
          html:true
        })

        click = 0;
      }
    })
  }
}

//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder(){
  var order_code = $('#order_code').val();
	$.ajax({
		url: BASE_URL + 'orders/orders/save/'+ order_code,
		type:"POST",
    cache:false,
		success:function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Saved',
          type: 'success',
          timer: 1000
        });
				setTimeout(function(){ editOrder(order_code) }, 1200);
			}else{
				swal("Error ! ", rs , "error");
			}
		}
	});
}


$('#customer-code').autocomplete({
    source: BASE_URL + 'auto_complete/get_customer_code_and_name',
    autoFocus: true,
    close: function() {
      let rs = $(this).val();
      let arr = rs.split(' | ');

      if(arr.length == 2) {
        let code = arr[0];
        let name = arr[1];

        $(this).val(code);
        $('#customerCode').val(code);
        $('#customer-name').val(name);

        zoneInit(code, true);
      }
      else {
        $(this).val('');
        $('#customerCode').val('');
        $('#customer-name').val('');
        zoneInit('');
      }
    }
})


$("#customer-name").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function() {
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customer-code").val(code);
      $('#customerCode').val(code);
			$(this).val(name);
      zoneInit(code, true);
		}
    else
    {
      $('#customer-code').val('');
			$("#customerCode").val('');
			$(this).val('');
      zoneInit('');
		}
	}
});


function zoneInit(customer_code, edit)
{
  if(edit){
    $('#zone_code').val('');
    $('#zone').val('');
  }

  $('#zone').autocomplete({
    source:BASE_URL + 'auto_complete/get_consign_zone/' + customer_code,
    autoFocus: true,
    close:function(){
      var rs = $.trim($(this).val());
      var arr = rs.split(' | ');
      if(arr.length == 2)
      {
        var code = arr[0];
        var name = arr[1];
        $('#zone_code').val(code);
        $('#zone').val(name);
      }
      else {
        $('#zone_code').val('');
        $('#zone').val('');
      }
    }
  })
}



var customer;
var channels;
var payment;
var date;


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
  customer = $("#customerCode").val();
	channels = $("#channels").val();
	payment  = $("#payment").val();
	date = $("#date").val();
}


//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addToOrder(){
  var order_code = $('#order_code').val();
	//var count = countInput();
  var data = [];
  $(".order-grid").each(function(index, element){
    if($(this).val() != ''){
      var code = $(this).attr('id');
      data.push({'code' : code, 'qty' : $(this).val()});
    }
  });

	if(data.length > 0 ){
		$("#orderGrid").modal('hide');
		$.ajax({
			url: BASE_URL + 'orders/orders/add_detail/'+order_code,
			type:"POST",
      cache:"false",
      data: {
        'data' : data
      },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
            title: 'success',
            type: 'success',
            timer: 1000
          });
					$("#btn-save-order").removeClass('hide');
					updateDetailTable(); //--- update list of order detail
				}else{
					swal("Error", rs, "error");
				}
			}
		});
	}
}


function update_detail(id) {
	var c_qty = parseDefaultValue($('#current_qty_'+id).val(), 0, 'float');
	var qty = parseDefaultValue($('#qty_'+id).val(), 0, 'float');
	var price = parseDefaultValue($('#price_'+id).val(), 0, 'float');
	var discount = parseDiscount($('#disc_'+id).val(), price);
	var total_amount = parseDefaultValue($('#line_total_'+id).val(), 0, 'float');

	$.ajax({
		url:BASE_URL + 'orders/orders/update_detail',
		type:'POST',
		cache:false,
		data:{
			'id' : id,
			'qty' : qty,
			'price' : price,
			'discount' : discount,
			'total_amount' : total_amount
		},
		success:function(rs) {
			var rs = $.trim(rs)
			if(rs == 'success') {
				$('#current_qty_'+id).val(qty);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});

				$('#qty_'+id).val(c_qty);
				recal(id);
			}
		}
	})

}

// JavaScript Document
function updateDetailTable(){
	var order_code = $("#order_code").val();
	$.ajax({
		url: BASE_URL + 'orders/orders/get_detail_table/'+order_code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source = $("#detail-table-template").html();
				var data = $.parseJSON(rs);
				var output = $("#detail-table");
				render(source, data, output);
			}
			else
			{
				var source = $("#nodata-template").html();
				var data = [];
				var output = $("#detail-table");
				render(source, data, output);
			}
		}
	});
}



function removeDetail(id, name){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '" + name + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: BASE_URL + 'orders/orders/remove_detail/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title: 'Deleted', type: 'success', timer: 1000 });
						updateDetailTable();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}




$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true
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




//--- ตรวจสอบจำนวนที่คีย์สั่งใน order grid
function countInput(){
	var qty = 0;
	$(".order-grid").each(function(index, element) {
        if( $(this).val() != '' ){
			qty++;
		}
    });
	return qty;
}


function updateOrder() {
  if(click === 0) {
    click = 1;

    let h = {
      'code' : $('#order_code').val(),
      'date' : $('#date').val(),
      'customer_code' : $('#customer-code').val(),
      'customer_name' : $('#customer-name').val(),
      'zone_code' : $('#zone_code').val(),
      'zone_name' : $('#zone').val(),
      'gp' : parseDefault(parseFloat($('#gp').val()), 0),
      'remark' : $('#remark').val().trim(),
      'order_round' : $('#order-round').val(),
      'shipping_round' : $('#shipping-round').val(),
      'shipping_date' : $('#ship-date').val()
    };

    if( ! isDate(h.date)) {
      swal("วันที่ไม่ถูกต้อง");
      click = 0;
      return false;
    }

    if(h.customer_code.length == 0 || h.customer_name.length == 0) {
      swal("รหัสลูกค้าไม่ถูกต้อง");
      click = 0;
      return false;
    }

    if(h.zone_code.length == 0 || h.zone_name.length == 0) {
      swal("โซนไม่ถูกต้อง");
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'update_order',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(rs.trim() == 'success') {
          swal({
            title:'success',
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
            type:'error',
            html:true
          });
        }

        click = 0;
      },
      error:function(rs) {
        load_out();

        swal({
          title:'Error!',
          text:rs.responseText,
          type:'error',
          html:true
        })

        click = 0;
      }
    })

  }
}


function recal(id) {
	var price = parseDefault(parseFloat($('#price_'+id).val()), 0);
	var qty = parseDefault(parseFloat($('#qty_'+id).val()), 0);
	var disc = $('#disc_'+id).val();
	var discAmount = parseDiscountAmount(disc, price);
	var lineTotal = (price * qty) - (discAmount * qty);
	$('#line_total_'+id).val(lineTotal.toFixed(2));


	recalTotal();
}

//--- convert line total to discount

function recalDiscount(id) {
	var qty = parseDefault(parseFloat($('#qty_'+id).val()), 0);
	var price = parseDefault(parseFloat($('#price_'+id).val()), 0);
	var amount = parseDefault(parseFloat($('#line_total_'+id).val()), 0);

	var disc = (1- (amount/qty)/price) * 100;

	$('#disc_'+id).val(disc.toFixed(2)+"%");

	recalTotal();
}




function recalTotal() {
	var total_order = 0;
	var totalAfDisc = 0;
	var total_qty = 0;
	var total_disc = 0;

	var net_amount = 0;
	var shipping_fee = parseDefault(parseFloat($('#shipping-box').val()), 0);
	var service_fee = parseDefault(parseFloat($('#service-box').val()), 0);
	var deposit = parseDefault(parseFloat($('#deposit-amount').val()), 0);

	$('.line-total').each(function(){
		let id = $(this).data('id');
		let price = parseDefault(parseFloat($('#price_'+id).val()), 0);
		let qty = parseDefault(parseFloat($('#qty_'+id).val()), 0);
		let amount = parseDefault(parseFloat($('#line_total_'+id).val()), 0);
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
	net_amount = (total_order + shipping_fee + service_fee) - total_disc - deposit;

	$('#total-qty').text(addCommas(total_qty.toFixed(2)));
	$('#total-order').text(addCommas(total_order.toFixed(2)));
	$('#total-disc').text("-" + addCommas(total_disc.toFixed(2)));
	$('#shipping-fee').text(addCommas(shipping_fee.toFixed(2)));
	$('#service-fee').text(addCommas(service_fee.toFixed(2)));
	$('#deposit').text("-" + addCommas(deposit.toFixed(2)));
	$('#net-amount').text(addCommas(net_amount.toFixed(2)));
}


// JavaScript Document
function changeState(){
    var order_code = $("#order_code").val();
    var state = $("#stateList").val();
    if( state != 0){
        $.ajax({
            url:BASE_URL + 'orders/orders/order_state_change',
            type:"POST",
            cache:"false",
            data:{
              "order_code" : order_code,
              "state" : state
            },
            success:function(rs){
                var rs = $.trim(rs);
                if(rs == 'success'){
                    swal({
                      title:'success',
                      text:'status updated',
                      type:'success',
                      timer: 1000
                    });

                    setTimeout(function(){
                      window.location.reload();
                    }, 1500);

                }else{
                    swal("Error !", rs, "error");
                }
            }
        });
    }
}



function setNotExpire(option){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/set_never_expire',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : order_code,
      'option' : option
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}

function unExpired(){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/un_expired',
    type:'GET',
    cache:'false',
    data:{
      'order_code' : order_code
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}
