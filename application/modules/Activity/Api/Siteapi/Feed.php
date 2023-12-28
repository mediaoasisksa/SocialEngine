<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Feed.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Activity_Api_Siteapi_Feed extends Core_Api_Abstract {

    /**
     * Activity template parsing
     * 
     * @param object $action: Getting the activity feed object.
     * @param array $data: Extra information, which you want to pass.
     * @return array
     */
    public function getFeeds($actions = null, array $data = array()) {
        if (null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract))) {
            return '';
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $activity_moderate = "";
        $group_owner = "";
        $group = "";
        try {
            $group = Engine_Api::_()->core()->getSubject('group');
        } catch (Exception $e) {
            
        }

        if ($group) {
            $table = Engine_Api::_()->getDbtable('groups', 'group');
            $select = $table->select()
                    ->where('group_id = ?', $group->getIdentity())
                    ->limit(1);

            $row = $table->fetchRow($select);
            $group_owner = $row['user_id'];
        }

        if ($viewer->getIdentity()) {
            $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
        }

        $data = array_merge($data, array(
            'actions' => $actions,
            'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
            'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
            'activity_group' => $group_owner,
            'activity_moderate' => $activity_moderate,
        ));


        $getActivityArray = $this->_getActivityArray($data);
        return $getActivityArray;
    }

    /**
     * Assembles action string
     * 
     * @return string
     */
    public function getContent($action) {
        $params = array_merge(
                $action->toArray(), (array) $action->params, array(
            'subject' => $action->getSubject(),
            'object' => $action->getObject()
                )
        );

        $content = $this->assemble($action->getTypeInfo()->body, $params);
        return $content;
    }

    /**
     * Gets the plugin loader
     * 
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader() {
        if (null === $this->_pluginLoader) {
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR
                    . 'modules' . DIRECTORY_SEPARATOR
                    . 'Activity';
            $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
                'Activity_Model_Helper_' => $path . '/Model/Helper/'
            ));
        }

        return $this->_pluginLoader;
    }

    /**
     * Get a helper
     * 
     * @param string $name
     * @return Activity_Model_Helper_Abstract
     */
    public function getHelper($name) {
        $name = $this->_normalizeHelperName($name);
        if (!isset($this->_helpers[$name])) {
            $helper = $this->getPluginLoader()->load($name);
            $this->_helpers[$name] = new $helper;
        }

        return $this->_helpers[$name];
    }

    /**
     * Activity template parsing
     * 
     * @param string $body
     * @param array $params
     * @return string
     */
    public function assemble($body, array $params = array()) {
        $body = Engine_Api::_()->getApi('Core', 'siteapi')->translate($body);

        // By pass for un supported modules.
        $getDefaultAPPModules = DEFAULT_APP_MODULES;
        if (!empty($getDefaultAPPModules)) {
            $getDefaultAPPModuleArray = @explode(",", DEFAULT_APP_MODULES);
            if (!empty($params['object']) && is_object($params['object'])) {
                $moduleName = $params['object']->getModuleName();
                $moduleName = strtolower($moduleName);
                if (!in_array($moduleName, $getDefaultAPPModuleArray))
                    return $body;
            }
        }

        // Do other stuff
        preg_match_all('~\{([^{}]+)\}~', $body, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $tag = $match[0];
            $args = explode(':', $match[1]);
            $helper = array_shift($args);

            $helperArgs = array();
            foreach ($args as $arg) {
                if (substr($arg, 0, 1) === '$') {
                    $arg = substr($arg, 1);
                    $helperArgs[] = ( isset($params[$arg]) ? $params[$arg] : null );
                } else {
                    $helperArgs[] = $arg;
                }
            }

            try {
                $helper = $this->getHelper($helper);
                $r = new ReflectionMethod($helper, 'direct');
                $content = $r->invokeArgs($helper, $helperArgs);
            } catch (Exception $ex) {
                return $body;
            }

            $content = preg_replace('/\$(\d)/', '\\\\$\1', $content);
            $body = preg_replace("/" . preg_quote($tag) . "/", $content, $body, 1);
        }

        $body = strip_tags($body);

        return $body;
    }

    /**
     * Normalize helper name
     * 
     * @param string $name
     * @return string
     */
    protected function _normalizeHelperName($name) {
        $name = preg_replace('/[^A-Za-z0-9]/', '', $name);
        $name = ucfirst($name);
        return $name;
    }

    /**
     * Get activity array
     *
     * @param array $data: Get activity information array
     * @return array
     */
    private function _getActivityArray($data) {
        if (empty($data['actions'])) {
            return Engine_Api::_()->getApi('Core', 'siteapi')->translate("The action you are looking for does not exist.");
        } else {
            $actions = $data['actions'];
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();

        foreach ($actions as $action) {
            try {
                // prevents a bad feed item from destroying the entire page
                // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
                if (!$action->getTypeInfo()->enabled)
                    continue;
                if (!$action->getSubject() || !$action->getSubject()->getIdentity())
                    continue;
                if (!$action->getObject() || !$action->getObject()->getIdentity())
                    continue;

                $activityMenu = $activityFeedArray = array();

                // PREPARE THE FEED ARRAY
                $activityFeedArray['feed'] = $action->toArray();
                $activityFeedArray['feed']['main_content'] = $this->getContent($action);  //$action->getContent();        
                $activityFeedArray['feed']['time_value'] = $action->getTimeValue();
                $activityFeedArray['feed']['subject'] = $action->getSubject()->toArray();
                $activityFeedArray['feed']['subject'] = Engine_Api::_()->getApi('Core', 'siteapi')->sanitizeArray($activityFeedArray['feed']['subject'], array('email'));
                $getSubjectModName = $action->getSubject()->getModuleName();
                $activityFeedArray['feed']['subject']['name'] = (!empty($getSubjectModName)) ? strtolower($getSubjectModName) : '';
//                $activityFeedArray['feed']['subject']["url"] = $getHost . $action->getSubject()->getHref();
                $activityFeedArray['feed']['subject']["image"] = ($action->getSubject()->getPhotoUrl('thumb.main')) ? $getHost . $action->getSubject()->getPhotoUrl('thumb.main') : '';
                $activityFeedArray['feed']['subject']["image_icon"] = ($action->getSubject()->getPhotoUrl('thumb.icon')) ? $getHost . $action->getSubject()->getPhotoUrl('thumb.icon') : '';
                $activityFeedArray['feed']['subject']["image_profile"] = ($action->getSubject()->getPhotoUrl('thumb.profile')) ? $getHost . $action->getSubject()->getPhotoUrl('thumb.profile') : '';
                $activityFeedArray['feed']['subject']["image_normal"] = ($action->getSubject()->getPhotoUrl('thumb.normal')) ? $getHost . $action->getSubject()->getPhotoUrl('thumb.normal') : '';
                $activityFeedArray['feed']['subject']["owner_url"] = $getHost . $action->getSubject()->getOwner()->getHref();
                $activityFeedArray['feed']['subject']["owner_title"] = $action->getSubject()->getOwner()->getTitle();

                // PREPARE FOR FEED ATTACHMENT
                if ($action->getTypeInfo()->attachable && $action->attachment_count > 0) {
                    // WE ARE NOT IMPLEMENTIMNG THE GETRICHCONTENT FOR API NOW. IF ANY API BELONGS TO GETRICHCONTENT THEN IT WILL BE SKIP AND PROCESS WILL PROCEED TO NEXT ACTIVITY FEED.
//          if ( count( $action->getAttachments() ) == 1 &&
//                  null != ( $richContent = current( $action->getAttachments() )->item->getRichContent()) ) {

                    if (!$action->getAttachments()) {
                        // IN CASE OF 1 ATTACHMENT OR GETRICHCONTENT CASE, WE ARE NOT USING GETRICHCONTENT AND USING THE DEFAULT ATTACHEMENT MENTHODS.
                    } else {
                        $attachmentArray = array();
                        $attachedImageCount = $imageCount = 0;

                        foreach ($action->getAttachments() as $attachment) {
                            if ($attachment->meta->mode == 0) {
                                
                            } elseif (($attachment->meta->mode == 1) || ($attachment->meta->mode == 2)) {
                                $tempAttachmentArray = array();
                                if ($attachment->meta->mode == 1)
                                    $tempAttachmentArray = $attachment->item->toArray();

                                $tempAttachmentArray["attachment_type"] = $attachment->item->getType();
//                if ( strpos( $attachment->meta->type, '_photo' ) ) {
                                $tempAttachmentArray['likes_count'] = $attachment->item->likes()->getLikeCount();
                                $tempAttachmentArray['is_like'] = ($attachment->item->likes()->isLike($viewer)) ? 1 : 0;
                                $tempAttachmentArray['menu'] = array();
                                if (empty($attachedImageCount))
                                    $attachedImageCount = @count($action->getAttachments());

                                if ($attachment->item->getPhotoUrl()) {
                                    $imageCount++;
                                    $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.main');
                                    $getimagesize = @getimagesize($imageUrl);
                                    if (!empty($getimagesize)) {
                                        $tempAttachmentArray["image_main"] = array(
                                            "src" => $imageUrl,
                                            "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                        );
                                    }

                                    $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.icon');
                                    $getimagesize = @getimagesize($imageUrl);
                                    if (!empty($getimagesize)) {
                                        $tempAttachmentArray["image_icon"] = array(
                                            "src" => $imageUrl,
                                            "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                        );
                                    }

                                    $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.profile');
                                    $getimagesize = @getimagesize($imageUrl);
                                    if (!empty($getimagesize)) {
                                        $tempAttachmentArray["image_profile"] = array(
                                            "src" => $imageUrl,
                                            "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                        );
                                    }

                                    $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.normal');
                                    $getimagesize = @getimagesize($imageUrl);
                                    if (!empty($getimagesize)) {
                                        $tempAttachmentArray["image_normal"] = array(
                                            "src" => $imageUrl,
                                            "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                        );
                                    }

                                    $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.medium');
                                    $getimagesize = @getimagesize($imageUrl);
                                    if (!empty($getimagesize)) {
                                        $tempAttachmentArray["image_medium"] = array(
                                            "src" => $imageUrl,
                                            "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                        );
                                    }
                                } else if (strpos($attachment->meta->type, '_playlist')) {
                                    $tempAttachmentArray["attachment_type"] = "music";
                                    $tempAttachmentArray['likes_count'] = $attachment->item->likes()->getLikeCount();
                                    $tempAttachmentArray['is_like'] = ($attachment->item->likes()->isLike($viewer)) ? 1 : 0;
                                    $tempAttachmentArray['menu'] = array();
                                    $attachedFlag++;
                                } else if (strpos($attachment->meta->type, 'video')) {
                                    $tempAttachmentArray["attachment_type"] = "video";
                                    try {
                                        $tempAttachmentArray['likes_count'] = $attachment->item->likes()->getLikeCount();
                                        $tempAttachmentArray['is_like'] = ($attachment->item->likes()->isLike($viewer)) ? 1 : 0;
                                    } catch (Exception $ex) {
                                        $tempAttachmentArray['likes_count'] = 0;
                                        $tempAttachmentArray['is_like'] = 0;
                                    }
                                    $tempAttachmentArray['menu'] = array();
                                    $attachedFlag++;
                                }
                            } elseif ($attachment->meta->mode == 3) { // Description Type Only
                                $tempAttachmentArray["description"] = $attachment->item->getDescription();
                            } else if ($attachment->meta->mode == 4) {
                                
                            }
                            if (($attachment->meta->mode == 1) && ($activityFeedArray['feed']['type'] == 'share') && $tempAttachmentArray['attachment_type'] = 'activity_action') {
                                try {
                                    // Add uri in case of share feed
                                    if ($attachment->item->getHref())
                                        $tempAttachmentArray['uri'] = $tempHost . $attachment->item->getHref();
                                } catch (Exception $ex) {
                                    // Blank Exception
                                }
                            }
                            $tempAttachmentArray['mode'] = $attachment->meta->mode;
                            $attachmentArray[] = $tempAttachmentArray;
                        }
                        $activityFeedArray['feed']['attachment'] = $attachmentArray;
                        $activityFeedArray['feed']['photo_attachment_count'] = !empty($imageCount) ? $imageCount : 0;
                    }
                }

                // FEED COMMENT AND LIKE INFORMATION
                $canComment = ($action->getTypeInfo()->commentable &&
                        $viewer->getIdentity() &&
                        Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'));
                $activityFeedArray['can_comment'] = !empty($canComment) ? $canComment : 0;

                if (!empty($canComment)) {
                    $isLike = $action->likes()->isLike($viewer);
                    $activityFeedArray['is_like'] = !empty($isLike) ? 1 : 0;

                    if (empty($isLike)) {
                        $activityMenu["like"]["name"] = "like";
                        $activityMenu["like"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Like");
                        $activityMenu["like"]["url"] = "like";
                        $activityMenu["like"]['urlParams'] = array(
                            "action_id" => $action->action_id
                        );
                    } else {
                        $activityMenu["like"]["name"] = "unlike";
                        $activityMenu["like"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Unlike");
                        $activityMenu["like"]["url"] = "unlike";
                        $activityMenu["like"]['urlParams'] = array(
                            "action_id" => $action->action_id
                        );
                    }
                }


                $activityFeedArray['can_delete'] = $canDelete = ($viewer->getIdentity() && ($data["activity_moderate"] || (($viewer->getIdentity() == $data["activity_group"]) || ($data["allow_delete"] && (('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)))))) ? 1 : 0;
                if (!empty($canDelete)) {
                    $activityMenu["delete"]["name"] = "delete";
                    $activityMenu["delete"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Delete");
                    $activityMenu["delete"]["url"] = "activity-feed/delete";
                    $activityMenu["delete"]['urlParams'] = array(
                        "action_id" => $action->action_id
                    );
                }


                $activityFeedArray['can_share'] = $isShareable = ($action->getTypeInfo()->shareable && $viewer->getIdentity()) ? 1 : 0;
                if (!empty($isShareable)) {
                    if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())) {
                        $activityMenu["share"]["name"] = "share";
                        $activityMenu["share"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Share");
                        $activityMenu["share"]["url"] = "activity/share";
                        $activityMenu["share"]['urlParams'] = array(
                            "type" => $attachment->item->getType(),
                            "id" => $attachment->item->getIdentity()
                        );
                    } else if ($action->getTypeInfo()->shareable == 2) {
                        $activityMenu["share"]["name"] = "share";
                        $activityMenu["share"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Share");
                        $activityMenu["share"]["url"] = "activity/share";
                        $activityMenu["share"]['urlParams'] = array(
                            "type" => $subject->getType(),
                            "id" => $subject->getIdentity()
                        );
                    } elseif ($action->getTypeInfo()->shareable == 3) {
                        $activityMenu["share"]["name"] = "share";
                        $activityMenu["share"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Share");
                        $activityMenu["share"]["url"] = "activity/share";
                        $activityMenu["share"]['urlParams'] = array(
                            "type" => $object->getType(),
                            "id" => $object->getIdentity()
                        );
                    } else if ($action->getTypeInfo()->shareable == 4) {
                        $activityMenu["share"]["name"] = "share";
                        $activityMenu["share"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Share");
                        $activityMenu["share"]["url"] = "activity/share";
                        $activityMenu["share"]['urlParams'] = array(
                            "type" => $action->getType(),
                            "id" => $action->getIdentity()
                        );
                    }
                }

                $activityFeedArray['feed_menus'] = $activityMenu;
//        $getInitView = Engine_Api::_()->getApi('Core', 'siteapi')->getInitView();
//        $view = $getInitView["Zend_View"];
//        $local = $getInitView["Local"];


                if (!empty($data["viewAllLikes"])) {
                    $activityFeedArray['feed_likes_count'] = $action->likes()->getLikeCount();

                    // ALLOW TO VIEW ALL LIKE TILL 3 LIKES.
                    if ($action->likes()->getLikeCount() <= 3) {
                        $getAllLikesUsers = $action->likes()->getAllLikesUsers();
                        foreach ($getAllLikesUsers as $getUser) {
                            $tempLikedUserTitleArray['user_id'] = $getUser->getIdentity();
                            $tempLikedUserTitleArray['title'] = $getUser->getTitle();
                            $getLikedUserTitleArray[] = $tempLikedUserTitleArray;
                        }
                        $activityFeedArray['view_feed_likes'] = $getLikedUserTitleArray;
                    }
                }


                if (!empty($data["viewAllComments"])) {
                    $activityFeedArray['feed_comments_count'] = $action->comments()->getCommentCount();

                    $comments = $action->getComments(true);
                    $commentLikes = $action->getCommentsLikes($comments, $viewer);

                    foreach ($comments as $comment) {
                        $tempViewComment = $comment->toArray();
                        $posterObj = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
                        if (!empty($posterObj)) {
                            $tempViewComment['poster'] = $posterObj->toArray();
                            $tempViewComment['poster'] = Engine_Api::_()->getApi('Core', 'siteapi')->sanitizeArray($tempViewComment['poster']);
                            $tempViewComment['poster']["url"] = $getHost . $posterObj->getHref();
                            $tempViewComment['poster']["image"] = ($posterObj->getPhotoUrl('thumb.main')) ? $getHost . $posterObj->getPhotoUrl('thumb.main') : '';
                            $tempViewComment['poster']["image_icon"] = ($posterObj->getPhotoUrl('thumb.icon')) ? $getHost . $posterObj->getPhotoUrl('thumb.icon') : '';
                            $tempViewComment['poster']["image_profile"] = ($posterObj->getPhotoUrl('thumb.profile')) ? $getHost . $posterObj->getPhotoUrl('thumb.profile') : '';
                            $tempViewComment['poster']["image_normal"] = ($posterObj->getPhotoUrl('thumb.normal')) ? $getHost . $posterObj->getPhotoUrl('thumb.normal') : '';
                            $tempViewComment['poster']["owner_url"] = $getHost . $posterObj->getOwner()->getHref();
                            $tempViewComment['poster']["owner_title"] = $posterObj->getOwner()->getTitle();
                        }

                        if (isset($tempViewComment['poster']["creation_ip"]) && !empty($tempViewComment['poster']["creation_ip"]))
                            unset($tempViewComment['poster']["creation_ip"]);

                        if (isset($tempViewComment['poster']["lastlogin_ip"]) && !empty($tempViewComment['poster']["lastlogin_ip"]))
                            unset($tempViewComment['poster']["lastlogin_ip"]);

                        $tempViewComment['can_comment'] = $canComment;
                        $tempViewComment['can_delete'] = $canDelete = ($viewer->getIdentity() &&
                                (('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) ||
                                ($viewer->getIdentity() == $comment->poster_id)) ) ? 1 : 0;
                        if (!empty($canDelete)) {
                            $activityCommentMenu["delete"]["name"] = "delete";
                            $activityCommentMenu["delete"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Delete");
                            $activityCommentMenu["delete"]["url"] = "activity-feed/delete";
                            $activityCommentMenu["delete"]['urlParams'] = array(
                                "action_id" => $action->action_id,
                                "comment_id" => $comment->comment_id
                            );
                        }

                        if (!empty($canComment)) {
                            $isLike = !empty($commentLikes[$comment->comment_id]);
                            $tempViewComment['is_like'] = !empty($isLike) ? 1 : 0;
                            $tempViewComment['commentfeed_like_count'] = $comment->likes()->getLikeCount();

                            if (empty($isLike)) {
                                $activityCommentMenu["like"]["name"] = "like";
                                $activityCommentMenu["like"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Like");
                                $activityCommentMenu["like"]["url"] = "like";
                                $activityCommentMenu["like"]['urlParams'] = array(
                                    "action_id" => $action->action_id,
                                    "comment_id" => $comment->comment_id
                                );
                            } else {
                                $activityCommentMenu["like"]["name"] = "unlike";
                                $activityCommentMenu["like"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Unlike");
                                $activityCommentMenu["like"]["url"] = "unlike";
                                $activityCommentMenu["like"]['urlParams'] = array(
                                    "action_id" => $action->action_id,
                                    "comment_id" => $comment->comment_id
                                );
                            }
                        }

                        $tempViewComment["commentfeed_menu"] = $activityCommentMenu;
                        $tempFeedComments[] = $tempViewComment;
                    }

                    $activityFeedArray['view_feed_comments'] = $tempFeedComments;
                }
            } catch (Exception $e) {
                
            }

            if (!empty($activityFeedArray))
                $activityFeed[] = $activityFeedArray;
        }

        return $activityFeed;
    }

}
