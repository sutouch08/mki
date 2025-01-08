var HOME = BASE_URL + 'orders/orders/';

function addNew(){
  window.location.href = BASE_URL + 'orders/orders/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'orders/orders';
}



function editDetail(){
  var code = $('#order_code').val();
  window.location.href = BASE_URL + 'orders/orders/edit_detail/'+ code;
}


function editOrder(code){
  window.location.href = BASE_URL + 'orders/orders/edit_order/'+ code;
}


function leave(code) {
	swal({
    title:'คุณแน่ใจ ?',
    text:'รายการทั้งหมดจะไม่ถูกบันทึก ต้องการออกหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'ไม่ใช่',
    confirmButtonText:'ออกจากหน้านี้',
  },
  function(){
    editOrder(code);
  });
}



function clearFilter(){
  var url = BASE_URL + 'orders/orders/clear_filter';
  $.get(url, function(rs){ goBack(); });
}



function getSearch(){
  $('#searchForm').submit();
}

function toggleState(state){
  var current = $('#state_'+state).val();
  if(current == 'Y'){
    $('#state_'+state).val('N');
    $('#btn-state-'+state).removeClass('btn-info');
  }else{
    $('#state_'+state).val('Y');
    $('#btn-state-'+state).addClass('btn-info');
  }

  getSearch();
}


function toggleNotSave(){
  var current = $('#notSave').val();
  if(current == ''){
    $('#notSave').val(1);
    $('#btn-not-save').addClass('btn-info');
  }else{
    $('#notSave').val('');
    $('#btn-not-save').removeClass('btn-info');
  }

  getSearch();
}


function toggleOnlyMe(){
  var current = $('#onlyMe').val();
  if(current == ''){
    $('#onlyMe').val(1);
    $('#btn-only-me').addClass('btn-info');
  }else{
    $('#onlyMe').val('');
    $('#btn-only-me').removeClass('btn-info');
  }

  getSearch();
}


function toggleIsExpire(){
  var current = $('#isExpire').val();
  if(current == ''){
    $('#isExpire').val(1);
    $('#btn-expire').addClass('btn-info');
  }else{
    $('#isExpire').val('');
    $('#btn-expire').removeClass('btn-info');
  }

  getSearch();
}

$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});


function sort(field){
  var sort_by = "";
  if(field === 'date_add'){
    el = $('#sort_date_add');
    sort_by = el.hasClass('sorting_desc') ? 'ASC' : 'DESC';
    sort_class = el.hasClass('sorting_desc') ? 'sorting_asc' : 'sorting_desc';
  }else{
    el = $('#sort_code');
    sort_by = el.hasClass('sorting_desc') ? 'ASC' : 'DESC';
    sort_class = el.hasClass('sorting_desc') ? 'sorting_asc' : 'sorting_desc';
  }

  $('.sorting').removeClass('sorting_desc');
  $('.sorting').removeClass('sorting_asc');

  el.addClass(sort_class);
  $('#sort_by').val(sort_by);
  $('#order_by').val(field);

  getSearch();
}
