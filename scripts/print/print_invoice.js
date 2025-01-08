function print_invoice(code) {
	//--- properties for print
	var center    = ($(document).width())/2;
	var prop 			= "height=900. left="+center+", scrollbars=yes";

	if(code === undefined) {
		code = $('#code').val();
	}

	var target  = BASE_URL + 'orders/order_invoice/print_invoice/'+code;
  window.open(target, '_blank', prop);
}


function print_do_invoice(code) {
	//--- properties for print
	var center    = ($(document).width())/2;
	var prop 			= "height=900. left="+center+", scrollbars=yes";
	if(code === undefined) {
		code = $('#code').val();
	}
	var target  = BASE_URL + 'orders/order_invoice/print_do_invoice/'+code;
  window.open(target, '_blank', prop);
}

function print_do_receipt(code) {
	//--- properties for print
	var center    = ($(document).width())/2;
	var prop 			= "height=900. left="+center+", scrollbars=yes";
	if(code === undefined) {
		code = $('#code').val();
	}
	var target  = BASE_URL + 'orders/order_invoice/print_do_receipt/'+code;
  window.open(target, '_blank', prop);
}

function print_tax_receipt(code) {
	//--- properties for print
	var center    = ($(document).width())/2;
	var prop 			= "height=900. left="+center+", scrollbars=yes";
	if(code === undefined) {
		code = $('#code').val();
	}
	var target  = BASE_URL + 'orders/order_invoice/print_tax_receipt/'+code;
  window.open(target, '_blank', prop);
}


function print_tax_invoice(code) {
	//--- properties for print
	var center    = ($(document).width())/2;
	var prop 			= "height=900. left="+center+", scrollbars=yes";
	if(code === undefined) {
		code = $('#code').val();
	}
	var target  = BASE_URL + 'orders/order_invoice/print_tax_invoice/'+code;
  window.open(target, '_blank', prop);
}
