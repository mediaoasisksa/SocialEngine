<?php
class Sitecourse_LearningController extends Core_Controller_Action_Standard {

	public function init() {
		if( !$this->_helper->requireUser()->isValid() ) return;
		$checks = new Sitecourse_Api_Checks();
		$course_id=$this->_getParam('course_id');
		// type of viewer
		$viewerType = $checks->viewerType($course_id);
		// viewer is not (owner,admin,buyer)
		if(!in_array($viewerType,$checks->_viewerType)) {
			$this->_helper->requireSubject->forward();
		}
	}

	public function indexAction() {
		$this->_helper->content
		->setEnabled();		
	}

	public function toggleTopiccompleteAction() {
		$course_id = $this->_getParam('course_id');
		$topic_id = $this->_getParam('topic_id');
		$lesson_id = $this->_getParam('lesson_id');

		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		$dbTable = Engine_Api::_()->getDbtable('completedlessons','sitecourse');
		$isCompleted = $dbTable->isCompleted($course_id,$lesson_id,$topic_id,$viewer_id);

		$db = $dbTable->getAdapter();
		$db = $db->beginTransaction();

		try{
			if(!$isCompleted){
				$row = $dbTable->createRow();
				$row->setFromArray(array(
					'course_id' => $course_id,
					'user_id' => $viewer_id,
					'lesson_id' => $lesson_id,
					'topic_id' => $topic_id
				));
				$row->save();
				$this->view->completed = true;
			}
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			throw $e;
		}

	}

	public function downloadAction() {
		$file= $this->_getParam('lesson_id');
		$storageTable = Engine_Api::_()->getDbTable('files', 'storage');
		$select = $storageTable->select()->from($storageTable->info('name'), array('storage_path', 'name'))
		->where('parent_id = ?', $file)
		->where('parent_type = ?','lesson');
		$storageData = $storageTable->fetchRow($select);

		$basePath = APPLICATION_PATH . '/' . $storageData->storage_path;

		$storageData = (object) $storageData->toArray();
		if (empty($storageData->name) || $storageData->name == '' || empty($storageData->storage_path) || $storageData->storage_path == '')
			return;

		@chmod($basePath, 0777);
		header("Content-Disposition: attachment; filename=" . urlencode(basename($storageData->name)), true);
		header("Content-Transfer-Encoding: Binary", true);
		header("Content-Type: application/force-download", true);
		header("Content-Type: application/octet-stream", true);
		header("Content-Type: application/download", true);
		header("Content-Description: File Transfer", true);
		header("Content-Length: " . filesize($basePath), true);
		readfile("$basePath");
		exit();
      	// for safety resason double check
		return;
	}

	public function displayAction() {
		$lesson_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('lesson_id');
		$this->view->lesson = $lesson = Engine_Api::_()->getItemTable('sitecourse_lesson')->getLesson($lesson_id);
		if($lesson['type'] == 'video'){
			$lesson_id = $lesson['lesson_id'];
			$video = Engine_Api::_()->getItemTable('sitecourse_video')->getVideoItem($lesson_id,'lesson');
			$this->view->video_url = $video_url = Engine_Api::_()->sitecourse()->getVideoURL($video,false);	
			$this->view->video_type = $lesson['type'];
		} elseif($lesson['type'] == 'doc'){
			$this->view->docDownloadUrl =  $this->view->htmlLink(array('route' => 'sitecourse_learning_specific', 'module' => 'sitecourse', 'controller' => 'learning', 'action' => 'download', 'course_id' => $lesson['course_id'],'lesson_id' => $lesson['lesson_id'] ,),$this->view->translate('To Download the Lesson Click Here'),array());
		}
	}

	public function previewCertificateAction() {		
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_title = $viewer->getTitle();

		$this->view->course_id = $course_id = $this->_getParam('course_id');
		$course = Engine_Api::_()->getItem('sitecourse_course',$course_id);

		if(!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'certification')){
			return $this->_helper->redirector->gotoRoute(array('action' => 'index','course_id'=>$course_id ), 'sitecourse_learning', true);
		}

		$lessons = Engine_Api::_()->getDbtable('lessons','sitecourse')->fetchAllLessons($course_id);
		$lessonCount = count($lessons);

		$completedLessons = Engine_Api::_()->getDbtable('completedlessons','sitecourse')->fetchCompletedLessons($course_id,$viewer->getIdentity());

		$completedLessonCount = count($completedLessons);

		if($lessonCount != $completedLessonCount){
			return $this->_helper->redirector->gotoRoute(array('action' => 'index','course_id'=>$course_id ), 'sitecourse_learning', true);
		}

		$issuedCertificate = Engine_Api::_()->getDbtable('completedcourses','sitecourse')->checkIssuedCertificate($course_id);

		$this->_helper->layout->setLayout('default-simple');
		
		
		$bodyHTML = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.format.bodyhtml');

		$company_logo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.companylogo');

		$background_image = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.cbackground');

		if(!$background_image){
			$background_image = 'application/modules/Sitecourse/externals/images/backgroundImage.jpg';
		}
		if(!$company_logo){
			$company_logo = 'application/modules/Sitecourse/externals/images/companyLogo.png';
		}

		$storage_file = Engine_Api::_()->getItem('storage_file', $course->signaturePhoto_id);
		$src= 'application/modules/Sitecourse/externals/images/transparent.png';
		if(!empty($storage_file)){
			//$storage_file->map();
			$src=$storage_file->map(); 
		}

		$completion_date = date("d-m-Y");

		$course_name = $course['title'];

		$owner = Engine_Api::_()->getItem('user',$course['owner_id']); 

		$placehoders = array("[Student_Name]", "[Hours]", "[Course_Name]", "[Date]", "[Creator_Name]", "[Signature]","[Company_Logo]","[Background_Image]");

		$commonValues = array($viewer_title, $course['duration'], $course_name, $completion_date, $owner['displayname'], $src, $company_logo, $background_image);

		$this->view->bodyHTML = str_replace($placehoders, $commonValues, $bodyHTML);

		$completedCourseTable = Engine_Api::_()->getDbtable('completedcourses', 'sitecourse');
		$db = $completedCourseTable->getAdapter();
		$db->beginTransaction();
		try {
			$completedCourseTable->setInfo($course_id);
			$db->commit();			
			$owner = Engine_Api::_()->getItem("user" , $course->owner_id);
			if(!$issuedCertificate){
				Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($viewer,$owner, $course, 'sitecourse_certificate',array(
					'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
					'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
				Engine_Api::_()->sitecourse()->sendMail('CERTIFICATE', $course->course_id);

			}
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
		
	}
}
?>
