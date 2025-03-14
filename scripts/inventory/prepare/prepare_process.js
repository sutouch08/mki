function viewBuffer() {
  $('#buffer-form').submit();
}

//--- จัดสินค้า ตัดยอดออกจากโซน เพิ่มเข้า buffer
function doPrepare(){
  var order_code = $("#order_code").val();
  var zone_code = $("#zone_code").val();
  var barcode = $("#barcode-item").val();
  var qty   = $("#qty").val();

  if( zone_code == ""){
    beep();
    swal("Error!", "ไม่พบรหัสโซน กรุณาเปลี่ยนโซนแล้วลองใหม่อีกครั้ง", "error");
    return false;
  }

  if( barcode.length == 0){
    beep();
    swal("Error!", "บาร์โค้ดสินค้าไม่ถูกต้อง", "error");
    return false;
  }

  if( isNaN(parseInt(qty))){
    beep();
    swal("Error!", "จำนวนไม่ถูกต้อง", "error");
    return false;
  }

  $.ajax({
    url: BASE_URL + 'inventory/prepare/do_prepare',
    type:"POST",
    cache:"false",
    data:{
        "order_code" : order_code,
        "zone_code" : zone_code,
        "barcode" : barcode,
        "qty" : qty
    },
    success: function(rs){
        var rs = $.trim(rs);
        if( isJson(rs)){
          var rs = $.parseJSON(rs);
          var order_qty = parseInt( removeCommas( $("#order-qty-" + rs.id).text() ) );
          var prepared = parseInt( removeCommas( $("#prepared-qty-" + rs.id).text() ) );
          var balance = parseInt( removeCommas( $("#balance-qty-" + rs.id).text() ) );
          var prepare_qty = parseInt(rs.qty);

          prepared = prepared + prepare_qty;
          balance = order_qty - prepared;

          $("#prepared-qty-" + rs.id).text(addCommas(prepared));
          $("#balance-qty-" + rs.id).text(addCommas(balance));

          $("#qty").val(1);
          $("#barcode-item").val('');


          if( rs.valid == '1') {
            $("#complete-table").append($("#incomplete-" + rs.id));
            $("#incomplete-" + rs.id).removeClass('incomplete');
          }

          if( $(".incomplete").length == 0){
            $("#force-bar").addClass('hide');
            $("#close-bar").removeClass('hide');
          }

        }else{
          beep();
          swal("Error!", rs, "error");
          $("#qty").val(1);
          $("#barcode-item").val('');
        }
    }
  });
}


//---- จัดเสร็จแล้ว
function finishPrepare(){
  var order_code = $("#order_code").val();
  $.ajax({
    url: BASE_URL + 'inventory/prepare/finish_prepare',
    type:"POST",
    cache:"false",
    data: {
      "order_code" : order_code
    },
    success: function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({title: "Success", type:"success", timer: 1000});
        setTimeout(function(){ goBack();}, 1200);
      }else{
        beep();
        swal("Error!", rs, "error");
      }
    }
  });
}


function forceClose(){
  swal({
    title: "Are you sure ?",
    text: "ต้องการบังคับจบออเดอร์นี้หรือไม่ ?",
    type: "warning",
    showCancelButton:true,
    confirmButtonColor:"#FA5858",
    confirmButtonText: "ใช่ ฉันต้องการ",
    cancelButtonText: "ยกเลิก",
    closeOnConfirm:false
  }, function(){
    finishPrepare();
  });

}


$('.b-click').click(function(){
  if(!$('#barcode-item').prop('disabled'))
  {
    var barcode = $.trim($(this).text());
    $('#barcode-item').val(barcode);
    $('#barcode-item').focus();
  }

});


//---- ถ้าใส่จำนวนไม่ถูกต้อง
$("#qty").keyup(function(e){
  if( e.keyCode == 13){
    if(! isNaN($(this).val())){
      $("#barcode-item").focus();
    }else{
      swal("จำนวนไม่ถูกต้อง");
      $(this).val(1);
    }
  }
});


function set_focus() {
  $('#barcode-item').val('').focus();
}


//--- เมื่อยิงบาร์โค้ดสินค้าหรือกดปุ่ม Enter
$("#barcode-item").keyup(function(e){
  if(e.keyCode == 13){
    if( $(this).val() != ""){
      doPrepare();
    }
  }
})


//--- เปิด/ปิด การแสดงที่เก็บ
function toggleForceClose(){
  if( $("#force-close").prop('checked') == true){
    $("#btn-force-close").removeClass('not-show');
  }else{
    $("#btn-force-close").addClass('not-show');
  }
}


//---- กำหนดค่าการแสดงผลที่เก็บสินค้า เมื่อมีการคลิกปุ่มที่เก็บ
$(function () {
  $('.btn-pop').popover({html:true});
});


$("#showZone").change(function(){
  if( $(this).prop('checked')){
    $(".btn-pop").addClass('hide');
    $(".zoneLabel").removeClass('hide');
    setZoneLabel(1);
  }else{
    $(".zoneLabel").addClass('hide');
    $(".btn-pop").removeClass('hide');
    setZoneLabel(0);
  }
});


function setZoneLabel(showZone){
  //---- 1 = show , 0 == not show;
  $.get(BASE_URL + 'inventory/prepare/set_zone_label/'+showZone);
}


function removeBuffer(id) {
  let el = $('#buffer-'+id);
  let order_code = el.data('order');
  let product_code = el.data('item');
  let zone_code = el.data('zonecode');

  swal({
    title:'ลบการจัด',
    text:'ต้องการลบ '+el.data('item')+' : '+el.data('zonename')+' : '+el.data('qty')+' หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    confirmButtonColor:'#dd5a43',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + '/remove_buffer',
        type:'POST',
        cache:false,
        data:{
          'order_code' : order_code,
          'product_code' : product_code,
          'zone_code' : zone_code,
          'buffer_id' : id
        },
        success:function(rs) {
          load_out();
          if(rs.trim() === 'success') {
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
    }, 200);
  })
}


var intv = setInterval(function(){
  var order_code = $('#order_code').val();
  $.ajax({
    url: BASE_URL + 'inventory/prepare/check_state',
    type:'GET',
    cache:'false',
    data:{
      'order_code':order_code
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs != 4){
        window.location.reload();
      }
    }
  })
}, 10000);
