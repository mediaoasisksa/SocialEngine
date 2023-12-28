<?php
class Sitecourse_IndexController extends Core_Controller_Action_Standard
{
	public function init() {
		//if( !$this->_helper->requireUser()->isValid() ) return;
	}

	public function createAction() {
		if( !$this->_helper->requireAuth()->setAuthParams('sitecourse_course', null, 'create')->isValid()) return;

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_main');

          // set up data needed to check quota
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();

		$courseCount = Engine_Api::_()->getItemTable('sitecourse_course')->getCourseCount($user_id);
		$this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'max_courses');
		$this->view->canCreate = true;
          //check creation quota if quota is not equal to zero
		if($quota && $courseCount >= $quota){
			$this->view->canCreate = false;
			return;
		}

		$this->view->form = $form = new Sitecourse_Form_Create();

              // If not post or form not valid, return
		if( !$this->getRequest()->isPost() ) {
			return;
		}

		$postValues = $this->getRequest()->getPost();
		$this->view->subcategory_id = 0;
		if(isset($postValues['subcategory_id']) && $postValues['subcategory_id']){
			$this->view->subcategory_id = $postValues['subcategory_id'];
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
		$formValues = $form->getValues();
		
        // url and price validations
		if(!$form->validUrl($formValues['url'])) return $form->addError("Url Must be Alphanumeric");

		if(!$form->validPrice($formValues['price'])) {
			return $form->addError("Price must contain only digits.");
		} 

		$duration = $formValues['duration'];
		if(!empty($duration) && !is_numeric($duration)){
			return $form->addError("Duration must a numeric value");	
		} 

		if(isset($formValues['category_id'])){
			if(empty($formValues['category_id']) || $formValues['category_id'] == 0){
				return $form->addError("Please choose a valid category");
			}
		}

        //subcategory is empty and not set
		if(!isset($formValues['subcategory_id']) || empty($formValues['subcategory_id'])){
			$formValues['subcategory_id'] = 0;
		}
		$formValues['price'] = @round($formValues['price'],2);
        // Process
		$table = Engine_Api::_()->getItemTable('sitecourse_course');
		$db = $table->getAdapter();
		$db->beginTransaction();
        //check url validation
		if($table->urlExists($formValues['url'])){
			$form->addError("Url Already Exists");
			return;
		}
		try {
            // Create course
			$values = array_merge($formValues, array(
				'owner_id' => $user_id,
				'view_privacy' => $formValues['auth_view'],
				'comment_privacy' => $formValues['auth_comment'],
				'draft' => 1
			));

			$course = $table->createRow();
			$course->setFromArray($values);
			$course->save();

			$categoryId = $values['category_id'];

			$categoryItem = Engine_Api::_()->getItem('sitecourse_category',$categoryId);
			$categoryItem = $categoryItem->toArray();
			$course_count = $categoryItem['course_count']+1;
			Engine_Api::_()->getItemTable('sitecourse_category')->update(array('course_count' => $course_count),
				array(
					'category_id = ?'=> $categoryId,
				)
			);

			if( !empty($values['photo']) ) { 
				$course->setPhoto($form->photo);
			}
            // Auth
			$auth = Engine_Api::_()->authorization()->context;
			$roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
			
			foreach( $roles as $i => $role ) {
				$auth->setAllowed($course, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($course, $role, 'comment', ($i <= $commentMax));
			}

            // Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$course->tags()->addTagMaps($viewer, $tags);

            // Commit
			$db->commit();
		} catch( Exception $e ) {
			return $this->exceptionWrapper($e, $form, $db);
		}
		return $this->_helper->redirector->gotoRoute(array('action' => 'edit','course_id'=>$course->getIdentity()), 'sitecourse_specific', true);
	}

	public function editAction() {
		$viewer = Engine_Api::_()->user()->getViewer();

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_main');
		$course_id = $this->_getParam('course_id', 0);
		$course= Engine_Api::_()->getItem('sitecourse_course', $course_id);
        // validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		$courseArr = $course->toArray();
		$viewer_id = $viewer->getIdentity();

		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);

		$this->view->subcategory_id = $courseArr['subcategory_id'];
		$this->view->draft = $draft = $courseArr['draft'];
		$this->view->approval = $courseArr['approved'];
        // Prepare form
		$this->view->form = $form = new Sitecourse_Form_Edit(array('category_id'=>$courseArr['category_id'],'draft'=>$draft,'canPublish'=>$this->canSendPublishRequest($course['request_count'],$viewer)));


		$this->view->reason = $courseArr['disapprove_reason'];
		$this->view->course_id= $course_id=$this->_getParam('course_id');
        // Populate form
		$form->populate($courseArr);
		$tagStr = '';
		foreach( $course->tags()->getTagMaps() as $tagMap ) {
			$tag = $tagMap->getTag();
			if( !isset($tag->text) ) continue;
			if( '' !== $tagStr ) $tagStr .= ', ';
			$tagStr .= $tag->text;
		}

		$form->populate(array(
			'tags' => $tagStr
		));
		$this->view->tagNamePrepared = $tagStr;
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		foreach( $roles as $role ) {
			if ($form->auth_view){
				if( $auth->isAllowed($course, $role, 'view') ) {
					$form->auth_view->setValue($role);
				}
			}

			if ($form->auth_comment) {
				if( $auth->isAllowed($course, $role, 'comment') ) {
					$form->auth_comment->setValue($role);
				}
			}
			
		}
		$this->view->canEditCat = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.editcategory',true);
		// get buyers table
		$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		/**
		 * get buyers count
		 * buyers count > 0 then make price disabled
		 */
		$buyersCount = $buyerdetailTable->courseEnrollementCount($course_id);
		if($buyersCount > 0) {
			$form->price->setAttrib('readonly',true);
			$values['price'] = $course['price'];
		}

        // Check post/form
		if( !$this->getRequest()->isPost() ) {
			return;
		}
		$postValues = $this->getRequest()->getPost();
		$this->view->subcategory_id = 0;
		if(isset($postValues['subcategory_id']) && $postValues['subcategory_id']){
			$this->view->subcategory_id = $postValues['subcategory_id'];
		}

		if(!$form->isValid($this->getRequest()->getPost())) {
			return;
		}
		// form values
		$values = $form->getValues();
		if(!$form->validPrice($values['price'])) {
			return $form->addError("Pirce must contain only digits");
		}

		$duration = $values['duration'];
		if(!empty($duration) && !is_numeric($duration)){
			return $form->addError("Duration must a numeric value");	
		} 
		
        // Process
		if($values['category_id'] == '0'){
			return $form->addError("Please select a valid category");
		}
		// round off by 2 decimal points
		$values['price'] = @round($values['price'],2);

		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		$table = Engine_Api::_()->getItemTable('sitecourse_course');
		if(
			$values['course_publish'] === 'publish' 
			&& !$table->canPublish($course_id)
		) {
			return $form->addError("Please add at least one lesson and one topic under the current course before publishing the course");
		}

		// must include intro video
		$courseVideoTable = Engine_Api::_()->getItemTable('sitecourse_video');
		$intro_video = $courseVideoTable->getVideoItem($course_id, 'course');
		if(empty($intro_video)) {
			return $form->addError("Please add intro video before publishing the course");
		}
		try {
			if( empty($values['auth_view']) ) {
				$values['auth_view'] = 'everyone';
			}

			if(empty($values['auth_comment'])) {
				$values['auth_comment'] = 'everyone';
			}
			$values['view_privacy'] = $values['auth_view'];
			$values['comment_privacy'] = $values['auth_comment'];
			$categoryId = $course['category_id'];
			if($categoryId != $values['category_id']){
				$this->changeCourseCount($categoryId,$values['category_id']);
			}
			$course->setFromArray($values);
			$course->modified_date = date('Y-m-d H:i:s');
			$course->save();

			// Add photo
			if( !empty($values['photo']) ) {
				$course->setPhoto($form->photo);
			}

            // Auth
			$viewMax = array_search($values['view_privacy'], $roles);
			foreach( $roles as $i => $role ) {
				$auth->setAllowed($course, $role, 'view', ($i <= $viewMax));
			}
			$commentMax = array_search($values['comment_privacy'], $roles);
			foreach( $roles as $i => $role ) {
				$auth->setAllowed($course, $role, 'comment', ($i <= $commentMax));
			}

            // handle tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$course->tags()->setTagMaps($viewer, $tags);
			
			if($values['course_publish'] === 'publish'){
                  // auto approval or admin submission work
				$permissionsTable = Engine_Api::_()->getDbtable('permissions','authorization');
				$responseArr = $permissionsTable->getAllowed('sitecourse_course', $viewer->level_id, array('approve'));
				$auto_approve = $responseArr['approve'];
				if($auto_approve){
					Engine_Api::_()->sitecourse()->generateFeed($course_id);
					$form->addNotice('Your Course is successfully Published.');
					Engine_Api::_()->sitecourse()->sendMail('STATUS', $course_id, 'approved');
					Engine_Api::_()->sitecourse()->sendMail('NEWEST', $course_id);
				}
				$table->update(
					array('draft'=>0,
						'approved' => ($auto_approve)?1:0,
						'request_count' => ++$course['request_count'],
					),
					array(
						'course_id = ?'=>$course_id
					)
				);
			}
			$db->commit();

		}
		catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
		$form->addNotice("Changes added successfully");
		$form->price->setValue($values['price']);
		$this->view->subcategory_id = $values['subcategory_id'];
		if($values['course_publish'] == 'publish'){
			$form->removeElement('course_publish');
			if(!$auto_approve){
				$this->view->approval = 0;
				$this->view->draft = 0;
			}
		}
	}

	public function changeCourseCount($prevId, $currId) {
		$categoryItem = Engine_Api::_()->getItem('sitecourse_category',$prevId);
		if(empty($categoryItem)) {
			return;
		}
		$categoryItem = $categoryItem->toArray();
		$course_count = $categoryItem['course_count']-1;
		Engine_Api::_()->getItemTable('sitecourse_category')->update(array('course_count' => $course_count),
			array(
				'category_id = ?'=> $prevId,
			)
		);
		$categoryItem = Engine_Api::_()->getItem('sitecourse_category',$currId);
		if(empty($categoryItem)) {
			return;
		}
		$categoryItem = $categoryItem->toArray();
		$course_count = $categoryItem['course_count']+1;
		Engine_Api::_()->getItemTable('sitecourse_category')->update(array('course_count' => $course_count),
			array(
				'category_id = ?'=> $currId,
			)
		);
	}

    /**
     * 
     * @return {array} subcategorys of given category 
     * 
     */
    public function subcategoryAction() {
    	$parent_category = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_category');
    	if(!$parent_category){
    		$this->view->subcats = array();
    		return;
    	}
    	$subCategories = Engine_Api::_()->getDbtable('categories', 'sitecourse')->getSubCategoresAssoc($parent_category);
    	$subCategoryOptions = array();
    	foreach($subCategories as $category) {
    		$subCategoryOptions[$category['category_id']] = $category['category_name'];
    	}
    	$this->view->subcats = $subCategoryOptions;
    }

    private function canSendPublishRequest($requestCount, $viewer){
        // get no of requests user can send
    	$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    	$responseArr = $permissionsTable->getAllowed('sitecourse_course', $viewer->level_id, array('approval_reminders'));
    	$approval_reminders = $responseArr['approval_reminders'];

    	$can_request = true;
            // check the no of responses send by the user.
    	if($approval_reminders && $approval_reminders <= $requestCount){
    		$can_request = false;
    	}   
    	return $can_request;
    }

    /**
     * url cant contain underscore,dashed and alphanumerice value
     * minimum url length must be 3
     * url validation
     */
    public function validateurlAction() {
    	$url = Zend_Controller_Front::getInstance()->getRequest()->getParam('url');
    	$alphaNumericRegex = "/^[A-Za-z0-9_-]*$/";
    	$regx=preg_match($alphaNumericRegex,$url);
    	if($url=='')
    		return;

    	$this->view->message = ( $regx )? "Url is valid ": "Url is invalid";
    	if(!$regx)
    		return;
    	$check= Engine_Api::_()->getItemTable('sitecourse_course')->urlExists($url);
    	$this->view->message = ( $check )? "URL is already being used": "URL is Available";
    }

    // places checks for profile page
    public function profileAction() {

    	$course_url = $this->_getParam('url');
    	$course_id = Engine_Api::_()->sitecourse()->getCourseId($course_url);
    	// check user is allowed to view the course
    	
    	$checks = new Sitecourse_Api_Checks();
    	$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
    	
    	$viewer = Engine_Api::_()->user()->getViewer();
    	if( $course ) {
    		Engine_Api::_()->core()->setSubject($course);
    	}
    	if( !$this->_helper->requireSubject()->isValid() ) {
    		return;
    	}

    	if( !$this->_helper->requireAuth()->setAuthParams($course, $viewer, 'view')->isValid() ) {
    		return;
    	}
    	if(!$this->_helper->requireAuth()->setAuthParams('sitecourse_course', null, 'view')->isValid()) {
    		return;
    	}
    	if(!$checks->_canViewProfilePage($course_id)) {
    		return $this->_helper->requireSubject->forward();
    	}
    	$this->_helper->content
    	->setEnabled();
    }

    // send message to owner
    public function messageownerAction() {
      	// Get viewer detail
    	$viewer = Engine_Api::_()->user()->getViewer();
    	$viewer_id = $viewer->getIdentity();

      	// Get course id and course object
    	$course_id = $this->_getParam('course_id');
    	$course = Engine_Api::_()->getItem('sitecourse_course',$course_id);

        // If course not present
    	if(!$course && empty($course)){
    		return;
    	}
      	// Course owner can't send message to itself
    	if($viewer_id == $course['owner_id']){
    		return $this->_helper->requireSubject->forward();
    	}
    	$this->view->form = $form = new Sitecourse_Form_MessageOwner();
        // check post request
    	if(!$this->getRequest()->isPost()){
    		return;
    	}
    	if(!$form->isValid($this->getRequest()->getPost())){
    		return;
    	}
        // get form values
    	$formValues = $form->getValues();
    	$db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    	$db->beginTransaction();
    	try{
    		$recipients = array($course['owner_id']);
    		$course_title = $course['title'];
    		$course_title_with_link = '<a href ="http://'.$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array('course_id'=>$course['course_id'],'action'=>'profile'),'sitecourse_specific',false).'">'.$course_title.'</a>';
    		$conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
    			$viewer, $recipients, $formValues['title'], $formValues['body'] . "<br><br>" . "This message corresponds to the course:" . $course_title_with_link
    		); 
    		Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
    		$db->commit();
    	}catch (Exception $e) {
    		$db->rollBack();
    		throw $e;
    	}
    	return $this->_forward('success', 'utility', 'core', array(
    		'smoothboxClose' => 20,
    		'parentRefresh'=> 10,
    		'messages' => array('Message Sent Successfully'),
    	));
    }  

  	// toggle favorite
    public function togglefavouriteAction() {
      	// Get viewer detail
    	$viewer = Engine_Api::_()->user()->getViewer();
    	$viewer_id = $viewer->getIdentity();

      	// Get course id and course object
    	$course_id = $this->_getParam('course_id');
    	$course = Engine_Api::_()->getItem('sitecourse_course',$course_id);

    	$dbTable = Engine_Api::_()->getDbtable('favourites','sitecourse');
    	$isFavourite = $dbTable->isFavourite($course_id,$viewer_id);

    	$db = $dbTable->getAdapter();
    	$db = $db->beginTransaction();
	    /**
	     * if already in favourite remove the row
	     * else create a row
	     */
	    try{
	    	if($isFavourite){
	    		$dbTable->delete(array(
	    			'course_id = ?' => $course_id,
	    			'owner_id = ?' => $viewer_id
	    		)); 
	    		$this->view->favourite = false;
	    	}
	    	else{
	    		$row = $dbTable->createRow();
	    		$row->setFromArray(array('course_id'=>$course_id,'owner_id'=>$viewer_id));
	    		$row->save();
	    		$owner = Engine_Api::_()->getItem("user" , $course->owner_id);
	    		Engine_Api::_()->getDbtable('notifications', 'activity')
	    		->addNotification($owner,$viewer , $course, 'sitecourse_favourite',array(
	    			'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
	    			'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
	    		$this->view->favourite = true;
	    	}
	    	$db->commit();
	    }catch(Exception $e){
	    	$db->rollBack();
	    	throw $e;
	    }
	}  

	public function favouritesAction() {
		if( !$this->_helper->requireUser()->isValid() ) return;
		if( !$this->_helper->requireAuth()->setAuthParams('sitecourse_course', null, 'view')->isValid()) return;

		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		$favoriteCourses = Engine_Api::_()->getItemTable('sitecourse_favourite')
		->getFavouriteCourses($viewer_id);
		$courses = array();

		foreach($favoriteCourses as $favourite){
			$course = Engine_Api::_()->getItem('sitecourse_course',$favourite['course_id']);
			$image = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);
			$courses[] = array_merge($course->toArray()	,$image);
		}
		$this->view->favouriteCourses = $courses;

	}

	// load courses
	public function ajaxLoadAction() {

		if($this->_getParam('is_ajax')) {
			$pagination_params = json_decode($this->_getParam('pagination_params',null),true);
			$viewer = Engine_Api::_()->user()->getViewer();
			$viewerId = $viewer->getIdentity();
			$page_id = $this->_getParam('page_id',null);
			$items_per_page = $this->_getParam('item_count',10);
			$textTrucationLimit = $this->_getParam('textTrucationLimit',20); 
			//print_r($textTrucationLimit);die;


			if(empty($pagination_params) || !isset($page_id)){
				$this->view->error = "There Is Some Error";
				return;
			}
			// viewer validation in case user id is given
			if(
				!empty($pagination_params['user_id']) &&
				$viewerId !== $pagination_params['user_id']
			) {
				// client is 
				$this->view->status = 401;
				$this->view->error = "Unauthorized request. You do not have access.";
				return;
			}
			$this->view->offset = $offset = intval($page_id) * intval($items_per_page);
			$pagination_params['offset'] = $offset;
			$pagination_params['limit'] = $items_per_page + 1 ;
			$pagination_params['is_ajax'] = true;

			$checks = new Sitecourse_Api_Checks();

			$courses = Engine_Api::_()->getDbtable('courses','sitecourse')->getCoursesSelect($pagination_params);
			$load_more = false;
			$this->view->count = count($courses);
			// check more content is available
			if(count($courses) > $items_per_page) {
				$load_more = true;
				// remove the last element no need to send
				array_pop($courses);
			}
			$this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
			$difficulty_levels = array(
				0 => 'Beginner',
				1 => 'Intermediate',
				2 => 'Expert'
			);
			// favourite courses db table
			$favouriteTable = Engine_Api::_()->getDbtable('favourites', 'sitecourse');

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
				$courses[$key]['toprated'] = Engine_Api::_()->getDbtable('courses','sitecourse')->isTopRatedCourse($course['course_id']);
				$courses[$key]['newest'] = Engine_Api::_()->getDbtable('courses','sitecourse')->isNewestCourse($course['course_id']);
				$courses[$key]['topics'] = Engine_Api::_()->getDbtable('topics','sitecourse')->fetchAllTopics($course['course_id']);
				$courses[$key]['link'] = $this->view->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),substr($course['title'],0,$textTrucationLimit).'...',array());
				$storage_file = Engine_Api::_()->getItem('storage_file', $course['photo_id']);
				$src='';
				if(!empty($storage_file)){
					$src=$storage_file->map(); 
				}
				$courses[$key]['img_src'] =  $src;


				$courses[$key]['canBuy'] = $canBuy = $checks->_canBuyCourse($course['course_id']);


				$courses[$key]['isPurchased'] = $isPurchased = $checks->_canViewLearningPage($course['course_id']);

				if($canBuy){
					$courses[$key]['getEnrolled'] = $this->view->url(array('action' => 'buyer-details','course_id' =>$course['course_id'] ), 'sitecourse_order', true);
				} elseif($isPurchased){
					$courses[$key]['getEnrolled'] = $this->view->url(array('action' => 'index','course_id'=>$course['course_id']), 'sitecourse_learning', true);
				} else {
					$courses[$key]['getEnrolled'] = $this->view->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$this->view->translate('GO TO COURSE'),array());
				}

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
						$percentage = round(($completeLessonCnt / $totalLessonCnt) * 100);
						$courses[$key]['percentage'] = $percentage.'%';
					}
					
				}
				// get category 
				$category = Engine_Api::_()->getItem('sitecourse_category', $course['category_id']);
				$courses[$key]['category_name'] = $category['category_name'];	
				$courses[$key]['difficulty_name'] = $difficulty_levels[$course['difficulty_level']];
				// favourite process
				$courses[$key]['is_favourite'] = $favouriteTable->isFavourite($course['course_id'], $viewerId);
				$courses[$key]['favourite_url'] = $this->view->url(array('action' => 'togglefavourite', 'course_id' => $course['course_id']), 'sitecourse_specific', array());

				$courses[$key]['edit_url'] = $this->view->url(array('action' => 'edit', 'course_id' => $course['course_id']), 'sitecourse_specific', array());

				$courses[$key]['details_url'] = $this->view->url(array('action' => 'course-details', 'course_id' => $course['course_id']),'sitecourse_specific',array());
				$courses[$key]['share_url'] = $this->view->url( array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => 'sitecourse_course', 'id' => $course['course_id'],'format' => 'smoothbox'), 'default', array('class' =>'smoothbox'));

			    if($viewer->getIdentity()) {
                    $deletePermission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'delete');
    				if(!empty($courses[$key]['buyers_count'])) {
    					$deletePermission = 0;
    				}
                }
				$courses[$key]['delete_permission'] = $deletePermission;
				$courses[$key]['delete_url'] = $this->view->url(array('action' => 'course-delete', 'course_id' => $course['course_id']), 'sitecourse_specific', array());

			}
			$this->view->courses = $courses;
			$this->view->load_more = $load_more;
			$this->view->status = 200;
			return;	
		}
	}

	public function indexAction() {
		$this->_helper->content
		->setEnabled();
	}

	public function manageAction() {
		if( !$this->_helper->requireUser()->isValid() ) return;
      	// Render
		$this->_helper->content
		->setEnabled();
	}

	// place check -> canViewProfilePage
	public function reportAction() {
		$this->_helper->layout->setLayout('default-simple');
		$course_id= $this->_getParam('course_id');
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		// check user is allowed to view the course
		$helper = $this->_helper;
		$checks = new Sitecourse_Api_Checks();
		$canReport = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.report', 0);
		if(
			$checks->viewerType($course_id) === "owner" ||
			!$course || $course->approved == 2 || $course->draft == 1 || !$canReport
		) {
			return $this->_helper->requireSubject->forward();
		}

		$viewer = Engine_Api::_()->user()->getViewer();
		$report =  Engine_Api::_()->getItemTable('sitecourse_report')->getReport($viewer->getIdentity(),$course_id);
		$this->view->form = $form = new Sitecourse_Form_Report();
		if($report)
			$form->populate($report);

		if( !$this->getRequest()->isPost() ){
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ){
			return;
		}

    	// Process
		$table = Engine_Api::_()->getItemTable('sitecourse_report');
		$db = $table->getAdapter();
		$db->beginTransaction();

		try{
			// report is not created
			if(!$report) {
				$row = $table->createRow();
			} else {
				$reportsTable = Engine_Api::_()->getItemTable('sitecourse_report');
				$row = $reportsTable->find($report['report_id'])->current();
			}
			$row->setFromArray(array_merge($form->getValues(), array(
				'course_id' =>$course_id,
				'reporter_id' => $viewer->getIdentity(),
			)));
			$row->save();
			$course = Engine_Api::_()->getItem("sitecourse_course" , $course_id);
			$owner = Engine_Api::_()->getItem("user" , $course->owner_id);
			if(!$report) {
				Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($owner,$viewer , $course, 'sitecourse_report',array(
					'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
					'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
			}
			$db->commit();
		}
		catch( Exception $e ){
			$db->rollBack();
			throw $e;
		}

      	// Close smoothbox
		return $this->_forward('success', 'utility', 'core', array(
			'messages' => $this->view->translate('Your report has been submitted'),
			'smoothboxClose' => true,
			'parentRefresh' => false,
		));
	}

	public function courseDeleteAction() {
		if( !$this->_helper->requireUser()->isValid() ) return;
		$this->_helper->layout->setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$course_id = $this->_getParam('course_id', 0);
		$course= Engine_Api::_()->getItem('sitecourse_course', $course_id);
		$deletePermission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'delete');
        // validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) || !$deletePermission ) {
			return $this->_forward('success', 'utility', 'core', array(
				'messages' => '',
				'smoothboxClose' => true,
				'parentRefresh' => false,
			));
		}
		$this->view->form = $form = new Sitecourse_Form_CourseDelete();
		if( !$this->getRequest()->isPost() ){
			return;
		}
		// delete the course
		Engine_Api::_()->sitecourse()->deleteCourse($course_id);
     	// Close smoothbox
		return $this->_forward('success', 'utility', 'core', array(
			'messages' => $this->view->translate('Your course deleted Successsfully'),
			'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}

	public function courseDetailsAction() {

		$this->_helper->layout->setLayout('default-simple');
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

	public function tagscloudAction() {
		if (!$this->_helper->requireAuth()->setAuthParams('sitecourse_course', null, 'view')->isValid()) {
			return;
		}
		$this->_helper->content->setNoRender()->setEnabled();
	}

}
?>
