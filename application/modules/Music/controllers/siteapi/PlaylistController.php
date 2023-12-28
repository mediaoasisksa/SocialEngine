<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PlaylistController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Music_PlaylistController extends Siteapi_Controller_Action_Standard {

    public function init() {
        // Check auth
        if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
            $this->_forward('throw-error', 'playlist', 'music', array(
                "error_code" => "unauthorized"
            ));
            return;
        }

        // Get subject
        if (null !== ($playlist_id = $this->getRequestParam('playlist_id')) && null !== ($playlist = Engine_Api::_()->getItem('music_playlist', $playlist_id)) && $playlist instanceof Music_Model_Playlist && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($playlist);
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
     * Get playlist
     *
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (Engine_Api::_()->core()->hasSubject())
            $playlist = Engine_Api::_()->core()->getSubject('music_playlist');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($playlist))
            $this->respondWithError('no_record');

        $bodyParams = $playlist->toArray();

        $bodyParams['title'] = @html_entity_decode(Engine_Api::_()->getApi('Core', 'siteapi')->translate($bodyParams['title']), ENT_QUOTES, "utf-8");
        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist);
        $bodyParams = array_merge($bodyParams, $getContentImages);

        // Add owner images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist, true);
        $bodyParams = array_merge($bodyParams, $getContentImages);

        // Getting viewer like or not to content.
        $bodyParams["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($playlist);

        // Getting like count.
        $bodyParams["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($playlist);

        $bodyParams["owner_title"] = $playlist->getOwner()->getTitle();

        //Member verification Work...............
        $bodyParams['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($playlist->getOwner());

        $getPlaylistSongs = $playlist->getSongs();
        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);
        $getParentHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseParentUrl = @trim($baseParentUrl, "/");
        $songHost = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl;
        foreach ($getPlaylistSongs as $songObj) {
            $songArray = $songObj->toArray();
            $songHost = $this->getHost;
            if ((strstr($songObj->getFilePath(), 'http://')) || (strstr($songObj->getFilePath(), 'https://'))) {
                $songHost = '';
            }

            $songArray['filePath'] = $songHost . $songObj->getFilePath();
            $bodyParams['playlist_songs'][] = $songArray;
        }

        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($playlist);

        // Increment view count
        if (!$viewer->isSelf($playlist->getOwner())) {
            $playlist->view_count++;
            $playlist->save();
        }

//    // if this is sending a message id, the user is being directed from a coversation
//    // check if member is part of the conversation
//    $message_view = false;
//    if ( null !== ($message_id = $this->getRequestParam('message')) ) {
//      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
//      $message_view = $conversation->hasRecipient($viewer);
//    }
//    $bodyParams['message_view'] = $message_view;
        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Edit created playlist
     *
     * @return array
     */
    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $playlist = Engine_Api::_()->core()->getSubject('music_playlist');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($playlist))
            $this->respondWithError('no_record');

        // only user and admins and moderators can edit
        if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'music')->getForm($playlist);
            $formValues = $playlist->toArray();
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $roleString) {
                $role = $roleString;
                if (1 == $auth->isAllowed($playlist, $role, "view")) {
                    $formValues['auth_view'] = $roleString;
                }

                if (1 == $auth->isAllowed($playlist, $role, "comment")) {
                    $formValues['auth_comment'] = $roleString;
                }
            }

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            // CONVERT POST DATA INTO THE ARRAY.
            $data = $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'music')->getForm($playlist);
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }
            $data = $values;

            // START FORM VALIDATION
            $viewer = Engine_Api::_()->user()->getViewer();
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'music')->getFormValidators($playlist);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
            $db->beginTransaction();
            try {
                $playlist->setFromArray($values);
                $playlist->save();

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

                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($playlist, $role, "view", ($i <= $viewMax));
                    $auth->setAllowed($playlist, $role, "comment", ($i <= $commentMax));
                }

                $db->commit();
                $this->successResponseNoContent('no_content', 'siteapi_music');
            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Delete created playlist
     *
     * @return array
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        if (Engine_Api::_()->core()->hasSubject())
            $playlist = Engine_Api::_()->core()->getSubject('music_playlist');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($playlist))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'delete')->isValid())
            $this->respondWithError('unauthorized');

        $db = $playlist->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            foreach ($playlist->getSongs() as $song) {
                $song->deleteUnused();
            }
            $playlist->delete();
            $db->commit();
            $this->successResponseNoContent('no_content', 'siteapi_music');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Add song in playlist
     *
     * @return array
     */
    public function addSongAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $post_attach = $this->getRequestParam('post_attach', 0);
        $type = $this->getRequestParam('type');
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

//    // Check user
//    if ( !$this->_helper->requireUser()->isValid() ) {
//      $this->view->success = false;
//      $this->view->error = $this->view->translate('You must be logged in.');
//      return;
//    }
        // Check auth
        if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->checkRequire())
            $this->respondWithError('unauthorized');

        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();
        $playlistTable = Engine_Api::_()->getDbTable('playlists', 'music');

        if ($post_attach == 1 && $type == 'wall') {

            $playlist = $playlistTable->getSpecialPlaylist($viewer, $type);
            Engine_Api::_()->core()->setSubject($playlist);
        }

        // Get special playlist
        else if (0 >= ($playlist_id = $this->getRequestParam('playlist_id')) && false != ($type = $this->getRequestParam('type'))) {
            $playlist = $playlistTable->getSpecialPlaylist($viewer, $type);
            Engine_Api::_()->core()->setSubject($playlist);
        }




        // Check subject
        if (!$this->_helper->requireSubject('music_playlist')->checkRequire())
            $this->respondWithError('no_record');

        // Get playlist
        $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
        $playlist_id = $playlist->getIdentity();

        // check auth
        if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        // Check file
        $values = $this->getRequest()->getPost();

        // Process
        $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
        $db->beginTransaction();

        try {
            // Create song
            $file = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['Filedata']);

            if (!$file) {
                $this->respondWithValidationError('internal_server_error');
            }

            // Add song
            $song = $playlist->addSong($file);

            if (!$song) {
                $this->respondWithValidationError('internal_server_error');
            }
            $db->commit();
            if ($song && $post_attach == 1 && $type == 'wall') {
                $response = $song->toArray();
                $this->respondWithSuccess($response);
            }
            $this->successResponseNoContent('no_content', 'siteapi_music');
        } catch (Music_Model_Exception $e) {
            $db->rollback();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        } catch (Exception $e) {
            $db->rollback();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
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

        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $playlist->getType(),
                    "id" => $playlist->getIdentity()
                )
            );
        }

        if ($viewer->getIdentity() && ($viewer->getIdentity() != $owner->getIdentity())) {
            $menus[] = array(
                'label' => $this->translate('Report'),
                'name' => 'report',
                'url' => 'report/create/subject/' . $playlist->getGuid(),
                'urlParams' => array(
                    "type" => $playlist->getType(),
                    "id" => $playlist->getIdentity()
                )
            );
        }

        return $menus;
    }

}
