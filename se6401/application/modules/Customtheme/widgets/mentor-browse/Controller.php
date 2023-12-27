<?php

class Customtheme_Widget_MentorBrowseController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->setNoRender();
    // Prepare data
    if ($this->_getParam('isAjax')) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
      $this->getElement()->removeDecorator('');
      $this->view->isAjax = true;
    }

    $this->view->flag = 0;

    // Make form
    // Note: this code is duplicated in the sitebooking.search-form widget
    $this->view->form = $form = new Sitebooking_Form_Service_Search();

    $request = Zend_Controller_Front::getInstance()->getRequest();
    // getting search values from url
    $values = $request->getParams();

    $values['status'] = "1";
    $values['approved'] = "1";
    $values['type'] = "2";
    if(!empty($values['detectlocation']) && !empty($values['location']) && !empty($values['locationDistance'] )){
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

    $customFieldValues = array_intersect_key($values, $form->getFieldElements());

    if(!empty($this->_getParam('first_level_category_id')))     
      $values['first_level_category_id'] = $this->_getParam('first_level_category_id');

    if(!empty($this->_getParam('second_level_category_id')))      
      $values['second_level_category_id'] = $this->_getParam('second_level_category_id');

    $this->view->params = $values;

    $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($values, $customFieldValues);

    $this->view->formValues = array_filter($values);

    $this->view->paginator = $paginator = Zend_Paginator::factory($sql);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
     
    $items_per_page = 100; //(int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page',100);

    $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);

  }
}