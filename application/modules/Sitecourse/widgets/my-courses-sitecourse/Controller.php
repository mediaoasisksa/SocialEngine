<?php
class Sitecourse_Widget_MyCoursesSitecourseController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();

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

    $this->view->formValues = array_filter($values);
    $values['user_id'] = $viewer_id = $viewer->getIdentity();

    $items_per_page = 6;
    $values['is_ajax'] = true;
    $values['limit'] = $this->view->itemPerPage = $items_per_page;
    $values['offest'] = 0;
    // send ajax request data
    $this->view->pagination_params = $values;    

    /**
     * get favorite courses
     */
    $favoriteCourses = Engine_Api::_()->getItemTable('sitecourse_favourite')
    ->getFavouriteCourses($viewer->getIdentity());
    
    $favCourses = array();
    foreach($favoriteCourses as $key => $favourites){
      $favCourses[$favourites['course_id']] = $favourites['course_id'];
    }
    $this->view->favouriteCourses = $favCourses;

    $this->view->course_info = array( 0 => "postedBy", 1 => "enrolledCount", 2 => "category", 3 => "difficultyLevel" );
  }

}
?>
