<?php
class Stock_movement_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_last_move_by_warehouse($item_code, $warehouse_code, $move = "out")
  {
    $this->db
    ->select_max('date_upd')
    ->where('product_code', $item_code)
    ->where('warehouse_code', $warehouse_code);

    if($move == "in")
    {
      $this->db->where('move_in >', 0);
    }

    if($move == "out")
    {
      $this->db->where('move_out >', 0);
    }

    $rs = $this->db->get('stock_movement');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->date_upd;
    }

    return NULL;
  }


  public function get_last_move_by_item($item_code, $move = "out")
  {
    $this->db
    ->select_max('date_upd')
    ->where('product_code', $item_code);

    if($move == "in")
    {
      $this->db->where('move_in >', 0);
    }

    if($move == "out")
    {
      $this->db->where('move_out >', 0);
    }

    $rs = $this->db->get('stock_movement');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->date_upd;
    }

    return NULL;
  }


  public function get_movement_by_item_each_warehouse($item_code, array $whs = array(), $date)
  {
    if( ! empty($whs))
    {
      $this->db->where_in('warehouse_code', $whs);
    }

    $rs = $this->db
    ->select('product_code, warehouse_code')
    ->select_sum('move_in')
    ->select_sum('move_out')
    ->where('product_code', $item_code)
    ->where('date_upd <=', to_date($date))
    ->group_by('product_code')
    ->group_by('warehouse_code')
    ->order_by('warehouse_code', 'ASC')
    ->get('stock_movement');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_movement_by_item_group_warehouse($item_code, array $whs = array(), $date)
  {
    if( ! empty($whs))
    {
      $this->db->where_in('warehouse_code', $whs);
    }

    $rs = $this->db
    ->select('product_code')
    ->select_sum('move_in')
    ->select_sum('move_out')
    ->where('product_code', $item_code)
    ->where('date_upd <=', to_date($date))
    ->group_by('product_code')
    ->get('stock_movement');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_items()
  {
    $rs = $this->db->select('code, name')->where('count_stock', 1)->get('products');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_items_by_range($from_code, $to_code)
  {
    $from = $from_code;
    $to = $to_code;

    if($from > $to)
    {
      $from = $to_code;
      $to = $from_code;
    }

    $rs = $this->db
    ->select('code, name')
    ->where('code >=', $from)
    ->where('code <=', $to)
    ->where('count_stock', 1)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse)
  {
    $this->db
    ->select('products.barcode, products.code, products.name, products.cost')
    ->select_sum('stock.qty')
    ->from('stock')
    ->join('products', 'stock.product_code = products.code', 'left')
    ->join('product_size', 'product_size.code = products.size_code', 'left');

    //--- if specify warehouse
    if(empty($allWhouse))
    {
      $this->db->join('zone', 'stock.zone_code = zone.code', 'left');
      $this->db->where_in('zone.warehouse_code', $warehouse);
    }

    //--- if specify product
    if(empty($allProduct))
    {
      $this->db->where('products.style_code >=', $pdFrom);
      $this->db->where('products.style_code <=', $pdTo);
    }

    $this->db->group_by('stock.product_code');
    $this->db->order_by('products.style_code', 'ASC');
    $this->db->order_by('products.color_code', 'ASC');
    $this->db->order_by('product_size.position', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_stock_balance_prev_date($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $date)
  {
    $date = to_date($date);

    $qr  = "SELECT pd.barcode, pd.code, pd.name, pd.cost, (SUM(s.move_in) - SUM(s.move_out)) AS qty ";
    $qr .= "FROM stock_movement AS s ";
    $qr .= "JOIN products AS pd ON s.product_code = pd.code ";
    $qr .= "JOIN product_size AS ps ON pd.size_code = ps.code ";
    if($allWhouse == 0)
    {
      $qr .= "JOIN zone AS z ON s.zone_code = z.code ";
    }

    $qr .= "WHERE s.date_add <= '{$date}' ";

    if($allProduct == 0)
    {
      $qr .= "AND pd.style_code >= '{$pdFrom}' ";
      $qr .= "AND pd.style_code <= '{$pdTo}' ";
    }


    if($allWhouse == 0)
    {
      $wh_list = "";
      $i = 1;
      foreach($warehouse as $wh)
      {
        $wh_list .= $i === 1 ? "'{$wh}'" : ", '{$wh}'";
        $i++;
      }

      $qr .= "AND z.warehouse_code IN({$wh_list}) ";
    }

    $qr .= "GROUP BY pd.code ";
    $qr .= "ORDER BY pd.style_code ASC, ";
    $qr .= "pd.color_code ASC, ";
    $qr .= "ps.position ASC";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }
}

 ?>
