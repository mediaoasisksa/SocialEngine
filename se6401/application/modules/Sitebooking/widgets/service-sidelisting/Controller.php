<?php

class Sitebooking_Widget_ServiceSidelistingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params['filter_type'] = $this->_getParam('list_id', "featured");
    $params['limit'] = $this->_getParam('limit', 5);
    $params['approved'] = "1";
    $params['status'] = "1";

    $serviceTable = Engine_Api::_()->getDbtable('sers', 'sitebooking');

    $select = $serviceTable->serviceListTabs($params);

    $this->view->paginator = $paginator = $serviceTable->fetchAll($select);

    //DONT RENDER IF COUNT ZERO
    if (count($paginator) <= 0) {
      return $this->setNoRender();
    } 

  }

}