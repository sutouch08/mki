var HOME = BASE_URL + 'masters/channels/';

function addNew(){
  window.location.href = BASE_URL + 'masters/channels/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/channels';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/channels/edit/'+code;
}


function clearFilter(){
  var url = BASE_URL + 'masters/channels/clear_filter';
  var page = BASE_URL + 'masters/channels';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function save_add() {
	var code = $('#code').val();
	var name = $('#name').val();
	var customer_code = $('#customer_code').val();
	var customer_name = $('#customer_name').val();

	if(code.length === 0) {
		set_error($('#code'), $('#code-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}

	if(name.length === 0) {
		set_error($('#name'), $('#name-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}


	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'customer_code' : customer_code,
			'customer_name' : customer_name
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					addNew();
				}, 1200)
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
				text:'Error-' + xhr.status+': '+xhr.statusText,
				type:'error'
			})
		}
	})
}


function update() {
	var code = $('#code').val();
	var name = $('#name').val();
	var old_name = $('#channels_name').val();
	var customer_code = $('#customer_code').val();
	var customer_name = $('#customer_name').val();
	var is_default = $('#is_default').is(':checked') ? 1 : 0;

	if(code.length === 0) {
		set_error($('#code'), $('#code-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}

	if(name.length === 0) {
		set_error($('#name'), $('#name-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}


	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'customer_code' : customer_code,
			'customer_name' : customer_name,
			'channels_name' : old_name,
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
				text:'Error-' + xhr.status+': '+xhr.statusText,
				type:'error'
			})
		}
	})
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
    window.location.href = BASE_URL + 'masters/channels/delete/' + code;
  })
}


$('#customer_name').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customer_code").val(code);
			$("#customer_name").val(name);
		}else{
			$("#customer_code").val('');
			$(this).val('');
		}
	}
})
