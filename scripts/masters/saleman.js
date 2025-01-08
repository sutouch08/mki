var HOME = BASE_URL + 'masters/saleman/';

function goBack(){
  window.location.href = HOME;
}



function addNew(){
  window.location.href = HOME + 'add_new';
}

function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function saveAdd() {
	let code = $('#code').val();
	let name = $('#name').val();
	let active = $('#active').is(':checked') ? 1 : 0;

	if(code.length == 0) {
		$('#code').addClass('has-error');
		return false;
	}
	else {
		$('#code').removeClass('has-error');
	}


	if(name.length == 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}


	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'active' : active
		},
		success:function(rs) {
			if(rs === 'success') {
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
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	});
}



function update() {
	let code = $('#code').val();
	let name = $('#name').val();
	let active = $('#active').is(':checked') ? 1 : 0;

	$.ajax({
		url:HOME + 'is_exists_name',
		type:'POST',
		cache:false,
		data:{
			"code" : code,
			"name" : name
		},
		success:function(rs) {
			if(rs == 'ok') {
				$.ajax({
					url:HOME + 'update',
					type:'POST',
					cache:false,
					data:{
						"code" : code,
						"name" : name,
						"active" : active
					},
					success:function(rs) {
						if(rs === 'success') {
							swal({
								title:'Success',
								type:'success',
								timer:1000
							});
						}
						else {
							swal({
								title:'Error!',
								text:rs,
								type:'error'
							});
						}
					}
				});
			}
			else {
				swal({
					title:"Error!",
					text:rs,
					type:'error'
				});
			}
		}
	});
}




function getDelete(code, name){
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
      url: HOME + 'delete/'+code,
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
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
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

function goBack(){
  window.location.href = HOME;
}


function syncData(){
  load_in();
  $.ajax({
    url:HOME + 'syncData',
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs == 'success'){
        swal({
          title:'Completed',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goBack();
        }, 1500);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}
