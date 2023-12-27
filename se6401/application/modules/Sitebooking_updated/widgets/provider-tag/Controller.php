<?php

class Sitebooking_Widget_ProviderTagController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $limit = $this->_getParam('limit', 5);

    $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');

    $providerTable = Engine_Api::_()->getItemTable('sitebooking_pro');

    $params["limit"] = $limit;

    $select = $providerTable->getProviderTags($params);

    $this->view->tagData = $tagTable->fetchAll($select);

    if(count($this->view->tagData) <= 0){
      return $this->setNoRender();
    } 

  }
}