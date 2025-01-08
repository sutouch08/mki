<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-info" onclick="print()"><i class="fa fa-print"></i> พิมพ์</button>
				<button type="button" class="btn btn-sm btn-success" onclick="doExport()"><i class="fa fa-file-excel-o"></i> Export</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<div class="row">
	<div class="col-sm-12">
		<table style="width:100%; margin-bottom:0px;">
			<tr>
				<td class="width-20 middle" style="padding-bottom:5px;">
					<img src="<?php echo base_url().'images/company/company_logo.png'; ?>" height="60px" />
				</td>
				<td class="middle text-center font-size-18" style="padding-bottom:5px;">
					ใบรายงานจัดเตรียมสินค้าเพื่อขนส่ง
				</td>
				<td class="middle width-20 text-right font-size-14" style="padding-bottom:5px;">
					วันที่ <?php echo date('d/m/Y'); ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="row">
  <div class="col-sm-12">
    <table class="table table-bordered table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">No</th>
          <th class="text-center">ชื่อและที่อยู่</th>
          <th class="width-20 text-center">หมายเหตุ</th>
          <th class="width-10 text-center">จำนวนลัง</th>
          <th class="width-10 text-center">ยอดเงิน</th>
          <th class="width-10 text-center">การจัดส่ง</th>
          <th class="width-15 text-center">การชำระเงิน</th>
        </tr>
      </thead>
      <tbody>
<?php $code = ""; ?>
<?php if(!empty($data))  : ?>
<?php $no = 1; ?>

<?php   foreach($data as $rs)  : ?>
		<?php $adr = $rs['adr']; ?>
        <tr class="font-size-12">
          <td class="text-center"><?php echo $no; ?></td>
          <td class="" style="white-space:normal;">
            <?php if(!empty($adr)) : ?>
						<?php echo "ชื่อ : {$adr->name} <br/> ที่อยู่ : {$adr->address} {$adr->sub_district} {$adr->district} {$adr->province} {$adr->postcode} <br/>"; ?>
						<?php echo "โทร. {$adr->phone} <br/>"; ?>
						<?php endif; ?>
						<?php echo "เลขที่บิล : {$rs['code']}"; ?>
          </td>

          <td class="">
            <?php echo $rs['notes']; ?>
          </td>

          <td class="text-center">
            <?php echo $rs['box'].' ลัง'; ?>
          </td>

          <td class="text-center">
            <?php echo number($rs['amount'], 2); ?>
          </td>

          <td class="text-center" >
            <?php echo $rs['sender']; ?>
          </td>

          <td class="text-center hide-text">
            <?php echo $rs['payment']; ?>
          </td>

        </tr>
<?php  $code .= $no === 1 ? $rs['code'] : ",{$rs['code']}"; ?>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<form id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<input type="hidden" id="code" name="code" value="<?php echo $code; ?>">
</form>
<script src="<?php echo base_url(); ?>scripts/report/inventory/delivery_slip.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
