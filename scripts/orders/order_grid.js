// JavaScript Document
function getItemGrid(){
	var itemCode 	= $("#item-code").val();
	var whCode = $('#warehouse').val();
	var isView = $('#view').length;
	if( itemCode.length > 0  ){
		$.ajax({
			url:BASE_URL + 'orders/orders/get_item_grid',
			type:'GET',
			cache:false,
			data:{
				'warehouse_code' : whCode,
				'itemCode' : itemCode,
				'isView' : isView
			},
			success:function(rs){
				var rs = rs.split(' | ');
				if(rs[0] === 'success'){
					$('#stock-qty').val(rs[2]);
					$('#input-qty').val('').focus();

					getPOBacklogs(itemCode);
					getDoBacklogs(itemCode);
				}
				else{
					$('#stock-qty').val('');
					$('#input-qty').val('');
					$('#po-qty').val('');
					$('#do-qty').val('');
					swal(rs[1]);
				}
			}
		})
	}
}


function getPOBacklogs(itemCode) {
	if(itemCode != "") {
		$.ajax({
			url:BASE_URL + 'main/get_po_backlogs',
			type:'POST',
			cache:false,
			data:{
				'item_code' : itemCode
			},
			success:function(rs) {
				$('#po-qty').val(rs);
			},
			error:function(rs) {
				$('#po-qty').val(0);
			}
		})
	}
}


function getDoBacklogs(itemCode) {
	if(itemCode != "") {
		$.ajax({
			url:BASE_URL + 'main/get_do_backlogs',
			type:'POST',
			cache:false,
			data:{
				'item_code' : itemCode
			},
			success:function(rs) {
				$('#do-qty').val(rs);
			},
			error:function(rs) {
				$('#do-qty').val(0);
			}
		})
	}
}


//----
function getOrderItemGrid(code) {
	var whCode = $('#warehouse').val();
	var isView = $('#view').length;

	if(code.length) {
		$.ajax({
			url:BASE_URL + 'orders/orders/get_order_item_grid',
			type:'GET',
			cache:false,
			data:{
				'warehouse_code' : whCode,
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
					$("#modal-item").css("width", width +"px");
					$("#modalItemTitle").html(pdCode);
					//$("#id_style").val(style);
					$("#modalItemBody").html(grid);
					$("#orderItemGrid").modal('show');
					$('#orderItemGrid').on('shown.bs.modal', function(){
						$('#'+code).val(1);
						$('#'+code).focus();
						$('#'+code).select();
					})

				}else{
					swal("สินค้าไม่ถูกต้อง");
				}
			}
		})
	}
}


// JavaScript Document
function getProductGrid(){
	var pdCode 	= $("#pd-box").val();
	var whCode = $('#warehouse').val();
	var isView = $('#view').length;
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_order_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode,
				"warehouse_code" : whCode,
				"isView" : isView
			},
			success: function(rs){
				load_out();
				var rs = rs.split(' | ');
				if( rs.length > 3 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];
					var cover = rs[4];

					if(rs.length === 6){
						var price = rs[5];
						pdCode = pdCode + ' <span style="color:red;"> : ' + price + ' ฿</span>';
					}

					if(grid == 'notfound'){
						swal("ไม่พบสินค้า");
						return false;
					}

					$("#modal").css("width", width +"px");
					$("#modal-content").css("width", width +"px");
					$("#modalTitle").html(pdCode);
					$("#id_style").val(style);
					$("#modalBody").html(grid);
					$('#image-cover').html('<img src="'+cover+'" />');
					$("#orderGrid").modal('show');
				}else{
					swal("สินค้าไม่ถูกต้อง");
				}
			}
		});
	}
}



function getOrderGrid(styleCode){
	var whCode = $('#warehouse').val();
	var isView = $('#view').length;
	load_in();
	$.ajax({
		url: BASE_URL + 'orders/orders/get_order_grid',
		type:"GET",
		cache:"false",
		data:{
			"style_code" : styleCode,
			"warehouse_code" : whCode,
			"isView" : isView
		},
		success: function(rs){
			load_out();
			var rs = rs.split(' | ');
			if( rs.length > 3 ){
				var grid = rs[0];
				var width = rs[1];
				var pdCode = rs[2];
				var style = rs[3];
				var cover = rs[4];

				if(rs.length === 6){
					var price = rs[5];
					pdCode = pdCode + '<span style="color:red;"> : ' + price + ' ฿</span>';
				}

				if(grid == 'notfound'){
					swal("ไม่พบสินค้า");
					return false;
				}

				$("#modal").css("width", width +"px");
				$("#modal-content").css("width", width +"px");
				$("#modalTitle").html(pdCode);
				$("#id_style").val(style);
				$("#modalBody").html(grid);
				$('#image-cover').html('<img src="'+cover+'" />');
				$("#orderGrid").modal('show');
			}else{
				swal("สินค้าไม่ถูกต้อง");
			}
		}
	});
}


function valid_qty(el, qty){
	var order_qty = el.val();
	if(parseInt(order_qty) > parseInt(qty) )	{
		swal('สั่งได้ '+qty+' เท่านั้น');
		el.val('');
		el.focus();
	}
}
