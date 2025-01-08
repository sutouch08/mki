
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
    <table class="table table-striped border-1" style="min-width:930px;">
      <thead>
        <tr class="font-size-12">
      <?php if($doc->status == 0) : ?>
          <th class="fix-width-60 text-center">
            <label>
              <input type="checkbox" id="chk-all" class="ace" onchange="checkAll()" />
              <span class="lbl"></span>
            </label>
          </th>
        <?php endif; ?>
          <th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-150">รหัส</th>
          <th class="min-width-200">สินค้า</th>
          <th class="fix-width-100 text-right">ราคา</th>
          <th class="fix-width-120 text-right">ส่วนลด</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-120 text-right">มูลค่า</th>
        <?php if($doc->status == 0) : ?>
          <th class="fix-width-40"></th>
        <?php endif; ?>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  $totalQty = 0; ?>
<?php  $totalAmount = 0; ?>
<?php  foreach($details as $rs) : ?>
        <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <?php if($doc->status == 0) : ?>
          <td class="middle text-center">
            <label>
              <input type="checkbox" class="ace chk" id="chk-<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>" />
              <span class="lbl"></span>
            </label>
          </td>
        <?php endif; ?>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle"><?php echo $rs->product_name; ?></td>
          <td class="middle text-right">
            <?php if($doc->status == 0) : ?>
              <input type="number"
                class="form-control input-xs text-right input-price"
                id="input-price-<?php echo $rs->id; ?>"
                data-id="<?php echo $rs->id; ?>"
                data-price="<?php echo $rs->price; ?>"
                value="<?php echo round($rs->price,2); ?>"
                onchange="updateRow(<?php echo $rs->id; ?>)"/>
            <?php else : ?>
              <?php echo number($rs->price, 2); ?>
            <?php endif; ?>
          </td>
          <td class="middle text-right">
            <?php if($doc->status == 0) : ?>
              <input type="text"
                class="form-control input-xs text-right input-disc"
                id="input-disc-<?php echo $rs->id; ?>"
                data-id="<?php echo $rs->id; ?>"
                data-disc="<?php echo $rs->discount; ?>"
                value="<?php echo $rs->discount; ?>"
                onchange="updateRow(<?php echo $rs->id; ?>)"/>
            <?php else : ?>
              <?php echo $rs->discount; ?>
            <?php endif; ?>
          </td>
          <td class="middle text-right">
            <?php if($doc->status == 0) : ?>
              <input type="number"
                class="form-control input-xs text-right input-qty"
                id="qty-<?php echo $rs->id; ?>"
                data-id="<?php echo $rs->id; ?>"
                data-qty="<?php echo $rs->qty; ?>"
                value="<?php echo $rs->qty; ?>"
                onchange="updateRow(<?php echo $rs->id; ?>)"/>
              <?php else : ?>
                <?php echo number($rs->qty); ?>
              <?php endif; ?>
          </td>
          <td class="middle text-right">
            <?php if($doc->status == 0) : ?>
              <input type="text"
                class="form-control input-xs text-right line-amount"
                id="amount-<?php echo $rs->id; ?>"
                data-id="<?php echo $rs->id; ?>"
                data-amount="<?php echo $rs->amount; ?>"
                value="<?php echo number($rs->amount, 2); ?>" disabled/>
            <?php else : ?>
              <?php echo number($rs->amount, 2); ?>
            <?php endif; ?>
          </td>
        <?php if($doc->status == 0) : ?>
          <td class="middle text-center">
            <?php if($this->pm->can_edit OR $this->pm->can_delete) : ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="deleteRow('<?php echo $rs->id; ?>', '<?php echo $rs->product_code; ?>')">
                <i class="fa fa-trash"></i>
              </button>
            <?php endif; ?>
        <?php endif; ?>
          </td>
        </tr>

<?php  $no++; ?>
<?php  $totalQty += $rs->qty; ?>
<?php  $totalAmount += $rs->amount; ?>
<?php endforeach; ?>
<?php $colspan = $doc->status == 0 ? 6 : 5; ?>
      <tr id="total-row">
        <td colspan="<?php echo $colspan; ?>" class="middle text-right"><strong>รวม</strong></td>
        <td id="total-qty" class="middle text-right"><?php echo number($totalQty); ?></td>
        <td id="total-amount" class="middle text-right"><?php echo number($totalAmount,2); ?></td>
      <?php if($doc->status == 0) : ?>
        <td></td>
      <?php endif; ?>
      </tr>
<?php else : ?>
  <?php $colspan = $doc->status == 0 ? 6 : 5; ?>
  <tr id="total-row">
    <td colspan="<?php echo $colspan; ?>" class="middle text-right"><strong>รวม</strong></td>
    <td id="total-qty" class="middle text-right">0</td>
    <td id="total-amount" class="middle text-right">0</td>
    <?php if($doc->status == 0) : ?>
      <td></td>
    <?php endif; ?>
  </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script id="new-row-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-12 rox" id="row-{{id}}">
    <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace chk" id="chk-{{id}}" value="{{id}}>" />
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center no"></td>
    <td class="middle">{{product_code}}</td>
    <td class="middle">{{product_name}}</td>
    <td class="middle text-right">
      <input type="number"
        class="form-control input-xs text-right input-price"
        id="input-price-{{id}}"
        data-id="{{id}}"
        data-price="{{price}}"
        value="{{price}}" onchange="updateRow({{id}})"/>
    </td>
    <td class="middle text-right">
      <input type="text"
        class="form-control input-xs text-right input-disc"
        id="input-disc-{{id}}"
        data-id="{{id}}"
        data-disc="{{discount}}"
        value="{{discount}}" onchange="updateRow({{id}})" />
    </td>
    <td class="middle text-right">
      <input type="number"
        class="form-control input-xs text-right input-qty"
        id="qty-{{id}}"
        data-id="{{id}}"
        data-qty="{{qty}}"
        value="{{qty}}" onchange="updateRow({{id}})" />
    </td>
    <td class="middle text-right">
      <input type="text"
        class="form-control input-xs text-right line-amount"
        id="amount-{{id}}"
        data-id="{{id}}"
        data-amount="{{amount}}"
        value="{{amountLabel}}" disabled/>
    </td>
    <td class="middle text-center">
      <button type="button" class="btn btn-minier btn-danger" onclick="deleteRow('{{id}}', '{{product}}')">
        <i class="fa fa-trash"></i>
      </button>
    </td>
  </tr>
</script>

<script id="row-template" type="text/x-handlebarsTemplate">
  <td class="middle text-center no"></td>
  <td class="middle text-center">{{barcode}}</td>
  <td class="middle hide-text">{{product}}</td>
  <td class="middle text-right price" id="price-{{id}}">{{price}}</td>
  <td class="middle text-right disc" id="disc-{{id}}">{{discount}}</td>
  <td class="middle text-right qty" id="qty-{{id}}">{{qty}}</td>
  <td class="middle text-right amount" id="amount-{{id}}">{{amount}}</td>
  <td class="middle text-center">
    <button type="button" class="btn btn-xs btn-danger" onclick="deleteRow('{{id}}', '{{product}}')">
      <i class="fa fa-trash"></i>
    </button>
  </td>
</script>

<script id="detail-template" type="text/x-handlebarsTemplate">
{{#each this}}
  {{#if @last}}
  <tr id="total-row">
    <td colspan="5" class="middle text-right"><strong>รวม</strong></td>
    <td id="total-qty" class="middle text-center">{{ total_qty }}</td>
    <td id="total-amount" colspan="2" class="middle text-center">{{ total_amount }}</td>
  </tr>
  {{else}}
  <tr class="font-size-12 rox" id="row-{{id}}">
    <td class="middle text-center no"></td>
    <td class="middle text-center">{{barcode}}</td>
    <td class="middle">{{product}}</td>
    <td class="middle text-center">
      <input type="number" class="form-control input-xs text-center padding-5 price" min="0" id="price-{{id}}" value="{{price}}" onKeyup="reCal('{{id}}')" onChange="reCal('{{id}}')" />
    </td>
    <td class="middle text-center">
      <input type="text" class="form-control input-xs text-center disc" id="disc-{{id}}" value="{{discount}}" onKeyup="recal('{{id}}')" onChange="recal('{{id}}')" />
    </td>
    <td class="middle text-center">
      <input type="number" class="form-control input-xs text-center qty" min="0" id="qty-{{id}}" value="{{qty}}" onKeyup="reCal('{{id}}')" onChange="reCal('{{id}}')" />
    </td>
    <td class="middle text-right amount" id="amount-{{id}}">{{ amount }}</td>
    <td class="middle text-center">
      <button type="button" class="btn btn-xs btn-danger" onclick="deleteRow('{{id}}', '{{product}}')"><i class="fa fa-trash"></i></button>
    </td>
  </tr>
  {{/if}}

{{/each}}
</script>
