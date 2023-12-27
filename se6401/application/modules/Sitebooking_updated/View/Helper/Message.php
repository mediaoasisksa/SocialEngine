<?php class Sitebooking_View_Helper_Message extends Zend_View_Helper_Abstract {

  public function Message($user, $viewer = null) {

  if (null === $viewer) {
    $viewer = Engine_Api::_()->user()->getViewer();
  }

  if (!$viewer->getIdentity() || $viewer->getGuid(false) === $user->getGuid(false)) {
    return '';
  }
  
  if(!$user->getIdentity())
    return '';

  $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
  if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
    return '';
  }

  $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
  if ($messageAuth == 'none') {
    return false;
  } 

  return $this->view->htmlLink(array('route' => 'sitebooking_messages_general', 'action' => 'compose', 'to' => $user->getIdentity()), '' . $this->view->translate('Send Message'), array('class' => 'smoothbox buttonlink'));
  }

}
?>