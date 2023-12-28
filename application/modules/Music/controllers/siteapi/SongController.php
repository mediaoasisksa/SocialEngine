<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    songController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Music_SongController extends Siteapi_Controller_Action_Standard {

    public function init() {
        // Check auth
        if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
            $this->_forward('throw-error', 'song', 'music', array(
                "error_code" => "unauthorized"
            ));
            return;
        }

        // Get subject
        if (null !== ($song_id = $this->getRequestParam('song_id')) &&
                null !== ($song = Engine_Api::_()->getItem('music_playlist_song', $song_id)) &&
                $song instanceof Music_Model_PlaylistSong) {
            Engine_Api::_()->core()->setSubject($song);
        }

        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

        $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();
  
        $this->getHost = '';
        if($getDefaultStorageType == 'local')
            $this->getHost = !empty($staticBaseUrl)? $staticBaseUrl: $serverHost;
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
     * Rename songs
     *
     * @return array
     */
    public function renameAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (Engine_Api::_()->core()->hasSubject())
            $song = Engine_Api::_()->core()->getSubject('music_playlist_song');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($song))
            $this->respondWithError('no_record');

        // Get song/playlist
        $playlist = $song->getParent();
        if (empty($playlist))
            $this->respondWithError('no_record');

        // Check auth
        if (!Engine_Api::_()->authorization()->isAllowed($playlist, null, 'edit'))
            $this->respondWithError('unauthorized');

        // Process
        $db = $song->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $requestTitle = $this->getRequestParam('title');
            $song->setTitle($requestTitle);
            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Delete songs
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
            $song = Engine_Api::_()->core()->getSubject('music_playlist_song');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($song))
            $this->respondWithError('no_record');

        // Get song/playlist
        $playlist = $song->getParent();
        if (empty($playlist))
            $this->respondWithError('no_record');

        // Check auth
        if (!Engine_Api::_()->authorization()->isAllowed($playlist, null, 'edit'))
            $this->respondWithError('unauthorized');

        // Get file
        $file = Engine_Api::_()->getItem('storage_file', $song->file_id);
        if (!$file)
            $this->respondWithError('no_record');

        $db = $song->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $song->deleteUnused();
            $db->commit();

            $this->successResponseNoContent('no_content', 'siteapi_music');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * To increment thw play count
     *
     * @return array
     */
    public function tallyAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (Engine_Api::_()->core()->hasSubject())
            $song = Engine_Api::_()->core()->getSubject('music_playlist_song');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($song))
            $this->respondWithError('no_record');

        // Get song/playlist
        $playlist = $song->getParent();
        if (empty($playlist))
            $this->respondWithError('no_record');

        // Process
        $db = $song->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $song->play_count++;
            $song->save();

            $playlist->play_count++;
            $playlist->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        $this->respondWithSuccess($song->toArray());
    }

    /**
     * Move song to my playlist
     *
     * @return array
     */
    public function appendAction() {
        if (Engine_Api::_()->core()->hasSubject())
            $song = Engine_Api::_()->core()->getSubject('music_playlist_song');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($song))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid())
            $this->respondWithError('unauthorized');

        $response = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $playlistTable = Engine_Api::_()->getDbtable('playlists', 'music');

        // CHECK FORUM FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            // Populate form
            $tempPlaylist = array("0" => "Create New Playlist");
            $songTable = $song->getTable();
            $playlists = $playlistTable->select()
                    ->from($playlistTable, array('playlist_id', 'title'))
                    ->where('owner_type = ?', 'user')
                    ->where('owner_id = ?', $viewer->getIdentity())
                    ->query()
                    ->fetchAll();
            foreach ($playlists as $playlist) {
                if (!empty($playlist['title']) && $playlist['playlist_id'] != $song->playlist_id) {
                    $tempPlaylist[$playlist['playlist_id']] = $playlist['title'];
                }
            }

            $response[] = array(
                'type' => 'Select',
                'name' => 'playlist_id',
                'label' => $this->translate('Choose Playlist'),
                'multiOptions' => $tempPlaylist
            );

            $response[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => $this->translate('Playlist Name')
            );

            $response[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Add Song')
            );

            $this->sendResponse(array(
                "status_code" => 200,
                "body" => $response
            ));
        } else if ($this->getRequest()->isPost()) {
            // CONVERT POST DATA INTO THE ARRAY.
            if (isset($_REQUEST['playlist_id']))
                $values['playlist_id'] = $_REQUEST['playlist_id'];

            if (isset($_REQUEST['playlist_id']))
                $values['title'] = $_REQUEST['title'];

            if (!isset($values['playlist_id'])) {
                $this->respondWithValidationError("parameter_missing", "playlist_id");
            }

            if (!isset($values['title']) || empty($values['title'])) {
                $this->respondWithValidationError("parameter_missing", "title");
            }

            // Process
            $db = $song->getTable()->getAdapter();
            $db->beginTransaction();
            try {
                // Existing playlist
                if (!empty($values['playlist_id'])) {
                    $playlist = Engine_Api::_()->getItem('music_playlist', $values['playlist_id']);

                    // already exists in playlist
                    $alreadyExists = $songTable->select()
                            ->from($songTable, 'song_id')
                            ->where('playlist_id = ?', $playlist->getIdentity())
                            ->where('file_id = ?', $song->file_id)
                            ->limit(1)
                            ->query()
                            ->fetchColumn()
                    ;

                    if ($alreadyExists)
                        $this->respondWithError('exist_in_playlist');
                }

                // New playlist
                else {
                    $playlist = $playlistTable->createRow();
                    $playlist->title = trim($values['title']);
                    $playlist->owner_type = 'user';
                    $playlist->owner_id = $viewer->getIdentity();
                    $playlist->search = 1;
                    $playlist->save();

                    // Add action and attachments
                    $auth = Engine_Api::_()->authorization()->context;
                    $auth->setAllowed($playlist, 'registered', 'comment', true);
                    foreach (array('everyone', 'registered', 'member') as $role) {
                        $auth->setAllowed($playlist, $role, 'view', true);
                    }

                    // Only create activity feed item if "search" is checked
                    if ($playlist->search) {
                        $activity = Engine_Api::_()->getDbtable('actions', 'activity');
                        $action = $activity->addActivity(Engine_Api::_()->user()->getViewer(), $playlist, 'music_playlist_new');
                        if ($action) {
                            $activity->attachActivity($action, $playlist);
                        }
                    }
                }

                // Add song
                $playlist->addSong($song->file_id);
                $db->commit();

                $response = $playlist->toArray();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist);
                $response = array_merge($response, $getContentImages);

                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist, true);
                $response = array_merge($response, $getContentImages);

                $response["owner_title"] = $playlist->getOwner()->getTitle();

                $getPlaylistSongs = $playlist->getSongs();
                foreach ($getPlaylistSongs as $songObj) {
                    $songArray = $songObj->toArray();
                    $songArray['filePath'] = $this->getHost . $songObj->getFilePath();
                    $response['playlist_songs'][] = $songArray;
                }

                $this->respondWithSuccess($response);
            } catch (Music_Model_Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

//  public function uploadAction()
//  {
//    // only members can upload music
//    if( !$this->_helper->requireUser()->checkRequire() ) {
//      $this->view->status = false;
//      $this->view->error = $this->view->translate('Max file size limit exceeded or session expired.');
//      return;
//    }
//
//    // Check method
//    if( !$this->getRequest()->isPost() ) {
//      $this->view->status = false;
//      $this->view->error = $this->view->translate('Invalid request method');
//      return;
//    }
//
//    // Check file
//    $values = $this->getRequest()->getPost();
//    if( empty($values['Filename']) || empty($_FILES['Filedata']) ) {
//      $this->view->status = false;
//      $this->view->error = $this->view->translate('No file');
//      return;
//    }
//
//
//    // Process
//    $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
//    $db->beginTransaction();
//    
//    try {
//      $song = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['Filedata']);
//      $this->view->status   = true;
//      $this->view->song     = $song;
//      $this->view->song_id  = $song->getIdentity();
//      $this->view->song_url = $song->getHref();
//      $db->commit();
//
//    } catch( Music_Model_Exception $e ) {
//      $db->rollback();
//
//      $this->view->status = false;
//      $this->view->message = $this->view->translate($e->getMessage());
//
//    } catch( Exception $e ) {
//      $db->rollback();
//
//      $this->view->status  = false;
//      $this->view->message = $this->view->translate('Upload failed by database query');
//      
//      throw $e;
//    }
//  }
}
