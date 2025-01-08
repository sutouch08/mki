var HOME = BASE_URL + 'masters/shop/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });
}


function getSearch() {
	$('#searchForm').submit();
}


$('.search-box').keyup(function(e){
	if(e.keyCode === 13){
		getSearch();
	}
});


function save() {
	var code = $('#code').val();
	var name = $('#name').val();
	var zone = $('#zone').val();
	var zoneCode = $('#zone_code').val();
	var customer = $('#customer').val();
	var customerCode = $('#customer_code').val();
	var bill_logo = $('#bill_logo').val();
	var bill_header = $('#bill_header').val();
	var bill_text = $('#bill_text').val();
	var bill_footer = $('#bill_footer').val();
	var use_vat = $('#use_vat').val();
	var tax_id = $('#tax_id').val();
	var active = $('#active').val();

	if(code.length === 0) {
		$('#code').addClass('has-error');
		return false;
	}
	else {
		$('#code').removeClass('has-error');
	}

	if(name.length === 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}


	if(zone.length === 0 || zoneCode.length === 0) {
		$('#zone').addClass('has-error')
		return false;
	}
	else {
		$('#zone').removeClass('has-error');
	}

	if(customer.length === 0 || customerCode.length === 0) {
		$('#customer').addClass('has-error')
		return false;
	}
	else {
		$('#customer').removeClass('has-error');
	}

	if(use_vat == 1 && tax_id.length == 0) {
		$('#tax_id').addClass('has-error');
		return false;
	}
	else {
		$('#tax_id').removeClass('has-error');
	}


	load_in();
	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'zone_code' : zoneCode,
			'customer_code' : customerCode,
			'bill_logo' : bill_logo,
			'bill_header' : bill_header,
			'bill_text' : bill_text,
			'bill_footer' : bill_footer,
			'use_vat' : use_vat,
			'tax_id' : tax_id,
			'active' : active
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

				setTimeout(function(){
					addNew();
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr, status, error){
			load_out();
			var errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:'Error-'+errorMessage,
				type:'error'
			});
		}
	})

}


function update() {
	var code = $('#code').val();
	var name = $('#name').val();
	var old_name = $('#old_name').val();
	var zone = $('#zone').val();
	var zoneCode = $('#zone_code').val();
	var customer = $('#customer').val();
	var customerCode = $('#customer_code').val();
	var bill_logo = $('#bill_logo').val();
	var bill_header = $('#bill_header').val();
	var bill_text = $('#bill_text').val();
	var bill_footer = $('#bill_footer').val();
	var use_vat = $('#use_vat').val();
	var tax_id = $('#tax_id').val();
	var active = $('#active').val();


	if(name.length === 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}


	if(zone.length === 0 || zoneCode.length === 0) {
		$('#zone').addClass('has-error')
		return false;
	}
	else {
		$('#zone').removeClass('has-error');
	}

	if(customer.length === 0 || customerCode.length === 0) {
		$('#customer').addClass('has-error')
		return false;
	}
	else {
		$('#customer').removeClass('has-error');
	}

	if(use_vat == 1 && tax_id.length == 0) {
		$('#tax_id').addClass('has-error');
		return false;
	}
	else {
		$('#tax_id').removeClass('has-error');
	}



	load_in();
	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'old_name' : old_name,
			'zone_code' : zoneCode,
			'customer_code' : customerCode,
			'bill_logo' : bill_logo,
			'bill_header' : bill_header,
			'bill_text' : bill_text,
			'bill_footer' : bill_footer,
			'use_vat' : use_vat,
			'tax_id' : tax_id,
			'active' : active
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

			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr, status, error){
			load_out();
			var errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:'Error-'+errorMessage,
				type:'error'
			});
		}
	})

}


function getDelete(code, name, no){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
			url:HOME + 'delete',
			type:'POST',
			cache:false,
			data:{
				'code' : code
			},
			success:function(rs) {
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Deleted',
						text:'ลบรายการเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						$('#row-'+no).remove();
						reIndex();
					}, 1200);
				} else {
					swal({
						title:'Error!',
						text: rs,
						type:'error'
					})
				}
			}
		})
  })
}


$('#code').keyup(function(e){
	if(e.keyCode === 13){
		$('#name').focus();
	}
})

$('#name').keyup(function(e){
	if(e.keyCode === 13){
		$('#zone').focus();
	}
})



$('#zone').autocomplete({
	source:HOME + 'get_zone_code_and_name',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length === 2) {
			$('#zone_code').val(arr[0]);
			$('#zone').val(arr[1]);
			$('#customer').focus();
		}
		else {
			$('#zone_code').val('');
			$('#zone').val('');
		}
	}
});


$('#customer').autocomplete({
	source:HOME + 'get_customer_code_and_name',
	autoFocus:true,
	close:function() {
		var rs = $(this).val()
		var arr = rs.split(' | ')
		if(arr.length == 2) {
			$('#customer_code').val(arr[0])
			$('#customer').val(arr[1])
		}
		else {
			$('#customer_code').val('')
			$('#customer').val('')
		}
	}
})


function toggleActive(option) {
	$('#active').val(option)

	if(option == 1) {
		$('#btn-active-yes').addClass('btn-success')
		$('#btn-active-no').removeClass('btn-danger')
		return
	}

	if(option == 0) {
		$('#btn-active-yes').removeClass('btn-success')
		$('#btn-active-no').addClass('btn-danger')
		return
	}
}


function toggleVat(option) {
	$('#use_vat').val(option);

	if(option == 1) {
		$('#btn-vat-yes').addClass('btn-success')
		$('#btn-vat-no').removeClass('btn-success')
		return
	}

	if(option == 0) {
		$('#btn-vat-yes').removeClass('btn-success')
		$('#btn-vat-no').addClass('btn-success')
		return
	}
}


$('#user-box').autocomplete({
	source:HOME + 'get_user_and_name',
	autoFocus:true,
	close:function() {
		var user = $(this).val();
		var arr = user.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
})


function add_user() {
	var shop_id = $('#shop_id').val();
	var uname = $.trim($('#user-box').val());
	if(uname.length > 0)
	{
		load_in();
		$.ajax({
			url:HOME + 'add_user',
			type:'POST',
			cache:false,
			data:{
				'shop_id' : shop_id,
				'uname' : uname
			},
			success:function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var data = $.parseJSON(rs);
					var source = $('#row-template').html();
					var output = $('#table');
					render_append(source, data, output);
					reIndex();
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						$('#user-box').val('');
						$('#user-box').focus();
					},1100);


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
				load_out();
				var errorMessage = xhr.status +': '+ xhr.statusText;
				swal({
					title:'Error!',
					text:'Error-' + errorMessage,
					type:'error'
				});
			}
		})
	}
}


function removeUser(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
			url:HOME + 'remove_user',
			type:'POST',
			cache:false,
			data:{
				'id' : id
			},
			success:function(rs) {
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Deleted',
						text:'ลบรายการเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						$('#row-'+id).remove();
						reIndex();
					}, 1200);

				} else {
					swal({
						title:'Error!',
						text: rs,
						type:'error'
					})
				}
			},
			error:function(xhr, status, error) {
				var errorMessage = xhr.status + ": "+xhr.statusText;
				swal({
					title:'Error!',
					text:'Error-'+ errorMessage,
					type:'error'
				})
			}
		})
  })
}
