
var chk = setInterval(function () { checkState(); }, 10000);



function checkState(){
  var order_code = $("#order_code").val();
  $.ajax({
    url: HOME + 'get_state',
    type: 'GET',
    data: {
      'order_code' : order_code
    },
    success: function(rs){
      var rs = parseDefault(parseInt($.trim(rs)), 0);
      if( rs > 7){
        $("#btn-confirm-order").remove();
        clearInterval(chk);
      }
    }
  });
}



function confirmOrder(){
  var order_code = $("#order_code").val();
  load_in();
  $.ajax({
    url: HOME + 'confirm_order',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : order_code
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if( rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        },1200);

      }else {
        swal('Error!', rs, 'error');
      }
    }
  });
}


function open_invoice(code) {
	load_in();
	$.ajax({
		url:BASE_URL + 'orders/order_invoice/gen_new_invoice',
		type:'POST',
		cache:false,
		data:{
			"orders" : [code]
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);

				if(ds.failed.count > 0) {
					swal({
						title:'Error!',
						text:ds.failed.order,
						type:'error'
					});
				}

				if(ds.success.count > 0) {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						print_invoice(ds.success.code);
					}, 1200);

					setTimeout(function(){
						window.location.reload();
					}, 1500);
				}

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
}


function print_invoice(code) {
	//--- properties for print
	var center    = ($(document).width())/2;
	var prop 			= "height=900. left="+center+", scrollbars=yes";
	var target  = BASE_URL + 'orders/order_invoice/print_invoice/'+code;
  window.open(target, '_blank', prop);
}

function refresh() {
	window.location.reload();
}
