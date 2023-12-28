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
class Classified_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if ($this->getRequestParam("classified_id") && (0 !== ($classified_id = (int) $this->getRequestParam("classified_id")) &&
                null !== ($classified = Engine_Api::_()->getItem('classified', $classified_id))))
            Engine_Api::_()->core()->setSubject($classified);
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
        if (!Engine_Api::_()->authorization()->isAllowed('classified', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            $getBrowseSearchForm = Engine_Api::_()->getApi('Siteapi_Core', 'classified')->getBrowseSearchForm($subject);
        } else {
            $getBrowseSearchForm = Engine_Api::_()->getApi('Siteapi_Core', 'classified')->getBrowseSearchForm();
        }

        $this->respondWithSuccess($getBrowseSearchForm, true);
    }

    /**
     * Return the Classifieds of "Browse Classified" page. 
     * 
     * @return array
     */
    public function indexAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('classified', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $bodyParams = array();
        $response = $this->_getClassifiedLists($this->getRequestAllParams);

        $this->respondWithSuccess($response, true);
    }

    /**
     * Return the Classifieds of "My Classified" page. 
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

        if (!Engine_Api::_()->authorization()->isAllowed('classified', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $getRequest = $this->getRequestAllParams;
        $getRequest['manage'] = 1;
        $response = $this->_getClassifiedLists($getRequest);

        $this->respondWithSuccess($response);
    }

    /**
     * Return the Classified View page.
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

        $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

        $this->getHost = '';
        if ($getDefaultStorageType == 'local')
            $this->getHost = !empty($staticBaseUrl) ? $staticBaseUrl : $serverHost;

        $siteapiClassifiedView = Zend_Registry::isRegistered('siteapiClassifiedView') ? Zend_Registry::get('siteapiClassifiedView') : null;

        if (empty($siteapiClassifiedView)) {
            $module_error_type = @ucfirst($subject->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        if (!Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'view')) {
            $module_error_type = @ucfirst($subject->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }

        $owner = $subject->getOwner();

        if ($subject->closed && ((!$viewer->getIdentity()) || ($viewer->getIdentity() && ($viewer->getIdentity() != $owner->getIdentity()))))
            $this->respondWithError('unauthorized');

        $album = $subject->getSingletonAlbum();
        $photoPaginator = $album->getCollectiblesPaginator();
        $photoPaginator->setCurrentPageNumber($this->_getParam('page', 1));
        $photoPaginator->setItemCountPerPage(100);
        $images = array();
        foreach ($photoPaginator as $photo) {
            $tempImages = $photo->toArray();


            // Getting viewer like or not to content.
            $tempImages["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);

            // Getting like count.
            $tempImages["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($photo);

            if (!$photo->getPhotoUrl('thumb.main'))
                continue;
            $tempImages['image'] = ($photo->getPhotoUrl('thumb.main')) ? $this->getHost . $photo->getPhotoUrl('thumb.main') : '';
            $tempImages['image_profile'] = ($photo->getPhotoUrl('thumb.profile')) ? $this->getHost . $photo->getPhotoUrl('thumb.profile') : '';
            $tempImages['image_normal'] = ($photo->getPhotoUrl('thumb.normal')) ? $this->getHost . $photo->getPhotoUrl('thumb.normal') : '';
            $tempImages['image_icon'] = ($photo->getPhotoUrl('thumb.icon')) ? $this->getHost . $photo->getPhotoUrl('thumb.icon') : '';

            if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'edit')) {
                $tempImages['menu'][] = array(
                    'label' => $this->translate('Delete'),
                    'name' => 'delete',
                    'url' => 'albums/photo/delete',
                    'urlParams' => array(
                        "classified_id" => $subject->getIdentity(),
                        "photo_id" => $photo->getIdentity()
                    )
                );
            }

            $images[] = $tempImages;
        }

        // GETTING THE GUTTER-MENUS.
        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($subject);

        $bodyParams['response'] = $subject->toArray();
        $bodyParams['response']['totalItemCount'] = $photoPaginator->getTotalItemCount();

        //contentURL
        $contentURL = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($subject);

        if (!empty($contentURL))
            $bodyParams['response'] = array_merge($bodyParams['response'], $contentURL);

        if ($viewer->getIdentity())
            $bodyParams['response']['canUpload'] = $subject->authorization()->isAllowed(null, 'photo');

        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
        if (!empty($categories) && is_array($categories) && array_key_exists($bodyParams['response']['category_id'], $categories))
            $bodyParams['response']['category_title'] = $categories[$bodyParams['response']['category_id']];

        $bodyParams['response']["images"] = $images;

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

        $bodyParams['response']["owner_title"] = $subject->getOwner()->getTitle();

        //Member verification Work...............
        $bodyParams['response']['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($subject->getOwner());

        // Getting viewer like or not to content.
        $bodyParams['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($subject);

        // Getting like count.
        $bodyParams['response']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($subject);
        // GETTING THE GUTTER-MENUS.
//    if ( $this->getRequestParam('gutter_menu', true) )
//      $value['gutterMenu'] = $this->_gutterMenus($subject);
        // Get tags
        $classifiedTags = $subject->tags()->getTagMaps();
        if (!empty($classifiedTags)) {
            foreach ($classifiedTags as $tag) {
                $tagArray[$tag->getTag()->tag_id] = $tag->getTag()->text;
            }
        }
        $bodyParams['response']['tags'] = $tagArray;

        // Add Profile Fields
        $bodyParams['response']['profile_fields'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getProfileInfo($subject);

        if (!$subject->isOwner($viewer)) {
            Engine_Api::_()->getDbtable('classifieds', 'classified')->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
                    ), array(
                'classified_id = ?' => $subject->getIdentity(),
            ));
        }

        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Return the "Create Classified" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function createAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id)) {
            $this->respondWithError('unauthorized');
        } else {
            $level_id = $viewer->level_id;
        }


        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToCreate = $permissionsTable->getAllowed('classified', $level_id, 'create');
        }

        $siteapiClassifiedCreate = Zend_Registry::isRegistered('siteapiClassifiedCreate') ? Zend_Registry::get('siteapiClassifiedCreate') : null;
        if (empty($allowToCreate) || empty($siteapiClassifiedCreate))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
            $values['user_id'] = $viewer_id;
            $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values);

            $quota = $quota = Engine_Api::_()->authorization()->getPermission($level_id, 'classified', 'max');
            $current_count = $paginator->getTotalItemCount();

            if (($current_count >= $quota) && !empty($quota))
                $this->respondWithError('unauthorized', 'You have already uploaded the maximum number of entries allowed. If you would like to upload a new entry, please delete an old one first.');
        }

        /* RETURN THE CLASSIFIED CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'classified')->getForm());
        } else if ($this->getRequest()->isPost()) {
            /* CREATE THE CLASSIFIED IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('classified', $viewer->level_id, 'flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("classifieds",'classified');
                    $select = $tableFlood->select()->where("owner_id = ?",$viewer->getIdentity())->order("creation_date DESC");
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
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'classified')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $viewer = Engine_Api::_()->user()->getViewer();
            $data = $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'classified')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $table = Engine_Api::_()->getItemTable('classified');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $classified = $table->createRow();
                $classified->setFromArray($values);
                $classified->save();

                // Set photo
                if (!empty($_FILES['photo'])) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'classified')->setPhoto($_FILES['photo'], $classified);
                }

                // Save the profile fields information.
                Engine_Api::_()->getApi('Siteapi_Core', 'user')->setProfileFields($classified, $values);

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $tags = array_filter(array_map("trim", $tags));
                $classified->tags()->addTagMaps($viewer, $tags);

                // Add fields [NEED TODO CUSTUM WORK HERE]
//        $customfieldform = $form->getSubForm('fields');
//        $customfieldform->setItem($classified);
//        $customfieldform->saveValues();
                // Set privacy
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = array("everyone");
                }
                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = array("everyone");
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($classified, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($classified, $role, 'comment', ($i <= $commentMax));
                }

                // Commit
                $db->commit();

                try {

                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $classified, 'classified_new', '', array('privacy' => isset($values['networks'])? $network_privacy : null));

                    if( $action != null ) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $classified);
                    }
                    $db->commit();
                } catch( Exception $e ) {
                    $db->rollBack();
                    throw $e;
                }


                // Change request method POST to GET
                $this->setRequestMethod();

                $this->_forward('view', 'index', 'classified', array(
                    'classified_id' => $classified->getIdentity()
                ));
                return;
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Return the "Edit Classified" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $profile_feilds_value_merge = $this->getRequestParam('profile_feilds_value_merge', 0);

        $isAllowedView = $subject->authorization()->isAllowed($viewer, 'edit');

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO EDIT CLASSIFIED.
        if (empty($isAllowedView))
            $this->respondWithError('unauthorized');

        // FIND OUT THE AUTH COMMENT AND AOUTH VIEW VALUE.
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        // CHECK CLASSIFIED FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE CLASSIFIED EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            // IF THERE ARE NO FORM POST YET THEN RETURN THE CLASSIFIED FORM.
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'classified')->getForm($subject);
            $formValues = $subject->toArray();

            foreach ($roles as $role) {
                if ($auth->isAllowed($subject, $role, 'view'))
                    $formValues['auth_view'] = $role;

                if ($auth->isAllowed($subject, $role, 'comment'))
                    $formValues['auth_comment'] = $role;
            }

            // SET THE TAGS  
            $tagStr = '';
            foreach ($subject->tags()->getTagMaps() as $tagMap) {
                $tag = $tagMap->getTag();
                if (!isset($tag->text))
                    continue;
                if ('' !== $tagStr)
                    $tagStr .= ', ';
                $tagStr .= $tag->text;
            }
            $formValues['tags'] = $tagStr;

            // Add profile fields
            $profile_feilds = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getProfileInfo($subject, true);

            if (isset($profile_feilds) &&
                    !empty($profile_feilds) &&
                    isset($profile_feilds_value_merge) &&
                    !empty($profile_feilds_value_merge) &&
                    $profile_feilds_value_merge == 1
            )
                $formValues = array_merge($formValues, $profile_feilds);
            else
                $formValues['profile_fields'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getProfileInfo($subject, true);

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));
            return;
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* UPDATE THE CLASSIFIED INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO THE ARRAY.
            $data = $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'classified')->getForm($subject);
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values;

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'classified')->getFormValidators($subject);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // handle save for tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $tags = array_filter(array_map("trim", $tags));

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $subject->setFromArray($values);
                $subject->modified_date = date('Y-m-d H:i:s');

                $subject->tags()->setTagMaps($viewer, $tags);
                $subject->save();

//        if ( !empty($_FILES['photo']) ) {
//          $classified = Engine_Api::_()->getApi('Siteapi_Core', 'classified')->setPhoto($_FILES['photo'], $subject);
//        }
                // Save the profile fields information.
                Engine_Api::_()->getApi('Siteapi_Core', 'user')->setProfileFields($subject, $values);

                // CREATE AUTH STUFF HERE
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if (!empty($values['auth_view'])) {
                    $auth_view = $values['auth_view'];
                } else {
                    $auth_view = "everyone";
                }
                $viewMax = array_search($auth_view, $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($subject, $role, 'view', ($i <= $viewMax));
                }

                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if (!empty($values['auth_comment'])) {
                    $auth_comment = $values['auth_comment'];
                } else {
                    $auth_comment = "everyone";
                }
                $commentMax = array_search($auth_comment, $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($subject, $role, 'comment', ($i <= $commentMax));
                }

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
     * Delete the Classified.
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
            $subject = Engine_Api::_()->core()->getSubject('classified');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id))
            $level_id = $viewer->level_id;

        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToDelete = $permissionsTable->getAllowed('classified', $viewer->level_id, 'delete');
        }

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO DELETE CLASSIFIED.
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
     * Close the classified.
     * 
     * @return array
     */
    public function closeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('classified');

        if (empty($subject))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        // @todo convert this to post only
        $table = $subject->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $subject->closed = $this->getRequestParam('closed', 1);
            $subject->save();

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Getting the classified list for "Browse Classified" and "My Classified"
     * 
     * @return array
     */
    private function _getClassifiedLists($params = array()) {
        $tempParams = $params;
        $getSearchValue = $response = $value = $tempResponse = array();
        $imageType = 'thumb.icon';

        $viewer = Engine_Api::_()->user()->getViewer();

        // PASS THE PERMISSION
        $siteapiClassifiedGetListing = Zend_Registry::isRegistered('siteapiClassifiedGetListing') ? Zend_Registry::get('siteapiClassifiedGetListing') : null;
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->checkRequire();

        // SET THIS VARIABLE ONLY IN THE CASE OF "MY CLASSIFIED".
        if (!empty($params['manage']))
            $tempParams['user_id'] = $viewer->getIdentity();

        if (isset($params['user_id']))
            $tempParams['user_id'] = $params['user_id'];

        if (!empty($getSearchValue)) {
            $tempParams = @array_merge($tempParams, $getSearchValue);
            if (@$tempParams['show'] == 2) {
                // Get an array of friend ids
                $table = Engine_Api::_()->getItemTable('user');
                $select = $viewer->membership()->getMembersSelect('user_id');
                $friends = $table->fetchAll($select);

                // Get stuff
                $ids = array();
                foreach ($friends as $friend) {
                    $ids[] = $friend->user_id;
                }
                $tempParams['users'] = $ids;
            }
        }

        if (!empty($params['image_type']))
            $imageType = $params['image_type'];

        $tableObj = Engine_Api::_()->getDbtable('classifieds', 'classified');


        // ------------------- Start: Profile Field Searching Work -----------------
        // Get the profile fields.
        $profileFieldsParams = array();
        $getContentProfileFields = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getContentProfileFields('classified');
        foreach ($getContentProfileFields as $profileFieldFormElement) {
            if (isset($params[$profileFieldFormElement['name']]) && !empty($params[$profileFieldFormElement['name']]))
                $profileFieldsParams[$profileFieldFormElement['name']] = $params[$profileFieldFormElement['name']];
        }

        // Process options
        if (!empty($profileFieldsParams)) {
            $tmp = array();
            foreach ($profileFieldsParams as $k => $v) {
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
            $profileFieldsParams = $tmp;
        }

        // ------------------- End: Profile Field Searching Work -----------------

        $getclassifiedSelect = Engine_Api::_()->getItemTable('classified')->getClassifiedsSelect($tempParams, $profileFieldsParams);

        // If get the 'page' and 'limit' in request then apply the pagination.
        if (isset($params['page']) && !empty($params['page']) && isset($params['limit']) && !empty($params['limit'])) {
            $paginator = Zend_Paginator::factory($getclassifiedSelect);

            $itemsCount = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10);
            $paginator->setItemCountPerPage($itemsCount);

            if (!empty($params['page'])) {
                $paginator->setCurrentPageNumber($params['page']);
            }

            $classifiedsObj = $paginator;
            $paginator->clearPageItemCache();
            $getTempClassifiedCount = $paginator->getTotalItemCount();
        } else {
            $classifiedsObj = $tableObj->fetchAll($getclassifiedSelect);
            $getTempClassifiedCount = COUNT($classifiedsObj);
        }

        $response['totalItemCount'] = $getTempClassifiedCount;

        if (COUNT($classifiedsObj)) {
            foreach ($classifiedsObj as $classifiedObj) {
                $value = $classifiedObj->toArray();

                if (!empty($params['manage'])) {
                    $tempMenu = array();
                    if ($classifiedObj->isOwner($viewer)) {
                        $tempMenu[] = array(
                            'label' => $this->translate('Edit Listing'),
                            'name' => 'edit',
                            'url' => 'classifieds/edit/' . $classifiedObj->getIdentity(),
                            'urlParams' => array(
                            )
                        );

                        $tempMenu[] = array(
                            'label' => $this->translate('Add Photos'),
                            'name' => 'photo',
                            'url' => '',
                            'urlParams' => array(
                            )
                        );

                        $tempMenu[] = array(
                            'label' => $this->translate('Delete Listing'),
                            'name' => 'delete',
                            'url' => 'classifieds/delete/' . $classifiedObj->getIdentity(),
                            'urlParams' => array(
                            )
                        );
                    }

                    $value["menu"] = $tempMenu;
                }

                $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
                if (isset($classifiedObj->category_id) && !empty($categories))
                    $value['category_title'] = $categories[$classifiedObj->category_id];

                // Get Profile Fields
                $value['profile_fields'] = (($getProfileFields = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getProfileInfo($classifiedObj)) && !empty($getProfileFields)) ? $getProfileFields : array();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($classifiedObj);
                $value = array_merge($value, $getContentImages);

                $value["owner_title"] = $classifiedObj->getOwner()->getTitle();
//        $value["short_description"] = @substr(@strip_tags($classifiedObj->body), 0, 300);

                $isAllowedView = $classifiedObj->authorization()->isAllowed($viewer, 'view');
                $value["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $isAllowedEdit = $classifiedObj->authorization()->isAllowed($viewer, 'edit');
                $isAllowedDelete = $classifiedObj->authorization()->isAllowed($viewer, 'delete');
                if (empty($params['manage'])) {
                    $value["is_edit"] = empty($isAllowedEdit) ? 0 : 1;
                    $value["is_delete"] = empty($isAllowedDelete) ? 0 : 1;
                }

                $tempResponse[] = $value;
            }

            if (!empty($tempResponse))
                $response['response'] = $tempResponse;
        }

        if (!empty($siteapiClassifiedGetListing))
            return $response;
    }

    /**
     * Gutter menu show on the classified profile page.
     * 
     * @return array
     */
    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();

        if ($subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'label' => $this->translate('Edit'),
                'name' => 'edit',
                'url' => 'classifieds/edit/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );

            $menus[] = array(
                'label' => $this->translate('Add Photos'),
                'name' => 'photo',
                'url' => '',
                'urlParams' => array(
                )
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'label' => $this->translate('Delete'),
                'name' => 'delete',
                'url' => 'classifieds/delete/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        if ($viewer->getIdentity() && ($viewer->getIdentity() == $owner->getIdentity())) {
            $menus[] = array(
                'label' => ($subject->closed) ? $this->translate('Open') : $this->translate('Close'),
                'name' => 'close',
                'url' => 'classifieds/close/' . $subject->getIdentity(),
                'urlParams' => array(
                    "closed" => ($subject->closed) ? 0 : 1,
                )
            );
        }

        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );

            $menus[] = array(
                'label' => $this->translate('Report'),
                'name' => 'report',
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        return $menus;
    }

}
