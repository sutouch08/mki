<?php
  $ship_active = ($order->shipping_fee > 0 OR $order->state == 8 OR $order->is_paid == 1) ? 'disabled' : '';
  $ship_edit = $order->shipping_fee > 0 ? '' : 'hide';
  $ship_update = $order->shipping_fee > 0 ? 'hide' :'' ;
  $service_active = ($order->service_fee > 0 OR $order->state == 8 OR $order->is_paid == 1) ? 'disabled' : '';
  $service_edit = $order->service_fee > 0 ? '' : 'hide';
  $service_update = $order->service_fee > 0 ? 'hide' : '';
?>
<?php if($order->role == 'S') : ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="padding-top:10px;">
  <?php echo paymentLabel($payments); ?>
  <?php if($order->state == 8 && $order->is_paid == 0 && $order->payment_role == 4): ?>
    <button type="button" class="btn btn-sm btn-yellow" onclick="paid_order()">ทำเครื่องหมายว่าชำระแล้ว</button>
  <?php endif; ?>
  <?php if($order->state == 8 && $order->is_paid == 1 && $order->payment_role == 4): ?>
		<button type="button" class="btn btn-sm btn-success"><i class="fa fa-check"></i> ชำระเงินแล้ว</button>
    <button type="button" class="btn btn-sm btn-yellow" onclick="unpaid_order()">
      <i class="fa fa-times"></i> ทำเครื่องหมายว่ายังไม่ได้รับเงิน
    </button>
  <?php endif; ?>
  </div>
</div>
<hr class="padding-5" />
<?php endif; ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <div class="tabable">
    	<ul class="nav nav-tabs" role="tablist">
        <li class="active">
        	<a href="#state" aria-expanded="true" aria-controls="state" role="tab" data-toggle="tab">สถานะ</a>
        </li>
      	<li role="presentation">
          <a href="#address" aria-expanded="false" aria-controls="address" role="tab" data-toggle="tab">ที่อยู่</a>
        </li>
      </ul>
          <!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px;">
				<div role="tabpanel" class="tab-pane fade" id="address">
          <div class='row' style="margin-left:-6px; margin-right:-6px;">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="table-responsive">
                <table class="table table-bordered" style="margin-bottom:0px; border-collapse:collapse; border:0;">
                  <thead>
                    <tr style="background-color:white;">
                      <td colspan="7" align="center">ที่อยู่สำหรับจัดส่ง
                        <p class="pull-right top-p">
                          <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
                        </p>
                      </td>
                    </tr>
                    <tr style="font-size:12px;">
                      <td class="fix-width-80 text-center">ชื่อเรียก</td>
											<td class="fix-width-80 text-center">สาขา</td>
                      <td class="fix-width-120">ผู้รับ</td>
                      <td class="min-width-200">ที่อยู่</td>
                      <td class="fix-width-150">โทรศัพท์</td>
                      <td class="fix-width-120"></td>
                    </tr>
                  </thead>
                  <tbody id="adrs">
            <?php if(!empty($addr)) : ?>
            <?php 	foreach($addr as $rs) : ?>
                    <tr style="font-size:12px;" id="<?php echo $rs->id; ?>">
                      <td align="center"><?php echo $rs->alias; ?></td>
											<td class="text-center"><?php echo $rs->code; ?></td>
                      <td><?php echo $rs->name; ?></td>
                      <td><?php echo $rs->address.' '. $rs->sub_district.' '.$rs->district.' '.$rs->province.' '. $rs->postcode; ?></td>
                      <td><?php echo $rs->phone; ?></td>
                      <td align="right">
                <?php if( $rs->id == $order->address_id ) : ?>
                        <button type="button" class="btn btn-minier btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setDefault(<?php echo $rs->id; ?>)">
                          <i class="fa fa-check"></i>
                        </button>
                <?php else : ?>
                        <button type="button" class="btn btn-minier btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setDefault(<?php echo $rs->id; ?>)">
                          <i class="fa fa-check"></i>
                        </button>
                <?php endif; ?>
  											<button type="button" class="btn btn-minier btn-primary" onclick="printOnlineAddress(<?php echo $rs->id; ?>, '<?php echo $order->code; ?>')"><i class="fa fa-print"></i></button>
                        <button type="button" class="btn btn-minier btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
                      </td>
                    </tr>
            <?php 	endforeach; ?>
            <?php else : ?>
                    <tr><td colspan="6" align="center">ไม่พบที่อยู่</td></tr>
            <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div><!-- /row-->
        </div>
        <div role="tabpanel" class="tab-pane active" id="state">
  <?php $this->load->view('orders/order_state'); ?>
        </div>
      </div>
    </div>
	</div>
</div>
<hr class="padding-5">
