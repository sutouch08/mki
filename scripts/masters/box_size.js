var HOME = BASE_URL + 'masters/box_size/';

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


$('.search').keyup(function(e){
	if(e.keyCode === 13){
		getSearch();
	}
});


function save() {
	var code = $('#code').val();
	var name = $('#name').val();
	var box_type = $('#box_type').val();
	var box_width = $('#box_width').val();
	var box_length = $('#box_length').val();
	var box_height = $('#box_height').val();

	if(code.length == 0) {
		$('#code').addClass('has-error');
		return false;
	} else {
		$('#code').removeClass('has-error');
	}


	if(name.length == 0) {
		$('#name').addClass('has-error');
		return false;
	} else {
		$('#name').removeClass('has-error');
	}

	if(box_type.length == 0) {
		$('#box_type').addClass('has-error');
		return false;
	} else {
		$('#box_type').removeClass('has-error');
	}

	load_in();
	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'box_type' : box_type,
			'box_width' : box_width,
			'box_length' : box_length,
			'box_height' : box_height
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					text:'เพิ่มรายการเรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				$('#code').val('');
				$('#name').val('');
				$('#box_type').val('');
				$('#box_width').val('');
				$('#box_length').val('');
				$('#box_height').val('');
				$('#code').focus();

			} else {
				swal({
					title:"Error!!",
					text:rs,
					type:'error'
				})
			}
		}
	})
}


function update() {
	var code = $('#code').val();
	var name = $('#name').val();
	var box_type = $('#box_type').val();
	var box_width = $('#box_width').val();
	var box_length = $('#box_length').val();
	var box_height = $('#box_height').val();

	if(code.length == 0) {
		swal({
			title:'Error!',
			text:'ไม่พบรหัส',
			type:'error'
		});

		return false;
	}


	if(name.length == 0) {
		$('#name').addClass('has-error');
		return false;
	} else {
		$('#name').removeClass('has-error');
	}

	if(box_type.length == 0) {
		$('#box_type').addClass('has-error');
		return false;
	} else {
		$('#box_type').removeClass('has-error');
	}

	load_in();
	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'box_type' : box_type,
			'box_width' : box_width,
			'box_length' : box_length,
			'box_height' : box_height
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					text:'ปรับปรุงรายการเรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

			} else {
				swal({
					title:"Error!!",
					text:rs,
					type:'error'
				})
			}
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
		$('#box_type').focus();
	}
})

$('#box_type').keyup(function(e){
	if(e.keyCode === 13){
		$('#box_width').focus();
	}
})


$('#box_width').keyup(function(e){
	if(e.keyCode === 13){
		$('#box_length').focus();
	}
})

$('#box_length').keyup(function(e){
	if(e.keyCode === 13){
		$('#box_height').focus();
	}
})

$('#box_height').keyup(function(e){
	if(e.keyCode === 13){
		$('#btn-save').click();
	}
})
