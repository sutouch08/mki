<!--
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5">
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">
				<i class="ace-icon fa fa-times"></i>
			</button>
			<strong>Warning!</strong>
				สัญญาการให้บริการจะสินสุดวันที่ <?php //echo thai_date($this->system_end_date); ?> กรุณาติดต่อตัวแทนผู้ให้บิรการ
			<br>
		</div>
	</div>
</div>
-->
<script>
$(document).ready(function(){
	if(!getCookie('warning_shown')) {
		$.gritter.add({
		    title: 'Warning!',
		    text: 'สัญญาการให้บริการจะสินสุดวันที่ <?php echo thai_date($this->system_end_date); ?> กรุณาติดต่อตัวแทนผู้ให้บิรการ',
		    // image: 'path/to/image',
		    sticky: true,
		    time: 2000,
				position: 'top-left',
		    class_name: 'gritter-error gritter-light gritter-center',
				before_close:function(e, manual_close) {
					//--- set cookie expires midnight
					var name = "warning_shown";
					var value = 1;
					var expires = "";
			    var date = new Date();
			    var midnight = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59);
			    expires = "; expires=" + midnight.toGMTString();

			    document.cookie = name + "=" + value + expires + "; path=/";
				}
		});
	}
})

</script>
