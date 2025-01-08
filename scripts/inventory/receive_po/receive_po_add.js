// JavaScript Document

var data = [];
var poError = 0;
var invError = 0;
var zoneError = 0;


$('#venderCode').autocomplete({
  source: BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  open:function(event, ui){
    $(this).autocomplete("widget").css({
      'width' : 'auto',
      'min-width' : $(this).width() + 'px'
    })
  },
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length == 2){
      $('#venderCode').val(arr[0]);
      $('#venderName').val(arr[1]);
    }else{
      $('#venderCode').val('');
      $('#venderName').val('');
    }
  }
});


$('#venderName').autocomplete({
  source: BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  open:function(event, ui){
    $(this).autocomplete("widget").css({
      'width' : 'auto',
      'min-width' : $(this).width() + 'px'
    })
  },
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length == 2){
      $('#venderCode').val(arr[0]);
      $('#venderName').val(arr[1]);
    }else{
      $('#venderCode').val('');
      $('#venderName').val('');
    }
  }
});

$('#venderName').focusout(function(event) {
	if($(this).val() == ''){
		$('#venderCode').val('');
	}
	poInit();
});

$('#venderCode').focusout(function(event) {
	if($(this).val() == ''){
		$('#venderName').val('');
	}
	poInit();
});



$('#venderCode').keyup(function(e) {
	if(e.keyCode == 13){
		if($(this).val().length > 0){
			$('#poCode').focus();
		}
	}
});



function add()
{
  let h = {
    'date_add' : $('#date-add').val(),
    'post_date' : $('#post-date').val(),
    'venderCode' : $('#venderCode').val().trim(),
    'poCode' : $('#poCode').val().trim(),
    'invoice' : $('#invoice').val().trim(),
    'warehouse_code' : $('#warehouse').val(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  if( ! isDate(h.post_date)) {
    swal('วันที่รับไม่ถูกต้อง');
    return false;
  }

  if(h.venderCode.length == 0){
    swal('กรุณาระบุผู้ขาย');
    return false;
  }

  if(h.poCode.length == 0){
    swal('กรุณาระบุใบสั่งผลิต');
    return false;
  }

  if(h.invoice.length == 0){
    swal('กรุณาระบุใบส่งสินค้า');
    return false;
  }

  if(h.warehouse_code == ""){
    swal('กรุณาเลือกคลัง');
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
          });
        }
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
      })
    }
  })
}



$(document).ready(function() {
	poInit();
});


function poInit(){
	var vender_code = $('#venderCode').val();
	if(vender_code == ''){
		$("#poCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_po_code_and_vender_name',
			autoFocus: true,
			open: function(event, ui) {
				$(this).autocomplete("widget").css({
		            "width": "auto",
								"min-width" : ($(this).width() + "px")
		        });
		    },
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[0]);
          updateVender(arr[0]);
				}else{
					$(this).val('');
				}
			}
		});
	}else{
		$("#poCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_po_code_and_vender_name/'+vender_code,
			autoFocus: true,
			open: function(event, ui) {
				$(this).autocomplete("widget").css({
		            "width": "auto",
								"min-width" : ($(this).width() + "px")
		        });
		    },
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[0]);
          updateVender(arr[0]);
				}else{
					$(this).val('');
				}
			}
		});
	}
}


$('#poCode').keyup(function(e) {
	if(e.keyCode == 13){
		$('#invoice').focus();
	}
});


//---- ดึงรหัสและชื่อผู้ขายจากเลขที่ PO
function updateVender(poCode){
  $.get(HOME+'get_vender_by_po/'+poCode, function(rs){
    var arr = rs.split(' | ');
    if(arr.length == 2){
      $('#venderCode').val(arr[0]);
      $('#venderName').val(arr[1]);
    }
    poInit();
  });
}


$("#date-add").datepicker({ dateFormat: 'dd-mm-yy'});
$('#post-date').datepicker({ dateFormat:'dd-mm-yy'});
$('#receive-date').datepicker({ dateFormat:'dd-mm-yy'});
