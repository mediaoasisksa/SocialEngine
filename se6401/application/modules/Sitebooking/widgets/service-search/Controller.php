<?php

class Sitebooking_Widget_ServiceSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Make form
    $this->view->form = $form = new Sitebooking_Form_Service_Search();
    $form->setAction($this->view->url(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service', 'action' => 'index')));
    
    $form->removeElement('status');
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
