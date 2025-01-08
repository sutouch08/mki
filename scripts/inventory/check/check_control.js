var autoFocus = 0;
var heartBeat = setInterval(function() {renewActiveTime()}, 10000); ///--- ส่ง heartbeat ไปบอก server ว่าเรายังทำงานอยู่นะ


window.addEventListener('load', () => {
  focus_init();
});

function renewActiveTime() {
  let id = $('#check_id').val();
  let url = HOME + 'set_active_time/'+id;
  $.get(url);
}

function focus_init() {
	$('.focus').focusout(function() {
		autoFocus = 1
		setTimeout(() => {
			if(autoFocus == 1) {
				barcodeFocus();
			}
		}, 3000)
	})

	$('.focus').focusin(function() {
		autoFocus = 0;
	});
}

function barcodeFocus() {
	$('#barcode').focus();
}

function doChecking() {
  let id = $('#check_id').val();
  let barcode = $('#barcode').val().trim();
  let pdCode = $('#pd-code').val().trim();
  barcode = barcode.length == 0 ? pdCode : barcode;
  let qty = parseDefault(parseInt($('#qty').val()), 1);

  if(qty > 1000) {
    swal({
      title:'Error!',
      text:'ใส่จำนวนได้ไมเกินครั้งละ 1000',
      type:'error'
    });

    beep();

    return false;
  }


  if(barcode.length) {

    inactiveControl();

    $.ajax({
      url:HOME + 'do_checking',
      type:'POST',
      cache:false,
      data:{
        'check_id' : id,
        'barcode' : barcode,
        'qty' : qty
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status == 'success') {

            let source = $('#check-template').html();
            let data = ds.row;
            let output = $('#check-table');

            render_prepend(source, data, output);

            if($('#'+ds.bc_id).length) {
              let checkedQty = parseDefault(parseInt(removeCommas($('#'+ds.bc_id).text())), 1);
              let newQty = checkedQty + qty;
              $('#'+ds.bc_id).text(addCommas(newQty));
              $('#row-'+ds.bc_id).insertAfter($('#head'));

              activeControl();
            }
            else {
              $.ajax({
                url:HOME + 'get_checked_row',
                type:'GET',
                cache:false,
                data:{
                  'check_id' : id,
                  'barcode' : barcode
                },
                success:function(rd) {
                  if(isJson(rd)) {
                    let ds = JSON.parse(rd);

                    if(ds.status == 'success') {
                      let source = $('#checked-template').html();
                      let data = ds.row;
                      let output = $('#head');

                      render_after(source, data, output);
                    }
                    else {
                      beep();
                      swal({
                        title:'Error!',
                        text:ds.message,
                        type:'error'
                      });
                    }
                  }
                  else {
                    beep();
                    swal({
                      title:"Error!",
                      type: 'error',
                      text:rd
                    });
                  }

                  activeControl();
                }
              })
            }
          }
          else {
            if(ds.item_code == "not_found") {
              beep();
              swal({
                title:'ไม่พบรายการสินค้า',
                text:"ต้องการบันทึกรายการตรวจนับนี้หรือไม่ ?",
                type:"warning",
                showCancelButton:true,
                confirmButtonColor:'#cf4f27',
                confirmButtonText:'ดำเนินการต่อ',
                cancelButtonText:'กลับไปแก้ไข',
                closeOnConfirm:true
              },
              function(isConfirm) {
                if(isConfirm) {
                  checkWithNoItem(id, barcode, qty);
                }
                else {
                  activeControl(1);
                }
              });
            }
            else {
              beep();
              swal({
                title:'Error!',
                text:ds.message,
                type:'error'
              });

              activeControl();
            }
          }
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
          beep();
          activeControl();
        }
      }
    })
  }
}


function checkWithNoItem(id, barcode, qty) {
  if(barcode.length) {

    inactiveControl();

    $.ajax({
      url:HOME + 'check_with_no_item',
      type:'POST',
      cache:false,
      data:{
        'check_id' : id,
        'barcode' : barcode,
        'qty' : qty
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status == 'success') {

            let source = $('#check-template').html();
            let data = ds.row;
            let output = $('#check-table');

            render_prepend(source, data, output);

            if($('#'+ds.bc_id).length) {
              let checkedQty = parseDefault(parseInt(removeCommas($('#'+ds.bc_id).text())), 1);
              let newQty = checkedQty + qty;
              $('#'+ds.bc_id).text(addCommas(newQty));
              $('#row-'+ds.bc_id).insertAfter($('#head'));

              activeControl();
            }
            else {
              $.ajax({
                url:HOME + 'get_checked_row',
                type:'GET',
                cache:false,
                data:{
                  'check_id' : id,
                  'barcode' : barcode
                },
                success:function(rd) {
                  if(isJson(rd)) {
                    let ds = JSON.parse(rd);

                    if(ds.status == 'success') {
                      let source = $('#checked-template').html();
                      let data = ds.row;
                      let output = $('#head');

                      render_after(source, data, output);
                    }
                    else {
                      swal({
                        title:'Error!',
                        text:ds.message,
                        type:'error'
                      });
                    }
                  }
                  else {
                    swal({
                      title:"Error!",
                      type: 'error',
                      text:rd
                    });
                  }

                  activeControl();
                }
              })
            }
          }
          else {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            });

            activeControl();
          }
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
          activeControl();
        }
      }
    })
  }
}


function activeControl(ok) {

  let allow_input_qty = $('#allow_input_qty').val();

  if(ok === undefined) {

    if(allow_input_qty == '1') {
      $('#qty').val(1).removeAttr('disabled');
    }

    $('#btn-check').removeAttr('disabled');
    $('#pd-code').val('').removeAttr('disabled');
    $('#barcode').val('').removeAttr('disabled').focus();
  }
  else {
    setTimeout(() => {
      if(allow_input_qty == '1') {
        $('#qty').val(1).removeAttr('disabled');
      }

      $('#btn-check').removeAttr('disabled');
      $('#barcode').removeAttr('disabled').focus();
    }, 100)
  }
}

function inactiveControl() {
  $('#qty').attr('disabled', 'disabled');
  $('#btn-check').attr('disabled', 'disabled');
  $('#pd-code').attr('disabled', 'disabled');
  $('#barcode').attr('disabled', 'disabled');
}


$('#qty').focusin(function() {
  $(this).select();
});


$('#qty').keydown(function(e) {
  if(e.keyCode == 13 || e.keyCode == 32) {
    e.preventDefault()
    $('#barcode').focus()
  }
})

$('#barcode').keydown(function(e) {
  if(e.keyCode === 32) {
    e.preventDefault()
    $('#qty').focus()
  }
})

$('#barcode').keyup(function(e) {
  if(e.keyCode === 13) {
    let barcode = $(this).val().trim();

    if(barcode.length) {
      doChecking()
    }
  }
});

$('#pd-code').keyup(function(e) {
  if(e.keyCode === 13) {
    let pdCode = $(this).val().trim();

    if(pdCode.length) {
      setTimeout(() => {
        doChecking();
      }, 100);
    }
  }
});

$('#view-qty').keyup(function(e) {
  if(e.keyCode === 13) {
    viewHistory()
  }
})

function inputView() {
  $('#view-qty').val(10);
  $('#viewModal').modal('show');
}

$('#viewModal').on('shown.bs.modal', function() {
  $('#view-qty').focus().select();
});

function viewHistory() {
  let id = $('#check_id').val();
  let qty = parseDefault(parseInt($('#view-qty').val()), 0);

  if(qty > 0) {
    $('#viewModal').modal('hide');
    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'get_history',
        type:'GET',
        cache:false,
        data:{
          'check_id' : id,
          'qty' : qty
        },
        success:function(rs) {
          load_out();

          if( isJson(rs)) {
            let ds = JSON.parse(rs);
            let count = parseDefault(parseInt(ds.count), 0);

            if(count > 0) {
              let source = $('#history-template').html();
              let data = ds.rows;
              let output = $('#check-table');

              render(source, data, output);
            }
            else {
              swal({
                title:'ไม่พบประวัติ',
                type:'info'
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
    }, 500);
  }
}


function clearSheet() {
  $('#check-table').html('');
  $('#barcode').val('').removeAttr('disabled').focus();
}

$(document).keydown(function(e) {
  if(e.keyCode == 113) //--- F2
	{
    e.preventDefault()
		clearSheet()
	}
  else if(e.keyCode == 114) //--- F3
  {
    e.preventDefault()
    inputView()
  }
});


function removeCheck() {
  let check_id = $('#check_id').val();
  let rows = [];

  $('.chk:checked').each(function() {
    rows.push($(this).val());
  });

  if(rows.length) {
    swal({
      title : "ลบรายการตรวจนับ",
  		text : "คุณต้องการลบรายการตรวจนับที่เลือกใช่หรือไม่? ",
  		type : "warning",
  	  showCancelButton: true,
  	  confirmButtonColor: "#DD6B55",
  	  confirmButtonText: "ใช่ ลบเลย",
  	  cancelButtonText: "ไม่ใช่",
  	  closeOnConfirm: true
    },
    function() {
      load_in();

      setTimeout(() => {
        $.ajax({
          url:HOME + 'delete_checked_details',
          type:'POST',
          cache:false,
          data:{
            'check_id' : check_id,
            'rows' : JSON.stringify(rows)
          },
          success:function(rs) {
            load_out();

            if(rs == 'success') {
              rows.forEach((id) => {
                let bc_id = $('#chk-'+id).data('bcid');
                let qty = parseDefault(parseInt($('#chk-'+id).data('qty')), 0);
                let chkQty = parseDefault(parseInt(removeCommas($('#'+bc_id).text())), 0);
                let balance = chkQty - qty;

                $('#row-'+id).remove();

                if(balance > 0) {
                  $('#'+bc_id).text(addCommas(balance));
                }
                else {
                  $('#row-'+bc_id).remove();
                }
              });
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
      }, 200);
    });
  }
}

$('#pd-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_product_code_and_name',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');
    $(this).val(arr[0]);
  }
})
