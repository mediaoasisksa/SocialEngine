<?php

class Sitecourse_Widget_FullwidthSliderCoursesController extends Seaocore_Content_Widget_Abstract {

	public function indexAction(){
		$itemCount = $this->_getParam('itemCount',10);
		// fetch only given courses
		$coursesCriteria = $this->_getParam('coursesCriteria',0);
		// course details that will be visible
		$courseInfo = $this->_getParam('courseInfo');
		// course title
		$title = $this->_getParam('title');
		// truncation limit for title text
		$textTrucationLimit = $this->_getParam('truncationLimit');
		// height of the slider
		$height = $this->_getParam('sliderHieght');
		// threshold for newest course in no of days.
		$newestThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.latest.threshold', '5');


		$courses = Engine_Api::_()->getDbtable('courses','sitecourse')->getFullSliderCourses($itemCount, $coursesCriteria, $newestThreshold);
		
		// if no course found
		if(empty($courses)){
			return $this->setNoRender();
		}
		// pass data to view
		$this->view->courses = $coursesData;
		$this->view->height = $height;
		$this->view->enrolled_count = false;
		$this->view->view_count = false;
		$this->view->course_owner = false;
		$this->view->course_difficulty = false;
		$this->view->course_category = false;
		foreach($courseInfo as $info){
			switch($info){
				case 'creationDate':
				$this->view->creation_date = true;
				break;
				case 'difficultyLevel':
				$this->view->course_difficulty = true;
				break;
				case 'postedBy':
				$this->view->owner_name = true;
				break;
				case 'enrolledCount':
				$this->view->enrolled_count = true;
				break;
				case 'category':
				$this->view->course_category = true;
				break;
			}
		}

		$this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
		$difficulty_levels = array(
			0 => 'Beginner',
			1 => 'Intermediate',
			2 => 'Expert'
		);
			// modify display data
		foreach($courses as $key => $course) {
			$owner = Engine_Api::_()->getItem('user',$course['owner_id']);
			$owner_name = '';
			if(!empty($owner)) {
				$owner_name = $owner['displayname'] ?? '';
			}
			$courses[$key]['owner_name'] = sprintf("<a href='%s' > %s </a>", $owner->getHref(), $owner->getTitle());
			$courses[$key]['creation_date'] = $this->view->timestamp(strtotime($course['creation_date']));
			$courses[$key]['modified_date'] = $this->view->timestamp(strtotime($course['modified_date']));
			$courses[$key]['link'] = $this->view->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),substr($course['title'],0,$textTrucationLimit).'...',array());
			$storage_file = Engine_Api::_()->getItem('storage_file', $course['photo_id']);
			$src='';
			if(!empty($storage_file)){
				$src=$storage_file->map(); 
			}
			$courses[$key]['img_src'] =  $src;

				// get buyers table
			$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
			$courses[$key]['buyers_count'] = $buyerdetailTable->courseEnrollementCount($course['course_id']);
			$courses[$key]['percentage'] = '0%';
			if(!empty($pagination_params['buyer_id'])) {

					// find courses completion percentage
				$totalLessonCnt = Engine_Api::_()->getDbtable('lessons', 'sitecourse')
				->getLessonsCount($course['course_id']);
				$completeLessonCnt = Engine_Api::_()->getDbtable('completedlessons','sitecourse')->getCompletedLessonsCount($course['course_id'], $pagination_params['buyer_id']);
				if(!empty($totalLessonCnt)) {
					$percentage = ($completeLessonCnt / $totalLessonCnt) * 100;
					$courses[$key]['percentage'] = $percentage.'%';
				}

			}
				// get category 
			$category = Engine_Api::_()->getItem('sitecourse_category', $course['category_id']);
			$courses[$key]['category_name'] = $category['category_name'];	
			$courses[$key]['difficulty_name'] = $difficulty_levels[$course['difficulty_level']];
		}
		$this->view->courses = $courses;


	}


}


?>
