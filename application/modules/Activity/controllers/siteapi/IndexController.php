<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    indexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Activity_IndexController extends Siteapi_Controller_Action_Standard {

    protected $_helpers;

    public function init() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerId = $viewer->getIdentity();
        if (empty($viewerId)) {
            $this->_forward('throw-error', 'index', 'activity', array(
                "error_code" => "unauthorized"
            ));
            return;
        }
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
            $helper = $this->_getPluginLoader()->load($name);
            $this->_helpers[$name] = new $helper;
        }

        return $this->_helpers[$name];
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
     * Share a content
     *
     * @return void
     */
    public function shareAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (null === ($type = $this->getRequestParam('type')))
            $this->respondWithValidationError('parameter_missing', 'type');

        if (null === ($id = $this->getRequestParam('id')))
            $this->respondWithValidationError('parameter_missing', 'id');

        if (null === ($body = $this->getRequestParam('body')))
            $this->respondWithValidationError('parameter_missing', 'body');

        $attachment = Engine_Api::_()->getItem($type, $id);
        if (!$attachment)
            $this->respondWithError('no_record');

        // Process
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();
        try {
            // Set Params for Attachment
            $params = array(
                'type' => '<a href="' . $attachment->getHref() . '">' . $attachment->getMediaType() . '</a>',
            );

            // Add activity
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
            $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
            if ($action) {
                $api->attachActivity($action, $attachment);
            }
            $db->commit();

            // Notifications
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
                    'label' => $attachment->getMediaType(),
                ));
            }

            // Preprocess attachment parameters
            $publishMessage = html_entity_decode($body);
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
                if ($publishPicUrl && preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
                    $publishPicUrl = null;
                }
            } else {
                $publishUrl = $action->getHref();
            }


            // Check to ensure proto/host
            if ($publishUrl && false === stripos($publishUrl, 'http://') && false === stripos($publishUrl, 'https://')) {
                $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
            }
            if ($publishPicUrl && false === stripos($publishPicUrl, 'http://') && false === stripos($publishPicUrl, 'https://')) {
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
            if ($this->getRequestParam('post_to_facebook') && 'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
                try {
                    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                    $facebookApi = $facebook = $facebookTable->getApi();
                    $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

                    if ($fb_uid && $fb_uid->facebook_uid && $facebookApi && $facebookApi->getUser() && $facebookApi->getUser() == $fb_uid->facebook_uid) {
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
            if ($this->getRequestParam('post_to_twitter') && 'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
                try {
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    if ($twitterTable->isConnected()) {

                        // Get attachment info
                        $title = $attachment->getTitle();
                        $url = $attachment->getHref();
                        $picUrl = $attachment->getPhotoUrl();

                        // Check stuff
                        if ($url && false === stripos($url, 'http://')) {
                            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
                        }
                        if ($picUrl && false === stripos($picUrl, 'http://')) {
                            $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
                        }

                        // Try to keep full message
                        // @todo url shortener?
                        $message = html_entity_decode($form->getValue('body'));
                        if (strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140) {
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                            if ($picUrl) {
                                $message .= ' - ' . $picUrl;
                            }
                        } else if (strlen($message) + strlen($title) + strlen($url) + 6 <= 140) {
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                        } else {
                            if (strlen($title) > 24) {
                                $title = Engine_String::substr($title, 0, 21) . '...';
                            }
                            // Sigh truncate I guess
                            if (strlen($message) + strlen($title) + strlen($url) + 9 > 140) {
                                $message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
                            }
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                        }

                        $twitter = $twitterTable->getApi();
                        $twitter->statuses->update($message);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            }

            // Publish to janrain
            if ('publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
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

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Getting the notifications
     *
     * @return array
     */
    public function notificationsAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);
        $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $serverHost = trim($serverHost, '/');
        $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseParentUrl = @trim($baseParentUrl, "/");

        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();
        $getHost = '';
        if ($getDefaultStorageType == 'local')
            $getHost = !empty($staticBaseUrl) ? $staticBaseUrl : $serverHost;

        $viewer = Engine_Api::_()->user()->getViewer();
        $limit = $this->getRequestParam("limit", 10);
        $page = $this->getRequestParam("page", 1);
        try {
            if ($this->getRequestParam("myRequests", 1)) {
                // Prepare Request Array.
                $requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer);
                $requestLimit = $limit;
                $requestPage = $page;

                $requests->setItemCountPerPage($requestLimit);
                $requests->setCurrentPageNumber($requestPage);
                $response['requestTotalItemCount'] = $requests->getTotalItemCount();

                foreach ($requests as $notification) {
                    // Set the notification information.
                    $values = $notification->toArray();
                    $values['module'] = $this->getNotificationModuleName($notification);
                    if($notification->type == 'live_streaming'){
                        $values['module'] = 'livestreamingvideo';
                    }
                    $enabledModule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($values['module']);
                    if (!$enabledModule) {
                        continue;
                    }
                    $values['url'] = $this->getNotificationModuleUrl($notification);
                    if (!strstr($values['url'], 'http'))
                        $values['url'] = empty($baseParentUrl) ? $serverHost . DIRECTORY_SEPARATOR . ltrim($values['url'], '/') : $serverHost . DIRECTORY_SEPARATOR . $baseParentUrl . ltrim($values['url'], '/');

                    $getFeedTypeInfo = $notification->getTypeInfo()->toArray();
                    $values["action_type_body"] = @strtolower(Engine_Api::_()->getApi('Core', 'siteapi')->translate($getFeedTypeInfo['body']));

                    $values["feed_title"] = $this->_getContent($notification);
                    $values["action_type_body_params"] = $this->_getContent($notification, 1);

                    $finalArray = array();
                    $getActionTypeBodyParams = $values['action_type_body_params'];
                    $getDefaultKey = false;
                    if (isset($getActionTypeBodyParams) && !empty($getActionTypeBodyParams))
                        foreach ($getActionTypeBodyParams as $key => $paramArray) {
                            if (isset($paramArray['label']) && !empty($paramArray['label']) && !strstr($paramArray['label'], " ") && strstr($paramArray['label'], "_")) {
                                try {
                                    if (isset($paramArray['type']) && isset($paramArray['id']) && !empty($paramArray['type']) && !empty($paramArray['id'])) {
                                        $getTempObj = Engine_Api::_()->getItem($paramArray['type'], $paramArray['id']);
                                        if ($getTempObj->getTitle())
                                            $paramArray['label'] = $getTempObj->getTitle();
                                    }else {
                                        $paramArray['label'] = "";
                                    }
                                } catch (Exception $ex) {
                                    $paramArray['label'] = "";
                                }
                            }
                            if (isset($paramArray['type']) && isset($paramArray['id']) && !empty($paramArray['type']) && !empty($paramArray['id']) && $paramArray['type'] == 'suggestion') {
                                $paramArray['type'] = $notification->type;
                            }

                            $finalArray[] = $paramArray;
                        }

                    if (isset($finalArray) && !empty($finalArray))
                        $values['action_type_body_params'] = $finalArray;

                    $values["feed_title"] = $this->_actionTypeBody($values["feed_title"], $values["action_type_body_params"], $notification);

                    $values["request_action"] = '';
                    if (isset($notification->type) && strstr($notification->type, "friend_")) {
                        $objSubject = $notification->getSubject();
                        $values["request_action"] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->userFriendship($objSubject);
                    }

                    $values["advanced_menus"] = Engine_Api::_()->getApi('Siteapi_Core', 'Activity')->getNotificationMenus($notification);

                    if ($this->getRequestParam("subject", 1)) {

                        // Set the subject information.
                        if ($notification->getSubject())
                            $objSubject = $notification->getSubject();

                            $getSubjectType = $objSubject->getType();
                            if ($getSubjectType == 'user'){
                                $values["subject"] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($objSubject);
                                $values["subject"]['status'] = html_entity_decode($values["subject"]['status'], ENT_QUOTES, 'UTF-8');
                            }
                            else{
                                $values["subject"] = $objSubject->toArray();
                                $values["subject"]['body'] = html_entity_decode($values["subject"]['body'], ENT_QUOTES, 'UTF-8');
                            }

                        if (!empty($objSubject)) {
                            $values["subject"] = $objSubject->toArray();
                            $values["subject"]["image"] = ($objSubject->getPhotoUrl('thumb.main')) ? $getHost . $objSubject->getPhotoUrl('thumb.main') : '';
                            $values["subject"]["image_icon"] = ($objSubject->getPhotoUrl('thumb.icon')) ? $getHost . $objSubject->getPhotoUrl('thumb.icon') : '';
                            $values["subject"]["image_profile"] = ($objSubject->getPhotoUrl('thumb.profile')) ? $getHost . $objSubject->getPhotoUrl('thumb.profile') : '';
                            $values["subject"]["image_normal"] = ($objSubject->getPhotoUrl('thumb.normal')) ? $getHost . $objSubject->getPhotoUrl('thumb.normal') : '';
                            $values["subject"]["showVerifyIcon"] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($objSubject);

                            if (empty($values["subject"]["image"])) {
                                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($objSubject);
                                if (isset($getContentImages) && !empty($getContentImages))
                                    $values["subject"] = array_merge($values["subject"], $getContentImages);
                            }
                        }

                        if (isset($values["subject"]["creation_ip"]) && !empty($values["subject"]["creation_ip"]))
                            unset($values["subject"]["creation_ip"]);
                        if (isset($values["subject"]["lastlogin_ip"]) && !empty($values["subject"]["lastlogin_ip"]))
                            unset($values["subject"]["lastlogin_ip"]);
                    }

                    if ($this->getRequestParam("object", 1)) {
                        // Set the object information.
                        $objObject = $notification->getObject();
                        if (!empty($objObject)) {
                            $values["object"] = $objObject->toArray();

                            $getObjectType = $objObject->getType();
                            if ($getObjectType == 'user'){
                                $values["object"] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($objObject);
                                $values["object"]['status'] = html_entity_decode($values["object"]['status'], ENT_QUOTES, 'UTF-8');
                            }
                            else{
                                $values["object"] = $objObject->toArray();
                                $values["object"]['body'] = html_entity_decode($values["object"]['body'], ENT_QUOTES, 'UTF-8');
                            }

                            if (isset($values['module']) && ($values['module'] == 'forum'))
                                $values["object"]['slug'] = $objObject->getSlug();

                            if (isset($values['object']) && (isset($values['object']['resource_type']) && $values['object']['resource_type'] == 'sitereview_listing') && isset($values['object']['resource_id'])) {
                                $sitereviewObj = Engine_Api::_()->getItem('sitereview_listing', $values['object']['resource_id']);
                                if (isset($sitereviewObj) && !empty($sitereviewObj) && isset($sitereviewObj->listingtype_id))
                                    $values["object"]['listingtype_id'] = $sitereviewObj->listingtype_id;
                                $values["object"]['listing_title'] = $sitereviewObj->title;
                            }

                            if (isset($values["object"]["creation_ip"]) && !empty($values["object"]["creation_ip"]))
                                unset($values["object"]["creation_ip"]);
                            if (isset($values["object"]["lastlogin_ip"]) && !empty($values["object"]["lastlogin_ip"]))
                                unset($values["object"]["lastlogin_ip"]);

                            $values["object"]["image"] = ($objObject->getPhotoUrl('thumb.main')) ? $getHost . $objObject->getPhotoUrl('thumb.main') : '';
                            $values["object"]["image_icon"] = ($objObject->getPhotoUrl('thumb.icon')) ? $getHost . $objObject->getPhotoUrl('thumb.icon') : '';
                            $values["object"]["image_profile"] = ($objObject->getPhotoUrl('thumb.profile')) ? $getHost . $objObject->getPhotoUrl('thumb.profile') : '';
                            $values["object"]["image_normal"] = ($objObject->getPhotoUrl('thumb.normal')) ? $getHost . $objObject->getPhotoUrl('thumb.normal') : '';

                            if (empty($values["object"]["image"])) {
                                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($objObject);
                                if (isset($getContentImages) && !empty($getContentImages))
                                    $values["object"] = array_merge($values["object"], $getContentImages);
                            }
                        }
                    }
                    $response['myRequests'][] = $values;
                }
            }

            if ($this->getRequestParam("recentUpdates", 1)) {
                // Prepare Notification Array.
                $ids = array();
                $notifications_sql = Engine_Api::_()->getApi('Siteapi_Core', 'activity')->getNotificationsPaginatorSql($viewer);
                $notifications = Zend_Paginator::factory($notifications_sql);
                $requestLimit = $limit;
                $requestPage = $page;

                $notifications->setItemCountPerPage($requestLimit);
                $notifications->setCurrentPageNumber($requestPage);

                $notifications->clearPageItemCache();
                $response['recentUpdateTotalItemCount'] = $notifications->getTotalItemCount();

                foreach ($notifications as $notification) {
                    $ids[] = $notification->notification_id;

                    // Set the notification information.
                    $values = $notification->toArray();
                    $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("event");
                    if (isset($values) && !empty($values['object_type']) && $values['object_type'] == 'event_photo' && !$isModEnabled) {
                        continue;
                    }
                    $values['module'] = $this->getNotificationModuleName($notification);
                    if($notification->type == 'live_streaming'){
                        $values['module'] = 'livestreamingvideo';
                    }
                    $enabledModule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($values['module']);
                    if (!$enabledModule) {
                        continue;
                    }
                    $values['url'] = $this->getNotificationModuleUrl($notification);
                    $values['url'] =  Engine_Api::_()->getApi('Siteapi_Core', 'advancedactivity')->getHostUrl($values['url']);
                    if (!strstr($values['url'], 'http'))
                        $values['url'] = empty($baseParentUrl) ? $serverHost . DIRECTORY_SEPARATOR . ltrim($values['url'], '/') : $serverHost . DIRECTORY_SEPARATOR . $baseParentUrl . ltrim($values['url'], '/');
                    
                    if($notification->type == 'live_streaming'){
                        $getFeedTypeInfo = $this->getLiveStreamingTypeInfo();;
                    }
                    else
                    $getFeedTypeInfo = $notification->getTypeInfo()->toArray();
                    
                    $values["action_type_body"] = @strtolower(Engine_Api::_()->getApi('Core', 'siteapi')->translate($getFeedTypeInfo['body']));

                    $values["showVerifyIcon"] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($notification->getSubject());

                    $values["feed_title"] = $this->_getContent($notification);
                    $values["action_type_body_params"] = $this->_getContent($notification, 1);
                    
                    if($notification->type == 'live_streaming'){
                        $isLive = $this->isLiveVideo($notification->params);
                        if($isLive == 'was'){
                            $values["action_type_body"] = '{item:$subject} {var:$stream_verb} live.';
                        }
                        
                        
                    }

                    // @Todo: Following code should be modified and move in "getContent()" method.
                    $finalArray = array();
                    $getActionTypeBodyParams = $values['action_type_body_params'];
                    $getDefaultKey = false;
                    if (isset($getActionTypeBodyParams) && !empty($getActionTypeBodyParams))
                        foreach ($getActionTypeBodyParams as $key => $paramArray) {
                            if (isset($paramArray['label']) && !empty($paramArray['label']) && !strstr($paramArray['label'], " ") && strstr($paramArray['label'], "_")) {
                                try {
                                    if (isset($paramArray['type']) && isset($paramArray['id']) && !empty($paramArray['type']) && !empty($paramArray['id'])) {
                                        $getTempObj = Engine_Api::_()->getItem($paramArray['type'], $paramArray['id']);
                                        if ($getTempObj->getTitle())
                                            $paramArray['label'] = $getTempObj->getTitle();
                                    }else {
                                        $paramArray['label'] = "";
                                    }
                                } catch (Exception $ex) {
                                    $paramArray['label'] = "";
                                }
                            }

                            $finalArray[] = $paramArray;
                        }

                    if (isset($finalArray) && !empty($finalArray))
                        $values['action_type_body_params'] = $finalArray;

                    //This work is done for feed title for ios
                    $values["feed_title"] = $this->_actionTypeBody($values["feed_title"], $values["action_type_body_params"], $notification);


                    if ($this->getRequestParam("subject", 1)) {
                        // Set the subject information.
                        $objSubject = $notification->getSubject();
                        if (!empty($objSubject)) {
                            $getSubjectType = $objSubject->getType();
                            if ($getSubjectType == 'user'){
                                $values["subject"] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($objSubject);
                                $values["subject"]['status'] = html_entity_decode($values["subject"]['status'], ENT_QUOTES, 'UTF-8');
                            }
                            else{
                                $values["subject"] = $objSubject->toArray();
                                $values["subject"]['body'] = html_entity_decode($values["subject"]['body'], ENT_QUOTES, 'UTF-8');
                            }

//                            $getSubjectModName = $objSubject->getModuleName();
//                            $values['subject']['name'] = (!empty($getSubjectModName)) ? strtolower($getSubjectModName) : '';
//                            $values['subject']["url"] = $getHost . $objSubject->getHref();                            
                            $values["subject"]["image"] = ($objSubject->getPhotoUrl('thumb.main')) ? $getHost . $objSubject->getPhotoUrl('thumb.main') : '';
                            $values["subject"]["image_icon"] = ($objSubject->getPhotoUrl('thumb.icon')) ? $getHost . $objSubject->getPhotoUrl('thumb.icon') : '';
                            $values["subject"]["image_profile"] = ($objSubject->getPhotoUrl('thumb.profile')) ? $getHost . $objSubject->getPhotoUrl('thumb.profile') : '';
                            $values["subject"]["image_normal"] = ($objSubject->getPhotoUrl('thumb.normal')) ? $getHost . $objSubject->getPhotoUrl('thumb.normal') : '';
                            $values["subject"]["showVerifyIcon"] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($objSubject);

                            if (empty($values["subject"]["image"])) {
                                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($objSubject);
                                if (isset($getContentImages) && !empty($getContentImages))
                                    $values["subject"] = array_merge($values["subject"], $getContentImages);
                            }
                        }
                    }

                    if ($this->getRequestParam("object", 1)) {
                        // Set the object information.
                        $objObject = $notification->getObject();
                        if (!empty($objObject)) {
                            $getObjectType = $objObject->getType();
                            if ($getObjectType == 'user'){
                                $values["object"] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($objObject);
                                $values["object"]['status'] = html_entity_decode($values["object"]['status'], ENT_QUOTES, 'UTF-8');
                            }
                            else{
                                $values["object"] = $objObject->toArray();
                                $values["object"]['body'] = html_entity_decode($values["object"]['body'], ENT_QUOTES, 'UTF-8');
                            }

                            if (isset($values["object"]["ip_address"]) && !empty($values["object"]["ip_address"]))
                                unset($values["object"]["ip_address"]);

                            if (isset($values['object']) && (isset($values['object']['resource_type']) && $values['object']['resource_type'] == 'sitereview_listing') && isset($values['object']['resource_id'])) {
                                $sitereviewObj = Engine_Api::_()->getItem('sitereview_listing', $values['object']['resource_id']);
                                if (isset($sitereviewObj) && !empty($sitereviewObj) && isset($sitereviewObj->listingtype_id))
                                    $values["object"]['listingtype_id'] = $sitereviewObj->listingtype_id;
                                $values["object"]['listing_title'] = $sitereviewObj->title;
                            }

                            if (isset($values['module']) && ($values['module'] == 'forum'))
                                $values["object"]['slug'] = $objObject->getSlug();

                            // Set video and music url
                            $getObjType = $objObject->getType();
                            if (strstr($getObjType, 'video') && !strstr($getObjType, 'channel') && !strstr($getObjType, 'playlist') && !strstr($getObjType, 'sitereview')) {
                                try {
                                     if($objObject->status == 1)
                                    $values["object"]["video_url"] = Engine_Api::_()->getApi('Core', 'siteapi')->getVideoURL($objObject);
                                    $values["object"]["type"] = Engine_Api::_()->getApi('Core', 'siteapi')->videoType($objObject->type);
                                } catch (Exception $ex) {
                                    
                                }
                            } else if ($getObjType == 'music_playlist') {
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('music')) {
                                    $getPlaylistSongs = $objObject->getSongs();
                                    foreach ($getPlaylistSongs as $songObj) {
                                        $songArray = $songObj->toArray();
                                        $songArray['filePath'] = $getHost . $songObj->getFilePath();
                                        $values["object"]['playlist_songs'][] = $songArray;
                                    }
                                }
                            } else if (strstr($getObjType, 'photo')) {
                                try {
                                    $getLikeCount = $objObject->likes()->getLikePaginator();
                                    if (isset($getLikeCount)) {
                                        $values["object"]['likes_count'] = $getLikeCount->getTotalItemCount();
                                    }

                                    $values["object"]['comment_count'] = $objObject->comments()->getCommentCount();
                                    $values["object"]['is_like'] = ($objObject->likes()->isLike($viewer)) ? 1 : 0;
                                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereaction.reaction.active', 1)) {
                                        $values["object"]['reactions'] = $this->_getPhotoReaction($objObject);
                                    }
                                } catch (Exception $ex) {
                                    
                                }
                            }

                            try {
                                $values['object']["url"] = $serverHost . $objObject->getHref();
                                if (isset($values['url']) && empty($values['url'])) {
                                    $values['url'] = $values['object']["url"];
                                }
                            } catch (Exception $ex) {
                                //Blank Exception 
                            }
                            $values["object"]["image"] = ($objObject->getPhotoUrl('thumb.main')) ? $getHost . $objObject->getPhotoUrl('thumb.main') : '';
                            $values["object"]["image_icon"] = ($objObject->getPhotoUrl('thumb.icon')) ? $getHost . $objObject->getPhotoUrl('thumb.icon') : '';
                            $values["object"]["image_profile"] = ($objObject->getPhotoUrl('thumb.profile')) ? $getHost . $objObject->getPhotoUrl('thumb.profile') : '';
                            $values["object"]["image_normal"] = ($objObject->getPhotoUrl('thumb.normal')) ? $getHost . $objObject->getPhotoUrl('thumb.normal') : '';

                            if (empty($values["object"]["image"])) {
                                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($objObject);
                                if (isset($getContentImages) && !empty($getContentImages))
                                    $values["object"] = array_merge($values["object"], $getContentImages);
                            }
                        }

                        //Reaction work..............................
                        $ReactionDetails = array();
                        if ($getObjType == 'activity_action')
                            $ReactionDetails = $this->getFeedReactionDetail($objObject);
                        if (!empty($ReactionDetails)) {
                            $values["object"]['reactionsEnabled'] = $ReactionDetails['reactionsEnabled'];
                            $values["object"]['feed_reactions'] = $ReactionDetails['feed_reactions'];
                        }
                        //..........................................
                    }

                    $response['recentUpdates'][] = $values;
                }
                //Engine_Api::_()->getApi('Siteapi_Core', 'activity')->markUpdatesAsShow($viewer, $ids);
            }

            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }
    
    /*
     * Mark notification as show 
     * @param notifiction_id
     * return status code 204
     */
     public function markNotificationShowAction(){
         $this->validateRequestMethod('POST');
        $notification_id = $this->getRequestParam("notification_id", 0);
        $ids=array();
        if($notification_id){
            $ids[] = $notification_id;
        }
         $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');
        try{
            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->markUpdatesAsShow($viewer, $ids);
         $this->successResponseNoContent('no_content');
        } catch (Exception $ex) {
           $this->respondWithValidationError('internal_server_error', $ex->getMessage()); 
        }
        
    }

    protected function getNotificationModuleUrl($notification) {

        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();
        $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $staticBaseUrl = ($getDefaultStorageType != 'local') ? $serverHost : $staticBaseUrl;

        try {
            $getTypeInfo = $notification->getTypeInfo();
            if ($getTypeInfo->type == 'liked') {
                $getNotificationObj = $notification->getObject();
                if (isset($getNotificationObj->resource_type)) {
                    $isItemTypeAvailable = Engine_Api::_()->hasItemType($getNotificationObj->resource_type);
                    if (!empty($isItemTypeAvailable)) {
                        $item = Engine_Api::_()->getItem($getNotificationObj->resource_type, $getNotificationObj->resource_id);
                        if (!empty($item)) {
                            $getObjectModHref = $item->getHref();
                        }
                    }
                } else if (isset($getNotificationObj->object_type)) {
                    $getTempObject = $getNotificationObj->getObject();
                    if (!empty($getTempObject))
                        $getObjectModHref = $getTempObject->getHref();
                }else if (is_object($getNotificationObj) && $getNotificationObj->getModuleName()) {
                    $getObjectModHref = $getNotificationObj->getHref();
                }
            } else if (($getTypeInfo->type == 'commented') || ($getTypeInfo->type == 'replied')) {
                $getTempObject = $notification->getObject();
                if (!empty($getTempObject) && isset($getTempObject->object_type)) {
                    $getObjectModHref = $getTempObject->getObject()->getHref();
                } else if (is_object($getTempObject) && $getTempObject->getModuleName()) {
                    $getObjectModHref = $getTempObject->getHref();
                }
            } else {
                // By pass for un supported modules.
                $getDefaultAPPModules = DEFAULT_APP_MODULES;
                if (!empty($getDefaultAPPModules)) {
                    $getDefaultAPPModuleArray = @explode(",", DEFAULT_APP_MODULES);
                    if (in_array($getTypeInfo->module, $getDefaultAPPModuleArray)) {
                        $getTempObject = $notification->getObject();
                        if (isset($getTempObject))
                            $getObjectModHref = $getTempObject->getHref();
                    }else {
//                        $getObjectModHref = $getTypeInfo->module;
                    }
                }
            }

            $getObjectModHref = !empty($getObjectModHref) ? $staticBaseUrl . strtolower($getObjectModHref) : '';
            return $getObjectModHref;
        } catch (Exception $ex) {
            return '';
        }
    }

    protected function getNotificationModuleName($notification) {
        try {
            $getTypeInfo = $notification->getTypeInfo();
            if ($getTypeInfo->type == 'liked') {
                $getNotificationObj = $notification->getObject();
                if (isset($getNotificationObj->resource_type)) {
                    $isItemTypeAvailable = Engine_Api::_()->hasItemType($getNotificationObj->resource_type);
                    if (!empty($isItemTypeAvailable)) {
                        $item = Engine_Api::_()->getItem($getNotificationObj->resource_type, $getNotificationObj->resource_id);
                        if (!empty($item)) {
                            $getObjectModName = $item->getModuleName();
                        }
                    }
                } else if (isset($getNotificationObj->object_type)) {
                    $getTempObject = $getNotificationObj->getObject();
                    if (!empty($getTempObject))
                        $getObjectModName = $getTempObject->getModuleName();
                }else if (is_object($getNotificationObj) && $getNotificationObj->getModuleName()) {
                    $getObjectModName = $getNotificationObj->getModuleName();
                }
            } else if (($getTypeInfo->type == 'commented') || ($getTypeInfo->type == 'replied')) {
                $getTempObject = $notification->getObject();
                if (!empty($getTempObject) && isset($getTempObject->object_type)) {
                    $getObjectModName = $getTempObject->getObject()->getModuleName();
                } else if (is_object($getTempObject) && $getTempObject->getModuleName()) {
                    $getObjectModName = $getTempObject->getModuleName();
                }
            } else {
                // By pass for un supported modules.
                $getDefaultAPPModules = DEFAULT_APP_MODULES;
                if (!empty($getDefaultAPPModules)) {
                    $getDefaultAPPModuleArray = @explode(",", DEFAULT_APP_MODULES);
                    if (in_array($getTypeInfo->module, $getDefaultAPPModuleArray)) {
                        $getTempObject = $notification->getObject();
                        if (isset($getTempObject))
                            $getObjectModName = $getTempObject->getModuleName();
                    }else {
                        $getObjectModName = $getTypeInfo->module;
                    }
                }
            }

            $getObjectModName = !empty($getObjectModName) ? strtolower($getObjectModName) : '';
            return $getObjectModName;
        } catch (Exception $ex) {
            return '';
        }
    }

    /**
     * Hide the notification respect to the logged-in user.
     *
     * @return array the notification respect to the logged-in 
     */
    public function hideAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();

        Engine_Api::_()->getApi('Siteapi_Core', 'activity')->markNotificationsAsRead($viewer);
        $this->successResponseNoContent('no_content');
    }

    /**
     * Hide the notification respect to the logged-in user.
     *
     * @return array the notification respect to the logged-in 
     */
    public function markFriendRequestReadAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();

        Engine_Api::_()->getApi('Siteapi_Core', 'activity')->markFriendRequestRead($viewer);
        $this->successResponseNoContent('no_content');
    }

    /**
     * Mark as read the notification.
     *
     * @return array
     */
    public function markreadAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (null === ($action_id = $this->getRequestParam('action_id', null)))
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $db = $notificationsTable->getAdapter();
        $db->beginTransaction();
        try {
            $notification = Engine_Api::_()->getItem('activity_notification', $action_id);
            if ($notification) {
                $notification->read = 1;
                $notification->save();
            }
            // Commit
            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Getting all the new updates
     *
     * @return array
     */
    public function newUpdatesAction() {
        // Validate request methods
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();

        $updates = array();
        if ($viewer->getIdentity()) {
            $updates['notifications'] = (int) Engine_Api::_()->getApi('Siteapi_Core', 'activity')->getNewUpdatesCount($viewer, array('isNotification' => 'true'));

            $updates['friend_requests'] = (int) Engine_Api::_()->getApi('Siteapi_Core', 'activity')->getNewUpdatesCount($viewer, array('type' => 'friend_request'));

            $updates['messages'] = (int) Engine_Api::_()->getApi('Siteapi_Core', 'activity')->getUnreadMessageCount($viewer);

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore') && $viewer->getIdentity()) {
                $updates['cartProductsCount'] = 0;
                $cartId = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($viewer->getIdentity());
                if ($cartId)
                    $updates['cartProductsCount'] = (int) Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts($cartId);
            }
        }

        $this->respondWithSuccess($updates);
    }

    /**
     * Getting the friend requests notification.
     *
     * @return array
     */
    public function friendRequestAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);
        $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();

        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();
        $getHost = '';
        if ($getDefaultStorageType == 'local')
            $getHost = !empty($staticBaseUrl) ? $staticBaseUrl : $serverHost;

        $viewer = Engine_Api::_()->user()->getViewer();
        $response = $ids = array();

        try {
            $friendRequests = Engine_Api::_()->getApi('Siteapi_Core', 'activity')->getRequestsPaginator($viewer);
            $requestLimit = $this->getRequestParam("limit", 10);
            $requestPage = $this->getRequestParam("page", 1);

            $friendRequests->setItemCountPerPage($requestLimit);
            $friendRequests->setCurrentPageNumber($requestPage);
            $response['totalItemCount'] = $friendRequests->getTotalItemCount();

            // Now mark them all as view
            foreach ($friendRequests as $notification) {
                $ids[] = $notification->notification_id;

                $values = $notification->toArray();
                $getFeedTypeInfo = $notification->getTypeInfo()->toArray();
                $values["action_type_body"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getFeedTypeInfo['body']);

                $values["showVerifyIcon"] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($notification->getSubject());
                $values["feed_title"] = $this->_getContent($notification);
                $values["action_type_body_params"] = $this->_getContent($notification, 1);
//        $values["request_action"] = '';
                if (isset($notification->type) && strstr($notification->type, "friend_")) {
                    $objSubject = $notification->getSubject();
                }

                if (!$this->getRequestParam("subject", 0)) {
                    // Set the subject information.            

                    if (empty($objSubject))
                        $objSubject = $notification->getSubject();

                    $values["subject"] = ($objSubject->getType() == 'user') ? Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($objSubject) : $objSubject->toArray();

                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($objSubject);
                    $values["subject"] = array_merge($values["subject"], $getContentImages);

                    // Member Verification work......
                    $values["subject"]["showVerifyIcon"] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($notification->getSubject());

                    if (isset($values['subject']['lastlogin_ip']) && !empty($values['subject']['lastlogin_ip']))
                        unset($values['subject']['lastlogin_ip']);


                    if (isset($values['subject']['creation_ip']) && !empty($values['subject']['creation_ip']))
                        unset($values['subject']['creation_ip']);
                }

                if (!$this->getRequestParam("object", 0)) {
                    // Set the object information.
                    $objObject = $notification->getObject();
                    $values["object"] = ($objObject->getType() == 'user') ? Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($objObject) : $objObject->toArray();

                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($objObject);
                    $values["object"] = array_merge($values["object"], $getContentImages);

//                    $values["object"]["image"] = ($objObject->getPhotoUrl('thumb.main')) ? $getHost . $objObject->getPhotoUrl('thumb.main') : '';
//                    $values["object"]["image_icon"] = ($objObject->getPhotoUrl('thumb.icon')) ? $getHost . $objObject->getPhotoUrl('thumb.icon') : '';
//                    $values["object"]["image_profile"] = ($objObject->getPhotoUrl('thumb.profile')) ? $getHost . $objObject->getPhotoUrl('thumb.profile') : '';
//                    $values["object"]["image_normal"] = ($objObject->getPhotoUrl('thumb.normal')) ? $getHost . $objObject->getPhotoUrl('thumb.normal') : '';

                    if (isset($values['object']['lastlogin_ip']) && !empty($values['object']['lastlogin_ip']))
                        unset($values['object']['lastlogin_ip']);


                    if (isset($values['object']['creation_ip']) && !empty($values['object']['creation_ip']))
                        unset($values['object']['creation_ip']);
                }

                $response['response'][] = $values;
            }

            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->markUpdatesAsShow($viewer, $ids);
            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Get feed body.
     *
     * @return array
     */
    private function _getContent($action, $flag = false) {
        $model = Engine_Api::_()->getApi('core', 'activity');
        $params = array_merge(
                $action->toArray(), (array) $action->params, array(
            'subject' => $action->getSubject(),
            'object' => $action->getObject()
                )
        );

        $params['flag'] = $flag;
         if($action->type == 'live_streaming'){
             $content = $this->assemble('{item:$subject} {var:$stream_verb} live Now.', $params);
         }
         else
            $content = $this->assemble($action->getTypeInfo()->body, $params);
         
        return $content;
    }

    protected $_pluginLoader;

    /**
     * Feed Title - Load the Plugins
     *
     * @return array
     */
    private function _getPluginLoader() {
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
     * Normalize helper name
     * 
     * @param string $name
     * @return string
     */
    private function _normalizeHelperName($name) {
        $name = preg_replace('/[^A-Za-z0-9]/', '', $name);
        $name = ucfirst($name);
        return $name;
    }

    /**
     * Activity template parsing
     * 
     * @param string $body
     * @param array $params
     * @return string
     */
    private function assemble($body, array $params = array()) {
        if('{item:$subject} {var:$stream_verb} live Now.' == $body){
            $body = '{item:$subject} {var:$stream_verb} live.';
        }
        $body = Engine_Api::_()->getApi('Core', 'siteapi')->translate($body);

//        // By pass for un supported modules.
//        $getDefaultAPPModules = DEFAULT_APP_MODULES;
//        if (!empty($getDefaultAPPModules)) {
//            $getDefaultAPPModuleArray = @explode(",", DEFAULT_APP_MODULES);
//
//            if ($params['object_type'] == 'core_comment') {
//                $coreCommentObj = Engine_Api::_()->getItem('core_comment', $params['object_id']);
//                if (!empty($coreCommentObj)) {
//                    $coreResourceObj = Engine_Api::_()->getItem($coreCommentObj->resource_type, $coreCommentObj->resource_id);
//                    $moduleName = !empty($coreResourceObj) ? $coreResourceObj->getModuleName() : '';
//                    $moduleName = strtolower($moduleName);
//                }
//            } else if (!empty($params['object']) && is_object($params['object'])) {
//                $moduleName = $params['object']->getModuleName();
//                $moduleName = strtolower($moduleName);
//            }
//
//            if (!in_array($moduleName, $getDefaultAPPModuleArray))
//                return $body;
//        }
        // Do other stuff
        preg_match_all('~\{([^{}]+)\}~', $body, $matches, PREG_SET_ORDER);
        $feedParams = array();

        foreach ($matches as $match) {
            $tag = @strtolower($match[0]);
            $args = explode(':', $match[1]);
            $helper = array_shift($args);

            $tempParams = $helperArgs = array();
            $tempLabel = $tempType = $tempId = '';
            foreach ($args as $arg) {
                if (substr($arg, 0, 1) === '$') {
                    $arg = substr($arg, 1);
                    $helperArgs[] = ( isset($params[$arg]) ? $params[$arg] : null );
                } else {
                    $helperArgs[] = $arg;
                    $tempLabel .= $arg;
                }
            }

            try {
                $helper = $this->getHelper($helper);
                $r = new ReflectionMethod($helper, 'direct');
                $content = $r->invokeArgs($helper, $helperArgs);
            } catch (Exception $ex) {
                return $body;
            }

            // Make a feed type body params for dynamic Feed Title
            if (isset($helperArgs[0]) && !empty($helperArgs[0])) {
                if (is_object($helperArgs[0])) {

                    $tempParams['search'] = $tag;
                    $tempParams['label'] = (isset($helperArgs[1]) && !empty($helperArgs[1]) && is_string($helperArgs[1])) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($helperArgs[1]) : Engine_Api::_()->getApi('Core', 'siteapi')->translate($helperArgs[0]->getTitle());

                    // @Todo: We will update the following code with Advanced Event Plugin Api's
                    if ($tag == '{itemparent:$object::event topic}') {
                        $tempParams['search'] = '{itemparent:$object::event topic}';
                        $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event topic');
                    }

                    $tempParams['type'] = $helperArgs[0]->getType();
                    $tempParams['id'] = $helperArgs[0]->getIdentity();

                    if (isset($helperArgs[1]) && is_object($helperArgs[1]) && strstr($tag, '{actors:$subject:$object}')) {
                        $tempParams['object']['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($helperArgs[1]->getTitle());
                        $tempParams['object']['type'] = $helperArgs[1]->getType();
                        $tempParams['object']['id'] = $helperArgs[1]->getIdentity();
                    }
                } else {
                    $tempParams['search'] = $tag;
                    $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate(preg_replace('/<\/?a[^>]*>/', '', $helperArgs[0]));

                    // In case of GUID, create object and send respective array to client.
                    if (isset($helperArgs[0]) && !empty($helperArgs[0]) && is_string($helperArgs[0]) && strstr($helperArgs[0], '_')) {
                        $explodeItemTypes = @explode("_", $helperArgs[0]);
                        $id = @end($explodeItemTypes);
                        array_pop($explodeItemTypes);
                        $type = @implode("_", $explodeItemTypes);
                        if (!empty($type) && !empty($id)) {
                            try {
                                $getObj = Engine_Api::_()->getItem($type, $id);
                            } catch (Exception $e) {
                                //Blank Exception
                            }
                            if (!empty($getObj)) {
                                $tempParams['search'] = $tag;
                                $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getObj->getTitle());
                                $tempParams['type'] = $getObj->getType();
                                $tempParams['id'] = $getObj->getIdentity();
                            }
                        }
                    }
                }

                if ($tag == '{item:$postguid:posted}') {
                    $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('posted');
                }

                if (isset($tempParams['search']) && ($tempParams['search'] == '{item:$page}'))
                    $tempParams['label'] = '';
                if (isset($tempParams['search']) && !empty($tempParams['search']))
                    $tempParams['search'] = @strtolower($tempParams['search']);
                $feedParams[] = $tempParams;
            } else {
                if (isset($tempParams['search']) && !empty($tempParams['search']))
                    $tempParams['search'] = @strtolower($tempParams['search']);
                if($tag == '{var:$stream_verb}'){
                    //check video is live or not......
                    $isLive = $this->isLiveVideo($params['params']);
                    $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($isLive);;
               }
                $tempParams['search'] = $tag;
                $feedParams[] = $tempParams;
            }
            // Make a Feed Title
            $content = preg_replace('/\$(\d)/', '\\\\$\1', $content);
            if (isset($tempParams['label']) && !empty($tempParams['label']))
                $body = preg_replace("/" . preg_quote($tag) . "/", $tempParams['label'], $body, 1);
            else
                $body = preg_replace("/" . preg_quote($tag) . "/", $content, $body, 1);
        }

        if (isset($params['flag']) && !empty($params['flag'])) {
            return $feedParams;
        } else {
            $body = strip_tags($body);
            return $body;
        }
    }

    function _actionTypeBody($feed_title, $feedTypebodyParam = array(), $action) {
        $params = array_merge(
                $action->toArray(), (array) $action->params, array(
            'subject' => $action->getSubject(),
            'object' => $action->getObject()
                )
        );
        if($action->type == 'live_streaming'){
            $body = '{item:$subject} {var:$stream_verb} live Now.';
         }
         else
            $body = $action->getTypeInfo()->body;
        preg_match_all('~\{([^{}]+)\}~', $body, $matches, PREG_SET_ORDER);
        foreach ($matches as $key => $match) {
            $tag = $match[0];
            if (stripos($feed_title, $tag)) {

                $label = $this->_getLabel($tag, $feedTypebodyParam);

                $feed_title = str_ireplace($tag, $label, $feed_title);
            }
        }
        return $feed_title;
    }

    private function _getLabel($text, $feedTypeBodyParam) {
        $title = '';
        foreach ($feedTypeBodyParam as $key => $val) {

            $text = strtolower($text);
            if ($val['search'] == $text) {
                $title = strtolower($val['label']);
                break;
            }
        }
        return $title;
    }

    private function getFeedReactionDetail($action = null) {
        $getBodyResponse = array();
        $getBodyResponse['reactionsEnabled'] = 0;
        $getBodyResponse['reactionsEnabled'] = array();
        if (Engine_Api::_()->getApi('Siteapi_Feed', 'advancedactivity')->isSitereactionPluginLive()) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereaction.reaction.active', 1)) {
                $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
                $action = $actionTable->getActionById($action->action_id);

                $getBodyResponse['reactionsEnabled'] = 1;
                $popularity = Engine_Api::_()->getApi('core', 'sitereaction')->getLikesReactionPopularity($action);
                $getBodyResponse['feed_reactions'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitereaction')->getLikesReactionIcons($popularity, 1);
            }
        }

        return $getBodyResponse;
    }
    
      private function _getPhotoReaction($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!isset($subject) || empty($subject))
            return;

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
    
    public function isLiveVideo($params){
        if(isset($params['stream_name'])){
            $streamTable = Engine_Api::_()->getDbtable('streams' , 'livestreamingvideo');
            $stream = $streamTable->getstream($params['stream_name']);
        }
        $isLive = 'was';
        if($stream->end_time == null){
            $isLive = 'is';
        }
        return $isLive;
    }
    
    /* Add a new notification type */
    public function getLiveStreamingTypeInfo()
    {
        return array(
            'type'=>'live_streaming',
            'module' => 'livestreamingvideo',
            'body' => '{item:$subject} {var:$stream_verb} live Now.',
            
        );
    }

}
