var HOME = BASE_URL + 'masters/shipping_round/';

function goBack(){
  window.location.href = HOME;
}


function addNew(){
  window.location.href = HOME + 'add_new';
}

function goEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function saveAdd() {
  clearErrorByClass('e');
	let name = $('#name').val().trim();
	let active = $('#active').is(':checked') ? 1 : 0;

	if(name.length == 0) {
		$('#name').hasError();
		return false;
	}

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'name' : name,
			'active' : active
		},
		success:function(rs) {
			if(rs.trim() === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					addNew();
				}, 1200);
			}
			else {
				showError(rs);
			}
		}
	});
}


function update() {
  clearErrorByClass('e');
	let id = $('#id').val();
	let name = $('#name').val().trim();
	let active = $('#active').is(':checked') ? 1 : 0;

  if(name.length == 0) {
    $('#name').hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'name' : name,
      'active' : active
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}



function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + 'delete/'+id,
      type:'POST',
      cache:false,
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+name+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1500);
        }
        else{
          showError(rs);
        }
      }
    });
  })
}


function getSearch(){
  $('#searchForm').submit();
}


$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}
