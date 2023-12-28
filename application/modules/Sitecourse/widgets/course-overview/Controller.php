<?php

class Sitecourse_Widget_CourseOverviewController extends Seaocore_Content_Widget_Abstract {

	public function indexAction(){

		$course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
		$course_id = $course_url ? Engine_Api::_()->sitecourse()->getCourseId($course_url) : Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id') ; 
		
		if (!$course_id) {
			return $this->setNoRender();
		}
		
		$overview = Engine_Api::_()->getItemTable('sitecourse_course')->getCourseOverview($course_id);

		if(empty($overview)) {
			return $this->setNoRender();
		}

		$this->view->overview = $overview;
	}

}
?>
