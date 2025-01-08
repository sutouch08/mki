$('#pd-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true
});

$('#pd-code').keyup(function(e){
  if(e.keyCode == 13){
    getProductGrid();
  }
});


function getProductGrid(){
	var pdCode 	= $("#pd-code").val();
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
					$("#modal").css("min-width", width +"px");
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

function valid_qty(){
  return true;
}


function insert_item()
{
	$('#orderGrid').modal('hide');
	var code = $('#code').val();

	var items = [];

  $('.input-qty').each(function(){
    //let pdCode = $(this).attr('id');
		let pdCode = $(this).data('pdcode');
    var qty = parseDefault(parseFloat($(this).val()), 0);

    if(qty > 0){
      var item = {
        'product_code' : pdCode,
        'qty' : qty
      }

      items.push(item);
    }
  });

  if(items.length == 0){
    swal('กรุณาระบุจำนวนอย่างน้อย 1 รายการ');
    return false;
  }

  var data = JSON.stringify(items);

	load_in();

  $.ajax({
    url:HOME + 'add_details/' + code,
		type:'POST',
		cache:false,
		data:{
			'details' : data
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'เพิ่ม '+items.length+' รายการ เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				},1500);
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				}, function(){
					$('#orderGrid').modal('show');
				});
			}
		}
  });
}


function addItem()
{
  let code = $('#code').val();
  let receive_date = $('#receive-date').val();
	let barcode = $('#barcode-item').val();
	let zone_code = $('#zone-code').val();
  let zone_name = $('#zone-name').val();
	let qty = parseDefault(parseFloat($('#item-qty').val()), 0);

  if(zone_code.length == 0 || zone_name.length == 0) {
    swal("กรุณาระบุโซน");
    return false;
  }

	if(barcode.length > 0 && qty > 0) {

		$('#btn-add-item').attr('disabled', 'disabled');

	  $.ajax({
	    url:HOME + 'add_item',
			type:'POST',
			cache:false,
			data:{
        'code' : code,
				'barcode' : barcode,
				'zone_code' : zone_code,
				'qty' : qty,
        'receive_date' : receive_date
			},
			success:function(rs) {
				$('#btn-add-item').removeAttr('disabled');

				if(rs == 'success') {
					updateReceiveTable(code);
					$('#barcode-item').val('');
          $('#item-qty').val(1);
          $('#barcode-item').focus();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error',
						html:true
					});
				}
			}
	  });
	}
}


function updateReceiveTable(code) {
	load_in();
	$.ajax({
		url:HOME + 'update_receive_table',
		type:'GET',
		cache:false,
		data: {
			'code' : code
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var data = $.parseJSON(rs);
				var source = $('#receiveTableTemplate').html();
				var output = $('#receiveTable');

				render(source, data, output);
			}
			else {
				swal({
					title:'Error',
					text: rs,
					type:'error',
					html:true
				});
			}
		},
		error:function(rs) {
			load_out();
			swal({
				title: "Error",
				text:rs.responseText,
				type:'error',
				html:true
			});
		}
	})
}


function getData(){
	let po = $("#poCode").val();
  let zone_code = $('#zone-code').val();
  let zone_name = $('#zone-name').val();

  if(zone_code.length == 0 || zone_name.length == 0) {
    swal({
      title:'Oops!',
      text:"กรุณาระบุโซนรับสินค้า",
      type:'warning'
    },
    function() {
      setTimeout(() => {
        $('#zone-code').focus();
      }, 200);
    });

    return false;
  }

	if(po.length > 0) {
		load_in();
		$.ajax({
			url: HOME + 'get_po_details',
			type:"GET",
			cache:"false",
			data:{
				"po_code" : po
			},
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( isJson(rs) ){
					data = $.parseJSON(rs);
					$('#pre_label').remove();
	        $('#po-title').text(po);
					var source = $('#po-template').html();
	        var data = $.parseJSON(rs);
	        var output = $('#po-body');
	        render(source, data, output);
	        $('#poGrid').modal('show');
				}else{
					swal("ข้อผิดพลาด !", rs, "error");
				}
			}
		});
	}
}



function insertPoItems()
{
	$('#poGrid').modal('hide');

	let code = $("#code").val();
  let receive_date = $('#receive-date').val();
  let zone_code = $('#zone-code').val();
  let zone_name = $('#zone-name').val();
  let zoneCode = $('#zoneCode').val();

  if(zone_code.length == 0 || zone_name.length == 0) {
    swal("กรุณาระบุโซนรับสินค้า");
    return false;
  }

  if(zoneCode != zone_code) {
    swal("โซนไม่ถูกต้อง");
    return false;
  }

	var items = [];

  $('.receive_qty').each(function() {
		let pdCode = $(this).data('pdcode');
    let qty = parseDefault(parseFloat($(this).val()),0);

    if(qty > 0){
      var item = {
        'product_code' : pdCode,
        'receive_date' : receive_date,
        'qty' : qty,
        'zone_code' : zone_code
      }

      items.push(item);
    }
  });

  if(items.length == 0){
    swal({
      title:'Error!',
      text:'กรุณาใส่จำนวนอย่างน้อย 1 รายการ',
      type:'error'
    });

    return false;
  }

  var data = JSON.stringify(items);

	load_in();

  $.ajax({
    url:HOME + 'add_details/'+code,
		type:'POST',
		cache:false,
		data:{
			'details' : data
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'เพิ่ม '+items.length+' รายการ เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				updateReceiveTable(code);

			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				},function(){
					$('#poGrid').modal('show');
				});
			}
		}
  });
}


function receiveAll() {
	$('.receive_qty').each(function() {
		var no = $(this).data('no');
		var backlogs = parseDefault(parseFloat($('#qty-'+no).val()), 0);

		if(backlogs > 0) {
			$('#pdCode-'+no).val(backlogs);
		}
		else {
			$('#pdCode-'+no).val('');
		}
	})
}


function clearAll() {
	$('.receive_qty').each(function(){
		$(this).val('');
	})
}

function changeZone() {
  $('#zone-code').val('');
  $('#zone-name').val('');
  $('#zone-code').removeAttr('disabled');
  $('#btn-change-zone').addClass('hide');
  $('#btn-add-zone').removeClass('hide');

  $('#barcode-item').val('');
  $('#barcode-item').attr('disabled', 'disabled');
  $('#item-qty').val(1);
  $('#btn-add-item').attr('disabled', 'disabled');
  $('#zone-code').focus();
}


function getZone() {
  let code = $('#zone-code').val().trim();

  if(code.length) {
    $.ajax({
      url:HOME + 'get_zone',
      type:'POST',
      cache:false,
      data:{
        'code' : code
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            $('#zone-code').val(ds.data.code);
            $('#zone-name').val(ds.data.name);
            $('#zoneCode').val(ds.data.code);
            // $('#zone-code').attr('disabled', 'disabled');
            // $('#btn-add-zone').addClass('hide');
            // $('#btn-change-zone').removeClass('hide');
            // $('#barcode-item').removeAttr('disabled');
            // $('#item-qty').removeAttr('disabled');
            // $('#btn-add-item').removeAttr('disabled');
            $('#item-qty').val(1);
            $('#barcode-item').focus();
          }
          else {
            $('#zoneCode').val('');

            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            })
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
}

$('#zone-code').focus(function() {
  $(this).select();
});

$('#zone-code').focusout(function() {
  let code = $(this).val().trim();
  let zone = $('#zondCode').val();

  if(code.length > 0 && code != zone) {
    getZone();
  }

  if(code.length == 0) {
    $('#zone-name').val('');
    $('#zoneCode').val('');
  }
});



$("#zone-code").keyup(function(e) {
  if(e.keyCode === 13) {
    getZone();
  }
})


$('#barcode-item').keyup(function(e) {
  if(e.keyCode == 13) {
    addItem();
  }
});

$('#item-qty').keyup(function(e) {
  if(e.keyCode == 13) {
    $('#barcode-item').focus();
  }
})
