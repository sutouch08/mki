function goBack() {
  window.location.href = HOME;
}

function addNew() {
  window.location.href = HOME + 'add_new';
}

function goEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}

function getEdit() {
  $('.e').removeAttr('disabled');

  $('#btn-h-edit').addClass('hide');
  $('#btn-h-update').removeClass('hide');
}

function goChecking(id) {
  window.location.href = HOME + 'checking/'+id;
}

function viewDetail(id) {
  window.location.href = HOME + 'view_details/'+id;
}

function add() {
  let date_add = $('#date_add').val();
  let subject = $('#subject').val().trim();
  let zone_code = $('#zone_code').val().trim();
  let zone_name = $('#zone_name').val().trim();
  let allow_input_qty = $('#allow_input_qty').val();
  let remark = $('#remark').val().trim();

  if( ! isDate(date_add)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if( subject.length == 0) {
    swal("กรุณาระบุหัวข้อ");
    return false;
  }

  if( zone_code.length == 0 || zone_name.length == 0) {
    swal("กรุณาระบุโซน");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'date_add' : date_add,
      'subject' : subject,
      'zone_code' : zone_code,
      'allow_input_qty' : allow_input_qty,
      'remark' : remark
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let id = ds.check_id;
          goChecking(id);
        }
        else {
          swal({
            title:'Error',
            text:ds.message,
            type:'error'
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}

function update() {
  let id = $('#check_id').val();
  let date_add = $('#date_add').val();
  let subject = $('#subject').val().trim();
  let zone_code = $('#zone_code').val().trim();
  let zone_name = $('#zone_name').val().trim();
  let allow_input_qty = $('#allow_input_qty').val();
  let remark = $('#remark').val().trim();

  if( ! isDate(date_add)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if( subject.length == 0) {
    swal("กรุณาระบุหัวข้อ");
    return false;
  }

  if( zone_code.length == 0 || zone_name.length == 0) {
    swal("กรุณาระบุโซน");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'date_add' : date_add,
      'subject' : subject,
      'zone_code' : zone_code,
      'allow_input_qty' : allow_input_qty,
      'remark' : remark
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          $('.e').attr('disabled', 'disabled');
          $('#btn-h-update').addClass('hide');
          $('#btn-h-edit').removeClass('hide');
        }
        else {
          swal({
            title:'Error',
            text:ds.message,
            type:'error'
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}

$('#from_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#to_date').datepicker("option", "minDate", sd);
  }
});

$('#to_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#from_date').datepicker("option", "maxDate", sd);
  }
});

$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#zone_code').autocomplete({
  source:BASE_URL + 'auto_complete/get_zone_code_and_name',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      $('#zone_code').val(arr[0]);
      $('#zone_name').val(arr[1]);
    }
    else {
      $('#zone_code').val('');
      $('#zone_name').val('');
    }
  }
});


function updateCost() {
  let id = $('#check_id').val();

  load_in();

  $.ajax({
    url:HOME + 'update_cost/'+id,
    type:'POST',
    cache:false,
    success:function(rs) {
      load_out();
      if(rs === 'success') {
        swal({
          title:'Success',
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
        })
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:'Error!',
        type:xhr.responseText,
        type:'error',
        html:true
      });
    }
  })
}


function closeCheck() {
  let id = $('#check_id').val();

  swal({
    title:'ปิดการตรวจนับ',
    text:'ต้องการปิดการตรวจนับหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    confirmButtonColor:'#4caf50',
    confirmButtonText:'ปิดการตรวจนับ',
    cancelButtonText:'ยกเลิก',
    closeOnConfirm:true
  },
  function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'close_check/'+id,
        type:'POST',
        cache:false,
        success:function(rs) {
          load_out();

          if(rs == 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              viewDetail(id);
            }, 1200);
          }
          else {
            swal({
              title:'Error!',
              text:rs,
              type:'error'
            });
          }
        }
      })
    }, 200);
  })
}


function reOpenCheck() {
  let id = $('#check_id').val();

  swal({
    title:'คำเตือน !',
    text:'<center>ยอดตั้งต้นจะถูกรีเซ็ตให้เป็น 0 </center><center>ต้องการย้อนสถานะการตรวจนับหรือม่ ?</center>',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonColor:'#DD6B55',
    confirmButtonText:'ย้อนสถานะ',
    cancelButtonText:'ยกเลิก',
    closeOnConfirm:true
  },
  function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 're_open_check/'+id,
        type:'POST',
        cache:false,
        success:function(rs) {
          load_out();

          if(rs == 'success') {
            swal({
              title:'Success',
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
              type:'error'
            });
          }
        }
      })
    }, 200);
  })
}


function cancelCheck(id, code) {
  swal({
    title:'คำเตือน !',
    text:'<center>ยอดตั้งต้นจะถูกรีเซ็ตให้เป็น 0 </center><center>ต้องการยกเลิกการตรวจนับหรือม่ ?</center>',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonColor:'#DD6B55',
    confirmButtonText:'ใช่',
    cancelButtonText:'ไม่ใช่',
    closeOnConfirm:true
  },
  function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'cancel_check/'+id,
        type:'POST',
        cache:false,
        success:function(rs) {
          load_out();

          if(rs == 'success') {
            swal({
              title:'Success',
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
              type:'error'
            });
          }
        }
      })
    }, 200);
  })
}


function getStockZone() {
  let id = $('#check_id').val();

  swal({
    title:'คำเตือน !',
    text:'<center>ยอดตั้งต้นจะถูกรีเซ็ตและแทนที่ด้วยยอดใหม่</center><center>ต้องการดำเนินการหรือไม่ ?</center>',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonColor:'#4caf50',
    confirmButtonText:'ดำเนินการ',
    cancelButtonText:'ยกเลิก',
    closeOnConfirm:true
  },
  function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'get_stock_zone',
        type:'GET',
        cache:false,
        data: {
          'check_id' : id
        },
        success:function(rs) {
          load_out();

          if(rs === 'success') {
            swal({
              title:'Success',
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
              type:'error'
            });
          }
        }
      })
    }, 200);
  });
}


function exportResult() {
  let token = uniqueId();
  $('#token').val(token);

  get_download(token);

  $('#exportForm').submit();
}
