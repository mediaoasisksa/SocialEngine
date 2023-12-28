<?php

class Sitebooking_Widget_ProviderSidelistingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params['filter_type'] = $this->_getParam('list_id', "featured");
    $params['limit'] = $this->_getParam('limit', 5);
    $params['approved'] = "1";
    $params['status'] = "1";

    $providerTable = Engine_Api::_()->getDbtable('pros', 'sitebooking');

    $select = $providerTable->providerListTabs($params);

    $this->view->paginator = $paginator = $providerTable->fetchAll($select);

    //DONT RENDER IF COUNT ZERO
    if (count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}