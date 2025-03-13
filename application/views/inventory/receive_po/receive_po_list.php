<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
  	<p class="pull-right top-p">
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
    <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>ใบสั่งผลิต</label>
    <input type="text" class="form-control input-sm search" name="po" value="<?php echo $po; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm search" name="invoice" value="<?php echo $invoice; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>ผู้ผลิต</label>
		<input type="text" class="form-control input-sm search" name="venderx" value="<?php echo $vender; ?>" />
  </div>

	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <p class="pull-right">
      <?php label('status'); ?> : <?php label('empty'); ?> = <?php label('normal'); ?>, &nbsp;
      <span class="red">CN</span> = <?php label('cancle'); ?>, &nbsp;
      <span class="blue">NC</span> = <?php label('not_save'); ?>
    </p>
  </div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1000px;">
			<thead>
				<tr>
					<th class="fix-width-60 middle text-center"><?php label('Num'); ?></th>
					<th class="fix-width-100 middle text-center"><?php label('date'); ?></th>
					<th class="fix-width-120 middle"><?php label('doc_num'); ?></th>
					<th class="fix-width-120 middle"><?php label('inv'); ?></th>
					<th class="fix-width-120 middle">ใบสั่งผลิต</th>
					<th class="min-width-200 middle"><?php label('vender'); ?></th>
					<th class="fix-width-150 middle">ผู้ดำเนินการ</th>
					<th class="fix-width-100 middle text-center"><?php label('qty'); ?></th>
					<th class="fix-width-80 middle text-center"><?php label('status'); ?></th>
          <th class="fix-width-100"></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($document)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($document as $rs) : ?>
            <tr id="row-<?php echo $rs->code; ?>" style="font-size:12px;">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->invoice_code; ?></td>
              <td class="middle"><?php echo $rs->po_code; ?></td>
              <td class="middle"><?php echo $rs->vender_name; ?></td>
							<td class="middle"><?php echo get_display_name($rs->user); ?></td>
              <td class="middle text-center"><?php echo $rs->qty; ?></td>
              <td class="middle text-center">
                <?php if($rs->status == 0 ) : ?>
                  <span class="blue"><strong>NC</strong></span>
                <?php endif; ?>
                <?php if($rs->status == 2) : ?>
                <span class="red"><strong>CN</strong></span>
                <?php endif; ?>
              </td>
              <td class="middle text-right">
                <button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
                <?php if(($this->pm->can_edit OR $this->pm->can_add) && $rs->status == 0) : ?>
                  <button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
                <?php endif; ?>
                <?php if($this->pm->can_delete && $rs->status != 2) : ?>
                  <button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd');?>"></script>

<?php $this->load->view('include/footer'); ?>
