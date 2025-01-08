// JavaScript Document

function expandTab(el){
	var className = "open";
	if (el.classList){
		el.classList.add(className);
	}else if (!hasClass(el, className)){
		el.className += " " + className;
	}
}

function collapseTab(el)
{
	var className = "open";
	if (el.classList){
		el.classList.remove(className);
	}else if (hasClass(el, className)) {
		var reg = new RegExp("(\\s|^)" + className + "(\\s|$)");
		el.className=el.className.replace(reg, " ");
	}
}



//--------------------------------  โหลดรายการสินค้าสำหรับจิ้มสั่งสินค้า  -----------------------------//
function getOrderTabs(id) {
	var output = $("#cat-" + id);
	$(".tab-pane").removeClass("active");
	$(".menu").removeClass("active");

	if (output.html() == "") {
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_product_order_tab',
			type: "POST",
			cache: "false",
			data: {
				"id": id
			},
			success: function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var data = $.parseJSON(rs);
					var source = $('#productTabs-template').html();
					render(source, data, output);
				}
				else {
					output.html("<center><h4>ไม่พบสินค้าในหมวดหมู่ที่เลือก</h4></center>");
					$(".tab-pane").removeClass("active");
					output.addClass("active");
				}
			}
		});
	}

	output.addClass("active");
}



//--------------------------------  โหลดรายการสินค้าสำหรับจิ้มสั่งสินค้า  -----------------------------//
function getItemTabs(id) {
	var output = $("#cat-" + id);
	$(".tab-pane").removeClass("active");
	$(".menu").removeClass("active");

	if (output.html() == "") {
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_order_item_tab',
			type: "POST",
			cache: "false",
			data: {
				"id": id
			},
			success: function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var data = $.parseJSON(rs);
					var source = $('#itemTabs-template').html();
					render(source, data, output);
				}
				else {
					output.html("<center><h4>ไม่พบสินค้าในหมวดหมู่ที่เลือก</h4></center>");
					$(".tab-pane").removeClass("active");
					output.addClass("active");
				}
			}
		});
	}

	output.addClass("active");
}
