<?php
class Sitebooking_MessageController extends Core_Controller_Action_Standard
{
  public function composeAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$recipients = $this->_getParam('to');
		$recipientItem = Engine_Api::_()->getItem('user',$recipients);
		$attachment = null;

		$recipientName['name'] = $recipientItem->displayname;
		$this->view->form = $form = new Sitebooking_Form_Compose($recipientName);
		// Check method/data
		if (!$this->getRequest()->isPost()) {
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		$values = $form->getValues();

		//PROCESS.
		$db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
		$db->beginTransaction();

		try {
			$conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'], $attachment);
		  //Increment messages counter
			Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
			$db->commit();

			$form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'));

			$this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => true,
						// 'parentRefresh' => true,
						'class' => 'global_form_popup',
						'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
						'format' => 'smoothbox'
					));
		} 
		catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
  }
}
