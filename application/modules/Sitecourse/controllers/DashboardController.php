<?php

class Sitecourse_DashboardController extends Core_Controller_Action_Standard {

	public function init() {
		if( !$this->_helper->requireUser()->isValid() ) return;
	}

	public function targetstudentsAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$course = Engine_Api::_()->getItem('sitecourse_course', $this->_getParam('course_id'));

  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);
    	// Prepare form
		$this->view->form = $form = new Sitecourse_Form_Targetstudents();
		$this->view->course_id=$this->_getParam('course_id');
    	// parent type & parent id for video
		$this->view->parent_type= $parent_type = "course";
		$this->view->parent_id= $parent_id = $this->_getParam('course_id');

		$form->populate($course->toArray());

   		// check intro video exists or not
		$video = Engine_Api::_()->getDbtable('videos','sitecourse')->getVideoItem($parent_id,$parent_type);
		$intro_video = true;
		if($video && !empty($video)){
			$intro_video = false;
		}
		$this->view->intro_video = $intro_video;

   		// Check post/form
		if( !$this->getRequest()->isPost() ) {
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}

		$formValues = $form->getValues();

		$table = Engine_Api::_()->getItemTable('sitecourse_course');
		$db = $table->getAdapter();
		$db->beginTransaction();

		try{
			$course->modified_date = date('Y-m-d H:i:s');
			$course->setFromArray($formValues);
			$course->save();

			$db->commit();
		} catch( Exception $e ) {
			return $this->exceptionWrapper($e, $form, $db);
		}
		$form->addNotice("Changes added successfully");
	}

	public function topicsAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->course_id= $course_id = $this->_getParam('course_id', 0);
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);

  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$this->view->topics = $topics = Engine_Api::_()->getDbtable('topics','sitecourse')->fetchAllTopics($course_id);
		$lessons = Engine_Api::_()->getDbtable('lessons','sitecourse')->fetchAllLessons($course_id);
		$this->view->lessons= $lessons;
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);

		// get buyers table and buyers count
		$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		$this->view->buyersCount = $buyerdetailTable->courseEnrollementCount($course_id);
	}

	public function addTopicAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$course_id = $this->_getParam('course_id');
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);

  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$this->view->form = $form = new Sitecourse_Form_Topic();
		$form->setAction($this->view->url(array()));

  		// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('dashboard/topic_form.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('dashboard/topic_form.tpl');
			return;
		}
    	// Process
		$course_id = $this->_getParam('course_id');
		$value = $form->getValues();

		$table = Engine_Api::_()->getItemTable('sitecourse_topic');
		$db = $table->getAdapter();
		$db->beginTransaction();

		try {
			$values = array_merge($value,array('course_id'=>$course_id,'order'=>$table->getOrder($course_id)));
			$topic = $table->createRow();
			$topic->setFromArray($values);
			$topic->save();
      		// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Topic created successfully')
		));
	}


	public function editTopicAction() {
		$this->_helper->layout->setLayout('default-simple');

		$viewer = Engine_Api::_()->user()->getViewer();
		$topic_id = $this->_getParam('topic_id');
		$itemTopic = Engine_Api::_()->getItem('sitecourse_topic', $topic_id);
		$topic = $itemTopic->toArray();
		$course = Engine_Api::_()->getItem('sitecourse_course',$topic['course_id']);
  		// validate course and course owner
		if( !$topic || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		// get buyers table
		$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		/**
		 * get buyers count
		 * buyers count > 0 then return
		 */
		$buyersCount = $buyerdetailTable->courseEnrollementCount($topic['course_id']);
		if($buyersCount > 0) {
			return;
		}

		$this->view->form = $form = new Sitecourse_Form_Topic();
		$form->setAction($this->view->url(array()));
		$form->populate($topic);
    	// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('dashboard/topic_form.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('dashboard/topic_form.tpl');
			return;
		}
		$table = Engine_Api::_()->getItemTable('sitecourse_topic');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try {
			$values = $form->getValues();
			$itemTopic->setFromArray($values);
			$itemTopic->save();
      		// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Topic changed successfully')
		));
	}

	public function deleteTopicAction() {
		$this->_helper->layout->setLayout('default-simple');

		$viewer = Engine_Api::_()->user()->getViewer();
		$topic_id = $this->_getParam('topic_id');
		$course_id = $this->_getParam('course_id');
		$itemTopic = Engine_Api::_()->getItem('sitecourse_topic', $topic_id);

		$topic = $itemTopic->toArray();
		$course = Engine_Api::_()->getItem('sitecourse_course',$topic['course_id']);
    	// validate course and course owner
		if( !$topic || (!$course->isOwner($viewer)) || $itemTopic['course_id'] != $course_id ) {
			return $this->_helper->requireSubject->forward();
		}

		// get buyers table
		$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		/**
		 * get buyers count
		 * buyers count > 0 then return
		 */
		$buyersCount = $buyerdetailTable->courseEnrollementCount($topic['course_id']);
		if($buyersCount > 0) {
			return $this->_helper->requireSubject->forward();
		}
		
		$this->view->form = $form = new Sitecourse_Form_TopicDelete();
		$form->setAction($this->view->url(array()));

    	// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('dashboard/topic_form.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('dashboard/topic_form.tpl');
			return;
		}

    	//post data
		$formValues = $this->getRequest()->getPost();
    	// Process
		$topicTable = Engine_Api::_()->getItemTable('sitecourse_topic');
		$db = $topicTable->getAdapter();
		$db->beginTransaction();

		try {
      	    // delete lessons
			$lessons = Engine_Api::_()->getItemTable('sitecourse_lesson')->fetchAllLessons($itemTopic->getIdentity());
			foreach($lessons as $lesson){
				$lessonItem = Engine_Api::_()->getItem('sitecourse_lesson',$lesson['lesson_id']);
				$lessonItem->delete();
			}
      		//delete topic
			$itemTopic->delete();
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh'=> 10,
			'messages' => array('Lesson Deleted Successfully.')
		));
	}

	public function courseOverviewAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$course_id = $this->_getParam('course_id');
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);

    	// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);
		$this->view->form = $form = new Sitecourse_Form_Courseoverview();
		$this->view->course_id=$this->_getParam('course_id');
		$form->populate($course->toArray());

    	// Check post
		if( !$this->getRequest()->isPost() ) {
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
    	// Process
		$table = Engine_Api::_()->getItemTable('sitecourse_course');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try {
			$values = $form->getValues();
			$course->setFromArray($values);
			$course->save();
      		// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
		$form->addNotice("Changes added successfully");
	}

	public function addLessonAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$topic_id = $this->_getParam('topic_id');
		$topic = Engine_Api::_()->getItem('sitecourse_topic', $topic_id);
		$topicCourseId = $topic->toArray();
		$course = Engine_Api::_()->getItem('sitecourse_course',$topicCourseId['course_id']);

		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		$this->view->form = $form = new Sitecourse_Form_Lesson();
		$form->setAction($this->view->url(array()));
    	// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('dashboard/lesson_form.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('dashboard/lesson_form.tpl');
			return;
		}
    	// Process
		$topic_id = $this->_getParam('topic_id');
		$value = $form->getValues();
		$table = Engine_Api::_()->getItemTable('sitecourse_lesson');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try {
			$values = array_merge($value,array('topic_id'=>$topic_id,'order'=>9999,'type'=>'text','course_id'=>$course['course_id'],));
			$topic = $table->createRow();
			$topic->setFromArray($values);
			$topic->save();
      		// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Lesson created successfully')
		));
	}
	
	
	
	
	
	public function addLessonVideoAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$topic_id = $this->_getParam('topic_id');
		$topic = Engine_Api::_()->getItem('sitecourse_topic', $topic_id);
		$topicCourseId = $topic->toArray();
		$course = Engine_Api::_()->getItem('sitecourse_course',$topicCourseId['course_id']);

		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		$this->view->form = $form = new Sitecourse_Form_LessonVideo();
		$form->setAction($this->view->url(array()));
    	// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('dashboard/lesson_form_video.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('dashboard/lesson_form_video.tpl');
			return;
		}
    	// Process
		$topic_id = $this->_getParam('topic_id');
		$value = $form->getValues();
		$table = Engine_Api::_()->getItemTable('sitecourse_lesson');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try {
			$values = array_merge($value,array('topic_id'=>$topic_id,'order'=>9999,'type'=>'text','course_id'=>$course['course_id'],));
			$topic = $table->createRow();
			$topic->setFromArray($values);
			$topic->save();
      		// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Lesson created successfully')
		));
	}


	public function editLessonAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('default-simple');

		$viewer = Engine_Api::_()->user()->getViewer();
		$lesson_id = $this->_getParam('lesson_id');
		$lesson = Engine_Api::_()->getItem('sitecourse_lesson', $lesson_id);
		$lessonArray=$lesson->toArray();
		// fetch course
		$course = Engine_Api::_()->getItem('sitecourse_course',$lessonArray['course_id']);
		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$this->view->form = $form = new Sitecourse_Form_Lesson();
		$form->setAction($this->view->url(array()));
		$form->populate($lessonArray);

    	// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('dashboard/lesson_form.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('dashboard/lesson_form.tpl');
			return;
		}
    	// Process
		$value = $form->getValues();
		$table = Engine_Api::_()->getItemTable('sitecourse_lesson');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try {
			$values = $form->getValues();
			$lesson->setFromArray($values);
			$lesson->save();
      		// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Lesson edited successfully')
		));

	}

	public function introVideoAction() {
		$courseId = $this->_getParam('course_id');
		$viewer = Engine_Api::_()->user()->getViewer();
		$course = Engine_Api::_()->getItem('sitecourse_course',$courseId);
      	// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
      	//form
		$this->view->form = $form = new Sitecourse_Form_IntroVideo(array('parent_id'=>$courseId,'parent_type'=>'course'));

		$this->view->course_id = $courseId;
      	// Check post
		if( !$this->getRequest()->isPost() ) {
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}

      	// Process
		$value = $form->getValues();

		if(!$value['lesson_id']){
			return $form->addError('Please select a valid video');
		}
		$table = Engine_Api::_()->getItemTable('sitecourse_video');
		// check video exists
		$intro_video = $table->getVideoItem($courseId, 'course');
		// process
		$lesson = $table->getVideoItem($value['lesson_id'],'lesson');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try {
			$values = $lesson;
			unset($values['video_id']);
			$values['parent_type'] = 'course';
        	// get the parent id from controller;
			$values['parent_id'] = $courseId;
			if(empty($intro_video)) {
				$row = $table->createRow();
			} else {
				$row = Engine_Api::_()->getItem('sitecourse_video', $intro_video['video_id']);
			}
			$row->setFromArray($values);
			$row->save();
        	// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Lesson created successfully')
		));
	}

	public function deleteLessonAction() {
	    // In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$lesson_id = $this->_getParam('lesson_id');
	    //check viewer is the course owner
		$viewer = Engine_Api::_()->user()->getViewer();
		$lesson = Engine_Api::_()->getItem('sitecourse_lesson',$lesson_id);
		$course_id = $lesson['course_id'];
		$topic_id = $lesson['topic_id'];
		$course = Engine_Api::_()->getItem('sitecourse_course',$course_id);
	    // validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		// get buyers table
		$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
		/**
		 * get buyers count
		 * buyers count > 0 then return
		 */
		$buyersCount = $buyerdetailTable->courseEnrollementCount($course_id);
		if($buyersCount > 0) {
			return $this->_helper->requireSubject->forward();
		}

		$this->view->form = $form = new Sitecourse_Form_LessonDelete();

		$lessonsTable = Engine_Api::_()->getItemTable('sitecourse_lesson');
		$lesson = $lessonsTable->find($lesson_id)->current();

		if( !$lesson ) {
			return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('')
			));
		} else {
			$lesson_id = $lesson->getIdentity();
		}
	    //check post request
		if( !$this->getRequest()->isPost() ) {
	      // Output
			return;
		}
	    //post data
		$formValues = $this->getRequest()->getPost();
	    // Process
		$db = $lessonsTable->getAdapter();
		$db->beginTransaction();

		try {
	        // delete lesson
			switch($lesson['type']){
				case 'doc':
				$this->deleteDocEntry($lesson['lesson_id'],$viewer->getIdentity(),'lesson');
				break;
				case 'video':
				$this->deleteVideoEntry($lesson['lesson_id'],$viewer->getIdentity(),'lesson');
				break;
			}
			$lesson->delete();
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh'=> 10,
			'messages' => array('Lesson Deleted Successfully.')
		));
	}


	/**
	 * delete entry from sitecourse_video. If file type is upload then also
	 * remove the entry from storage table
	 * 
	 */
	private function deleteVideoEntry($parent_id, $user_id, $parent_type) {
		$videoTable = Engine_Api::_()->getItemTable('sitecourse_video');
		$video = $videoTable->getVideoItem($parent_id,$parent_type);
		$db = $videoTable->getAdapter();
		$db->beginTransaction();
		try {
	      // if type upload then also remove storage table entry
			if($video['type'] === 'upload'){
				// do file id also refer to intro video
				$storageItem = Engine_Api::_()->getItem('storage_file',$video['file_id']);
				if($storageItem)
					$storageItem->delete();
			}
			$item = Engine_Api::_()->getItem('sitecourse_video',$video['video_id']);
			if($item)
				$item->delete();
			$videoTable->delete(array('file_id = ?' => $video['file_id']));
		}catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
	}
	// remove doc entry from storage table
	private function deleteDocEntry($parent_id, $user_id, $parent_type) {
		$storageTable = Engine_Api::_()->getItemTable('storage_file');

		$db = $storageTable->getAdapter();
		$db->beginTransaction();
		try {
			$storageTable->delete(array(
				'parent_type'=>$parent_type,
				'parent_id'=>$parent_id,
				'user_id'=>$user_id
			));
		}catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
	}

	public function getlessonsAction() {
		$topic_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('topic_id');
		$topic = Engine_Api::_()->getItem('sitecourse_topic', $topic_id);
		if(!$topic || !$topic->getIdentity()) {
			return $this->_helper->requireSubject->forward();	
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$course = Engine_Api::_()->getItem('sitecourse_course',$topic->course_id);
	    // validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		$this->view->lessons = Engine_Api::_()->getItemTable('sitecourse_lesson')->fetchAllVideoLessons($topic_id);

	}

	public function getvideoAction() {
		$lesson_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('lesson_id', 0);
		$lesson = Engine_Api::_()->getItem('sitecourse_lesson', $lesson_id);
		if(!$lesson || !$lesson->getIdentity()) {
			return $this->_helper->requireSubject->forward();	
		}
		$course = Engine_Api::_()->getItem('sitecourse_course',$lesson->course_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		
	    // validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		$videoItem = Engine_Api::_()->getItemTable('sitecourse_video')->getVideoItem($lesson_id,'lesson');

	    // get the video from the storage db
		if($videoItem['type'] == 'upload'){
			$this->view->video_type = 'upload';
			$video = Engine_Api::_()->getItem('storage_file',$videoItem['file_id']);
			$this->view->video = $video->toArray();
		}else{
			$this->view->video = array_merge(array('path'=>$this->getVideoURL($videoItem['type'],$videoItem['code'])),$videoItem);
		}
		$this->view->video_url = $video_url = Engine_Api::_()->sitecourse()->getVideoURL($videoItem,false);
	}

	public function orderchangeAction() {
		$src = Zend_Controller_Front::getInstance()->getRequest()->getParam('src');
		$dest = Zend_Controller_Front::getInstance()->getRequest()->getParam('dest');

		Engine_Api::_()->getItemTable('sitecourse_topic')->updateOrder($src,$dest);

		$this->view->changed = true; 
	}

	private function getVideoURL($video, $code, $autoplay = true) {
	     // YouTube
		if ($video == 'youtube') 
		{
			return 'www.youtube.com/embed/' . $code . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "");
		} 
		elseif ($video == 'vimeo') 
		{ 
	        // Vimeo
			return 'player.vimeo.com/video/' . $code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
		} 
		elseif ($video == 'dailymotion')
		{
			return 'www.dailymotion.com/embed/video/' . $code . '?wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
		}
	}

	public function announcementsAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->course_id = $course_id = $this->_getParam('course_id');
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.announcements',1)){
			return $this->_helper->requireSubject->forward();
		}
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);

		$this->view->course_id = $course_id;

		if($this->getRequest()->isPost()){			
			$values = $this->getRequest()->getPost();
			$itemTable = Engine_Api::_()->getItemTable('sitecourse_announcement');
			$db = $itemTable->getAdapter();
			$db->beginTransaction();
			try{
				$enable = ($values['enable'] == 'true')?1:0;
				$itemTable->update(array(
					'enable' => $enable,
				), array(
					'announcement_id = ?' => $values['id'],
				));
				foreach($announcements as $idx => $announcement){

					if($values['id'] == $announcement['announcement_id'])
						$announcements[$idx]['enable'] = $enable;
				}
				$db->commit();
			}catch(Exception $e){
				$db->rollBack();
				throw $e;			
			}
		}

		$paginator = Engine_Api::_()->getItemTable('sitecourse_announcement')->getAnnouncementPaginator($course_id);
		$items_per_page = 5;
		$paginator->setItemCountPerPage($items_per_page);
		$this->view->paginator = $paginator->setCurrentPageNumber( $this->_getParam('page') );
	}

	public function createAnnouncementAction() {
	    // In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$course_id = $this->_getParam('course_id');
		$viewer = Engine_Api::_()->user()->getViewer();
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		$totalActiveAnnouncementsCount = Engine_Api::_()->getDbtable('announcements','sitecourse')->getTotalActiveAnnouncementsCount($course_id);


  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		$this->view->form = $form = new Sitecourse_Form_Announcement();

		if(!$this->getRequest()->isPost()){
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
		if($totalActiveAnnouncementsCount > 9){
			return $form->addError('Max limit reached');
		}
      	// Process
		$formValue = $form->getValues();
		$responseData = $this->isValidDateInterval($formValue['start_date'],$formValue['end_date']);
		if(!$responseData['valid']){
			return $form->addError($responseData['error']);
		}

		$dbTable = Engine_Api::_()->getDbtable('announcements','sitecourse');
		$db = $dbTable->getAdapter();
		$db->beginTransaction();

		try{
			$formValue['course_id'] = $course_id;
			$row = $dbTable->createRow();
			$row->setFromArray($formValue);
			$row->save();
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Announcement Created Successfully.')
		));

	}

	public function deleteAnnouncementAction() {
		 // In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$announcement_id = $this->_getParam('announcement_id');
		$announcement = Engine_Api::_()->getItem('sitecourse_announcement',$announcement_id);
		$course = Engine_Api::_()->getItem('sitecourse_course', $announcement->course_id);
  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		$this->view->form = $form = new Sitecourse_Form_AnnouncementDelete();
		// post request
		if(!$this->getRequest()->isPost()){
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
      	// Process
		$formValue = $form->getValues();

		$db = $announcement->getTable()->getAdapter();
		$db->beginTransaction();

		try{
			$announcement->delete();
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Announcement Deleted Successfully.')
		));

	}

	private function isValidDateInterval($start_date,$end_date) {
		$output = array('valid'=>false,'error'=>'0');
		/**
		 * calculate the diff between end date and start date
		 * based on output first char(+|-) 
		 * + end date > start date vice versa
		 * same for the todays date and start day
		 */
		$todayDate = date_create(date('Y-m-d'));
		$startDate = date_create($start_date);
		$endDate = date_create($end_date);
		$diff =  date_diff($startDate,$endDate);
		$diff = $diff->format("%R%a days");
		if($diff[0] == '-'){
			$output['error'] = "End date must finish after Start Date";
			return $output;
		}
		// start date must start from today or date after today
		$diff = date_diff($todayDate,$startDate);
		$diff = $diff->format("%R%a days");

		if($diff[0] == '-'){
			$output['error'] = "Start Date must start from today or after today.";
			return $output;
		}
		$output['valid'] = true;
		return $output;
	}

	public function editAnnouncementAction() {
	    // In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$announcement_id = $this->_getParam('announcement_id');
		$announcement =  Engine_Api::_()->getItem('sitecourse_announcement',$announcement_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$course = Engine_Api::_()->getItem('sitecourse_course', $announcement->course_id);
  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$this->view->form = $form = new Sitecourse_Form_Announcement();
		$form->populate($announcement->toArray());
		// post request
		if(!$this->getRequest()->isPost()){
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
      	// Process
		$formValue = $form->getValues();
		$responseData = $this->isValidDateInterval($formValue['start_date'],$formValue['end_date']);
		if(!$responseData['valid']){
			return $form->addError($responseData['error']);
		}

		$dbTable = Engine_Api::_()->getDbtable('announcements','sitecourse');
		$db = $dbTable->getAdapter();
		$db->beginTransaction();

		try{
			$announcement->setFromArray($formValue);
			$announcement->save();
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Announcement Edited Successfully.')
		));

	}

	public function enrolledMembersAction() {
		$this->view->course_id = $course_id = $this->_getParam('course_id');
		$viewer = Engine_Api::_()->user()->getViewer();
		$course = Engine_Api::_()->getItem('sitecourse_course', $this->_getParam('course_id'));
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);
  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}

		$paginator = Engine_Api::_()->getItemTable('sitecourse_buyerdetail')->getEnrolledPaginator($course_id);
		$items_per_page = 10;
		$paginator->setItemCountPerPage($items_per_page);
		$this->view->paginator = $paginator->setCurrentPageNumber( $this->_getParam('page') );
	}


	public function transactionsAction() {
		$this->view->course_id = $course_id = $this->_getParam('course_id',0);
		$viewer = Engine_Api::_()->user()->getViewer();
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);
  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$params = array('course_id' => $course_id);
		// search params is given as get request
		if(!empty($_GET)) {
			$params['search'] = $_GET;
		}
		// process paginator
		$paginator = Engine_Api::_()->getDbtable('transactions','sitecourse')->getCourseTransactionsPaginator($params);
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->paginator = $paginator;

	}

	//ACTION FOR CHANGING THE COURSE PROFILE PICTURE
	public function coursePictureAction() {
        //GET COURSE ID
		$this->view->course_id = $course_id = $this->_getParam('course_id');
		$viewer = Engine_Api::_()->user()->getViewer();
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);
        //GET SITECOURSE ITEM
		$this->view->sitecourse = $sitecourse = Engine_Api::_()->getItem('sitecourse_course', $course_id);
         //GET FORM
		$this->view->form = $form = new Sitecourse_Form_Photo();

        //CHECK FORM VALIDATION
		if (!$this->getRequest()->isPost()) {
			return;
		}

        //CHECK FORM VALIDATION
		if (!$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		if ($form->Signature->getValue() !== null) {
            //GET DB
			$db = $sitecourse->getTable()->getAdapter();
			$db->beginTransaction();
            //PROCESS
			try {
                //SET PHOTO
				$sitecourse->setPhoto($form->Signature,'sitecourse_signature');
				$db->commit();
			} catch (Engine_Image_Adapter_Exception $e) {
				$db->rollBack();
				$form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
			} catch (Exception $e) {
				$db->rollBack();
				throw $e;
			}
		} else if ($form->getValue('coordinates2') !== '') {
			$storage = Engine_Api::_()->storage();
			$iProfile = $storage->get($sitecourse->signaturePhoto_id, 'thumb.profile');
			$pName = $iProfile->getStorageService()->temporary($iProfile);
			$iName = dirname($pName) . '/nis_' . basename($pName);
			list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates2'));
			$image = Engine_Image::factory();
			$image->open($pName)
			->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
			->write($iName)
			->destroy();
			$iSquare->store($iName);
			@unlink($iName);
		}
        //UPLOAD PHOTO
		if ($form->Filedata->getValue() !== null) {
            //GET DB
			$db = $sitecourse->getTable()->getAdapter();
			$db->beginTransaction();
            //PROCESS
			try {
                //SET PHOTO
				$sitecourse->setPhoto($form->Filedata);
				$db->commit();
			} catch (Engine_Image_Adapter_Exception $e) {
				$db->rollBack();
				$form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
			} catch (Exception $e) {
				$db->rollBack();
				throw $e;
			}
		} else if ($form->getValue('coordinates') !== '') {
			$storage = Engine_Api::_()->storage();
			$iProfile = $storage->get($sitecourse->photo_id, 'thumb.profile');
			$pName = $iProfile->getStorageService()->temporary($iProfile);
			$iName = dirname($pName) . '/nis_' . basename($pName);
			list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
			$image = Engine_Image::factory();
			$image->open($pName)
			->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
			->write($iName)
			->destroy();
			$iSquare->store($iName);
			@unlink($iName);
		}

		return $this->_helper->redirector->gotoRoute(array('action' => 'course-picture', 'course_id' => $course_id), 'sitecourse_dashboard', true);
	}

	public function courseIntroVideoAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$course_id = $this->_getParam('course_id');
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);

  		// validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}		
		// course info
		$this->view->course_id = $course_id;
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);
		// validate already existing video
		$video = Engine_Api::_()->getItemTable('sitecourse_video')->getVideoItem($course_id, 'course');
		$video_url = $video_type = null;
		if(!empty($video)) {
			$video_url = Engine_Api::_()->sitecourse()->getVideoURL($video,false);
			$video_type = $video['type'];
		}
		$this->view->video_url = $video_url; 
		$this->view->video_type = $video_type;
	}

}

