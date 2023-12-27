<?php
class Sitebooking_Model_DbTable_Pros extends Core_Model_Item_DbTable_Abstract 
{

  protected $_rowClass = "Sitebooking_Model_Pro";

  public function getProvidersPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getProvidersSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }

    if( empty($params['limit']) )
    {
      $page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page');
      $paginator->setItemCountPerPage($page);
    }

    return $paginator;
  }

  public function getProvidersSelect($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $rName = $table->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');

    $locationsTable = Engine_Api::_()->getDbtable('providerlocations', 'sitebooking');
    $locationsTableName = $locationsTable->info('name');

    $select = $table->select()
      ->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : $rName.'.creation_date DESC' );
    if( !empty($params['user_id']) && is_numeric($params['user_id']) )
    {
      $owner = Engine_Api::_()->getItem('user', $params['user_id']);

      $select = $this->getProfileItemsSelect($owner, $select);
    } elseif( !empty($params['user']) && $params['user'] instanceof User_Model_User ) {
      $owner = $params['user'];
      $select = $this->getProfileItemsSelect($owner, $select);
    } elseif( isset($params['users']) ) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName.'.owner_id in (?)', new Zend_Db_Expr($str));
      if( !in_array($viewer->level_id, $this->_excludedLevels) ) {
        $select->where("view_privacy != ? ", 'owner');
      }

    } else {
      $param = array();
      $select = $this->getItemsSelect($param, $select);
    }

    if( !empty($params['tag']) && !empty($params['tag_id']) )
    {
      $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($tmName, "$tmName.resource_id = $rName.pro_id")
        ->where($tmName.'.resource_type = ?', 'sitebooking_pro')
        ->where($tmName.'.tag_id = ?', $params['tag_id']);
    }

    if( !empty($params['category']) )
    {
      $category_id = $params['category'];
      $serviceTable = Engine_Api::_()->getItemtable('sitebooking_ser');
      $serviceTableName = $serviceTable->info('name');
      $select
        ->setIntegrityCheck(false)
        ->from($rName,array('*'))
        ->join($serviceTableName, "$serviceTableName.parent_id = $rName.pro_id",'title as service_title')
        ->where("$serviceTableName.category_id = $category_id OR $serviceTableName.first_level_category_id = $category_id OR $serviceTableName.second_level_category_id  = $category_id" )
        ->group($rName.'.pro_id');
    }

    if(isset($params['status']) )
    {
      $select->where($rName.'.status = ?', $params['status']);
    }

    // Could we use the search indexer for this?
    if( !empty($params['search']) )
    {
      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$params['search'].'%');
    }

    if( !empty($params['start_date']) )
    {
      $select->where($rName.".creation_date > ?", date('Y-m-d', $params['start_date']));
    }

    if( !empty($params['end_date']) )
    {
      $select->where($rName.".creation_date < ?", date('Y-m-d', $params['end_date']));
    }

    if( !empty($params['visible']) )
    {
      $select->where($rName.".search = ?", $params['visible']);
    }

    if( !empty($params['approved']) )
    {
      $select->where($rName.".approved = ?", $params['approved']);
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
              ->from($rName)
              ->joinLeft($locationsTableName, "$rName.pro_id = $locationsTableName.pro_id", array("(degrees(acos($latitudeSin * sin(radians($locationsTableName.latitude)) + $latitudeCos * cos(radians($locationsTableName.latitude)) * cos(radians($longitude - $locationsTableName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationsTableName.latitude)) + $latitudeCos * cos(radians($locationsTableName.latitude)) * cos(radians($longitude - $locationsTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
      }

    }      

    if( (!empty($params['city']) && $params['locationDistance'] <= 0) || (!empty($params['country']) && $params['locationDistance'] <= 0) || (!empty($params['location']) && $params['locationDistance'] <= 0) && !empty($params['detectlocation']) ){

      $select = $select->setIntegrityCheck(false)
            ->from($rName)
            ->joinLeft($locationsTableName, "$rName.pro_id = $locationsTableName.pro_id");
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

    if( !empty($owner) ) {
      return $select;
    }

    if(empty($params['enabled']))
      $select->where($rName.".enabled = 1");

    return $this->getAuthorisedSelect($select);
  }

  public function providerListTabs($params = array()) {

    $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');
    $providerTableName = $providerTable->info('name');

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
    $serviceTableName = $serviceTable->info('name');

    $select = $providerTable->select();

    if( !empty($params['approved']) ) {
      $select->where($providerTableName.".approved = ?", $params['approved']);
    }

    if( !empty($params['status']) ) {
      $select->where($providerTableName.".status = ?", $params['status']);
    }

    if($params['filter_type'] == "creation_date") {
      $select->order($providerTableName . "." . 'creation_date DESC');
    }  

    if(!empty($params['category_id']) && $params['category_id'] != -1) {

      $select
      ->setIntegrityCheck(false)
      ->from($providerTableName,array('*'))
      ->join($serviceTableName, "$providerTableName.pro_id = $serviceTableName.parent_id", array('category_id'));
      $select->group($providerTableName . '.pro_id');
      $select->where($serviceTableName . '.category_id = ?', $params['category_id']);
    }

    if (!empty($params['filter_type'])) {

        if(in_array($params['filter_type'], array("featured", "hot", "newlabel", "sponsored", "verified"))) {
            $select->where($providerTableName . ".".$params['filter_type'] ."= ?", 1);
        }

        if(in_array($params['filter_type'], array("rating", "review_count", "like_count", "comment_count"))) {

            $select->order(!empty($params['filter_type']) ? $providerTableName . "." . $params['filter_type'] . ' DESC' : $providerTableName . '.creation_date DESC');
        }

    } 
    $select->where($providerTableName.".enabled = 1");
    
    if(!empty($params['limit'])) {
      $select->limit($params['limit']);
    }


    return $select;
  }

  public function getItemOfDay() {

    $providerTableName = $this->info('name');

    $itemofthedaytable = Engine_Api::_()->getDbtable('itemofthedays', 'sitebooking');
    $itemofthedayName = $itemofthedaytable->info('name');

    $select = $this->select();
    $select = $select->setIntegrityCheck(false)
            ->from($providerTableName, array('*'))
            ->join($itemofthedayName, $providerTableName . ".pro_id = " . $itemofthedayName . '.resource_id', array('start_date'))
            ->where($itemofthedayName . '.resource_type=?', 'sitebooking_pro')
            ->where($itemofthedayName . '.start_date <=?', date('Y-m-d'))
            ->where($itemofthedayName . '.end_date >=?', date('Y-m-d'))
            ->order('RAND()');

    return $this->fetchRow($select);
  }

  public function getDayItems($title, $limit = 10) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('pro_id', 'owner_id', 'title', 'photo_id'));

    $select->where($this->info('name') . ".title LIKE ?", '%' . $title . '%')
            ->where('approved = ?', '1')
            ->where('enabled = ?', '1')
            ->where('status = ?', '1')
            ->order('title ASC')
            ->limit($limit);

    //FETCH RESULTS
    return $this->fetchAll($select);
  }
  
    public function getProId($id) {

    //MAKE QUERY
    $select = $this->select()
      ->from($this->info('name'), array('pro_id'));

    $select->where("owner_id = ?", $id);

    $pro_id = $select->query()->fetchColumn();

    //RETURN EVENTS COUNT
    return $pro_id;
  }

  public function getProviderTags($params = array()) {
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

    $sql = $tagMapsTableName.".resource_type = "."'sitebooking_pro'"." AND ".$tagMapsTableName.".tagger_type = '"."user"."' AND ".$tagMapsTableName.".tag_type = "."'core_tag'";
    $select->group($tagTableName.'.tag_id');
    $select->where($sql);
    if(!empty($params["limit"]))
      $select->limit($params["limit"]);

    return $select;  
  }
}