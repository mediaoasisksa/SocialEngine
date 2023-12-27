<?php

class Sitebooking_Widget_ProviderCarouselController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');
    $limit = $this->_getParam('limit',10);
    $providerTableName = $providerTable->info('name');

    $select = $providerTable->select();
    $select->where($providerTableName.".approved = 1");
    $select->where($providerTableName.".enabled = 1");
    $select->where($providerTableName.".status = 1");
    $select->order($providerTableName . '.creation_date DESC');
    $select->limit($limit);

    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitebooking_pro')->fetchAll($select);
    
    if(count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}

?>