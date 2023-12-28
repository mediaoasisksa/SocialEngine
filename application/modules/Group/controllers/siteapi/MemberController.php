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
class Group_MemberController extends Siteapi_Controller_Action_Standard {
    
    public function init() {
        if (0 !== ($group_id = (int) $this->getRequestParam('group_id')) &&
                null !== ($group = Engine_Api::_()->getItem('group', $group_id))) {
            Engine_Api::_()->core()->setSubject($group);
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        } else {
            $this->_forward('throw-error', 'member', 'group', array(
                "error_code" => "parameter_missing",
                "message" => "group_id"
            ));
            return;
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        if (!Engine_Api::_()->core()->hasSubject() && !$this->getRequestParam('group_id')) {
            $this->_forward('throw-error', 'member', 'group', array(
                "error_code" => "no_record"
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
     * Return the list of Group Members.
     * 
     * @return array
     */
    public function listAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $group = Engine_Api::_()->core()->getSubject();

        // SET PARAMS
        $bodyParams = array();
        $isAllowedView = $group->authorization()->isAllowed($viewer, 'edit');
        $page = $this->getRequestParam('page', 1);
        $limit = $this->getRequestParam('limit', 20);
        $search = $this->getRequestParam('search');
        $waiting = $this->getRequestParam('waiting');
        $menu = $this->getRequestParam('menu', true);

        // GET OFFICER
        $list = $group->getOfficerList();

        //  GET WAITING MEMBER OBJECT COUNT.
        if ($viewer->getIdentity() && ( $group->isOwner($viewer) || $list->has($viewer) )) {
            $select = $group->membership()->getMembersObjectSelect(false);
            if ($search)
                $select->where('displayname LIKE ?', '%' . $search . '%');
            $waitingMembers = Zend_Paginator::factory($select);
            $waitingMembers->setCurrentPageNumber($page);
            $waitingMembers->setItemCountPerPage($limit);
            $getWaitingMemberCount = $waitingMembers->getTotalItemCount();
        }

        $bodyParams['getWaitingItemCount'] = !empty($getWaitingMemberCount) ? $getWaitingMemberCount : 0;

        // GET THE FULL MEMBER OBJECT
        $select = $group->membership()->getMembersObjectSelect();
        if ($search)
            $select->where('displayname LIKE ?', '%' . $search . '%');

        $fullMembers = Zend_Paginator::factory($select);
        $fullMembers->setCurrentPageNumber($page);
        $fullMembers->setItemCountPerPage($limit);

        //  RETURN THE WAITING MEMBERS AS RESPONSE.
        if (($viewer->getIdentity() && ( $group->isOwner($viewer) || $list->has($viewer) )) && ($waiting || ($fullMembers->getTotalItemCount() <= 0 && $search == ''))) {
            foreach ($waitingMembers as $value) {
                $member = Engine_Api::_()->getItem('user', $value->user_id);
                if (!empty($member))
                    $membersArray[] = $this->_getMemberInfo(array(
                        "member" => $member,
                        "group" => $group,
                        "menu" => $menu
                    ));
            }

            $bodyParams['members'] = $membersArray;
            $getTotalItemCount = $getWaitingMemberCount;
            $bodyParams['waiting'] = $waiting = true;
        } else { //  RETURN THE FULL MEMBERS AS RESPONSE.
            $groupOwner = $group->getOwner();
            foreach ($fullMembers as $member) {
                if (!empty($member))
                    $membersArray[] = $this->_getMemberInfo(array(
                        "member" => $member,
                        "group" => $group,
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
     * Invite the Group Members by Group Owner OR by Allowed Members.
     * 
     * @return array
     */
    public function inviteAction() {
        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $friends = $viewer->membership()->getMembers();

        if (!$subject->authorization()->isAllowed($viewer, 'invite'))
            $this->respondWithError('unauthorized');

        /* RETURN THE GROUP MEMBER INVITE FORM IN THE FOLLOWING CASES:      
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
                    'label' => 'Members',
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
            /* INVITE TO THE GROUP MEMBERS, IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            $table = $subject->getTable();
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {

                if ($this->getRequestParam('users')) {
                    $postValue = $this->getRequestParam('users');
                    $usersIds = preg_split('/[,. ]+/', $postValue);
                }

                if (empty($postValue) || empty($usersIds))
                    $this->respondWithError('invalid_user_ids');

                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                foreach ($friends as $friend) {
                    if (!in_array($friend->getIdentity(), $usersIds)) {
                        continue;
                    }

                    $subject->membership()->addMember($friend)->setResourceApproved($friend);
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($friend, $viewer, $subject, 'group_invite');
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
     * Accept Join Group Request by Group Owner OR by Allowed Members. 
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
                    $viewer, $subject, 'group_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            // ADD ACTIVITY
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $subject, 'group_join');

            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Ignore Group Member Request.
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
                    $user, $subject, 'group_invite');
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
     * Leave Group
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

        $list = $subject->getOfficerList();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            // REMOVE FROM OFFICER LIST
            $list->remove($viewer);
            $subject->membership()->removeMember($viewer);
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Join Group
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

        //IF MEMBER ALREADY PART OF THIS GROUP
        if ($subject->membership()->isMember($viewer))
            $this->respondWithError('unauthorized');

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->addMember($viewer)->setUserApproved($viewer);

            // SET THE REQUEST AS HANDLED
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'group_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            // ADD ACTIVITY
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $subject, 'group_join');

            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Request by Members to Join Group.
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
            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($owner, $viewer, $subject, 'group_approve');
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Request Cancel
     * 
     * @return array
     */
    public function requestCancelAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $subject = Engine_Api::_()->core()->getSubject();

        // Get user
        if (0 === ($user_id = (int) $this->getRequestParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $user = Engine_Api::_()->user()->getViewer();
        }

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->membership()->removeMember($user);

            // Remove the notification?
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $subject->getOwner(), $subject, 'group_approve');
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
     * Remove from membership by Group Owner.
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
        $list = $subject->getOfficerList();

        if (!$subject->membership()->isMember($user))
            $this->respondWithError('unauthorized');

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            // Remove as officer first (if necessary)
            $list->remove($user);

            // Remove membership
            $subject->membership()->removeMember($user);

            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Allow Group Owner to Edit Staff
     * 
     * @return array
     */
    public function editAction() {
        // Validate request methods
        $this->validateRequestMethod('PUT');

        // Get user
        if (0 === ($user_id = (int) $this->getRequestParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        $subject = Engine_Api::_()->core()->getSubject('group');
        $memberInfo = $subject->membership()->getMemberInfo($user);

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $memberInfo->title = $this->getRequestParam('title', "");
            $memberInfo->save();
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Approve add-member request by Group Owner
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

            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($user, $viewer, $subject, 'group_accepted');

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($user, $subject, 'group_join');
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Promote group members by Group Owner
     * 
     * @return array
     */
    public function promoteAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Get user
        if (0 === ($user_id = (int) $this->getRequestParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $list = $subject->getOfficerList();

        if (!$subject->membership()->isMember($user))
            $this->respondWithError('unauthorized');

        $table = $list->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $list->add($user);

            // Add notification
            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($user, $viewer, $subject, 'group_promote');

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($user, $subject, 'group_promote');
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Deomote group members by Group Owner
     * 
     * @return array
     */
    public function demoteAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Get user
        if (0 === ($user_id = (int) $this->getRequestParam('user_id')) ||
                null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        $subject = Engine_Api::_()->core()->getSubject();
        $list = $subject->getOfficerList();

        if (!$subject->membership()->isMember($user))
            $this->respondWithError('unauthorized');

        $table = $list->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $list->remove($user);
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
        $group = $params["group"];

        $list = $group->getOfficerList();
        $listItem = $list->get($member);
        $isOfficer = ( null !== $listItem );

        $viewer = Engine_Api::_()->user()->getViewer();
        $memberArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($member);
        $memberInfo = $group->membership()->getMemberInfo($member);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($member);
        $memberArray = array_merge($memberArray, $getContentImages);

        if (isset($params["menu"]) && !empty($params["menu"])) {
            if ($group->isOwner($viewer)) {
                if (!$group->isOwner($member) && $memberInfo->active == true) {
                    $menus[] = array(
                        'label' => $this->translate('Remove Member'),
                        'name' => 'remove_member',
                        'url' => 'groups/member/remove/' . $group->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                if ($memberInfo->active == false && $memberInfo->resource_approved == false) {
                    $menus[] = array(
                        'label' => $this->translate('Approve Request'),
                        'name' => 'approved_member',
                        'url' => 'groups/member/approve/' . $group->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );

                    $menus[] = array(
                        'label' => $this->translate('Reject Request'),
                        'name' => 'reject_member',
                        'url' => 'groups/member/ignore/' . $group->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                if ($memberInfo->active == false && $memberInfo->resource_approved == true) {
                    $menus[] = array(
                        'label' => $this->translate('Cancel Invite'),
                        'name' => 'cancel_invite',
                        'url' => 'groups/member/ignore/' . $group->getIdentity(),
                        'urlParams' => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }

                if ($memberInfo->active) {
                    if ($isOfficer) {
                        $memberArray['isGroupAdmin'] = 1;
                        $menus[] = array(
                            'label' => $this->translate('Demote Officer'),
                            'name' => 'demote_officer',
                            'url' => 'groups/member/demote/' . $group->getIdentity(),
                            'urlParams' => array(
                                "user_id" => $member->getIdentity()
                            )
                        );
                    } elseif (!$group->isOwner($member)) {
                        $memberArray['isGroupAdmin'] = 0;
                        $menus[] = array(
                            'label' => $this->translate('Make Officer'),
                            'name' => 'make_officer',
                            'url' => 'groups/member/promote/' . $group->getIdentity(),
                            'urlParams' => array(
                                "user_id" => $member->getIdentity()
                            )
                        );
                    }
                }
                $memberArray['menu'] = $menus;
            }
        }

        if ($group->isOwner($member))
            $staff = ( $memberInfo->title ? $memberInfo->title : 'owner' );
        elseif ($isOfficer)
            $staff = ( $memberInfo->title ? $memberInfo->title : 'officer' );

        $memberArray['staff'] = $staff;

        return $memberArray;
    }

    public function getInvitedUsersAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->core()->hasSubject())
            $this->respondWithError('no_record');

        $group = Engine_Api::_()->core()->getSubject();

        //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $level_id = $viewer->level_id;
            $viewer_id = $viewer->getIdentity();
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        // SET PARAMS
        $bodyParams = $membersArray = array();
        $page = $this->getRequestParam('page', 1);
        $limit = $this->getRequestParam('limit', 20);
        $menu = $this->getRequestParam('menu', true);

        $select = $this->getMembersObjectSelect($group, 0);
        $members = Zend_Paginator::factory($select);

        foreach ($members as $member) {
            if (!$member->user_id)
                continue;

            $membersArray[] = $this->_getMemberInfo(array(
                "member" => $member,
                "group" => $group,
                "menu" => $menu
            ));
        }
        $bodyParams['members'] = $membersArray;
        $bodyParams['totalItemCount'] = count($membersArray);
        $this->respondWithSuccess($bodyParams, true);
    }

    public function getMembersObjectSelect(Core_Model_Item_Abstract $resource, $active = true) {
        $table = Engine_Api::_()->getDbtable('users', 'user');
        $tableName = $table->info('name');

        $subtable = Engine_Api::_()->getDbtable('membership', 'group');
        $subtableName = $subtable->info('name');

        $select = $table->select()
                ->setIntegrityCheck(false)
                ->from($tableName)
                ->joinRight($subtableName, '`' . $subtableName . '`.`user_id` = `' . $tableName . '`.`user_id`', array('resource_approved', 'active'))
                ->where('`' . $subtableName . '`.`resource_id` = ?', $resource->getIdentity())
        ;
        if ($active !== null) {
            $select->where('`' . $subtableName . '`.`active` = ?', (bool) $active);
        }
        $select->where('`' . $subtableName . '`.`resource_approved` = ?', (bool) 1);
        $select->where('`' . $subtableName . '`.`user_approved` = ?', (bool) 0);

        return $select;
    }

}
