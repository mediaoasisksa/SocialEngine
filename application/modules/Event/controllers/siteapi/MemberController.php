<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    MemberController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Event_MemberController extends Siteapi_Controller_Action_Standard {

    // DONE
    public function init() {
        if (0 !== ($event_id = (int) $this->getRequestParam('event_id')) &&
                null !== ($event = Engine_Api::_()->getItem('event', $event_id))) {
            Engine_Api::_()->core()->setSubject($event);
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        } else {
            $this->_forward('throw-error', 'member', 'event', array(
                "error_code" => "parameter_missing",
                "message" => "event_id"
            ));
            return;
        }

        if (!Engine_Api::_()->core()->hasSubject() && !$this->getRequestParam('event_id')) {
            $this->_forward('throw-error', 'member', 'event', array(
                "error_code" => "no_record"
            ));
            return;
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
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
     * Return the list of Event Members.
     * 
     * @return array
     */
    public function listAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $event = Engine_Api::_()->core()->getSubject();
        $viewer_id = $viewer->getIdentity();

        // SET PARAMS
        $bodyParams = array();
        $isAllowedView = $event->authorization()->isAllowed($viewer, 'edit');
        $page = $this->getRequestParam('page', 1);
        $limit = $this->getRequestParam('limit', 20);
        $search = $this->getRequestParam('search');
        $waiting = $this->getRequestParam('waiting');
        $menu = $this->getRequestParam('menu', true);

        //  GET WAITING MEMBER OBJECT COUNT.
        if ($viewer->getIdentity() && ($event->isOwner($viewer))) {
            $select = $event->membership()->getMembersObjectSelect(false);
            if ($search)
                $select->where('displayname LIKE ?', '%' . $search . '%');
            $waitingMembers = Zend_Paginator::factory($select);
            $waitingMembers->setCurrentPageNumber($page);
            $waitingMembers->setItemCountPerPage($limit);
            $getWaitingMemberCount = $waitingMembers->getTotalItemCount();
        }

        $bodyParams['getWaitingItemCount'] = !empty($getWaitingMemberCount) ? $getWaitingMemberCount : 0;

        // GET THE FULL MEMBER OBJECT
        $select = $event->membership()->getMembersObjectSelect();
        if ($search)
            $select->where('displayname LIKE ?', '%' . $search . '%');

        $fullMembers = Zend_Paginator::factory($select);
        $fullMembers->setCurrentPageNumber($page);
        $fullMembers->setItemCountPerPage($limit);

        //  RETURN THE WAITING MEMBERS AS RESPONSE.
        if (($viewer->getIdentity() && $event->isOwner($viewer)) && ($waiting || ($fullMembers->getTotalItemCount() <= 0 && $search == ''))) {
            foreach ($waitingMembers as $value) {
                $member = Engine_Api::_()->getItem('user', $value->user_id);
                if (!empty($member))
                    $membersArray[] = $this->_getMemberInfo(array(
                        "member" => $member,
                        "event" => $event,
                        "menu" => $menu
                    ));
            }

            $bodyParams['members'] = $membersArray;
            $getTotalItemCount = $getWaitingMemberCount;
            $bodyParams['waiting'] = $waiting = true;
        } else { //  RETURN THE FULL MEMBERS AS RESPONSE.      
            $eventOwner = $event->getOwner();
            foreach ($fullMembers as $member) {
                if (!empty($member))
                    $membersArray[] = $this->_getMemberInfo(array(
                        "member" => $member,
                        "event" => $event,
                        "menu" => $menu
                    ));
            }
            $bodyParams['members'] = $membersArray;
            $bodyParams['waiting'] = $waiting = false;
            $getTotalItemCount = $fullMembers->getTotalItemCount();
        }

        $bodyParams['getTotalItemCount'] = !empty($getTotalItemCount) ? $getTotalItemCount : 0;
        $bodyParams['canEdit'] = !empty($isAllowedView) ? $isAllowedView : 0;

        $this->respondWithSuccess($bodyParams, true);
    }

    /**
     * Invite the Event Members by Event Owner OR by Allowed Members.
     * 
     * @return array
     */
    public function inviteAction() {
        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $subject = Engine_Api::_()->core()->getSubject();
        $friends = $viewer->membership()->getMembers();

        if (!$subject->authorization()->isAllowed($viewer, 'invite'))
            $this->respondWithError('unauthorized');

        /* RETURN THE EVENT MEMBER INVITE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $bodyParams = $userFriends = array();
            foreach ($friends as $friend) {
                if ($subject->membership()->isMember($friend, null))
                    continue;
                $userFriends[$friend->getIdentity()] = $friend->getTitle();
            }

            if (COUNT($userFriends)) {
                $bodyParams[] = array(
                    'type' => 'Checkbox',
                    'name' => 'selectall',
                    'label' => $this->translate('Choose All Friends')
                );

                $bodyParams[] = array(
                    'type' => 'Multicheckbox',
                    'name' => 'users',
                    'label' => $this->translate('Invite Members'),
                    'description' => $this->translate('Choose the people you want to invite to this event.'),
                    'multiOptions' => $userFriends,
                );

                $bodyParams[] = array(
                    'type' => 'Submit',
                    'name' => 'submit',
                    'label' => $this->translate('Send Invites')
                );
            }
            $this->respondWithSuccess($bodyParams);
        } else if ($this->getRequest()->isPost()) {
            /* INVITE TO THE EVENT MEMBERS, IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            $table = $subject->getTable();
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {

                $postValue = $this->getRequestParam('users');
                $usersIds = preg_split('/[,. ]+/', $postValue);

                if (empty($postValue) || empty($usersIds))
                    $this->respondWithError('invalid_user_ids');

                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                foreach ($friends as $friend) {
                    if (!in_array($friend->getIdentity(), $usersIds)) {
                        continue;
                    }

                    $subject->membership()->addMember($friend)->setResourceApproved($friend);
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($friend, $viewer, $subject, 'event_invite');
                }

                $db->commit();

                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Accept Join Event Request by Event Owner OR by Allowed Members. 
     * 
     * @return array
     */
    public function acceptAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $subject = Engine_Api::_()->core()->getSubject();

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->setUserApproved($viewer);

            // SET THE REQUEST AS HANDLED
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'event_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            // ADD ACTIVITY
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $subject, 'event_join');

            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Ignore Event Member Request.
     * 
     * @return array
     */
    public function ignoreAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Get user
        if (0 === ($user_id = (int) $this->getRequestParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $user = Engine_Api::_()->user()->getViewer();
        }

        $subject = Engine_Api::_()->core()->getSubject();

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->removeMember($user);

            // SET THE REQUEST AS HANDLED
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $user, $subject, 'event_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Leave Event
     * 
     * @return array
     */
    public function leaveAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->isOwner($viewer))
            $this->respondWithError('unauthorized');

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $subject->membership()->removeMember($viewer);
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Join Event
     * 
     * @return array
     */
    public function joinAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $subject = Engine_Api::_()->core()->getSubject();

        //IF MEMBER ALREADY PART OF THIS EVENT
        if ($subject->membership()->isMember($viewer))
            $this->respondWithError('unauthorized');

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->addMember($viewer)->setUserApproved($viewer);
            $getRSVP = $this->getRequestParam('rsvp', 2);
            if (isset($getRSVP)) {
                $subject->membership()
                        ->getMemberInfo($viewer)
                        ->setFromArray(array('rsvp' => $this->getRequestParam('rsvp')))
                        ->save();
            }

            // SET THE REQUEST AS HANDLED
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'event_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            // ADD ACTIVITY
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $subject, 'event_join');

            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Request by Members to Join Event.
     * 
     * @return array
     */
    public function requestAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $subject = Engine_Api::_()->core()->getSubject();
        $owner = $subject->getOwner();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->addMember($viewer)->setUserApproved($viewer);
            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($owner, $viewer, $subject, 'event_approve');
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Cancel sent invitation by event owner.{Cancel Request}
     * 
     * @return array
     */
    public function cancelAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Get user
        if (0 === ($user_id = (int) $this->getRequestParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        $subject = Engine_Api::_()->core()->getSubject('event');
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $subject->membership()->removeMember($user);

            // Remove the notification?
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $subject->getOwner(), $subject, 'event_approve');
            if ($notification) {
                $notification->delete();
            }

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Remove from membership by Event Owner.
     * 
     * @return array
     */
    public function removeAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        // Get user
        if (0 === ($user_id = (int) $this->getRequestParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->membership()->isMember($user))
            $this->respondWithError('unauthorized');

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            // Remove membership
            $subject->membership()->removeMember($user);

            // Remove the notification?
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $subject->getOwner(), $subject, 'event_approve');
            if ($notification) {
                $notification->delete();
            }
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Approve add-member request by Event Owner
     * 
     * @return array
     */
    public function approveAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Get user
        if (0 === ($user_id = (int) $this->getRequestParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->setResourceApproved($user);

            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($user, $viewer, $subject, 'event_accepted');

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($user, $subject, 'event_join');

            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Ignore Event Invite
     * 
     * @return array
     */
    public function rejectAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Process form
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->removeMember($viewer);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'event_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    private function _getMemberInfo($params) {
        $staff = '';
        $member = $params["member"];
        $event = $params["event"];

        $viewer = Engine_Api::_()->user()->getViewer();
        $memberArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($member);
        $memberInfo = $event->membership()->getMemberInfo($member);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($member);
        $memberArray = array_merge($memberArray, $getContentImages);
        $memberArray['is_owner'] = ($event->isOwner($member)) ? 1 : 0;
        if (isset($memberInfo->rsvp)) {
            $memberArray['rsvp'] = $memberInfo->rsvp;
        }

        if (isset($params["menu"]) && !empty($params["menu"])) {
            if ($event->isOwner($viewer)) {
                if (!$event->isOwner($member) && $memberInfo->active == true) {
                    $menus[] = array(
                        'label' => $this->translate('Remove Member'),
                        'name' => 'remove_member',
                        'url' => 'events/member/remove/' . $event->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                if ($memberInfo->active == false && $memberInfo->resource_approved == false) {
                    $menus[] = array(
                        'label' => $this->translate('Approve Request'),
                        'name' => 'approved_member',
                        'url' => 'events/member/approve/' . $event->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );

                    $menus[] = array(
                        'label' => $this->translate('Reject Request'),
                        'name' => 'reject_member',
                        'url' => 'events/member/ignore/' . $event->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                if ($memberInfo->active == false && $memberInfo->resource_approved == true) {
                    $menus[] = array(
                        'label' => $this->translate('Cancel Invite'),
                        'name' => 'cancel_invite',
                        'url' => 'events/member/remove/' . $event->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                $memberArray['menu'] = $menus;
            }
        }

        return $memberArray;
    }

}
