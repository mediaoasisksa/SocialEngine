<?php

class Sitebooking_Widget_ServiceTagController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $limit = $this->_getParam('limit', 5);

    $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');

    $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');

    $params["limit"] = $limit;

    $select = $serviceTable->getServiceTags($params);

    $this->view->tagData = $tagTable->fetchAll($select);

    if(count($this->view->tagData) <= 0) {
      return $this->setNoRender();
    } 
  }
}