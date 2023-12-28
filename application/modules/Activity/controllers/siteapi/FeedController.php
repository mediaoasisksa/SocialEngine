<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FeedController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Activity_FeedController extends Siteapi_Controller_Action_Standard {

    /**
     * Init model
     *
     */
    public function init() {
        $subject_type = $this->getRequestParam('subject_type');
        if (0 !== ($subject_id = (int) $this->getRequestParam('subject_id')) &&
                null !== ($subject = Engine_Api::_()->getItem($subject_type, $subject_id)))
            Engine_Api::_()->core()->setSubject($subject);
    }

    /**
     * Get the activity feed for member home page.
     * 
     * @return array
     */
    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        $subject = null;
        if (($subject_type = $this->getRequestParam('subject_type', null)) && ($subject_id = $this->getRequestParam('subject_id', null))) {

            $subject = Engine_Api::_()->core()->getSubject();
            if (!empty($subject)) {
                if (!$subject->authorization()->isAllowed($viewer, 'view'))
                    $this->respondWithError('unauthorized');
            }
        }

        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // GET PARAMS
        $length = $this->getRequestParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
        $viewAllLikes = $this->getRequestParam('viewAllLikes', $this->getRequestParam('show_likes', false));
        $viewAllComments = $this->getRequestParam('viewAllComments', $this->getRequestParam('show_comments', false));
        $getUpdate = $this->getRequestParam('getUpdate');
        $checkUpdate = $this->getRequestParam('checkUpdate');
        $action_id = (int) $this->getRequestParam('action_id');
        $post_failed = (int) $this->getRequestParam('pf');
        $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);
        $siteapiActivityFeeds = Zend_Registry::isRegistered('siteapiActivityFeeds') ? Zend_Registry::get('siteapiActivityFeeds') : null;
        $updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
        $length = ($length > 50) ? 50 : $length;

        // Get config options for activity
        $config = array(
            'action_id' => (int) $this->getRequestParam('action_id'),
            'max_id' => (int) $this->getRequestParam('maxid'),
            'min_id' => (int) $this->getRequestParam('minid'),
            'limit' => (int) $length
        );

        if (empty($siteapiActivityFeeds))
            $this->respondWithError('unauthorized');

        $activityPostMenus = null;
        if ($this->getRequestParam('post_menus', true)) {
            $getPostMenus = $this->_getPostMenus();
            if (!empty($getPostMenus))
                $activityPostMenus = $getPostMenus;
        }

        // Pre-process feed items
        $selectCount = 0;
        $nextid = $firstid = null;
        $tmpConfig = $config;
        $itemActionCounts = $friendRequests = $activity = array();
        $endOfFeed = false;

        do {
            // Get current batch
            $actions = null;

            // Where the Activity Feed is Fetched
            if (!empty($subject)) {
                $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
            } else {
                $actions = $actionTable->getActivity($viewer, $tmpConfig);
            }
            $selectCount++;

            // Are we at the end?
            if (count($actions) < $length || count($actions) <= 0) {
                $endOfFeed = true;
            }

            // Pre-process
            if (count($actions) > 0) {
                foreach ($actions as $action) {
                    // get next id
                    if (null === $nextid || $action->action_id <= $nextid) {
                        $nextid = $action->action_id - 1;
                    }
                    // get first id
                    if (null === $firstid || $action->action_id > $firstid) {
                        $firstid = $action->action_id;
                    }
                    // skip disabled actions
                    if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled)
                        continue;
                    // skip items with missing items
                    if (!$action->getSubject() || !$action->getSubject()->getIdentity())
                        continue;
                    if (!$action->getObject() || !$action->getObject()->getIdentity())
                        continue;
                    // track/remove users who do too much (but only in the main feed)
                    if (empty($subject)) {
                        $actionSubject = $action->getSubject();
                        $actionObject = $action->getObject();
                        if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
                            $itemActionCounts[$actionSubject->getGuid()] = 1;
                        } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
                            continue;
                        } else {
                            $itemActionCounts[$actionSubject->getGuid()] ++;
                        }
                    }

                    // remove duplicate friend requests
                    if ($action->type == 'friends') {
                        $id = $action->subject_id . '_' . $action->object_id;
                        $rev_id = $action->object_id . '_' . $action->subject_id;
                        if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
                            continue;
                        } else {
                            $friendRequests[] = $id;
                            $friendRequests[] = $rev_id;
                        }
                    }
                    // remove items with disabled module attachments
                    try {
                        $attachments = $action->getAttachments();
                    } catch (Exception $e) {
                        // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
                        continue;
                    }

                    // add to list
                    if (count($activity) < $length) {
                        $activity[] = $action;
                        if (count($activity) == $length) {
                            $actions = array();
                        }
                    }
                }
            }

            $tmpConfig['max_id'] = $nextid;

            if (!empty($tmpConfig['action_id'])) {
                $actions = array();
            }
        } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);

        $getFeeds = Engine_Api::_()->getApi('Siteapi_Feed', 'activity')->getFeeds($activity, array(
            'action_id' => $action_id,
            'viewAllComments' => $viewAllComments,
            'viewAllLikes' => $viewAllLikes,
            'getUpdate' => $getUpdate
        ));

        // ACTIVITY FEED POST SHOULD BE SHOW TO API VIEWER
        $enableComposer = false;
        if ($viewer->getIdentity() && !$this->getRequestParam('action_id')) {
            if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))) {
                if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status')) {
                    $enableComposer = true;
                }
            } else if ($subject) {
                if (Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment')) {
                    $enableComposer = true;
                }
            }
        }

        // UPLOADE IMAGE IN ACTIVITY FEED POST SHOULD BE SHOW TO API VIEWER
        $allow_photo_uploade = (($enableComposer) && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("album")) && (Engine_Api::_()->authorization()->isAllowed("album", null, "create"))) ? true : false;

        // PREPARE SUCCESS RESPONSE
        $this->respondWithSuccess(array(
            "data" => $getFeeds,
            "maxid" => $nextid,
            "enable_composer" => $enableComposer,
            "enable_composer_photo" => $allow_photo_uploade,
            "feed_post_menu" => $activityPostMenus
        ));
    }

    /**
     * POST the activity feed.
     * 
     * @return array
     */
    public function postAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Get subject if necessary
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = null;
        if (($subject_type = $this->getRequestParam('subject_type', null)) && ($subject_id = $this->getRequestParam('subject_id', null)))
            $subject = Engine_Api::_()->core()->getSubject();

        // Use viewer as subject if no subject
        if (null === $subject)
            $subject = $viewer;

        // Check auth
        if (!$subject->authorization()->isAllowed($viewer, 'comment'))
            $this->respondWithError('unauthorized');

        if (!$this->getRequest()->isPost())
            $this->respondWithError('unauthorized');

        // Getting the POST values
        $postData = $_REQUEST;

        if (!isset($postData['body']) || empty($postData['body']))
            $this->respondWithError('validation_fail');

        $body = @$postData['body'];
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $postData['body'] = $body;

        // set up action variable
        $action = null;

        // Process
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            // Set photo, if exist.
            if (!empty($_FILES['photo'])) {
                $table = Engine_Api::_()->getDbtable('albums', 'album');
                $type = $this->getRequestParam('image_type', 'wall');
                if (empty($type))
                    $type = 'wall';

                $album = $table->getSpecialAlbum($viewer, $type);
                $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                $photo = $photoTable->createRow();
                $photo->owner_type = 'user';
                $photo->owner_id = $viewer->getIdentity();
                $photo->save();

                $photo = $this->_setPhoto($_FILES['photo'], $photo);

                $photo->order = $photo->photo_id;
                $photo->album_id = $album->album_id;
                $photo->save();

                if (!$album->photo_id) {
                    $album->photo_id = $photo->getIdentity();
                    $album->save();
                }

                if ($type != 'message') {
                    $auth = Engine_Api::_()->authorization()->context;
                    $auth->setAllowed($photo, 'everyone', 'view', true);
                    $auth->setAllowed($photo, 'everyone', 'comment', true);
                }

                $attachment = $photo;
            }

            // Get body
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);
            if (!$attachment && $viewer->isSelf($subject)) {
                if ($body != '') {
                    $viewer->status = $body;
                    $viewer->status_date = date('Y-m-d H:i:s');
                    $viewer->save();

                    $viewer->status()->setStatus($body);
                }

                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body);
            } else { // General post
                $type = 'post';
                if ($viewer->isSelf($subject)) {
                    $type = 'post_self';
                }

                // Add notification for <del>owner</del> user
                $subjectOwner = $subject->getOwner();

                if (!$viewer->isSelf($subject) &&
                        $subject instanceof User_Model_User) {
                    $notificationType = 'post_' . $subject->getType();
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                        'url1' => $subject->getHref(),
                    ));
                }

                // Add activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $type, $body);

                // Try to attach if necessary
                if ($action && $attachment) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
                }
            }

            // Preprocess attachment parameters
            $publishMessage = html_entity_decode($postData['body']);
            $publishUrl = null;
            $publishName = null;
            $publishDesc = null;
            $publishPicUrl = null;
            // Add attachment
            if ($attachment) {
                $publishUrl = $attachment->getHref();
                $publishName = $attachment->getTitle();
                $publishDesc = $attachment->getDescription();
                if (empty($publishName)) {
                    $publishName = ucwords($attachment->getShortType());
                }
                if (($tmpPicUrl = $attachment->getPhotoUrl())) {
                    $publishPicUrl = $tmpPicUrl;
                }
                // prevents OAuthException: (#100) FBCDN image is not allowed in stream
                if ($publishPicUrl &&
                        preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
                    $publishPicUrl = null;
                }
            } else {
                $publishUrl = !$action ? null : $action->getHref();
            }

            // Check to ensure proto/host
            if ($publishUrl &&
                    false === stripos($publishUrl, 'http://') &&
                    false === stripos($publishUrl, 'https://')) {
                $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
            }
            if ($publishPicUrl &&
                    false === stripos($publishPicUrl, 'http://') &&
                    false === stripos($publishPicUrl, 'https://')) {
                $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
            }
            // Add site title
            if ($publishName) {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                        . ": " . $publishName;
            } else {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
            }

            // Publish to facebook, if checked & enabled
            if ($this->getRequestParam('post_to_facebook', false) &&
                    'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
                try {

                    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                    $facebook = $facebookApi = $facebookTable->getApi();
                    $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

                    if ($fb_uid &&
                            $fb_uid->facebook_uid &&
                            $facebookApi &&
                            $facebookApi->getUser() &&
                            $facebookApi->getUser() == $fb_uid->facebook_uid) {
                        $fb_data = array(
                            'message' => $publishMessage,
                        );
                        if ($publishUrl) {
                            $fb_data['link'] = $publishUrl;
                        }
                        if ($publishName) {
                            $fb_data['name'] = $publishName;
                        }
                        if ($publishDesc) {
                            $fb_data['description'] = $publishDesc;
                        }
                        if ($publishPicUrl) {
                            $fb_data['picture'] = $publishPicUrl;
                        }
                        $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            } // end Facebook
            // Publish to twitter, if checked & enabled
            if ($this->getRequestParam('post_to_twitter', false) &&
                    'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
                try {
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    if ($twitterTable->isConnected()) {
                        // @todo truncation?
                        // @todo attachment
                        $twitter = $twitterTable->getApi();
                        $twitter->statuses->update($publishMessage);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            }

            // Publish to janrain
            if (//$this->getRequestParam('post_to_janrain', false) &&
                    'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
                try {
                    $session = new Zend_Session_Namespace('JanrainActivity');
                    $session->unsetAll();

                    $session->message = $publishMessage;
                    $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
                    $session->name = $publishName;
                    $session->desc = $publishDesc;
                    $session->picture = $publishPicUrl;
                } catch (Exception $e) {
                    // Silence
                }
            }

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Delete feeds
     *
     * @return void
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');


        // Identify if it's an action_id or comment_id being deleted
        $comment_id = (int) $this->getRequestParam('comment_id', null);
        $action_id = (int) $this->getRequestParam('action_id', null);

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
        if (empty($action))
            $this->respondWithError('no_record');

        // Both the author and the person being written about get to delete the action_id
        if (!$comment_id && (
                $activity_moderate ||
                ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
                ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id))) {   // commenter
            // Delete action item and all comments/likes
            $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
            $db->beginTransaction();
            try {
                $action->deleteItem();
                $db->commit();


                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        } elseif ($comment_id) {
            $comment = $action->comments()->getComment($comment_id);
            // allow delete if profile/entry owner
            $db = Engine_Api::_()->getDbtable('comments', 'activity')->getAdapter();
            $db->beginTransaction();
            if ($activity_moderate ||
                    ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
                    ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)) {
                try {
                    $action->comments()->removeComment($comment_id);
                    $db->commit();

                    $this->successResponseNoContent('no_content');
                } catch (Exception $e) {
                    $db->rollBack();
                    $this->respondWithValidationError('internal_server_error', $e->getMessage());
                }
            } else {
                $this->respondWithError('unauthorized');
            }
        } else {
            $this->respondWithError('unauthorized');
        }
    }

    /**
     * Like to the feed.
     * 
     * @return void
     */
    public function likeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Collect params
        $action_id = $this->getRequestParam('action_id');
        $comment_id = $this->getRequestParam('comment_id');
        $callingAction = $this->getRequestParam('calling_from', 'advancedactivity');
        $viewer = Engine_Api::_()->user()->getViewer();

        // Start transaction
        $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            if (($callingAction == 'advancedactivity') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
            } else {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
            }

            // Action
            if (!$comment_id) {

                // Check authorization
                if ($action && !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
                    $this->respondWithError('unauthorized');

                $action->likes()->addLike($viewer);

                // Add notification for owner of activity (if user and not viewer)
                if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                    $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);

                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
                        'label' => 'post'
                    ));
                }
            }
            // Comment
            else {
                $comment = $action->comments()->getComment($comment_id);

                // Check authorization
                if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment'))
                    $this->respondWithError('unauthorized');

                $comment->likes()->addLike($viewer);

                // @todo make sure notifications work right
                if ($comment->poster_id != $viewer->getIdentity()) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')
                            ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
                                'label' => 'comment'
                    ));
                }

                // Add notification for owner of activity (if user and not viewer)
                if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                    $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
                }
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        $this->successResponseNoContent('no_content');
    }

    /**
     * Unlike to the feed.
     * 
     * @return void
     */
    public function unlikeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Collect params
        $action_id = $this->getRequestParam('action_id');
        $comment_id = $this->getRequestParam('comment_id');
        $callingAction = $this->getRequestParam('calling_from', 'advancedactivity');
        $viewer = Engine_Api::_()->user()->getViewer();

        // Start transaction
        $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            if (($callingAction == 'advancedactivity') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
            } else {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
            }

            // Action
            if (!$comment_id) {
                // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
                    $this->respondWithError('unauthorized');

                $action->likes()->removeLike($viewer);
            }

            // Comment
            else {
                $comment = $action->comments()->getComment($comment_id);

                // Check authorization
                if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment'))
                    $this->respondWithError('unauthorized');

                $comment->likes()->removeLike($viewer);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        $this->successResponseNoContent('no_content');
    }

    /**
     * Comment on the feed.
     * 
     * @return void
     */
    public function commentAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $callingAction = $this->getRequestParam('calling_from', 'advancedactivity');
        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $action_id = $this->getRequestParam('action_id', $this->getRequestParam('action', null));

            if (($callingAction == 'advancedactivity') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
            } else {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
            }

            if (!$action)
                $this->respondWithError('unauthorized');

            $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);

            // Getting the POST values
            $postData = $_REQUEST;

            $body = $postData['body'];

            // Check authorization
            if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
                $this->respondWithError('unauthorized');

            // Add the comment
            $comment = $action->comments()->addComment($viewer, $body);

            // Notifications
            $notifyApi = Engine_Api::_()->getApi('Siteapi_Core', 'activity');

            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
                    'label' => 'post'
                ));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
                        'label' => 'post'
                    ));
                }
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            foreach ($action->likes()->getAllLikesUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
                        'label' => 'post'
                    ));
                }
            }

            $canComment = Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment');
            $canDelete = Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'edit');

            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');
            $commentInfo = array();
            if (!empty($comment)) {
                $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
                $commentInfo["action_id"] = $action_id;
                $commentInfo["comment_id"] = $comment->comment_id;

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, false, 'author');
                $commentInfo = array_merge($commentInfo, $getContentImages);

                $commentInfo["author_title"] = $poster->getTitle();
                $commentInfo["comment_body"] = $comment->body;
                $commentInfo["comment_date"] = $comment->creation_date;

                if (!empty($canDelete) || $poster->isSelf($viewer)) {
                    $commentInfo["delete"] = array(
                        "name" => "delete",
                        "label" => $this->translate("Delete"),
                        "url" => "comment-delete",
                        'urlParams' => array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->comment_id
                        )
                    );
                } else {
                    $commentInfo["delete"] = null;
                }

                if (!empty($canComment)) {
                    $isLiked = $comment->likes()->isLike($viewer);
                    if (empty($isLiked)) {
                        $likeInfo["name"] = "like";
                        $likeInfo["label"] = $this->translate("Like");
                        $likeInfo["url"] = "like";
                        $likeInfo["urlParams"] = array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->getIdentity()
                        );
                    } else {
                        $likeInfo["name"] = "unlike";
                        $likeInfo["label"] = $this->translate("Unlike");
                        $likeInfo["url"] = "unlike";
                        $likeInfo["urlParams"] = array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->getIdentity()
                        );
                    }

                    $commentInfo["like"] = $likeInfo;
                } else {
                    $commentInfo["like"] = null;
                }
                $db->commit();
                $this->respondWithSuccess($commentInfo);
            } else {
                $this->respondWithValidationError('internal_server_error');
            }
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Get the complete information of likes and comment respective of action_id OR comment_id.
     * 
     * @return array
     */
    public function likesCommentsAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $action_id = $this->getRequestParam('action_id');
        $comment_id = $this->getRequestParam('comment_id', null);
        $page = $this->getRequestParam('page', null);
        $limit = $this->getRequestParam('limit', null);
        $callingAction = $this->getRequestParam('calling_from', 'advancedactivity');
        $viewer = Engine_Api::_()->user()->getViewer();
        $bodyParams = $likeUsersArray = array();

        if (empty($action_id)) {
            $this->respondWithValidationError('parameter_missing', 'action_id');
        }

        if (($callingAction == 'advancedactivity') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
        } else {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
        }

        if (!empty($comment_id)) {
            $comment = $action->comments()->getComment($comment_id);
            $getAllLikesUsers = $comment->likes()->getAllLikesUsers();
            $likes = $comment->likes()->getLikePaginator();
        } else {
            $getAllLikesUsers = $action->likes()->getAllLikesUsers();
            $likes = $action->likes()->getLikePaginator();
        }

        $isLike = Engine_Api::_()->getDbtable('likes', 'activity')->isLike($action, $viewer);
        $viewAllLikes = $this->getRequestParam('viewAllLikes');
        if (!empty($viewAllLikes)) {
            foreach ($getAllLikesUsers as $user) {
                $tempUserArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                $tempUserArray = array_merge($tempUserArray, $getContentImages);

                $likeUsersArray[] = $tempUserArray;
            }
            $bodyParams['viewAllLikesBy'] = $likeUsersArray;
        }
        $canComment = $action->authorization()->isAllowed($viewer, 'comment');
        $canDelete = $action->authorization()->isAllowed($viewer, 'edit');

        // If has a page, display oldest to newest
        if (null !== $page) {
            $commentSelect = $action->comments()->getCommentSelect();
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page);
            $comments->setItemCountPerPage($limit);
        } else {
            // If not has a page, show the
            $commentSelect = $action->comments()->getCommentSelect();
            $commentSelect->order('comment_id DESC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
        }

        // Hide if can't post and no comments
        if (!$canComment && !$canDelete && count($comments) <= 0 && count($likes) <= 0)
            $this->respondWithError('unauthorized');

        $getTotalCommentCount = $comments->getTotalItemCount();

        $viewAllComments = $this->getRequestParam('viewAllComments');
        if (!empty($viewAllComments)) {
            // Iterate over the comments backwards (or forwards!)
            $comments = $comments->getIterator();
            if ($page) {
                $i = 0;
                $l = count($comments) - 1;
                $d = 1;
                $e = $l + 1;
            } else {
                $i = count($comments) - 1;
                $l = count($comments);
                $d = -1;
                $e = -1;
            }

            for (; $i != $e; $i += $d) {
                $comment = $comments[$i];
                $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
                $commentInfo["action_id"] = $action_id;
                $commentInfo["comment_id"] = $comment->comment_id;

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, false, 'author');
                $commentInfo = array_merge($commentInfo, $getContentImages);

                $commentInfo["author_title"] = $poster->getTitle();
                $commentInfo["comment_body"] = $comment->body;
                $commentInfo["comment_date"] = $comment->creation_date;

                if (!empty($canDelete) || $poster->isSelf($viewer)) {
                    $commentInfo["delete"] = array(
                        "name" => "delete",
                        "label" => $this->translate("Delete"),
                        "url" => "comment-delete",
                        'urlParams' => array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->comment_id
                        )
                    );
                } else {
                    $commentInfo["delete"] = null;
                }

                if (!empty($canComment)) {
                    $isLiked = $comment->likes()->isLike($viewer);
                    if (empty($isLiked)) {
                        $likeInfo["name"] = "like";
                        $likeInfo["label"] = $this->translate("Like");
                        $likeInfo["url"] = "like";
                        $likeInfo['urlParams'] = array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->getIdentity()
                        );

                        $likeInfo["isLike"] = 0;
                    } else {
                        $likeInfo["name"] = "unlike";
                        $likeInfo["label"] = $this->translate("Unlike");
                        $likeInfo["url"] = "unlike";
                        $likeInfo['urlParams'] = array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->getIdentity()
                        );
                        $likeInfo["isLike"] = 1;
                    }

                    $commentInfo["like"] = $likeInfo;
                } else {
                    $commentInfo["like"] = null;
                }

                $allComments[] = $commentInfo;
            }

            $bodyParams['viewAllComments'] = $allComments;
        }

        // FOLLOWING ARE THE GENRAL INFORMATION OF THE PLUGIN, WHICH WILL RETURN IN EVERY CALLING.
        $bodyParams['isLike'] = !empty($isLike) ? 1 : 0;
        $bodyParams['canComment'] = $canComment;
        $bodyParams['canDelete'] = $canDelete;
        $bodyParams['getTotalComments'] = $getTotalCommentCount;
        $bodyParams['getTotalLikes'] = $likes->getTotalItemCount();

        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Tags to the people
     * 
     * @return array
     */
    public function tagFriendAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $id = $this->getRequestParam('action_id', null);
        if (empty($id))
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $action = Engine_Api::_()->getItem('activity_action', $id);

        if ('user' !== $action->subject_type)
            $this->respondWithError('unauthorized');

        $actionTag = new Engine_ProxyObject($action, Engine_Api::_()->getDbtable('tags', 'core'));
        $tempUsers = $this->getRequestParam('users');
        $usersStr = (isset($tempUsers) && !empty($tempUsers)) ? $this->getRequestParam('users') : $this->getRequestParam('values');
        $user_ids = array_values(array_unique(explode(",", $usersStr)));

        $users = array();
        if (!empty($user_ids)) {
            $users = Engine_Api::_()->getItemMulti('user', $user_ids);
        }

        $tagsAdded = $actionTag->setTagMaps($action->getSubject(), $users);

        // Add notification
        $type_name = str_replace('_', ' ', 'post');
        if (is_array($type_name)) {
            $type_name = $type_name[0];
        } else {
            $type_name = 'post';
        }

        $params = (array) $action->params;
        if (!(is_array($params) && isset($params['checkin']))) {
            foreach ($tagsAdded as $value) {
                $tag = Engine_Api::_()->getItem($value->tag_type, $value->tag_id);
                if (($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification(
                            $tag, $action->getSubject(), $action, 'tagged', array(
                        'object_type_name' => $type_name,
                        'label' => $type_name,
                            )
                    );
                }
            }
        }

        $this->successResponseNoContent('no_content');
    }

    /**
     * Get the available activity feed menus.
     * 
     * @return array
     */
    private function _getPostMenus() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $activityPost = array();

        $subject = null;
        if (($subject_type = $this->getRequestParam('subject_type', null)) && ($subject_id = $this->getRequestParam('subject_id', null)))
             $subject = Engine_Api::_()->core()->getSubject();

        // Use viewer as subject if no subject
        if (null === $subject) {
            $subject = $viewer;
        }

        if (empty($viewer_id) || !$subject->authorization()->isAllowed($viewer, 'comment'))
            return;

        $activityPost['status'] = 1;
        $activityPost['emotions'] = 1;

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("album"))
            $activityPost['photo'] = 1;

        return $activityPost;
    }

    /**
     * Save photo and send activity feed accordingly.
     * 
     * @return array
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

        // Save
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
            // Remove temp files
            @unlink($mainPath);
            @unlink($normalPath);
            // Throw
            if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                throw new Album_Model_Exception($e->getMessage(), $e->getCode());
            } else {
                throw $e;
            }
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

}
