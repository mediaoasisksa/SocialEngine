<?php

class Sitebooking_Widget_ProviderSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    $combine = $module.$controller.$action;
    $this->view->compare = "";
  
    // Make form
    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Search();
    $form->setAction($this->view->url(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'index')));
    
    if( !$viewer->getIdentity() ) {
      $form->removeElement('show');
    }

    // Process form
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $form->isValid($p);
    $values = $form->getValues();
    $this->view->formValues = array_filter($values);
    $values['status'] = "1";
    
    $this->view->assign($values);
  
  }

}


?>