<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label><?php label('doc_num'); ?></label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label><?php label('vender'); ?></label>
    <input type="text" class="form-control input-sm search" name="vender" value="<?php echo $vender; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block"><?php label('status'); ?></label>
		<select class="form-control input-sm search" name="status" onchange="getSearch()">
			<option value="all" <?php echo is_selected('all', $status); ?>><?php label('all'); ?></option>
			<option value="0" <?php echo is_selected('0', $status); ?>><?php label('not_save'); ?></option>
			<option value="1" <?php echo is_selected('1', $status); ?>><?php label('normal'); ?></option>
			<option value="2" <?php echo is_selected('2', $status); ?>><?php label('closed'); ?></option>
			<option value="3" <?php echo is_selected('3', $status); ?>><?php label('cancle'); ?></option>
		</select>
  </div>


	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:950px;">
			<thead>
				<tr>
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-50 middle text-center"><?php label('num'); ?></th>
					<th class="fix-width-100 middle text-center"><?php label('date'); ?></th>
					<th class="fix-width-150 middle"><?php label('doc_num'); ?></th>
					<th class="min-width-200 middle"><?php label('vender'); ?></th>
					<th class="fix-width-150 middle text-right"><?php label('amount'); ?></th>
					<th class="fix-width-100 middle"><?php label('due_date'); ?></th>
					<th class="fix-width-100 middle text-center"><?php label('status'); ?></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($po)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($po as $rs) : ?>
            <tr id="row-<?php echo $rs->code; ?>">
							<td class="middle">
									<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if($rs->status <= 2 && $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
								<?php if($rs->status < 2 && $this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
              </td>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->name; ?></td>
							<td class="middle text-right"><?php echo number($rs->total_amount, 2); ?></td>
              <td class="middle"><?php echo thai_date($rs->due_date); ?></td>
              <td class="middle text-center">
								<?php
									switch($rs->status)
									{
										case 0 :
											echo 'NC';
										break;
										case 1 :
										 	echo '';
										break;
										case 2 :
											echo 'Part';
										break;
										case 3 :
											echo 'Closed';
										break;
										case 4 :
											echo 'Cancled';
										break;
										default :
											echo '';
										break;
									}
								 ?>
							</td>

            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="8" class="text-center">---- <?php label('no_content'); ?> ----</td>
					</tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script src="<?php echo base_url(); ?>scripts/purchase/po.js"></script>

<?php $this->load->view('include/footer'); ?>
