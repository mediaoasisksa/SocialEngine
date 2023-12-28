<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Group_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if ($this->getRequestParam('group_id') && (0 !== ($group_id = (int) $this->getRequestParam('group_id')) &&
                null !== ($group = Engine_Api::_()->getItem('group', $group_id))))
            Engine_Api::_()->core()->setSubject($group);

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
    }

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('group', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'group')->getBrowseSearchForm(), true);
    }

    /**
     * Return the Groups of "Browse Group". 
     * 
     * @return array
     */
    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('group', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $getRequest = $this->getRequestAllParams;
        $getRequest['search'] = 1;
        $response = $this->_getGroupLists($getRequest);

        $this->respondWithSuccess($response, true);
    }

    /**
     * Return the "My Group". 
     * 
     * @return array
     */
    public function manageAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!Engine_Api::_()->authorization()->isAllowed('group', $viewer, 'create'))
            $this->respondWithError('unauthorized');

        $getRequest = $this->getRequestAllParams;
        $getRequest['manage'] = 1;
        $response = $this->_getGroupLists($getRequest);

        $this->respondWithSuccess($response);
    }

    /**
     * Return the Group View page.
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('group');

        // Return if no subject available.
        if (empty($subject))
            $this->respondWithError('no_record');

        $isAllowedView = $subject->authorization()->isAllowed($viewer, 'view');
        $siteapiGroupView = Zend_Registry::isRegistered('siteapiGroupView') ? Zend_Registry::get('siteapiGroupView') : null;

        // Return if logged-in user not authorized to view group.
        if (empty($siteapiGroupView) || empty($isAllowedView)) {
            $module_error_type = @ucfirst($subject->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }

        $bodyParams = array();

        // GETTING THE GUTTER-MENUS.
        if ($this->getRequestParam("gutter_menu", true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($subject);

        // GETTING THE GROUP PROFILE TABS.
        if ($this->getRequestParam("profile_tabs", true))
            $bodyParams['profile_tabs'] = $this->_profileTAbsContainer($subject);

        // PREPARE RESPONSE ARRAY
        $bodyParams['response'] = $subject->toArray();

        if ($viewer->getIdentity()) {
            $is_member = $subject->membership()->isMember($viewer, null);
            $can_upload = $subject->authorization()->isAllowed(null, 'photo');
            $bodyParams['response']['isMember'] = (!empty($is_member)) ? 1 : 0;
            $bodyParams['response']['canUpload'] = (!empty($can_upload)) ? 1 : 0;
        }

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

        $bodyParams['response']["owner_title"] = $subject->getOwner()->getTitle();
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

        //Member verification Work...............
        $bodyParams['response']['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($subject->getOwner());
        
        // Group View Count Increment
        if (!$subject->isOwner($viewer)) {
            Engine_Api::_()->getDbtable('groups', 'group')->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
                    ), array(
                'group_id = ?' => $subject->getIdentity(),
            ));
        }

        // GET STAFF
        $staff = $ids = array();
        $ids[] = $subject->getOwner()->getIdentity();
        $list = $subject->getOfficerList();
        foreach ($list->getAll() as $listiteminfo) {
            $ids[] = $listiteminfo->child_id;
        }

        foreach ($ids as $id) {
            $user = Engine_Api::_()->getItem('user', $id);
            $membership = $subject->membership()->getMemberInfo($user);
            $staffUser = $user->getTitle();

            if ($subject->isOwner($user)) {
                $tempStaff = (!empty($membership) && $membership->title ? $membership->title : 'owner');
            } else {
                $tempStaff = (!empty($membership) && $membership->title ? $membership->title : 'officer');
            }

            $staffUser .= " (" . trim($tempStaff) . ")";
            $staffArray[] = $staffUser;
        }

        if (!empty($staffArray))
            $bodyParams['response']['staff'] = $staffArray;


        // GET CATEGORY TITLE
        if (!empty($subject->category_id) &&
                ($category = $subject->getCategory()) instanceof Core_Model_Item_Abstract &&
                !empty($category->title))
            $bodyParams['response']['category_title'] = (string) $category->title;


        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Return the "Create Group" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function createAction() {
        // CHECK GROUP CREATE PERMISSION.
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id)) {
            $this->respondWithError('unauthorized');
        } else {
            $level_id = $viewer->level_id;
        }

        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToCreate = $permissionsTable->getAllowed('group', $level_id, 'create');
        }

        $siteapiGroupCreate = Zend_Registry::isRegistered('siteapiGroupCreate') ? Zend_Registry::get('siteapiGroupCreate') : null;

        if (empty($siteapiGroupCreate) || empty($allowToCreate))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
            $values['user_id'] = $viewer->getIdentity();
            $paginator = Engine_Api::_()->getItemTable('group')->getGroupPaginator($values);

            $quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'group', 'max');
            $current_count = $paginator->getTotalItemCount();

            if (($current_count >= $quota) && !empty($quota))
                $this->respondWithError('unauthorized', 'You have already uploaded the maximum number of entries allowed. If you would like to upload a new entry, please delete an old one first.');
        }

        /* RETURN THE GROUP CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'group')->getForm());
        } else if ($this->getRequest()->isPost()) {
            /* CREATE THE GROUP IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('group', $viewer->level_id, 'flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("groups",'group');
                    $select = $tableFlood->select()->where("user_id = ?",$viewer->getIdentity())->order("creation_date DESC");
                    if($itemFlood[1] == "minute"){
                        $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
                    }else if($itemFlood[1] == "day"){
                        $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 DAY)");
                    }else{
                        $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 HOUR)");
                    }
                    $floodItem = $tableFlood->fetchAll($select);
                    if(count($floodItem) && $itemFlood[0] <= count($floodItem)){
                        $type = $itemFlood[1];
                        $time =  "1 ".$type;
                        $message = 'You have reached maximum limit of posting in '.$time.'. Try again after this duration expires.';
                        $this->respondWithError('unauthorized', $message);
                    }
                }
            }

            // CONVERT POST DATA INTO THE ARRAY.
            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'group')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            // CONVERT POST DATA INTO AN ARRAY.
            $data = $values = @array_merge($values, array(
                        'user_id' => $viewer_id
            ));

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'group')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // START TO CREATE GROUP.
            $db = Engine_Api::_()->getDbtable('groups', 'group')->getAdapter();
            $db->beginTransaction();
            try {
                // CREATE GROUP
                $table = Engine_Api::_()->getDbtable('groups', 'group');
                $subject = $table->createRow();
                $subject->setFromArray($values);
                $subject->save();

                // ADD OWNER AS MEMBER
                $subject->membership()->addMember($viewer)
                        ->setUserApproved($viewer)
                        ->setResourceApproved($viewer);

                // SET PHOTO
                if (!empty($_FILES['photo']))
                    Engine_Api::_()->getApi('Siteapi_Core', 'group')->setPhoto($_FILES['photo'], $subject);

                // PROCESS PRIVACY
                $auth = Engine_Api::_()->authorization()->context;

                $roles = array('officer', 'member', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'everyone';
                }

                if (empty($values['auth_photo'])) {
                    $values['auth_photo'] = 'everyone';
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_event'] = 'everyone';
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);
                $photoMax = array_search($values['auth_photo'], $roles);
                $eventMax = array_search($values['auth_event'], $roles);
                $inviteMax = array_search($values['auth_invite'], $roles);

                $officerList = $subject->getOfficerList();

                foreach ($roles as $i => $role) {
                    if ($role === 'officer') {
                        $role = $officerList;
                    }
                    $auth->setAllowed($subject, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($subject, $role, 'comment', ($i <= $commentMax));
                    $auth->setAllowed($subject, $role, 'photo', ($i <= $photoMax));
                    $auth->setAllowed($subject, $role, 'event', ($i <= $eventMax));
                    $auth->setAllowed($subject, $role, 'invite', ($i <= $inviteMax));
                }

                // CREATE SOME AUTH STAFF FOR ALL OFFICERS.
                $auth->setAllowed($subject, $officerList, 'photo.edit', 1);
                $auth->setAllowed($subject, $officerList, 'topic.edit', 1);

                // ADD AUTH FOR INVITE USERS
                $auth->setAllowed($subject, 'member_requested', 'view', 1);

                // ADD ACTION
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($viewer, $subject, 'group_create');
                if ($action) {
                    $activityApi->attachActivity($action, $subject);
                }

                $db->commit();

                // Change request method POST to GET
                $this->setRequestMethod();

                $this->_forward('view', 'index', 'group', array(
                    'group_id' => $subject->getIdentity()
                ));
            } catch (Engine_Image_Exception $e) {
                $db->rollBack();
                $errorMessage = $e->getMessage();
            } catch (Exception $e) {
                $db->rollBack();
                $errorMessage = $e->getMessage();
            }
        }

        if (!empty($errorMessage))
            $this->respondWithValidationError('internal_server_error', $errorMessage);
    }

    /**
     * Return the "Edit Group" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('group');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $officerList = $subject->getOfficerList();
        $isAllowedView = $subject->authorization()->isAllowed($viewer, 'edit');

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO EDIT GROUP.
        if (empty($isAllowedView))
            $this->respondWithError('unauthorized');

        // FIND OUT THE PRIVACY.
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('officer', 'member', 'registered', 'everyone');
        $actions = array('view', 'comment', 'invite', 'photo', 'event');

        // CHECK Group FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE GROUP EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            // IF THERE ARE NO FORM POST YET THEN RETURN THE Group FORM.
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'group')->getForm($subject);
            $formValues = $subject->toArray();

            foreach ($roles as $roleString) {
                $role = $roleString;
                if ($role === 'officer') {
                    $role = $officerList;
                }

                foreach ($actions as $action) {
                    if ($auth->isAllowed($subject, $role, $action)) {
                        $formValues['auth_' . $action] = $roleString;
                    }
                }
            }

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));

            return;
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* UPDATE THE GROUP INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO THE ARRAY.
            $data = $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'group')->getForm($subject);
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values;

            // START FORM VALIDATION
            $viewer = Engine_Api::_()->user()->getViewer();
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'group')->getFormValidators($subject);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Api::_()->getItemTable('group')->getAdapter();
            $db->beginTransaction();

            try {
                $subject->setFromArray($values);
                $subject->save();

                if (!empty($_FILES['photo']))
                    Engine_Api::_()->getApi('Siteapi_Core', 'group')->setPhoto($_FILES['photo'], $subject);

                // PROCESS PRIVACY
                $auth = Engine_Api::_()->authorization()->context;

                $roles = array('officer', 'member', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'everyone';
                }

                if (empty($values['auth_photo'])) {
                    $values['auth_photo'] = 'everyone';
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_event'] = 'everyone';
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);
                $photoMax = array_search($values['auth_photo'], $roles);
                $eventMax = array_search($values['auth_event'], $roles);
                $inviteMax = array_search($values['auth_invite'], $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'officer') {
                        $role = $officerList;
                    }
                    $auth->setAllowed($subject, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($subject, $role, 'comment', ($i <= $commentMax));
                    $auth->setAllowed($subject, $role, 'photo', ($i <= $photoMax));
                    $auth->setAllowed($subject, $role, 'event', ($i <= $eventMax));
                    $auth->setAllowed($subject, $role, 'invite', ($i <= $inviteMax));
                }

                // CREATE SOME AUTH STUFF FOR ALL OFFICERS
                $auth->setAllowed($subject, $officerList, 'photo.edit', 1);
                $auth->setAllowed($subject, $officerList, 'topic.edit', 1);

                // ADD AUTH FOR INVITED USERS
                $auth->setAllowed($subject, 'member_requested', 'view', 1);

                // Commit
                $db->commit();

                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Delete the Group.
     * 
     * @return array
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('group');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // GET LOGGED-IN USER LEVEL ID.
        if (!empty($viewer_id))
            $level_id = $viewer->level_id;

        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToDelete = $permissionsTable->getAllowed('group', $level_id, 'delete');
        }

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO DELETE GROUP.
        if (empty($allowToDelete))
            $this->respondWithError('unauthorized');

        $db = $subject->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $subject->delete();
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Getting the group list for "Browse Group" and "My Group"
     * 
     * @return array
     */
    private function _getGroupLists($params = array()) {
        $getSearchValue = $response = $value = $tempResponse = array();
        $imageType = 'thumb.icon';

        $viewer = Engine_Api::_()->user()->getViewer();

        // PASS THE PERMISSION
        $siteapiGroupBrowse = Zend_Registry::isRegistered('siteapiGroupBrowse') ? Zend_Registry::get('siteapiGroupBrowse') : null;
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('group', null, 'create')->checkRequire();

        if (isset($params['user_id']))
            $subject = Engine_Api::_()->getItem('user', $params['user_id']);

        // SET THIS VARIABLE ONLY IN THE CASE OF "MY GROUP".
        if (!isset($params['user_id']) && !empty($params['manage']))
            $params['user_id'] = $viewer->getIdentity();

        if( $viewer->getIdentity() && @$params['view'] == 1 ) {
          $params['users'] = array();
          foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
            $params['users'][] = $memberinfo->user_id;
          }
        }

        if (empty($params['manage']) && isset($params['show']) && $params['show'] == 2) {
            // Get an array of friend ids
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);

            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        if (!empty($params['image_type']))
            $imageType = $params['image_type'];

        $tableObj = Engine_Api::_()->getDbtable('groups', 'group');
        $table = Engine_Api::_()->getItemTable('group');
        $tName = $table->info('name');
        if (!empty($params['manage'])) {
            $membership = Engine_Api::_()->getDbtable('membership', 'group');
            $subject = !empty($subject) ? $subject : $viewer;
            $getgroupSelect = $membership->getMembershipsOfSelect($subject);
            $getgroupSelect->where('group_id IS NOT NULL');

            if ($params['view'] == 2) {
                $getgroupSelect->where("`{$tName}`.`user_id` = ?", $subject->getIdentity());
            }
        } else if (isset($params['user_id']) &&
                !empty($params['user_id']) &&
                isset($subject) &&
                !empty($subject)
        ) {
            $membership = Engine_Api::_()->getDbtable('membership', 'group');
            $getgroupSelect = $membership->getMembershipsOfSelect($subject);
            $params['page'] = empty($params['page']) ? 1 : $params['page'];
            $params['limit'] = empty($params['limit']) ? 20 : $params['limit'];
        } else {
            $getgroupSelect = Engine_Api::_()->getItemTable('group')->getGroupSelect($params);
        }

        if (!empty($params['text'])) {
            $getgroupSelect->where(
                    $table->getAdapter()->quoteInto("`{$tName}`.`title` LIKE ?", '%' . $params['text'] . '%') . ' OR ' .
                    $table->getAdapter()->quoteInto("`{$tName}`.`description` LIKE ?", '%' . $params['text'] . '%')
            );
        }

        // IF PASS THE PAGE AND LIMIT THEN APPLY THE PAGINATION THERE.
        if (isset($params['page']) && !empty($params['page']) && isset($params['limit']) && !empty($params['limit'])) {
            $paginator = Zend_Paginator::factory($getgroupSelect);

            $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('group_page', 12));

            if (!empty($params['page'])) {
                $paginator->setCurrentPageNumber($params['page']);
            }

            $groupsObj = $paginator;
            $paginator->clearPageItemCache();
            $getTempGroupCount = $paginator->getTotalItemCount();
        } else {
            $groupsObj = $tableObj->fetchAll($getgroupSelect);
            $getTempGroupCount = COUNT($groupsObj);
        }

        $response['totalItemCount'] = $getTempGroupCount;

        if ($getTempGroupCount) {
            foreach ($groupsObj as $groupObj) {
                $value = $groupObj->toArray();

                if (!empty($params['manage'])) {
                    $tempMenu = array();
                    if ($groupObj->isOwner($viewer)) {
                        $tempMenu[] = array(
                            'label' => $this->translate('Edit Group'),
                            'name' => 'edit',
                            'url' => 'groups/edit/' . $groupObj->getIdentity(),
                            'urlParams' => array(
                            )
                        );

                        $tempMenu[] = array(
                            'label' => $this->translate('Delete Group'),
                            'name' => 'delete',
                            'url' => 'groups/delete/' . $groupObj->getIdentity(),
                            'urlParams' => array(
                            )
                        );
                    } elseif (!$groupObj->membership()->isMember($viewer, null)) {
                        $tempMenu[] = array(
                            'label' => $this->translate('Join Group'),
                            'name' => 'delete',
                            'url' => 'groups/member/join/' . $groupObj->getIdentity(),
                            'urlParams' => array(
                            )
                        );
                    } else if ($groupObj->membership()->isMember($viewer, true)) {
                        $tempMenu[] = array(
                            'label' => $this->translate('Leave Group'),
                            'name' => 'leave_group',
                            'url' => 'groups/member/leave/' . $groupObj->getIdentity(),
                            'urlParams' => array(
                            )
                        );
                    }

                    $value["menu"] = $tempMenu;
                }

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($groupObj);
                $value = array_merge($value, $getContentImages);

                $value["owner_title"] = $groupObj->getOwner()->getTitle();

                // VIEW PRIVACY
                $isAllowedView = $groupObj->authorization()->isAllowed($viewer, 'view');
                $value["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                // EDIT PRIVACY
                $isAllowedEdit = $groupObj->authorization()->isAllowed($viewer, 'edit');
                if (isset($params['is_edit']) && !empty($params['is_edit']))
                    $value["edit"] = empty($isAllowedEdit) ? 0 : 1;

                // DELETE PRIVACY
                $isAllowedDelete = $groupObj->authorization()->isAllowed($viewer, 'delete');
                if (isset($params['is_delete']) && !empty($params['is_delete']))
                    $value["delete"] = empty($isAllowedDelete) ? 0 : 1;

                $tempResponse[] = $value;
            }

            if (!empty($tempResponse))
                $response['response'] = $tempResponse;
        }

        if (!empty($siteapiGroupBrowse))
            return $response;
    }

    /**
     * Get the list of container tabs
     * 
     * @return array
     */
    private function _profileTAbsContainer($subject) {
        $response[] = array(
            'label' => $this->translate('Updates'),
            'name' => 'update',
            'totalItemCount' => 0
        );

        $response[] = array(
            'label' => $this->translate('Members'),
            'name' => 'members',
            'totalItemCount' => $subject->member_count,
            'url' => 'groups/member/list/' . $subject->getIdentity(),
            'urlParams' => array(
            )
        );

        $response[] = array(
            'label' => $this->translate('Photos'),
            'name' => 'photos',
            'totalItemCount' => $subject->getSingletonAlbum()->getCollectiblesPaginator()->getTotalItemCount(),
            'url' => 'groups/photo/list',
            'urlParams' => array(
                "group_id" => $subject->getIdentity()
            )
        );

        return $response;
    }

    /**
     * Gutter menu show on the group profile page.
     * 
     * @return array
     */
    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();

        // EDIT GROUP DETAILS
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'label' => $this->translate('Edit Group Details'),
                'name' => 'edit',
                'url' => 'groups/edit/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        // GROUP PROFILE MEMBERS
        if ($viewer->getIdentity()) {
            $row = $subject->membership()->getRow($viewer);

            // Not yet associated at all
            if (null === $row) {
                if ($subject->membership()->isResourceApprovalRequired()) {
                    $menus[] = array(
                        'label' => $this->translate('Request Membership'),
                        'name' => 'request_member',
                        'url' => 'groups/member/request/' . $subject->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                } else {
                    $menus[] = array(
                        'label' => $this->translate('Join Group'),
                        'name' => 'join',
                        'url' => 'groups/member/join/' . $subject->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }
            }

            // Full member
            // @todo consider owner
            else if ($row->active) {
                if (!$subject->isOwner($viewer)) {
                    $menus[] = array(
                        'label' => $this->translate('Leave Group'),
                        'name' => 'leave',
                        'url' => 'groups/member/leave/' . $subject->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                } else {
                    $menus[] = array(
                        'label' => $this->translate('Delete Group'),
                        'name' => 'delete',
                        'url' => 'groups/delete/' . $subject->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }
            } else if (!$row->resource_approved && $row->user_approved) {
                $menus[] = array(
                    'label' => $this->translate('Cancel Membership Request'),
                    'name' => 'cancel_request',
                    'url' => 'groups/member/request-cancel/' . $subject->getIdentity(),
                    'urlParams' => array(
                    )
                );
            } else if (!$row->user_approved && $row->resource_approved) {
                $menus[] = array(
                    'label' => $this->translate('Accept Membership Request'),
                    'name' => 'accept_request',
                    'url' => 'groups/member/accept/' . $subject->getIdentity(),
                    'urlParams' => array(
                    )
                );

                $menus[] = array(
                    'label' => $this->translate('Ignore Membership Request'),
                    'name' => 'reject_request',
                    'url' => 'groups/member/ignore/' . $subject->getIdentity(),
                    'urlParams' => array(
                    )
                );
            }
        }


        // INVITE GROUP
        if ($subject->authorization()->isAllowed($viewer, 'invite')) {
//            $is_member = $subject->membership()->isMember($viewer, null);
//            if (empty($is_member)) {
            $menus[] = array(
                'label' => $this->translate('Invite Members'),
                'name' => 'invite',
                'url' => 'groups/member/invite/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
//            }
        }

        // SHARE GROUP
        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Share Group'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        return $menus;
    }

}
