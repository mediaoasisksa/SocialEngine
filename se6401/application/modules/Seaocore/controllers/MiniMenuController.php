<?php

class Seaocore_MiniMenuController extends Core_Controller_Action_Standard
{

  public function messageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->noOfUpdates = $noOfUpdates = $this->_getParam('noOfUpdates', 10);
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getInboxPaginator($viewer);
    $paginator->setItemCountPerPage($noOfUpdates);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);

    Engine_Api::_()->getApi('updates', 'seaocore')->markMessagesAsShow($viewer);
  }

  public function settingAction()
  {
    $this->view->settings = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("user_settings", array());
    // Check last super admin
    $user = Engine_Api::_()->user()->getViewer();
    if( $user && $user->getIdentity() ) {
      if( 1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $user->level_id ) {
        foreach( $navigation as $page ) {
          if( $page instanceof Zend_Navigation_Page_Mvc &&
            $page->getAction() == 'delete' ) {
            $navigation->removePage($page);
          }
        }
      }
    }
  }

  public function friendRequestAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->noOfUpdates = $noOfUpdates = $this->_getParam('noOfUpdates', 10);

    $this->view->showSuggestion = $this->_getParam('showSuggestion', 0);
    $this->view->requests = $friendRequests = Engine_Api::_()->getApi('updates', 'seaocore')->getRequestsPaginator($viewer);

    $friendRequests->setItemCountPerPage($noOfUpdates);

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);
    Engine_Api::_()->getApi('updates', 'seaocore')->markFriendRequstAsShow($viewer);
  }

  public function notificationAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->noOfUpdates = $noOfUpdates = $this->_getParam('noOfUpdates', 10);

    $notifications_sql = Engine_Api::_()->getApi('updates', 'seaocore')->getNotificationsPaginatorSql($viewer);
    $this->view->notifications = $notifications = Zend_Paginator::factory($notifications_sql);
    $notifications->setItemCountPerPage($noOfUpdates);

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);

    $this->view->hasunread = false;
    Engine_Api::_()->getApi('updates', 'seaocore')->markUpdatesAsShow($viewer);
  }

  public function markNotificationsAsReadAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    Engine_Api::_()->getApi('updates', 'seaocore')->markNotificationsAsRead($viewer);
  }

  public function markMessageReadUnreadAction()
  {
    $message_id = $this->_getParam('messgae_id', null);
    $is_read = $this->_getParam('is_read', 0);
    if( empty($message_id) ) {
      return;
    }

    ngine_Api::_()->getApi('updates', 'seaocore')->markMessageReadUnread($message_id, $is_read);
  }

  public function checkNewUpdatesAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->newMessage = Engine_Api::_()->getApi('updates', 'seaocore')->getUnreadMessageCount($viewer);

    $this->view->newFriendRequest = Engine_Api::_()->getApi('updates', 'seaocore')->getNewFriendRequestCount($viewer);

    $this->view->newNotification = Engine_Api::_()->getApi('updates', 'seaocore')->getNewUpdatesCount($viewer);
  }

}