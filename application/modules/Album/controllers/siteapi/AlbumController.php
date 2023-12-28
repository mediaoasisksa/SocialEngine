<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AlbumController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Album_AlbumController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            $this->_forward('throw-error', 'album', 'album', array(
                "error_code" => "unauthorized"
            ));
            return;
        }

        if (0 !== ($photo_id = (int) $this->getRequestParam('photo_id')) &&
                null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
            Engine_Api::_()->core()->setSubject($photo);
        } else if (0 !== ($album_id = (int) $this->getRequestParam('album_id')) &&
                null !== ($album = Engine_Api::_()->getItem('album', $album_id))) {
            Engine_Api::_()->core()->setSubject($album);
        } else {
            $this->_forward('throw-error', 'album', 'album', array(
                "error_code" => "parameter_missing",
                "message" => "photo_id OR album_id"
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
     * Edit album
     *
     * @return array
     */
    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        // Prepare data
        $album = Engine_Api::_()->core()->getSubject();

        // FIND OUT THE AUTH COMMENT AND AOUTH VIEW VALUE.
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        // CHECK ALBUM FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE ALBUM EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            // IF THERE ARE NO FORM POST YET THEN RETURN THE ALBUM FORM.
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'album')->getForm($album);
            $formValues = $album->toArray();

            foreach ($roles as $role) {
                if ($auth->isAllowed($album, $role, 'view'))
                    $formValues['auth_view'] = $role;

                if ($auth->isAllowed($album, $role, 'comment'))
                    $formValues['auth_comment'] = $role;

                if ($auth->isAllowed($album, $role, 'tag'))
                    $formValues['auth_tag'] = $role;
            }

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));
            return;
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* UPDATE THE ALBUM INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO THE ARRAY.
            $data = $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'album')->getForm($album);
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values;

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'album')->getFormValidators($album);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            
            if(isset($values['auth_view']))
            {
                $values['view_privacy'] = $values['auth_view'];
            }

            // Process
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $album->setFromArray($values);
                $album->save();

                // CREATE AUTH STUFF HERE
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view']))
                    $values['auth_view'] = 'everyone';

                if (empty($values['auth_comment']))
                    $values['auth_comment'] = 'owner_member';

                if (empty($values['auth_tag']))
                    $values['auth_tag'] = 'owner_member';

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);
                $tagMax = array_search($values['auth_tag'], $roles);

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

            $db->beginTransaction();
            try {
                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($album) as $action) {
                    $actionTable->resetActivityBindings($action);
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * Album profile page.
     *
     * @return array
     */
    public function viewAction() {

        // Validate request methods
        $this->validateRequestMethod();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id=null;
        if(!empty($viewer)){
            $viewer_id = $viewer->getIdentity();
        }
        $album = Engine_Api::_()->core()->getSubject();
        $siteapiAlbumView = Zend_Registry::isRegistered('siteapiAlbumView') ? Zend_Registry::get('siteapiAlbumView') : null;

        if (!Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')) {

            $module_error_type = @ucfirst($album->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }

        // Prepare params
        $page = $this->getRequestParam('page', 1);
        $limit = $this->getRequestParam('limit', 20);

        // Prepare data
        $response = $bodyParams = array();
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $paginator = $photoTable->getPhotoPaginator(array(
            'album' => $album,
        ));
        $paginator->setItemCountPerPage($settings->getSetting('album_page', $limit));
        $paginator->setCurrentPageNumber($page);
        $response['totalPhotoCount'] = $paginator->getTotalItemCount();

        $response['album'] = $album->toArray();

        
          // FIND OUT THE AUTH COMMENT AND AOUTH VIEW VALUE.
        


        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );
        
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $role) {
                if ($auth->isAllowed($album, $role, 'view'))
                     $response['album']['auth_view'] = $role;

                if ($auth->isAllowed($album, $role, 'comment'))
                     $response['album']['auth_comment'] = $role;

                if ($auth->isAllowed($album, $role, 'tag'))
                     $response['album']['auth_tag'] = $role;
        }

        if($response['album']['auth_view'])
           $response['album']['view_privacy'] = $response['album']['auth_view'];

        if ($viewer->getIdentity())
            $response['canUpload'] = $album->authorization()->isAllowed(null, 'photo');

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album);
        $response['album'] = array_merge($response['album'], $getContentImages);

        // Add owner images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album, true);
        $response['album'] = array_merge($response['album'], $getContentImages);

        $response['album']["owner_title"] = $album->getOwner()->getTitle();
        $canTag = $album->authorization()->isAllowed($viewer, 'tag');
        $response['album']["canTag"] = !empty($canTag) ? $canTag : 0;
        // Getting viewer like or not to content.
        $response['album']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($album);

        // Getting like count.
        $response['album']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($album);

        $response['response'] = '';

        $canEdit = 0;
        if ($viewer->getIdentity()) {
            if (isset($viewer->level_id) && $viewer->level_id == 1)
                $canEdit = 1;

            if ($viewer->getIdentity() == $album->getOwner()->getIdentity())
                $canEdit = 1;
        }

        $response["canEdit"] = $canEdit;

        foreach ($paginator as $photo) {
            $tempAlbumPhoto = $photo->toArray();

            // Getting viewer like or not to content.
            $tempAlbumPhoto["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);

            // Getting like count.
            $tempAlbumPhoto["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($photo);
            $tempAlbumPhoto['tags'] = Engine_Api::_()->getApi('Siteapi_Core', 'album')->getPhotoTag($photo);
            // Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
            $tempAlbumPhoto = array_merge($tempAlbumPhoto, $getContentImages);

            if ($viewer->getIdentity()) {
                $menu = array();
                if (!empty($canEdit)) {
                    $menu[] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'albums/photo/edit',
                        'urlParams' => array(
                            "album_id" => $photo->getAlbum()->getIdentity(),
                            "photo_id" => $photo->getIdentity()
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'albums/photo/delete',
                        'urlParams' => array(
                            "album_id" => $photo->getAlbum()->getIdentity(),
                            "photo_id" => $photo->getIdentity()
                        )
                    );
                }

                $menu[] = array(
                    'label' => $this->translate('Share'),
                    'name' => 'share',
                    'url' => 'activity/share',
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

                $tempAlbumPhoto['menu'] = $menu;
            }

            $bodyParams[] = $tempAlbumPhoto;
        }

        if ($this->getRequestParam('gutter_menu', 1) && !empty($canEdit)) {
            $response['gutterMenu'][] = array(
                'label' => $this->translate('Add More Photos'),
                'name' => 'add',
                'url' => 'albums/upload',
                'urlParams' => array(
                    "album_id" => $album->album_id
                )
            );

            $response['gutterMenu'][] = array(
                'label' => $this->translate('Edit Settings'),
                'name' => 'edit',
                'url' => 'albums/edit/' . $album->album_id,
                'urlParams' => array(
                )
            );

            $response['gutterMenu'][] = array(
                'label' => $this->translate('Delete Album'),
                'name' => 'delete',
                'url' => 'albums/delete/' . $album->album_id,
                'urlParams' => array(
                )
            );

            
        }
        
       if(!empty($viewer_id)){
            
            $response['gutterMenu'][] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $album->getType(),
                    "id" => $album->getIdentity()
                )
            );

        }

        if (!empty($bodyParams))
            $response['albumPhotos'] = $bodyParams;


        // Increase the View Count.
        if (!$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
            $album->getTable()->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
                    ), array(
                'album_id = ?' => $album->getIdentity(),
            ));
        }

         $response['viewPrivacy'] =$this->viewPrivacy($album->view_privacy);
         
        if (!empty($siteapiAlbumView))
            $this->respondWithSuccess($response);
    }
    /**
     * Delete album
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

        $album = Engine_Api::_()->core()->getSubject();

        if (!$album)
            $this->respondWithError('no_record');

        $db = $album->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $album->delete();
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

//  /**
//   * Edit Photos
//   *
//   * @return array
//   */
//  public function editphotosAction() {
//    $viewer = Engine_Api::_()->user()->getViewer();
//    $viewer_id = $viewer->getIdentity();
//    if ( empty($viewer_id) ) {
//      $this->respondWithError('unauthorized');
//      return;
//    }
//
//
//    $album = Engine_Api::_()->core()->getSubject();
//
//    if ( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
//      $this->respondWithError('unauthorized');
//      return;
//    }
//
//    // Prepare data
//    $album = Engine_Api::_()->core()->getSubject();
//    $photoTable = Engine_Api::_()->getItemTable('album_photo');
//    $paginator = $photoTable->getPhotoPaginator(array(
//        'album' => $album,
//    ));
//    
//    $requestLimit = $this->getRequestParam("limit", 10);
//    $requestPage = $this->getRequestParam("page", 1);
//    $paginator->setCurrentPageNumber($requestPage);
//    $paginator->setItemCountPerPage($requestLimit);
//
//    // Get albums
//    $albumTable = Engine_Api::_()->getItemTable('album');
//    $myAlbums = $albumTable->select()
//            ->from($albumTable, array('album_id', 'title'))
//            ->where('owner_type = ?', 'user')
//            ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
//            ->query()
//            ->fetchAll();
//
//    $albumOptions = array(0 => '');
//    foreach ( $myAlbums as $myAlbum ) {
//      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
//    }
//    if ( count($albumOptions) == 1 ) {
//      $albumOptions = array();
//    }
//
//
//    /* RETURN THE MANAGE ALBUM FORM IN THE FOLLOWING CASES:      
//     * - IF THERE ARE GET METHOD AVAILABLE.
//     * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
//     */
//    if ( $this->getRequest()->isGet() ) {
//      $getBody = array();
//      $getBody['cover_photo_id'] = isset($album->photo_id) ? $album->photo_id : '';
//      foreach ( $paginator as $photo ) {
//        $tempImages = $photo->toArray();
//        
//        // Add owner images
//        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
//        $tempImages = array_merge($tempImages, $getContentImages);
//        
//        $getBody['images'][] = $tempImages;
//      }
//
//      $getBody['form'][] = array(
//          'type' => 'Text',
//          'name' => 'title'
//      );
//
//      $getBody['form'][] = array(
//          'type' => 'Textarea',
//          'name' => 'description'
//      );
//
//      if ( !empty($albumOptions) ) {
//        $getBody['form'][] = array(
//            'type' => 'Select',
//            'name' => 'move',
//            'multiOptions' => $albumOptions
//        );
//      }
//
//      $getBody['form'][] = array(
//          'type' => 'Checkbox',
//          'name' => 'delete'
//      );
//
//      $getBody['form'][] = array(
//          'type' => 'Radio',
//          'name' => 'cover'
//      );
//
//      $this->respondWithSuccess($getBody);      
//    } else if ( $this->getRequest()->isPost() ) {
//      /* EDIT THE MANAGE ALBUM IN THE FOLLOWING CASES:  
//       * - IF THERE ARE POST METHOD AVAILABLE.
//       * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
//       */
//      
//      // CONVERT POST DATA INTO THE ARRAY.
//        $values = array();
//        $getForm = array('title', 'description', 'move', 'delete', 'cover');
//        foreach ( $getForm as $element ) {
//          if ( isset($_REQUEST[$element]) )
//            $values[$element] = $_REQUEST[$element];
//        }
//
//      $table = $album->getTable();
//      $db = $table->getAdapter();
//      $db->beginTransaction();
//
//      try {
//        if ( !empty($values) ) {
//          foreach ( $values as $photo_id => $value ) {
//            if ( empty($photo_id) )
//              continue;
//
//            $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
//
//            if ( empty($photo) )
//              continue;
//
//            if ( isset($value['cover']) && !empty($value['cover']) ) {
//              $album->photo_id = $photo_id;
//              $album->save();
//            }
//
//            if ( isset($value['title']) && !empty($value['title']) ) {
//              $photo->title = $value['title'];
//              $photo->save();
//            }
//
//            if ( isset($value['description']) && !empty($value['description']) ) {
//              $photo->description = $value['description'];
//              $photo->save();
//            }
//
//            if ( isset($value['delete']) && !empty($value['delete']) ) {
//              $photo->delete();
//              continue;
//            } else if ( isset($value['move']) && !empty($value['move']) ) {
//              $nextPhoto = $photo->getNextPhoto();
//
//              $old_album_id = $photo->album_id;
//              $photo->album_id = $photo_id;
//              $photo->save();
//
//              // Change album cover if necessary
//              if ( ($nextPhoto instanceof Album_Model_Photo) &&
//                      ( int ) $album->photo_id == ( int ) $photo->getIdentity() ) {
//                $album->photo_id = $nextPhoto->getIdentity();
//                $album->save();
//              }
//
//              // Remove activity attachments for this photo
//              Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($photo);
//            }
//          }
//        }
//
//        $db->commit();
//
//        $this->successResponseNoContent('no_content');
//      } catch ( Exception $e ) {
//        $db->rollBack();
//        $this->respondWithValidationError('internal_server_error', $e->getMessage());
//      }
//    }
//  }
    
    /*
     * change privacy of album.
     * @param auth_view
     * @return 204
     */
    
public function changePrivacyAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');
        
        $privacy = $this->_getParam('auth_view',null);
        if(empty($privacy))
            $this->respondWithError('unauthorized',"privacy is required.");
        // Prepare data
        $album = Engine_Api::_()->core()->getSubject();
        
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $album->view_privacy = $privacy;
        $album->save();
        $viewMax = array_search($privacy, $roles);
        foreach ($roles as $i => $role)
        {
            $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
            $auth->setAllowed($album, $role, 'comment', ($i <= $viewMax));
        }
        
        $this->successResponseNoContent('no_content');
        
    }

    
    public function viewPrivacy($privacy='everyone')
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if(!$viewer || !$viewer->getIdentity())
            return array();
        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );

// Element: auth_view
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this album?'),
                'multiOptions' => $viewOptions,
                'value' =>$privacy,
            );
        }
        
        return $accountForm;

    }
    
}
