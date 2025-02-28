function saveConsign(){
  var code = $('#consign_code').val();
  swal({
		title: "บันทึกขายและตัดสต็อก",
		text: "เมื่อบันทึกแล้วจะไม่สามารถแก้ไขได้ ต้องการบันทึกหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#8CC152",
		confirmButtonText: 'บันทึก',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      load_in();
      $.ajax({
        url: HOME + 'save_consign',
        type:'POST',
        cache:'false',
        data:{
          'code' : code
        },
        success:function(rs) {
          load_out();
          if(rs.trim() == 'success') {
            swal({
              title:'Saved',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              viewDetail(code);
            },1200);
          }
          else {
            swal({
              title:'Error!',
              text:rs,
              type:'error',
              html:true
            })
          }
        },
        error:function(rs) {
          load_out();

          swal({
            title:'Error!',
            text:rs.responseText,
            type:'error',
            html:true
          })
        }
      });
	});
}


$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      zoneInit(code, true);
      $('#zone_code').focus();
		}
    else {
			$("#customerCode").val('');
			$(this).val('');
      zoneInit('');
		}
	}
});


$("#customerCode").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      zoneInit(code, true);
      $('#zone_code').focus();
		}
    else {
			$("#customerCode").val('');
			$(this).val('');
      zoneInit('');
		}
	}
});


//---	กำหนดให้สามารถค้นหาโซนได้ก่อนจะค้นหาลูกค้า(กรณี edit header)
$(document).ready(function(){
	var customer_code = $('#customerCode').val();
	zoneInit(customer_code, false);
});


function zoneInit(customer_code, edit) {
  if(edit) {
    $('#zone_code').val('');
    $('#zone').val('');
  }

  $('#zone').autocomplete({
    source:BASE_URL + 'auto_complete/get_consign_zone/' + customer_code,
    autoFocus: true,
    close:function(){
      let rs = $.trim($(this).val());
      let arr = rs.split(' | ');
      if(arr.length == 2)
      {
        let code = arr[0];
        let name = arr[1];
        $('#zone_code').val(code);
        $('#zone').val(name);
      }
      else {
        $('#zone_code').val('');
        $('#zone').val('');
      }
    }
  })

  $('#zone_code').autocomplete({
    source:BASE_URL + 'auto_complete/get_consign_zone/'+customer_code,
    autoFocus:true,
    close:function() {
      let rs = $(this).val().trim();
      let arr = rs.split(' | ');
      if(arr.length == 2) {
        let code = arr[0];
        let name = arr[1];

        $('#zone_code').val(code);
        $('#zone').val(name);
      }
      else {
        $('#zone_code').val('');
        $('#zone').val('');
      }
    }
  })
}


function add() {
  $('.e').clearError();

  let h = {
    'customer_code' : $('#customerCode').val().trim(),
    'customer_name' : $('#customer').val().trim(),
    'date_add' : $('#date').val(),
    'zone_code' : $('#zone_code').val().trim(),
    'zone_name' : $('#zone').val().trim(),
    'remark' : $('#remark').val().trim()
  }


  if(h.customer_code.length == 0 || h.customer_name.length == 0) {
    $('#customerCode').hasError();
    $('#customer').hasError();

    return false;
  }

  if( ! isDate(h.date_add))
  {
    $('#date').hasError();
    return false;
  }

  if(h.zone_code.length == 0 || h.zone_name.length == 0)
  {
    $('#zone').hasError();
    $('#zone_code').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          goEdit(ds.code);
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error',
            html:true
          })
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        })
      }
    },
    error:function(rs) {
      load_out();

      swal({
        title:'Error!',
        text:rs.responseText,
        type:'error',
        html:true
      })
    }
  })

  $('#addForm').submit();
}


var customer;
var payment;
var date;


function getEdit(){
  $('.e').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function getUpdate() {
  $('.e').clearError();

  let code = $('#consign_code').val();
  let date = $('#date').val();
  let remark = $('#remark').val().trim();
  let customer_code = $('#customer_code').val();
  let customer_name = $('#customer').val().trim();
  let prev_customer_code = $('#prev-customer-code').val();
  let prev_customer_name = $('#prev-customer-name').val();
  let zone_code = $('#zone_code').val().trim();
  let zone_name = $('#zone').val();
  let prev_zone_code = $('#prev-zone-code').val().trim();
  let rows = $('.rox').length;

  if( ! isDate(date)) {
    $('#date').hasError();
    return false;
  }

  if(customer_code.length == 0 || customer_name.length == 0) {
    $('#customerCode').hasError();
    $('#customer').hasError();
    return false;
  }

  if(zone_code.length == 0 || zone_name.length == 0) {
    $('#zone_code').hasError();
    $('#zone').hasError();
    return false;
  }

  if(zone_code !== prev_zone_code && rows > 0) {
    swal({
      title:'คำเตือน !',
      text:'เนื่องจากโซนมีการเปลี่ยนแปลง รายการที่มีอยู่จะถูกลบทั้งหมด<br/> ต้องการดำเนินการต่อหรือไม่ ?',
      type:'warning',
      html:true,
      showCancelButton:true,
      cancelButtonText:'No',
      confirmButtonText:'Yes',
      closeOnConfirm:true
    },
    function() {
      update();
    })
  }
  else {
    update();
  }
}


function update() {
  let h = {
    'code' : $('#consign_code').val(),
    'date_add' : $('#date').val(),
    'customer_code' : $('#customer_code').val(),
    'customer_name' : $('#customer').val().trim(),
    'zone_code' : $('#zone_code').val().trim(),
    'zone_name' : $('#zone').val().trim(),
    'remark' : $('#remark').val().trim()
  };

  load_in();

  setTimeout(() => {
    $.ajax({
      url: HOME + 'update',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(rs.trim() == 'success'){
          swal({
            title:'Updted',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error',
            html:true
          });
        }
      },
      error:function(rs) {
        load_out();

        swal({
          title:'Error!',
          text:rs.responseText,
          type:'error',
          html:true
        });
      }
    })
  }, 100);
}


function deleteRow(id, code){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: true
		}, function(){
      deleteDetail(id);
	});
}


function deleteDetail(id){
  $.ajax({
    url: HOME + 'delete_detail/'+id,
    type:'POST',
    cache:'false',
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){

        swal({
          title:'Deleted',
          type:'success',
          timer:1000
        });
        $('#row-'+id).remove();
        reIndex();
        updateTotalQty();
        updateTotalAmount();
      }
    }
  });
}


function deleteChecked() {
  if($('.chk:checked').length > 0) {

    swal({
      title: "คุณแน่ใจ ?",
      text: "ต้องการลบรายการที่เลือกหรือไม่ ?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#FA5858",
      confirmButtonText: 'ใช่, ฉันต้องการลบ',
      cancelButtonText: 'ยกเลิก',
      closeOnConfirm: true
    },
    function() {
      let h = {
        'code' : $('#consign_code').val(),
        'rows' : []
      };


      $('.chk:checked').each(function() {
        h.rows.push($(this).val());
      });

      if(h.rows.length > 0) {
        load_in();
        setTimeout(() => {
          $.ajax({
            url:HOME + 'delete_details',
            type:'POST',
            cache:false,
            data:{
              'data' : JSON.stringify(h)
            },
            success:function(rs) {
              load_out();

              if(rs.trim() == 'success') {
                window.location.reload();
              }
              else {
                swal({
                  title:'Error!',
                  text:rs,
                  type:'error',
                  html:true
                })
              }
            },
            error :function(rs) {
              load_out();

              swal({
                title:'Error!',
                text:rs.responseText,
                type:'error',
                html:true
              })
            }
          })
        }, 100);
      }
    });
  }
}


//--- ลบรายการนำเข้ายอดต่าง
function clearImportDetail(check_code){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบรายการนำเข้าจาก '"+ check_code +"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
    closeOnConfirm:false
		}, function(){
      load_in();
      var code = $('#consign_code').val();

      $.ajax({
        url: HOME + 'remove_import_details/'+code,
        type:'POST',
        cache:'false',
        data:{
          'check_code' : check_code
        },
        success:function(rs){
          load_out();
          var rs = $.trim(rs);
          if(rs == 'success'){
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(function(){
              window.location.reload();
            }, 1500);

          }else{
            swal('Error!', rs, 'error');
          }
        }
      });
	});
}


function getActiveCheckList(){
  var zone_code = $('#zone_code').val();
  load_in();
  $.ajax({
    url:HOME + 'get_active_check_list/'+zone_code,
    type:'GET',
    cache:'false',
    success:function(rs){
      load_out();
      if(isJson(rs)){
        var source = $('#check-list-template').html();
        var data = $.parseJSON(rs);
        var output = $('#check-list-body');
        render(source, data, output);
        $('#check-list-modal').modal('show');
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}



function loadCheckDiff(check_code){
  $('#check-list-modal').modal('hide');
  swal({
    title: "นำเข้ายอดต่าง",
		text: "ต้องการนำเข้ายอดต่างจากเอกสารกระทบยอด "+check_code+" หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    var code = $('#consign_code').val();
    load_in();
    $.ajax({
      url: HOME + 'load_check_diff/'+code,
      type:'POST',
      cache:'false',
      data:{
        'check_code' : check_code
      },
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if(rs == 'success'){
          swal({
            title: 'Success',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            window.location.reload();
          },1500);
        }else{
          swal('Error!', rs, 'error');
        }
      }
    });

  });//--- swal
}


function getSample(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = HOME + 'get_sample_file/'+token;
}



function getUploadFile(target){
  $('#target').val(target);
  $('#upload-modal').modal('show');
}



function getFile(){
  $('#uploadFile').click();
}


$("#uploadFile").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if( size > 5000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}
		//readURL(this);
    $('#show-file-name').text(name);
	}
});



function uploadfile(){
  var code = $('#consign_code').val();
  var target = $('#target').val();
  var excel = $('#uploadFile')[0].files[0];
  let url = HOME + 'import_excel_file/'+code;

  if(target == '1') {
    url = HOME + 'import_pos_file/'+code;
  }

	$("#upload-modal").modal('hide');

	var fd = new FormData();

	fd.append('excel', $('input[type=file]')[0].files[0]);
	load_in();

	$.ajax({
		// url:HOME + 'import_excel_file/'+code,
    url:url,
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
          title:'Success',
          type:'success',
          timer: 1000
        });

				setTimeout(function(){
          window.location.reload();
        }, 1200);
			}
			else
			{
				swal("Error!", rs, "error");
			}
		}
	});
}
