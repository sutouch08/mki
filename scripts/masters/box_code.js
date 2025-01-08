var HOME = BASE_URL + 'masters/box_code/';

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


$('#box-code').keyup(function(e){
	if(e.keyCode === 13) {
		addBoxCode();
	}
})


function addBoxCode(){
	let size = $('#box-size').val();
	let code = $('#box-code').val();

	if(size.length == 0) {
		swal('Error!', 'กรุณาระบุขนาดกล่อง', 'error');
		return false;
	}

	if(code.length == 0)
	{
		return false;
	}

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"size_code" : size,
			"code" : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)){
				let data = $.parseJSON(rs);
				let source = $("#added-template").html();
				let output = $('#added-table');

				render_append(source, data, output);
				reIndex();

				$('#box-code').val('');
				$('#box-code').focus();

			} else {
				swal({
					title:'Error !',
					text: rs,
					type:'error'
				});
			}
		}
	})
}



function update() {
	let code = $('#code').val();
	let size = $('#box-size').val();

	if(size.length == 0) {
		$('#box-size').addClass('has-error');
		return false;
	} else {
		$('#box-size').removeClass('has-error');
	}

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'size_code' : size
		},
		success:function(rs) {
			rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Updated',
					text:'ปรับปรุงรายการเรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});
			} else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
}



function getTemplate(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = HOME + 'get_sample_file/'+token;
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
