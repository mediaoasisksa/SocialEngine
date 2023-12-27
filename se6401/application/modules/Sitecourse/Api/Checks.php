<?php 
class Sitecourse_Api_Checks {

    public $_viewerType = array('owner','buyer');

    /**
     * @return null || course item
     */
    private function validCourse($course_id = null) {
        if(!$course_id) return null;
        // get course item
        $course = Engine_Api::_()->getItem('sitecourse_course',$course_id);
        // no course found
        if(!$course || !$course->getIdentity()) return null;

        return $course;
    }

    /**
     * @param {int} course id
     * @return {array} course status array
     */
    public function courseStatus($course_id = null) {
        $course = $this->validCourse($course_id);
        if(!$course) return null;
        // publish and enrollement disable status
        $publishStatus = (!$course['draft'] && $course['approved'] == 1);
        $disableStatus = $course['disable_enrollment'];

        return array('publishStatus' => $publishStatus, 
            'disableStatus' => $disableStatus);
    }

    /**
     * @return viewer type ('owner','admin','buyer','')
     */
    public function viewerType($course_id = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $course = $this->validCourse($course_id);
        if(!$viewer || !$course) return null;
        $type = '';
        if($course->isOwner($viewer)) $type = 'owner';
        else if(Engine_Api::_()->getItemTable('sitecourse_buyerdetail')->validatePurchase($course_id,$viewer->getIdentity())) $type = 'buyer';

        return $type;
    }

    /**
     * 
     * course owner/superadmin/buyer -> can view profile page in all conditions
     * course viewer -> can only view the course in case of memebr level has permission
     * @param {int} course id
     * @return {boolean} viewer have permission to view profile page
     * 
     */
    public function _canViewProfilePage($course_id = null, $helper = null) {
        $courseStatus = $this->courseStatus($course_id);
        if(!$courseStatus) return false;

        $type = $this->viewerType($course_id);

        if(in_array($type,$this->_viewerType)) return true;

        // course is published and viewer has the permission to view
        if($courseStatus['publishStatus']) {
            return true;
        }

        return false;
    }

    /**
     * @return {boolean} permission to buy the course
     */
    public function _canBuyCourse($course_id = null) {
        $viewer = Engine_Api::_()->user()->getViewer(); 
        $courseStatus = $this->courseStatus($course_id);

        if(!$courseStatus) return false;
        $type = $this->viewerType($course_id);

        if(in_array($type,$this->_viewerType)) return false;         
        // course is not published or disabled
        if(
            !$courseStatus['publishStatus'] || 
            $courseStatus['disableStatus']
        ) {
            return false;       
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
        // get global setting
        $enrollmentCount = Engine_Api::_()->authorization()->getPermission($course->getOwner()->level_id, 'sitecourse_course', 'max_enrollment');
        // get buyer counts
        $buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
        $buyersCount = $buyerdetailTable->courseEnrollementCount($course_id);
        if(
            $enrollmentCount > 0 && 
            $buyersCount >= $enrollmentCount

        ) { 

            return false;
        }

        return true;
    }

    /**
     * @return {boolean} permission to visit course learning page
     */
    public function _canViewLearningPage($course_id = null) {
        if($this->_canBuyCourse($course_id)) return false;
        $type = $this->viewerType($course_id);

        return in_array($type,$this->_viewerType);
    }

    /**
     * @return {boolean} permission to rate a course
     */
    public function _canRate($course_id){
       $course = $this->validCourse($course_id);
       $viewer = Engine_Api::_()->user()->getViewer();

       if($course->isOwner($viewer)) return false;

       $canRate = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'reviews_ratings');
      
       return $canRate;
   }
}
?>
