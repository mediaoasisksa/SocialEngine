<?php

class Sitecourse_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE PAGES
	public function indexAction() {

    //NAVIGATION TAB CREATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_manage');
		$this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecourse_admin_main_manage', array(), 'sitecourse_admin_main_index');

    //FORM GENERATION
		$this->view->formFilter = $formFilter = new Sitecourse_Form_Admin_Manage_Filter();

    //GET PAGE ID
		$page = $this->_getParam('page', 1);

    //MAKE QUERY
		$tableUser = Engine_Api::_()->getItemTable('user')->info('name');

		$tableCourse = Engine_Api::_()->getDbtable('courses', 'sitecourse');
		$tableCourseName = $tableCourse->info('name');

		// buyers table
		$tableBuyers = Engine_Api::_()->getDbtable('buyerdetails', 'sitecourse');
		$tableBuyerName = $tableBuyers->info('name'); 

    //select for course table
		$select = $tableCourse->select()
		->from($tableCourseName);

    //Join on course and user table
		$select = $tableCourse->select()
		->setIntegrityCheck(false)
		->from($tableCourseName)
		->joinLeft($tableUser, "$tableCourseName.owner_id = $tableUser.user_id", 'username');

		$values = array();

		if ($formFilter->isValid($this->_getAllParams())) {
			$values = $formFilter->getValues();
		}
		foreach ($values as $key => $value) {

			if (null == $value) {
				unset($values[$key]);
			}
		}

    //SEARCHING
		$this->view->owner = '';
		$this->view->title = '';
		$this->view->approved = '';
		$this->view->category_id = '';
		$this->view->courseBrowse='';
		$this->view->newest='';

		$values = array_merge(array(
			'order' => 'course_id',
			'order_direction' => 'DESC',
		), $values);

    //Assigning variable value for search by owner
		if (!empty($_POST['owner'])) {
			$user_name = $_POST['owner'];
		} else {
			$user_name = '';
		}


    //Assigning variable value for search by title
		if (!empty($_POST['title'])) {
			$course_name = $_POST['title'];
		} else {
			$course_name = '';
		}

    //SEARCHING
		$this->view->owner = $values['owner'] = $user_name;
		$this->view->title = $values['title'] = $course_name;

		if (!empty($course_name)) {
			//select statement for title 
			$select->where($tableCourseName . '.title  LIKE ?', '%' . $course_name . '%');
		}

		if (!empty($user_name)) {
			//select statement for owner search
			$select->where($tableUser . '.displayname  LIKE ?', '%' . $user_name . '%');
		}

		$select->where($tableCourseName. '.approved != 0');

		if (isset($_POST['search'])) {

			//select statement for approval status search
			if (!empty($_POST['course_status'])&& $_POST['course_status']) {
				$this->view->course_status = $_POST['course_status'];
				$select->where($tableCourseName . '.approved = ? ', $_POST['course_status']-1);

			}

			//select statement for difficulty level search
			if (!empty($_POST['difficulty_level'])&& $_POST['difficulty_level']) {
				$this->view->difficulty_level = $_POST['difficulty_level'];
				$select->where($tableCourseName . '.difficulty_level = ? ', $_POST['difficulty_level']-1);

			}

			//select statement for category search
			if (!empty($_POST['category_id'])) {
				$this->view->category_id = $_POST['category_id'];
				$select->where($tableCourseName . '.category_id = ? ', $_POST['category_id']);
			}

			//select statement for sub category search
			if (!empty($_POST['subcategory_id'])) {
				$this->view->subcategory_id = $_POST['subcategory_id'];
				$select->where($tableCourseName . '.category_id = ? ', $_POST['subcategory_id']);
			} 


			//select statement for newest only
			if (!empty($_POST['newest']) && $_POST['newest'] == 1 ) {
				$todayDate = date('Y-m-d');
				$newestThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.latest.threshold', '5');
				$select->where('DATEDIFF("'.$todayDate.'" ,'.$tableCourseName.'.`creation_date`'.') BETWEEN 0 AND '.$newestThreshold)->order('creation_date ASC');
				$this->view->newest = $_POST['newest'];
				
			}

			//select statement for from date search
			if (!empty($_POST['date_from'])) {
				$select->where('DATEDIFF("'.$_POST['date_from'].'" ,'.$tableCourseName.'.`creation_date`'.') <=0')->order('creation_date ASC');

			}

			//select statement for to date search
			if (!empty($_POST['date_to'])) {
				$select->where('DATEDIFF("'.$_POST['date_to'].'" ,'.$tableCourseName.'.`creation_date`'.') >=0')->order('creation_date ASC');

			} 
			//select statement for browse courses on bases on rating and newest
			if (!empty($_POST['courseBrowse'])&& $_POST['courseBrowse']) {
				if($_POST['courseBrowse'] == 1){
					$this->view->courseBrowse = $_POST['courseBrowse'];
					$select->order($tableCourseName . '.creation_date DESC');
				}else if($_POST['courseBrowse'] == 2){
					$this->view->courseBrowse = $_POST['courseBrowse'];
					$select->order($tableCourseName . '.rating DESC');
				}
			}
		}
		$this->view->formValues = array_filter($values);
		$this->view->assign($values);

		$select->order((!empty($values['order']) ? $values['order'] : 'course_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    //MAKE PAGINATOR
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->paginator->setItemCountPerPage(50);
		$this->view->paginator = $paginator->setCurrentPageNumber($page);
	}

	public function requestAction() {

    //NAVIGATION TAB CREATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_manage');

		$this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecourse_admin_main_manage', array(), 'sitecourse_admin_main_request');

    //FORM GENERATION
		$this->view->formFilter = $formFilter = new Sitecourse_Form_Admin_Manage_Filter();

    //GET PAGE ID
		$page = $this->_getParam('page', 1);

    //MAKE QUERY
		$tableUser = Engine_Api::_()->getItemTable('user')->info('name');

		$tableCourse = Engine_Api::_()->getDbtable('courses', 'sitecourse');
		$tableCourseName = $tableCourse->info('name');

		$select = $tableCourse->select()
		->from($tableCourseName);

		$select = $tableCourse->select()
		->setIntegrityCheck(false)
		->from($tableCourseName)
		->joinLeft($tableUser, "$tableCourseName.owner_id = $tableUser.user_id", 'username');

		$values = array();

		if ($formFilter->isValid($this->_getAllParams())) {
			$values = $formFilter->getValues();
		}
		foreach ($values as $key => $value) {

			if (null == $value) {
				unset($values[$key]);
			}
		}

    //SEARCHING
		$this->view->owner = '';
		$this->view->title = '';
		$this->view->approved = '';
		$this->view->category_id = '';

		$values = array_merge(array(
			'order' => 'course_id',
			'order_direction' => 'DESC',
		), $values);


		if (!empty($_POST['owner'])) {
			$user_name = $_POST['owner'];
		} else {
			$user_name = '';
		}


		if (!empty($_POST['title'])) {
			$course_name = $_POST['title'];
		} else {
			$course_name = '';
		}

    //SEARCHING
		$this->view->owner = $values['owner'] = $user_name;
		$this->view->title = $values['title'] = $course_name;

		//select statement for title 
		if (!empty($course_name)) {
			$select->where($tableCourseName . '.title  LIKE ?', '%' . $course_name . '%');
		}
		//select statement for onwer 
		if (!empty($user_name)) {
			$select->where($tableUser . '.displayname  LIKE ?', '%' . $user_name . '%');
		}

		$select->where($tableCourseName . '.draft = 0 ');
		$select->where($tableCourseName. '.approved = 0');

		if (isset($_POST['search'])) {


			if (!empty($_POST['difficulty_level'])&& $_POST['difficulty_level']) {

				$this->view->difficulty_level = $_POST['difficulty_level'];
				$select->where($tableCourseName . '.difficulty_level = ? ', $_POST['difficulty_level']-1);

			}

			if (!empty($_POST['category_id'])) {
				$this->view->category_id = $_POST['category_id'];
				$select->where($tableCourseName . '.category_id = ? ', $_POST['category_id']);
			}

			if (!empty($_POST['subcategory_id'])) {
				$this->view->subcategory_id = $_POST['subcategory_id'];
				$select->where($tableCourseName . '.category_id = ? ', $_POST['subcategory_id']);
			} 
		}
		$this->view->formValues = array_filter($values);
		$this->view->assign($values);

		$select->order((!empty($values['order']) ? $values['order'] : 'course_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    //MAKE PAGINATOR
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->paginator->setItemCountPerPage(50);
		$this->view->paginator = $paginator->setCurrentPageNumber($page);
	}

 //ACTION FOR MAKE SITECOURSE APPROVE
	public function approvedAction() {

		$course_id = $this->_getParam('id');
		$viewer = Engine_Api::_()->user()->getViewer();

		try {
			$sitecourse = Engine_Api::_()->getItem('sitecourse_course', $course_id);
			$sitecourse->approved = 1;
			Engine_Api::_()->sitecourse()->generateFeed($course_id);
			$sitecourse->save();
			//for notification
			$owner = Engine_Api::_()->getItem("user" , $sitecourse->owner_id);
			$course = Engine_Api::_()->getItem("sitecourse_course" , $sitecourse->course_id);
			Engine_Api::_()->getDbtable('notifications', 'activity')
			->addNotification($owner,$viewer , $course, 'sitecourse_approval',array(
				'object_link' => Engine_Api::_()->getItem("sitecourse_course",$sitecourse->course_id)->getHref(),
				'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
			Engine_Api::_()->sitecourse()->sendMail('STATUS', $course_id, 'approved');
			Engine_Api::_()->sitecourse()->sendMail('NEWEST', $course_id);
		} catch (Exception $e) {
			throw $e;
		}

		$this->_redirect('admin/sitecourse/manage/request');
	}

//ACTION FOR MAKE SITECOURSE DISAPPROVE
	public function disapprovedAction() {

		$course_id = $this->_getParam('id');
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->form = $form = new Sitecourse_Form_Disapprove();
		
    // Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('admin-manage/disapprove.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('admin-manage/disapprove.tpl');
			return;
		}

		$formValues = $form->getValues();

		try {
			$sitecourse = Engine_Api::_()->getItem('sitecourse_course', $course_id);
			$sitecourse->approved = 2;
			$sitecourse->draft = 1;
			$sitecourse->disapprove_reason=$formValues['reason'];
			$sitecourse->save();
			//for notification
			$owner = Engine_Api::_()->getItem("user" , $sitecourse->owner_id);
			$course = Engine_Api::_()->getItem("sitecourse_course" , $sitecourse->course_id);
			Engine_Api::_()->getDbtable('notifications', 'activity')
			->addNotification($owner,$viewer , $course, 'sitecourse_disapproval',array(
				'object_link' => Engine_Api::_()->getItem("sitecourse_course",$sitecourse->course_id)->getHref()));
			Engine_Api::_()->sitecourse()->sendMail('STATUS', $course_id, 'disapproved');

		} catch (Exception $e) {
			throw $e;
		}

		try {
      // Main params
			$defaultParams = array(
				'host' => $_SERVER['HTTP_HOST'],
				'email' => $viewer->email
			);
			$params = array('title'=>$course['title'],'status'=>$course['approved']);

			Engine_Api::_()->getApi('mail', 'core')->sendSystem($viewer, 'notify_' . 'sitecourse_course_status', array_merge($defaultParams, (array) $params));
		} catch (Exception $e) {
      // Silence exception
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Course Disapproved Successfully')
		));
	}


	public function reviewAction() {

    //NAVIGATION TAB CREATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_reviews');

    //FORM GENERATION
		$this->view->formFilter = $formFilter = new Sitecourse_Form_Admin_Manage_Filter();

    //GET PAGE ID
		$page = $this->_getParam('page', 1);

    //MAKE QUERY
		$tableUser = Engine_Api::_()->getItemTable('user')->info('name');
		$tableReview = Engine_Api::_()->getDbtable('reviews', 'sitecourse');
		$tableReviewName = $tableReview->info('name');
		$select = $tableReview->select()
		->from($tableReviewName);

		$values = array();

		$select = $tableReview->select()
		->setIntegrityCheck(false)
		->from($tableReviewName)
		->joinLeft($tableUser, "$tableReviewName.user_id = $tableUser.user_id", 'username');

		if ($formFilter->isValid($this->_getAllParams())) {
			$values = $formFilter->getValues();
		}
		foreach ($values as $key => $value) {
			//unsetting keys for null values in values[]
			if (null == $value) {
				unset($values[$key]);
			}
		}

    //SEARCHING
		$this->view->owner = '';
		$this->view->title = '';
		$this->view->status = '';
		$this->view->rating = '';

		$values = array_merge(array(
			'order' => 'review_id',
			'order_direction' => 'DESC',
		), $values);


		if (!empty($_POST['owner'])) {
			$user_name = $_POST['owner'];
		} else {
			$user_name = '';
		}


		if (!empty($_POST['title'])) {
			$review_name = $_POST['title'];
		} else {
			$review_name = '';
		}

    //SEARCHING
		$this->view->owner = $values['owner'] = $user_name;
		$this->view->title = $values['title'] = $review_name;

		if (!empty($review_name)) {
			$select->where($tableReviewName . '.review_title  LIKE ?', '%' . $review_name . '%');
		}

		if (!empty($user_name)) {
			$select->where($tableUser . '.displayname  LIKE ?', '%' . $user_name . '%');
		}

		if (isset($_POST['search'])) {

			if (!empty($_POST['review_status'])&& $_POST['review_status']) {
				$this->view->status = $_POST['review_status'];
				$select->where($tableReviewName . '.status = ? ', $_POST['review_status']-1);
			}        

			if (!empty($_POST['rating'])&& $_POST['rating']) {
				$this->view->rating = $rating = $_POST['rating'];

				switch ($rating) {
					case "1":
					$select->where($tableReviewName . '.rating <= ? ', $_POST['rating']);
					break;
					case "2":
					$select->where($tableReviewName . '.rating > ? ', $_POST['rating']-1)
					->where($tableReviewName . '.rating <= ? ', $_POST['rating']);
					break;
					case "3":
					$select->where($tableReviewName . '.rating > ? ', $_POST['rating']-1)
					->where($tableReviewName . '.rating <= ? ', $_POST['rating']);
					break;
					case "4":
					$select->where($tableReviewName . '.rating > ? ', $_POST['rating']-1)
					->where($tableReviewName . '.rating <= ? ', $_POST['rating']);
					break;
					case "5":
					$select->where($tableReviewName . '.rating > ? ', $_POST['rating']-1)
					->where($tableReviewName . '.rating <= ? ', $_POST['rating']);
					break;
					default:
					break;
				}
			}
		}
		$this->view->formValues = array_filter($values);
		$this->view->assign($values);

		$select->order((!empty($values['order']) ? $values['order'] : 'course_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    //MAKE PAGINATOR
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->paginator->setItemCountPerPage(50);
		$this->view->paginator = $paginator->setCurrentPageNumber($page);
	}

	public function reviewApproveAction() {

		$review_id = $this->_getParam('id');
		$course_id = $this->_getParam('course_id');
		$viewer = Engine_Api::_()->user()->getViewer();

		try {
			$review = Engine_Api::_()->getItem('sitecourse_review', $review_id);
			$review->status = !$review->status;
			$review->save();
			$course = Engine_Api::_()->getItem("sitecourse_course" , $course_id);
			$owner = Engine_Api::_()->getItem("user" , $course->owner_id);
			$avgRating=Engine_Api::_()->getItemTable('sitecourse_review')->getRating($course_id);
			//updating avg rating in courses table
			Engine_Api::_()->getItemTable('sitecourse_course')->update(array('rating' => $avgRating),array('course_id =?'=>$course_id));
			//notifiaction
			if($review->status){
				Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($owner,$viewer , $course, 'sitecourse_review_approval',array(
					'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
					'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
				Engine_Api::_()->sitecourse()->sendMail('REVIEW', $course_id);
			}else{
				Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($owner,$viewer , $course, 'sitecourse_review_disapproval',array(
					'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
					'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
			}
		} catch (Exception $e) {
			throw $e;
		}
		$this->_redirect('admin/sitecourse/manage/review');
	}

	public function multiReviewDeleteAction() {
		if ($this->getRequest()->isPost()) {
			$values = $this->getRequest()->getPost();
			
			foreach ($values as $key => $value) {
				if ($key == 'delete_' . $value) {
          //DELETE REVIEWS FROM DATABASE
					$review_id = (int) $value;
					$reviewLike = Engine_Api::_()->getDbTable('reviewlikes','sitecourse');
					if($review){
						$reviewLike->delete(array('review_id =?'=>$value));
						/**
		 					* set avg rating in course table
			  			* delete the review form review table
						*/
			  			Engine_Api::_()->getItemTable('sitecourse_review')->deductRating($review_id);
			  		}
			  	}
			  }
			}
			return $this->_helper->redirector->gotoRoute(array('action' => 'review'));
		}

		public function deleteReviewAction(){
      // In smoothbox
			$this->_helper->layout->setLayout('admin-simple');
			$review_id = $this->_getParam('review_id',0);
			if(!$this->getRequest()->isPost()){
				return;
			}

			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();		
			$reviewLike = Engine_Api::_()->getDbTable('reviewlikes','sitecourse');
			try{
				$reviewLike->delete(array('review_id =?'=>$review_id));
			/**
		 		* set avg rating in course table
			  * delete the review form review table
			*/
		 		Engine_Api::_()->getItemTable('sitecourse_review')->deductRating($review_id);
		 		$db->commit();
		 	}catch(Exception $e){
		 		$db->rollBack();
		 		throw $e;
		 	}
		 	return $this->_forward('success', 'utility', 'core', array(
		 		'smoothboxClose' => 20,
		 		'parentRefresh'=> 10,
		 		'messages' => array('Success'),
		 	));
		 }


		 public function transactionsAction() {
		 	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
		 	->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_transcations');

		 	$this->view->formFilter = $formFilter = new Sitecourse_Form_Admin_Manage_Filter();

		 	if($formFilter->isValid($this->_getAllParams())) {
		 		$filterValues = $formFilter->getValues();
		 	}
		 	$params = array();
		 	foreach ($filterValues as $key => $value) {
		 		if (null == $value) {
		 			unset($filterValues[$key]);
		 		}
		 	}

		 	$params = array_merge(array(
		 		'order' => 'transaction_id',
		 		'order_direction' => 'DESC'
		 	),$filterValues);


		 	if(!empty($_GET)) {
		 		$params['search'] = $_GET;
		 	}

    //GET PAGE ID
		 	$page = $this->_getParam('page', 1);

		 	$paginator = Engine_Api::_()->getDbtable('transactions','sitecourse')->getTransactionsPaginator($params);
		 	$paginator->setItemCountPerPage(50);
		 	$paginator->setCurrentPageNumber($page);
		 	$this->view->paginator = $paginator;
		 	$this->view->order = $params['order'];
		 	$this->view->order_direction = $params['order_direction'];
		 }

		 public function transactionDetailsAction() {
		// In smoothbox
		 	$this->_helper->layout->setLayout('admin-simple');
		 	$transactionId = $this->_getParam('transaction_id',null);
		 	if(empty($transactionId)) {
		 		return $this->_forward('success', 'utility', 'core', array(
		 			'smoothboxClose' => 20,
		 			'parentRefresh'=> 10,
		 			'messages' => array('Please Try After Some Time')
		 		));
		 	}

		 	$transaction = Engine_Api::_()->getItem('sitecourse_transaction',$transactionId);

		 	if(empty($transaction)) {
		 		return $this->_forward('success', 'utility', 'core', array(
		 			'smoothboxClose' => 20,
		 			'parentRefresh'=> 10,
		 			'messages' => array('Please Try After Some Time')
		 		));		
		 	}
		 	$ordercourses_table = Engine_Api::_()->getDbtable('ordercourses','sitecourse');
		 	$orderCourseDetails = $ordercourses_table->getOrderCourses($transaction['order_id']);	
		 	$this->view->details = array_merge($transaction->toArray(),$orderCourseDetails);
		 }

		 public function disableEnrollmentAction() {
		 	$course_id = $this->_getParam('course_id');
		 	$viewer = Engine_Api::_()->user()->getViewer();

		 	if(!$this->getRequest()->isPost()){
		 		return;
		 	}

		 	try {
		 		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		 		$course->disable_enrollment = 1;
		 		$course->save();
			//notification
		 		$owner = Engine_Api::_()->getItem("user" , $course->owner_id);
		 		Engine_Api::_()->getDbtable('notifications', 'activity')
		 		->addNotification( $owner,$viewer , $course, 'sitecourse_enrollment_disabled',array(
		 			'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
		 			'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
		 	} catch (Exception $e) {
		 		throw $e;
		 	}
		 	return $this->_forward('success', 'utility', 'core', array(
		 		'smoothboxClose' => 20,
		 		'parentRefresh'=> 10,
		 		'messages' => array('Success'),
		 	));
		 }

		 public function enableEnrollmentAction() {
		 	$course_id = $this->_getParam('course_id');
		 	$viewer = Engine_Api::_()->user()->getViewer();
		 	try {
		 		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		 		$course->disable_enrollment = 0;
		 		$course->save();
			//notification
		 		$owner = Engine_Api::_()->getItem("user" , $course->owner_id);		 		
		 		Engine_Api::_()->getDbtable('notifications', 'activity')
		 		->addNotification( $owner,$viewer , $course, 'sitecourse_enrollment_enabled',array(
		 			'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
		 			'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
		 	} catch (Exception $e) {
		 		throw $e;
		 	}
		 	$this->_redirect('admin/sitecourse/manage');
		 }


		 public function enrollmentCountAction() {
		 	$ids = $this->_getParam('ids');
		 	$enrollment = array();
		// buyers table
		 	$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		 	foreach($ids as $id) {
		 		$enrollment[$id] = $buyerdetailTable->courseEnrollementCount($id);
		 	}
		 	$this->view->enrollment = $enrollment;
		 }

		 public function courseDetailsAction() {
		 	$this->_helper->layout->setLayout('admin-simple');
		 	$id = $this->_getParam('course_id');
		 	$course = Engine_Api::_()->getItem('sitecourse_course', $id);

		 	if(empty($course)) {
		 		return;
		 	}
		 	$difficulty_levels = array(
		 		0 => 'Beginner',
		 		1 => 'Intermediate',
		 		2 => 'Expert'
		 	);
		 	$contentImages = Engine_Api::_()->sitecourse()->getContentImage($course);
		 	$this->view->image = $contentImages['image_profile'];
		 	$this->view->course = $course;

		// reviews count
		 	$this->view->reviews = Engine_Api::_()->getItemTable('sitecourse_review')->reviewsCount($id);
		 	$this->view->favourites = Engine_Api::_()->getItemTable('sitecourse_favourite')->getFavouriteCount($id);
		// get buyers table
		 	$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		 	$this->view->enrolled_count = $buyerdetailTable->courseEnrollementCount($id);

		// get category 
		 	$category = Engine_Api::_()->getItem('sitecourse_category', $course['category_id']);
		 	$this->view->category = $category['category_name'];   
		 	$this->view->difficulty = $difficulty_levels[$course['difficulty_level']];
		 }

		 public function detailTransactionAction() {
		 	$gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
		 	$enable_gateway = $gateway_table->select()
		 	->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin', 'config'))
		 	->where('enabled = 1')
		 	->where('plugin in (?)', array('Payment_Plugin_Gateway_PayPal'))
		 	->query()
		 	->fetch();
        // Payment_Plugin_Gateway_PayPal Not Enabled
		 	if(empty($enable_gateway)) {
		 		die;
		 	}
		 	$transaction_id = $this->_getParam('transaction_id');
		 	$transaction = Engine_Api::_()->getItem('sitecourse_transaction', $transaction_id);
		 	$gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);

		 	$link = null;
		 	if ($this->_getParam('show-parent')) {
		 		if (!empty($transaction->gateway_parent_transaction_id)) {
		 			$link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_parent_transaction_id);
		 		}
		 	} else {
		 		if (!empty($transaction->gateway_transaction_id)) {
		 			$link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_transaction_id);
		 		}
		 	}

		 	if ($link) {
		 		return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
		 	} else {
		 		die();
		 	}
		 }
		}
		?>
