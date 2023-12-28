<?php
class Sitecourse_Widget_CourseRequirementsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
     $course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
     $course_id = $course_url ? Engine_Api::_()->sitecourse()->getCourseId($course_url) : Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id') ; 

     if(!$course_id && empty($course)){
            return $this->setNoRender();
        }
    $courseReq = Engine_Api::_()->getItemTable('sitecourse_course')->getCourseRequirements($course_id);
    // course requirements are not added
    if(!isset($courseReq) || !$courseReq){
      return $this->setNoRender();
    }
    $this->view->courseReq = $courseReq;
  }
}
?>
