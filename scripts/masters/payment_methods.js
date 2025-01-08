var HOME = BASE_URL + 'masters/payment_methods/';

function addNew(){
  window.location.href = BASE_URL + 'masters/payment_methods/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/payment_methods';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/payment_methods/edit/'+code;
}


function clearFilter(){
  var url = BASE_URL + 'masters/payment_methods/clear_filter';
  var page = BASE_URL + 'masters/payment_methods';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function getDelete(code, name){
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
    window.location.href = BASE_URL + 'masters/payment_methods/delete/' + code;
  })
}



function getSearch(){
  $('#searchForm').submit();
}



$('.search').keyup(function(e){
	if(e.keyCode === 13){
		getSearch();
	}
})



function save_add() {
	var code = $('#code').val();
	var name = $('#name').val();
	var role = $('#role').val();
	var acc_no = $('#acc_no').val();
	var error = 0;

	if(code.length === 0) {
		set_error($('#code'), $('#code-error'), "Required");
		error++;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}

	if(name.length === 0) {
		set_error($('#name'), $('#name-error'), "Required");
		error++;
	}
	else {
		clear_error($('#name'), $('#name-error'))
	}

	if(role == "") {
		set_error($('#role'), $('#role-error'), "Required");
		error++;
	}
	else {
		clear_error($('#role'), $('#role-error'));
	}


	if(error > 0) {
		return false;
	}

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'role' : role,
			'acc_no' : acc_no
		},
		success:function(rs) {
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
				});
			}
		},
		error:function(xhr, status, error) {
			swal({
				title:'Error!',
				text:'Error-'+xhr.status + ' : ' + xhr.statusText,
				type:'error'
			})
		}
	})
}



function update() {
	var code = $('#code').val();
	var name = $('#name').val();
	var old_name = $('#old_name').val();
	var role = $('#role').val();
	var acc_no = $('#acc_no').val();
	var is_default = $('#is_default').is(':checked') ? 1 : 0

	var error = 0;

	if(code.length === 0) {
		set_error($('#code'), $('#code-error'), "Required");
		error++;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}

	if(name.length === 0) {
		set_error($('#name'), $('#name-error'), "Required");
		error++;
	}
	else {
		clear_error($('#name'), $('#name-error'))
	}

	if(role == "") {
		set_error($('#role'), $('#role-error'), "Required");
		error++;
	}
	else {
		clear_error($('#role'), $('#role-error'));
	}


	if(error > 0) {
		return false;
	}

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'old_name' : old_name,
			'role' : role,
			'acc_no' : acc_no,
			'is_default' : is_default
		},
		success:function(rs) {
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
				});
			}
		},
		error:function(xhr, status, error) {
			swal({
				title:'Error!',
				text:'Error-'+xhr.status + ' : ' + xhr.statusText,
				type:'error'
			})
		}
	})
}


function toggleRole() {
	var role = $('#role').val();
	$('#bank_account').addClass('hide');
	if(role == 3) {
		$('#bank_account').removeClass('hide');
	}

}
