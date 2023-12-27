<?php

class Sitebooking_Widget_ProviderBrowseController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    // Prepare data
    if ($this->_getParam('isAjax')) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
      $this->getElement()->removeDecorator('');
      $this->view->isAjax = true;
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $values = $request->getParams();

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Sitebooking_Form_ServiceProvider_Search();
    
    $values['status'] = "1";
    $values['approved'] = "1";

    if(!empty($values['detectlocation']) && !empty($values['location']) && !empty($values['locationDistance'])) {
      $temp = json_decode($values['detectlocation'], true);
      $values['latitude'] = $temp['latitude'];
      $values['longitude'] = $temp['longitude'];
    }

    if (empty($values['location']) && !empty($values['locationDistance'])) {
      
      if (isset($values['city']) && !empty($values['city'])) {
        $values['location'].= $values['city'] . ',';
      } 
      
      if (isset($values['country']) && !empty($values['country'])) {
        $values['location'].= $values['country'];
      }
    }

    $this->view->assign($values);
   
    $this->view->formValues = array_filter($values);

    $this->view->params = $values;
    
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitebooking_pro')->getProvidersPaginator($values);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    $items_per_page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page',10);
    $this->view->paginator = $paginator = $paginator->setItemCountPerPage($items_per_page);

  }
}