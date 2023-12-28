<?php

class Seaocore_Api_Updates extends Core_Api_Abstract
{

  /**
   * Get a paginator for friend-request or friend-follow request
   *
   * @param User_Model_User $user
   * @return Zend_Paginator
   */
  public function getRequestsPaginator(User_Model_User $user)
  {
    return Zend_Paginator::factory($this->getRequestsSelect($user));
  }

  public function getRequestsSelect(User_Model_User $user)
  {
    $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notificationTypeTable = Engine_Api::_()->getDbtable('notificationTypes', 'activity');
    $enabledNotificationTypes = array();
    foreach( $notificationTypeTable->getNotificationTypes() as $type ) {
      if( !in_array($type->type, array('friend_request', 'friend_follow_request')) || !$type->is_request ) {
        continue;
      }
      $enabledNotificationTypes[] = $type->type;
    }

    $select = $notificationTable->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('type IN(?)', $enabledNotificationTypes)
      ->where('mitigated = ?', 0)
      ->order('date DESC');

    return $select;
  }

  /**
   * Get a paginator for notifications
   *
   * @param User_Model_User $user
   * @return Zend_Paginator
   */
  public function getNotificationsPaginatorSql(User_Model_User $user)
  {
    $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notificationTypeTable = Engine_Api::_()->getDbtable('notificationTypes', 'activity');

    $excludeNotifications = array(
      'message_new', 'friend_request', 'friend_follow_request'
    );
    $enabledNotificationTypes = array();
    foreach( $notificationTypeTable->getNotificationTypes() as $type ) {
      if( in_array($type->type, $excludeNotifications) ) {
        continue;
      }
      $enabledNotificationTypes[] = $type->type;
    }
    $select = $notificationTable->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('`type` IN(?)', $enabledNotificationTypes)
      ->order('date DESC')
      ->limit(100);

    return $select;
  }

  /**
   * Does the user have new updates, returns the number or 0
   *
   * @param User_Model_User $user
   * @param $params
   * @return int The number of new updates the user has
   */
  public function getNewUpdatesCount(User_Model_User $user)
  {
    $notification_id = Engine_Api::_()->getDbtable('userSettings', 'seaocore')->getSetting($user, 'seao_activity_update_view', 0);
    $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notificationTableName = $notificationTable->info('name');

    $select = $notificationTable->select()
      ->from($notificationTableName, "COUNT(notification_id)")
      ->where("$notificationTableName.user_id = ?", $user->getIdentity())
      ->where("$notificationTableName.read = ?", 0)
      ->where("$notificationTableName.notification_id > ?", $notification_id)
      ->where("$notificationTableName.type NOT IN (?)", array('message_new', 'friend_request', 'friend_follow_request'));


    $newUpdatesCount = $select->query()->fetchColumn();
    return empty($newUpdatesCount) ? false : $newUpdatesCount;
  }

  /**
   * Does the user have new updates, returns the number or 0
   *
   * @param User_Model_User $user
   * @param $params
   * @return int The number of new updates the user has
   */
  public function getNewFriendRequestCount(User_Model_User $user, $params = array())
  {
    $notification_id = Engine_Api::_()->getDbtable('userSettings', 'seaocore')->getSetting($user, 'seao_activity_friendreq_view', 0);
    $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notificationTableName = $notificationTable->info('name');

    $select = $notificationTable->select()
      ->from($notificationTableName, "COUNT(notification_id)")
      ->where("$notificationTableName.user_id = ?", $user->getIdentity())
      ->where("$notificationTableName.read = ?", 0)
      ->where("$notificationTableName.notification_id > ?", $notification_id)
      ->where("$notificationTableName.type IN(?) ", array('friend_request', 'friend_follow_request'));

    $newUpdatesCount = $select->query()->fetchColumn();
    return empty($newUpdatesCount) ? false : $newUpdatesCount;
  }

  /**
   * Does the user have new messages, returns the number or 0
   *
   * @param User_Model_User $user
   * @return int The number of new messages the user has
   */
  public function getUnreadMessageCount(User_Model_User $user)
  {
    $inbox_message_lastUpdated = Engine_Api::_()->getDbtable('userSettings', 'seaocore')->getSetting($user, 'seao_message_inbox_view', 0);

    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $count = Engine_Api::_()->getDbtable('recipients', 'messages')->select()
      ->from($rName, new Zend_Db_Expr('COUNT(conversation_id) AS unread'))
      ->where($rName . '.user_id = ?', $user->getIdentity())
      ->where($rName . '.inbox_deleted = ?', 0)
      ->where($rName . '.inbox_read = ?', 0)
      ->where($rName . '.inbox_updated > ?', $inbox_message_lastUpdated)
      ->query()
      ->fetchColumn();

    return empty($count) ? false : $count;
  }

  /**
   * Mark all new unread notifications and messages as show
   *
   * @param User_Model_User $user
   * @param array $ids
   * @return object
   */
  public function markUpdatesAsShow(User_Model_User $user)
  {
    $table = Engine_Api::_()->getDbtable('notifications', 'activity');

    $results = $table->fetchRow($this->getNotificationsPaginatorSql($user));
    $notification_id = $results ? $results->notification_id : 0;
    Engine_Api::_()->getDbtable('userSettings', 'seaocore')->setSetting($user, 'seao_activity_update_view', $notification_id);
    return $this;
  }

  /**
   * Mark all new unread messages as show
   *
   * @param User_Model_User $user
   * @param array $ids
   * @return object
   */
  public function markMessagesAsShow(User_Model_User $user)
  {
    $table = Engine_Api::_()->getDbtable('recipients', 'messages');
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $select = $table->select()
      ->from($rName)
      ->where("`{$rName}`.`user_id` = ?", $user->getIdentity())
      ->where("`{$rName}`.`inbox_deleted` = ?", 0)
      ->order(new Zend_Db_Expr('inbox_updated DESC'));

    $results = $table->fetchRow($select);
    $messageUpdateTime = $results ? $results->inbox_updated : 0;
    Engine_Api::_()->getDbtable('userSettings', 'seaocore')->setSetting($user, 'seao_message_inbox_view', $messageUpdateTime);

    return $this;
  }

  /**
   * Mark all new unread messages as show
   *
   * @param User_Model_User $user
   * @param array $ids
   * @return object
   */
  public function markFriendRequstAsShow(User_Model_User $user)
  {


    $table = Engine_Api::_()->getDbtable('notifications', 'activity');

    $results = $table->fetchRow($this->getRequestsSelect($user));
    $notification_id = $results ? $results->notification_id : 0;
    Engine_Api::_()->getDbtable('userSettings', 'seaocore')->setSetting($user, 'seao_activity_friendreq_view', $notification_id);

    return $this;
  }

  /**
   * Mark all new unread messages as show
   *
   * @param User_Model_User $user
   * @param array $ids
   * @return object
   */
  public function markNotificationsAsRead(User_Model_User $user)
  {
    $where = array(
      '`user_id` = ?' => $user->getIdentity(),
      '`type` !=?' => 'friend_request',
      '`type` !=?' => 'message_new'
    );

    Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('read' => 1), $where);

    return $this;
  }

  public function markMessageReadUnread($conversation_id, $is_read = false)
  {
    $table = Engine_Api::_()->getDbtable('recipients', 'messages');
    $table->update(array('inbox_read' => $is_read ? 1 : 0), array('conversation_id =?' => $conversation_id));
  }

}