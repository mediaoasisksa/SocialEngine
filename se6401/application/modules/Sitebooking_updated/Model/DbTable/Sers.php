<?php

class Sitebooking_Model_DbTable_Sers extends Core_Model_Item_DbTable_Abstract {

  protected $_rowClass = "Sitebooking_Model_Ser";

  public function getMappedSiteservice($category_id) {

    //RETURN IF CATEGORY ID IS NULL
    if (empty($category_id)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
      ->from($this->info('name'), 'ser_id')
      ->where("category_id = $category_id OR first_level_category_id = $category_id OR second_level_category_id = $category_id");

    //GET DATA
    $categoryData = $this->fetchAll($select);

    if (!empty($categoryData)) {
      return $categoryData->toArray();
    }

    return null;
  }

  public function getCategoryList($category_id, $categoryType) {

    //RETURN IF CATEGORY ID IS NULL
    if (empty($category_id)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
      ->from($this->info('name'), 'ser_id')
      ->where("$categoryType = ?", $category_id);

    //GET DATA
    return $this->fetchAll($select);
  }

  public function getServicesCount($id, $column_name, $foruser = null) {

    //MAKE QUERY
    $select = $this->select()
      ->from($this->info('name'), array('COUNT(*) AS count'));

    if (!empty($column_name) && !empty($id)) {
      $select->where("$column_name = ?", $id);
    }

    $totalServices = $select->query()->fetchColumn();

    //RETURN EVENTS COUNT
    return $totalServices;
  }
  
  
  public function getServicesPaginator($params = array(), $customParams = array()) {
    $paginator = Zend_Paginator::factory($this->getServicesSelect($params, $customParams));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    } else {
      $page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.services.page', 10);
      $paginator->setItemCountPerPage($page);
    }

    return $paginator;
  }

  public function getServicesSelect($params = array(), $customParams = array()) {

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
    $serviceTableName = $serviceTable->info('name');

    $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');
    $providerTableName = $providerTable->info('name');

    $locationsTable = Engine_Api::_()->getDbtable('providerlocations', 'sitebooking');
    $locationsTableName = $locationsTable->info('name');

    $tagMapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tagMapsTableName = $tagMapsTable->info('name');

    $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
    $tagTableName = $tagTable->info('name');


    //GET SEARCH TABLE
    $searchTable = Engine_Api::_()->fields()->getTable('sitebooking_ser', 'search')->info('name');

    $select = $serviceTable->select()
      ->order(!empty($params['orderby']) ? $serviceTableName . "." . $params['orderby'] . ' DESC' : $serviceTableName . '.creation_date DESC');

    $select
      ->setIntegrityCheck(false)
      ->from($serviceTableName, array('*'))
      ->join($providerTableName, "$serviceTableName.parent_id = $providerTableName.pro_id", array('title as provider_title', 'photo_id as provider_photo_id', 'slug as provider_slug','telephone_no'));


    if( !empty($params['tag']) && !empty($params['tag_id'])) {
      $select
        ->setIntegrityCheck(false)
        // ->from($serviceTableName, array('*'))
        ->joinLeft($tagMapsTableName, "$tagMapsTableName.resource_id = $serviceTableName.ser_id")
        ->where($tagMapsTableName . '.resource_type = ?', 'sitebooking_ser')
        ->where($tagMapsTableName . '.tag_id = ?', $params['tag_id']);
    }


    if (isset($customParams) && !empty($customParams)) {

      //PROCESS OPTIONS
      $tmp = array();
      foreach ($customParams as $k => $v) {
        if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
          continue;
        } else if (false !== strpos($k, '_field_')) {
          list($null, $field) = explode('_field_', $k);
          $tmp['field_' . $field] = $v;
        } else if (false !== strpos($k, '_alias_')) {
          list($null, $alias) = explode('_alias_', $k);
          $tmp[$alias] = $v;
        } else {
          $tmp[$k] = $v;
        }
      }
      $customParams = $tmp;

      $select = $select
        ->setIntegrityCheck(false)
        ->joinLeft($searchTable, "$searchTable.item_id = $serviceTableName.ser_id", null);

      $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitebooking_ser', $customParams);
      foreach ($searchParts as $k => $v) {
        $select->where("`{$searchTable}`.{$k}", $v);
      }
    }

    if (isset($params['pro_id'])) {
      $select->where($providerTableName . ".pro_id = ?", $params['pro_id']);
    }

    if (isset($params['user_id'])) {
      $select->where("$serviceTableName.owner_id = ?", $params['user_id']);
    }

    if (!empty($params['category']) && $params['category'] != -1) {
      $select->where($serviceTableName . '.category_id = ?', $params['category']);
    }

    if (!empty($params['first_level_category_id']) && $params['first_level_category_id'] != -1) {
      $select->where($serviceTableName . '.first_level_category_id = ?', $params['first_level_category_id']);
    }

    if (!empty($params['second_level_category_id']) && $params['second_level_category_id'] != -1) {
      $select->where($serviceTableName . '.second_level_category_id = ?', $params['second_level_category_id']);
    }

    if (isset($params['status'])) {
      $select->where($serviceTableName . '.status = ?', $params['status']);
    }

    if (isset($params['approved'])) {
      $select->where($serviceTableName . '.approved = ?', $params['approved']);
    }

    if (!empty($params['search'])) {
      $sql = $select->where($serviceTableName . ".title LIKE ?", '%' . $params['search'] . '%');
    }

    if (!empty($params['provider'])) {
      $select->where($providerTableName . ".title LIKE ?", '%' . $params['provider'] . '%');
    }


    if(!empty($params['locationDistance']) && $params['locationDistance'] > 0 && !empty($params['location'])){

      $latitude = $params['latitude'];
      $longitude = $params['longitude'];

      if(empty($latitude) && empty($longitude)){
        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Directory / Pages'));
        $latitude = $locationResults['latitude'];
        $longitude = $locationResults['longitude'];
      }

      $radius = $params['locationDistance']; //in miles
      $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.search",'miles');
      if ($unit === "kilometers") {
          $radius = $radius * (0.621371192);
      }

      if(!empty($latitude) && !empty($longitude)){

        //Start We have follow this code from event plugin.
        // $latitudeRadians = deg2rad($latitude);
        $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";

        $select->setIntegrityCheck(false)
              // ->from($providerTableName)
              ->joinLeft($locationsTableName, "$providerTableName.pro_id = $locationsTableName.pro_id", array("(degrees(acos($latitudeSin * sin(radians($locationsTableName.latitude)) + $latitudeCos * cos(radians($locationsTableName.latitude)) * cos(radians($longitude - $locationsTableName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationsTableName.latitude)) + $latitudeCos * cos(radians($locationsTableName.latitude)) * cos(radians($longitude - $locationsTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
      }

        


    }      

    if( (!empty($params['city']) && $params['locationDistance'] <= 0) || (!empty($params['country']) && $params['locationDistance'] <= 0) || (!empty($params['location']) && $params['locationDistance'] <= 0) && !empty($params['detectlocation']) ){

      $select = $select->setIntegrityCheck(false)
            // ->from($providerTableName)
            ->joinLeft($locationsTableName, "$providerTableName.pro_id = $locationsTableName.pro_id");
      if( !empty($params['location']) && $params['locationDistance'] <= 0 && !empty($params['detectlocation']) ){
        $temp = json_decode($params['detectlocation'], true);
        $select = $select->where($locationsTableName . ".city  LIKE '%" . $temp['city'] . "%'");
        $select->where($locationsTableName . ".state  LIKE '%" . $temp['state'] . "%'");
        $select->where($locationsTableName . ".country  LIKE '%" . $temp['country'] . "%'");

      }

      if( !empty($params['city']) ){
        $select = $select->where($locationsTableName . ".city  LIKE '%" . $params['city'] . "%' OR ".$locationsTableName . ".state  LIKE '%" . $params['city']."%'");

      }

      if(!empty($params['country']) ){
        $select = $select->where($locationsTableName . '.country  LIKE ?', '%' . $params['country'] . '%');
      } 
    }

    if (empty($params['enabled'])) {
      $select->where($serviceTableName . '.enabled = 1');
    }

    if (!empty($params['limit'])) {
      $select->limit($params['limit']);
    }
    
    return $select;
  }

  public function serviceListTabs($params = array()) {

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
    $serviceTableName = $serviceTable->info('name');

    $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');
    $providerTableName = $providerTable->info('name');


    $select = $serviceTable->select();
    $select
      ->setIntegrityCheck(false)
      ->from($serviceTableName, array('*'))
      ->join($providerTableName, "$serviceTableName.parent_id = $providerTableName.pro_id", array('title as provider_title', 'photo_id as provider_photo_id', 'slug as provider_slug','telephone_no'));

    if (isset($params['approved'])) {
      $select->where($serviceTableName . '.approved = ?', $params['approved']);
    }  

    if (isset($params['status'])) {
      $select->where($serviceTableName . '.status = ?', $params['status']);
    }  

    if($params['filter_type'] == "creation_date") {
      $select->order($serviceTableName . "." . 'creation_date DESC');
    }  

    if (!empty($params['filter_type'])) {

        if(in_array($params['filter_type'], array("featured", "hot", "newlabel", "sponsored"))) {
            $select->where($serviceTableName . ".".$params['filter_type'] ."= ?", 1);
        }

        if(in_array($params['filter_type'], array("rating", "review_count", "like_count", "comment_count"))) {

            $select->order(!empty($params['filter_type']) ? $serviceTableName . "." . $params['filter_type'] . ' DESC' : $serviceTableName . '.creation_date DESC');
        }

    } 

    if(!empty($params['category_id']) && $params['category_id'] != -1) {
      $select->where($serviceTableName . '.category_id = ?', $params['category_id']);
    }

    $select->where($serviceTableName . '.enabled = 1');

    if(!empty($params['limit'])) {
      $select->limit($params['limit']);
    }

    return $select;
  }


  function calculatedistance($lat1, $lon1, $lat2, $lon2) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.search",'miles');
    if ($unit === "kilometers") {
        return ($miles * 1.609344);
    } 
    elseif ($unit == "miles") {
        return $miles;
    }

  }

  public function getItemOfDay() {

    $serviceTableName = $this->info('name');

    $itemofthedaytable = Engine_Api::_()->getDbtable('itemofthedays', 'sitebooking');
    $itemofthedayName = $itemofthedaytable->info('name');

    $select = $this->select();
    $select = $select->setIntegrityCheck(false)
            ->from($serviceTableName, array('*'))
            ->join($itemofthedayName, $serviceTableName . ".ser_id = " . $itemofthedayName . '.resource_id', array('start_date'))
            ->where($itemofthedayName . '.resource_type=?', 'sitebooking_ser')
            ->where($itemofthedayName . '.start_date <=?', date('Y-m-d'))
            ->where($itemofthedayName . '.end_date >=?', date('Y-m-d'))
            ->order('RAND()');
    return $this->fetchRow($select);
  }

  public function getDayItems($title, $limit = 10) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('ser_id', 'owner_id', 'title', 'photo_id'));

    $select->where($this->info('name') . ".title LIKE ?", '%' . $title . '%')
            ->where('approved = ?', '1')
            ->where('enabled = ?', '1')
            ->where('status = ?', '1')
            ->order('title ASC')
            ->limit($limit);

    //FETCH RESULTS
    return $this->fetchAll($select);
  }

  public function getServiceTags($params = array()) {

    $tagMapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tagMapsTableName = $tagMapsTable->info('name');

    $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
    $tagTableName = $tagTable->info('name');

    $select = $tagMapsTable->select();
    $select->order($tagTableName . '.tag_count DESC');
    $select
        ->setIntegrityCheck(false)
        ->from($tagMapsTableName,array($tagMapsTableName.".resource_type"))
        ->join($tagTableName, "$tagMapsTableName.tag_id = $tagTableName.tag_id",array("*"));

    $sql = $tagMapsTableName.".resource_type = "."'sitebooking_ser'"." AND ".$tagMapsTableName.".tagger_type = '"."user"."' AND ".$tagMapsTableName.".tag_type = "."'core_tag'";
    $select->group($tagTableName.'.tag_id');
    $select->where($sql);
    if(!empty($params["limit"]))
      $select->limit($params["limit"]);

    return $select;
  }

}

?>