<?php
class Sitecourse_Widget_CourseLearningCurriculumController extends Seaocore_Content_Widget_Abstract
{
    public function indexAction()
    {   
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->course_id= $course_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id');
        $this->view->viewer_id = $viewer->getIdentity();

        if (!$course_id) {
            return $this->setNoRender();
        }

        $course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
        $this->view->owner_id = $course['owner_id'];

        $this->view->topics = $topics = Engine_Api::_()->getDbtable('topics','sitecourse')->fetchAllTopics($course_id);
        
        $lessons = Engine_Api::_()->getDbtable('lessons','sitecourse')->fetchAllLessons($course_id);
        $this->view->lessonCount = count($lessons);


        $completedLessons = Engine_Api::_()->getDbtable('completedlessons','sitecourse')->fetchCompletedLessons($course_id,$viewer->getIdentity());

        $this->view->completedLessonCount = count($completedLessons);
        
        $completedLessonsIds = array();
        foreach($completedLessons as $key => $lesson){
            $completedLessonsIds[$lesson['lesson_id']] = true;
        }
        $this->view->completedLessons = $completedLessonsIds;                      
        $course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
        $this->view->topics = $topics = Engine_Api::_()->getDbtable('topics','sitecourse')->fetchAllTopics($course_id);
        $lessons = Engine_Api::_()->getDbtable('lessons','sitecourse')->fetchAllLessons($course_id);
        $this->view->lessons = $lessons;

        $this->view->issuePermission = $issuePermission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'certification');

        $this->view->issuedCertificate = $issuedCertificate = Engine_Api::_()->getDbtable('completedcourses','sitecourse')->checkIssuedCertificate($course_id);
    }
}
?>
