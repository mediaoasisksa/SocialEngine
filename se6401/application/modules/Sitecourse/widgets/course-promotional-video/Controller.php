<?php
class Sitecourse_Widget_CoursePromotionalVideoController extends Seaocore_Content_Widget_Abstract {

  public function indexAction(){
    $course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
    $this->view->course_id = $course_id = Engine_Api::_()->sitecourse()->getCourseId($course_url);


    // get the course 
    $course = Engine_Api::_()->getItem('sitecourse_course',$course_id);
    if(!$course && empty($course)){
      return $this->setNoRender();
    }

    $courseTable = Engine_Api::_()->getDbtable('courses','sitecourse');
    $tag_cloud_array = $courseTable->getCourseTags($itemCount,$alphabetical,$course_id);

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->course = $course;
    $courseLevels = array(0 => 'Beginner', 1 => 'Intermediate', 2 => 'Expert');
    $video = Engine_Api::_()->getItemTable('sitecourse_video')->getVideoItem($course['course_id'],'course'); 

    if(!isset($video) || !$video){
      return $this->setNoRender();
    }

    $this->view->video_url = $video_url = Engine_Api::_()->sitecourse()->getVideoURL($video,false);
    $this->view->video = $video; 
    // get buyers table
    $buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
    $this->view->buyersCount = $buyerdetailTable->courseEnrollementCount($course['course_id']); 

    $this->view->difficultyLevel = $courseLevels[$course['difficulty_level']]; 
     $category = Engine_Api::_()->getItem('sitecourse_category', $course['category_id']);
    $this->view->category_name = $category_name = $category['category_name'];
    // get owner images
    $owner = $course->getOwner();
    if(!empty($owner->photo_id)) {
      $getContentImages = Engine_Api::_()->sitecourse()->getContentImage($course, true);
      $ownerImg = $getContentImages['owner_image_icon'];     
    }
    $this->view->ownerImg = $ownerImg ?: "application/modules/User/externals/images/nophoto_user_thumb_profile.png";
    // get badges

    $coursesTable = Engine_Api::_()->getDbtable('courses', 'sitecourse');

    $this->view->isTopRated = $coursesTable->isTopRatedCourse($course['course_id']);
    $this->view->isNewest = $coursesTable->isNewestCourse($course['course_id']);

    $checks = new Sitecourse_Api_Checks();
    $this->view->canBuy = $checks->_canBuyCourse($course_id);
    $this->view->isPurchased = $checks->_canViewLearningPage($course_id);

    // favourite courses db table
    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'sitecourse');
    $this->view->favText = $isFav = $favouriteTable->isFavourite($course['course_id'], $viewer->getIdentity());
    

     $tag_array = array();
    $tag_id_array = array();
    foreach ($tag_cloud_array as $values) {
      $tag_array[$values['text']] = $values['Frequency'];
      $tag_id_array[$values['text']] = $values['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;
      if ($spread == 0) {
        $spread = 1;
      }
      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
    
  }


}
