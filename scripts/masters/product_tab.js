var HOME = BASE_URL + 'masters/product_tab/';

function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}



function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}

function saveAdd(){
  addForm.submit();
}


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
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
      url: HOME + 'delete/' + id,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบรายการเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
						window.location.reload();
					}, 1200)
					
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



function getSearch(){
  $('#searchForm').submit();
}


function save() {
	var name = $('#tab_name').val();
	if(name.length == 0) {
		$('#tab_name').addClass('has-error');
		return false;
	}
	else {
		$('#tab_name').removeClass('has-error');
	}

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'name' : name
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
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
		},
		error:function(xhr, status, error) {
			swal({
				title:'Error!',
				text:'Error-'+xhr.status+': '+xhr.statusText,
				type:'error'
			});
		}
	})
}



function update() {
	var name = $('#tab_name').val();
	var id = $('#id').val();

	if(name.length == 0) {
		$('#tab_name').addClass('has-error');
		return false;
	}
	else {
		$('#tab_name').removeClass('has-error');
	}

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'id' : id,
			'name' : name
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					getEdit(id);
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		},
		error:function(xhr, status, error) {
			swal({
				title:'Error!',
				text:'Error-'+xhr.status+': '+xhr.statusText,
				type:'error'
			});
		}
	})
}
