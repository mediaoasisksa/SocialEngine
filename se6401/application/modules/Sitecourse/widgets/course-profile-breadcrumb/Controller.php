<?php
class Sitecourse_Widget_CourseProfileBreadcrumbController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

   $course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
      $course_id = Engine_Api::_()->sitecourse()->getCourseId($course_url);

    // course id is not present
    if (!$course_id) {
      return $this->setNoRender();
    }
    //GET SUBJECT
    $sitecourse = Engine_Api::_()->getItem('sitecourse_course',$course_id);
    // valid sitecourse   
    if(!$sitecourse || !$sitecourse->getIdentity()){
      return $this->setNoRender();
    }
    $this->view->sitecourse = $sitecourse;
    //GET CATEGORY TABLE
    $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'sitecourse');
    
    if (!empty($sitecourse->category_id)) {
      $this->view->category_name = $category_name =$this->view->tableCategory->getCategory($sitecourse->category_id)->category_name;

      if (!empty($sitecourse->subcategory_id)) {
        $this->view->subcategory_name = $this->view->tableCategory->getCategory($sitecourse->subcategory_id)->category_name;

      }
    }
  }

}
