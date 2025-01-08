var HOME = BASE_URL + 'masters/products/';

function addNew(){
  window.location.href = BASE_URL + 'masters/products/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/products';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/products/edit/'+code;
}


function changeURL(style, tab)
{

	var url = BASE_URL + 'masters/products/edit/' + style + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'products', url);
}




function newItems(){
  var style = $('#style').val();
  window.location.href = BASE_URL + 'masters/products/item_gen/' + style;
}




function clearFilter(){
  var url = BASE_URL + 'masters/products/clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function getDelete(code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: BASE_URL + 'masters/products/delete_style/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบรุ่นสินค้าเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          $('#row-'+code).remove();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}

function checkAdd() {
	var code = $('#code').val();
	var name = $('#name').val();

	if(code.length == 0) {
		set_error($('#code'), $('#code-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}

	if(name.length == 0) {
		set_error($('#name'), $('#name-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	$.ajax({
		url:HOME + 'is_style_exists/'+code,
		type:'POST',
		cache:false,
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'exists') {
				set_error($('#code'), $('#code-error'), 'รหัสซ้ำ');
				return false;
			}
			else {
				$('#addForm').submit();
			}
		}
	})


}



function getSearch(){
  $('#searchForm').submit();
}


$('#cost').focus(function() {
	$(this).select();
})

$('#price').focus(function() {
	$(this).select();
})


function doExport(code){
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/products/export_products/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}



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
			console.log(xhr);
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
	"color" : "เพิ่มสี",
	"size" : "เพิ่มไซส์",
	"unit_code" : "เพิ่มหน่วยนับ",
	"group": "เพิ่มกลุ่มสินค้า",
	"subGroup" : "เพิ่มกลุ่มย่อยสินค้า",
	"category" : "เพิ่มหมวดหมู่สินค้า",
	"kind" : "เพิ่มประเภทสินค้า",
	"type" : "เพิ่มชนิดสินค้า"
}


function addAttribute(attribute){
	$('#title').text(attr[attribute]);
	$('#attribute').val(attribute);
	$('#a_code').val('');
	$('#a_name').val('');

	$('#attributeModal').modal('show');
}
