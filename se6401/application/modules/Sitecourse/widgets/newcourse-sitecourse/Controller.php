<?php
class Sitecourse_Widget_NewcourseSitecourseController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
      //WHO CAN CREATE STORE
    $menusClass = new Sitecourse_Plugin_Menus();
    $this->view->canCreateCourses = $menusClass->canCreateSitecourse();
    $this->view->canCreate = 1;
    if(!$this->view->canCreateCourses ) {
      $this->view->canCreate = 0;
    }             
  }
}

?>
