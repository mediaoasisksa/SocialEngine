<?php
class Sitecourse_Widget_CourseProfilePhotoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction(){
       $course_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
      $course_id = Engine_Api::_()->sitecourse()->getCourseId($course_url);

        // get the course 
        $course = Engine_Api::_()->getItem('sitecourse_course',$course_id);
        // get the storage file
        $storageFile = Engine_Api::_()->getItem('storage_file',$course['photo_id']);

        $this->view->image_src = $storageFile['storage_path'];
    }




}

?>
