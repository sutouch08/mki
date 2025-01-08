var HOME = BASE_URL + 'inventory/buffer/';

function goBack(){
  window.location.href = BASE_URL + 'inventory/buffer';
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
  $.get(BASE_URL + 'inventory/buffer/clear_filter', function(){
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


function checkAll() {
  let el = $('#chk-all');

  if(el.is(':checked')) {
    $('.row-chk').prop('checked', true);
  }
  else {
    $('.row-chk').prop('checked', false);
  }
}


function removeChecked() {
  let buffer = [];

  if($('.row-chk:checked').length) {
    $('.row-chk:checked').each(function() {
      buffer.push($(this).val());
    });

    if(buffer.length > 0) {
      swal({
        title:'คุณแน่ใจ ?',
        text:'ต้องการลบ ' + buffer.length +' รายการหรือไม่ ?',
        type:'warning',
        showCancelButton:true,
        confirmButtonText:'Yes',
        cancelButtonText:'No',
        confirmButtonColor:'red',
        closeOnConfirm:true
      }, function() {
        setTimeout(() => {
          load_in();

          $.ajax({
            url:HOME + 'remove_select_buffer',
            type:'POST',
            cache:false,
            data:{
              'buffer' : buffer
            },
            success:function(rs) {
              load_out();

              if(rs.trim() === 'success') {

                swal({
                  title:'Success',
                  type:'success',
                  timer:1000
                });

                buffer.forEach((id) => {
                  $('#row-'+id).remove();
                });

                reIndex();
              }
              else {
                swal({
                  title:'Error!',
                  text:rs,
                  type:'error',
                  html:true
                });
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
        }, 200);
      })
    }
  }
}
