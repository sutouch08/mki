var HOME = BASE_URL + 'inventory/cancle';

function goBack(){
  window.location.href = BASE_URL + 'inventory/cancle';
}

function getSearch(){
  $('#searchForm').submit();
}


$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
})


function clearFilter(){
  $.get(BASE_URL + 'inventory/cancle/clear_filter', function(){
    goBack();
  })
}


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


function move_back(id){
  $.ajax({
    url:BASE_URL + 'inventory/cancle/move_back/'+id,
    type:'POST',
    cache:false,
    success:function(rs){
      if(rs === 'success'){
        $('#row-'+id).remove();
        reIndex();
      }
    }
  })
}


function moveCheckedBack() {
  let ds = [];

  if($('.chk:checked').length)
  {
    $('.chk:checked').each(function() {
      let id = $(this).val();

      ds.push(id);
    });

    if(ds.length) {

      load_in();

      $.ajax({
        url:HOME + '/move_cancle_back_to_stock',
        type:'POST',
        cache:false,
        data:{
          'ids' : ds
        },
        success:function(rs) {
          load_out();

          if(rs === 'success') {
            setTimeout(() => {
              window.location.reload();
            }, 200);
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
    }
  }
}


function checkAll(el) {
  if(el.is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
}
