var HOME = BASE_URL + 'masters/vender/';

function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}



function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code) {
	window.location.href = HOME + 'view_detail/'+code;
}



function toggleActive(option){
  $('#active').val(option);
  if(option == 1){
    $('#active-on').addClass('btn-success');
    $('#active-off').removeClass('btn-danger');
  }
  else
  {
    $('#active-on').removeClass('btn-success');
    $('#active-off').addClass('btn-danger');
  }
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });

}



function save() {
	var code = $.trim($('#code').val());
	var name = $.trim($('#name').val());
	var term = parseDefaultValue($('#credit_term').val(), 0, 'int');
	var tax_id = $.trim($('#tax_id').val());
	var branch = $.trim($('#branch_name').val());
	var address = $.trim($('#address').val());
	var phone = $.trim($('#phone').val());
	var active = $('#active').val();

	if(code.length == 0) {
		set_error($('#code'), $('#code-error'), "รหัสไม่ถูกต้อง");
		return false;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}

	if(name.length == 0) {
		set_error($('#name'), $('#name-error'), "ชื่อไม่ถูกต้อง");
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	$.ajax({
		url:HOME + 'is_exists_code',
		type:'POST',
		cache:false,
		data:{
			"code" : code
		},
		success:function(rs) {
			if(rs === 'success') {
				$.ajax({
					url:HOME + 'add',
					type:'POST',
					cache:false,
					data:{
						'code' : code,
						'name' : name,
						'term' : term,
						'tax_id' : tax_id,
						'branch' : branch,
						'address' : address,
						'phone' : phone,
						'active' : active
					},
					success:function(rs) {
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
								text: rs,
								type:'error'
							});
						}
					}
				})
			}
			else {
				swal({
					title:'รหัสซ้ำ',
					text:'กรุณาใช้รหัสใหม่',
					type:'error'
				});
			}
		}
	})
}



function update() {
	var code = $.trim($('#code').val());
	var name = $.trim($('#name').val());
	var old_name = $('#old_name').val();
	var term = parseDefaultValue($('#credit_term').val(), 0, 'int');
	var tax_id = $.trim($('#tax_id').val());
	var branch = $.trim($('#branch_name').val());
	var address = $.trim($('#address').val());
	var phone = $.trim($('#phone').val());
	var active = $('#active').val();


	if(name.length == 0) {
		set_error($('#name'), $('#name-error'), "ชื่อไม่ถูกต้อง");
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
			'old_name' : old_name,
			'term' : term,
			'tax_id' : tax_id,
			'branch' : branch,
			'address' : address,
			'phone' : phone,
			'active' : active
		},
		success:function(rs) {
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
					text: rs,
					type:'error'
				});
			}
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
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
				else {
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



$('.filter').change(function(){
  getSearch();
});


$('.filter').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function getSearch(){
  $('#searchForm').submit();
}
