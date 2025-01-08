
//--- properties for print
var prop 			= "width=800, height=900. left="+center+", scrollbars=yes";
var center    = ($(document).width() - 800)/2;


//--- พิมพ์ packing list แบบไม่มีบาร์โค้ด
function printQuantation(){
	var code = $('#code').val();
  var target  = BASE_URL + 'orders/quotation/print_quotation/'+code;
  window.open(target, '_blank', prop);
}
