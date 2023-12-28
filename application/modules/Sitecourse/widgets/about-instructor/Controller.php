<?php
class Sitecourse_Widget_AboutInstructorController extends Seaocore_Content_Widget_Abstract
{
	public function indexAction()
	{
		$course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
		$course_id = $course_url ? Engine_Api::_()->sitecourse()->getCourseId($course_url) : Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id') ; 
		if (!$course_id) {
			return $this->setNoRender();
		}
		$aboutIns = Engine_Api::_()->getItemTable('sitecourse_course')->getAboutInstructor($course_id);
		
		if(!isset($aboutIns) || !$aboutIns){
			return $this->setNoRender();
		}
		$this->view->aboutIns = $aboutIns;
	}
}
?>
