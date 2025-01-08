var HOME = BASE_URL + 'report/inventory/delivery_slip/';

function goBack() {
	window.location.href = HOME;
}


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


function checkAll() {
	var checked = $('#chk-all').is(':checked');

	$('.chk').each(function() {
		this.checked = checked;
	});
}


function getReport() {
	var mapForm = document.createElement("form");
	mapForm.target = "_blank";
	mapForm.method = "POST";
	mapForm.action = HOME + "get_report";

	// Create an input
	$('.chk').each(function() {
		if(this.checked === true) {
			var mapInput = document.createElement("input");
			mapInput.type = "hidden";
			mapInput.name = "code[]";
			mapInput.value = this.value;
			// Add the input to the form
			mapForm.appendChild(mapInput);
		}
	});

	// Add the form to dom
	document.body.appendChild(mapForm);

	// // Just submit
	mapForm.submit();
}



function doExport() {
	var token = Math.floor(Math.random() * Date.now());
	var mapForm = document.createElement("form");
	//mapForm.target = "_blank";
	mapForm.method = "POST";
	mapForm.action = HOME + "do_export";

	// Create an input
	$('.chk').each(function() {
		if(this.checked === true) {
			var mapInput = document.createElement("input");
			mapInput.type = "hidden";
			mapInput.name = "code[]";
			mapInput.value = this.value;
			// Add the input to the form
			mapForm.appendChild(mapInput);
		}
	});

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

// function doExport() {
// 	$('#reportForm').submit();
// }


function exportKerryTemplate() {
	var token = Math.floor(Math.random() * Date.now());
	var mapForm = document.createElement("form");
	//mapForm.target = "_blank";
	mapForm.method = "POST";
	mapForm.action = HOME + "exportKerryTemplate";

	// Create an input
	$('.chk').each(function() {
		if(this.checked === true) {
			var mapInput = document.createElement("input");
			mapInput.type = "hidden";
			mapInput.name = "code[]";
			mapInput.value = this.value;
			// Add the input to the form
			mapForm.appendChild(mapInput);
		}
	});

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
