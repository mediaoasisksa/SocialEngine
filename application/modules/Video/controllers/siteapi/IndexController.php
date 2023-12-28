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
class Video_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
        // only show videos if authorized
        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid())
            $this->respondWithError("unauthorized");

        $siteapiVideoAPIEnabled = Zend_Registry::isRegistered('siteapiVideoAPIEnabled') ? Zend_Registry::get('siteapiVideoAPIEnabled') : null;
        if (empty($siteapiVideoAPIEnabled))
            $this->respondWithError("unauthorized");

        $id = $this->getRequestParam('video_id', $this->getRequestParam('id', null));
        if ($id) {
            $video = Engine_Api::_()->getItem('video', $id);
            if ($video)
                Engine_Api::_()->core()->setSubject($video);
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
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'video')->getBrowseSearchForm(), true);
    }

    /**
     * Get browse video page.
     * 
     * @return array
     */
    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $response = array();
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();

        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();

        $values = array();
        $values = $this->getRequestAllParams;
        $values['status'] = 1;
        $values['search'] = 1;

        if ($this->getRequestParam('search'))
            $values['text'] = $this->getRequestParam('search');

        if ($this->getRequestParam('orderby'))
            $values['orderby'] = $this->getRequestParam('orderby');

        if ($this->getRequestParam('category'))
            $values['category'] = $this->getRequestParam('category');

        // check to see if request is for specific user's listings
        $user = $this->getRequestParam('user', null);
        if ($user)
            $values['user_id'] = $user;

        $user_id = $this->getRequestParam('user_id', null);
        if ($user_id)
            $values['user_id'] = $user_id;

        // Get videos
        $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 12);
        $paginator->setItemCountPerPage($items_count);
        $requestPage = $this->getRequestParam('page', 1);
        $paginator->setCurrentPageNumber($requestPage);

        foreach ($paginator as $video) {
            $browseVideo = $video->toArray();
            $browseVideo['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'video')->videoType($video->type);

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
            $browseVideo = array_merge($browseVideo, $getContentImages);

            // Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
            $browseVideo = array_merge($browseVideo, $getContentImages);

            $browseVideo["owner_title"] = $video->getOwner()->getTitle();
            $isAllowedView = $video->authorization()->isAllowed($viewer, 'view');
            $browseVideo["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
            $browseVideo["like_count"] = $video->likes()->getLikeCount();
            $browseVideo["rating_count"] = Engine_Api::_()->video()->ratingCount($video->getIdentity());
            $browseVideo['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getVideoURL($video);

            $response['response'][] = $browseVideo;
        }

        $response['totalItemCount'] = $paginator->getTotalItemCount();

        $this->respondWithSuccess($response, true);
    }

    /**
     * Get the manage video page.
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

        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $values = array();
        $values = $this->getRequestAllParams;
        $values['status'] = 1;
//        $values['search'] = 1;
        $values['user_id'] = $this->getRequestParam('user_id', $viewer_id);
        if ($this->getRequestParam('search'))
            $values['text'] = $this->getRequestParam('search');

        if ($this->getRequestParam('orderby'))
            $values['orderby'] = $this->getRequestParam('orderby');

        if ($this->getRequestParam('category'))
            $values['category'] = $this->getRequestParam('category');

        $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 12);
        $paginator->setItemCountPerPage($items_count);
        $requestPage = $this->getRequestParam('page', 1);
        $paginator->setCurrentPageNumber($requestPage);
        foreach ($paginator as $video) {
            $browseVideo = $video->toArray();
            $browseVideo['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'video')->videoType($video->type);

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
            $browseVideo = array_merge($browseVideo, $getContentImages);

            // Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
            $browseVideo = array_merge($browseVideo, $getContentImages);

            $browseVideo["owner_title"] = $video->getOwner()->getTitle();
            $isAllowedView = $video->authorization()->isAllowed($viewer, 'view');
            $browseVideo["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
            $browseVideo["like_count"] = $video->likes()->getLikeCount();
            $browseVideo["rating_count"] = Engine_Api::_()->video()->ratingCount($video->getIdentity());
            $browseVideo['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getVideoURL($video);

            if ($video->isOwner($viewer)) {
                $menus = array();
                if ($video->authorization()->isAllowed($viewer, 'edit')) {
                    $menus[] = array(
                        'label' => $this->translate('Edit Video'),
                        'name' => 'edit',
                        'url' => 'videos/edit/' . $video->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }

                if ($video->authorization()->isAllowed($viewer, 'delete')) {
                    $menus[] = array(
                        'label' => $this->translate('Delete Video'),
                        'name' => 'delete',
                        'url' => 'videos/delete/' . $video->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }

                $browseVideo['menu'] = $menus;
            }

            $response['response'][] = $browseVideo;
        }

        $response['totalItemCount'] = $paginator->getTotalItemCount();
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();
        $response['quota'] = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');

        $this->respondWithSuccess($response);
    }

    /**
     * Get video profile page
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($video))
            $this->respondWithError('no_record');

        $bodyParams = array();
        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($video);

        $bodyParams['response'] = $video->toArray();
        $bodyParams['response']['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'video')->videoType($video->type);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
        if (!empty($getContentImages))
            $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

        //contentURL
        $contentURL = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($video);
        if (!empty($contentURL))
            $bodyParams['response'] = array_merge($bodyParams['response'], $contentURL);

        // Add owner images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

        $bodyParams['response']["owner_title"] = $video->getOwner()->getTitle();
        $bodyParams['response']["rating_count"] = Engine_Api::_()->video()->ratingCount($video->getIdentity());

        //Member verification Work...............
        $bodyParams['response']['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($video->getOwner());

        // Getting viewer like or not to content.
        $bodyParams['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($video);

        // Getting like count.
        $bodyParams['response']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($video);

        // if this is sending a message id, the user is being directed from a coversation
        // check if member is part of the conversation
        $message_id = $this->getRequestParam('message');
        $message_view = false;
        if ($message_id) {
            $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
            if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
                $message_view = true;
            }
        }

        $bodyParams['response']['message_view'] = $message_view;

        $videoTags = $video->tags()->getTagMaps();
        if (!empty($videoTags)) {
            $tagArray = array();
            foreach ($videoTags as $tag) {
                $tagArray[$tag->getTag()->tag_id] = $tag->getTag()->text;
            }

            if(!empty($tagArray))
                $bodyParams['response']['tags'] = $tagArray;
        }

        // Check if edit/delete is allowed
        $bodyParams['response']['can_edit'] = $can_edit = $this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->checkRequire();
        $bodyParams['response']['can_delete'] = $can_delete = $this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->checkRequire();

        // check if embedding is allowed
        $can_embed = true;
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
            $can_embed = false;
        } else if (isset($video->allow_embed) && !$video->allow_embed) {
            $can_embed = false;
        }
        $bodyParams['response']['can_embed'] = $can_embed;

        // increment count
        $embedded = "";
        if ($video->status == 1) {
            if (!$video->isOwner($viewer)) {
                $video->view_count++;
                $video->save();
            }
        }

        $bodyParams['response']['rating_count'] = Engine_Api::_()->video()->ratingCount($video->getIdentity());
        $bodyParams['response']['rated'] = Engine_Api::_()->video()->checkRated($video->getIdentity(), $viewer->getIdentity());
        $bodyParams['response']['videoEmbedded'] = $embedded;
        if ($video->category_id) {
            $category = Engine_Api::_()->video()->getCategory($video->category_id);

            if (!empty($category) && isset($category->category_name))
                $bodyParams['response']['category'] = $category->category_name;
        }

        $bodyParams['response']['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getVideoURL($video);
        //get all reaction on live video.
        $livstreamvideo = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('livestreamingvideo');
        if($livstreamvideo)
        {
            $streamTable = Engine_Api::_()->getDbtable ( "streams", "livestreamingvideo" );
         $isLivesteamingVideo = $streamTable->isLivestreamingVideo($video);
         if($isLivesteamingVideo)
            $bodyParams['response']['reactions'] = Engine_Api::_()->getApi('Siteapi_Core', 'livestreamingvideo')->getAllReactions($video);
        }

        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Get video create form
     * 
     * @return array
     */
    public function createAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'create')->isValid())
            $this->respondWithError('unauthorized');

        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
        $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator(array("user_id" => $viewer->getIdentity()));
        $current_count = $paginator->getTotalItemCount();

        if (($current_count >= $quota) && !empty($quota))
            $this->respondWithError('unauthorized', 'You have already uploaded the maximum number of entries allowed. If you would like to upload a new entry, please delete an old one first.');

        /* RETURN THE VIDEO CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $post_attach = $this->getRequestParam('post_attach', 0);
            $message = $this->getRequestParam('message', 0);
            $type = $this->getRequestParam('type', 0);
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'video')->getForm($post_attach, null, $type, $message));
        } else if ($this->getRequest()->isPost()) {

            /* CREATE VIDEO IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('video', $viewer->level_id, 'flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("videos",'video');
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
            $post_attach = $this->getRequestParam('post_attach', 0);
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));

            // START FORM VALIDATION
            $data = $values;
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'video')->getFormValidators(null, null, $post_attach);
            $data['validators'] = $validators;
            $insert_action = false;

            //for video posting through advanced activity
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $validationMessage = array();
            if (($values['type'] == '1' || $values['type'] == '2' || $values['type'] == 'iframely') && (!isset($values['url']) || empty($values['url']))) {
                $validationMessage['url'] = $this->translate("Please complete this field it is required");
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            if (($values['type'] == '3') && (!isset($_FILES['filedata']) || empty($_FILES['filedata']['name']))) {
                $validationMessage['filedata'] = $this->translate("Please complete this field it is required");
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            $db = Engine_Db_Table::getDefaultAdapter();
            $select = new Zend_Db_Select($db);
            $coreVersion = $select
                    ->from('engine4_core_modules', 'version')
                    ->where('name = ?', 'core')
                    ->query()
                    ->fetchColumn();

            $version = Engine_Api::_()->getApi('Core', 'siteapi')->checkVersion($coreVersion, 4.9);
            $values['type'] == 'iframely';
            // IN CASE OF YOUTUBE AND VIMEO UPLOAD THE VIDEO AND RETURN THE VIDEO OBJECT.
            if (isset($values['url']) && !empty($values['url']) && $values['type'] != 'iframely')
                $video = $this->_composeUploadAction($values);

            if ($values['type'] == 'iframely') {
                $information = $this->handleIframelyInformation($values['url']);

                if (empty($information)) {
                    $form->addError('We could not find a video there - please check the URL and try again.');
                }
                $values['code'] = $information['code'];
                $values['status'] = 1;
                $thumbnail = $information['thumbnail'];
                $table = Engine_Api::_()->getDbtable('videos', 'video');
                $video = $table->createRow();
                $video->setFromArray($values);
                $video->save();

                $insert_action = true;
            }

            // IN CASE OF DEVICE UPLOADED VIDEOS.
            if (isset($_FILES['filedata']) && !empty($_FILES['filedata']['name']))
                $video = $this->_uploadVideoAction($values);

            $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
            $db->beginTransaction();
            try {

                if (isset($values['type']) && $values['type'] != 3 && $values['type'] != 'iframely') {
                    try {
                        // Now try to create thumbnail
                        $thumbnail = Engine_Api::_()->getApi('Core', 'siteapi')->handleSiteVideoThumbnail($video->type, $video->code);
                        $video = Engine_Api::_()->getApi('Core', 'siteapi')->saveVideoThumbnail($thumbnail, $video);

                        if ($version == 0) {
                            $video->type = $values['type'];
                        } else {
                            $video->type = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getVideoType($video->type);
                        }
                        $video->save();
                    } catch (Exception $ex) {
                        //Blank Exception
                    }
                    $insert_action = true;
                } else {
                    // Now try to create thumbnail
                    if (!isset($thumbnail) || empty($thumbnail))
                        $thumbnail = $this->_handleThumbnail($video->type, $video->code);
                    $ext = ltrim(strrchr($thumbnail, '.'), '.');
                    $thumbnail_parsed = @parse_url($thumbnail);
                    if (@GetImageSize($thumbnail)) {
                        $valid_thumb = true;
                    } else {
                        $valid_thumb = false;
                    }

                    if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

                        $src_fh = fopen($thumbnail, 'r');
                        $tmp_fh = fopen($tmp_file, 'w');
                        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

                        $image = Engine_Image::factory();
                        $image->open($tmp_file)
                                ->resize(360, 480)
                                ->write($thumb_file)
                                ->destroy();
                        try {
                            $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                                'parent_type' => $video->getType(),
                                'parent_id' => $video->getIdentity()
                            ));

                            // Remove temp file
                            @unlink($thumb_file);
                            @unlink($tmp_file);
                        } catch (Exception $e) {
                            
                        }
                        $video->photo_id = $thumbFileRow->file_id;
                    }

                    if ($video->type != 'iframely')
                        $information = $this->_handleInformation($video->type, $video->code);
                    $video->save();
                }
                $video->duration = $information['duration'];
                if (!$video->description) {
                    $video->description = $information['description'];
                    if ($version == 0) {
                        $video->type = 3;
                    } else {
                        $video->type = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getVideoType($video->type);
                    }
                    $video->save();
                    // Insert new action item
                }

                // CREATE AUTH STUFF HERE
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if (isset($values['auth_view']))
                    $auth_view = $values['auth_view'];
                else
                    $auth_view = "everyone";
                $viewMax = array_search($auth_view, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
                }

                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if (isset($values['auth_comment']))
                    $auth_comment = $values['auth_comment'];
                else
                    $auth_comment = "everyone";
                $commentMax = array_search($auth_comment, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
                }

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }

                if (isset($video->view_privacy))
                    $video->view_privacy = $values['auth_view'];
                $video->save();

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $video->tags()->addTagMaps($viewer, $tags);
                if(isset($_FILES['photo']))
                    $insert_action=true;
                
                unset($_FILES['photo']);
                unset($_FILES['filedata']);
                $db->commit();
            } catch (Exception $e) {

                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $db->beginTransaction();
            try {
                if ($insert_action && empty($post_attach) && $post_attach != 1) {
                    $owner = $video->getOwner();
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'video_new');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
                    }

                    // Rebuild privacy
                    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    foreach ($actionTable->getActionsByObject($video) as $action) {
                        $actionTable->resetActivityBindings($action);
                    }
                }

                $db->commit();

                // Change request method POST to GET
                if (!empty($post_attach) && ((_IOS_VERSION && _IOS_VERSION >= '2.4.1') || (_ANDROID_VERSION && _ANDROID_VERSION >= '3.0'))) {
                    $_POST['video_id'] = $video->getIdentity();
                    $_POST['type'] = 'video';
                    $this->_forward('post', 'feed', 'advancedactivity', array(
                        'video_id' => $video->getIdentity(),
                        'type' => 'video'
                    ));
                    return;
                } else {
                    $this->setRequestMethod();
                    $this->_forward('view', 'index', 'video', array(
                        'video_id' => $video->getIdentity()
                    ));
                }
            } catch (Exception $e) {
                $db->commit();
                $_SERVER['REQUEST_METHOD'] = 'GET';
                $this->_forward('view', 'index', 'video', array(
                    'video_id' => $video->getIdentity()
                ));
            }
        }
    }

    /**
     * Delete the video.
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
            $video = Engine_Api::_()->core()->getSubject('video');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($video))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid())
            $this->respondWithError('unauthorized');

        $db = $video->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            Engine_Api::_()->getApi('core', 'video')->deleteVideo($video);
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Return the "Edit Video" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $video = $subject = Engine_Api::_()->core()->getSubject('video');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        if ($viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        // FIND OUT THE AUTH COMMENT AND AOUTH VIEW VALUE.
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        // CHECK VIDEO FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE VIDEO EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            // IF THERE ARE NO FORM POST YET THEN RETURN THE VIDEO FORM.
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getForm(0, $subject);
            $formValues = $subject->toArray();
            $formValues['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'video')->videoType($video->type);
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

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* UPDATE THE VIDEO INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO THE ARRAY.
            $data = $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values;

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'video')->getFormValidators($subject);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Process
            $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
            $db->beginTransaction();
            try {
                $video->setFromArray($values);
                $video->save();

                // CREATE AUTH STUFF HERE
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if ($values['auth_view'])
                    $auth_view = $values['auth_view'];
                else
                    $auth_view = "everyone";
                $viewMax = array_search($auth_view, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
                }

                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if ($values['auth_comment'])
                    $auth_comment = $values['auth_comment'];
                else
                    $auth_comment = "everyone";
                $commentMax = array_search($auth_comment, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
                }

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $video->tags()->setTagMaps($viewer, $tags);

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $db->beginTransaction();
            try {
                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($video) as $action) {
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
     * Rate to the video
     * 
     * @return array
     */
    public function rateAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (($video_id = $this->getRequestParam('video_id')) && empty($video_id)) {
            $this->respondWithValidationError("parameter_missing", "video_id");
        }

        if (($rating = $this->getRequestParam('rating')) && empty($rating)) {
            $this->respondWithValidationError("parameter_missing", "rating");
        }

        $table = Engine_Api::_()->getDbtable('ratings', 'video');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            Engine_Api::_()->video()->setRating($video_id, $viewer_id, $rating);

            $video = Engine_Api::_()->getItem('video', $video_id);
            $video->rating = Engine_Api::_()->video()->getRating($video->getIdentity());
            $video->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        $total = Engine_Api::_()->video()->ratingCount($video->getIdentity());

        $this->respondWithSuccess(array(
            "rating_count" => $total
        ));
    }

    /**
     * Get helper method
     *
     * @return array
     */
    private function _extractCode($url, $type) {
        switch ($type) {
            //youtube
            case "1":
                // change new youtube URL to old one
                $new_code = @pathinfo($url);
                $url = preg_replace("/#!/", "?", $url);

                // get v variable from the url
                $arr = array();
                $arr = @parse_url($url);
                if ($arr['host'] === 'youtu.be') {
                    $data = explode("?", $new_code['basename']);
                    $code = $data[0];
                } else {
                    $parameters = $arr["query"];
                    parse_str($parameters, $data);
                    $code = $data['v'];
                    if ($code == "") {
                        $code = $new_code['basename'];
                    }
                }
                return $code;
            //vimeo
            case "2":
                // get the first variable after slash
                $code = @pathinfo($url);
                return $code['basename'];
        }
    }

    /**
     * Check YouTube videos exist or not.
     *
     * @return array
     */
    private function _checkYouTube($code) {
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
        if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key))
            return false;

        $data = Zend_Json::decode($data);
        if (empty($data['items']))
            return false;
        return true;
    }

    /**
     * Check Vimeo videos exist or not.
     *
     * @return array
     */
    private function _checkVimeo($code) {
        $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $id = count($data->video->id);
        if ($id == 0)
            return false;
        return true;
    }

    /**
     * Handle thumbnail
     *
     * @return array
     */
    private function _handleThumbnail($type, $code = null) {
        switch ($type) {
            //youtube
            case "1":
                //https://i.ytimg.com/vi/Y75eFjjgAEc/default.jpg
                return "https://i.ytimg.com/vi/$code/default.jpg";
            //vimeo
            case "2":
                //thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_medium;
                return $thumbnail;
        }
    }

    /**
     * Retrieves information and returns title and description.
     *
     * @return array
     */
    private function _handleInformation($type, $code) {
        switch ($type) {
            //youtube
            case "1":
                $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
                $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
                if (empty($data)) {
                    return;
                }
                $data = Zend_Json::decode($data);
                $information = array();
                $youtube_video = $data['items'][0];
                $information['title'] = $youtube_video['snippet']['title'];
                $information['description'] = $youtube_video['snippet']['description'];
                $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
                return $information;
            //vimeo
            case "2":
                //thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_medium;
                $information = array();
                $information['title'] = $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration'] = $data->video->duration;
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                return $information;
        }
    }

    /**
     * Upload the video in case of YouTube and Vimeo
     *
     * @return array
     */
    private function _composeUploadAction($values) {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity())
            $this->respondWithError("unauthorized");

        $values['user_id'] = $viewer->getIdentity();
        $code = $this->_extractCode($values['url'], $values['type']);

        // check if code is valid
        // check which API should be used
        if ($values['type'] == 1) {
            $valid = $this->_checkYouTube($code);
            if (empty($valid))
                $this->respondWithError("youtube_validation_fail");
        }

        if ($values['type'] == 2) {
            $valid = $this->_checkVimeo($code);
            if (empty($valid))
                $this->respondWithError("vimeo_validation_fail");
        }

        if (!empty($valid)) {
            $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
            $db->beginTransaction();
            try {
                // Getting the URL information.
                $information = $this->_handleInformation($values['type'], $code);

                $values['code'] = $code;
                $values['title'] = (!empty($values['title'])) ? $values['title'] : $information['title'];
                $values['description'] = !empty($values['description']) ? $values['description'] : $information['description'];
                $values['duration'] = !empty($information['duration']) ? $information['duration'] : '';

                // create video
                $table = Engine_Api::_()->getDbtable('videos', 'video');
                $video = $table->createRow();
                $video->setFromArray($values);
                $video->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            return $video;
        }

        $this->respondWithError("video_not_found");
    }

    /**
     * Upload video from device
     *
     * @return array
     */
    private function _uploadVideoAction($values) {
        if (!$this->_helper->requireUser()->checkRequire())
            $this->respondWithError("invalid_file_size");

        if (empty($_FILES['filedata']))
            $this->respondWithError("no_record");

        if (!isset($_FILES['filedata']) || !is_uploaded_file($_FILES['filedata']['tmp_name']))
            $this->respondWithError("invalid_upload");

        $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
        if (in_array(pathinfo($_FILES['filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions))
            $this->respondWithError("invalid_upload");

        $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
        $db->beginTransaction();
        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $params = array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            );

            $video = $this->createVideo($params, $_FILES['filedata'], $values);


            // sets up title and owner_id now just incase members switch page as soon as upload is completed
            $video->title = (!empty($values['title'])) ? $values['title'] : "video";
            $video->description = (!empty($values['description'])) ? $values['description'] : '';
            $video->owner_id = $viewer->getIdentity();
            $video->type = 3;
            $video->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        return $video;
    }

    /**
     * Get the list of gutter menus list.
     * 
     * @return array
     */
    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();

        // CREATE VIDEO LINK
        if (($viewer->getIdentity() == $owner->getIdentity()) && Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create')) {
            $menus[] = array(
                'label' => $this->translate('Post New Video'),
                'name' => 'create',
                'url' => 'videos/create'
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'label' => $this->translate('Edit Video'),
                'name' => 'edit',
                'url' => 'videos/edit/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'label' => $this->translate('Delete Video'),
                'name' => 'delete',
                'url' => 'videos/delete/' . $subject->getIdentity(),
                'urlParams' => array(
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
        }

        if ($viewer->getIdentity() && ($viewer->getIdentity() != $owner->getIdentity())) {
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

    // HELPER FUNCTIONS

    public function handleIframelyInformation($uri) {
        $iframelyDisallowHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('video_iframely_disallow');
        if (parse_url($uri, PHP_URL_SCHEME) === null) {
            $uri = "http://" . $uri;
        }
        $uriHost = Zend_Uri::factory($uri)->getHost();
        if ($iframelyDisallowHost && in_array($uriHost, $iframelyDisallowHost)) {
            return;
        }
        $config = Engine_Api::_()->getApi('settings', 'core')->core_iframely;
        $iframely = Engine_Iframely::factory($config)->get($uri);
        if (!in_array('player', array_keys($iframely['links']))) {
            return;
        }
        $information = array('thumbnail' => '', 'title' => '', 'description' => '', 'duration' => '');
        if (!empty($iframely['links']['thumbnail'])) {
            $information['thumbnail'] = $iframely['links']['thumbnail'][0]['href'];
            if (parse_url($information['thumbnail'], PHP_URL_SCHEME) === null) {
                $information['thumbnail'] = str_replace(array('://', '//'), '', $information['thumbnail']);
                $information['thumbnail'] = "http://" . $information['thumbnail'];
            }
        }

        if (!isset($information['thumbnail']) || empty($information['thumbnail'])) {
            $page_content = file_get_contents($uri);
            $dom_obj = new DOMDocument();
            $dom_obj->loadHTML($page_content);
            $meta_val = null;

            foreach ($dom_obj->getElementsByTagName('meta') as $meta) {

                if ($meta->getAttribute('property') == 'og:image') {

                    $information['thumbnail'] = $meta->getAttribute('content');
                }
            }
        }

        if (!empty($iframely['meta']['title'])) {
            $information['title'] = $iframely['meta']['title'];
        }
        if (!empty($iframely['meta']['description'])) {
            $information['description'] = $iframely['meta']['description'];
        }
        if (!empty($iframely['meta']['duration'])) {
            $information['duration'] = $iframely['meta']['duration'];
        }
        $information['code'] = $iframely['html'];
        return $information;
    }

    // handle video upload
    public function createVideo($params, $file, $values) {
        if ($file instanceof Storage_Model_File) {
            $params['file_id'] = $file->getIdentity();
        } else {
            // create video item
            $video = Engine_Api::_()->getDbtable('videos', 'video')->createRow();
            $file_ext = pathinfo($file['name']);
            $file_ext = $file_ext['extension'];
            $video->code = $file_ext;
            $video->save();

            // Store video in temporary storage object for ffmpeg to handle
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $storageObject = $storage->createFile($file, array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id,
            ));

            // Remove temporary file
            @unlink($file['tmp_name']);

            $video->file_id = $storageObject->file_id;
            $video->save();

            if (!empty($_FILES['photo'])) {
                Engine_Api::_()->getApi('Siteapi_Core', 'video')->setPhoto($_FILES['photo'], $video, false);
                    $video->status = 1;
                    $video->save();
                    return $video;
            }


            // Add to jobs
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.html5', false)) {
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('video_encode', array(
                    'video_id' => $video->getIdentity(),
                    'type' => 'mp4',
                ));
            } else {
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('video_encode', array(
                    'video_id' => $video->getIdentity(),
                    'type' => 'flv',
                ));
            }
        }

        return $video;
    }

}
