<?php
class Sitecourse_Widget_CourseReviewController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{	
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		$course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
		$course_id = Engine_Api::_()->sitecourse()->getCourseId($course_url);

		if(!$course_id){
			return $this->setNoRender();
		}
		$this->view->course_id = $course_id;
		$this->view->viewer_id = $viewer_id;
		if($viewer_id) {
		$this->view->levelId = $viewer->level_id;
}
	$ratingTable = Engine_Api::_()->getDbtable('reviews', 'sitecourse');
		$this->view->rated = $rated = $ratingTable->checkRated(array('course_id' => $course_id,'viewer_id'=> $viewer_id));

		$this->view->rating_count = $rating_count = $ratingTable->ratingCount(array('course_id' => $course_id));
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		$this->view->course_rating = $course_rating = $course['rating'];
		if($viewer_id) {
		$this->view->delPermission = $delPermission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'review_deletion');
		}
	$this->view->reviews_count = $reviews_count = Engine_Api::_()->getDbtable('reviews','sitecourse')->reviewsCount($course_id);
	}
}
?>
