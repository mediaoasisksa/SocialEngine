<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Activity_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Add a notification
     *
     * @param User_Model_User $user The user to receive the notification
     * @param Core_Model_Item_Abstract $subject The item responsible for causing the notification
     * @param Core_Model_Item_Abstract $object Bleh
     * @param string $type
     * @param array $params
     * @return Activity_Model_Notification
     */
    public function addNotification(User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $type, array $params = null) {
        if ((Engine_Api::_()->hasModuleBootstrap('siteiosapp') || Engine_Api::_()->hasModuleBootstrap('siteandroidapp')) && Engine_Api::_()->getApi('settings', 'core')->getSetting('notification.queueing', 1)) {
            return $this->addNotificationQueues($user,$subject,$object,$type,$params);
        }
        $notificationSettingsTable = Engine_Api::_()->getDbtable('notificationSettings', 'activity');
        $notEnable = $notificationSettingsTable->checkEnabledNotification($user, $type);
        if(empty($notEnable))
            return;
        
        $notifyTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        // We may want to check later if a request exists of the same type already
        $row = $notifyTable->createRow();
        $row->user_id = $user->getIdentity();
        $row->subject_type = $subject->getType();
        $row->subject_id = $subject->getIdentity();
        $row->object_type = $object->getType();
        $row->object_id = $object->getIdentity();
        $row->type = $type;
        $row->params = $params;
        $row->date = date('Y-m-d H:i:s');
        $row->save();

        // Try to add row to caching
//        if (Zend_Registry::isRegistered('Zend_Cache')) {
//            $cache = Zend_Registry::get('Zend_Cache');
//            $id = __CLASS__ . '_new_' . $user->getIdentity();
//            $cache->save(true, $id);
//        }
        // Try to send an email
        $notificationSettingsTable = Engine_Api::_()->getDbtable('notificationSettings', 'activity');

        if ($notificationSettingsTable->checkEnabledNotification($user, $type) && !empty($user->email)) {
//      $view = Zend_Registry::get('Zend_View');
//      echo 'ter'; die;
            $sender_photo = $subject->getPhotoUrl('thumb.icon');

            if (!$sender_photo) {
                $sender_photo = '/' . Engine_Api::_()->getApi('Siteapi_Core', 'core')->getNoPhoto($subject, 'thumb.icon');
            }

            $recipient_photo = $user->getPhotoUrl('thumb.icon');
            if (!$recipient_photo) {
                $recipient_photo = '/' . Engine_Api::_()->getApi('Siteapi_Core', 'core')->getNoPhoto($user, 'thumb.icon');
            }

            // Main params
            $defaultParams = array(
                'host' => $_SERVER['HTTP_HOST'],
                'email' => $user->email,
                'date' => time(),
                'recipient_title' => $user->getTitle(),
                'recipient_link' => $user->getHref(),
                'recipient_photo' => $recipient_photo,
                'sender_title' => $subject->getTitle(),
                'sender_link' => $subject->getHref(),
                'sender_photo' => $sender_photo,
                'object_title' => $object->getTitle(),
                'object_photo' => $object->getPhotoUrl('thumb.icon'),
                'object_description' => $object->getDescription(),
            );

            $getAPIModulesName = Engine_Api::_()->getApi('Core', 'siteapi')->getAPIModulesName();
            $getModuleName = $object->getModuleName();
            $getModuleName = @strtolower($getModuleName);
            if (in_array($getModuleName, $getAPIModulesName) || $getModuleName == 'user') {
                $defaultParams['object_link'] = $object->getHref();
            }

            // Extra params
            try {
                $objectParent = $object->getParent();
                if ($objectParent && !$objectParent->isSelf($object)) {
                    $defaultParams['object_parent_title'] = $objectParent->getTitle();
                    $defaultParams['object_parent_link'] = $objectParent->getHref();
                    $defaultParams['object_parent_photo'] = $objectParent->getPhotoUrl('thumb.icon');
                    $defaultParams['object_parent_description'] = $objectParent->getDescription();
                }
            } catch (Exception $e) {
                
            }
            try {
                $objectOwner = $object->getParent();
                if ($objectOwner && !$objectOwner->isSelf($object)) {
                    $defaultParams['object_owner_title'] = $objectOwner->getTitle();
                    $defaultParams['object_owner_link'] = $objectOwner->getHref();
                    $defaultParams['object_owner_photo'] = $objectOwner->getPhotoUrl('thumb.icon');
                    $defaultParams['object_owner_description'] = $objectOwner->getDescription();
                }
            } catch (Exception $e) {
                
            }
            // Send
            try {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'notify_' . $type, array_merge($defaultParams, (array) $params));
            } catch (Exception $e) {
                // Silence exception
            }
        }

        //SEND PUSH NOTIFICATION WORK
        //To check notification from being send twice.
//        try {
//            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteandroidapp')) {
//                $plugin = Engine_Api::_()->loadClass('Siteandroidapp_Plugin_Pushnotification');
//                $canSendAndroidNotification = $plugin->canSendAndroidNotification();
//                if (!empty($canSendAndroidNotification)) {
//                    $gcmUsers = $plugin->getGCMUser($row->user_id);
//                    foreach ($gcmUsers as $registrationTokn) {
//                        $plugin->sendAndroidNotification($registrationTokn, $row);
//                    }
//                }
//            }
//
//            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteiosapp')) {
//                $plugin = Engine_Api::_()->loadClass('Siteiosapp_Plugin_Pushnotification');
//                $canSendAndroidNotification = $plugin->canSendIosNotification();
//                if (!empty($canSendAndroidNotification)) {
//                    $gcmUsers = $plugin->getAPNUser($row->user_id);
//                    foreach ($gcmUsers as $registrationTokn) {
//                        $plugin->sendIosNotification($registrationTokn, $row);
//                    }
//                }
//            }
//        } catch (Exception $ex) {
//            
//        }

        return $row;
    }

    /**
     * Get a paginator for friend-request or friend-follow request
     *
     * @param User_Model_User $user
     * @return Zend_Paginator
     */
    public function getRequestsPaginator(User_Model_User $user) {
        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notificationTableName = $notificationTable->info('name');

        $notificationTypeTable = Engine_Api::_()->getDbtable('notificationTypes', 'activity');
        $notificationTypeTableName = $notificationTypeTable->info('name');

        $select = $notificationTable->select()
                ->from($notificationTableName)
                ->join($notificationTypeTableName, $notificationTypeTableName . '.type = ' . $notificationTableName . '.type', null)
                ->where('module = ?', 'user')
                ->where('user_id = ?', $user->getIdentity())
                ->where('is_request = ?', 1)
                ->where('mitigated = ?', 0)
                ->where("$notificationTypeTableName.type = 'friend_request' OR $notificationTypeTableName.type = 'friend_follow_request'")
                ->order('date DESC');

        return Zend_Paginator::factory($select);
    }

    /**
     * Does the user have new updates, returns the number or 0
     *
     * @param $conversation_id: Message conversation id
     * @param $is_read: Flag variable
     * @return Boolean
     */
    public function markMessageReadUnread($conversation_id, $is_read = false) {
        if (empty($is_read))
            Engine_Api::_()->getDbtable('recipients', 'messages')->update(array('inbox_read' => 0), array('conversation_id =?' => $conversation_id));
        else
            Engine_Api::_()->getDbtable('recipients', 'messages')->update(array('inbox_read' => 1), array('conversation_id =?' => $conversation_id));
    }

    /**
     * Does the user have new updates, returns the number or 0
     *
     * @param User_Model_User $user
     * @param $params
     * @return int The number of new updates the user has
     */
    public function getNewUpdatesCount(User_Model_User $user, $params = array()) {
        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notificationTableName = $notificationTable->info('name');

        $select = $notificationTable->select()
                ->from($notificationTableName, "COUNT(notification_id)")
                ->where("$notificationTableName.user_id = ?", $user->getIdentity())
                ->where("$notificationTableName.show = ?", 0)
                ->where("$notificationTableName.read = ?", 0);

        if (isset($params['isNotification']) && !empty($params['isNotification']))
            $select->where("$notificationTableName.type != 'friend_request'")
                    ->where("$notificationTableName.type != 'message_new'")
                    ->where("$notificationTableName.type != 'friend_follow_request'");
        elseif (isset($params['type']) && !empty($params['type'])) {
            $notificationType = $params['type'];
            $select->where("$notificationTableName.type = 'friend_follow_request' OR $notificationTableName.type = '" . $notificationType . "'");
        }

        $newUpdatesCount = $select->query()->fetchColumn();
        return empty($newUpdatesCount) ? false : $newUpdatesCount;
    }

    /**
     * Does the user have new messages, returns the number or 0
     *
     * @param User_Model_User $user
     * @return int The number of new messages the user has
     */
    public function getUnreadMessageCount(User_Model_User $user) {
        $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
        $select = Engine_Api::_()->getDbtable('recipients', 'messages')->select()
                ->from($rName, new Zend_Db_Expr('COUNT(conversation_id) AS unread'))
                ->where($rName . '.user_id = ?', $user->getIdentity())
                ->where($rName . '.inbox_deleted = ?', 0)
                ->where($rName . '.inbox_read = ?', 0)
                ->where($rName . '.inbox_view = ?', 0)
                ->query()
                ->fetchColumn();

        return empty($select) ? false : $select;
    }

    /**
     * Get a paginator for notifications
     *
     * @param User_Model_User $user
     * @return Zend_Paginator
     */
    public function getNotificationsPaginatorSql(User_Model_User $user) {
        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notificationTypeTable = Engine_Api::_()->getDbtable('notificationTypes', 'activity');

        $enabledNotificationTypes = array();
        foreach ($notificationTypeTable->getNotificationTypes() as $type) {
            $enabledNotificationTypes[] = $type->type;
        }
        $enabledNotificationTypes[] = 'live_streaming';

        $messageKey = array_search('message_new', $enabledNotificationTypes);
        $friendRequestKey = array_search('friend_request', $enabledNotificationTypes);
        $friendFollowRequestKey = array_search('friend_follow_request', $enabledNotificationTypes);


        if (!empty($messageKey))
            unset($enabledNotificationTypes[$messageKey]);

        if (!empty($friendRequestKey))
            unset($enabledNotificationTypes[$friendRequestKey]);

        if (!empty($friendFollowRequestKey))
            unset($enabledNotificationTypes[$friendFollowRequestKey]);

        $select = $notificationTable->select()
                ->where('user_id = ?', $user->getIdentity())
                ->where('`type` IN(?)', $enabledNotificationTypes)
                ->order('date DESC')
                ->limit(100);

        return $select;
    }

    /**
     * Mark all new unread notifications and messages as show
     *
     * @param User_Model_User $user
     * @param array $ids
     * @return object
     */
    public function markUpdatesAsShow(User_Model_User $user, array $ids = null) {
        if (is_array($ids) && empty($ids))
            return;

        $where = array(
            '`user_id` = ?' => $user->getIdentity(),
            '`show` = ?' => 0
            //'`read` = ?' => 0 
        );

        if (!empty($ids)) {
            $where['`notification_id` IN(?)'] = $ids;
        }

        Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('show' => 1), $where);

        return;
    }

    /**
     * Mark all new unread messages as show
     *
     * @param User_Model_User $user
     * @param array $ids
     * @return object
     */
    public function markMessagesAsShow(User_Model_User $user, array $ids = null) {
        $where = array(
            '`user_id` = ?' => $user->getIdentity(),
            '`inbox_view` = ?' => 0,
            '`inbox_read` = ?' => 0
        );

        if (!empty($ids)) {
            $where['`conversation_id` IN(?)'] = $ids;
        }

        Engine_Api::_()->getDbtable('recipients', 'messages')->update(array('inbox_view' => 1), $where);

        return;
    }

    /**
     * Mark all new unread messages as show
     *
     * @param User_Model_User $user
     * @param array $ids
     * @return object
     */
    public function markNotificationsAsRead(User_Model_User $user) {
        $where = array(
            '`user_id` = ?' => $user->getIdentity(),
            '`type` !=?' => 'friend_request',
            //'`type` !=?' => 'message_new' //mark all notification as read.
        );

        Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('read' => 1), $where);

        return $this;
    }

    /**
     * Mark all new unread messages as show
     *
     * @param User_Model_User $user
     * @param array $ids
     * @return object
     */
    public function markFriendRequestRead(User_Model_User $user) {
        $where = array(
            '`user_id` = ?' => $user->getIdentity(),
            '`type` =?' => 'friend_request'
        );

        Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('read' => 1), $where);

        return $this;
    }

    public function getNotificationMenus($notification) {
        $parts = array();
        $parts = explode('.', $notification->getTypeInfo()->handler);
        $plugin = $parts[0] . 'NotificationMenus';
        if (method_exists($this, $plugin)) {
            $menus = $this->$plugin($notification, $parts);
            return $menus;
        }
    }

    public function siteeventNotificationMenus($notification, $parts) {
        $occurrence_id = '';
        $menu_array = array();

        if (isset($notification->params) && isset($notification->params['occurrence_id'])) {
            $occurrence_id = $notification->params['occurrence_id'];
        }

        if (!isset($parts[2]) || empty($parts[2]))
            return;

        try {
            $object = $notification->getObject();

            if (isset($object) && !empty($object))
                $event_id = $notification->getObject()->getIdentity();

            if (empty($event_id))
                return;

            if ($parts[2] == 'request-event') {
                $menu_array[] = array(
                    'name' => 'accept_invite',
                    'label' => $this->translate('Attending'),
                    'url' => 'advancedevents/member/accept/' . $event_id,
                    'urlParams' => array(
                        'rsvp' => 2,
                        'occurrence_id' => $occurrence_id
                    ),
                );
                $menu_array[] = array(
                    'name' => 'accept_invite',
                    'label' => $this->translate('Maybe Attending'),
                    'url' => 'advancedevents/member/accept/' . $event_id,
                    'urlParams' => array(
                        'rsvp' => 1,
                        'occurrence_id' => $occurrence_id
                    ),
                );
                $menu_array[] = array(
                    'name' => 'accept_invite',
                    'label' => $this->translate('Not Attending'),
                    'url' => 'advancedevents/member/accept/' . $event_id,
                    'urlParams' => array(
                        'rsvp' => 0,
                        'occurrence_id' => $occurrence_id
                    ),
                );
                $menu_array[] = array(
                    'name' => 'reject_invite',
                    'label' => $this->translate('ignore request'),
                    'url' => 'advancedevents/member/reject/' . $event_id,
                    'urlParams' => array(
                        'occurrence_id' => $occurrence_id
                    ),
                );
            } else if ($parts[2] == 'approve-event') {
                $event_id = $notification->getObject()->getIdentity();

                $menu_array[] = array(
                    'name' => 'approve_request',
                    'label' => $this->translate('APPROVE REQUEST'),
                    'url' => 'advancedevents/member/approve/' . $event_id,
                    'urlParams' => array(
                        'occurrence_id' => $occurrence_id
                    ),
                );
                $menu_array[] = array(
                    'name' => 'approve_request',
                    'label' => $this->translate('ignore request'),
                    'url' => 'advancedevents/member/remove/' . $event_id,
                    'urlParams' => array(
                        'occurrence_id' => $occurrence_id
                    ),
                );
            }
            return $menu_array;
        } catch (Exception $ex) {
            return;
        }
    }

    public function suggestionNotificationMenus($notification, $parts) {
        $suggObj = Engine_Api::_()->getItem('suggestion', $notification->object_id);
        if (strstr($suggObj->entity, "sitereview")) {
            $getListingTypeId = Engine_Api::_()->getItem('sitereview_listing', $suggObj->entity_id)->listingtype_id;
            $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($getListingTypeId);
            $modInfoArray = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed("sitereview_" . $getModId);
            $modInfoArray = $modInfoArray["sitereview_" . $getModId];
        } else {
            $modInfoArray = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($suggObj->entity);
            $modInfoArray = $modInfoArray[$suggObj->entity];
        }
        $displayName = $modInfoArray['displayName'];
        $buttonText = '';
        switch ($suggObj->entity) {
            case 'friendfewfriend':
            case 'friend':
                $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
                $label = $this->translate($label);
                $getIdForTitle = $suggObj->entity_id;
                $getOwnerObjectForTitle = Engine_Api::_()->getItem('user', $getIdForTitle);
                $buttonText = $getOwnerObjectForTitle;
                break;

            case 'group':
                $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
                $label = $this->translate($label);
                if ($viewer()->getIdentity() && !$modObj->membership()->isMember($viewer, null)) {
                    $buttonText = $this->translate('Join Group');
                } else {
                    // Delete group suggestion if already join the group.
                    Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($suggObj->entity, $suggObj->entity_id, 'group_suggestion');
                }
                break;

            case 'event':
                $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
                $label = $this->translate($label);
                if ($viewer->getIdentity() && !$modObj->membership()->isMember($viewer, null)) {
                    $buttonText = $this->translate('Join Event');
                } else {
                    // Delete grouop suggestion if already join the group.
                    Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($suggObj->entity, $suggObj->entity_id, 'event_suggestion');
                }
                break;

            case 'magentoint':
                $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
                $label = $this->translate($label);
                $bodyText = $this->translate('%s %s %s.');
                $buttonText = $this->translate($modInfoArray['buttonLabel']);
                break;

            default:
                $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
                $label = $this->translate($label);
                $buttonText = $this->translate($modInfoArray['buttonLabel']);
                break;
        }

        $menu_array[] = array(
            'name' => 'view_suggestion',
            'label' => $buttonText,
            'urlParams' => array(
                'entity_type' => $modInfoArray['itemType'],
                'entity_id' => $suggObj->entity_id
            ),
        );
        $menu_array[] = array(
            'name' => 'approve_request',
            'label' => $this->translate('ignore suggestion'),
            'url' => 'suggestions/remove-notification',
            'urlParams' => array(
                'entity' => $suggObj->entity,
                'entity_id' => $suggObj->entity_id,
                'notificationType' => $notification->type
            ),
        );
        return $menu_array;
    }

    public function eventNotificationMenus($notification, $parts) {
        if (!isset($parts[2]) || empty($parts[2]))
            return;

        $object = $notification->getObject();

        if (isset($object) && !empty($object))
            $event_id = $notification->getObject()->getIdentity();

        if (empty($event_id))
            return;

        if ($parts[2] == 'request-event') {
            $menu_array[] = array(
                'name' => 'accept_invite',
                'label' => $this->translate('Attending'),
                'url' => 'events/member/accept/' . $event_id,
                'urlParams' => array(
                    'rsvp' => 2,
                ),
            );
            $menu_array[] = array(
                'name' => 'accept_invite',
                'label' => $this->translate('Maybe Attending'),
                'url' => 'events/member/accept/' . $event_id,
                'urlParams' => array(
                    'rsvp' => 1,
                ),
            );

            $menu_array[] = array(
                'name' => 'reject_invite',
                'label' => $this->translate('ignore request'),
                'url' => 'advancedevents/member/reject/' . $event_id,
            );

            return $menu_array;
        }
    }

    public function groupNotificationMenus($notification, $parts) {
        if (!isset($parts[2]) || empty($parts[2]))
            return;

        $object = $notification->getObject();

        if (isset($object) && !empty($object))
            $group_id = $notification->getObject()->getIdentity();

        if (empty($group_id))
            return;

        if ($parts[2] == 'request-group') {
            $menu_array[] = array(
                'name' => 'accept_invite',
                'label' => $this->translate('Join Group'),
                'url' => 'groups/member/accept/' . $group_id,
            );
            $menu_array[] = array(
                'name' => 'accept_invite',
                'label' => $this->translate('ignore request'),
                'url' => 'groups/member/ignore/' . $group_id,
            );

            return $menu_array;
        }
    }

    /*
     * translate function
     */

    public function translate($subject) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($subject);
    }
    
    public function addNotificationQueues($user,$subject,$object,$type,$params){
        $notificationTable = Engine_Api::_()->getDbtable('notificationQueues', 'advancedactivity');
       $notification_id = $notificationTable->insert(array(
            'type' => $type,
            'user_id' => $user->getIdentity(),
            'subject_id' => $subject->getIdentity(),
            'subject_type' => $subject->getType(),
            'object_id' => $object->getIdentity(),
            'object_type' => $object->getType(),
            'date' => date('Y-m-d H:i:s'),
            'params' => $params
        ));
       
       $select = $notificationTable->select()
               ->where('notification_id =?',$notification_id);
       return $notificationTable->fetchRow($select);
       
    }

}
