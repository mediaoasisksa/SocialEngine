<?php
class Sitecourse_Widget_CourseOptionsController extends Seaocore_Content_Widget_Abstract
{
	public function indexAction(){
		$course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
		$course_id = $course_url ? Engine_Api::_()->sitecourse()->getCourseId($course_url) : Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id') ; 


		if (!$course_id) {
			return $this->setNoRender();
		}
        // get course
		$this->view->course =$course = Engine_Api::_()->getItem('sitecourse_course',$course_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		$this->view->is_owner = ($viewer_id == $course['owner_id']) ? true : false;
		$this->view->course_id = $course_id;
		$this->view->resource_id = $course_id;
		$this->view->resource_type = "sitecourse_course";

    	// get buyers table and buyers count
		$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		$this->view->buyersCount = $buyersCount = $buyerdetailTable->courseEnrollementCount($course_id);

    	// get course images
		$getContentImages = Engine_Api::_()->sitecourse()->getContentImage($course);
		$this->view->courseImg = $getContentImages['image_profile'];

		$this->view->canReport = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.report', 0);

		$deletePermission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'delete');
		if($buyersCount) {
			$deletePermission = 0;
		}
		$this->view->deletePermission = $deletePermission;
		

	}

}

?>
