function save() {
	var code = $('#code').val();
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
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Saved',
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					view_detail(code);
				},1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(rs) {
			load_out();
			swal({
				title:'Error!',
				text:rs,
				type:'error'
			})
		}
	})
}



function getOrderList() {
	var customer_code = $('#customer_code').val();

	if(customer_code.length > 0) {
		load_in();
		$.ajax({
			url:HOME + 'get_order_list',
			type:'GET',
			cache:false,
			data:{
				'customer_code' : customer_code
			},
			success:function(rs) {
				load_out();
				if(isJson(rs)) {
					var data = $.parseJSON(rs);
					var source = $('#orderListTemplate').html();
					var output = $('#orderList');

					render(source, data, output);

					$('#order-list-modal').modal('show');
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


function addToOrder() {
	var code = $('#code').val();
	var list = [];

	$('.chk').each(function(){
		if($(this).is(':checked')) {
			list.push($(this).val());
		}
	})

	if(list.length)
	{
		load_in();

		$.ajax({
			url:HOME + 'add_to_order',
			type:'POST',
			cache:false,
			data:{
				'code' : code,
				'order_list' : list
			},
			success:function(rs) {
				load_out();

				if(rs === 'success') {
					window.location.reload();
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




function confirm_remove(reference) {
	var code = $('#code').val();

	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการลบ '+reference+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
	},
	function() {
		load_in();

		$.ajax({
			url:HOME + 'remove_reference_detail',
			type:'POST',
			cache:false,
			data:{
				'reference' : reference,
				'code' : code
			},
			success:function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					})

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
	})
}
