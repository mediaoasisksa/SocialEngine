<?php

class Sitebooking_Widget_ServiceCarouselController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $values = array();

    $values['limit'] = $this->_getParam('limit', 10);
    $values['status'] = "1";
    $values['approved'] = "1";

    $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($values);

    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitebooking_ser')->fetchAll($sql);
    
    if(!count($paginator)){
      return $this->setNoRender();
    }
  }
}

?>