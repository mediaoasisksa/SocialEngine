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
class Album_IndexController extends Siteapi_Controller_Action_Standard {

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'album')->getBrowseSearchForm(array()), true);
    }

    /**
     * Browse Album
     * 
     * @return array
     */
    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();
        $params = $this->_getAllParams();
        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        // Set the translate
        Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        $siteapiAlbumBrowse = Zend_Registry::isRegistered('siteapiAlbumBrowse') ? Zend_Registry::get('siteapiAlbumBrowse') : null;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerId = null;
        if (!empty($viewer)) {
            $viewerId = $viewer->getIdentity();
        }

        // Get params
        $settings = Engine_Api::_()->getApi('settings', 'core');

        // moved to Albums/widgets/browse-menu/Controller.php
        // // Get navigation
        // $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        //   ->getNavigation('album_main');
        // Get params
        switch ($this->_getParam('sort', 'recent')) {
            case 'popular':
                $order = 'view_count';
                break;
            case 'recent':
            default:
                $order = 'creation_date';
                break;
        }

        $userId = $this->_getParam('user_id');
        $excludedLevels = array(1, 2, 3);   // level_id of Superadmin,Admin & Moderator
        $isOwnerOrAdmin = false;
        if (!empty($viewerId) && ((isset($userId) && ($userId == $viewerId)) || in_array($viewer->level_id, $excludedLevels))) {
            $isOwnerOrAdmin = true;
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitealbum")) {
            if ($userId) {
                $user = Engine_Api::_()->getItem('user', $userId);
                $params['owner'] = $user;
            }
            $params['ignoreAlbum'] = 1 ;
            if(!isset($params['orderby']) || empty($params['orderby']))
                $params['orderby'] = $order;

            $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getAlbumPaginator($params);
        } else {

            $registeredPrivacy = array('everyone', 'registered');
            if ($viewer->getIdentity() && empty($userId)) {
                $viewerId = $viewer->getIdentity();
                $netMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                if (!empty($viewerNetwork)) {
                    array_push($registeredPrivacy, 'owner_network');
                }

                $friends = $viewer->membership()->getMembers();
                $friendsIds = array();
                foreach ($friends as $friend) {
                    $friendsIds[] = $friend->user_id;
                }
                $friendsOfFriendsIds = $friendsIds;
                foreach ($friendsIds as $friendId) {
                    $friend = Engine_Api::_()->getItem('user', $friendId);
                    $friendsFriends = $friend->membership()->getMembers();
                    $friendMembersIds = array();
                    foreach ($friendsFriends as $friendSn) {
                        $friendMembersIds[] = $friendSn->user_id;
                    }
                    $friendsOfFriendsIds = array_merge($friendsOfFriendsIds, $friendMembersIds);
                }
            }

            // Prepare data
            $table = Engine_Api::_()->getItemTable('album');
            if (!in_array($order, $table->info('cols'))) {
                $order = 'creation_date';
            }

            $select = $table->select();
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $selectVersion = new Zend_Db_Select($db);
            $coreVersion = $selectVersion
                    ->from('engine4_core_modules', 'version')
                    ->where('name = ?', 'core')
                    ->query()
                    ->fetchColumn();

            $existView_privay = $db->query("SHOW COLUMNS FROM engine4_album_albums LIKE 'view_privacy'")->fetch();

            if (!empty($coreVersion) && $coreVersion > '4.9.0' && !empty($existView_privay)) {
                if (!$viewer->getIdentity()) {
                    $select->where("view_privacy = ?", 'everyone');
                } elseif ($userId) {
                    $owner = Engine_Api::_()->getItem('user', $userId);
                    if ($owner) {
                        $select = $table->getAlbumSelect(array('owner' => $owner));
                    }
                } elseif (!in_array($viewer->level_id, $excludedLevels) && $registeredPrivacy) {
                    $select->Where("owner_id = ?", $viewerId)
                            ->orwhere("view_privacy IN (?)", $registeredPrivacy);
                    if (!empty($friendsIds)) {
                        $select->orWhere("view_privacy = 'owner_member' AND owner_id IN (?)", $friendsIds);
                    }
                    if (!empty($friendsOfFriendsIds)) {
                        $select->orWhere("view_privacy = 'owner_member_member' AND owner_id IN (?)", $friendsOfFriendsIds);
                    }
                    if (empty($viewerNetwork) && !empty($friendsOfFriendsIds)) {
                        $select->orWhere("view_privacy = 'owner_network' AND owner_id IN (?)", $friendsOfFriendsIds);
                    }

                    $subquery = $select->getPart(Zend_Db_Select::WHERE);
                    $select->reset(Zend_Db_Select::WHERE);
                    $select->where(implode(' ', $subquery));
                }
            } else {
                if ($userId) {
                    $owner = Engine_Api::_()->getItem('user', $userId);
                    if ($owner) {
                        $select = $table->getAlbumSelect(array('owner' => $owner));
                    }
                }
            }

            if (!$isOwnerOrAdmin) {
                $select->where("search = 1")
                        ->order($order . ' DESC');
            } else {
                $select->order('creation_date DESC');
            }
            if ($this->_getParam('category_id'))
                $select->where("category_id = ?", $this->_getParam('category_id'));

            if ($this->_getParam('search', false)) {
                $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
            }
            
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitealbum")) {
                if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
                    $select->where('type IS NULL');
            }
            $paginator = Zend_Paginator::factory($select);
        }

        $response['response'] = '';
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');


        $requestPage = $this->getRequestParam("page", 1);
        $limit = $this->getRequestParam("limit", 20);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($requestPage);
        $response['totalItemCount'] = $paginator->getTotalItemCount();

        $bodyParams = array();
        foreach ($paginator as $album) {
            $tempAlbum = $album->toArray();
            $tempAlbum['search'] = 1;

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album);
            $tempAlbum = array_merge($tempAlbum, $getContentImages);

            // Getting viewer like or not to content.
            $tempAlbum["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($album);

            $isAllowedToView = $album->authorization()->isAllowed($viewer, 'view');
            $tempAlbum["allow_to_view"] = empty($isAllowedToView) ? 0 : 1;

            // Getting like count.
            $tempAlbum["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($album);

            try {
                $owner = $album->getOwner();
                $tempAlbum["owner_title"] = $album->getOwner()->getTitle();
            } catch (Exception $ex) {
                $tempAlbum["owner_title"] = '';
            }
            if($album->count() > 0)
                $tempAlbum["photo_count"] = $album->count();
            $bodyParams[] = $tempAlbum;
        }

        if (!empty($bodyParams))
            $response['response'] = $bodyParams;
        $response['filter'] = $this->filterForm();

        if (!empty($siteapiAlbumBrowse))
            $this->respondWithSuccess($response, true);
    }

    /**
     * Manage Page
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

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        // Get params
        $page = $this->getRequestParam('page');
        $limit = $this->getRequestParam('limit', 20);
        $siteapiAlbumManage = Zend_Registry::isRegistered('siteapiAlbumManage') ? Zend_Registry::get('siteapiAlbumManage') : null;

        // Get params
        switch ($this->getRequestParam('sort', 'recent')) {
            case 'popular':
                $order = 'view_count';
                break;
            case 'recent':
            default:
                $order = 'creation_date';
                break;
        }

        // Prepare data
        $table = Engine_Api::_()->getItemTable('album');

        if (!in_array($order, $table->info('cols'))) {
            $order = 'creation_date';
        }

        $user_id = $this->getRequestParam('user_id', $viewer_id);
        $select = $table->select()
                ->where('owner_id = ?', $user_id)
                ->order($order . ' DESC');
        ;

        if ($this->getRequestParam('category_id'))
            $select->where("category_id = ?", $this->getRequestParam('category_id'));

        if ($this->getRequestParam('search', false)) {
            $select->where('title LIKE ? OR description LIKE ?', '%' . $this->getRequestParam('search') . '%');
        }

        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $response['totalItemCount'] = $paginator->getTotalItemCount();

        $bodyParams = array();
        foreach ($paginator as $album) {
            $tempAlbum = $album->toArray();

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album);
            $tempAlbum = array_merge($tempAlbum, $getContentImages);

            // Getting viewer like or not to content.
            $tempAlbum["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($album);

            // Getting like count.
            $tempAlbum["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($album);

            $tempAlbum["owner_title"] = $album->getOwner()->getTitle();
            $tempAlbum["photo_count"] = $album->count();

            $tempAlbum['menu'][] = array(
                'label' => $this->translate('Edit Settings'),
                'name' => 'edit',
                'url' => 'albums/edit/' . $album->album_id,
                'urlParams' => array(
                )
            );

            $tempAlbum['menu'][] = array(
                'label' => $this->translate('Delete Album'),
                'name' => 'delete',
                'url' => 'albums/delete/' . $album->album_id,
                'urlParams' => array(
                )
            );

            if (isset($_REQUEST['addPhotoLink'])) {
                $tempAlbum['menu'][] = array(
                    'label' => $this->translate('Add Photos'),
                    'name' => 'photo',
                    'url' => '',
                    'urlParams' => array(
                    )
                );
            }

            $bodyParams[] = $tempAlbum;
        }

        if (!empty($bodyParams))
            $response['response'] = $bodyParams;

        if (!empty($siteapiAlbumManage))
            $this->respondWithSuccess($response);
    }

    /**
     * Upload photos in album OR create new album.
     * 
     * @return array
     */
    public function uploadAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            $this->respondWithError('unauthorized');
        } else {
            $level_id = $viewer->level_id;
        }

        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToCreate = $permissionsTable->getAllowed('album', $level_id, 'create');
        }

        if (empty($allowToCreate))
            $this->respondWithError('unauthorized');


        /* RETURN THE ALBUM CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'album')->getForm());
        } else if ($this->getRequest()->isPost()) {
            /* CREATE THE GROUP IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('album', $viewer->level_id, 'flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("albums",'album');
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

            $photos_id = array();
            // CONVERT POST DATA INTO THE ARRAY.
            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'album')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            if (isset($_REQUEST['album_id']) && !empty($_REQUEST['album_id']))
                $values['album_id'] = $_REQUEST['album_id'];

            $data = $values = @array_merge($values, array(
                        'owner_type' => 'user',
                        'owner_id' => $viewer_id
            ));

            if (!isset($data['album_id']) && empty($data['album_id'])) {
                // START FORM VALIDATION
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'album')->getFormValidators();
                $data['validators'] = $validators;
                $validationMessage = $this->isValid($data);
                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                $availableLabels = array(
                    'everyone' => $this->translate('Everyone'),
                    'registered' => $this->translate('All Registered Members'),
                    'owner_network' => $this->translate('Friends and Networks'),
                    'owner_member_member' => $this->translate('Friends of Friends'),
                    'owner_member' => $this->translate('Friends Only'),
                    'owner' => $this->translate('Just Me')
                );

                // Element: auth_view
                $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_view');
                $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

                if (!empty($viewOptions) && count($viewOptions) == 1) {
                    $data['auth_view'] = key($viewOptions);
                }
                if (!isset($data['auth_view']) || empty($data['auth_view']))
                    $data['auth_view'] = 'everyone';

                // Element: auth_comment
                $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_comment');
                $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

                if (!empty($commentOptions) && count($commentOptions) == 1) {
                    $data['auth_comment'] = key($commentOptions);
                }
                if (!isset($data['auth_comment']) || empty($data['auth_comment']))
                    $data['auth_comment'] = 'everyone';

                // Element: auth_tag
                $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_tag');
                $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));

                if (!empty($tagOptions) && count($tagOptions) == 1) {
                    $data['auth_tag'] = key($tagOptions);
                }
                if (!isset($data['auth_tag']) || empty($data['auth_tag']))
                    $data['auth_tag'] = 'everyone';

                $db = Engine_Api::_()->getItemTable('album')->getAdapter();
                $db->beginTransaction();

                try {
                    $data['photos_count'] = 1;
                    $table = Engine_Api::_()->getDbtable('albums', 'album');
                    $album = $table->createRow();
                    $album->setFromArray($data);
                    $album->save();
                    $album_id = $album->getIdentity();
                    // CREATE AUTH STUFF HERE
                    $auth = Engine_Api::_()->authorization()->context;
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    $viewMax = array_search($data['auth_view'], $roles);
                    $commentMax = array_search($data['auth_comment'], $roles);
                    $tagMax = array_search($data['auth_tag'], $roles);

                    foreach ($roles as $i => $role) {
                        $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
                        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
                        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
                    }

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    $this->respondWithValidationError('internal_server_error', $e->getMessage());
                }
            }

            if (isset($_FILES)) {
                if (!$this->_helper->requireUser()->checkRequire())
                    $this->respondWithError('invalid_file_size');

                $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
                $db->beginTransaction();

                try {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    foreach ($_FILES as $file) {
                        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                        $photo = $photoTable->createRow();
                        $photo->setFromArray(array(
                            'owner_type' => 'user',
                            'owner_id' => $viewer->getIdentity()
                        ));
                        $photo->save();

                        $photo->order = $photo->photo_id;
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum')) {
                            ;
                            $photo = Engine_Api::_()->getApi('core', 'siteapi')->setPhoto($file, $photo);
                        } else
                            $photo = Engine_Api::_()->getApi('Siteapi_Core', 'album')->setPhoto($file, $photo);

                        $photos_id[] = $photo->photo_id;

                        $photo->album_id = $album_id = (isset($data['album_id']) && !empty($data['album_id'])) ? $data['album_id'] : $album->album_id;
                        $photo->save();
                        if (!empty($album) && !isset($album->photo_id) && !empty($album->photo_id)) {
                            $album->photo_id = $photo->photo_id;
                            $album->save();
                        }
                    }
                    $db->commit();
                } catch (Album_Model_Exception $e) {
                    $db->rollBack();
                    $this->respondWithValidationError('internal_server_error', $e->getMessage());
                } catch (Exception $e) {
                    $db->rollBack();
                    $this->respondWithValidationError('internal_server_error', $e->getMessage());
                }
            }

            $db->beginTransaction();
            try {
                if (!isset($album) || empty($album)) {
                    $album = $photo->getParent();
                }

                $owner = $album->getOwner();

                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $album, 'album_photo_new', null, array('count' => count($_FILES)));
                $count = 0;
                if ($action != null) {
                    foreach ($photos_id as $photoId) {
                        $photo = Engine_Api::_()->getItem("album_photo", $photoId);
                        if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                            continue;
                        if ($action instanceof Activity_Model_Action && $count < 100) {
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                        }
                        $count++;
                    }
                }

                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($album) as $action) {
                    $actionTable->resetActivityBindings($action);
                }
                $db->commit();
            } catch (Exception $ex) {
                
            }
        }

        if (empty($album_id))
            $this->respondWithError('unauthorized');

        // Change request method POST to GET
        $this->setRequestMethod();

        $this->_forward('view', 'album', 'album', array(
            'album_id' => $album_id
        ));
    }

    /*
     * Get all photos
     */

    public function viewAlbumAction() {

        $values = $this->_getAllParams();

        $subject_type = $values['subject_type'];
        $subject_id = $values['subject_id'];

        Engine_Api::_()->core()->clearSubject();

        $type = substr($subject_type, -5);

        if ($type == 'album')
            $album = Engine_Api::_()->getItem($subject_type, $subject_id);
        else {
            $photo = Engine_Api::_()->getItem($subject_type, $subject_id);

            if (!$photo)
                $this->respondWithError("no_record");

            $subject_type = substr($subject_type, 0, -5) . "album";

            $album = Engine_Api::_()->getItem($subject_type, $photo->album_id);
        }

        if (!$album)
            $this->respondWithError('no_record');
        $values['album_id'] = $album->getIdentity();

        switch ($subject_type) {
            case "sitepage_album":
                $values['page_id'] = $album->page_id;
                $this->_forward('viewalbum', 'photo', 'sitepagealbum', $values);
                return;
                break;
            case "album":
                $values['album_id'] = $album->album_id;
                $this->_forward('view', 'album', 'album', $values);
                return;
                break;
            case "classified_album":
                $values['classified_id'] = $album->classified_id;
                $this->_forward('list', 'photo', 'classified', $values);
                return;
                break;
            case "group_album":
                $values['group_id'] = $album->group_id;
                $this->_forward('list', 'photo', 'group', $values);
                return;
                break;
            case "siteevent_album":
                $values['event_id'] = $album->event_id;
                $this->_forward('list', 'photo', 'siteevent', $values);
                return;
                break;
            case "event_album":
                $values['event_id'] = $album->event_id;
                $this->_forward('list', 'photo', 'event', $values);
                return;
                break;
            case "sitegroup_album":
                $values['group_id'] = $album->group_id;
                $this->_forward('viewalbum', 'photo', 'sitegroupalbum', $values);
                return;
                break;
            case "sitereview_album":
                // not working roght now as the developer has not found the url for album view in sitereview
                $listing = Engine_Api::_()->getItem('sitereview_listing', $album->listing_id);
                $values['listingtype_id'] = $listing->listingtype_id;
                $values['listing_id'] = $album->listing_id;
                $this->_forward('list', 'photo', 'sitereview', $values);
                return;
                break;
            case "sitestore_album":
                $values['store_id'] = $album->store_id;
                $this->_forward('view-album', 'photo', 'sitestore', $values);
                return;
                break;
            // sitestoreproduct and sitestoreoffer album are yet to be created (i will create them)
            // apis for sitevideo_album and sitesuggestion_album have not been made yet
        }

        $this->successResponseNoContent('no_content');
    }

    public function viewContentAlbumAction() {
        try {

            $values = $this->_getAllParams();
            $settings = Engine_Api::_()->getApi('settings', 'core');

            $subject_type = $values['subject_type'];
            $subject_id = $values['subject_id'];

            Engine_Api::_()->getApi('Core', 'siteapi')->setView();
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
            $viewer = Engine_Api::_()->user()->getViewer();
            $type = substr($subject_type, -5);

            if ($type == 'album') {

                $album = Engine_Api::_()->getItem($subject_type, $subject_id);
                if ($album)
                    $values['album_id'] = $album->getIdentity();
            }
            else {

                $subject = Engine_Api::_()->getItem($subject_type, $subject_id);

                if ($subject)
                    $album = $subject->getSingletonAlbum();

                if ($album)
                    $values['album_id'] = $album->getIdentity();
            }

            // Prepare params
            $page = $this->getRequestParam('page', 1);
            $limit = $this->getRequestParam('limit', 20);
            if (isset($values['album_id']) && !empty($values['album_id'])) {
                try {
                    if (($subject_type == 'album') && !empty($album) && !empty($album)) {
                        $photoTable = Engine_Api::_()->getItemTable('album_photo');
                        $paginators = $photoTable->getPhotoPaginator(array(
                            'album' => $album,
                        ));
                    } else {
                        $paginators = $album->getCollectiblesPaginator();
                    }
                } catch (Exception $ex) {
                    // Blank Exception
                }
            } else
                $this->respondWithError('no_record');
            $paginators->setItemCountPerPage($limit);
            $paginators->setCurrentPageNumber($page);
            $total_photo = $paginators->getTotalItemCount();
            $photos = array();
            $canEdit = 0;
            if ($viewer->getIdentity()) {
                if (isset($viewer->level_id) && $viewer->level_id == 1)
                    $canEdit = 1;

                $viewer_id = $viewer->getIdentity();
                if (isset($viewer_id) && isset($album->owner_id) && $album->owner_id == $viewer_id)
                    $canEdit = 1;
            }
            if(!empty($subject))    {
                if($subject->getType() == 'sitereview_listing')
                    $canEdit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");
            }

            $response["canEdit"] = $canEdit;
            if ($total_photo > 0) {
                foreach ($paginators as $photo) {
                    $data = $photo->toArray();
                    $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, false));
                    $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, true));
                    $data["canComment"] = $photo->authorization()->isAllowed($viewer, 'comment');
                    $data['user_title'] = $photo->getOwner()->getTitle();
                    $data['like_count'] = $photo->likes()->getLikeCount();
                    $data['is_like'] = ($photo->likes()->isLike($viewer)) ? 1 : 0;
                    $data['isLike'] = ($photo->likes()->isLike($viewer)) ? 'true' : 'false';
                    $data["canLike"] = ($photo->likes()->isLike($viewer)) ? 1 : 0;
                    $data['menu'] = $this->_getPhotoMenus($album, $photo, $canEdit);
                    $data['reactions'] = $this->getPhotoReaction($photo);
                    $data['tags'] = Engine_Api::_()->getApi('Siteapi_Core', 'album')->getPhotoTag($photo);
                    $data["canTag"] = !empty($canTag) ? $canTag : 0;
                    $photos[] = $data;
                    unset($data);
                }
            } else
                $photos = null;

            $response = array();

            $getBodyResponse['reactionsEnabled'] = 0;
            $getBodyResponse['stickersEnabled'] = 0;
            $getBodyResponse['emojiEnabled'] = 0;

            try {
                if (Engine_Api::_()->getApi('Siteapi_Feed', 'advancedactivity')->isSitereactionPluginLive() && isset($values['getReaction']) && $values['getReaction']) {
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereaction.reaction.active', 1)) {
                        $getBodyResponse['reactionsEnabled'] = 1;
                        $getBodyResponse['reactions'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitereaction')->getAllReactionIcons();
                    }
                }
                if (Engine_Api::_()->getApi('Siteapi_Feed', 'advancedactivity')->isSitestickerPluginLive()) {
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('nestedcomment') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereaction.collection.active', 1)) {
                        $getBodyResponse['stickersEnabled'] = 1;
                        $getBodyResponse['emojiEnabled'] = 1;
                    }
                }
                $response['reactions'] = $getBodyResponse;
            } catch (Exception $ex) {
                //Blank Exception
            }

            $response['totalPhotoCount'] = $total_photo;
            $response['albumPhotos'] = $photos;
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Returns the contents of the album (photos)
     *
     */
    private function _getPhotoMenus($album, $photo, $canEdit) {
        $album_type = $album->getType();
        $album_id = $album->getIdentity();
        $photo_id = $photo->getIdentity();
        if ($canEdit) {
            switch ($album_type) {
                case "sitepage_album":
                    $page_id = $album->page_id;
                    $menu[] = array(
                        'label' => $this->translate("Edit Photo"),
                        'name' => 'edit',
                        'url' => 'advancedgroups/photos/editphoto/' . $page_id . '/' . $album_id . '/' . $photo_id,
                    );
                    $menu[] = array(
                        'label' => $this->translate("Delete Photo"),
                        'name' => 'delete',
                        'url' => 'advancedgroups/photos/deletephoto/' . $page_id . '/' . $album_id . '/' . $photo_id,
                    );
                    break;
                case "album":
                    $values['album_id'] = $album->album_id;
                    $menu[] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'albums/photo/edit',
                        'urlParams' => array(
                            "album_id" => $album_id,
                            "photo_id" => $photo_id
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'albums/photo/delete',
                        'urlParams' => array(
                            "album_id" => $album_id,
                            "photo_id" => $photo_id
                        )
                    );
                    break;
                case "sitegroup_album":
                    $group_id = $album->group_id;
                    $menu[] = array(
                        'label' => $this->translate("Edit Photo"),
                        'name' => 'edit',
                        'url' => 'advancedgroups/photos/editphoto/' . $group_id . '/' . $album_id . '/' . $photo_id,
                    );
                    $menu[] = array(
                        'label' => $this->translate("Delete Photo"),
                        'name' => 'delete',
                        'url' => 'advancedgroups/photos/deletephoto/' . $group_id . '/' . $album_id . '/' . $photo_id,
                    );
                    break;
                case "group_album":
                    $menu[] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'groups/photo/edit/' . $photo->getIdentity(),
                        'urlParams' => array(
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'groups/photo/delete/' . $photo->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                    break;
                case "siteevent_album":
                    $menu[] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'advancedevents/photo/edit/' . $photo_id,
                    );

                    $menu[] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'advancedevents/photo/delete/' . $photo_id,
                    );
                    break;
                case "event_album":
                    $menu[] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'events/photo/edit/' . $photo->getIdentity(),
                        'urlParams' => array(
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'events/photo/delete/' . $photo->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                    break;
                case "sitereview_album":
                    $values['listing_id'] = $album->listing_id;
                    $menu[] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'listings/photo/edit/' . $values['listing_id'],
                        'urlParams' => array(
                            "photo_id" => $photo->getIdentity()
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'listings/photo/delete/' . $values['listing_id'],
                        'urlParams' => array(
                            "photo_id" => $photo->getIdentity()
                        )
                    );
                    break;
                case "sitestore_album":
                    $values['store_id'] = $album->store_id;

                    break;
                // sitestoreproduct and sitestoreoffer album are yet to be created (i will create them)
                // apis for sitevideo_album and sitesuggestion_album have not been made yet
            }
        }
        $menu[] = array(
            'label' => $this->translate('Share'),
            'name' => 'share',
            'url' => 'activity/index/share',
            'urlParams' => array(
                "type" => $photo->getType(),
                "id" => $photo->getIdentity()
            )
        );

        $menu[] = array(
            'label' => $this->translate('Report'),
            'name' => 'report',
            'url' => 'report/create/subject/' . $photo->getGuid(),
            'urlParams' => array(
                "type" => $photo->getType(),
                "id" => $photo->getIdentity()
            )
        );

        $menu[] = array(
            'label' => $this->translate('Make Profile Photo'),
            'name' => 'make_profile_photo',
            'url' => 'members/edit/external-photo',
            'urlParams' => array(
                "photo" => $photo->getGuid()
            )
        );
        return $menu;
    }

    private function getPhotoReaction($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $getReaction = $this->_getParam('getReaction', 0);

        if (!isset($subject) || empty($subject))
            $this->respondWithError('no_record');

        try {

            //Sitereaction Plugin work start here
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereaction.reaction.active', 1)) {
                $popularity = Engine_Api::_()->getApi('core', 'sitereaction')->getLikesReactionPopularity($subject);
                $feedReactionIcons = Engine_Api::_()->getApi('Siteapi_Core', 'sitereaction')->getLikesReactionIcons($popularity, 1);
                $response['feed_reactions'] = $feedReactionIcons;

                if (isset($viewer_id) && !empty($viewer_id)) {
                    $myReaction = $subject->likes()->getLike($viewer);
                    if (isset($myReaction) && !empty($myReaction) && isset($myReaction->reaction) && !empty($myReaction->reaction)) {
                        $myReactionIcon = Engine_Api::_()->getApi('Siteapi_Core', 'sitereaction')->getIcons($myReaction->reaction, 1);
                        $response['my_feed_reaction'] = $myReactionIcon;
                    }
                }
            }
            return $response;
        } catch (Exception $ex) {
            return;
        }
        //Sitereaction Plugin work end here
    }

    private function getLocationObject($params = array()) {
        if (!empty($params['restapilocation'])) {
            $params['location'] = $params['restapilocation'];
            $specificLocationsDetails = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getSpecificLocationRow($params['location']);
            $params['Latitude'] = $specificLocationsDetails->latitude;
            $params['Longitude'] = $specificLocationsDetails->longitude;

            if (empty($params['Latitude']) && isset($params['Latitude'])) {
                unset($params['Latitude']);
            }

            if (empty($_GET['Longitude']) && isset($params['Longitude'])) {
                unset($params['Longitude']);
            }
        }
        return $params;
    }
    
    function filterForm()
    {
                //filter work
        $categories = Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc();
        if (count($categories) > 0) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => $categories
            );
        }
        
        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'sort',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'none'=>'None',
                'recent' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'popular' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
            )
        );
            
            return $searchForm;
         
   
    }

}
