

function getSearch(){
  $("#searchForm").submit();
}



function clearFilter(){
  $.get(HOME + '/clear_filter', function(){ goBack(); });
}


function clearProcessFilter() {
  $.get(HOME + '/clear_filter', function(){ goProcess(); });
}


$(".search").keyup(function(e){
  if( e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd){
    $("#toDate").datepicker("option", "minDate", sd);
  }
});


$("#toDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd){
    $("#fromDate").datepicker("option", "maxDate", sd);
  }
});

$("#shipFromDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd){
    $("#shipToDate").datepicker("option", "minDate", sd);
  }
});


$("#shipToDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd){
    $("#shipFromDate").datepicker("option", "maxDate", sd);
  }
});


function checkAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}



function getPickList() {
	var token = Math.floor(Math.random() * Date.now());
	var mapForm = document.createElement("form");
	var count = 0;
	mapForm.method = "POST";
	mapForm.action = BASE_URL + "report/inventory/delivery_slip/do_export";

	// Create an input
	$('.chk').each(function() {
		if(this.checked === true) {
			var mapInput = document.createElement("input");
			mapInput.type = "hidden";
			mapInput.name = "code[]";
			mapInput.value = this.value;
			// Add the input to the form
			mapForm.appendChild(mapInput);
			count++;
		}
	});

	if(count == 0) {
		swal("กรุณาเลือกอย่างน้อย 1 เอกสาร");
		return false;
	}

	var mapInput = document.createElement("input");
	mapInput.type = "hidden";
	mapInput.name = "token";
	mapInput.value = token;
	mapForm.appendChild(mapInput);

	// Add the form to dom
	document.body.appendChild(mapForm);

	// // Just submit
	get_download(token);
	mapForm.submit();
}



function soldOrder() {
	let ds = [];

	$('.chk').each(function() {
		if($(this).is(':checked') === true) {
			let code = $(this).val();
			ds.push(code);
		}
	});

	if(ds.length > 0) {

		load_in();

		$.ajax({
			url:BASE_URL + 'inventory/delivery/sold_order',
			type:'POST',
			cache:false,
			data:{
				"orders" : ds
			},
			success:function(rs) {
				load_out();
				if(rs === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						window.location.reload();
					}, 1200);
				}
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          })
        }
			}
		});
	}
}


//---- Reload page every 5 minute
$(document).ready(function(){
  setInterval(function(){ goBack();}, 300000);
});
