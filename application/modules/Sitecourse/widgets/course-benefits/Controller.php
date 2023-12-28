<?php
class Sitecourse_Widget_CourseBenefitsController extends Seaocore_Content_Widget_Abstract
{
  public function indexAction()
  {
    $course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
     $course_id = $course_url ? Engine_Api::_()->sitecourse()->getCourseId($course_url) : Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id') ; 


    if (!$course_id) {
      return $this->setNoRender();
    }

    $courseBenefits = Engine_Api::_()->getItemTable('sitecourse_course')->getCourseBenefits($course_id);

    if(empty($courseBenefits)) {
      return $this->setNoRender();
    }

    $this->view->course_id = $course_id;
    $this->view->courseBenefits = $courseBenefits;
  }
}
?>
