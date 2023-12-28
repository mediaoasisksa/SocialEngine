<?php

class Sitecourse_Widget_CourseOwnerInfoController extends Seaocore_Content_Widget_Abstract 
{

	public function indexAction(){
		$course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
		$course_id = Engine_Api::_()->sitecourse()->getCourseId($course_url);

		$course = Engine_Api::_()->getItem('sitecourse_course',$course_id);
        // if no course found not render the widget.
		if(!$course && empty($course)){
			return $this->setNoRender();
		}
        //send only required info
		$viewer = Engine_Api::_()->getItem('user',$course['owner_id']);
		if($viewer && !empty($viewer)){
			$this->view->ownerInfo =$info= array('name'=>$viewer['displayname'],
				'email'=>$viewer['email']);
			if(!isset($info) || !$info){
				return $this->setNoRender();
			}
		}
		$this->view->owner = $owner = $course->getOwner();
		if(!empty($owner->photo_id)) {
			$getContentImages = Engine_Api::_()->sitecourse()->getContentImage($course, true);
			$ownerImg = $getContentImages['owner_image_icon'];     
		}
		$this->view->ownerImg = $ownerImg ?: "application/modules/User/externals/images/nophoto_user_thumb_profile.png";
		$this->view->ownerCourseNumber = $ownerCourseNumber = Engine_Api::_()->getItemTable('sitecourse_course')->getApprovedCourseCount($course['owner_id']);
	}

}
?>
