function viewImage(imageUrl) {
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}


function viewPaymentDetail(id) {
	var order_code = $('#order_code').val();
	load_in();
	$.ajax({
		url: BASE_URL + 'orders/orders/view_payment_detail/'+id,
		type:"POST",
		cache:"false",
		data:{
			"order_code" : order_code
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ) {
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูล', 'error');
			}
			else {
				var source 	= $("#detailTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#detailBody");
				render(source, data, output);
				$("#confirmModal").modal('show');
			}
		}
	});
}


$("#emsNo").keyup(function(e) {
	if( e.keyCode == 13 )	{
		saveDeliveryNo();
	}
});


function inputDeliveryNo() {
	$("#deliveryModal").modal('show');
}


function saveDeliveryNo() {
	var deliveryNo 	= $("#emsNo").val();
	var order_code 	= $("#order_code").val();
	if( deliveryNo != '') {

		$("#deliveryModal").modal('hide');

		$.ajax({
			url: BASE_URL + 'orders/orders/update_shipping_code/',
			type:"POST",
			cache:"false",
			data:{
				"shipping_code" : deliveryNo,
				"order_code" : order_code
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success') {
					window.location.reload();
				}
			}
		});
	}
}


function submitPayment() {
	var order_code	= $("#order_code").val();
	var id_account	= $("#id_account").val();
	var acc_no 			= $('#acc_no').val();
	var image				= $("#image")[0].files[0];
	var payAmount		= $("#payAmount").val();
	var orderAmount = $("#orderAmount").val();
	var payDate			= $("#payDate").val();
	var payHour			= $("#payHour").val();
	var payMin			= $("#payMin").val();
	var is_deposit  = $('#is_deposit').is(':checked') ? 1 : 0;

	if( order_code == '' ) {
		swal('ข้อผิดพลาด', 'ไม่พบไอดีออเดอร์กรุณาออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง', 'error');
		return false;
	}

	if( id_account == '' ) {
		swal('ข้อผิดพลาด', 'ไม่พบข้อมูลบัญชีธนาคาร กรุณาออกจากหน้านี้แล้วลองแจ้งชำระอีกครั้ง', 'error');
		return false;
	}

	if(acc_no == ''){
		swal('ข้อผิดพลาด', 'ไม่พลเลขที่บัญชี กรุณาออกจากหน้านี้แล้วลองใหม่อีกครั้ง', 'error');
		return false;
	}

	if( image == '' ){
		swal('ข้อผิดพลาด', 'ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง', 'error');
		return false;
	}

	if( payAmount == 0 || isNaN( parseFloat(payAmount) )){
		swal("ข้อผิดพลาด", "ยอดชำระไม่ถูกต้อง", 'error');
		return false;
	}


	if(is_deposit == 0)	{
		if( parseFloat(payAmount) < parseFloat(orderAmount) ){
			swal("ข้อผิดพลาด", "ยอดชำระไม่ครบ", 'error');
			return false;
		}
	}


	if( !isDate(payDate) ){
		swal('วันที่ไม่ถูกต้อง');
		return false;
	}

	$("#paymentModal").modal('hide');

	var fd = new FormData();
	fd.append('image', $('input[type=file]')[0].files[0]);
	fd.append('order_code', order_code);
	fd.append('id_account', id_account);
	fd.append('acc_no', acc_no);
	fd.append('payAmount', payAmount);
	fd.append('orderAmount', orderAmount);
	fd.append('payDate', payDate);
	fd.append('payHour', payHour);
	fd.append('payMin', payMin);
	fd.append('is_deposit', is_deposit);
	load_in();
	$.ajax({
		url: BASE_URL + 'orders/orders/confirm_payment',
		type:"POST",
		cache: "false",
		data: fd,
		processData:false,
		contentType: false,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success') {
				swal({
					title : 'สำเร็จ',
					text : 'แจ้งชำระเงินเรียบร้อยแล้ว',
					type: 'success',
					timer: 1000
				});

				clearPaymentForm();
				setTimeout(function(){
					window.location.reload();
				}, 1200);

			}
			else if( rs == 'fail' ) {
				swal("ข้อผิดพลาด", "ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง", "error");
			}
			else {
				swal("ข้อผิดพลาด", rs, "error");
			}
		}
	});
}


function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#previewImg').html('<img id="previewImg" src="'+e.target.result+'" width="200px" alt="รูปสลิปของคุณ" />');
		}
		reader.readAsDataURL(input.files[0]);
	}
}


$("#image").change(function() {
	if($(this).val() != '')	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;
		if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
		{
			swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
			$(this).val('');
			return false;
		}
		if( size > 2000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 2 MB", "error");
			$(this).val('');
			return false;
		}
		readURL(this);
		$("#btn-select-file").css("display", "none");
		$("#block-image").animate({opacity:1}, 1000);
	}
});


function clearPaymentForm() {
	$("#id_account").val('');
	$("#payAmount").val('');
	$("#payDate").val('');
	$("#payHour").val('00');
	$("#payMin").val('00');
	removeFile();
}


function removeFile() {
	$("#previewImg").html('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');
	$("#image").val('');
}


$("#payAmount").focusout(function(e) {
	if( $(this).val() != '' && isNaN(parseFloat($(this).val())) )	{
		swal('กรุณาระบุยอดเงินเป็นตัวเลขเท่านั้น');
	}
});


function dateClick() {
	$("#payDate").focus();
}


$("#payDate").datepicker({ dateFormat: 'dd-mm-yy'});


function selectFile() {
	$("#image").click();
}


function payOnThis(id, acc_no) {
	$("#selectBankModal").modal('hide');
	$.ajax({
		url:BASE_URL + 'orders/orders/get_account_detail/'+id,
		type:"POST",
		cache:"false",
		success: function(rs) {
			var rs = $.trim(rs);
			if( rs == 'fail' ) {
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูลที่ต้องการ กรุณาลองใหม่', 'error');
			}
			else {
				var ds = rs.split(' | ');
				var logo 	= '<img src="'+ ds[0] +'" width="50px" height="50px" />';
				var acc	= ds[1];
				$("#id_account").val(id);
				$('#acc_no').val(acc_no);
				$("#logo").html(logo)
				$("#detail").html(acc);
				$("#paymentModal").modal('show');
			}
		}
	});
}


function payOrder() {
	var order_code = $("#order_code").val();
	$.ajax({
		url: BASE_URL + 'orders/orders/get_pay_amount',
		type:"GET",
		cache:"false",
		data: {
			"order_code" : order_code
		},
		success: function(rs){
			var rs = $.trim(rs);
			$("#orderAmount").val(rs);
			$("#payAmountLabel").text("ยอดชำระ "+ addCommas(rs) +" บาท");
		}
	});
	$("#selectBankModal").modal('show');
}


var clipboard = new Clipboard('.btn');


function Summary() {
	var amount 		= parseFloat( removeCommas($("#total-td").text() ) );
	var discount 	= parseFloat( removeCommas( $("#discount-td").text() ) );
	var netAmount = amount - discount;
	$("#netAmount-td").text( addCommas( parseFloat(netAmount).toFixed(2) ) );
}


function print_order(id) {
	var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open("controller/orderController.php?print_order&order_code="+id, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");
}


function getSummary() {
	var order_code = $("#order_code").val();
	$.ajax({
		url:BASE_URL + 'orders/orders/get_summary',
		type:"POST",
		cache:"false",
		data:{
			"order_code" : order_code
		},
		success: function(rs){
			$("#summaryText").html(rs);
		}
	});

	$("#orderSummaryTab").modal("show");
}


$("#Fname").keyup(function(e){ if( e.keyCode == 13 ){ $("#address1").focus(); 	} });
$("#address1").keyup(function(e){ if( e.keyCode == 13 ){ $("#sub_district").focus(); 	} });
$("#phone").keyup(function(e){ if( e.keyCode == 13 ){ $("#email").focus(); 	} });
$("#email").keyup(function(e){ if( e.keyCode == 13 ){ $("#alias").focus(); 	} });
$("#alias").keyup(function(e){ if( e.keyCode == 13 ){ saveAddress(); } });


function activeShippingFee() {
	$('#shippingFee').removeAttr('disabled');
	$('#btn-edit-shipping-fee').addClass('hide');
	$('#btn-update-shipping-fee').removeClass('hide');
	$('#shippingFee').select();
}

$('#shippingFee').keyup(function(e){
	if(e.keyCode == 13){
		updateShippingFee();
	}
});


function updateShippingFee() {
	var order_code = $('#order_code').val();
	var amount = parseDefault(parseFloat($('#shippingFee').val()), 0.00);
	$.ajax({
		url:BASE_URL + 'orders/orders/update_shipping_fee',
		type:'POST',
		cache:false,
		data:{
			'order_code' : order_code,
			'shipping_fee' : amount
		},
		success:function(rs){
			if(rs == 'success'){
				window.location.reload();
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	})
}


function activeServiceFee(){
	$('#serviceFee').removeAttr('disabled');
	$('#btn-edit-service-fee').addClass('hide');
	$('#btn-update-service-fee').removeClass('hide');
	$('#serviceFee').select();
}


$('#serviceFee').keyup(function(e){
	if(e.keyCode == 13){
		updateServiceFee();
	}
});


function updateServiceFee(){
	var order_code = $('#order_code').val();
	var amount = parseDefault(parseFloat($('#serviceFee').val()), 0.00);
	$.ajax({
		url:BASE_URL + 'orders/orders/update_service_fee',
		type:'POST',
		cache:false,
		data:{
			'order_code' : order_code,
			'service_fee' : amount
		},
		success:function(rs){
			if(rs == 'success'){
				window.location.reload();
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	})
}
