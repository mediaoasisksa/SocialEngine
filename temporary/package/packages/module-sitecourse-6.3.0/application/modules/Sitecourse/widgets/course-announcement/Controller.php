<?php

class Sitecourse_Widget_CourseAnnouncementController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
     $course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
      $course_id = $course_url ? Engine_Api::_()->sitecourse()->getCourseId($course_url) : Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id') ; 

     if (!$course_id) {
      return $this->setNoRender();
    }

    $this->view->announcements = $announcements = Engine_Api::_()->getItemTable('sitecourse_announcement')->getActiveAnnouncements($course_id);
    
    // no announcements found
    if(!count($announcements)) return $this->setNoRender();
  }
}

?>
