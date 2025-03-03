<?php	
	$adr = parse_address((array) $address);
 ?>
<!DOCTYPE html>
<html>
  <head>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<link rel="icon" href="<?php echo base_url(); ?>assets/images/icons/favicon.ico" type="image/x-icon" />
  	<title><?php echo $this->title; ?></title>
  	<link href="<?php echo base_url(); ?>assets/css/bootstrap.css?v=1" rel="stylesheet" />
		<link href="<?php echo base_url(); ?>assets/css/font-awesome.css" rel="stylesheet"/>
  	<link href="<?php echo base_url(); ?>assets/css/template.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/print.css" rel="stylesheet" />
  	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
		<style>
		@page {
			size: A4 landscape;
		}
		.page_layout{
			border: solid 1px #aaa;
			border-radius:0px;
			width:282mm;
			height:200mm;
			margin:auto;
			display: flex;
			flex-flow: column;
			justify-content: center;
			align-items: center;
		}

		.page-content {
			height: 100%;
			padding-left: 50mm;
			padding-right: 50mm;
			display: flex;
			flex-flow: column;
			justify-content: center;
			align-items: center;
			line-height: 2;
		}

		@media print{
			.page_layout{ border: none; }
		}
		</style>
  	<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
  	</head>
  	<body>
    	<div class="hidden-print" style="margin-top:10px; padding-bottom:10px; padding-right:5mm; width:280mm; margin-left:auto; margin-right:auto; text-align:right">
    	   <button class="btn btn-primary" onclick="print()"><i class="fa fa-print"></i>&nbspพิมพ์</button>
    	</div>
      <div style="width:100%">
        <!-- Page Start -->
    		<div class="page_layout">
          <div class="width-100 text-center page-content">
            <div class="width-100 text-center font-size-48 red"><?php echo (empty($order->sender_name) ? "" : $order->sender_name); ?></div>
						<div class="width-100 text-center font-size-42 margin-top-30"><?php echo $cusName; ?></div>
						<div class="width-100 text-center font-size-42"><?php echo $adr; ?></div>
						<div class="width-100 text-center font-size-42">เบอร์โทร - <?php echo $cusPhone; ?></div>
          </div>
        </div>
      </div>
    </body>
  </html>
