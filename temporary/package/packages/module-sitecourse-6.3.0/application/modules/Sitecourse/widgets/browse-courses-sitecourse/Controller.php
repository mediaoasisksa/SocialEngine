<?php
class Sitecourse_Widget_BrowseCoursesSitecourseController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->itemPerPage = $itemPerPage = $this->_getParam('itemCount',10);
    $courseInfo = $this->_getParam('courseInfo','0');
    $this->view->textTrucationLimit  = $this->_getParam('truncationLimit','0');
    $viewer = Engine_Api::_()->user()->getViewer();


    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    //print_r($this->_getAllParams());die;
    // Get form
    $this->view->form = $form = new Sitecourse_Form_Search();
     
 
    if( !$viewer->getIdentity() ) {
      $form->removeElement('show');
    }
    // Process form
    $values = array('browse' => 1);
    if( $form->isValid($p) ) {
      $values = $form->getValues();
    }

    if($values['minprice'] > 0 && $values['maxprice'] > 0 ){
      if( $values['minprice'] > $values['maxprice']){
        return $form->addError("Max Price must be greater than Min price");
      }
    }
    // print_r($form->getValues());
    $this->view->formValues = array_filter($values);

    unset($values['show']);
 
    $values['is_ajax'] = true;
    $values['limit'] = $itemPerPage;
    $values['offest'] = 0;

    $this->view->pagination_params = $values;
    $this->view->course_info = $courseInfo;

  }
}
?>
