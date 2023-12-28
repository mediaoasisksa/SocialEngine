<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PhotoController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Album_PhotoController extends Siteapi_Controller_Action_Standard {
    public function init() {
        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            $this->_forward('throw-error', 'photo', 'album', array(
                "error_code" => "unauthorized"
            ));
            return;
        }

        if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
                null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
            Engine_Api::_()->core()->setSubject($photo);
        } else if (0 !== ($album_id = (int) $this->_getParam('album_id')) &&
                null !== ($album = Engine_Api::_()->getItem('album', $album_id))) {
            Engine_Api::_()->core()->setSubject($album);
        } else {
//            $this->_forward('throw-error', 'photo', 'album', array(
//                "error_code" => "parameter_missing",
//                "message" => "photo_id OR album_id"
//            ));
//            return;
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

    /*
     * RETURN THE LIST OF ALL PHOTOS OF ALBUM
     * 
     * @return array
     */
    public function listAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $bodyResponse = array();
        $bodyResponse['canUpload'] = 0;
        $photo_id = (int) $this->_getParam('photo_id');
        $user_id = (int) $this->_getParam('user_id');
        $itemType = $this->_getParam('item_type', null);

        // CHECK AUTHENTICATION
        if (Engine_Api::_()->core()->hasSubject('album')) {
            try {
                $album = $subject = Engine_Api::_()->core()->getSubject('album');
            } catch (Exception $ex) {
                if (0 !== ($album_id = (int) $this->_getParam('album_id')))
                    $album = $subject = Engine_Api::_()->getItem('album', $album_id);
            }

            if (!$subject->authorization()->isAllowed($viewer, 'view'))
                $this->respondWithError('unauthorized');

            $bodyResponse['canUpload'] = $subject->authorization()->isAllowed(null, 'photo');
        }else {
            $bodyResponse['canUpload'] = Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create');
        }


        /* RETURN THE LIST OF IMAGES, IF FOLLOWED THE FOLLOWING CASES:   
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - iF THERE ARE NO $_FILES AVAILABLE.
         */
        if (empty($_FILES) && $this->getRequest()->isGet()) {
            $requestLimit = $this->getRequestParam("limit", 20);
            $requestPage = $this->getRequestParam("page", 1);

            // Prepare data
            $photoTable = Engine_Api::_()->getItemTable('album_photo');
            $params = !empty($album) ? array('album' => $album) : array();

            if (isset($album) && !empty($album)) {
                $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
            } else {
                $canEdit = 0;
            }

            if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitealbum"))
                $params['order'] = 'creation_date DESC';
            else{
                unset($params['order']);
            }
            $params['album_id'] = $this->getRequestParam('album_id', null);
            $params['photo_id'] = $this->getRequestParam('photo_id', null);

            $paginator = $photoTable->getPhotoPaginator($params);

            try {
                // Get the photos for the requested item type
                if (!empty($itemType) && !empty($photo_id) && $itemType != 'album_photo') {
                    $photo = Engine_Api::_()->getItem($itemType, $photo_id);
                    if (!empty($photo)) {
                        $albumObj = $photo->getParent();
                        if (isset($albumObj->album_id) && !empty($albumObj->album_id))
                            $contentObj = $albumObj->getParent();
                        if (!empty($albumObj) && !empty($contentObj)) {
                            $paginator = $contentObj->getSingletonAlbum()->getCollectiblesPaginator();
                        }
                    }
                }
                if (!empty($itemType) && !empty($user_id) && $itemType == 'user') {
                    $params['user_id'] = $this->getRequestParam('user_id', null);
                    $paginator = Engine_Api::_()->getApi('Siteapi_Core', 'album')->getPhotoPaginator($params);
                }
            } catch (Exception $ex) {
                // Blank Exception
            }

            $paginator->setItemCountPerPage($requestLimit);
            $paginator->setCurrentPageNumber($requestPage);
            $bodyResponse['totalPhotoCount'] = $getTotalItemCount = $paginator->getTotalItemCount();

            // Check the Page Number for pass photo_id.
            if (!empty($photo_id)) {
                for ($page = 1; $page <= ceil($getTotalItemCount / $requestLimit); $page++) {
                    $paginator->setCurrentPageNumber($page);
                    $tmpGetPhotoIds = array();
                    foreach ($paginator as $photo) {
                        $tmpGetPhotoIds[] = $photo->photo_id;
                    }

                    if (in_array($photo_id, $tmpGetPhotoIds)) {
                        $bodyResponse['page'] = $page;
                        break;
                    }
                }
            }

            foreach ($paginator as $photo) {
                $isAllowedToView = $photo->authorization()->isAllowed($viewer, 'view');
                $tempAlbum = empty($isAllowedToView) ? 0 : 1;
                if (!empty($params['user_id']) || !empty($tempAlbum))
                    $tempAlbumPhoto = $photo->toArray();
                else
                    continue;

                $tempAlbumPhoto['owner_title'] = (($tempPhotoOwner = $photo->getOwner()) && !empty($tempPhotoOwner)) ? $tempPhotoOwner->getTitle() : '';
                $tempAlbumPhoto['album_title'] = (($tempAlbumObj = $photo->getAlbum()) && !empty($tempAlbumObj)) ? $tempAlbumObj->getTitle() : '';

//                $tempAlbumPhoto['album_title'] = $photo->getAlbum()->getTitle();
                // Getting viewer like or not to content.
                $tempAlbumPhoto["canLike"] = $tempAlbumPhoto["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);
                $tempAlbumPhoto["canComment"] = $photo->authorization()->isAllowed($viewer, 'comment');
                // Getting like count.
                $tempAlbumPhoto["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($photo);
                $tempAlbumPhoto['tags'] = Engine_Api::_()->getApi('Siteapi_Core', 'album')->getPhotoTag($photo);
                $tempAlbumPhoto['reactions'] = $this->getPhotoReaction($photo);
                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
                $tempAlbumPhoto = array_merge($tempAlbumPhoto, $getContentImages);

                if (!empty($itemType) && $itemType != 'album_photo' && !empty($albumObj) && !empty($contentObj)) {
                    $menu = array();
                    if ($viewer->getIdentity()) {
                        if ($photo->canEdit(Engine_Api::_()->user()->getViewer())) {
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

                            $menu[] = array(
                                'label' => $this->translate('Delete'),
                                'name' => 'delete',
                                'url' => 'events/photo/delete/' . $photo->getIdentity(),
                                'urlParams' => array(
                                )
                            );
                        }
                    }

                    $menu[] = array(
                        'label' => $this->translate('Remove Tag'),
                        'name' => 'delete_tag',
                        'url' => 'tags/remove',
                        'urlParams' => array(
                            "subject_type" => $photo->getType(),
                            "subject_id" => $photo->getIdentity()
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Report'),
                        'name' => 'report',
                        'url' => 'report/create/subject/' . $photo->getGuid()
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
                } else {
                    $tempAlbumPhoto['album_title'] = (($tempAlbumObj = $photo->getAlbum()) && !empty($tempAlbumObj)) ? $tempAlbumObj->getTitle() : '';
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

//                        $menu[] = array(
//                            'label' => $this->translate('Add Cover'),
//                            'name' => 'add_cover',
//                            'url' => 'albums/photo/add-cover',
//                            'urlParams' => array(
//                                "album_id" => $photo->getAlbum()->getIdentity(),
//                                "photo_id" => $photo->getIdentity()
//                            )
//                        );
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
                }

                $bodyResponse['photos'][] = $tempAlbumPhoto;
            }
            $bodyResponse['actual_count'] = count($bodyResponse['photos']);
            $this->respondWithSuccess($bodyResponse, true);
        } else if (Engine_Api::_()->core()->hasSubject() && !empty($_FILES) && $this->getRequest()->isPost()) {
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
                    $photo = Engine_Api::_()->getApi('core', 'siteapi')->setPhoto($file, $photo);
                } else
                    $photo = Engine_Api::_()->getApi('Siteapi_Core', 'album')->setPhoto($file, $photo);
                $photo->album_id = $album->album_id;
                $photo->save();
            }

            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * VIEW THE PHOTO
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $photo = Engine_Api::_()->core()->getSubject();
        $album = $photo->getAlbum();

        if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
            $photo->view_count = new Zend_Db_Expr('view_count + 1');
            $photo->save();
        }

        // if this is sending a message id, the user is being directed from a coversation
        // check if member is part of the conversation
        $message_id = $this->getRequestParam('message');
        $message_view = false;
        $response = array();
        if ($message_id) {
            $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
            if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
                $message_view = true;
        }
        $response['message_view'] = $message_view;
        $response['response'] = $photo->toArray();

        //if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) return;
        if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($photo, null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        if (!($album instanceof Core_Model_Item_Abstract) || !$album->getIdentity() || $album->album_id != $photo->album_id)
            $this->respondWithError('unauthorized');

        $response['canEdit'] = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
        $response['canDelete'] = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
        $response['canTag'] = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
        $response['canUntagGlobal'] = $canUntag = $album->isOwner($viewer);

        $response['nextPhoto'] = $photo->getNextPhoto()->toArray();
        $response['previousPhoto'] = $photo->getPreviousPhoto();

        // Get tags
        $tags = array();
        foreach ($photo->tags()->getTagMaps() as $tagmap) {
            $tags[] = array_merge($tagmap->toArray(), array(
                'id' => $tagmap->getIdentity(),
                'text' => $tagmap->getTitle(),
                'href' => $tagmap->getHref(),
                'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
            ));
        }
        $response['tags'] = $tags;

        if (!empty($viewer) && ($tempMenu = $this->getRequestParam('menu', true)) && !empty($tempMenu)) {
            if (!empty($canEdit)) {
                $menu = array();
                $menu[] = array(
                    'label' => $this->translate('Edit'),
                    'name' => 'edit',
                    'url' => 'albums/photo/edit',
                    'urlParams' => array(
                        "album_id" => $album->getIdentity(),
                        "photo_id" => $photo->getIdentity()
                    )
                );

                $menu[] = array(
                    'label' => $this->translate('Delete'),
                    'name' => 'delete',
                    'url' => 'albums/photo/delete',
                    'urlParams' => array(
                        "album_id" => $album->getIdentity(),
                        "photo_id" => $photo->getIdentity()
                    )
                );
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
                'url' => 'report/create/subject/' . $photo->getGuid()
            );

            $menu[] = array(
                'label' => $this->translate('Make Profile Photo'),
                'name' => 'make_profile_photo',
                'url' => 'members/edit/external-photo',
                'urlParams' => array(
                    "photo" => $photo->getGuid()
                )
            );

            $response['menu'] = $menu;
        }

        $this->respondWithSuccess($response);
    }

    /**
     * COVER PHOTO - ADD COVER PHOTO TO ALBUM
     * 
     */
    public function albumCoverAction() {

        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // CHECK AUTHENTICATION
        if (Engine_Api::_()->core()->hasSubject())
            $photo = Engine_Api::_()->core()->getSubject('album_photo');


        $album_id = $this->getRequestParam('album_id');
        $photo_id = $this->getRequestParam('photo_id');


        if (empty($album_id) || empty($photo_id))
            $this->respondWithValidationError('validation_fail', 'album_id/photo_id is required');

        $album = Engine_Api::_()->getItem('album', $album_id);

        if (empty($album))
            $this->respondWithError('no_record');

        if (empty($photo))
            $this->respondWithError('no_record');

        if ($photo->getParent() == $album) {

            $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
            if (empty($canEdit))
                $this->respondWithError('unauthorized');

            $album->photo_id = $photo_id;

            $album->save();
            $this->successResponseNoContent('no_content', true);
        }
        $this->respondWithValidationError('validation_fail', 'Photo should belong to album');
    }

    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireSubject('album_photo')->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'delete')->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $photo = Engine_Api::_()->core()->getSubject('album_photo');
        $album = $photo->getParent();

        try {
            // delete photo
            Engine_Api::_()->getDbtable('photos', 'album')->delete(array('photo_id = ?' => $photo->photo_id));

            // delete files from server
            $filesDB = Engine_Api::_()->getDbtable('files', 'storage');

            $filePath = $filesDB->fetchRow($filesDB->select()->where('file_id = ?', $photo->file_id))->storage_path;
            unlink($filePath);

            $thumbPath = $filesDB->fetchRow($filesDB->select()->where('parent_file_id = ?', $photo->file_id))->storage_path;
            unlink($thumbPath);

            // Delete image and thumbnail
            $filesDB->delete(array('file_id = ?' => $photo->file_id));
            $filesDB->delete(array('parent_file_id = ?' => $photo->file_id));

            // Check activity actions
            $attachDB = Engine_Api::_()->getDbtable('attachments', 'activity');
            $actions = $attachDB->fetchAll($attachDB->select()->where('type = ?', 'album_photo')->where('id = ?', $photo->photo_id));
            $actionsDB = Engine_Api::_()->getDbtable('actions', 'activity');

            foreach ($actions as $action) {
                $action_id = $action->action_id;
                $attachDB->delete(array('type = ?' => 'album_photo', 'id = ?' => $photo->photo_id));

                $action = $actionsDB->fetchRow($actionsDB->select()->where('action_id = ?', $action_id));
                $count = $action->params['count'];
                if (!is_null($count) && ($count > 1)) {
                    $action->params = array('count' => (integer) $count - 1);
                    $action->save();
                } else {
                    $actionsDB->delete(array('action_id = ?' => $action_id));
                }
            }

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * EDIT PHOTO - ADD TITLE AND DESCRIPTION
     * 
     * @return array
     */
    public function editAction() {
        if (!$this->_helper->requireSubject('album_photo')->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $photo = Engine_Api::_()->core()->getSubject('album_photo');

        if (!$viewer->getIdentity())
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $formValues = $photo->toArray();
            $this->respondWithSuccess(array(
                'form' => Engine_Api::_()->getApi('Siteapi_Core', 'album')->getPhotoEditForm(),
                'formValues' => $formValues
            ));
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            if (isset($_REQUEST['title']) && !empty($_REQUEST['title']))
                $values['title'] = $_REQUEST['title'];

            if (isset($_REQUEST['description']) && !empty($_REQUEST['description']))
                $values['description'] = $_REQUEST['description'];

            $db = $photo->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $photo->setFromArray($values);
                $photo->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    public function rotateAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (!$this->_helper->requireSubject('album_photo')->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->getRequest()->isPost())
            $this->respondWithError('invalid_method');

        $viewer = Engine_Api::_()->user()->getViewer();
        $photo = Engine_Api::_()->core()->getSubject('album_photo');

        $angle = (int) $this->getRequestParam('angle', 90);
        if (!$angle || !($angle % 360))
            $this->respondWithError('unauthorized');

        if (!in_array((int) $angle, array(90, 270)))
            $this->respondWithError('unauthorized');

        // Get file
        $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
        if (!($file instanceof Storage_Model_File))
            $this->respondWithError('unauthorized');

        // Pull photo to a temporary file
        $tmpFile = $file->temporary();

        // Operate on the file
        $image = Engine_Image::factory();
        $image->open($tmpFile)
                ->rotate($angle)
                ->write()
                ->destroy()
        ;

        // Set the photo
        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $photo->setPhoto($tmpFile);
            @unlink($tmpFile);

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);

            $db->commit();
            $this->respondWithSuccess($getContentImages);
        } catch (Exception $e) {
            @unlink($tmpFile);
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function flipAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (!$this->_helper->requireSubject('album_photo')->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->getRequest()->isPost())
            $this->respondWithError('invalid_method');

        $viewer = Engine_Api::_()->user()->getViewer();
        $photo = Engine_Api::_()->core()->getSubject('album_photo');

        $direction = $this->getRequestParam('direction');
        if (!in_array($direction, array('vertical', 'horizontal')))
            $this->respondWithError('unauthorized');

        // Get file
        $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
        if (!($file instanceof Storage_Model_File))
            $this->respondWithError('unauthorized');

        // Pull photo to a temporary file
        $tmpFile = $file->temporary();

        // Operate on the file
        $image = Engine_Image::factory();
        $image->open($tmpFile)
                ->flip($direction != 'vertical')
                ->write()
                ->destroy()
        ;

        // Set the photo
        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $photo->setPhoto($tmpFile);
            @unlink($tmpFile);

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);

            $db->commit();
            $this->respondWithSuccess($getContentImages);
        } catch (Exception $e) {
            @unlink($tmpFile);
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
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

}
