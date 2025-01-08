var HOME = BASE_URL + 'masters/items/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function duplicate(code){
  window.location.href = HOME + 'duplicate/'+code;
}



$('#style').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code_and_name',
  autoFocus:true,
	close:function(){
		var style = $(this).val();
		var arr = style.split(' | ');
		if(arr.length === 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
});



$('#color').autocomplete({
  source: BASE_URL + 'auto_complete/get_color_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


$('#size').autocomplete({
  source:BASE_URL + 'auto_complete/get_size_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


function checkAdd(){
	var code = $('#code').val();
	var name = $('#name').val();

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


  if(code.length > 0){
    $.ajax({
      url:HOME + 'is_exists_code/'+code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs != 'ok'){
          set_error($('#code'), $('#code-error'), rs);
          return false;
        }else{
          clear_error($('#code'), $('#code-error'));
          $('#addForm').submit();
        }
      }
    })
  }
}


function checkEdit(){
	var name = $('#name').val();

	if(name.length === 0) {
		set_error($('#name'), $('#name-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	$('#addForm').submit();

}


function clearFilter(){
  var url = HOME + 'clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(){
    goBack();
  });
}


$('#price').focus(function(){
	$(this).select();
})

$('#cost').focus(function(){
	$(this).select();
})

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
      url: BASE_URL + 'masters/items/delete_item/' + code,
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

function getTemplate(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = BASE_URL + 'masters/items/download_template/'+token;
}

$('#style').keyup(function(e){
	if(e.keyCode === 13) {
		$('#color').focus();
	}
})

$('#color').keyup(function(e){
	if(e.keyCode === 13) {
		$('#size').focus();
	}
})

$('#size').keyup(function(e){
	if(e.keyCode === 13) {
		$('#barcode').focus();
	}
})

$('#barcode').keyup(function(e){
	if(e.keyCode === 13) {
		$('#cost').focus();
	}
})


$('#cost').keyup(function(e){
	if(e.keyCode === 13) {
		$('#price').focus();
	}
})

$('#price').keyup(function(e){
	if(e.keyCode === 13) {
		$('#unit_code').focus();
	}
})

function getSearch(){
  $('#searchForm').submit();
}


function changeImage() {
	$('#imageModal').modal('show');
}

function doUpload()
{
	var code = $('#code').val();
	var image	= $("#image")[0].files[0];

	if( image == '' ){
		swal('ข้อผิดพลาด', 'ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง', 'error');
		return false;
	}


	$("#imageModal").modal('hide');

	var fd = new FormData();
	fd.append('image', $('input[type=file]')[0].files[0]);
	fd.append('code', code);

	load_in();

	$.ajax({
		url: HOME + 'change_image',
		type:"POST",
		cache: "false",
		data: fd,
		processData:false,
		contentType: false,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({
					title : 'Success',
					type: 'success',
					timer: 1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1200);

			}
			else
			{
				swal("ข้อผิดพลาด", rs, "error");
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:"Error-"+xhr.status+": "+xhr.statusText,
				type:'error'
			})
		}
	});
}

function readURL(input)
{
	 if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#previewImg').html('<img id="previewImg" src="'+e.target.result+'" width="200px" alt="รูปสินค้า" />');
				}
				reader.readAsDataURL(input.files[0]);
		}
}

$("#image").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;
		if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
		{
			swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
			$(this).val('');
			return false;
		}

		if( size > 2000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 2 MB", "error");
			$(this).val('');
			return false;
		}

		readURL(this);

		$("#btn-select-file").css("display", "none");
		$("#block-image").animate({opacity:1}, 1000);
	}
});


function removeFile()
{
	$("#previewImg").html('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');
	$("#image").val('');
}


function deleteImage()
{
	var code = $('#code').val();
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบรูปภาพ หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      $.ajax({
    		url: HOME + 'delete_image',
    		type:"POST",
        cache:"false",
        data:{
          "code" : code
        },
    		success: function(rs){
    			var rs = $.trim(rs);
    			if( rs == 'success' )
    			{
            swal({
              title:'Deleted',
              text:'ลบรูปภาพเรียบร้อยแล้ว',
              type:'success',
              timer:1000
            });

    				setTimeout(function(){
							window.location.reload();
						},1200)
    			}
    			else
    			{
    				swal({
							title:'Error!',
							text:rs,
							type:'error'
						})
    			}
    		},
				error: function(rs) {
					swal({
						title:'Error!',
						text:"Error-" + rs.status + ": "+rs.statusText,
						type:"error"
					})
				}
    	});
	});
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
	"category" : "เพิ่มหมวดหมสินค้าู่",
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
