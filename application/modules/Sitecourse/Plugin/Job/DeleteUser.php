<?php

class Sitecourse_Plugin_Job_DeleteUser extends Core_Plugin_Job_Abstract {

    protected function _execute() {
        // Get job and params
        $job = $this->getJob();
        
        // No owner id?
        if (
            !($owner_id = $this->getParam('owner_id'))) {
            $this->_setState('failed', 'No Owner identity provided.');
            $this->_setWasIdle();
            return;
        }
        // No Admin id?
        if (
            !($user_id = $this->getParam('user_id'))) {
            $this->_setState('failed', 'No Admin identity provided.');
            $this->_setWasIdle();
            return;
        }

         // Process
        try {
            $this->_process($owner_id , $user_id);
            $this->_setIsComplete(true);
        } catch (Exception $e) {
            $this->_setState('failed', 'Exception: ' . $e->getMessage());
        }
    }

    protected function _process($owner_id , $user_id) {
        //user_id = admin 
        //owner_id = current owner(user which is being deleted )

        $courseTable = Engine_Api::_()->getDbtable('courses', 'sitecourse');

        $disapprovedCourses = $courseTable->select()->where('owner_id =?',$owner_id)->where('approved !=?',1)->query()->fetchAll(); 
        

        foreach($disapprovedCourses as $course){
            Engine_Api::_()->sitecourse()->deleteCourse($course['course_id']);
        }

        $courseSelect = $courseTable->update(array('owner_id' => $user_id),array('owner_id =?' => $owner_id)); 

        $videosTable = Engine_Api::_()->getDbtable('videos', 'sitecourse');
        $select = $videosTable->update(array('owner_id' => $user_id),array('owner_id =?' => $owner_id)); 
       
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecourse');
        $select = $transactionsTable->update(array('user_id' => $user_id),array('user_id =?' => $owner_id)); 
 
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'sitecourse');
        $select = $ordersTable->update(array('user_id'=>  $user_id),array('user_id =?' => $owner_id)); 

        $buyerdetailsTable = Engine_Api::_()->getDbtable('buyerdetails', 'sitecourse');
        $select = $buyerdetailsTable->update(array('buyer_id' => $user_id),array('buyer_id =?'=> $owner_id)); 

        $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
        $select = $storageTable->update(array('user_id' => $user_id),array('user_id =?' => $owner_id)); 

        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitecourse');
        $reviewTable->delete(array('user_id =?'=>$owner_id));
        
        $reviewLike = Engine_Api::_()->getDbTable('reviewlikes','sitecourse');
        $reviewLike->delete(array('user_id =?'=>$owner_id));        

        //delete reports
        $reportTable = Engine_Api::_()->getDbtable('reports', 'sitecourse');
        $reportTable->delete(array('reporter_id =?'=>$owner_id));

        //favourite delete
        $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'sitecourse');
        $favouriteTable->delete(array('owner_id =?'=>$owner_id));

        // completed lessons delete
        $completedlessonTable = Engine_Api::_()->getDbtable('completedlessons', 'sitecourse');
        $completedlessonTable->delete(array('user_id =?'=>$owner_id));

         // completed course entry delete
        $completedcourseTable = Engine_Api::_()->getDbtable('completedcourses', 'sitecourse');
        $completedcourseTable->delete(array('user_id =?'=>$owner_id));
    }

}
?>
