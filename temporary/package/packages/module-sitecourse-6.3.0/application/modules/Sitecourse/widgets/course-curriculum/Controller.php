<?php
class Sitecourse_Widget_CourseCurriculumController extends Seaocore_Content_Widget_Abstract
{
	public function indexAction()
	{ 
		$viewer = Engine_Api::_()->user()->getViewer();
		$course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
		$course_id = Engine_Api::_()->sitecourse()->getCourseId($course_url);

		if (!$course_id) {
			return $this->setNoRender();
		}

		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		$this->view->topics = $topics = Engine_Api::_()->getDbtable('topics','sitecourse')->fetchAllTopics($course_id);
		$lessons = Engine_Api::_()->getDbtable('lessons','sitecourse')->fetchAllLessons($course_id);
		$topicLessonsMap = array();
		foreach($lessons as $lesson) {
			$id = $lesson['topic_id'];
			if(!array_key_exists($id, $topicLessonsMap)) {
				$topicLessonsMap[$id] = array();
			}
			$topicLessonsMap[$id][] = $lesson;
		}
		$this->view->lessons= $topicLessonsMap;

    	// topics or lessons are not available
		if(!count($topics) || !count($lessons)){
			return $this->setNoRender();
		}
	}
}
?>
