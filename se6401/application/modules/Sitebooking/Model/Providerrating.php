<?php

class Sitebooking_Model_Providerrating extends Core_Model_Item_Abstract
{
  public function getTable()
  {
    if( is_null($this->_table) )
    {
      $this->_table = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');
    }

    return $this->_table;
  }

  public function getOwner($recurseType = null)
  {
    return parent::getOwner($recurseType);
  }

}
