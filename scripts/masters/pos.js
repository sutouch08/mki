var HOME = BASE_URL + 'masters/pos/';

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
	var prefix = $('#prefix').val();
	var pos_code = $('#pos_code').val();
	var pos_no = $('#pos_no').val();
	var shop_id = $('#shop').val();
	var active = $('#active').val();
	var paper_size = $('#paper_size').val();

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


	if(prefix.length === 0 ) {
		$('#prefix').addClass('has-error')
		return false;
	}
	else {
		$('#prefix').removeClass('has-error');
	}

	if(shop_id === "") {
		$('#shop').addClass('has-error')
		return false;
	}
	else {
		$('#shop').removeClass('has-error');
	}


	load_in();
	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'prefix' : prefix,
			'pos_no' : pos_no,
			'pos_code' : pos_code,
			'shop_id' : shop_id,
			'active' : active,
			'paper_size' : paper_size
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
	var prefix = $('#prefix').val();
	var pos_code = $('#pos_code').val();
	var pos_no = $('#pos_no').val();
	var shop_id = $('#shop').val();
	var active = $('#active').val();
	var paper_size = $('#paper_size').val();


	if(name.length === 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}


	if(prefix.length === 0 ) {
		$('#prefix').addClass('has-error')
		return false;
	}
	else {
		$('#prefix').removeClass('has-error');
	}

	if(shop_id === "") {
		$('#shop').addClass('has-error')
		return false;
	}
	else {
		$('#shop').removeClass('has-error');
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
			'prefix' : prefix,
			'pos_no' : pos_no,
			'pos_code' : pos_code,
			'shop_id' : shop_id,
			'active' : active,
			'paper_size' : paper_size
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
		$('#prefix').focus();
	}
})

$('#prefix').keyup(function(e){
	if(e.keyCode === 13){
		$('#pos_no').focus();
	}
})

$('#pos_no').keyup(function(e){
	if(e.keyCode === 13){
		$('#pos_code').focus();
	}
})

$('#pos_code').keyup(function(e){
	if(e.keyCode === 13){
		$('#shop').focus();
	}
})

$('input[type=text]').focus(function(){
	$(this).select();
});






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
