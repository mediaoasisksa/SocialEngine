<?php

class Sitebooking_Widget_ServiceInfoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  
    // GET SUBJECT
    $this->view->sitebooking = $sitebooking = Engine_Api::_()->core()->getSubject();

    $resource_type = $sitebooking->getType();

    $resource_id = $sitebooking->getIdentity();

    $this->view->item = $item = Engine_Api::_()->getItem($resource_type, $resource_id);

    if (empty($item->profile_type)) {
      return $this->setNoRender();
    }

    //VIEW CODE TO SHOW THE PROFILE-MAPPING INFORMATION
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitebooking);

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $this->view->otherDetails = $view->fieldValueLoop($sitebooking, $this->view->fieldStructure);

    if(empty($this->view->otherDetails)) {
      return $this->setNoRender();
    }
    
    $params = $this->_getAllParams();
    $this->view->params = $params;
    $this->view->showContent = true;   
  
  }

}