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
class User_IndexController extends Siteapi_Controller_Action_Standard {
    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $tempForm = array();
        // Add profile types
        $getProfileTypes = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getProfileTypes(array('' => ' '));
        $tempForm['form'][] = array(
            'type' => 'Select',
            'name' => 'profile_type',
            'label' => $this->translate('Member Type'),
            'multiOptions' => $getProfileTypes,
        );

        $tempForm['form'][] = array(
            'type' => 'Text',
            'name' => 'displayname',
            'label' => $this->translate('Name')
        );


        $tempForm['form'][] = array(
            'type' => 'Checkbox',
            'name' => 'has_photo',
            'label' => $this->translate('Only Members With Photos')
        );

        $tempForm['form'][] = array(
            'type' => 'Checkbox',
            'name' => 'is_online',
            'label' => $this->translate('Only Online Members')
        );

        $tempForm['form'][] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $this->translate('Search')
        );

        // Add profile filds
        $tempForm['profile_fields'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getSearchProfileFields();

        $this->respondWithSuccess($tempForm);
    }

    /**
     * Get browse members page.
     * 
     * @return array
     */
    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $siteapiBrowseMembers = Zend_Registry::isRegistered('siteapiBrowseMembers') ? Zend_Registry::get('siteapiBrowseMembers') : null;
        $require_check = true; //Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
        if (empty($require_check) || empty($siteapiBrowseMembers)) {
            $this->respondWithError('unauthorized');
        } try {
            $_error = true;
            $_totalUsers = 0;
            $_userCount = 0;
            $_page = 1;

            // Get search params
            $limit = (int) $this->getRequestParam('limit', 20);
            $page = (int) $this->getRequestParam('page', 1);

            $options = array();
            $getUserProfileFields = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getContentProfileFields('user');
            $getAllParams = $this->getRequestAllParams;
            foreach ($getUserProfileFields as $profileFieldFormElement) {
                if (isset($getAllParams[$profileFieldFormElement['name']]) && !empty($getAllParams[$profileFieldFormElement['name']]))
                    $options[$profileFieldFormElement['name']] = $getAllParams[$profileFieldFormElement['name']];
            }

            // Process options
            $tmp = array();
            $originalOptions = $options;
            foreach ($options as $k => $v) {
                if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                    continue;
                } else if (false !== strpos($k, '_field_')) {
                    list($null, $field) = explode('_field_', $k);
                    $tmp['field_' . $field] = $v;
                } else if (false !== strpos($k, '_alias_')) {
                    list($null, $alias) = explode('_alias_', $k);
                    $tmp[$alias] = $v;
                } else {
                    $tmp[$k] = $v;
                }
            }
            $options = $tmp;

            // Remove it in last
            if ($this->getRequestParam('search', null)) {
                $options['displayname'] = $this->getRequestParam('search', null);
            }

            // Get table info
            $table = Engine_Api::_()->getItemTable('user');
            $userTableName = $table->info('name');

            $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
            $searchTableName = $searchTable->info('name');

            $profile_type = @$options['profile_type'];
            $displayname = @$options['displayname'];

            if (empty($displayname))
                $displayname = $this->getRequestParam('displayname', null);
            if ($this->getRequestParam('profile_type', null))
                $options['profile_type'] = $this->getRequestParam('profile_type', null);

            if ($this->getRequestParam('has_photo', null))
                $has_photo = $this->getRequestParam('has_photo', null);

            if ($this->getRequestParam('is_online', null))
                $is_online = $this->getRequestParam('is_online', null);

            if (!empty($options['extra'])) {
                extract($options['extra']); // is_online, has_photo, submit
            }

            // Contruct query
            $select = $table->select()
                    //->setIntegrityCheck(false)
                    ->from($userTableName)
                    ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
                    //->group("{$userTableName}.user_id")
                    ->where("{$userTableName}.search = ?", 1)
                    ->where("{$userTableName}.enabled = ?", 1);

            $searchDefault = true;

            // Build the photo and is online part of query
            if (isset($has_photo) && !empty($has_photo)) {
                $select->where($userTableName . '.photo_id != ?', "0");
                $searchDefault = false;
            }

            if (isset($is_online) && !empty($is_online)) {
                $select
                        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
                        ->group("engine4_user_online.user_id")
                        ->where($userTableName . '.user_id != ?', "0");
                $searchDefault = false;
            }

            // Add displayname
            if (!empty($displayname)) {
                $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
                $searchDefault = false;
            }

            // Build search part of query
            $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
            foreach ($searchParts as $k => $v) {
                $select->where("`{$searchTableName}`.{$k}", $v);

                if (isset($v) && $v != "") {
                    $searchDefault = false;
                }
            }

            if ($searchDefault) {
                $select->order("{$userTableName}.lastlogin_date DESC");
            } else {
                $select->order("{$userTableName}.displayname ASC");
            }

            // Build paginator
            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage($limit);
            $paginator->setCurrentPageNumber($page);
            $viewer = Engine_Api::_()->user()->getViewer();
            $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            $users = array();

            foreach ($paginator as $user) {
                $tempUser = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                $tempUser = array_merge($tempUser, $getContentImages);
                //Member verification Work...............
                $tempUser['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($user);
            
                $table = Engine_Api::_()->getDbtable('block', 'user');
                $select = $table->select()
                        ->where('user_id = ?', $user->getIdentity())
                        ->where('blocked_user_id = ?', $viewer->getIdentity())
                        ->limit(1);
                $row = $table->fetchRow($select);
                if ($row == NULL) {
                    $tempUser['menus'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->userFriendship($user);
                } else {
                    $tempUser['menus'] = array();
                }

                $users[] = $tempUser;
            }

            $params['isSitemember'] = 0;
            $params['page'] = $page;
            $params['response'] = $users;
            $params['totalItemCount'] = $paginator->getTotalItemCount();

            $this->respondWithSuccess($params);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Extract userIds and send the user information.
     * 
     * @return array
     */
    public function getListsAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $userIds = $this->getRequestParam('userIds', null);
        if (empty($userIds)) {
            $this->respondWithValidationError("parameter_missing", "userIds");
        } else {
            $getUsersArray = preg_split('/[,. ]+/', $userIds);
            $getUsersArray = array_unique($getUsersArray);
        }

        // Get tovalue
        $response = array();
        if (!empty($getUsersArray)) {
            foreach ($getUsersArray as $userId) {
                $user = Engine_Api::_()->getItem('user', $userId);
                if (!empty($user)) {
                    $userArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user, array('email'));

                    //profile picture added or note..................
                    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($user->photo_id);
                    if (!empty($file)) {
                        $userArray['IsProfilePicAvailable'] = 1;
                    } else {
                        $userArray['IsProfilePicAvailable'] = 0;
                    }

                    // Add images
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                    $userArray = array_merge($userArray, $getContentImages);
                     $userArray['cover']='';
                    //User cover photo work......................
                    $userArray['cover'] = "";

                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteusercoverphoto') && !empty($user->user_cover)) {
                        $getUserCoverPhoto = Engine_Api::_()->getApi('Siteapi_Core', 'siteusercoverphoto')->getCoverPhoto($user);
                        if (!empty($getUserCoverPhoto))
                            $userArray['cover'] = $getUserCoverPhoto;
                    }
                    //User coverphoto work end..........................
                    //Location Work................................
                    if (!empty($userArray['seao_locationid'])) {
                        $locationObj = Engine_Api::_()->getItem('seaocore_locationitems', $userArray['seao_locationid']);
                        if (isset($locationObj) && !empty($locationObj))
                            $userArray = array_merge($userArray, $locationObj->toArray());
                    }
                    //end of Location work.........................
                    //Member verification Work...............
                    $userArray['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($user);

                    $userArray = array_merge($userArray, $getContentImages);

                    $response[] = $userArray;
                }
            }
        }

        $this->respondWithSuccess($response);
    }

    public function getContactListMembersAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');
        $viewer = Engine_Api::_()->user()->getViewer();

        $membershipType = $this->getRequestParam('membershipType', 0);
        $page = $this->getRequestParam('page', 1);
        $limit = $this->getRequestParam('limit', 10);

        if (!empty($membershipType) && $membershipType == 'cancel_request') {
            $memberShipTable = Engine_Api::_()->getDbtable('membership', 'user');
            $select = $memberShipTable->select()
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->where('resource_approved = ?', 0);
            //Pagination of users where friend request has been sent
            $userFinalArray = $memberShipTable->fetchAll($select);
            $paginator = Zend_Paginator::factory($userFinalArray);
            $totalCount = $paginator->getTotalItemCount();
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage($limit);
            $response['totalItemCount'] = count($userFinalArray);

            foreach ($paginator as $friend) {
                $user = Engine_Api::_()->getItem('user', $friend->resource_id);

                $tempUser = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                $tempUser = array_merge($tempUser, $getContentImages);
                //Member verification Work............... 
                $tempUser['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($user);
                $tempUser['menus'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->userFriendship($user);
                $users[] = $tempUser;
            }
        } else {
            if (isset($_POST['emails']) && !empty($_POST['emails']))
                $emails = Zend_Json_Decoder::decode($_POST['emails']);
            else
                $this->respondWithError('no_record');
            
            foreach ($emails as $email => $name) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailExist = $this->isEmailExists($email);
                    if (!$emailExist && empty($membershipType)) {
                        $inviteUsers[$email] = $name;
                    }

                    if ($emailExist && !empty($membershipType)) {
                        $table = Engine_Api::_()->getDbtable('block', 'user');
                        $select = $table->select()
                                ->where('user_id = ?', $emailExist->getIdentity())
                                ->where('blocked_user_id = ?', $viewer->getIdentity())
                                ->limit(1);
                        $row = $table->fetchRow($select);
                        if ($row == NULL) {
                            $friendshipType = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($emailExist);
                            if ($friendshipType == $membershipType)
                                $userFinalArray[] = $emailExist;
                        }
                    }
                }
                else {
                    $isContactNoExist = $this->isContactNoExist($email);
                    if (!$isContactNoExist && empty($membershipType)) {
                        $inviteUsers['mobile_' . $email] = $name;
                    }
                    if ($isContactNoExist && !empty($membershipType)) {
                        $table = Engine_Api::_()->getDbtable('block', 'user');
                        $select = $table->select()
                                ->where('user_id = ?', $isContactNoExist->getIdentity())
                                ->where('blocked_user_id = ?', $viewer->getIdentity())
                                ->limit(1);
                        $row = $table->fetchRow($select);
                        if ($row == NULL) {
                            $friendshipType = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($isContactNoExist);
                            if ($friendshipType == $membershipType)
                                $userFinalArray[] = $isContactNoExist;
                        }
                    }
                }
            }
            //Pagination of users where invite is to be sent
            if (isset($inviteUsers) && count($inviteUsers) >= 1) {
                $totalResults = count($inviteUsers);
                $paginator = Zend_Paginator::factory($inviteUsers);
                $itemCount = ($totalResults < 10) ? $totalResults : 10;
                $paginator->setItemCountPerPage($limit);
                $paginator->setCurrentPageNumber($page);
                $response['totalItemCount'] = count($inviteUsers);

                foreach ($paginator as $key => $value) {
                    $users[$key] = $value;
                }
            }

            //Pagination of users where friend request is to be sent
            if (isset($userFinalArray) && count($userFinalArray) >= 1) {
                $totalResults = count($userFinalArray);
                $paginator = Zend_Paginator::factory($userFinalArray);
                $itemCount = ($totalResults < 10) ? $totalResults : 10;
                $paginator->setItemCountPerPage($limit);
                $paginator->setCurrentPageNumber($page);
                $response['totalItemCount'] = count($userFinalArray);

                foreach ($paginator as $emailExist) {
                    $tempUser = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($emailExist);
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($emailExist);
                    $tempUser = array_merge($tempUser, $getContentImages);
                    $tempUser['menus'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->userFriendship($emailExist);
                    $tempUser['contactListName'] = $name;
                    $tempUser['contactListEmail'] = $email;
                    $users[] = $tempUser;
                }
            }
        }

        $response['users'] = $users;
        $this->respondWithSuccess($response);
    }

    /* Get the user profile page.
     * 
     * @return array
     */
    public function getQuickCountAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $email = $this->getRequestParam('email', '');
        if (empty($email))
            $this->respondWithError('no_record');
        try {
            $user = Engine_Api::_()->user()->getUser($email);

            if (empty($user))
                $this->respondWithError('no_record');

            $updates = array();
            if ($user->getIdentity()) {
                $images = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                $updates['image'] = isset($images['image_icon']) ? $images['image_icon'] : '';
                $updates['isAdmin'] = $user->isAdmin();
                $updates['content_url'] = isset($images['content_url']) ? $images['content_url'] : '';
                $updates['notifications'] = (int) Engine_Api::_()->getApi('Siteapi_Core', 'activity')->getNewUpdatesCount($user, array('isNotification' => 'true'));

                $updates['friend_requests'] = (int) Engine_Api::_()->getApi('Siteapi_Core', 'activity')->getNewUpdatesCount($user, array('type' => 'friend_request'));

                $updates['messages'] = (int) Engine_Api::_()->getApi('Siteapi_Core', 'activity')->getUnreadMessageCount($user);

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore') && $user->getIdentity()) {
                    $updates['cartProductsCount'] = 0;
                    $cartId = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($user->getIdentity());
                    if ($cartId)
                        $updates['cartProductsCount'] = (int) Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts($cartId);
                }
            }

            $this->respondWithSuccess($updates);
        } catch (Exception $ex) {
            
        }
    }

    public function isEmailExists($email) {
        $table = Engine_Api::_()->getDbtable('users', 'user');
        $select = $table->select()
                ->where('email LIKE ?', '%' . $email . '%')
                ->limit(1);
        $row = $table->fetchRow($select);
        return ($row) ? $row : 0;
    }

    public function isContactNoExist($no) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $profileTypes = Engine_Api::_()->getApi('fields', 'siteapi')->getProfileTypes();
        foreach ($profileTypes as $key => $value) {
            $mappedProfileField = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteapi_contact_profile_" . $key);

            // Continue if the profile field is not mapped
            if (!isset($mappedProfileField) || empty($mappedProfileField))
                continue;

            $parts = explode('_', $mappedProfileField);
            if (count($parts) != 3)
                continue;

            list($parent_id, $option_id, $field_id) = $parts;
            $fieldname = 'field_' . $field_id;
            if (isset($fieldname) && !empty($fieldname)) {
                $table = Engine_Api::_()->getItemTable('user');
                $userTableName = $table->info('name');
                $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
                $searchTableName = $searchTable->info('name');
                $searchColumn = $db->query("SHOW COLUMNS FROM $searchTableName LIKE '$fieldname'")->fetch();
                if (empty($searchColumn)) {
                    continue;
                }
                $select = $table->select()
                        ->from($userTableName)
                        ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null);
                $select->where("`engine4_user_fields_search`.profile_type LIKE ?", $key);
                $select->where("`engine4_user_fields_search`.$fieldname = ?", $no);
                $select->limit(1);
                return $table->fetchRow($select);
            } else {
                return 0;
            }
        }
    }

}
