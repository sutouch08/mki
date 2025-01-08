var isClicked = 0;

$("#fromDate").datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $("#toDate").datepicker('option', 'minDate', sd);
  }
});


$("#toDate").datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $("#fromDate").datepicker('option', 'maxDate', sd);
  }
});



$(".search-box").keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function getSearch(){
  $("#searchForm").submit();
}



function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}


$(document).ready(function() {
	//---	reload ทุก 5 นาที
	setTimeout(function(){ goBack(); }, 300000);
});



function create_each_invoice(option) {
  if(isClicked == 0) {
    isClicked = 1;

    var data = [];

  	$('.chk').each(function() {
  		if($(this).is(':checked')) {
  			var no = $(this).data('no');
  			var order_code = $('#orderCode-'+no).val();
  			data.push(order_code);
  		}
  	});

  	if(data.length) {
  		load_in();
  		$.ajax({
  			url:BASE_URL + 'orders/order_invoice/create_each_order_invoice',
  			type:'POST',
  			cache:false,
  			data:{
  				'data' : data
  			},
  			success:function(rs) {
  				load_out();
  				var rs = $.trim(rs);
  				if(isJson(rs)) {
  					var ds = $.parseJSON(rs);
  					print_select_invoice(ds.gen_id, option);
  					window.location.reload();
  				}
  				else {
  					swal({
  						title:'Error!',
  						text:rs,
  						type:'error'
  					});

            isClicked = 0;
  				}
  			},
  			error:function(xhr, status, error) {
  				load_out();
  				swal({
  					title:'Error!',
  					text:'Error-'+xhr.status+' : '+xhr.statusText,
  					type:'error'
  				});

          isClicked = 0;
  			}
  		})
  	}
    else {
      isClicked = 0;
    }
  }

}



function create_one_invoice(option) {
  if(isClicked == 0) {
    isClicked = 1;
    var data = {};

    $('.chk').each(function() {
      if($(this).is(':checked')) {
        var no = $(this).data('no');
        var order_code = $('#orderCode-'+no).val();
        var customer_code = $('#customerCode-'+no).val();

        if(customer_code in data) {
          data[customer_code].push(order_code);
        }
        else {
          data[customer_code] = [order_code];
        }
      }
    })

    if(Object.keys(data).length) {
      load_in();

      $.ajax({
        url:BASE_URL + 'orders/order_invoice/create_multi_order_invoice',
        type:'POST',
        cache:false,
        data:{
          'data' : data
        },
        success:function(rs) {
          load_out();
          console.log(rs);
          var rs = $.trim(rs);
          if(isJson(rs)) {
            var ds = $.parseJSON(rs);
            var gen_id = ds.gen_id;
            print_select_invoice(gen_id, option);
            window.location.reload();
          }
          else {
            swal({
              title:'Error!',
              text:rs,
              type:'error'
            });

            isClicked = 0;
          }
        },
        error:function(xhr, status, error) {
          load_out();
          swal({
            title:'Error!',
            text:'Error-'+xhr.status+' : '+xhr.statusText,
            type:'error'
          });

          isClicked = 0;
        }
      });
    }
    else {
      isClicked = 0;
    }
  }
}


function print_select_invoice(gen_id, invoice_type) {

	switch (invoice_type) {
		case 'do_invoice':
				print_do_invoice(gen_id);
			break;
		case 'tax_invoice' :
				print_tax_invoice(gen_id);
			break;
		default:
				if(USE_VAT === '1' || USE_VAT === 1) {
					print_tax_invoice(gen_id);
				}
				else {
					print_do_invoice(gen_id);
				}
	}
}



function print_do_invoice(code) {
	//--- properties for print
	var center    = ($(document).width())/2;
	var prop 			= "height=900. left="+center+", scrollbars=yes";
	var target  = BASE_URL +'orders/order_invoice/print_multiple_do_invoice/'+code;
  window.open(target, '_blank', prop);
}



function print_tax_invoice(code) {
	//--- properties for print
	var center    = ($(document).width())/2;
	var prop 			= "height=900. left="+center+", scrollbars=yes";
	var target  = BASE_URL +'orders/order_invoice/print_multiple_tax_invoice/'+code;
  window.open(target, '_blank', prop);
}
