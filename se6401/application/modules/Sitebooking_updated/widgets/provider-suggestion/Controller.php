<?php

class Sitebooking_Widget_ProviderSuggestionController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_pro')) {
      return $this->setNoRender();
    }

    $limit = $this->_getParam('limit', 5);

    $provider = Engine_Api::_()->core()->getSubject('sitebooking_pro');
    $pro_id = $provider->getIdentity();

    $service = Engine_Api::_()->getItemTable('sitebooking_ser');
    $categories_id = $service->fetchAll($service->select()->where('parent_id = ?',$pro_id))->toArray();

    foreach ($categories_id as $key => $value) {
    	$id[] = $value['category_id'];
    }
    if(!empty($id)){
      $params['categories_id'] = implode(",", $id);
      $params['viewedpro_id'] = $pro_id;
    }

    $table = Engine_Api::_()->getDbtable('sers', 'sitebooking');
    $rName = $table->info('name');
    $prTable = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $prName = $prTable->info('name');

    $suggestedProviders = array();
    if( !empty($params['categories_id']) && !empty($params['viewedpro_id']) ) { 
      $select = $table->select();
      $select
        ->setIntegrityCheck(false)
        ->from($rName, array('title as service_title'))
        ->join($prName, "$rName.parent_id = $prName.pro_id", array('*'));

      $sql = $rName.".category_id IN (".$params['categories_id'].") OR ".$rName.".first_level_category_id IN (".$params['categories_id'].") OR ".$rName.".second_level_category_id IN (".$params['categories_id'].")";

      $select->group($prName.'.pro_id');
      $select->where($sql);
      $select->where($prName.'.pro_id != ?',$params['viewedpro_id']);
      $select->where($prName.".approved = 1");
      $select->where($prName.".status = 1");
      $select->where($prName.".enabled = 1");
      $select->where($prName.".status = 1");
      $select->limit($limit);

      $this->view->suggestedProviders = $suggestedProviders = $table->fetchAll($select);      
    }

    if(count($suggestedProviders) <= 0) {
      return $this->setNoRender();
    }
	}
}


?>