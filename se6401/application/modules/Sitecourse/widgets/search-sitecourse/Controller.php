<?php
class Sitecourse_Widget_SearchSitecourseController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();

    //FORM CREATION
    $this->view->viewType = $viewType=$this->_getParam('viewType', 'vertical');

    $widgetSettings = array(
      'viewType' => $this->view->viewType
    );
    $request = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $category_id = (isset($request['category_id']))?$request['category_id']:null;
    // print_r($category_id);die;
    $this->view->form = $form = new Sitecourse_Form_Search(array('type' => 'sitecourse_course', 'widgetSettings' => $widgetSettings,'category'=>$category_id));
    $form->isValid($request);
    $values = $form->getValues();

    if($values['minprice'] > 0 && $values['maxprice'] > 0 ){
      if( $values['minprice'] > $values['maxprice']){
        return $form->addError("Max Price must be greater than Min price");
      }
    }

    $this->view->formValues = array_filter($values);

    if (!$viewer) {
      $form->removeElement('show');
    }

    $this->view->assign($values);
    //  $form->tag->setValue("");
  }

}
