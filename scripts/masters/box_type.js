var HOME = BASE_URL + 'masters/box_type/';

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

	if(code.length == 0){
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

	load_in();
	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name
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

	if(code.length == 0){
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

	load_in();
	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name
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
