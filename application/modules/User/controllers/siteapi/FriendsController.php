<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FriendsController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_FriendsController extends Siteapi_Controller_Action_Standard {
    public function init() {
        // Try to set subject
        $user_id = $this->getRequestParam('user_id', null);
        if ($user_id && !Engine_Api::_()->core()->hasSubject()) {
            $user = Engine_Api::_()->getItem('user', $user_id);
            if ($user) {
                Engine_Api::_()->core()->setSubject($user);
            }
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Check if friendships are enabled
        $siteapiFriendActions = Zend_Registry::isRegistered('siteapiFriendActions') ? Zend_Registry::get('siteapiFriendActions') : null;
        if (empty($siteapiFriendActions) || ($this->getRequest()->getActionName() !== 'suggest' &&
                !Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible)) {
            $this->_forward('throw-error', 'friends', 'user', array(
                "error_code" => "unauthorized"
            ));
            return;
        }
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    /**
     * Create new list to categorize friends on member profile page.
     *
     * @return array
     */
    public function listCreateAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $user = Engine_Api::_()->user()->getViewer();
        $title = (string) $this->getRequestParam('title');
        $friend_id = (int) $this->getRequestParam('friend_id', null);
        if (!empty($friend_id))
            $friend = Engine_Api::_()->getItem('user', $friend_id);

        $user_id = $user->getIdentity();
        if (empty($user_id))
            $this->respondWithError('unauthorized');

        if (!$this->getRequest()->isPost())
            $this->respondWithError('invalid_method');

        if (empty($title)) {
            $this->respondWithValidationError('parameter_missing', 'title');
        }

        try {
            $listTable = Engine_Api::_()->getItemTable('user_list');
            $list = $listTable->createRow();
            $list->owner_id = $user->getIdentity();
            $list->title = $title;
            $list->save();

            if (!empty($friend) && $friend->getIdentity()) {
                $list->add($friend);
            }

            $this->respondWithSuccess($list->list_id);
        } catch (Engine_Image_Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Add list to activate respective list.
     *
     * @return array
     */
    /**
     * Add list to activate respective list.
     *
     * @return array
     */
    public function listAddAction() {
        $subject = $user = Engine_Api::_()->user()->getViewer();
        $user_id = $user->getIdentity();
        if (empty($user_id))
            $this->respondWithError('unauthorized');

        $friend_id = $this->getRequestParam('friend_id');
        $member = Engine_Api::_()->getItem('user', $friend_id);

        // Multiple friend mode
        $select = $subject->membership()->getMembersOfSelect();
        $friends = $paginator = $select->getTable()->fetchAll($select);
        // Get stuff
        $ids = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->resource_id;
        }

        $values = $this->_getAllParams();

        // Get lists if viewing own profile
        if ($user->isSelf($subject)) {
            // Get lists
            $listTable = Engine_Api::_()->getItemTable('user_list');
            $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $user->getIdentity()));

            $listIds = array();
            foreach ($lists as $list) {
                $listIds[] = $list->list_id;
            }

            // Build lists by user
            $listItems = array();
            $listsByUser = array();
            if (!empty($listIds)) {
                $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
                $listItemSelect = $listItemTable->select()
                        ->where('list_id IN(?)', $listIds)
                        ->where('child_id IN(?)', $ids);
                $listItems = $listItemTable->fetchAll($listItemSelect);
                foreach ($listItems as $listItem) {
                    $listsByUser[$listItem->child_id][] = $listItem->list_id;
                }
            }

            foreach ($lists as $list) {
                $inList = in_array($list->list_id, (array) @$listsByUser[$member->user_id]);

                if ($inList != false) {
                    $addToListForm[] = array(
                        'type' => 'Checkbox',
                        'name' => $list->list_id,
                        'label' => $list->title,
                        'value' => 1
                    );
                } else {
                    $addToListForm[] = array(
                        'type' => 'Checkbox',
                        'name' => $list->list_id,
                        'label' => $list->title,
                        'value' => 0
                    );
                }
            }
        }


        if ((_ANDROID_VERSION && (_ANDROID_VERSION >= '1.6.2')))
            $addToListForm[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => $this->translate('New List'),
                'value' => 0
            );


        if ($this->getRequest()->isGet()) {
            if (isset($addToListForm) && !empty($addToListForm)) {
                $response['form'] = $addToListForm;
                $this->respondWithSuccess($response);
            }
        }

        if (($list_id = $this->getRequestParam('list_id')) && empty($list_id)) {
            $this->respondWithValidationError('parameter_missing', 'list_id');
        }

        if (($friend_id = $this->getRequestParam('friend_id')) && empty($friend_id)) {
            $this->respondWithValidationError('parameter_missing', 'friend_id');
        }

        // Check friend
        $friend = Engine_Api::_()->getItem('user', $friend_id);
        if (empty($friend))
            $this->respondWithError('no_record');

        foreach ($lists as $list) {
            if (isset($values[$list->list_id]) && !empty($values[$list->list_id])) {
                // Check list
                $listTable = Engine_Api::_()->getItemTable('user_list');
                $list = $listTable->find($list->list_id)->current();
                if (empty($list))
                    $this->respondWithError('no_record');

                if ($list->owner_id != $user->getIdentity())
                    $this->respondWithError('unauthorized');

//                // Check if already target status
                if (!$list->has($friend))
                    $list->add($friend);
            }
            else if (isset($values[$list->list_id]) && empty($values[$list->list_id])) {
                // Check list
                $listTable = Engine_Api::_()->getItemTable('user_list');
                $list = $listTable->find($list->list_id)->current();

                if (empty($list))
                    $this->respondWithError('no_record');

                if ($list->owner_id != $user->getIdentity())
                    $this->respondWithError('unauthorized');

                // Check if already target status
                if ($list->has($friend))
                    $list->remove($friend);
            }
        }

        if (!empty($values['title'])) {
            try {
                $listTable = Engine_Api::_()->getItemTable('user_list');
                $list = $listTable->createRow();
                $list->owner_id = $user->getIdentity();
                $list->title = $values['title'];
                $list->save();

                if (!empty($friend) && $friend->getIdentity()) {
                    $list->add($friend);
                }

                $this->successResponseNoContent('no_content');
            } catch (Engine_Image_Exception $e) {
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }

        $this->successResponseNoContent('no_content');
    }

    /**
     * Remove list to activate respective list.
     *
     * @return array
     */
    public function listRemoveAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $user = Engine_Api::_()->user()->getViewer();
        $user_id = $user->getIdentity();

        if (empty($user_id))
            $this->respondWithError('unauthorized');

        if (($list_id = $this->getRequestParam('list_id')) && empty($list_id)) {
            $this->respondWithValidationError('parameter_missing', 'list_id');
        }

        if (($friend_id = $this->getRequestParam('friend_id')) && empty($friend_id)) {
            $this->respondWithValidationError('parameter_missing', 'friend_id');
        }

        $friend = Engine_Api::_()->getItem('user', $friend_id);
        if (empty($friend))
            $this->respondWithError('no_record');

        // Check list
        $listTable = Engine_Api::_()->getItemTable('user_list');
        $list = $listTable->find($list_id)->current();

        if (empty($list))
            $this->respondWithError('no_record');

        if ($list->owner_id != $user->getIdentity())
            $this->respondWithError('unauthorized');

        // Check if already target status
        if (!$list->has($friend))
            $this->respondWithError('user_not_in_list');

        $list->remove($friend);
        $this->successResponseNoContent('no_content');
    }

    /**
     * Delete list
     *
     * @return array
     */
    public function listDeleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $list_id = (int) $this->getRequestParam('list_id');
        $user = Engine_Api::_()->user()->getViewer();
        $user_id = $user->getIdentity();
        if (empty($user_id))
            $this->respondWithError('unauthorized');

        if (empty($list_id)) {
            $this->respondWithValidationError('parameter_missing', 'list_id');
        }

        // Check list
        $listTable = Engine_Api::_()->getItemTable('user_list');
        $list = $listTable->find($list_id)->current();
        if (!$list)
            $this->respondWithError('no_record');

        if ($list->owner_id != $user->getIdentity())
            $this->respondWithError('unauthorized');

        $list->delete();
        $this->successResponseNoContent('no_content');
    }

    /**
     * Add Friend OR Send Friend Request.
     *
     * @return array
     */
    public function addAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get viewer and other user
        if (null == ($user_id = $this->getRequestParam('user_id')) ||
                null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        // check that user is not trying to befriend 'self'
        if ($viewer->isSelf($user))
            $this->respondWithError('unauthorized');

        // check that user is already friends with the member
        if ($user->membership()->isMember($viewer))
            $this->respondWithError('user_already_friend');

        // check that user has not blocked the member
        if ($viewer->isBlocked($user))
            $this->respondWithError('user_blocked');

        // Process
        $siteapiGlobalView = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.global.view', 0);
        $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $siteapiManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.iosdevice.type', 0);
        $siteapiGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.global.type', 0);
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            // send request
            $user->membership()
                    ->addMember($viewer)
                    ->setUserApproved($viewer);

//            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitemember");
//            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
//            $enabledFollow = $coreSettings->getSetting('sitemember.user.follow.enable', 0);
//            $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
//            if (!empty($isModEnabled) && !empty($direction) && !empty($enabledFollow)) {
//                $resourceObj = $user;
//                $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
//
//                if ($resource_type == 'user') {
//                    $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($resourceObj, $viewer);
//                    $db = Engine_Db_Table::getDefaultAdapter();
//                    $db->beginTransaction();
//                    if (empty($follow)) {
//                        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
//                        $follow_id = $followTable->addFollow($resourceObj, $viewer);
//
//                        if ($viewer_id != $resourceObj->getOwner()->getIdentity()) {
//                            $action = $activityApi->addActivity($viewer, $resourceObj, 'follow_' . $resource_type, '', array(
//                                'owner' => $resourceObj->getOwner()->getGuid(),
//                            ));
//                            if (!empty($action))
//                                $activityApi->attachActivity($action, $resourceObj);
//                        }
//                        $db->commit();
//                    }
//                }
//            }
            if (!$viewer->membership()->isUserApprovalRequired() && !$viewer->membership()->isReciprocal()) {
                // if one way friendship and verification not required
                // Add activity
                Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($viewer, $user, 'friends_follow', '{item:$subject} is now following {item:$object}.');

                // Add notification
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')
                        ->addNotification($user, $viewer, $viewer, 'friend_follow');
            } else if (!$viewer->membership()->isUserApprovalRequired() && $viewer->membership()->isReciprocal()) {
                // if two way friendship and verification not required
                // Add activity
                Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
                Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');

                // Add notification
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')
                        ->addNotification($user, $viewer, $user, 'friend_accepted');
            } else if (!$user->membership()->isReciprocal()) {
                // if one way friendship and verification required
                // Add notification
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')
                        ->addNotification($user, $viewer, $user, 'friend_follow_request');
            } else if ($user->membership()->isReciprocal()) {
                // if two way friendship and verification required
                // Add notification
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')
                        ->addNotification($user, $viewer, $user, 'friend_request');
            }

            $tempHostType = '';
            if (empty($siteapiGlobalType)) {
                for ($check = 0; $check < strlen($hostType); $check++) {
                    $tempHostType += @ord($hostType[$check]);
                }
                $tempHostType = $tempHostType + $siteapiGlobalView;
            }

            if (!empty($tempHostType) && ($tempHostType != $siteapiManageType)) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteapi.global.type', 1);
            } else {
                $db->commit();
                 Engine_Api::_()->getApi('cache', 'advancedactivity')->flushAll();
                $this->successResponseNoContent('no_content');
            }
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Cancel sended friend request.
     *
     * @return array
     */
    public function cancelAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get viewer and other user
        if (null == ($user_id = $this->getRequestParam('user_id')) ||
                null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();
        try {
            $user->membership()->removeMember($viewer);

//            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitemember");
//            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
//            $enabledFollow = $coreSettings->getSetting('sitemember.user.follow.enable', 0);
//            $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
//            if (!empty($isModEnabled) && !empty($direction) && !empty($enabledFollow)) {
//                $resourceObj = $user;
//                $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
//
//                if ($resource_type == 'user') {
//                    $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($resourceObj, $viewer);
//                    $db = Engine_Db_Table::getDefaultAdapter();
//                    $db->beginTransaction();
//                    if (!empty($follow)) {
//                        $followTable->removeFollow($resourceObj, $viewer);
//
//                        if ($viewer_id != $resourceObj->getOwner()->getIdentity()) {
//                            //DELETE ACTIVITY FEED
//                            $action_id = Engine_Api::_()->getDbtable('actions', 'activity')
//                                    ->select()
//                                    ->from('engine4_activity_actions', 'action_id')
//                                    ->where('type = ?', "follow_$resource_type")
//                                    ->where('subject_id = ?', $viewer_id)
//                                    ->where('subject_type = ?', 'user')
//                                    ->where('object_type = ?', $resource_type)
//                                    ->where('object_id = ?', $resourceObj->getIdentity())
//                                    ->query()
//                                    ->fetchColumn();
//
//                            if (!empty($action_id)) {
//                                $activity = Engine_Api::_()->getItem('activity_action', $action_id);
//                                if (!empty($activity)) {
//                                    $activity->delete();
//                                }
//                            }
//                        }
//                    }
//                }
//            }
            // Set the requests as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($user, $viewer, 'friend_follow_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }

            $db->commit();
             Engine_Api::_()->getApi('cache', 'advancedactivity')->flushAll();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Reject sended friend request.
     *
     * @return array
     */
    public function rejectAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get viewer and other user
        if (null == ($user_id = $this->getRequestParam('user_id')) ||
                null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            if ($viewer->membership()->isMember($user)) {
                $viewer->membership()->removeMember($user);
            }

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

//  /**
//   * Ignore sended friend request.
//   *
//   * @return array
//   */
//  public function ignoreAction() {
//    // Validate request methods
//    $this->validateRequestMethod('DELETE');
//    
//    // Prepare data
//    $viewer = Engine_Api::_()->user()->getViewer();
//    $viewer_id = $viewer->getIdentity();
//    if ( empty($viewer_id) )
//      $this->respondWithError('unauthorized');
//
//    // Get viewer and other user
//    if ( null == ($user_id = $this->getRequestParam('user_id')) ||
//            null == ($user = Engine_Api::_()->getItem('user', $user_id)) ) {
//      $this->respondWithValidationError('parameter_missing', 'user_id');
//    }
//
//    // Process
//    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
//    $db->beginTransaction();
//
//    try {
//      $viewer->membership()->removeMember($user);
//
//      // Set the requests as handled
//      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
//              ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
//      if ( $notification ) {
//        $notification->mitigated = true;
//        $notification->read = 1;
//        $notification->save();
//      }
//      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
//              ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
//      if ( $notification ) {
//        $notification->mitigated = true;
//        $notification->read = 1;
//        $notification->save();
//      }
//
//      $db->commit();
//      $this->successResponseNoContent('no_content');
//    } catch ( Exception $e ) {
//      $db->rollBack();
//      $this->respondWithValidationError('internal_server_error', $e->getMessage());
//    }
//  }
    /**
     * Conform sended friend request.
     *
     * @return array
     */
    public function confirmAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get viewer and other user    
        if (null == ($user_id = $this->getRequestParam('user_id')) ||
                null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        $friendship = $viewer->membership()->getRow($user);
        if ($friendship->active)
            $this->respondWithError('unauthorized');

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer->membership()->setResourceApproved($user);

            // Add activity
            if (!$user->membership()->isReciprocal()) {
                Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.');
            } else {
                Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
                Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
            }

            // Add notification
            if (!$user->membership()->isReciprocal()) {
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')
                        ->addNotification($user, $viewer, $user, 'friend_follow_accepted');
            } else {
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')
                        ->addNotification($user, $viewer, $user, 'friend_accepted');
            }

            // Set the requests as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }

            // Increment friends counter
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Remove sended friend request.
     *
     * @return array
     */
    public function removeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get viewer and other user
        if (null == ($user_id = $this->getRequestParam('user_id')) ||
                null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();
        try {
            if ($this->getRequestParam('rev')) {
                $viewer->membership()->removeMember($user);
            } else {
                $user->membership()->removeMember($viewer);
            }

            // Remove from lists?
            // @todo make sure this works with one-way friendships
            $user->lists()->removeFriendFromLists($viewer);
            $viewer->lists()->removeFriendFromLists($user);

            // Set the requests as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }

            $db->commit();
             Engine_Api::_()->getApi('cache', 'advancedactivity')->flushAll();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Get Searched Friends
     *
     * @return array
     */
    public function suggestAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $data = array();
        $subject_guid = $this->getRequestParam('guid', $this->getRequestParam('subject', null));
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($subject_guid && (stripos($subject_guid, 'event') !== false || stripos($subject_guid, 'group') !== false)) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        } else {
            $subject = $viewer;
        }

        if ($viewer->getIdentity()) {
            $data = array();
            $table = Engine_Api::_()->getItemTable('user');
            $usersAllowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $viewer->level_id, 'auth');

            // send all users in case of message & only friends in other cases
            if ((bool) $this->_getParam('message') && $usersAllowed == "everyone") {
                $select = Engine_Api::_()->getDbtable('users', 'user')->select();
                $select->where('user_id <> ?', $viewer->user_id);
            } else {
                $select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();
            }

            if (0 < ($limit = (int) $this->getRequestParam('limit', 10))) {
                $select->limit($limit);
            }

            if (null !== ($text = $this->getRequestParam('search', $this->getRequestParam('value')))) {
                $select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
            }
            $select->where('`' . $table->info('name') . '`.`user_id` <> ?', $viewer->getIdentity());
            $select->order("{$table->info('name')}.displayname ASC");
            $ids = array();
            foreach ($select->getTable()->fetchAll($select) as $friend) {
                $tempData['type'] = 'user';
                $tempData['id'] = $friend->getIdentity();
                $tempData['guid'] = $friend->getGuid();
                $tempData['label'] = $friend->getTitle();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($friend);
                $tempData = array_merge($tempData, $getContentImages);

                $data[] = $tempData;
            }

            $this->respondWithSuccess($data);
        } else {
            $this->respondWithError('unauthorized');
        }
    }

}
