<?php


class Sitebooking_Model_DbTable_Itemofthedays extends Engine_Db_Table {

  protected $_rowClass = "Sitebooking_Model_Itemoftheday";

  public function getServiceOfDayList($params=array(), $resource_id, $resource_type) {

    //GET SERVICE OF THE DAY TABLE NAME
  $itemofthedayName = $this->info('name');
  
    //GET SERVICE TABLE INFO
  $serviceTable = Engine_Api::_()->getItemTable($resource_type);
    $serviceTableName = $serviceTable->info('name');

    //MAKE QUERY
  $select = $this->select()
      ->setIntegrityCheck(false)    
      ->from($itemofthedayName)
      ->join($serviceTableName, $serviceTableName . ".$resource_id = " . $itemofthedayName . '.resource_id')
            ->where($itemofthedayName.".resource_type = ?", $resource_type);

  $select->order((!empty($params['order']) ? $params['order'] : 'start_date' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));

    //RETURN RESULTS
  return $paginator = Zend_Paginator::factory($select);
  }

  public function getProviderOfDayList($params=array(), $resource_id, $resource_type) {

  //GET SERVICE OF THE DAY TABLE NAME
  $itemofthedayName = $this->info('name');
  
  //GET SERVICE TABLE INFO
  $providerTable = Engine_Api::_()->getItemTable($resource_type);
  $providerTableName = $providerTable->info('name');

  //MAKE QUERY
  $select = $this->select()
      ->setIntegrityCheck(false)    
      ->from($itemofthedayName)
      ->join($providerTableName, $providerTableName . ".$resource_id = " . $itemofthedayName . '.resource_id')
      ->where($itemofthedayName.".resource_type = ?", $resource_type);

  $select->order((!empty($params['order']) ? $params['order'] : 'start_date' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));

  //RETURN RESULTS
  return $paginator = Zend_Paginator::factory($select);
  }
}
?>
