<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">

      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="reference"  value="<?php echo $reference; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm search" name="product_code" value="<?php echo $product_code; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm search" name="zone_code" value="<?php echo $zone_code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>คลัง</label>
    <input type="text" class="form-control input-sm search" name="warehouse_code" value="<?php echo $warehouse_code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
	<div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> เคลียร์</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped table-bordered">
      <tr>
        <th class="width-5 text-center">ลำดับ</th>
        <th class="width-15 text-center">วันที่</th>
        <th class="width-15 text-center">เลขที่เอกสาร</th>
        <th class="width-30 text-center">สินค้า</th>
        <th class="width-10 text-center">เข้า</th>
        <th class="width-10 text-center">ออก</th>
    		<th class="width-15">โซน</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment(4) + 1; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-12">
        <td class="text-center"><?php echo $no; ?></td>
        <td class="text-center"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
        <td><?php echo $rs->reference; ?></td>
        <td><?php echo $rs->product_code; ?> -- <?php echo $rs->product_name; ?></td>
        <td class="text-center"><?php echo number($rs->move_in); ?></td>
    		<td class="text-center"><?php echo number($rs->move_out); ?></td>
        <td> <?php echo $rs->zone_name; ?></td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
			<tr class="font-size-12">
				<td colspan="4" class="text-right">รวม</td>
				<td class="text-center"><?php echo number($total_row->move_in); ?></td>
				<td class="text-center"><?php echo number($total_row->move_out); ?></td>
				<td>
					ยอดต่าง(เข้า - ออก) :
					<?php echo number($total_row->move_in - $total_row->move_out); ?>
				</td>
			</tr>
    <?php else : ?>
      <tr>
        <td colspan="7" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/inventory/movement/movement.js"></script>

<?php $this->load->view('include/footer'); ?>
