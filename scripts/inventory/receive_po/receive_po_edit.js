// JavaScript Document

function save(){
	var code = $('#code').val();
	load_in();
	$.ajax({
		url:HOME + 'save/'+code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs === 'success'){
				swal({
					title:'Saved',
					text:'Save success',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1500);
			}else{
				swal({
					title:'Error!',
					text: rs,
					type:'error'
				});
			}
		}
	});
}


function unSave(){
	var code = $('#code').val();

	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิกการบันทึก '" + code + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้อง',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'unsave/'+ code,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Unsaved',
              type: 'success',
              timer: 1000 });
						goEdit(code);
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}



function editHeader(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader() {
	let h = {
		'code' : $('#code').val(),
		'date_add' : $('#date-add').val(),
		'post_date' : $('#post-date').val(),
		'venderCode' : $('#venderCode').val().trim(),
		'poCode' : $('#poCode').val().trim(),
		'invoice' : $('#invoice').val().trim(),
		'warehouse_code' : $('#warehouse').val(),
		'remark' : $('#remark').val().trim()
	}

  if( ! isDate(h.date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if( ! isDate(h.post_date)) {
		swal('วันที่รับไม่ถูกต้อง');
		return false;
	}

  if(venderCode.length == 0){
    swal('กรุณาระบุผู้ผลิต');
    return false;
  }

  if(invoice.length == 0){
    swal('กรุณาระบุใบส่งสินค้า');
    return false;
  }

  if(h.warehouse_code == "") {
    swal('กรุณาเลือกคลัง');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
			'data' : JSON.stringify(h)
    },
    success:function(rs){
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
}


$('#poCode').keyup(function(e) {
	if(e.keyCode == 13){
		$('#invoice').focus();
	}
});


function removeRow(id, itemCode){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '" + itemCode + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'delete_detail/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000
						});

						let code = $('#code').val();
						updateReceiveTable(code);
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}


function removeAll(){
	var code = $('#code').val();
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบรายการทั้งหมดหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'delete_details/'+ code,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						updateReceiveTable(code);

						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000
						 });

					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}
