<!doctype html>
<title>Site Maintenance</title>
<head>
	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
	<script> var BASE_URL = "<?php echo base_url(); ?>"; </script>
</head>
<style>
  body { text-align: center; padding: 150px; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>

<article>
    <h1>We are sorry</h1>
    <div>
        <p><?php echo $text; ?></p>
        <p>&mdash; The Team</p>
    </div>
</article>
<script>
	$(document).ready(function() {
		//---	reload ทุก 5 นาที
		setTimeout(function(){ window.location.reload(); }, 300000);
	});

</script>
</html>
