var HOME = BASE_URL + 'masters/warehouse';

function goBack(){
  window.location.href = HOME;
}

function getSearch(){
  $('#searchForm').submit();
}


function clearFilter(){
  $.get(HOME +'/clear_filter', function(){
    goBack();
  });
}

function addNew(){
  window.location.href = HOME + '/add_new';
}

function getEdit(code){
  window.location.href = HOME + '/edit/'+code;
}



function getDelete(code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + '/delete/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบคลัง '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+code).remove();
          reIndex();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}


function toggleSell(option)
{
  $('#sell').val(option);
  if(option == 1){
    $('#btn-sell-yes').addClass('btn-success');
    $('#btn-sell-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-sell-yes').removeClass('btn-success');
    $('#btn-sell-no').addClass('btn-danger');
  }
}


function togglePrepare(option)
{
  $('#prepare').val(option);
  if(option == 1){
    $('#btn-prepare-yes').addClass('btn-success');
    $('#btn-prepare-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-prepare-yes').removeClass('btn-success');
    $('#btn-prepare-no').addClass('btn-danger');
  }
}


function toggleAuz(option)
{
  $('#auz').val(option);
  if(option == 1){
    $('#btn-auz-yes').addClass('btn-success');
    $('#btn-auz-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-auz-yes').removeClass('btn-success');
    $('#btn-auz-no').addClass('btn-danger');
  }
}


function toggleActive(option)
{
  $('#active').val(option);
  if(option == 1){
    $('#btn-active-yes').addClass('btn-success');
    $('#btn-active-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-active-yes').removeClass('btn-success');
    $('#btn-active-no').addClass('btn-danger');
  }
}


function add() {
  clearErrorByClass('e');

  let h = {
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'role' : $('#role').val(),
    'sell' : $('#sell').val(),
    'prepare' : $('#prepare').val(),
    'auz' : $('#auz').val(),
    'active' : $('#active').val()
  };

  if(h.code.length == 0) {
    $('#code').hasError();
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError();
    return false;
  }

  if(h.role == "" || h.role < 1) {
    $('#role').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + '/add',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      load_out();
      showError(rs);
    }
  })
}


function update() {
  clearErrorByClass('e');

  let h = {
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'role' : $('#role').val(),
    'sell' : $('#sell').val(),
    'prepare' : $('#prepare').val(),
    'auz' : $('#auz').val(),
    'active' : $('#active').val()
  };

  if(h.name.length == 0) {
    $('#name').hasError();
    return false;
  }

  if(h.role == "" || h.role < 1) {
    $('#role').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + '/update',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      load_out();
      showError(rs);
    }
  })
}
