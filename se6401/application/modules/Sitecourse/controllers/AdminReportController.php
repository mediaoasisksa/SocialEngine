<?php
class Sitecourse_AdminReportController extends Core_Controller_Action_Admin {
	
	public function indexAction(){
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_reports');
		$reportsTable = Engine_Api::_()->getDbtable('reports','sitecourse');
		$this->view->formFilter = $formFilter = new Sitecourse_Form_Admin_Manage_Filter();
		$params = array('page' => $page);

		if($this->getRequest()->isPost()){
			$postValues = $this->getRequest()->getPost();
			$params['order'] = $postValues['order'];
			$params['order_direction'] = $postValues['order_direction'];
			$this->view->order = $postValues['order'];
			$this->view->order_direction = $postValues['order_direction'];
		}
		$page = $this->_getParam('page', 1);
		$this->view->paginator = $reportsTable->getReportsPaginator($params);
	}

	public function deleteAction(){
		$report_id = $this->_getParam('id');
		$this->view->count = 1;
		if(!$this->getRequest()->isPost()){
			return;
		}
		$postValues = $this->getRequest()->getPost();
		$itemTable = Engine_Api::_()->getItemTable('sitecourse_report');
		$item = $itemTable->find($report_id)->current();
		$db = $itemTable->getAdapter();
		$db->beginTransaction();

		try{
			$item->delete();
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			throw $e;
		}
        //redirect to reports page
		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 50,
			'parentRefresh'=> 50,
			'messages' => array('Action taken successfully.')
		));
	}
	// delete selected reports
	public function deleteselectedAction(){
		if(!$this->getRequest()->isPost()){
			return;
		}

		$postData = $this->getRequest()->getPost();
        // sperate ids 
		$ids = explode(',',$postData['ids']);
		$this->view->count = count($ids);
		$this->view->ids = $postData['ids'];
        // check confirm is clicked with value yes
		if(!isset($postData['confirm']) || $postData['confirm'] != 'yes'){
			$this->renderScript('admin-report/delete.tpl');
			return;
		}
		$itemTable = Engine_Api::_()->getItemTable('sitecourse_report');
		$items = array();
		$db = $itemTable->getAdapter();
		$db->beginTransaction();

		try{
            // insert report item in items array;
			foreach($ids as $id){
				$item = $itemTable->find(intval($id))->current();
                // check whether a valid item or not
				if($item)
					$items[] = $item;
			}
            // delete items 
			foreach($items as $item) $item->delete();
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			throw $e;
		}
        //redirect to reports page
		return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
	}


	public function reportactionAction(){
		$report_id = $this->_getParam('report_id', null);
		$course_id = $this->_getParam('course_id', null);
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		// course is not persent
		if(empty($course)) {
			return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 50,
				'parentRefresh'=> 50,
				'messages' => array('Course Not Found.')
			));
		}
		$owner = Engine_Api::_()->getItem("user" , $course->owner_id);
		$viewer = Engine_Api::_()->user()->getViewer();

		$this->view->form = $form = new Sitecourse_Form_Admin_ReportAction();
		$multiOptions =  array(
			'0' => 'Delete Course',
			'1' => 'Disapprove Course',
			'2' => 'Disable Future Enrollments'
		);
		// get buyers table
		$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		/**
		 * get buyers count
		 * buyers count > 0 then return
		*/
		$buyersCount = $buyerdetailTable->courseEnrollementCount($course_id);
		if($buyersCount > 0) {
			$multiOptions = array(
				'2' => 'Disable Future Enrollments'
			);
			$form->action->setMultiOptions($multiOptions);
		}
		// is post request
		if(!$this->getRequest()->getPost()){
			return;
		}
		// is valid data
		if(!$form->isValid($this->getRequest()->getPost())){
			return;
		}
		$formValues = $form->getValues();
		$actions = array(0 =>'Delete Course','1'=>'Disapprove Course','2'=>'Disable Future Enrollments');
		if(!array_key_exists($formValues['action'],$actions)){
			$form->addError("Please select a valid option.");
			return;
		}
		$courseTable = Engine_Api::_()->getDbtable('courses','sitecourse');
		$db = $courseTable->getAdapter();
		$db->beginTransaction();
		$actionId = $formValues['action'];
		try{
            // delete course
			if($actionId == 0) {

				if($buyersCount > 0) {
					$form->addError("Course Cannot be Deleted.");
					return;
				}
				Engine_Api::_()->sitecourse()->deleteCourse($course_id);
				Engine_Api::_()->getDbtable('reports', 'sitecourse')->delete(array('course_id =?' => $course_id));
			}
			// disapprove course
			if($actionId == 1){
				if($buyersCount > 0) {
					$form->addError("Course Cannot be Disapprove.");
					return;
				}
				$courseTable->update(array(
					'draft'=>'1',
					'approved'=> '2'
				), array(
					'course_id = ?' => $course_id
				));
			}
			// disable future enrollment
			if($actionId == 2){
				$courseTable->update(array(
					'disable_enrollment'=>'1'
				), array(
					'course_id = ?' => $course_id
				));

				Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification( $owner,$viewer , $course, 'sitecourse_enrollment_disabled',array(
					'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
					'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
			}
			$item = Engine_Api::_()->getItem('sitecourse_report',$report_id);
			if(!empty($item)) {
				$item->delete();
			}
			$db->commit();
		} catch(Exception $e){
			$db->rollBack();
			throw $e;
		}
		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 50,
			'parentRefresh'=> 50,
			'messages' => array('Action taken successfully.')
		));
	}
}

?>
