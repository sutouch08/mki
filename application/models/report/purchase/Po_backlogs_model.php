<?php
class Po_backlogs_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_data(array $ds = array())
  {
    if(!empty($ds))
    {
      if(!empty($ds['is_item']))
      {
        $this->db
        ->select('po.code, po.date_add, po.due_date, po.vender_code, po.vender_name, po.status')
        ->select('pd.product_code, pd.qty, pd.received')
        ->from('po_detail AS pd')
        ->join('po AS po', 'po.code = pd.po_code', 'left')
        ->where('po.date_add >=', $ds['from_date'])
        ->where('po.date_add <=', $ds['to_date']);

        if($ds['po_status'] !== 'A')
        {
          //---- Open only
          if($ds['po_status'] === 'O')
          {
            $this->db->where_in('po.status', array(1, 2));
            $this->db->where('pd.valid', 0);
          }

          if($ds['po_status'] === 'C')
          {
            $this->db->where('po.status', 3);
            $this->db->where('pd.valid', 1);
          }
        }


        if(empty($ds['all_po']))
        {
          if(!empty($ds['po_from']) && !empty($ds['po_to']))
          {
            $this->db->where('po.code >=', $ds['po_from']);
            $this->db->where('po.code <=', $ds['po_to']);
          }
        }

        if(empty($ds['all_vendor']))
        {
          if(!empty($ds['vendor_from']) && !empty($ds['vendor_to']))
          {
            $this->db->where('po.vender_code >=', $ds['vendor_from']);
            $this->db->where('po.vender_code <=', $ds['vendor_to']);
          }
        }

        if(empty($ds['all_product']))
        {
          if(!empty($ds['item_from']) && !empty($ds['item_to']))
          {
            $this->db->where('pd.product_code >=', $ds['item_from']);
            $this->db->where('pd.product_code <=', $ds['item_to']);
          }
        }

      }
      else
      {
        $this->db
        ->select('po.code, po.date_add, po.due_date, po.vender_code, po.vender_name, po.status')
        ->select('pd.style_code AS product_code')
        ->select_sum('pd.qty', 'qty')
        ->select_sum('pd.received', 'received')
        ->from('po_detail AS pd')
        ->join('po AS po', 'po.code = pd.po_code', 'left')
        ->where('po.date_add >=', $ds['from_date'])
        ->where('po.date_add <=', $ds['to_date']);

        if($ds['po_status'] !== 'A')
        {
          //---- Open only
          if($ds['po_status'] === 'O')
          {
            $this->db->where_in('po.status', array(1, 2));
            $this->db->where('pd.valid', 0);
          }

          if($ds['po_status'] === 'C')
          {
            $this->db->where('po.status', 3);
            $this->db->where('pd.valid', 1);
          }
        }


        if(empty($ds['all_po']))
        {
          if(!empty($ds['po_from']) && !empty($ds['po_to']))
          {
            $this->db->where('po.code >=', $ds['po_from']);
            $this->db->where('po.code <=', $ds['po_to']);
          }
        }

        if(empty($ds['all_vendor']))
        {
          if(!empty($ds['vendor_from']) && !empty($ds['vendor_to']))
          {
            $this->db->where('po.vender_code >=', $ds['vendor_from']);
            $this->db->where('po.vender_code <=', $ds['vendor_to']);
          }
        }

        if(empty($ds['all_product']))
        {
          if(!empty($ds['style_from']) && !empty($ds['style_to']))
          {
            $this->db->where('pd.style_code >=', $ds['style_from']);
            $this->db->where('pd.style_code <=', $ds['style_to']);
          }
        }

        $this->db->group_by('pd.style_code');
      }

      $this->db->order_by('po.code', 'ASC');
      $this->db->order_by('pd.style_code', 'ASC');
      $this->db->order_by('pd.product_code', 'ASC');
      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }

    return FALSE;
  }



} //--- end class
 ?>
