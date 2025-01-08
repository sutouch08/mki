var HOME = BASE_URL + 'report/inventory/stock_movement/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
  let padding = 8 + 24;
  let nav = 45;
	let pageContentHeight = height - (nav + padding);
	let footerHeight = $('.footer-content').height();
  let headerHeight = $('#header-row').height();
  let filterHeight = $('#filter-row').height();
	let titleRowHeight = $('#title-row').height();
  let hrHeight = 20 + 20;

	tableHeight = pageContentHeight - (footerHeight + headerHeight + filterHeight + titleRowHeight + hrHeight);
  pageContentHeight = pageContentHeight - footerHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#report-row').css('height', tableHeight + 'px');
}

function toggleAllProduct(option) {
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').val('');
    $('#pdTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary');
    $('#btn-pd-range').addClass('btn-primary');
    $('#pdFrom').removeAttr('disabled');
    $('#pdTo').removeAttr('disabled');
    $('#pdFrom').focus();
  }
}


$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');

    if(arr.length > 1)
    {
      pdFrom = arr[0];
      $(this).val(pdFrom);

      let pdTo = $('#pdTo').val();

      if(pdTo.length > 0 && pdFrom.length > 0)
      {
        if(pdFrom > pdTo)
        {
          $('#pdFrom').val(pdTo);
          $('#pdTo').val(pdFrom);
        }
      }
    }
    else
    {
      $(this).val('');
    }
  }
});


$('#pdTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');

    if(arr.length > 1)
    {
      pdTo = arr[0];
      $(this).val(pdTo);

      let pdFrom = $('#pdFrom').val();

      if(pdTo.length > 0 && pdFrom.length > 0)
      {
        if(pdFrom > pdTo)
        {
          $('#pdFrom').val(pdTo);
          $('#pdTo').val(pdFrom);
        }
      }
    }
    else
    {
      $(this).val('');
    }
  }
})


function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#wh-modal').modal('show');
  }
}


function toggleGroupWarehouse(option)
{
  $('#groupWarehouse').val(option);

  if(option == 1)
  {
    $('#btn-group-all').addClass('btn-primary');
    $('#btn-group-range').removeClass('btn-primary');
    return
  }

  if(option == 0)
  {
    $('#btn-group-all').removeClass('btn-primary');
    $('#btn-group-range').addClass('btn-primary');
  }
}


$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


function getReport()
{
  $('.h').clearError();

  let error = 0;

  let filter = {
    'allProduct' : $('#allProduct').val(),
    'allWarehouse' : $('#allWarehouse').val(),
    'groupWarehouse' : $('#groupWarehouse').val(),
    'date' : $('#date').val(),
    'pdFrom' : $('#pdFrom').val().trim(),
    'pdTo' : $('#pdTo').val().trim(),
    'warehouse' : []
  };

  if(filter.allProduct == 0)
  {
    if(filter.pdFrom.length == 0)
    {
      $('#pdFrom').hasError();
      error++;
    }

    if(filter.pdTo.length == 0)
    {
      $('#pdTo').hasError();
      error++;
    }

    if(error > 0)
    {
      return false;
    }
  }

  if(filter.allWarehouse == 0)
  {
    if($('.chk:checked').length == 0)
    {
      $('#wh-modal').modal('show');
      return false;
    }
    else
    {
      $('.chk:checked').each(function(index, el) {
        filter.warehouse.push($(this).val());
      });
    }
  }

  if( ! isDate(filter.date))
  {
    $('#date').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:'false',
    data: {
      'filter' : JSON.stringify(filter)
    },
    success:function(rs){
      load_out();

      if(isJson(rs))
      {
        let ds = JSON.parse(rs);

        if(ds.status == 'success')
        {
          $('#date-title').text(ds.date_title);
          $('#item-title').text(ds.item_title);
          $('#wh-title').text(ds.wh_title);
          $('#group-title').text(ds.group_title);
          let source = $('#template').html();
          let output = $('#report-table');

          render(source, ds, output);
        }
        else
        {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error',
            html:true
          })
        }
      }
      else
      {
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
  });

}


function doExport(){
  $('.h').clearError();

  let error = 0;

  let filter = {
    'allProduct' : $('#allProduct').val(),
    'allWarehouse' : $('#allWarehouse').val(),
    'groupWarehouse' : $('#groupWarehouse').val(),
    'date' : $('#date').val(),
    'pdFrom' : $('#pdFrom').val().trim(),
    'pdTo' : $('#pdTo').val().trim(),
    'warehouse' : []
  };

  if(filter.allProduct == 0)
  {
    if(filter.pdFrom.length == 0)
    {
      $('#pdFrom').hasError();
      error++;
    }

    if(filter.pdTo.length == 0)
    {
      $('#pdTo').hasError();
      error++;
    }

    if(error > 0)
    {
      return false;
    }
  }

  if(filter.allWarehouse == 0)
  {
    if($('.chk:checked').length == 0)
    {
      $('#wh-modal').modal('show');
      return false;
    }
    else
    {
      $('.chk:checked').each(function(index, el) {
        filter.warehouse.push($(this).val());
      });
    }
  }

  if( ! isDate(filter.date))
  {
    $('#date').hasError();
    return false;
  }

  var token = generateUID();
  $('#token').val(token);
  $('#filter').val(JSON.stringify(filter));
  get_download(token);

  $('#reportForm').submit();
}
