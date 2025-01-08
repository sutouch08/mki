<?php
class Product_tab_model extends CI_Model
{

  public $id;
	public $name;
	public $id_parent;


  public function __construct()
  {
    parent::__construct();
  }

  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('product_tab');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }

  public function count_rows(array $ds = array())
  {
    $this->db->from('product_tab AS t')
    ->join('product_tab AS p', 't.id_parent = p.id', 'left')
    ->where('t.id >', 0, FALSE)
    ->where('t.name IS NOT NULL', NULL, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('t.name', $ds['name']);
    }

    if(!empty($ds['parent']))
    {
      $this->db->like('p.name', $ds['parent']);
    }

    return $this->db->count_all_results();
  }



  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('t.id, t.name, t.id_parent, p.name AS parent')
    ->from('product_tab AS t')
    ->join('product_tab AS p', 't.id_parent = p.id', 'left')
    ->where('t.id >', 0, FALSE)
    ->where('t.name IS NOT NULL', NULL, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('t.name', $ds['name']);
    }

    if(!empty($ds['parent']))
    {
      $this->db->like('p.name', $ds['parent']);
    }

    if(!empty($perpage))
    {
      $offset = empty($offset) ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


	public function add(array $ds = array())
	{
    if(!empty($ds))
    {
      return $this->db->insert('product_tab', $ds);
    }

    return FALSE;
	}


	public function update($id, array $ds = array())
	{
		if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('product_tab', $ds);
    }

    return FALSE;
	}



	public function updateChild($id, $id_parent)
	{
    return $this->db->set('id_parent', $id_parent)->where('id_parent', $id)->update('product_tab');
	}



	public function delete($id)
	{
    return $this->db->where('id', $id)->delete('product_tab');
	}


	public function delete_tab_style($id)
	{
		return $this->db->where('id_tab', $id)->delete('product_tab_style');
	}


	public function delete_tab_item($id)
	{
		return $this->db->where('id_tab', $id)->delete('product_tab_item');
	}
	

	public function updateTabsProduct($style_code, array $ds = array())
	{
		$this->db->trans_start();
		$this->db->where('style_code', $style_code)->delete('product_tab_style');

		if( !empty($ds))
		{
      foreach( $ds as $id)
      {
        $this->db->insert('product_tab_style', array('style_code' => $style_code, 'id_tab' => $id));
      }
		}

		$this->db->trans_complete();

		return $this->db->trans_status();
	}


	public function updateTabsItem($item_code, $ds = NULL)
	{
		$this->db->trans_start();
		//--- delete old data
		$this->db->where('item_code', $item_code)->delete('product_tab_item');

		if(!empty($ds))
		{
			//--- insert new data
			foreach($ds as $id)
			{
				$this->db->insert('product_tab_item', array('item_code' => $item_code, 'id_tab' => $id));
			}
		}

		$this->db->trans_complete();

		return $this->db->trans_status();
	}


	public function isExists($field, $val, $id='')
	{
		if( $id != '' )
		{
			$qs = $this->db->query("SELECT id FROM product_tab WHERE ".$field." = '".$val."' AND id != ".$id);
		}
		else
		{
			$qs = $this->db->query("SELECT id FROM product_tab WHERE ".$field." = '".$val."'");
		}

		if( $qs->num_rows() > 0)
		{
			return TRUE;
		}

		return FALSE;
	}



	public function getName($id)
	{
		$sc = "TOP LEVEL";
		$qs = $this->db->select('name')->where('id', $id)->get('product_tab');
		if( $qs->num_rows() == 1 )
		{
			return $qs->row()->name;
		}

		return $sc;
	}



	public function getParentId($id)
	{
		$sc = 0;
		$qs = $this->db->select('id_parent')->where('id', $id)->get('product_tab');
		if( $qs->num_rows() == 1 )
		{
			return $qs->row()->id_parent;
		}

		return $sc;
	}


	public function getAllParent($id)
	{
		$sc = array();
		$id_parent = $this->getParentId($id);
		while( $id_parent > 0 )
		{
			$sc[$id_parent] = $id_parent;
			$id_parent = $this->getParentId($id_parent);
		}
		return $sc;
	}



  //-------- เอารายการใน product_tab_style มา
  public function getStyleTabsId($code)
  {
    $sc = array();
    $qs = $this->db->select('id_tab')->where('style_code', $code)->get('product_tab_style');
    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[$rs->id_tab] = $rs->id_tab;
      }
    }

    return $sc;
  }


  public function getItemTabsId($code)
  {
    $sc = array();
    $qs = $this->db->select('id_tab')->where('item_code', $code)->get('product_tab_item');
    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[$rs->id_tab] = $rs->id_tab;
      }
    }

    return $sc;
  }



	//-------- เอารายการใน product_tab_style มา
	public function getParentTabsId($style_code)
	{
		$sc = array();
		$ds = $this->getStyleTabsId($style_code);
		if( !empty( $ds ))
		{
			foreach( $ds as $id )
			{
				$id_tab = $this->getParentId($id);
				while( $id_tab > 0 )
				{
					$sc[$id_tab] = $id_tab;
					$id_tab = $this->getParentId($id_tab);
				}
			}
			return $sc;
		}

		$qs = $this->db->select('id_tab')->where('style_code', $style_code)->get('product_tab_style');

		if( $qs->num_rows() > 0 )
		{
      foreach($qs->result() as $rs)
      {
        $sc[$rs->id_tab] = $rs->$id_tab;
      }
		}

		return $sc;
	}



	//-------- เอารายการใน product_tab_item มา
	public function getParentItemTabsId($code)
	{
		$sc = array();
		$ds = $this->getItemTabsId($code);

		if( !empty( $ds ))
		{
			foreach( $ds as $id )
			{
				$id_tab = $this->getParentId($id);

				while( $id_tab > 0 )
				{
					$sc[$id_tab] = $id_tab;
					$id_tab = $this->getParentId($id_tab);
				}
			}
			return $sc;
		}

		$qs = $this->db->select('id_tab')->where('item_code', $code)->get('product_tab_item');

		if( $qs->num_rows() > 0 )
		{
      foreach($qs->result() as $rs)
      {
        $sc[$rs->id_tab] = $rs->$id_tab;
      }
		}

		return $sc;
	}





	public function getParentList($id = 0)
	{
		//----- Parent cannot be yoursalfe
		return $this->db->where('id !=', $id)->get('product_tab');
	}





	//-----------------  Search Result
	public function getSearchResult($txt)
	{
		return $this->db->like('name', $txt)->get('product_tab');

	}






	public function countMember($id)
	{
    return $this->db->where('id_tab', $id)->count_all_results('product_tab_style');
	}





	public function getStyleInTab($id)
	{
		$qr = "SELECT t.style_code FROM product_tab_style AS t ";
		$qr .= "JOIN product_style AS p ON t.style_code = p.code ";
		$qr .= "WHERE p.active = 1 AND p.can_sell = 1 AND is_deleted = 0 ";
		$qr .= "AND id_tab = ".$id;

		return $this->db->query($qr);
	}


  public function get_style_in_tab($id)
  {
  	$rs = $this->db
		->select('t.style_code AS code')
		->select('p.name, p.price')
		->from('product_tab_style AS t')
		->join('product_style AS p', 't.style_code = p.code')
		->where('p.active', 1)
		->where('p.can_sell', 1)
		->where('p.is_deleted', 0)
		->where('t.id_tab', $id)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_item_in_tab($id)
	{
		$rs = $this->db
		->select('pd.code, pd.name, pd.price')
		->from('product_tab_item AS t')
		->join('products AS pd', 't.item_code = pd.code')
		->where('pd.active', 1)
		->where('pd.can_sell', 1)
		->where('pd.is_deleted', 0)
		->where('t.id_tab', $id)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}





	public function getStyleInSaleTab($id)
	{
		$qr = "SELECT t.style_code FROM product_tab_style AS t ";
		$qr .= "JOIN product_style AS p ON t.style_code = p.id ";
		$qr .= "WHERE p.active = 1 AND p.can_sell = 1 AND p.is_deleted = 0 AND p.show_in_sale = 1 ";
		$qr .= "AND id_tab = ".$id;

		return $this->db->query($qr);
	}



  public function is_has_child($id)
  {
    $this->db->where('id_parent', $id);
    $rs = $this->db->count_all_results('product_tab');
    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


	public function getChild($id)
	{
		$rs = $this->db
		->where('id_parent', $id)
		->get('product_tab');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



} //--- end class

?>
