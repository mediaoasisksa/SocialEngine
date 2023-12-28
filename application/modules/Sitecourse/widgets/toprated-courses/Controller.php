<?php 

class Sitecourse_Widget_TopratedCoursesController extends Seaocore_Content_Widget_Abstract {

    public function indexAction(){
        $itemCount = $this->_getParam('itemCount',2);
        $sortingCriteria = $this->_getParam('sortingCriteria',1);
        $courseInfo = $this->_getParam('courseInfo');
        $title = $this->_getParam('title');
        $topRatedThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.mostrated.threshold', '4');

        $courses = Engine_Api::_()->getDbtable('courses','sitecourse')->getTopratedCourses($itemCount,$sortingCriteria,$topRatedThreshold);
        if(empty($courses)) {
            return $this->setNoRender();
        }
        
        $this->view->course_info = $courseInfo;
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
            $courses[$key]['link'] = $this->view->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$course['title'],array());
            $courses[$key]['toprated'] = Engine_Api::_()->getDbtable('courses','sitecourse')->isTopRatedCourse($course['course_id']);
                $courses[$key]['newest'] = Engine_Api::_()->getDbtable('courses','sitecourse')->isNewestCourse($course['course_id']);
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
            $courses[$key]['share_url'] = $this->view->url( array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => 'sitecourse_course', 'id' => $course['course_id'],'format' => 'smoothbox'), 'default', array('class' =>'smoothbox'));
        }
        $this->view->courses = $courses;
    }
}

?>

