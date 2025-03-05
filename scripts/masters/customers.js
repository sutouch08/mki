var HOME = BASE_URL + 'masters/customers/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


function changeURL(code, tab)
{

	var url = HOME + 'edit/' + code + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'customers', url);
}


function changeView(code, tab)
{

	var url = HOME + 'view_detail/' + code + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'customers', url);
}



function saveAdd() {
	var code = $('#code').val();
  var run_no = $('#code').data('runno');
	var name = $('#name').val();
	var tax_id = $('#Tax_id').val();
	var group = $('#group').val();
	var kind = $('#kind').val();
	var type = $('#type').val();
	var grade = $('#class').val();
	var area = $('#area').val();
  var channels = $('#channels').val();
	var sale = $('#sale').val();
	var credit_term = $('#credit_term').val();
	var credit_amount = $('#CreditLine').val();
	var note = $('#note').text();

	if(code.length == 0) {
		set_error($('#code'), $('#code-error'), 'กรุณาระบุรหัสลูกค้า');
		return false;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}


	if(name.length == 0) {
		set_error($('#name'), $('#name-error'), 'กรุณาระบุชื่อลูกค้า');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	load_in();
	$.ajax({
		url: HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
      'run_no' : run_no,
			'name' : name,
			'Tax_id' : tax_id,
			'group' : group,
			'kind' : kind,
			'type' : type,
			'class' : grade,
			'area' : area,
      'channels' : channels,
			'sale' : sale,
			'credit_term' : credit_term,
			'CreditLine' : credit_amount,
			'note' : note
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs)
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer: 1000
				});

				setTimeout(function() {
					addNew();
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text: rs,
					type:'error'
				})
			}
		},
		error:function(xhr, status, error) {
			load_out();
			var errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:"Error-" + errorMessage,
				type:'error'
			})
		}
	})
}



function update() {
	var code = $('#code').val();
	var name = $('#name').val();
	var old_name = $('#old_name').val();
	var tax_id = $('#Tax_Id').val();
	var group = $('#group').val();
	var kind = $('#kind').val();
	var type = $('#type').val();
	var grade = $('#class').val();
	var area = $('#area').val();
  var channels = $('#channels').val();
	var sale = $('#sale').val();
	var credit_term = $('#credit_term').val();
	var credit_amount = $('#CreditLine').val();
	var note = $('#note').text();

	if(name.length == 0) {
		set_error($('#name'), $('#name-error'), 'กรุณาระบุชื่อลูกค้า');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	load_in();
	$.ajax({
		url: HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'old_name' : old_name,
			'Tax_id' : tax_id,
			'group' : group,
			'kind' : kind,
			'type' : type,
			'class' : grade,
			'area' : area,
      'channels' : channels,
			'sale' : sale,
			'credit_term' : credit_term,
			'CreditLine' : credit_amount,
			'note' : note
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs)
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer: 1000
				});
			}
			else {
				swal({
					title:'Error!',
					text: rs,
					type:'error'
				})
			}
		},
		error:function(xhr, status, error) {
			load_out();
			var errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:"Error-" + errorMessage,
				type:'error'
			})
		}
	})
}



function clearFilter() {
  $.get(HOME + 'clear_filter', function(rs){
    goBack();
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

		$.ajax({
			url:HOME + 'delete',
			type:'POST',
			cache:false,
			data:{
				"code" : code
			},
			success:function(rs) {
				if(rs === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						goBack();
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



$('.filter').change(function(){
  getSearch();
});



function getSearch(){
  $('#searchForm').submit();
}


function get_template() {
	var token	= new Date().getTime();
	get_download(token);
	window.location.href = HOME + 'download_template/'+token;
}

$('#code').focusout(function() {
  setTimeout(() => {
    let val = $(this).val();
    let res  = val.toUpperCase();
    $(this).val(res);
  }, 100);
})

$('#credit_term').focus(function(){
	$(this).select();
})

$('#CreditLine').focus(function(){
	$(this).select();
})

$('#name').focus(function(){
	$(this).select();
});

$('#Tax_id').focus(function() {
	$(this).select();
})


$('#attributeModal').on('shown.bs.modal', function(){
	$('#a_code').focus();
});

function saveAttribute() {
	var attribute = $('#attribute').val();
	var code = $('#a_code').val();
	var name = $('#a_name').val();

	if(code.length === 0) {
		$('#a_code').addClass('has-error');
		return false;
	}
	else {
		$('#a_code').removeClass('has-error');
	}

	if(name.length === 0) {
		$('#a_name').addClass('has-error');
		return false;
	}
	else {
		$('#a_name').removeClass('has-error');
	}

	load_in();

	$.ajax({
		url:HOME + 'add_attribute',
		type:'POST',
		cache:false,
		data:{
			'attribute' : attribute,
			'code' : code,
			'name' : name
		},
		success:function(rs) {
			load_out();
			$('#attributeModal').modal('hide');
			var rs = $.trim(rs);
			if(rs === 'success') {
				var option = '<option value="'+code+'">'+name+'</option>';
				$('#'+attribute).append(option);
				$('#'+attribute).val(code);

				//--- reset input
				$('#attribute').val('');
				$('#a_code').val('');
				$('#a_name').val('');
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr, satus, error) {
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

var attr = {
	"group": "เพิ่มกลุ่มลูกค้า",
	"kind" : "เพิ่มประเภทลูกค้า",
	"type" : "เพิ่มชนิดลูกค้า",
	"class" : "เพิ่มเกรดลูกค้า",
	"area" : "เพิ่มเขตการขาย"
}


function addAttribute(attribute){
	$('#title').text(attr[attribute]);
	$('#attribute').val(attribute);
	$('#a_code').val('');
	$('#a_name').val('');

	$('#attributeModal').modal('show');
}
