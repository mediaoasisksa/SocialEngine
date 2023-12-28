<?php

class Sitecourse_DocController extends Core_Controller_Action_Standard {

	public function addDoclessonAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('default-simple');

		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$topic_id = $this->_getParam('topic_id');
		$topic = Engine_Api::_()->getItem('sitecourse_topic', $topic_id);
		$topicCourseId = $topic->toArray();
		$course = Engine_Api::_()->getItem('sitecourse_course',$topicCourseId['course_id']);
    	//has permission to create course
		if( !$this->_helper->requireAuth()->setAuthParams('sitecourse_course', $viewer, 'create')->isValid() ) {
			return;
		}

		$this->view->form = $form = new Sitecourse_Form_Doclesson();
		//$form->setAction($this->view->url(array()));
    	// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('doc/doclesson_form.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('doc/doclesson_form.tpl');
			return;
		}
    	// Process
		$topic_id = $this->_getParam('topic_id');
		$value = $form->getValues();

		$table = Engine_Api::_()->getItemTable('sitecourse_lesson');
		$storage = Engine_Api::_()->getItemTable('storage_file');
		$db = $table->getAdapter();
		$db->beginTransaction();

		try {
			$values = array_merge($value,array('topic_id'=>$topic_id,'order'=>9999,'type'=>'doc','course_id'=>$course['course_id'],));
			$lesson = $table->createRow();
			$lesson->setFromArray($values);
			$lesson->save();

			if (!empty($values['filename'])) {
				$local_info = $lesson->setFile($form->filename);
			}
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
}
?>
