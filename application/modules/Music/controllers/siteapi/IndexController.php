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
class Music_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        // Check auth
        if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
            $this->_forward('throw-error', 'index', 'music', array(
                "error_code" => "unauthorized"
            ));
            return;
        }

        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

        $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

        $this->getHost = '';
        if ($getDefaultStorageType == 'local')
            $this->getHost = !empty($staticBaseUrl) ? $staticBaseUrl : $serverHost;
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
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'music')->getBrowseSearchForm(), true);
    }

    /**
     * Browse page of music
     *
     * @return array
     */
    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        // Can create?
        $response = array();
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');
        $siteapiMusicBrowse = Zend_Registry::isRegistered('siteapiMusicBrowse') ? Zend_Registry::get('siteapiMusicBrowse') : null;

        // Set search params.
        $values = array();
        $values = $this->getRequestAllParams;

        // Show
        $viewer = Engine_Api::_()->user()->getViewer();
        if (@$values['show'] == 2 && $viewer->getIdentity()) {
            // Get an array of friend ids
            $values['users'] = $viewer->membership()->getMembershipsOfIds();
        }
        unset($values['show']);

        $user_id = $this->getRequestParam('user_id', null);
        if (!empty($user_id))
            $values['user'] = $user_id;

        // Get paginator
        $requestLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10);
        $requestPage = $this->getRequestParam("page", 1);

        $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
        $paginator->setItemCountPerPage($requestLimit);
        $paginator->setCurrentPageNumber($requestPage);

        foreach ($paginator as $playlist) {
            $browseMusic = $playlist->toArray();
            $browseMusic['title'] =@html_entity_decode(Engine_Api::_()->getApi('Core', 'siteapi')->translate($browseMusic['title']), ENT_QUOTES, "utf-8");
            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist);
            $browseMusic = array_merge($browseMusic, $getContentImages);

            // Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist, true);
            $browseMusic = array_merge($browseMusic, $getContentImages);

            $browseMusic["owner_title"] = $playlist->getOwner()->getTitle();

            $getPlaylistSongs = $playlist->getSongs();
            foreach ($getPlaylistSongs as $songObj) {
                $songArray = $songObj->toArray();
                $songArray['filePath'] = $this->getHost . $songObj->getFilePath();
                $browseMusic['playlist_songs'][] = $songArray;
            }

            $response['response'][] = $browseMusic;
        }

        $response['totalItemCount'] = $paginator->getTotalItemCount();

        if (!empty($siteapiMusicBrowse))
            $this->respondWithSuccess($response, true);
    }

    /**
     * Manage page of music
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

        $response = array();
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');
        $siteapiMusicBrowse = Zend_Registry::isRegistered('siteapiMusicBrowse') ? Zend_Registry::get('siteapiMusicBrowse') : null;

        $values = array();
        $values = $this->getRequestAllParams;

        // Get paginator
        $requestLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10);
        $requestPage = $this->getRequestParam("page", 1);
        $values['user'] = $this->getRequestParam('user_id', $viewer_id);
        $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
        $paginator->setItemCountPerPage($requestLimit);
        $paginator->setCurrentPageNumber($requestPage);

        foreach ($paginator as $playlist) {
            $browseMusic = $playlist->toArray();

            $browseMusic['title'] =@html_entity_decode(Engine_Api::_()->getApi('Core', 'siteapi')->translate($browseMusic['title']), ENT_QUOTES, "utf-8");
            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist);
            $browseMusic = array_merge($browseMusic, $getContentImages);

            // Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist, true);
            $browseMusic = array_merge($browseMusic, $getContentImages);

            $browseMusic["owner_title"] = $playlist->getOwner()->getTitle();

            // GETTING THE GUTTER-MENUS.
            if ($this->getRequestParam('gutter_menu', true))
                $browseMusic['menu'] = $this->_gutterMenus($playlist);

            $getPlaylistSongs = $playlist->getSongs();
            foreach ($getPlaylistSongs as $songObj) {
                $songArray = $songObj->toArray();
                $songArray['filePath'] = $this->getHost . $songObj->getFilePath();
                $browseMusic['playlist_songs'][] = $songArray;
            }

            $response['response'][] = $browseMusic;
        }

        $response['totalItemCount'] = $paginator->getTotalItemCount();

        if (!empty($siteapiMusicBrowse))
            $this->respondWithSuccess($response);
    }

    /**
     * Create music playlist
     *
     * @return array
     */
    public function createAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid())
            $this->respondWithError('unauthorized');

        $siteapiMusicCreate = Zend_Registry::isRegistered('siteapiMusicCreate') ? Zend_Registry::get('siteapiMusicCreate') : null;

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Get form
        $playlist_id = $this->_getParam('playlist_id', 0);

        /* RETURN THE Playlist CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'music')->getForm());
        } else if (!empty($siteapiMusicCreate) && $this->getRequest()->isPost()) {
            /* CREATE THE Playlist IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('music_playlist', $viewer->level_id, 'flood');
                if(!empty($itemFlood[0])){
                        //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("playlists",'music');
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
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'music')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $viewer = Engine_Api::_()->user()->getViewer();
            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity()
            ));

            // START FORM VALIDATION
            $data = $values;
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'music')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);

            if (empty($_FILES['songs'])) {
                if (!is_array($validationMessage))
                    $validationMessage = array();

                $validationMessage['songs'] = $this->translate("There are no songs available to upload - it is required.");
            }

            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Process
            $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
            $db->beginTransaction();
            try {
                if (!empty($playlist_id)) {
                    $playlist = Engine_Api::_()->getItem('music_playlist', $playlist_id);
                } else {
                    $playlist = Engine_Api::_()->getDbtable('playlists', 'music')->createRow();
                    $playlist->setFromArray($values);
                    $playlist->save();

                    if ($playlist->search) {
                        $activity = Engine_Api::_()->getDbtable('actions', 'activity');
                        $action = $activity->addActivity(
                                Engine_Api::_()->user()->getViewer(), $playlist, 'music_playlist_new'
                        );
                        if (null !== $action)
                            $activity->attachActivity($action, $playlist);
                    }
                }

                $roles = array(
                    'everyone' => $this->translate('Everyone'),
                    'registered' => $this->translate('All Registered Members'),
                    'owner_network' => $this->translate('Friends and Networks'),
                    'owner_member_member' => $this->translate('Friends of Friends'),
                    'owner_member' => $this->translate('Friends Only'),
                    'owner' => $this->translate('Just Me')
                );

                // Authorizations
                $auth = Engine_Api::_()->authorization()->context;
                $prev_allow_comment = $prev_allow_view = false;
                foreach ($roles as $role => $role_label) {
                    // allow viewers
                    if ($values['auth_view'] == $role || $prev_allow_view) {
                        $auth->setAllowed($playlist, $role, 'view', true);
                        $prev_allow_view = true;
                    } else
                        $auth->setAllowed($playlist, $role, 'view', 0);

                    // allow comments
                    if ($values['auth_comment'] == $role || $prev_allow_comment) {
                        $auth->setAllowed($playlist, $role, 'comment', true);
                        $prev_allow_comment = true;
                    } else
                        $auth->setAllowed($playlist, $role, 'comment', 0);
                }

                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($playlist) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                if (!empty($_FILES['photo'])) {
                    $playlist = Engine_Api::_()->getApi('Siteapi_Core', 'music')->setPhoto($_FILES['photo'], $playlist);
                    unset($_FILES['photo']);
                }

                if (!empty($_FILES['songs'])) {
                    if (!$this->_helper->requireUser()->checkRequire())
                        $this->respondWithError('invalid_file_size');

                    $arrayDepth = Engine_Api::_()->getApi('Siteapi_Core', 'music')->array_depth($_FILES);
                    if ($arrayDepth == 1) {
                        Engine_Api::_()->getApi('Siteapi_Core', 'music')->uploadSong($_FILES['songs'], $playlist);
                    } else {
                        foreach ($_FILES as $song) {
                            Engine_Api::_()->getApi('Siteapi_Core', 'music')->uploadSong($song, $playlist);
                        }
                    }
                }

                $db->commit();

                // Change request method POST to GET
                $this->setRequestMethod();

                $this->_forward('view', 'playlist', 'music', array(
                    'playlist_id' => $playlist->getIdentity()
                ));
            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Gutter menu show on the blog profile page.
     * 
     * @return array
     */
    private function _gutterMenus($playlist) {
        $owner = $playlist->getOwner();
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($playlist->isDeletable() || $playlist->isEditable()) {
            if ($playlist->isEditable()) {
                $menus[] = array(
                    'label' => $this->translate('Edit Playlist'),
                    'name' => 'edit',
                    'url' => '/music/playlist/edit/' . $playlist->getIdentity(),
                    'urlParams' => array(
                    )
                );
            }

            if ($playlist->isDeletable()) {
                $menus[] = array(
                    'label' => $this->translate('Delete Playlist'),
                    'name' => 'delete',
                    'url' => '/music/playlist/delete/' . $playlist->getIdentity(),
                    'urlParams' => array(
                    )
                );
            }
        }

        return $menus;
    }

}
