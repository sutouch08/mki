
$('#item-code').keyup(function(e) {
  if(e.keyCode == 13){
    getItemByCode();
  }
});


$('#item-code').focusout(function(e){
  getItemByCode();
});


$('#item-code').autocomplete({
  source: BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  close:function() {
    let rs = $(this).val().trim();
    let arr = rs.split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);
      $('#item-name').val(arr[1]);

      getItemByCode();
    }
    else {
      $(this).val('');
    }
  }
});


function getItemByCode() {
  setTimeout(() => {
    let code = $.trim($('#item-code').val());
    let zone_code = $('#zone_code').val();

    if(code.length > 0)
    {
      $.ajax({
        url: HOME + 'get_item_by_code',
        type:'GET',
        cache:'false',
        data:{
          'code' : code,
          'zone_code' : zone_code
        },
        success:function(rs) {
          var rs = $.trim(rs);

          if( isJson(rs) ) {
            var ds = $.parseJSON(rs);
            $('#product_code').val(ds.pdCode);
            $('#txt-price').val(ds.price);
            $('#txt-disc').val(ds.disc);
            $('#stock-qty').text(ds.stock);
            $('#count_stock').val(ds.count_stock);
            $('#txt-price').focus();
            $('#txt-price').select();
          }
          else {
            swal('Error', rs, 'error');
            $('#item-code').val('');
            $('#product_code').val('');
            $('#item-name').val('');
            $('#txt-price').val('');
            $('#txt-disc').val('');
            $('#stock-qty').text(0);
            $('#count_stock').val(1);
            $('#item-code').focus();
          }
        }
      });
    }

  }, 200);
}


$('#txt-price').keydown(function(e) {

    //--- skip to qty if space bar key press
    if(e.keyCode == 32){
      e.preventDefault();
      $('#txt-qty').focus();
    }
});


$('#txt-price').keyup(function(e){
  if(e.keyCode == 13 && $(this).val() != ''){
    $('#txt-disc').focus();
    $('#txt-disc').select();
  }

  calAmount();
});


$('#txt-price').focusout(function(event) {
  var amount = parseFloat($(this).val());
  if(amount <= 0){
    $('#txt-disc').val(0);
  }

  if(amount < 0 ){
    $(this).val(0);
  }
});


$('#txt-disc').keyup(function(e){

  if(e.keyCode == 13){
    $('#txt-qty').focus();
    $('#txt-qty').select();
  }

  calAmount();
});


$('#txt-qty').keyup(function(e){
  if(e.keyCode == 13){
    var qty = parseInt($(this).val());
    if(qty > 0){
      addToDetail();
      return;
    }
  }

  calAmount();

});


function calAmount(){
  qty = parseDefault(parseInt($('#txt-qty').val()),0);
  price = parseDefault(parseFloat($('#txt-price').val()), 0);
  disc = parseDiscount($('#txt-disc').val(), price);
  discount = disc.discountAmount * qty;
  amount = (price * qty) - discount;
  $('#txt-amount').text(addCommas(amount.toFixed(2)));
}


function addToDetail(){
  var code = $('#consign_code').val();
  var qty = parseInt($('#txt-qty').val());
  var stock = parseInt($('#stock-qty').text());
  var product_code = $('#product_code').val();
  var price = $('#txt-price').val();
  var disc = $('#txt-disc').val();
  var auz = $('#auz').val();
  var count_stock = $('#count_stock').val();

  if(qty <= 0){
    swal('จำนวนไม่ถูกต้อง');
    return false;
  }

  if(qty > stock && auz == 0 && count_stock == 1){
    swal('ยอดในโซนไม่พอตัด');
    return false;
  }

  if(product_code == ''){
    swal('สินค้าไม่ถูกต้อง');
    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'add_detail/' + code,
    type:'POST',
    cache:false,
    data:{
      'product_code' : product_code,
      'qty' : qty,
      'price' : price,
      'disc' : disc
    },
    success:function(rs){
      load_out();

      if(isJson(rs)) {
        let data = JSON.parse(rs);
        let id = data.id;

        if($('#row-'+id).length == 1)
        {
          $('#qty-'+id).val(data.qty);
        }
        else
        {
          var source = $('#new-row-template').html();
          var output = $('#detail-table');
          render_prepend(source, data, output);
        }
        reIndex();
        reCalAll();
        clearFields();
      }
      else {
        swal('Error!', rs, 'error');
      }
    }
  })
}


function clearFields(){
  $('#item-code').val('');
  $('#item-name').val('');
  $('#txt-price').val('');
  $('#txt-disc').val('');
  $('#stock-qty').text(0);
  $('#txt-qty').val('');
  $('#txt-amount').text('');
  $('#product_code').val('');
  $('#item-code').focus();
}


function reCal(id){
  let price = parseDefault(parseFloat($('#input-price-'+id).val()), 0);
  let disc = parseDiscount($('#input-disc-'+id).val(), price);
  let qty  = parseDefault(parseFloat($('#qty-'+id).val()),1);
  let amount = qty * (price - disc.discountAmount);
  $('#amount-'+id).val(addCommas(amount.toFixed(2)));

  updateTotalQty();
  updateTotalAmount();
}


function reCalAll(){
  $('.input-qty').each(function(index, el) {
    let id = $(this).data('id');
    reCal(id);
  });

  updateTotalQty();
  updateTotalAmount();
}


function checkAll() {
  if($('#chk-all').is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
}


function updateTotalAmount(){
  let total = 0;
  $('.line-amount').each(function(index, el) {
    let amount = parseDefault(parseFloat(removeCommas($(this).val())), 0);
    total += amount;
  });

  $('#total-amount').text(addCommas(total.toFixed(2)));
}


function updateTotalQty(){
  var total = 0;
  $('.input-qty').each(function(index, el) {
    var qty = parseDefault(parseInt($(this).val()), 0);
    total += qty;
  });

  $('#total-qty').text(addCommas(total));
}


function nextFocus(el, className){
  var cl = $('.'+className);
  var idx = cl.index(el);
  cl.eq(idx+1).focus();
}


$('.input-price').keyup(function(e){
  var ids = $(this).attr('id').split('-');
  var id = ids[2];
  var price = parseDefault(parseFloat($(this).val()), 0);
  if(price < 0){
    swal('ราคาน้อยกว่า 0');
    $(this).val(0);
  }

  reCal(id);

  if(e.keyCode == 13){
    nextFocus($(this), 'input-price');
  }
});


$('.input-disc').keyup(function(e){
  var id = $(this).data('id');
  var price = parseDefault(parseFloat($('#input-price-'+id).val()), 0);
  var disc = parseDiscount($(this).val(), price);

  if(disc.discountAmount > price){
    swal('ส่วนลดเกินราคา');
    $(this).val('');
  }

  if(disc.discountAmount < 0 ){
    swal('ส่วนลดน้อยกว่า 0');
    $(this).val('');
  }

  reCal(id);

  if(e.keyCode == 13){
    nextFocus($(this), 'input-disc');
  }
});


function updateRow(id) {
  let code = $('#consign_code').val();
  let zone_code = $('#zone_code').val();

  let prevPrice = $('#input-price-'+id).data('price');
  let prevQty = $('#qty-'+id).data('qty');
  let prevDisc = $('#input-disc-'+id).data('disc');

  let price = parseDefault(parseFloat($('#input-price-'+id).val()), 0);
  let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
  let disc = $('#input-disc-'+id).val().trim();

  $.ajax({
    url:HOME + 'update_row',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'zone_code' : zone_code,
      'price' : price,
      'qty' : qty,
      'prevQty' : prevQty,
      'disc' : disc
    },
    success:function(rs) {
      if( rs.trim() == 'success') {
        reCal(id);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        }, function() {
          $('#input-price-'+id).val(prevPrice);
          $('#qty-'+id).val(prevQty);
          $('#input-disc-'+id).val(prevDisc);

          reCal(id);
        });
      }
    },
    error:function(rs) {
      swal({
        title:'Error!',
        text:rs,
        type:'error',
        html:true
      }, function() {
        $('#input-price-'+id).val(prevPrice);
        $('#qty-'+id).val(prevQty);
        $('#input-disc-'+id).val(prevDisc);

        reCal(id);
      });
    }
  })
}
