<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    MessagesController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Messages_MessagesController extends Siteapi_Controller_Action_Standard {

    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerId = $viewer->getIdentity();

        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $siteapiMessagesView = Zend_Registry::isRegistered('siteapiMessagesView') ? Zend_Registry::get('siteapiMessagesView') : null;
        if (empty($siteapiMessagesView) || empty($viewerId)) {
            $this->_forward('throw-error', 'messages', 'messages', array(
                "error_code" => "unauthorized"
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
     * Getting the posted messages
     *
     * @return array
     */
    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        try {
            $requestLimit = $this->getRequestParam("limit", 10);
            $requestPage = $this->getRequestParam("page", 1);

            $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getInboxPaginator($viewer);
            $paginator->setItemCountPerPage($requestLimit);
            $paginator->setCurrentPageNumber($requestPage);

            // Now mark them all as view
            $response = $conversation_ids = array();
            foreach ($paginator as $conversation) {
                $conversation_ids[] = $conversation->conversation_id;

                $values = array();
                $message = $conversation->getInboxMessage($viewer);
                if (!empty($message))
                    $values['message'] = $message->toArray();

                $recipient = $conversation->getRecipientInfo($viewer);
                if (!empty($recipient))
                    $values['recipient'] = $recipient->toArray();

                $resource = "";
                $sender = "";
                if ($conversation->hasResource() &&
                        ($resource = $conversation->getResource())) {
                    $sender = $resource;
                } else if ($conversation->recipients > 1) {
                    $sender = $viewer;
                } else {
                    foreach ($conversation->getRecipients() as $tmpUser) {
                        if ($tmpUser->getIdentity() != $viewer->getIdentity()) {
                            $sender = $tmpUser;
                        }
                    }
                }
                if ((!isset($sender) || !$sender) && $viewer->getIdentity() !== $conversation->user_id) {
                    $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
                }
                if (!isset($sender) || !$sender) {
                    //continue;
                    $sender = new User_Model_User(array());
                }

                $values['sender'] = $sender->toArray();
                $values['sender'] = Engine_Api::_()->getApi('Core', 'siteapi')->sanitizeArray($values['sender'], array('email'));
                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($sender);
                $values["sender"] = array_merge($values["sender"], $getContentImages);

                $response[] = $values;
            }
            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->markMessagesAsShow($viewer, $conversation_ids);
            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Mark message read unread.
     *
     * @return array
     */
    public function markMessageReadUnreadAction() {
        if (null === ($message_id = $this->getRequestParam('message_id', null))) {
            $this->validateRequestMethod('POST');

            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')->markMessagesAsShow($viewer);
                $this->successResponseNoContent('no_content');
            } else {
                $this->respondWithError('unauthorized');
            }
        }

        $is_read = $this->getRequestParam('is_read', 0);
        Engine_Api::_()->getApi('Siteapi_Core', 'activity')->markMessageReadUnread($message_id, $is_read);
        $this->successResponseNoContent('no_content');
    }

    /**
     * Getting the message inbox
     *
     * @return array
     */
    public function inboxAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        try {
            $requestLimit = $this->getRequestParam("limit", 10);
            $requestPage = $this->getRequestParam("page", 1);

            $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getInboxPaginator($viewer);
            $paginator->setItemCountPerPage($requestLimit);
            $paginator->setCurrentPageNumber($requestPage);
            $response['getTotalItemCount'] = $paginator->getTotalItemCount();
            $response['getUnreadMessageCount'] = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);

            foreach ($paginator as $conversation) {
                $values = array();
                $message = $conversation->getInboxMessage($viewer);
                $user = Engine_Api::_()->getItem('user', $message->user_id);
                if (!empty($message)) {
                    $values['message'] = $message->toArray();
                    ( (isset($message) && '' != ($title = trim($message->getTitle()))) ||
                            (isset($conversation) && '' != ($title = trim($conversation->getTitle()))) ||
                            $title = '' );

                    if (!empty($title))
                        $values['message']['title'] = $title;
                    $values['message']['recipients_count'] = $conversation->recipients;
                    $values['message']['conversation_id'] = $conversation->getIdentity();
                }
                else {
                    $values['message']['conversation_id'] = $conversation->getIdentity();
                }

                //Member verification Work...............
                $values['message']['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($user);

                $recipient = $conversation->getRecipientInfo($viewer);
                if (!empty($recipient))
                    $values['recipient'] = $recipient->toArray();
                else
                    $values['recipient']['conversation_id'] = 0;

                $resource = "";
                $sender = "";
                if ($conversation->hasResource() &&
                        ($resource = $conversation->getResource())) {
                    $sender = $resource;
                } else if ($conversation->recipients > 1) {
                    $sender = $viewer;
                } else {
                    foreach ($conversation->getRecipients() as $tmpUser) {
                        if ($tmpUser->getIdentity() != $viewer->getIdentity()) {
                            $sender = $tmpUser;
                        }
                    }
                }
                if ((!isset($sender) || !$sender) && $viewer->getIdentity() !== $conversation->user_id) {
                    $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
                }
                if (!isset($sender) || !$sender) {
                    //continue;
                    $sender = new User_Model_User(array());
                }

                if ($sender->getType() == 'user') {
                    $values['sender'] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($sender);
                } else if ($sender->getType() == 'group') {
                    $values['sender'] = $sender->toArray();
                }

//        // Get conversation title
//        if(!empty($resource)) {
//          $values['sender']['conversation_title'] = $resource->toString();          
//        } elseif($conversation->recipients == 1) {
//          $values['sender']['conversation_title'] = $sender->getTitle();
//        } else {
//          $recipientTitle =  ($conversation->recipients == 1)? 'person': 'people';
//          $values['sender']['conversation_title'] = $conversation->recipients . ' ' . $recipientTitle;
//        }
                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($sender);
                $values["sender"] = array_merge($values["sender"], $getContentImages);

                $response['response'][] = $values;
            }

            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Messaeg search
     *
     * @return array
     */
    public function searchAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        try {
            $table = Engine_Api::_()->getDbtable('messages', 'messages');
            $query = $table->select()
                    ->from('engine4_messages_messages')
                    ->joinRight('engine4_messages_recipients', 'engine4_messages_recipients.conversation_id = engine4_messages_messages.conversation_id', null)
                    ->where('engine4_messages_recipients.user_id = ?', $viewer->user_id)
                    ->where('(engine4_messages_messages.title LIKE ? || engine4_messages_messages.body LIKE ?)', '%' . $this->getRequestParam('query') . '%')
                    ->order('engine4_messages_messages.message_id DESC')
            ;

            $paginatorAdapter = new Zend_Paginator_Adapter_DbTableSelect($query);
            $requestLimit = $this->getRequestParam("limit", 10);
            $requestPage = $this->getRequestParam("page", 1);

            $paginator = new Zend_Paginator($paginatorAdapter);
            $paginator->setItemCountPerPage($requestLimit);
            $paginator->setCurrentPageNumber($requestPage);
            $response['getTotalItemCount'] = $paginator->getTotalItemCount();

            foreach ($paginator as $message) {
                $values['message'] = $message->toArray();

                $conversation = Engine_Api::_()->getItem('messages_conversation', $message->conversation_id);
                $recipient = $conversation->getRecipientInfo($viewer);
                if (!empty($recipient))
                    $values['recipient'] = $recipient->toArray();

                $resource = "";
                $sender = "";
                if ($conversation->hasResource() &&
                        ($resource = $conversation->getResource())) {
                    $sender = $resource;
                } else if ($conversation->recipients > 1) {
                    $sender = $viewer;
                } else {
                    foreach ($conversation->getRecipients() as $tmpUser) {
                        if ($tmpUser->getIdentity() != $viewer->getIdentity()) {
                            $sender = $tmpUser;
                        }
                    }
                }
                if ((!isset($sender) || !$sender) && $viewer->getIdentity() !== $conversation->user_id) {
                    $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
                }
                if (!isset($sender) || !$sender) {
                    //continue;
                    $sender = new User_Model_User(array());
                }

                $values['sender'] = $sender->toArray();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($sender);
                $values["sender"] = array_merge($values["sender"], $getContentImages);

                $response['response'][] = $values;
            }

            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Delete message
     *
     * @return array
     */
    public function deleteAction() {
        $message_ids = $this->getRequestParam('conversation_ids', null);
        if (!empty($message_ids))
            $messages = @explode(',', $message_ids);

        if (empty($message_ids))
            $this->respondWithValidationError('parameter_missing', 'message_id');

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();
        try {
            foreach ($messages as $message_id) {
                $recipients = Engine_Api::_()->getItem('messages_conversation', $message_id)->getRecipientsInfo();
                foreach ($recipients as $r) {
                    if ($viewer_id == $r->user_id) {
                        $r->inbox_deleted = true;
                        $r->outbox_deleted = true;
                        $r->save();
                    }
                }
            }
            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollback();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Outbox message
     *
     * @return array
     */
    public function outboxAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        try {
            $requestLimit = $this->getRequestParam("limit", 10);
            $requestPage = $this->getRequestParam("page", 1);

            $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getOutboxPaginator($viewer);
            $paginator->setItemCountPerPage($requestLimit);
            $paginator->setCurrentPageNumber($requestPage);
            $response['getTotalItemCount'] = $paginator->getTotalItemCount();
            $response['getUnreadMessageCount'] = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);

            foreach ($paginator as $conversation) {
                $values = array();
                $message = $conversation->getOutboxMessage($viewer);
                $user = Engine_Api::_()->getItem('user', $message->user_id);
                if (!empty($message)) {
                    $values['message'] = $message->toArray();
                    ( (isset($message) && '' != ($title = trim($message->getTitle()))) ||
                            (isset($conversation) && '' != ($title = trim($conversation->getTitle()))) ||
                            $title = '' );

                    if (!empty($title))
                        $values['message']['title'] = $title;
                    $values['message']['recipients_count'] = $conversation->recipients;
                }else {
                    --$response['getTotalItemCount'];
                    continue;
                }

                $recipient = $conversation->getRecipientInfo($viewer);
                if (!empty($recipient))
                    $values['recipient'] = $recipient->toArray();

                //Member verification Work...............
                $values['message']['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($user);

                $resource = "";
                $sender = "";
                if ($conversation->hasResource() &&
                        ($resource = $conversation->getResource())) {
                    $sender = $resource;
                } else if ($conversation->recipients > 1) {
                    $sender = $viewer;
                } else {
                    foreach ($conversation->getRecipients() as $tmpUser) {
                        if ($tmpUser->getIdentity() != $viewer->getIdentity()) {
                            $sender = $tmpUser;
                        }
                    }
                }

                if ((!isset($sender) || !$sender) && $viewer->getIdentity() !== $conversation->user_id) {
                    $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
                }
                if (!isset($sender) || !$sender) {
                    //continue;
                    $sender = new User_Model_User(array());
                }

                if ($sender->getType() == 'user') {
                    $values['sender'] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($sender);
                    if (!isset($values['sender']['user_id']) || empty($values['sender']['user_id']))
                        continue;
                } else if ($sender->getType() == 'group') {
                    $values['sender'] = $sender->toArray();
                }

//        // Get conversation title
//        if(!empty($resource)) {
//          $values['sender']['conversation_title'] = $resource->toString();          
//        } elseif($conversation->recipients == 1) {
//          $values['sender']['conversation_title'] = $sender->getTitle();
//        } else {
//          $recipientTitle =  ($conversation->recipients == 1)? 'person': 'people';
//          $values['sender']['conversation_title'] = $conversation->recipients . ' ' . $recipientTitle;
//        }
                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($sender);
                $values["sender"] = array_merge($values["sender"], $getContentImages);

                if (isset($values['sender']['lastlogin_ip']) && !empty($values['sender']['lastlogin_ip']))
                    unset($values['sender']['lastlogin_ip']);

                if (isset($values['sender']['creation_ip']) && !empty($values['sender']['creation_ip']))
                    unset($values['sender']['creation_ip']);

                $response['response'][] = $values;
            }

            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Message view
     *
     * @return array
     */
    public function viewAction() {
        $response = array();
        $id = $this->getRequestParam('id');
        $story_id = $this->getRequestParam('story_id', 0);
        $viewer = Engine_Api::_()->user()->getViewer();
        $post_attach = $this->getRequestParam('post_attach');
        $page = $this->getRequestParam('page', 0);
        $limit = $this->getRequestParam('limit', 0);
        // Get conversation info
        $conversation = Engine_Api::_()->getItem('messages_conversation', $id);
        $response['conversation'] = $conversation->toArray();
        $response['conversation']['isSendMessage'] =0;
        $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
            
        if($showMessageOwner == 'everyone'){
        $response['conversation']['isSendMessage'] =1;
        }
        // Make sure the user is part of the conversation
        if (!$conversation || !$conversation->hasRecipient($viewer)) {
            return $this->_forward('inbox');
        }

        // Check for resource
        if (!empty($conversation->resource_type) &&
                !empty($conversation->resource_id)) {
            $resource = Engine_Api::_()->getItem($conversation->resource_type, $conversation->resource_id);
            if (!($resource instanceof Core_Model_Item_Abstract)) {
                return $this->_forward('inbox');
            }
        }
        // Otherwise get recipients
        else {
            $recipients = $conversation->getRecipients();

            $blocked = false;
            $blocker = "";

            // This is to check if the viewered blocked a member
            $viewer_blocked = false;
            $viewer_blocker = "";

            foreach ($recipients as $recipient) {
                if($showMessageOwner == 'friends' && $recipient->membership()->isMember($viewer)){
              $response['conversation']['isSendMessage'] =1;

            }
                if ($viewer->isBlockedBy($recipient)) {
                    $blocked = true;
                    $blocker = $recipient;
                } elseif ($recipient->isBlockedBy($viewer)) {
                    $viewer_blocked = true;
                    $viewer_blocker = $recipient;
                }
            }
        }

        if ($this->getRequest()->isPost()) {

            if ($conversation->locked)
                $this->respondWithError('unauthorized');

            // Can we reply?
            if (!$conversation->locked) {
                // Assign the composing junk
                $composePartials = array();
                $body = $_REQUEST['body'];
                $values = $_REQUEST;

                if (!empty($story_id)) {
                    $story = Engine_Api::_()->getItem('sitestories_story', $story_id);
                    $browseStory = $story->toArray();
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($story);
                    if(!empty($browseStory['type']) && $browseStory['type'] == 'text'){
                        $params = Zend_Json::decode($browseStory['params'],true);

                        if($params['file_id'])
                        {
                            $file_id = $params['file_id'];
                            $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        
                            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.main');
                            $file1 = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.icon');
                            $file2 = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.profile');
                            $file3 = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.normal');
                           
                            if (!empty($file)) {
                                $tempArray = array(
                                    "image" => strstr($file->map(), 'http') ? $file->map() : $getHost . $file->map(),
                                    "image_icon" => strstr($file1->map(), 'http') ? $file1->map() : $getHost . $file1->map(),
                                    "image_profile" => strstr($file2->map(), 'http') ? $file2->map() : $getHost . $file2->map(),
                                    "image_normal" => strstr($file3->map(), 'http') ? $file3->map() : $getHost . $file3->map()
                                );
                                
                            }
                        }     
                    }
                    if($browseStory['type'] == 'text')
                        $story_thumbnail = $tempArray['image_profile'];
                    else
                        $story_thumbnail = $getContentImages['image_profile'];
                }

                if (isset($_REQUEST['body']) && !empty($_REQUEST['body'])) {
                    if (!empty($_REQUEST['isStory'])) {
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('primemessenger')) {
                            $values['body'] = 'Reply on Story: ' . $values['body'];
                        } else {
                                $values['body'] = 'Reply on Story: <img src="' . $story_thumbnail . '"><br>' . $values['body'];
                        }
                    }
                }

                if (!isset($_REQUEST['body']) || empty($_REQUEST['body']))
                    $this->respondWithError('validation_fail');

                $isValid = true;
                if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                  
                  $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'messages_flood');

                  if(!empty($itemFlood[0])){
                      //get last activity
                      $tableFlood = Engine_Api::_()->getDbTable("messages",'messages');
                      $select = $tableFlood->select()->where("user_id = ?",$viewer->getIdentity())->order("date DESC");
                      if($itemFlood[1] == "minute"){
                          $select->where("date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
                      }else if($itemFlood[1] == "day"){
                          $select->where("date >= DATE_SUB(NOW(),INTERVAL 1 DAY)");
                      }else{
                          $select->where("date >= DATE_SUB(NOW(),INTERVAL 1 HOUR)");
                      }
                      $floodItem = $tableFlood->fetchAll($select);
                      if(count($floodItem) && $itemFlood[0] <= count($floodItem)){
                          $type = $itemFlood[1];
                          $time =  "1 ".$type;
                          $message = 'You have reached maximum limit of posting in '.$time.'. Try again after this duration expires.';
                          $response['error_message'] = $message;
                          $isValid = false;
                      }
                  }
                }

                if($isValid) {
                    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
                    $db->beginTransaction();
                    try {
                        // Try attachment getting stuff
                        $attachment = null;

                        // Try attachment getting stuff
                        $attachment = null;
                        if (isset($post_attach) && ($post_attach == 1)) {
                            $type = $_POST['type'];
                            //to attach link
                            if ($type == 'link') {
                                // clean URL for html code
                                $uri = trim(strip_tags($_POST['uri']));
                                if (empty($uri))
                                    $this->respondWithValidationError('validation_fail', "URI is required");
                                $info = parse_url($uri);
                                // Process
                                $viewer = Engine_Api::_()->user()->getViewer();
                                // Use viewer as subject if no subject
                                if (null === $subject) {
                                    $subject = $viewer;
                                }

                                try {
                                    $client = new Zend_Http_Client($uri, array(
                                        'maxredirects' => 2,
                                        'timeout' => 10,
                                    ));
                                } catch (Exception $e) {
                                    $this->respondWithError('invalid_url');
                                }
                                // Try to mimic the requesting user's UA
                                $client->setHeaders(array(
                                    'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                                    'X-Powered-By' => 'Zend Framework'
                                ));
                                $tempResponse = $client->request();
                                $link = $this->_getUrlInfo($uri, $tempResponse);
                                if (isset($link) && !empty($link)) {
                                    $table = Engine_Api::_()->getDbtable('links', 'core');
                                    $db = $table->getAdapter();
                                    $db->beginTransaction();
                                    try {
                                        //link creation
                                        $attachment = Engine_Api::_()->getApi('links', 'core')->createLink($viewer, $link);
                                        $attachment->uri = $link['url'];
                                        $attachment->save();
                                        $db->commit();
                                    } catch (Exception $e) {
                                        throw $e;
                                        $this->respondWithValidationError('internal_server_error', $e->getMessage());
                                    }
                                } else
                                    $this->respondWithValidationError('internal_server_error');
                            } else if ($type == 'video' &&
                                    isset($_POST['video_id']) &&
                                    !empty($_POST['video_id'])) {
                                $attachmentData['video_id'] = $_POST['video_id'];
                                $video = Engine_Api::_()->getItem('video', $_POST['video_id']);
                                if (isset($video) && !empty($video)) {
                                    $attachmentData['title'] = $video->title;
                                    $attachmentData['description'] = $video->description;
                                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo'))
                                        $plugin = Engine_Api::_()->loadClass('Sitevideo_Plugin_Composer');
                                    elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video'))
                                        $plugin = Engine_Api::_()->loadClass('Video_Plugin_Composer');
                                    $method = 'onAttachVideo';
                                    $attachment = $plugin->$method($attachmentData);
                                }
                            } else if ($type == 'music' &&
                                    isset($_POST['song_id']) &&
                                    !empty($_POST['song_id'])) {
                                $plugin = Engine_Api::_()->loadClass('Music_Plugin_Composer');
                                $method = 'onAttachMusic';
                                $attachmentData['song_id'] = $_POST['song_id'];
                                $song = Engine_Api::_()->getItem('music_playlist_song', $_POST['song_id']);
                                if (isset($song) && !empty($song)) {
                                    $attachmentData['title'] = $song->title;
                                    $attachment = $plugin->$method($attachmentData);
                                }
                            } else if (!empty($_FILES['photo']) && ($type == 'photo')) {

                                $table = Engine_Api::_()->getDbtable('albums', 'album');
                                $type = $this->getRequestParam('image_type', 'message');
                                $album = $table->getSpecialAlbum($viewer, $type);
                                $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                                $photo = $photoTable->createRow();
                                $photo->owner_type = 'user';
                                $photo->owner_id = $viewer->getIdentity();
                                $photo->save();
                                // Set the photo
                                $photo = $this->_setPhoto($_FILES['photo'], $photo);
                                $photo->order = $photo->photo_id;
                                $photo->album_id = $album->album_id;
                                $photo->save();
                                if (!$album->photo_id) {
                                    $album->photo_id = $photo->getIdentity();
                                    $album->save();
                                }
                                if ($type != 'message') {
                                    // Authorizations
                                    $auth = Engine_Api::_()->authorization()->context;
                                    $auth->setAllowed($photo, 'everyone', 'view', true);
                                    $auth->setAllowed($photo, 'everyone', 'comment', true);
                                }
                                $attachment = $photo;
                            }
                            $parent = $attachment->getParent();
                            if ($parent->getType() === 'user') {
                                $attachment->search = 0;
                                $attachment->save();
                            } else {
                                $parent->search = 0;
                                $parent->save();
                            }
                        }

                        $params['body'] = $body;
                        $params['conversation'] = (int) $id;

                        $conversation->reply(
                                $viewer, $params['body'], $attachment
                        );

                        // Send notifications
                        foreach ($recipients as $user) {
                            if ($user->getIdentity() == $viewer->getIdentity()) {
                                continue;
                            }

                            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification(
                                    $user, $viewer, $conversation, 'message_new'
                            );
                        }
                        // Increment messages counter
                        Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollBack();
                        $this->respondWithValidationError('internal_server_error', $e->getMessage());
                    }
                }
            }
        }

        $photo_count = 0;
        try {
            // Make sure to load the messages after posting :P
            $paginator = $conversation->getMessages($viewer);
            if (isset($page) && !empty($page) && isset($limit) && !empty($limit)) {
                $messages = $this->getMessages($viewer, $conversation);
                $totalItemCount = count($messages);
                $paginator = Zend_Paginator::factory($messages);
                $paginator->setItemCountPerPage($limit);
                $paginator->setCurrentPageNumber($page);
            }
            foreach ($paginator as $message) {
                $values = array();
                $values['message'] = $message->toArray();

                if (isset($values['message']['body']) && !empty($values['message']['body']))
                    $values['message']['body'] = @htmlspecialchars_decode($values['message']['body']);

                if (isset($values['message']['attachment_type']) && isset($values['message']['attachment_id']) && !empty($values['message']['attachment_type']) && !empty($values['message']['attachment_id'])) {
                    $photo = Engine_Api::_()->getItem($values['message']['attachment_type'], $values['message']['attachment_id']);
                    $values['message']['is_like'] = (int) Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);
                }

                if (isset($message->attachment_type) &&
                        !empty($message->attachment_type) &&
                        !empty($message->attachment_id)) {
                    $attachmentObj = $message->getAttachment();
                    if (isset($attachmentObj) && !empty($attachmentObj))
                        $attachment = $message->getAttachment()->toArray();
                    else {
                        unset($values['message']['attachment_type']);
                        unset($values['message']['attachment_id']);
                    }
                }
                if (isset($attachment) && !empty($attachment))
                    $values['attachment'] = $attachment;
                // Add attached content image.
                if (isset($message->attachment_type) &&
                        !empty($message->attachment_type) &&
                        !empty($message->attachment_id) &&
                        ($message->attachment_type == 'album_photo')) {
                    $photo_count ++;
                    $subject = Engine_Api::_()->getItem('album_photo', $message->attachment_id);

                    // Add images
                    if (isset($subject) && !empty($subject)) {
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
                        $values["attachment"] = array_merge($values["attachment"], $getContentImages);
                        $values["attachment"]["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($subject);
                    }
                }

                // Add attached content image.
                if (isset($message->attachment_type) &&
                        !empty($message->attachment_type) &&
                        !empty($message->attachment_id) &&
                        ($message->attachment_type == 'video')) {
                    $subject = Engine_Api::_()->getItem('video', $message->attachment_id);
                    if (isset($subject) && !empty($subject)) {
                        // Add images
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
                        $values["attachment"] = array_merge($values["attachment"], $getContentImages);
                        $values["attachment"]["type"] = $this->videoType($subject);
                    }
                }

                if (isset($message->attachment_type) &&
                        !empty($message->attachment_type) &&
                        !empty($message->attachment_id) &&
                        ($message->attachment_type == 'core_link')) {
                    $subject = Engine_Api::_()->getItem('core_link', $message->attachment_id);
                    if (isset($subject) && !empty($subject)) {
                        // Add images
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
                        $values["attachment"] = array_merge($values["attachment"], $getContentImages);
                    }
                }

                $conversation = Engine_Api::_()->getItem('messages_conversation', $message->conversation_id);

                if ($conversation->hasResource() &&
                        ($resource = $conversation->getResource())) {
                    $sender = $resource;
                } else if ($conversation->recipients > 1) {
                    $sender = $viewer;
                } else {
                    foreach ($conversation->getRecipients() as $tmpUser) {
                        if ($tmpUser->getIdentity() != $viewer->getIdentity()) {
                            $sender = $tmpUser;
                        }
                    }
                }

                $recipient = $conversation->getRecipientInfo($viewer);
                if (!empty($recipient))
                    $values['recipient'] = $recipient->toArray();

                $sender = Engine_Api::_()->user()->getUser($message->user_id);

                $values['sender'] = $sender->toArray();
                $values['sender'] = Engine_Api::_()->getApi('Core', 'siteapi')->sanitizeArray($values['sender'], array('email'));

                if (!isset($values['sender']) || empty($values['sender'])) {
                    $values['sender']['user_id'] = 0;
                    $values['sender']['displayname'] = '';
                }

                if (isset($values['sender']['lastlogin_ip']) && !empty($values['sender']['lastlogin_ip']))
                    unset($values['sender']['lastlogin_ip']);

                if (isset($values['sender']['creation_ip']) && !empty($values['sender']['creation_ip']))
                    unset($values['sender']['creation_ip']);

                if (isset($values['recipient']['lastlogin_ip']) && !empty($values['recipient']['lastlogin_ip']))
                    unset($values['recipient']['lastlogin_ip']);

                if (isset($values['recipient']['creation_ip']) && !empty($values['recipient']['creation_ip']))
                    unset($values['recipient']['creation_ip']);

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($sender);
                $values["sender"] = array_merge($values["sender"], $getContentImages);

                if (is_object($values['attachment']))
                    $values['attachment'] = $values['attachment']->toArray();

                $response['messages'][] = $values;
            }

            $response['conversation_image_count'] = $photo_count;
            if (isset($totalItemCount) && !empty($totalItemCount)) {
                $response['totalItemCount'] = $totalItemCount;
            }
            $response['reply_form'][] = array(
                'type' => 'Textarea',
                'name' => 'body'
            );

            $response['reply_form'][] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Send Reply')
            );

            // Add message menu in response array.
            if ($this->getRequestParam('post_menus', true)) {
                $getPostMenus = $this->_getPostFeedOptions();
                if (!empty($getPostMenus))
                    $response['feed_post_menu'] = $getPostMenus;
            }
        } catch (Exception $ex) {
            // Blank Exception
        }

        $this->respondWithSuccess($response);
    }

    public function getMessages(User_Model_User $user, $conversation) {
        if (empty($conversation->store()->messages)) {
            if (!$conversation->hasRecipient($user)) {
                throw new Messages_Model_Exception('Specified user not in convo');
            }

            $table = Engine_Api::_()->getItemTable('messages_message');
            $select = $table->select()
                    ->where('conversation_id = ?', $conversation->getIdentity())
                    ->order('message_id DESC');
            ;

            $conversation->store()->messages = $table->fetchAll($select);
        }

        return $conversation->store()->messages;
    }

    /**
     * Compose message
     *
     * @return array
     */
    public function composeAction() {
        /* RETURN THE MESSAGE CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
         $viewer = Engine_Api::_()->user()->getViewer();
        $post_attach = $this->getRequestParam('post_attach', 0);
        $story_id = $this->getRequestParam('story_id', 0);
        $auth_Message = Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth');
        if ($auth_Message == 'none') {
            $this->respondWithError('unauthorized');
        }
        
        if ($this->getRequest()->isGet()) {
            // Prepare compose form.
            $responseForm[] = array(
                'type' => 'Text',
                'label' => $this->translate('Send To'),
                'name' => 'toValues',
            );

            $responseForm[] = array(
                'type' => 'Text',
                'label' => $this->translate('Subject'),
                'name' => 'title',
            );

            $responseForm[] = array(
                'type' => 'Textarea',
                'label' => $this->translate('Message'),
                'name' => 'body',
            );

            $responseForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Send Message')
            );


            if ($post_attach == 1) {
                $response['form'] = $responseForm;
                // Add message menu in response array.
                if ($this->getRequestParam('post_menus', true)) {
                    $getPostMenus = $this->_getPostFeedOptions();
                    if (!empty($getPostMenus))
                        $response['feed_post_menu'] = $getPostMenus;
                }
            }
            else {
                $response = $responseForm;
            }
            $this->respondWithSuccess($response);
        } else if ($this->getRequest()->isPost()) {
            /* CREATE THE BLOG IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'messages_flood');

                if(!empty($itemFlood[0])){
                  //get last activity
                  $tableFlood = Engine_Api::_()->getDbTable("messages",'messages');
                  $select = $tableFlood->select()->where("user_id = ?",$viewer->getIdentity())->order("date DESC");
                  if($itemFlood[1] == "minute"){
                      $select->where("date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
                  }else if($itemFlood[1] == "day"){
                      $select->where("date >= DATE_SUB(NOW(),INTERVAL 1 DAY)");
                  }else{
                      $select->where("date >= DATE_SUB(NOW(),INTERVAL 1 HOUR)");
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

            // Process
            $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
            $db->beginTransaction();
            $viewer = Engine_Api::_()->user()->getViewer();

            try {
                // Try attachment getting stuff
                $attachment = null;
                if (isset($post_attach) && ($post_attach == 1)) {
                    $type = $_POST['type'];
                    //to attach link
                    if ($type == 'link') {
                        // clean URL for html code
                        $uri = trim(strip_tags($_POST['uri']));
                        if (empty($uri))
                            $this->respondWithValidationError('validation_fail', "URI is required");
                        $info = parse_url($uri);
                        // Process
                        // Use viewer as subject if no subject
                        if (null === $subject) {
                            $subject = $viewer;
                        }

                        try {
                            $client = new Zend_Http_Client($uri, array(
                                'maxredirects' => 2,
                                'timeout' => 10,
                            ));
                        } catch (Exception $e) {
                            $this->respondWithError('invalid_url');
                        }
                        // Try to mimic the requesting user's UA
                        $client->setHeaders(array(
                            'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                            'X-Powered-By' => 'Zend Framework'
                        ));
                        $response = $client->request();
                        $link = $this->_getUrlInfo($uri, $response);
                        if (isset($link) && !empty($link)) {
                            $table = Engine_Api::_()->getDbtable('links', 'core');
                            $db = $table->getAdapter();
                            $db->beginTransaction();
                            try {
                                //link creation
                                $attachment = Engine_Api::_()->getApi('links', 'core')->createLink($viewer, $link);
                                $attachment->uri = $link['url'];
                                $attachment->save();
                                $db->commit();
                            } catch (Exception $e) {
                                throw $e;
                                $this->respondWithValidationError('internal_server_error', $e->getMessage());
                            }
                        } else
                            $this->respondWithValidationError('internal_server_error');
                    } else if ($type == 'video' &&
                            isset($_POST['video_id']) &&
                            !empty($_POST['video_id'])) {
                        $attachmentData['video_id'] = $_POST['video_id'];
                        $video = Engine_Api::_()->getItem('video', $_POST['video_id']);
                        if (isset($video) && !empty($video)) {
                            $attachmentData['title'] = $video->title;
                            $attachmentData['description'] = $video->description;
                            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo'))
                                $plugin = Engine_Api::_()->loadClass('Sitevideo_Plugin_Composer');
                            elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video'))
                                $plugin = Engine_Api::_()->loadClass('Video_Plugin_Composer');
                            $method = 'onAttachVideo';
                            $attachment = $plugin->$method($attachmentData);
                        }
                    } else if ($type == 'music' &&
                            isset($_POST['song_id']) &&
                            !empty($_POST['song_id'])) {
                        $plugin = Engine_Api::_()->loadClass('Music_Plugin_Composer');
                        $method = 'onAttachMusic';
                        $attachmentData['song_id'] = $_POST['song_id'];
                        $song = Engine_Api::_()->getItem('music_playlist_song', $_POST['song_id']);
                        if (isset($song) && !empty($song)) {
                            $attachmentData['title'] = $song->title;
                            $attachment = $plugin->$method($attachmentData);
                        }
                    } else if (!empty($_FILES['photo']) && ($type == 'photo')) {
                        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                        $type = $this->getRequestParam('image_type', 'message');
                        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                        $photo = $photoTable->createRow();
                        $photo->owner_type = 'user';
                        $photo->owner_id = $viewer->getIdentity();
                        $photo->save();

                        $photo = $this->_setPhoto($_FILES['photo'], $photo);

                        $status = true;
                        $name = $_FILES['userfile']['name'];
                        $photo_id = $photo->photo_id;
                        $photo_url = $photo->getPhotoUrl();

                        $table = Engine_Api::_()->getDbtable('albums', 'album');
                        $album = $table->getSpecialAlbum($viewer, 'message');

                        $photo->album_id = $album->album_id;
                        $photo->save();

                        if (!$album->photo_id) {
                            $album->photo_id = $photo->getIdentity();
                            $album->save();
                        }

                        $auth = Engine_Api::_()->authorization()->context;
                        $auth->setAllowed($photo, 'everyone', 'view', true);
                        $auth->setAllowed($photo, 'everyone', 'comment', true);
                        $auth->setAllowed($album, 'everyone', 'view', true);
                        $auth->setAllowed($album, 'everyone', 'comment', true);

                        $attachment = $photo;
                    }
                    if ($attachment) {
                        $parent = $attachment->getParent();

                        if ($parent->getType() === 'user') {
                            $attachment->search = 0;
                            $attachment->save();
                        } else {
                            $parent->search = 0;
                            $parent->save();
                        }
                    }
                }
                $viewer = Engine_Api::_()->user()->getViewer();

                // CONVERT POST DATA INTO THE ARRAY.
                $values = array();
                $getFormKeys = array("toValues", "title", "body");
                foreach ($getFormKeys as $element) {
                    if (isset($_REQUEST[$element]))
                        $values[$element] = $_REQUEST[$element];
                }

                if (!empty($story_id)) {
                    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestories'))
                        $story = Engine_Api::_()->getItem('sitestories_story', $story_id);
                    else 
                        $story = Engine_Api::_()->getItem('advancedactivity_story', $story_id);
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($story);
                    $browseStory = $story->toArray();
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($story);
                    if(!empty($browseStory['type']) && $browseStory['type'] == 'text'){
                        $params = Zend_Json::decode($browseStory['params'],true);

                        if($params['file_id'])
                        {
                            $file_id = $params['file_id'];
                            $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        
                            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.main');
                            $file1 = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.icon');
                            $file2 = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.profile');
                            $file3 = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.normal');
                           
                            if (!empty($file)) {
                                $tempArray = array(
                                    "image" => strstr($file->map(), 'http') ? $file->map() : $getHost . $file->map(),
                                    "image_icon" => strstr($file1->map(), 'http') ? $file1->map() : $getHost . $file1->map(),
                                    "image_profile" => strstr($file2->map(), 'http') ? $file2->map() : $getHost . $file2->map(),
                                    "image_normal" => strstr($file3->map(), 'http') ? $file3->map() : $getHost . $file3->map()
                                );
                                
                            }
                        }     
                    }
                    if($browseStory['type'] == 'text')
                        $story_thumbnail = $tempArray['image_profile'];
                    else
                        $story_thumbnail = $getContentImages['image_profile'];
                }
                if (isset($_REQUEST['body']) && !empty($_REQUEST['body'])) {
                    if (!empty($_REQUEST['isStory'])) {
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('primemessenger')) {
                            $values['body'] = 'Reply on Story: ' . $values['body'];
                        } else 
                            $values['body'] = 'Reply on Story: <img src="' . $story_thumbnail . '"><br>' . $values['body'];

                        $values['title'] = 'Reply on Story';
                    }
                }
                // Get tovalue
                $recipients = preg_split('/[,. ]+/', $values['toValues']);
                // clean the recipients for repeating ids
                // this can happen if recipient is selected and then a friend list is selected
                $recipients = array_unique($recipients);
                // Slice down to 10
                $recipients = array_slice($recipients, 0, $maxRecipients);
                // Get user objects
                $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
                // Validate friends
                if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
                    foreach ($recipientsUsers as &$recipientUser) {
                        if (!$viewer->membership()->isMember($recipientUser)) {
//              return $form->addError('One of the members specified is not in your friends list.');
                        }
                    }
                }
//        // Prepopulated
//        if ( $toObject instanceof User_Model_User ) {
//          $recipientsUsers = array($toObject);
//          $recipients = $toObject;
//          // Validate friends
//          if ( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
//            if ( !$viewer->membership()->isMember($recipients) ) {
//              return $form->addError('One of the members specified is not in your friends list.');
//            }
//          }
//        } else if ( $toObject instanceof Core_Model_Item_Abstract &&
//                method_exists($toObject, 'membership') ) {
//          $recipientsUsers = $toObject->membership()->getMembers();
////        $recipients = array();
////        foreach( $recipientsUsers as $recipientsUser ) {
////          $recipients[] = $recipientsUser->getIdentity();
////        }
//          $recipients = $toObject;
//        }
//        // Normal
//        else {
//          $recipients = preg_split('/[,. ]+/', $values['toValues']);
//          // clean the recipients for repeating ids
//          // this can happen if recipient is selected and then a friend list is selected
//          $recipients = array_unique($recipients);
//          // Slice down to 10
//          $recipients = array_slice($recipients, 0, $maxRecipients);
//          // Get user objects
//          $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
//          // Validate friends
//          if ( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
//            foreach ( $recipientsUsers as &$recipientUser ) {
//              if ( !$viewer->membership()->isMember($recipientUser) ) {
//                return $form->addError('One of the members specified is not in your friends list.');
//              }
//            }
//          }
//        }
                // Create conversation
                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                        $viewer, $recipients, $values['title'], $values['body'], $attachment
                );

                // Send notifications
                foreach ($recipientsUsers as $user) {
                    if ($user->getIdentity() == $viewer->getIdentity()) {
                        continue;
                    }
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification(
                            $user, $viewer, $conversation, 'message_new'
                    );
                }

                // Increment messages counter
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

                // Commit
                $db->commit();
                
                if (!empty($_REQUEST['isStory'])) {
                    $replyTable = Engine_Api::_()->getDbtable('replies','sitestories');
                    $storyRow = $replyTable->createRow();
                    $storyRow->conversation_id = $conversation->conversation_id;
                    $storyRow->story_id = $story_id;
                    $storyRow->save();
                }
               
                $this->setRequestMethod();

                $this->_forward('view', 'messages', 'messages', array(
                    'id' => $conversation->getIdentity()
                ));
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

//  public function uploadPhotoAction()
//  {
//    $viewer = Engine_Api::_()->user()->getViewer();
//
//    $this->_helper->layout->disableLayout();
//
//    if( !Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') ) {
//      return false;
//    }
//
//    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;
//
//    if( !$this->_helper->requireUser()->checkRequire() )
//    {
//      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
//      return;
//    }
//
//    if( !$this->getRequest()->isPost() )
//    {
//      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
//      return;
//    }
//    if( !isset($_FILES['userfile']) || !is_uploaded_file($_FILES['userfile']['tmp_name']) )
//    {
//      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
//      return;
//    }
//
//    $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
//    $db->beginTransaction();
//
//    try
//    {
//      $viewer = Engine_Api::_()->user()->getViewer();
//
//      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
//      $photo = $photoTable->createRow();
//      $photo->setFromArray(array(
//        'owner_type' => 'user',
//        'owner_id' => $viewer->getIdentity()
//      ));
//      $photo->save();
//
//      $photo->setPhoto($_FILES['userfile']);
//
//      $this->view->status = true;
//      $this->view->name = $_FILES['userfile']['name'];
//      $this->view->photo_id = $photo->photo_id;
//      $this->view->photo_url = $photo->getPhotoUrl();
//
//      $table = Engine_Api::_()->getDbtable('albums', 'album');
//      $album = $table->getSpecialAlbum($viewer, 'message');
//
//      $photo->album_id = $album->album_id;
//      $photo->save();
//
//      if( !$album->photo_id )
//      {
//        $album->photo_id = $photo->getIdentity();
//        $album->save();
//      }
//
//      $auth      = Engine_Api::_()->authorization()->context;
//      $auth->setAllowed($photo, 'everyone', 'view',    true);
//      $auth->setAllowed($photo, 'everyone', 'comment', true);
//      $auth->setAllowed($album, 'everyone', 'view',    true);
//      $auth->setAllowed($album, 'everyone', 'comment', true);
//
//
//      $db->commit();
//
//    } catch( Album_Model_Exception $e ) {
//      $db->rollBack();
//      $this->view->status = false;
//      $this->view->error = $this->view->translate($e->getMessage());
//      throw $e;
//      return;
//
//    } catch( Exception $e ) {
//      $db->rollBack();
//      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
//      throw $e;
//      return;
//    }
//  }
    /**
     * Set the uploaded photo from activity post.
     *
     * @return object
     */
    private function _setPhoto($photo, $subject) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Group_Model_Exception('invalid argument passed to setPhoto');
        }
        $fileName = $photo['name'];
        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $params = array(
            'parent_type' => $subject->getType(),
            'parent_id' => $subject->getIdentity(),
            'user_id' => $subject->owner_id,
            'name' => $fileName,
        );
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
// Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($mainPath)
                ->destroy();
// Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($normalPath)
                ->destroy();
// Store
        try {
            $iMain = $filesTable->createFile($mainPath, $params);
            $iIconNormal = $filesTable->createFile($normalPath, $params);
            $iMain->bridge($iIconNormal, 'thumb.normal');
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
// Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);
// Update row
        $subject->modified_date = date('Y-m-d H:i:s');
        $subject->file_id = $iMain->file_id;
        $subject->save();
        return $subject;
    }

    /**
     * Image url - returns information for image type url.
     *
     * @return array
     */
    protected function _previewImage($uri, Zend_Http_Response $response) {
        $imageCount = 1;
        $image = array();
        $image['images'] = array($uri);
        return $images;
    }

    /**
     * Text url - returns information for text type url.
     *
     * @return array
     */
    protected function _previewText($uri, Zend_Http_Response $response) {
        $body = $response->getBody();
        $text = array();
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
                preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)) {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
//    if( function_exists('mb_convert_encoding') ) {
//      $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
//    }
        // Reduce whitespace
        $text['body'] = $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);
        $text['title'] = $title = substr($body, 0, 63);
        $text['desciption'] = $description = substr($body, 0, 255);
        return $text;
    }

    /**
     * Text/html url - returns information for html/text type url.
     *
     * @return array
     */
    protected function _previewHtml($uri, Zend_Http_Response $response) {
        $body = $response->getBody();
        $html = array();
        $body = trim($body);
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
                preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)) {
            $charset = $charset = trim($matches[1]);
        } else {
            $charset = $charset = 'UTF-8';
        }
        if (function_exists('mb_convert_encoding')) {
            $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        }
        // Get DOM
        if (class_exists('DOMDocument')) {
            $dom = new Zend_Dom_Query($body);
        } else {
            $dom = null; // Maybe add b/c later
        }
        $title = null;
        if ($dom) {
            $titleList = $dom->query('title');
            if (count($titleList) > 0) {
                $title = trim($titleList->current()->textContent);
                $title = substr($title, 0, 255);
            }
        }
        $html['title'] = $title;
        $description = null;
        if ($dom) {
            $descriptionList = $dom->queryXpath("//meta[@name='description']");
            // Why are they using caps? -_-
            if (count($descriptionList) == 0) {
                $descriptionList = $dom->queryXpath("//meta[@name='Description']");
            }
            if (count($descriptionList) > 0) {
                $description = trim($descriptionList->current()->getAttribute('content'));
                $description = substr($description, 0, 255);
            }
        }
        $html['description'] = $description;
        $medium = null;
        if ($dom) {
            $mediumList = $dom->queryXpath("//meta[@name='medium']");
            if (count($mediumList) > 0) {
                $medium = $mediumList->current()->getAttribute('content');
            }
        }
        $medium = $medium;
        // Get baseUrl and baseHref to parse . paths
        $baseUrlInfo = parse_url($uri);
        $baseUrl = null;
        $baseHostUrl = null;
        $baseUrlScheme = $baseUrlInfo['scheme'];
        $baseUrlHost = $baseUrlInfo['host'];
        if ($dom) {
            $baseUrlList = $dom->query('base');
            if ($baseUrlList && count($baseUrlList) > 0 && $baseUrlList->current()->getAttribute('href')) {
                $baseUrl = $baseUrlList->current()->getAttribute('href');
                $baseUrlInfo = parse_url($baseUrl);
                if (!isset($baseUrlInfo['scheme']) || empty($baseUrlInfo['scheme'])) {
                    $baseUrlInfo['scheme'] = $baseUrlScheme;
                }
                if (!isset($baseUrlInfo['host']) || empty($baseUrlInfo['host'])) {
                    $baseUrlInfo['host'] = $baseUrlHost;
                }
                $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            }
        }
        if (!$baseUrl) {
            $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            if (empty($baseUrlInfo['path'])) {
                $baseUrl = $baseHostUrl;
            } else {
                $baseUrl = explode('/', $baseUrlInfo['path']);
                array_pop($baseUrl);
                $baseUrl = join('/', $baseUrl);
                $baseUrl = trim($baseUrl, '/');
                $baseUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . $baseUrl . '/';
            }
        }
        $images = array();
        if ($thumb) {
            $images[] = $thumb;
        }
        if ($dom) {
            $imageQuery = $dom->query('img');
            foreach ($imageQuery as $image) {
                $src = $image->getAttribute('src');
                // Ignore images that don't have a src
                if (!$src || false === ($srcInfo = @parse_url($src))) {
                    continue;
                }
                $ext = ltrim(strrchr($src, '.'), '.');
                // Detect absolute url
                if (strpos($src, '/') === 0) {
                    // If relative to root, add host
                    $src = $baseHostUrl . ltrim($src, '/');
                } else if (strpos($src, './') === 0) {
                    // If relative to current path, add baseUrl
                    $src = $baseUrl . substr($src, 2);
                } else if (!empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                    // Contians host and scheme, do nothing
                } else if (empty($srcInfo['scheme']) && empty($srcInfo['host'])) {
                    // if not contains scheme or host, add base
                    $src = $baseUrl . ltrim($src, '/');
                } else if (empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                    // if contains host, but not scheme, add scheme?
                    $src = $baseUrlInfo['scheme'] . ltrim($src, '/');
                } else {
                    // Just add base
                    $src = $baseUrl . ltrim($src, '/');
                }
                // Ignore images that don't come from the same domain
                //if( strpos($src, $srcInfo['host']) === false ) {
                // @todo should we do this? disabled for now
                //continue;
                //}
                // Ignore images that don't end in an image extension
                if (!in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                    // @todo should we do this? disabled for now
                    //continue;
                }
                if (!in_array($src, $images)) {
                    $images[] = $src;
                }
            }
        }
        // Unique
        $images = array_values(array_unique($images));
        // Truncate if greater than 20
        if (count($images) > 30) {
            array_splice($images, 30, count($images));
        }
        $html['imageCount'] = count($images);
        $html['images'] = $images;
        $thumb = null;
        if ($dom) {
            $thumbList = $dom->queryXpath("//link[@rel='image_src']");
            if (count($thumbList) > 0) {
                $thumb = $thumbList->current()->getAttribute('href');
            }
        }
        if (empty($thumb)) {
            $thumb = $images[0];
        }
        $html['thumb'] = $thumb;
        return $html;
    }

    /**
     * Info of URL - Common function called to get information about url.
     *
     * @return array
     */
    private function _getUrlInfo($uri, Zend_Http_Response $response) {
        // Get content-type
        list($contentType) = explode(';', $response->getHeader('content-type'));
        $link['contentType'] = $contentType;
        $link['url'] = $uri;
        // Prepare
        $title = null;
        $description = null;
        $thumb = null;
        $imageCount = 0;
        $images = array();

        // Handling based on content-type
        switch (strtolower($contentType)) {
            // Images
            case 'image/gif':
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/tif': // Might not work
            case 'image/xbm':
            case 'image/xpm':
            case 'image/png':
            case 'image/bmp': // Might not work
                $link = array_merge($link, $this->_previewImage($uri, $response));
                break;
            // HTML
            case '':
            case 'text/html':
                $link = array_merge($link, $this->_previewHtml($uri, $response));
                break;
            // Plain text
            case 'text/plain':
                $link = array_merge($link, $this->_previewText($uri, $response));
                break;
            // Unknown
            default:
                break;
        }
        return $link;
    }

    /**
     * Message Post Menus - Return an array of menus to post message. Which help to findout that which post-menu should be display to not.
     *
     * @return array
     */
    private function _getPostFeedOptions() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
// Throw error for logged-out user.
        if (empty($viewer_id)) {
//      $this->_forward('throw-error', 'feed', 'advancedactivity', array(
//          "error_code" => "unauthorized"
//      ));
            return;
        }
// Get the subject
        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject();
        else
            $subject = Engine_Api::_()->user()->getViewer();
// Check authorization permission
        if (!$subject->authorization()->isAllowed($viewer, 'comment')) {
//      $this->_forward('throw-error', 'feed', 'advancedactivity', array(
//          "error_code" => "unauthorized"
//      ));
            return;
        }

        $moduleEnabledByUs = array('album' => 'photo', 'video' => 'video', 'music' => 'music', 'core' => 'link');
        foreach ($moduleEnabledByUs as $modName => $key) {
            $activityPost[$key] = 1;
        }

        if (empty($activityPost['video'])) {
            $activityPost['video'] = (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) ? 1 : 0;
        }

        return $activityPost;
    }

    public function videoType($type) {
        switch ($type) {
            case 1:
            case 'youtube':
                return 1;
            case 2:
            case 'vimeo':
                return 2;
            case 3:
            case 'mydevice':
            case 'upload' :
                return 3;
            case 4:
            case 'dailymotion':
                return 4;
            case 5:
            case 'embedcode':
                return 5;
            case 6;
            case 'iframely':
                return 6;
            default : return $type;
        }
    }

}
