<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Core extends Core_Api_Abstract {

    /**
     * Getting the host name
     * 
     * @return string
     */
    public function getHost() {
        return _ENGINE_SSL ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST'];
    }

    /**
     * Translate the language
     * 
     * @param type $language
     * @return string
     */
    public function translate($defaultLanguage = null, $getLocal = null, $params = array()) {

        if (!empty($defaultLanguage) && is_array($defaultLanguage)) {
            foreach ($defaultLanguage as $key => $str) {
                $defaultLanguage[$key] = $this->getTranslation($str, $getLocal, $params);
            }
            return $defaultLanguage;
        } else {
            return $this->getTranslation($defaultLanguage, $getLocal, $params);
        }
    }

    public function setView() {
        $view = new Zend_View();
        $view->setEncoding('utf-8');
        $view->addScriptPath(APPLICATION_PATH);
        $view->addHelperPath('Engine/View/Helper/', 'Engine_View_Helper_');
        Zend_Registry::set('Zend_View', $view);
    }

    public function setLocal() {
        $locale = $this->getLocal();
        $localeObject = new Zend_Locale($locale);
        Zend_Registry::set('Locale', $localeObject);
    }

    public function setTimezone() {

        Zend_Registry::set('timezone', Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    }

    /**
     * Getting the login user local
     * 
     * @return string
     */
    public function getLocal() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;

        $locale = ($viewer->getIdentity()) ? $viewer->locale : Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
        $locale = !empty($locale) ? $locale : 'auto';

        return $locale;
    }

    /**
     * Getting the available API modules name.
     * 
     * @return array
     */
    public function getAPIModulesName() {
        return array(
            "blog",
            "classified",
            "group",
            "event",
            "album",
            "forum",
            "poll",
            "video",
            "music",
            "advancedactivity",
            "sitetagcheckin"
        );
    }

    /**
     * Get available itemtype
     * 
     * @return array
     */
    public function getItemTypes() {
        return array(
            "album", "album_photo",
            "blog",
            "classified", "classified_album",
            "core_link",
            "event",
            "forum", "forum_category", "forum_post", "forum_topic",
            "group", "group_photo", "group_post", "group_topic",
            "music_playlist", "music_playlist_song",
            "poll",
            "user",
            "video"
        );
    }

    /**
     * Is subject like by login user
     * 
     * @param type $subject
     * @return boolean
     */
    public function isLike($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (empty($subject) || empty($viewer))
            return;

        if ($viewer->getIdentity())
            $isLike = Engine_Api::_()->getDbTable("likes", "core")->isLike($subject, $viewer);

        $isLike = !empty($isLike) ? true : false;

        return $isLike;
    }

    /**
     * Getting the like count
     * 
     * @param type $subject
     * @return type
     */
    public function getLikeCount($subject) {
        $getLikeCount = $subject->likes()->getLikePaginator()->getTotalItemCount();
        $getLikeCount = !empty($getLikeCount) ? $getLikeCount : 0;

        return $getLikeCount;
    }

    /**
     * Check default "/index.php/application/" valid or not because in case of API calling, we need to change "index.php" to "siteapi.php"
     * 
     * @return boolean
     */
    public function isRootFileValid() {
        $isValidFileAvailable = false;
        $file = APPLICATION_PATH . "/index.php";
        if (file_exists($file) && is_readable($file)) {
            $myfile = @fopen($file, "r") or die("Unable to open file!");
            // Output one line until end-of-file
            while (!feof($myfile)) {
                $rowContent = @fgets($myfile);
                if (strstr($rowContent, 'siteapi.php')) {
                    $isValidFileAvailable = true;
                    break;
                }
            }
            @fclose($myfile);
        }
        return $isValidFileAvailable;
    }

    /**
     * Read default "/index.php/application/" file and return the content of the file, which should be update in root file.
     * 
     * @return type
     */
    public function getRootFileContent() {
        $file = APPLICATION_PATH . "/index.php";
        if (file_exists($file)) {
            $myfile = @fopen($file, "r") or die("Unable to open file!");
            // Output one line until end-of-file
            while (!feof($myfile)) {
                $rowContent = @fgets($myfile);

                if (strstr($rowContent, "'_ENGINE_R_TARG', 'index.php'")) {
                    $content .= '';
                    $content .= '$getRequestUri = htmlspecialchars($_SERVER[\'REQUEST_URI\']);';
                    $content .= 'if(isset($getRequestUri) && !empty($getRequestUri) && strstr($getRequestUri, "api/rest"))';
                    $content .= '  define(\'_ENGINE_R_TARG\', \'siteapi.php\');';
                    $content .= 'else';
                    $content .= '  define(\'_ENGINE_R_TARG\', \'index.php\');';
                } else {
                    $content .= $rowContent;
                }
            }
            @fclose($myfile);
        }

        return $content;
    }

    /**
     * Set the content in root "/index.php/application/" file.
     * 
     * @param type $content: Set content in root file.
     * @return boolean
     */
    public function setRootFileContent($content = '') {
        $file = APPLICATION_PATH . "/index.php";
        if (!empty($content) && file_exists($file)) {
            if (is_writable($file)) {
                $isRootFileValid = $this->isRootFileValid();
                if (empty($isRootFileValid)) {
                    $content = $this->getRootFileContent();
                    if (!empty($content)) {
                        $fh = @fopen($file, 'w') or die("can't open file");
                        @fwrite($fh, $content);
                        @fclose($fh);
                        return true;
                    }
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Generates a fluent list of item. Example:
     *   You
     *   You and Me
     *   You, Me, and Jenny
     * 
     * @param array|Traversable $items
     * @return string
     */
    public function fluentList($items, $translate = false) {
        if (0 === ($num = count($items))) {
            return '';
        }

        $comma = ',';
        $and = 'and';
        $index = 0;
        $content = '';
        foreach ($items as $item) {
            if ($num > 2 && $index > 0)
                $content .= $comma . ' ';
            else
                $content .= ' ';
            if ($num > 1 && $index == $num - 1)
                $content .= $and . ' ';

            $href = null;
            $title = null;

            if (is_object($item)) {
                if (method_exists($item, 'getTitle') && method_exists($item, 'getHref')) {
                    $href = $item->getHref();
                    $title = $item->getTitle();
                } else if (method_exists($item, '__toString')) {
                    $title = $item->__toString();
                } else {
                    $title = (string) $item;
                }
            } else {
                $title = (string) $item;
            }

            if ($translate) {
                $title = $title;
            }

            if (null === $href) {
                $content .= $title;
            } else {
                $content .= '<a href="' . $href . '">' . $title . '</a>';
            }

            $index++;
        }

        return $content;
    }

    /**
     * Remove restricted fields from user array in response.
     * 
     * @param type $user: SocialEngine user array
     * @return array
     */
    public function validateUserArray(User_Model_User $user, $ignoreParams = array()) {
        try {
            $restrictedFields = array('email', 'password', 'salt', 'creation_ip', 'lastlogin_ip');
            $isFriends = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($user);
            $userArray = $user->toArray();
            $userArray['displayname'] = $user->getTitle();
            foreach ($restrictedFields as $restrictedValue) {
                if (!in_array($restrictedValue, $ignoreParams))
                    unset($userArray[$restrictedValue]);
            }

            if (!empty($isFriends))
                $userArray['friendship_type'] = $isFriends;

            if (isset($user->language) && ($user->language == 'English'))
                $user->language = 'en';

            if (isset($user->local) && ($user->local == 'English'))
                $user->local = 'en';

            return $userArray;
        } catch (Exception $ex) {
            // Blank Exception
        }
    }

    /**
     * Getting the content URL
     * 
     * @param type $subject: Object of content
     * @return array
     */
    public function getContentURL($subject) {

        $url = array();
        try {
            if (!empty($subject)) {
                $getHref = $subject->getHref();
                if (!empty($getHref)) {
                    $host = $this->getHost();
                    $url['content_url'] = !empty($getHref) ? $host . $getHref : '';
                }
            }
        } catch (Exception $ex) {
            // Blank Exception
        }

        return $url;
    }

    /**
     * Getting the all type(main, icon, normal and profile) of image urls.
     * 
     * @param type $subject: Object of content
     * @param type $getOwnerImage: Need Object Owner images
     * @param type $key: Need to modify response key value
     * @return array
     */
    public function getContentImage($subject, $getOwnerImage = false, $key = false) {
        if (!isset($subject) || empty($subject))
            return;
        $getParentHost = $this->getHost();
        $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseParentUrl = @trim($baseParentUrl, "/");
        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

        // Check IF default service "Local Storage" or not.
        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();
        $host = '';
        if ($getDefaultStorageType == 'local')
            $host = !empty($staticBaseUrl) ? $staticBaseUrl : $this->getHost();

        $type = (empty($getOwnerImage)) ? $subject->getType() : $subject->getOwner()->getType();
        $images = array();
        if (empty($getOwnerImage)) { // Getting content images
            // If image url already contains http://
            if (strstr($subject->getPhotoUrl('thumb.main'), 'http://') || strstr($subject->getPhotoUrl('thumb.main'), 'https://'))
                $host = '';

            $tempKey = empty($key) ? 'image' : $key . '_image';
            $images[$tempKey] = (($thumbMain = $subject->getPhotoUrl('thumb.main')) && !empty($thumbMain)) ? (!strstr($thumbMain, "application/modules")) ? $host . $subject->getPhotoUrl('thumb.main') : $this->getDefaultImage($type, 'main') : $this->getDefaultImage($type, 'main');
            if (!strstr($images[$tempKey], 'http'))
                $images[$tempKey] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey];

            $images[$tempKey . '_normal'] = (($thubNormal = $subject->getPhotoUrl('thumb.normal')) && !empty($thubNormal)) ? (!strstr($thubNormal, "application/modules")) ? $host . $subject->getPhotoUrl('thumb.normal') : $this->getDefaultImage($type, 'normal') : $this->getDefaultImage($type, 'normal');
            if (!strstr($images[$tempKey . '_normal'], 'http'))
                $images[$tempKey . '_normal'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_normal'];

            $images[$tempKey . '_profile'] = (($thumbProfile = $subject->getPhotoUrl('thumb.profile')) && !empty($thumbProfile)) ? (!strstr($thubNormal, "application/modules")) ? $host . $subject->getPhotoUrl('thumb.profile') : $this->getDefaultImage($type, 'profile') : $this->getDefaultImage($type, 'profile');
            if (!strstr($images[$tempKey . '_profile'], 'http'))
                $images[$tempKey . '_profile'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_profile'];

            $images[$tempKey . '_icon'] = (($thumbIcon = $subject->getPhotoUrl('thumb.icon')) && !empty($thumbIcon)) ? (!strstr($thubNormal, "application/modules")) ? $host . $subject->getPhotoUrl('thumb.icon') : $this->getDefaultImage($type, 'icon') : $this->getDefaultImage($type, 'icon');
            if (!strstr($images[$tempKey . '_icon'], 'http'))
                $images[$tempKey . '_icon'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_icon'];

            $images['photo_id'] =  $subject->getIdentity();
            // Add content url
            $contentURL = $this->getContentURL($subject);
            $contentCoverImage = $this->getContentCoverPhoto($subject);
            $images = array_merge($images, $contentURL);
            if (isset($contentCoverImage) && !empty($contentCoverImage))
                $images = array_merge($images, $contentCoverImage);
        } else { // Getting owner images
            if (strstr($subject->getOwner()->getPhotoUrl('thumb.main'), 'http://') || strstr($subject->getOwner()->getPhotoUrl('thumb.main'), 'https://'))
                $host = '';

            $tempKey = empty($key) ? 'owner_image' : $key . '_owner_image';
            $images[$tempKey] = ($subject->getOwner()->getPhotoUrl('thumb.main')) ? $host . $subject->getOwner()->getPhotoUrl('thumb.main') : $this->getDefaultImage($type, 'main');
            if (!strstr($images[$tempKey], 'http'))
                $images[$tempKey] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey];

            $images[$tempKey . '_normal'] = ($subject->getOwner()->getPhotoUrl('thumb.normal')) ? $host . $subject->getOwner()->getPhotoUrl('thumb.normal') : $this->getDefaultImage($type, 'normal');
            if (!strstr($images[$tempKey . '_normal'], 'http'))
                $images[$tempKey . '_normal'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_normal'];

            $images[$tempKey . '_profile'] = ($subject->getOwner()->getPhotoUrl('thumb.profile')) ? $host . $subject->getOwner()->getPhotoUrl('thumb.profile') : $this->getDefaultImage($type, 'profile');
            if (!strstr($images[$tempKey . '_profile'], 'http'))
                $images[$tempKey . '_profile'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_profile'];

            $images[$tempKey . '_icon'] = ($subject->getOwner()->getPhotoUrl('thumb.icon')) ? $host . $subject->getOwner()->getPhotoUrl('thumb.icon') : $this->getDefaultImage($type, 'icon');
            if (!strstr($images[$tempKey . '_icon'], 'http'))
                $images[$tempKey . '_icon'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_icon'];
        }

        return $images;
    }

    /**
     * Getting the default images url
     * 
     * @param type $module: Module name
     * @param type $type: Image type
     * @return string
     */
    public function getDefaultImage($module, $type = 'icon') {
        $getHost = $this->getHost();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseUrl = @trim($baseUrl, "/");
        switch ($module) {
            case "album_photo":
            case "group_photo":
            case "event_photo":
                return '';
                break;

            case "user":
                $path = '/application/modules/User/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_user_thumb_icon.png';
                else
                    $imageName = 'nophoto_user_thumb_profile.png';
                break;

            case "classified":
                $path = '/application/modules/Classified/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_classified_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_classified_thumb_normal.png';
                else
                    $imageName = 'nophoto_classified_thumb_profile.png';
                break;

            case "sitestoreproduct_category":
                $path = '/application/modules/Sitestoreproduct/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_product_caregory.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_product_caregory.png';
                else
                    $imageName = 'nophoto_product_caregory.png';
                break;

            case "sitestoreproduct_wishlist":
                $path = '/application/modules/Sitestoreproduct/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_wishlist_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_wishlist_thumb_normal.png';
                else
                    $imageName = 'nophoto_wishlist_thumb_profile.png';
                break;

            case "sitestoreproduct_product":
                $path = '/application/modules/Sitestoreproduct/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_product_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_product_thumb_normal.png';
                else
                    $imageName = 'nophoto_product_thumb_profile.png';
                break;

            case "sitestore_store":
                $path = '/application/modules/Sitestore/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_store_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_store_thumb_normal.png';
                else
                    $imageName = 'nophoto_store_thumb_profile.png';
                break;

            case "sitestore_album":
                $path = '/application/modules/Sitestore/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_album_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_album_thumb_normal.png';
                else
                    $imageName = 'nophoto_album_thumb_normal.png';
                break;

            case "group":
                $path = '/application/modules/Siteapi/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_group_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_group_thumb_normal.png';
                else
                    $imageName = 'nophoto_group_thumb_profile.png';
                break;

            case "event":
                $path = '/application/modules/Siteapi/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_event_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_event_thumb_normal.png';
                else
                    $imageName = 'nophoto_event_thumb_profile.png';
                break;
            case "siteevent_event":
                $path = '/application/modules/Siteapi/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_event_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_event_thumb_normal.png';
                else
                    $imageName = 'nophoto_event_thumb_profile.png';
                break;
            case "siteevent_organizer":
                $path = '/application/modules/Siteevent/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_organizer_thumb_icon.png';
                else
                    $imageName = 'nophoto_organizer_thumb_profile.png';
                break;
            case "siteevent_diary":
                $path = '/application/modules/Siteapi/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_diary_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_diary_thumb_normal.png';
                else
                    $imageName = 'nophoto_diary_thumb_profile.png';
                break;
            case "siteevent_topic":
                $path = '/application/modules/Siteapi/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_event_topic_thumb_icon.jpg';
                else if ($type == 'normal')
                    $imageName = 'nophoto_event_topic_thumb_normal.jpg';
                else
                    $imageName = 'nophoto_event_topic_thumb_profile.jpg';
                break;
            case "siteevent_category":
                $path = '/application/modules/Siteevent/externals/images/';
                $imageName = 'nophoto_event_caregory.png';
                break;
            case "siteevent_organizer":
                $path = '/application/modules/Siteevent/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_organizer_thumb_icon.png';
                else
                    $imageName = 'nophoto_organizer_thumb_profile.png';
                break;

            case "album":
                $path = '/application/modules/Album/externals/images/';
                $imageName = 'nophoto_album_thumb_normal.png';
                break;

            case "forum":
                $path = '/application/modules/Siteapi/externals/images/';
                $imageName = 'forum.png';
                break;

            case "video":
            case "sitevideo_channel":
            case "sitevideo_playlist":
                $path = '/application/modules/Siteapi/externals/images/';
                $imageName = 'nophoto_video_thumb_icon.png';
                break;

            case "siteevent_video":
                $path = '/application/modules/Siteevent/externals/images/';
                $imageName = 'video.png';
                break;

            case "music_playlist":
                $path = '/application/modules/Siteapi/externals/images/';
                $imageName = 'nophoto_playlist_main.png';
                break;

            case "forum_post":
                $path = '/application/modules/Siteapi/externals/images/';
                $imageName = 'nophoto_post_thumb_icon.png';
                break;

            case "forum_forum":
                $path = '/application/modules/Siteapi/externals/images/';
                $imageName = 'nophoto_forum_thumb_icon.png';
                break;

            case "forum_topic":
                $path = '/application/modules/Siteapi/externals/images/';
                $imageName = 'nophoto_topic_thumb_icon.png';
                break;
            case "sitereview_listing":
                $path = '/application/modules/Sitereview/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_listing_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_listing_thumb_normal.png';
                else
                    $imageName = 'nophoto_listing_thumb_profile.png';
                break;
            case "sitereview_wishlist":
                $path = '/application/modules/Sitereview/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_wishlist_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_wishlist_thumb_normal.png';
                else
                    $imageName = 'nophoto_wishlist_thumb_profile.png';
                break;
            case "sitereview_category":
                $path = '/application/modules/Sitereview/externals/images/';
                $imageName = 'category.png';
                break;
            case "sitegroup_group":
                $path = '/application/modules/Siteapi/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_group_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_group_thumb_normal.png';
                else
                    $imageName = 'nophoto_group_thumb_profile.png';
                break;
            case "sitegroupoffer_offer":
                $path = '/application/modules/Sitegroupoffer/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_offer_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_offer_thumb_normal.png';
                else
                    $imageName = 'nophoto_offer_thumb_profile.png';
                break;
            case "siteeventticket_coupon":
                $path = '/application/modules/Siteeventticket/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_coupon_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_coupon_thumb_normal.png';
                else
                    $imageName = 'nophoto_coupon_thumb_profile.png';
                break;
            case "sitegroup_category":
                $path = '/application/modules/Sitegroup/externals/images/';
                $imageName = 'category.png';
                break;
            case "sitevideo":
                $path = '/application/modules/Siteapi/externals/images/';
                $imageName = 'nophoto_video_thumb_icon.png';
                break;
            case "poll":
            case "sitegrouppoll_poll":
                $path = '/application/modules/Siteapi/externals/images/';
                $imageName = 'nophoto_poll_thumb_icon.png';
                break;
            case "page":
            case 'sitepage_page':
            case 'sitepage':
                $path = '/application/modules/Siteapi/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_page_thumb_icon.png';
                else if ($type == 'normal')
                    $imageName = 'nophoto_page_thumb_normal.png';
                else
                    $imageName = 'nophoto_page_thumb_profile.png';
                break;

            case "core_link":
                $path = '/application/modules/Siteapi/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_core_link_thumb_icon.png';
                else
                    $imageName = 'nophoto_core_link_thumb_profile.png';;
                break;

            default:
                $path = '/application/modules/User/externals/images/';
                if ($type == 'icon')
                    $imageName = 'nophoto_user_thumb_icon.png';
                else
                    $imageName = 'nophoto_user_thumb_profile.png';
                break;
        }

        // Get file url
        $imageUrl = $getHost . '/' . $baseUrl . $path . $imageName;
        if (strstr($imageUrl, 'index.php/'))
            $imageUrl = str_replace('index.php/', '', $imageUrl);

        if (!empty($imageUrl))
            return $imageUrl;
    }

    public function webViewRestrictedModulesList() {
        $pluginTitle = array();
        $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitemobile');
        if (isset($sitemobile) && !empty($sitemobile)) {
            $pluginTitle[] = $sitemobile->title;
        }

        $mobi = Engine_Api::_()->getDbtable('modules', 'core')->getModule('mobi');
        if (isset($mobi) && !empty($mobi)) {
            $pluginTitle[] = $mobi->title;
        }

        $apptouch = Engine_Api::_()->getDbtable('modules', 'core')->getModule('apptouch');
        if (isset($apptouch) && !empty($apptouch)) {
            $pluginTitle[] = $apptouch->title;
        }

        return $pluginTitle;
    }

    /**
     * Get language array
     *
     * @return array $localeMultiOptions
     */
    public function getLanguages($onlyImageArray = false) {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            $this->setTranslate();

        //PREPARE LANGUAGE LIST
        $languageList = Zend_Registry::get('Zend_Translate')->getList();

        //PREPARE DEFAULT LANGUAGE
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = null;
            }
        }

        //INIT DEFAULT LOCAL
        $viewer = Engine_Api::_()->user()->getViewer();
        $local = ($viewer->getIdentity()) ? $viewer->locale : 'auto';
        $localeObject = new Zend_Locale($local); //Zend_Registry::get('Locale');
        $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $territories = Zend_Locale::getTranslationList('territory', $localeObject);

        $localeMultiOptions = array();
        foreach ($languageList as $key) {
            $languageName = null;
            if (!empty($languages[$key])) {
                $languageName = $languages[$key];
            } else {
                $tmpLocale = new Zend_Locale($key);
                $region = $tmpLocale->getRegion();
                $language = $tmpLocale->getLanguage();
                if (!empty($languages[$language]) && !empty($territories[$region])) {
                    $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }

            if ($languageName) {
                $localeMultiOptions[$key] = $languageName;
            } else {
                // Set the translations for zend library.
                if (!Zend_Registry::isRegistered('Zend_Translate'))
                    $this->setTranslate();

                $localeMultiOptions[$key] = Zend_Registry::get('Zend_Translate')->_('Unknown');
            }
        }

        // Get default language
        $defaultLanguage = ($viewer->getIdentity()) ? $viewer->language : $defaultLanguage;

        if (!empty($onlyImageArray))
            return $localeMultiOptions;

        return array(
            'default' => $defaultLanguage,
            'languages' => $localeMultiOptions
        );
    }

    // handles thumbnails
    public function handleSiteVideoThumbnail($type, $code = null) {
        switch ($type) {

            //youtube
            case "1":
                $thumbnail = "";
                $thumbnailSize = array('maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default');
                foreach ($thumbnailSize as $size) {
                    $thumbnailUrl = "https://i.ytimg.com/vi/$code/$size.jpg";
                    $data = @file_get_contents($thumbnailUrl);
                    if ($data && is_string($data)) {
                        $thumbnail = $thumbnailUrl;
                        break;
                    }
                }
                return $thumbnail;
            //vimeo
            case "2":
                $thumbnail = "";
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                if (isset($data->video->thumbnail_large))
                    $thumbnail = $data->video->thumbnail_large;
                else if (isset($data->video->thumbnail_medium))
                    $thumbnail = $data->video->thumbnail_medium;
                else if (isset($data->video->thumbnail_small))
                    $thumbnail = $data->video->thumbnail_small;

                return $thumbnail;
            //dailymotion
            case "4":
                $thumbnail = "";
                $thumbnailUrl = 'https://api.dailymotion.com/video/' . $code . '?fields=thumbnail_small_url,thumbnail_large_url,thumbnail_medium_url';
                $json_thumbnail = file_get_contents($thumbnailUrl);
                if ($json_thumbnail) {
                    $thumbnails = json_decode($json_thumbnail);
                    if (isset($thumbnails->thumbnail_large_url))
                        $thumbnail = $thumbnails->thumbnail_large_url;
                    else if (isset($thumbnails->thumbnail_medium_url)) {
                        $thumbnail = $thumbnails->thumbnail_medium_url;
                    } else if (isset($thumbnails->thumbnail_small_url)) {
                        $thumbnail = $thumbnails->thumbnail_small_url;
                    }
                }
                return $thumbnail;
        }
    }

    public function saveVideoThumbnail($fileName, $video) {
        $params = array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity(),
            'user_id' => $video->owner_id,
            'name' => $fileName,
        );
        $ext = ltrim(strrchr($fileName, '.'), '.');
        $thumbnail_parsed = @parse_url($fileName);

        if (@GetImageSize($fileName)) {
            $valid_thumb = true;
        } else {
            $valid_thumb = false;
        }

        if ($valid_thumb && $fileName && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {

            $file = APPLICATION_PATH . '/temporary/link_' . md5($fileName) . '.' . $ext;
            $mainPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_m.' . $ext;
            $normalPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_in.' . $ext;
            $largePath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_l.' . $ext;
            //Fetching the width and height of thumbmail
            $normalHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.height', 375);
            $normalWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
            $largeHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.height', 720);
            $largeWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
            $mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
            $mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);

            $src_fh = fopen($fileName, 'r');
            $tmp_fh = fopen($file, 'w');
            stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 30);
            // Resize image (main)
            if (file_exists($file)) {
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($mainWidth, $mainHeight)
                        ->write($mainPath)
                        ->destroy();

                // Resize image (large)
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($largeWidth, $largeHeight)
                        ->write($largePath)
                        ->destroy();

                // Resize image (normal)
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($normalWidth, $normalHeight)
                        ->write($normalPath)
                        ->destroy();
            }
            $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            // Store
            $iMain = $filesTable->createFile($mainPath, $params);
            $iLarge = $filesTable->createFile($largePath, $params);
            $iNormal = $filesTable->createFile($normalPath, $params);

            $iMain->bridge($iLarge, 'thumb.large');
            $iMain->bridge($iNormal, 'thumb.normal');
            $iMain->bridge($iMain, 'thumb.main');
            // Remove temp files
            @unlink($mainPath);
            @unlink($largePath);
            @unlink($normalPath);
            $video->photo_id = $iMain->getIdentity();
            $video->status = 1;
            $video->save();
            return $video;
        }
        return NULL;
    }

    /*
     * Set the translations for Zend library.
     */

    public function setTranslate() {
        $params = array(
            'scan' => Zend_Translate_Adapter::LOCALE_DIRECTORY,
            'logUntranslated' => true
        );

        $log = new Zend_Log();
        $log->addWriter(new Zend_Log_Writer_Null());
        $params['log'] = $log;

        // Check Locale
        $locale = Zend_Locale::findLocale();
        // Make Sure Language Folder Exist
        $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
        if ($languageFolder === false) {
            $locale = substr($locale, 0, 2);
            $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
            if ($languageFolder == false) {
                $locale = 'en';
            }
        }

        // Check which Translation Adapter has been selected
        $db = Engine_Db_Table::getDefaultAdapter();
        $translationAdapter = $db->select()
                ->from('engine4_core_settings', 'value')
                ->where('`name` = ?', 'core.translate.adapter')
                ->query()
                ->fetchColumn();

        // Use Array Translation Adapter, Loop through all Availible Translations
        if ($translationAdapter == 'array') {
            // Find all Valid Language Arrays
            // Check For Array Files
            $languagePath = APPLICATION_PATH . '/application/languages';
            // Get List of Folders
            $languageFolders = array_filter(glob($languagePath . DIRECTORY_SEPARATOR . '*'), 'is_dir');
            // Look inside Folders for PHP array
            $locale_array = array();
            foreach ($languageFolders as $folder) {
                // Get Locale code
                $locale_code = str_replace($languagePath . DIRECTORY_SEPARATOR, "", $folder);
                $locale_array[] = $locale_code;
                if (count(glob($folder . DIRECTORY_SEPARATOR . $locale_code . 'php')) == 0) {
                    // If Array files do not exist, switch to CSV
                    $translationAdapter = 'csv';
                }
            }

            $language_count = count($locale_array);
            // Add the First One
            $translate = new Zend_Translate(
                    array(
                'adapter' => 'array',
                'content' => $languagePath . DIRECTORY_SEPARATOR . $locale_array[0] . DIRECTORY_SEPARATOR . $locale_array[0] . '.php',
                'locale' => $locale_array[0])
            );
            if ($language_count > 1) {
                for ($i = 1; $i < $language_count; $i++) {
                    $translate->addTranslation(
                            array(
                                'content' => $languagePath . DIRECTORY_SEPARATOR . $locale_array[$i] . DIRECTORY_SEPARATOR . $locale_array[$i] . '.php',
                                'locale' => $locale_array[$i])
                    );
                }
            }
        }

        // Use CSV Translation Adapter
        else {
            $translate = new Zend_Translate(
                    'Csv', APPLICATION_PATH . '/application/languages', null, $params
            );
        }

        Zend_Registry::set('Zend_Translate', $translate);

        Zend_Validate_Abstract::setDefaultTranslator($translate);
        Zend_Form::setDefaultTranslator($translate);
        Zend_Controller_Router_Route::setDefaultTranslator($translate);

        return;
    }

    /**
     * Translate the language
     * 
     * @param type $language
     * @return string
     */
    private function getTranslation($defaultLanguage = null, $getLocal = null, $params = array()) {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            $this->setTranslate();

        if (empty($getLocal))
            $getLocal = $this->getLocal();

        // Check if the language sent is available in the website languages
        if (isset($_REQUEST['language']) && !empty($_REQUEST['language'])) {
            $languageList = Zend_Registry::get('Zend_Translate')->getList();
            if (in_array($_REQUEST['language'], $languageList)) {
                $getLocal = $_REQUEST['language'];
            } else {
                $getLocal = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
                if ($getLocal == 'auto') {
                    $getLocal = 'en';
                }
            }
        }

        $language = Zend_Registry::get('Zend_Translate')->translate($defaultLanguage, $getLocal);
        if (!empty($params)) {
            $tempLanguageArray = @explode("%s", $language);
            if (@is_array($tempLanguageArray)) {
                $language = '';
                foreach ($tempLanguageArray as $key => $value) {
                    $tempKey = (!empty($params[$key])) ? $params[$key] : '';
                    $language .= $value . $tempKey;
                }
            }
        }

        return (!is_array($language)) ? $language : $defaultLanguage;
    }

    /*
     * Get the array of create privacy.
     * 
     * @param $moduleName string: module name
     * @return array
     */

    public function getCreateAuthArray($moduleName) {
        $canCreateArray = array();
        $viewer = Engine_Api::_()->user()->getViewer();
//        $modName = ($moduleName == 'music') ? 'music_playlist' : $moduleName;
//        $modName = ($moduleName == 'siteevent') ? 'siteevent_event' : $moduleName;
//        $modName = ($moduleName == 'sitereview') ? 'sitereview_listing' : $moduleName;

        switch ($moduleName) {
            case 'music':
                $modName = 'music_playlist';
                break;
            case 'siteevent':
                $modName = 'siteevent_event';
                break;
            case 'sitepage':
                $modName = 'sitepage_page';
                break;
            case 'sitereview':
                $modName = 'sitereview';
                break;
            case 'sitegroup':
                $modName = 'sitegroup_group';
                break;
            case 'sitevideo':
                $modName = 'video';
                break;
            default :
                $modName = $moduleName;
        }
        if ($moduleName == 'forum')
            $canCreateArray['default'] = Engine_Api::_()->authorization()->isAllowed('forum', $viewer, 'post.create');
        else
            $canCreateArray['default'] = Engine_Api::_()->authorization()->isAllowed($modName, $viewer, 'create');

        switch ($moduleName) {
            case 'album':
            case 'classified':
                $canCreateArray['photo'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'photo');
                break;

            case 'event':
            case 'group':
                $canCreateArray['member'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'invite');
                $canCreateArray['photo'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'photo');
                $canCreateArray['discussion'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'comment');
                break;

            case 'siteevent':
                $canCreateArray['diary'] = Engine_Api::_()->authorization()->isAllowed('siteevent_diary', $viewer, 'create');
                $canCreateArray['member'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'invite');
                $canCreateArray['announcement'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'edit');
                $canCreateArray['photo'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'photo');
                $canCreateArray['video'] = Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create');
                $canCreateArray['review'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'review_create');
                $canCreateArray['packagesEnabled'] = $this->_packagesEnabled();

                break;

            case 'sitereview_listing':
                $canCreateArray['photo'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'photo');
                $canCreateArray['video'] = Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create');
                $canCreateArray['wishlist'] = Engine_Api::_()->authorization()->isAllowed($moduleName, $viewer, 'view');
                break;

            case 'sitegroup':
                $canCreateArray['packagesEnabled'] = Engine_Api::_()->sitegroup()->hasPackageEnable() ? 1 : 0;
                break;
            case 'sitepage':
                $canCreateArray['packagesEnabled'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 0);
                break;
        }

        return $canCreateArray;
    }

    /*
     * Get the array of create privacy for sitereview.
     * 
     * @param $moduleName string: module name
     * @return array
     */

    public function getCreateAuthSitereviewArray($menu) {
        $canCreateArray = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $moduleName = $menu->module;
        if (isset($menu->params) && !empty($menu->params)) {
            $params = @unserialize($menu->params);
            $listingType_id = $params['listingtype_id'];
            if (isset($listingType_id) && !empty($listingType_id)) {
                $canCreateArray['default'] = Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "create_listtype_$listingType_id");

                // Set photo creation permission
                $canCreateArray['photo'] = Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "photo_listtype_$listingType_id");

                // Set video creation permission
                $allowed_upload_video = 1;
                $allowed_upload_videoEnable = Engine_Api::_()->sitereview()->enableVideoPlugin();
                if (empty($allowed_upload_videoEnable))
                    $allowed_upload_video = 0;
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.show.video', 1)) {
                    $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
                    if (empty($allowed_upload_video_video))
                        $allowed_upload_video = 0;
                }
                $allowed_upload_video = Engine_Api::_()->authorization()->getPermission($level_id, 'sitereview_listing', "video_listtype_$listingType_id");
                if (empty($allowed_upload_video))
                    $allowed_upload_video = 0;
                $canCreateArray['video'] = $allowed_upload_video;

                // Set Wishlist creation permission
                $wishlistAllow = 0;
                if (!empty($viewer_id))
                    $wishlistAllow = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($listingType_id, 'wishlist');
                $canCreateArray['wishlist'] = $wishlistAllow;

                $canCreateArray['packagesEnabled'] = $this->_sitereviewPackageEnabled($listingType_id);
            }
        }
        if (isset($canCreateArray) && !empty($canCreateArray))
            return $canCreateArray;
    }

    /**
     * Verify a receipt and return receipt data
     *
     * @param string $receipt Base-64 encoded data
     * @param bool $isSandbox Optional. True if verifying a test receipt
     * @throws Exception If the receipt is invalid or cannot be verified
     * @return array Receipt info (including product ID and quantity)
     */
    public function getReceiptData($receipt, $transaction_id, $isSandbox = false) {
        // determine which endpoint to use for verifying the receipt
        if ($isSandbox) {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        } else {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        }

        $receipt = trim($receipt);
        $receipt = str_replace(' ', '+', $receipt);
        $itunesPassword = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.shared.secret');
        if (!isset($itunesPassword) || empty($itunesPassword))
            return false;

        // build the post data
        $postData = json_encode(
                array('receipt-data' => $receipt,
                    'password' => $itunesPassword
                )
        );

        // create the cURL request
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        // execute the cURL request and fetch response data
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);
        // ensure the request succeeded
        if ($errno != 0) {
            return false;
        }

        // parse the response data
        $data = json_decode($response);

        // ensure response data was a valid JSON string
        if (!is_object($data)) {
            return false;
        }

        // ensure the expected data is present
        if (!isset($data->status) || $data->status != 0) {
            return false;
        }

        if (isset($transaction_id) && !empty($transaction_id)) {
            if (isset($data->receipt->in_app))
                $userReceipts = $data->receipt->in_app;
            if (isset($data->latest_receipt_info))
                $latestReceipts = $data->latest_receipt_info;
        }

        foreach ($userReceipts as $userReceipt) {
            if ($userReceipt->transaction_id == $transaction_id) {
                $dataArray['receipt'] = $userReceipt;
            }
        }

        foreach ($latestReceipts as $latestReceipt) {
            if ($latestReceipt->transaction_id == $transaction_id) {
                $dataArray['latestReceipt'] = $latestReceipt;
            }
        }

        if (isset($dataArray) && !empty($dataArray))
            return $dataArray;
        else
            return false;
    }

    /**
     * Verify a user if it is subscribed to site via iOS IAP
     *
     * @param user object
     * @return bool false if not iOS subscriber else transaction id
     */
    public function hasUserIosSubscription($user) {
        $user_id = $user->getIdentity();
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
        $iosSubscriptionsTable = Engine_Api::_()->getDbtable('userSubscriptions', 'siteiosapp');

        // Get subscriptions that have expired or have finished their trial period
        // (trial is not yet implemented)
        $select = $subscriptionsTable->select()
                ->where('expiration_date <= ?', new Zend_Db_Expr('NOW()'))
                ->where('status = ?', 'expired')
                ->where('user_id = ?', $user_id)
                ->order('subscription_id DESC')
                ->limit(1);

        $isViewRowExist = $select->query()->fetchObject();

        if (isset($isViewRowExist) && !empty($isViewRowExist) && !empty($isViewRowExist->package_id)) {
            $package = Engine_Api::_()->getItem('payment_package', $isViewRowExist->package_id);

            // Check if the package has an expiration date
            if (isset($package) && !empty($package)) {
                $expiration = $package->getExpirationDate();
                if (!$expiration || !$package->hasDuration()) {
                    return false;
                }

                $isIosRowExist = $iosSubscriptionsTable->fetchRow(array(
                    'user_id = ?' => $user_id,
                    'package_id = ?' => $isViewRowExist->package_id,
                ));

                if ($isIosRowExist && isset($isIosRowExist->transaction_id) && !empty($isIosRowExist->transaction_id))
                    return $isIosRowExist->transaction_id;
                else
                    return false;
            }
        }

        return false;
    }

    /**
     * Verify if user has renewed the subscription via iOS IAP
     *
     * @param user object
     * @return bool false if not iOS subscriber else transaction id
     */
    public function hasUserIosSubscriptionExpire($user, $transaction_id) {
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
        $user_id = $user->getIdentity();

        // Get subscriptions that have expired or have finished their trial period
        $select = $subscriptionsTable->select()
                ->where('expiration_date <= ?', new Zend_Db_Expr('NOW()'))
                ->where('status = ?', 'expired')
                ->where('user_id = ?', $user_id)
                ->order('subscription_id DESC')
                ->limit(1);

        $isViewRowExist = $select->query()->fetchObject();
        $currentExpiration = $isViewRowExist->expiration_date;

        if ($isViewRowExist && $currentExpiration) {
            $isIosRowExist = $iosSubscriptionsTable->fetchRow(array(
                'transaction_id = ?' => $transaction_id
            ));

            if ($isIosRowExist) {
                $receipt = $this->getReceiptData($isIosRowExist->receipt, $transaction_id, $isIosRowExist->isSandbox);
                if (isset($receipt['latestReceipt']) && !empty($receipt['latestReceipt'])) {
                    $latestReceipt = $receipt['latestReceipt'];
                    if (isset($latestReceipt->expires_date_ms) && !empty($latestReceipt->expires_date_ms)) {
                        $seconds = $latestReceipt->expires_date_ms / 1000;
                        if ($latestReceipt->expires_date_ms > time()) {
                            $subscription = Engine_Api::_()->getItem('payment_subscription', $isViewRowExist->subscription_id);
                            $subscription->expiration_date = date('Y-m-d H:i:s', $seconds);
                            $subscription->status = 'active';
                            $subscription->save();
                            $subscription->setActive(true);
                            $subscription->onPaymentSuccess();
                        }
                    }
                }
            }
        }
    }

    private function _sitereviewPackageEnabled($listingType_id) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        // Return false if does not have create permission
        if (!Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "create_listtype_$listingType_id"))
            return 0;

        $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $listingType_id);

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewpaidlisting')) {
            if (Engine_Api::_()->sitereviewpaidlisting()->hasPackageEnable() && !empty($listingType->package)) {
                $packageCount = Engine_Api::_()->getDbTable('packages', 'sitereviewpaidlisting')->getPackageCount($listingType_id);
                if ($packageCount == 1) {
                    $package = Engine_Api::_()->getDbTable('packages', 'sitereviewpaidlisting')->getEnabledPackage($listingType_id);
                    // Return 0 if only one package & is free
                    if (($package->price == '0.00')) {
                        return 0;
                    } else {
                        return 1;
                    }
                } else {
                    return 1;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    private function _packagesEnabled() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->_hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();

        if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.6.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.1')) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid') && $this->_hasPackageEnable) {
                $packageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount();
                if ($packageCount == 1) {
                    $package = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getEnabledPackage();
                    if (($package->price == '0.00')) {
                        return 0;
                    } else
                        return 1;
                } else {
                    $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1);
                    $package_description = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.description', 1);
                    $paginator = Engine_Api::_()->getDbtable('packages', 'siteeventpaid')->getPackagesSql($viewer_id);
                    $getTotalItemCount = $paginator->getTotalItemCount();
                    if ($getTotalItemCount > 0) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            }
        }
        return 0;
    }

    public function getEnabledActionTypesAssoc() {
        $arr = array();
        foreach ($this->getActionTypes() as $type) {
            if (!$type->enabled || !$type->displayable)
                continue;
            $arr[$type->type] = $this->translate('_ACTIVITY_ACTIONTYPE_' . strtoupper($type->type));
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction') == 1) {
            unset($arr['friends_follow']);
        } else {
            unset($arr['friends']);
        }
        return $arr;
    }

    public function getActionTypes() {
        if (null === $this->_actionTypes) {
            // Only get enabled types
            //$this->_actionTypes = $this->fetchAll();
            $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
            $select = Engine_Api::_()->getDbtable('actionTypes', 'activity')->select()
                    ->where('module IN(?)', $enabledModuleNames)
            ;
            $this->_actionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->fetchAll($select);
        }

        return $this->_actionTypes;
    }

    /**
     * Get language array
     *
     * @param string $page_url
     * @return array $localeMultiOptions
     */
    public function getLanguageArray() {

        //PREPARE LANGUAGE LIST
        $languageList = Zend_Registry::get('Zend_Translate')->getList();

        //PREPARE DEFAULT LANGUAGE
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = null;
            }
        }
        //INIT DEFAULT LOCAL
        $localeObject = Zend_Registry::get('Locale');
        $languages = Zend_Locale::getTranslationList('language');
        $territories = Zend_Locale::getTranslationList('territory', $localeObject);
        $localeMultiOptions = array();
        foreach ($languageList as $key) {
            $languageName = null;
            if (!empty($languages[$key])) {
                $languageName = $languages[$key];
            } else {
                $tmpLocale = new Zend_Locale($key);
                $region = $tmpLocale->getRegion();
                $language = $tmpLocale->getLanguage();
                if (!empty($languages[$language]) && !empty($territories[$region])) {
                    $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }

            if ($languageName) {
                $localeMultiOptions[$key] = $languageName;
            } else {
                $localeMultiOptions[$key] = Zend_Registry::get('Zend_Translate')->_('Unknown (' . $key . ')');
            }
        }
        $localeMultiOptions = array_merge(array(
            $defaultLanguage => $defaultLanguage
                ), $localeMultiOptions);
        return $localeMultiOptions;
    }

    public function getContentCoverPhoto($subject) {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto'))
            return;
        if (!$subject)
            return;

        $resource_type = $subject->getType();

        if (!$resource_type)
            return;

        if (isset($subject->listingtype_id)) {
            $params = array('resource_type' => $resource_type . '_' . $subject->listingtype_id);
        } else {
            $params = array('resource_type' => $resource_type);
        }
        if (!Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->checkEnableModule($params))
            return;

        $contentName = strtolower($subject->getShortType()) . '_cover';

        $host = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $moduleName = strtolower($subject->getModuleName());
        $user = Engine_Api::_()->user()->getViewer();


        try {
            if ($subject->getType() != 'sitereview_listing') {
                if (isset($subject->$contentName)) {
                    $photo = Engine_Api::_()->getItem($moduleName . "_photo", $subject->$contentName);
                    if (isset($photo) && !empty($photo))
                        $getPhotoURL = $photo->getPhotoUrl();
                }
            } else {
                $tableName = 'engine4_sitereview_otherinfo';
                $db = Engine_Db_Table::getDefaultAdapter();
                $field = $db->query("SHOW COLUMNS FROM $tableName LIKE '$contentName'")->fetch();
                if (!empty($field)) {
                    $fieldNameValue = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($subject->listing_id, $contentName);
                    if ($fieldNameValue) {
                        $photo = Engine_Api::_()->getItem($moduleName . "_photo", $fieldNameValue);
                        if (isset($photo) && !empty($photo))
                            $getPhotoURL = $photo->getPhotoUrl();
                    }
                }
            }
            $finalPhotoURL['default_cover'] = 0;

            $permissionType = 'sitecontentcoverphoto_' . $resource_type;
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

            if (isset($user->level_id) && !empty($user->level_id) && !isset($photo)) {
                $id = $user->level_id;
                $resource = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getModuleName(array('resource_type' => $resource_type));
                if (isset($subject->listingtype_id) && $resource) {
                    $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $subject->listingtype_id);
                    $titleSinLc = strtolower($listingType->title_singular);
                    $setting = Engine_Api::_()->getApi("settings", "core")->getSetting("sitecontentcoverphoto.$resource.$titleSinLc.cover.photo.preview.level.$id.id");
                } elseif ($resource) {
                    $photo = $setting = Engine_Api::_()->getApi("settings", "core")->getSetting("sitecontentcoverphoto.$resource.cover.photo.preview.level.$id.id");
                }

                if ($resource && isset($setting) && !empty($setting)) {
                    $getPhotoURL = $photo = Engine_Api::_()->storage()->get($setting, 'thumb.cover')->map();
                    $finalPhotoURL['default_cover'] = 1;
                }
            }

            if (!empty($photo)) {
                $finalPhotoURL['cover_image'] = (strstr($getPhotoURL, 'http')) ? $getPhotoURL : $host . $getPhotoURL;
            }
            return $finalPhotoURL;
        } catch (Exception $ex) {
            // Blank Exception
        }
        return;
    }

    //response for android app
    public function responseFormat($response = null) {
        $res = array();
        if (!empty($response)) {
            foreach ($response as $key => $values) {
                $res[] = array($key => $values);
            }
        }
        return $res;
    }

    public function checkVersion($databaseVersion, $checkDependancyVersion) {

        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }

    public function setPhoto($photo, $subject) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => $subject->getType(),
            'parent_id' => $subject->getIdentity(),
            'user_id' => $subject->owner_id,
            'name' => $fileName,
        );
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
        $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($mainWidth, $mainHeight)
                ->write($mainPath)
                ->destroy();

        $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
        $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($normalWidth, $normalHeight)
                ->write($normalPath)
                ->destroy();

        $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
        $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
        // Resize image (normal)
        $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($normalLargeWidth, $normalLargeHeight)
                ->write($normalLargePath)
                ->destroy();

        // Resize image (icon)
        $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($squarePath)
                ->destroy();

        // Store
        try {
            $iMain = $filesTable->createFile($mainPath, $params);
            $iIconNormal = $filesTable->createFile($normalPath, $params);
            $iMain->bridge($iIconNormal, 'thumb.normal');
            $iIconNormalLarge = $filesTable->createFile($normalLargePath, $params);
            $iMain->bridge($iIconNormalLarge, 'thumb.medium');

            $iSquare = $filesTable->createFile($squarePath, $params);
            $iMain->bridge($iSquare, 'thumb.icon');
        } catch (Exception $e) {
            // Remove temp files
            @unlink($mainPath);
            @unlink($normalPath);
            @unlink($normalLargePath);
            @unlink($squarePath);
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
        @unlink($normalLargePath);
        @unlink($squarePath);
        // Update row
        $subject->modified_date = date('Y-m-d H:i:s');
        $subject->file_id = $iMain->file_id;
        $subject->save();

        // Delete the old file?
        if (!empty($tmpRow)) {
            $tmpRow->delete();
        }

        return $subject;
    }

    public function getVideoURL($video, $autoplay = true) {
// YouTube
        if ($video->type == 1 || $video->type == 'youtube') {
            return 'www.youtube.com/embed/' . $video->code . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "");
        } elseif ($video->type == 2 || $video->type == 'vimeo') { // Vimeo
            return 'player.vimeo.com/video/' . $video->code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video->type == 4 || $video->type == 'dailymotion') {
            return 'www.dailymotion.com/embed/video/' . $video->code . '?wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video->type == 3 || $video->type == 'upload' || $video->type == 'mydevice') { // Uploded Videos
            $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

            $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
            $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

            $host = '';
            if ($getDefaultStorageType == 'local')
                $host = !empty($staticBaseUrl) ? $staticBaseUrl : $getHost;

            $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType())->getHref();

            $video_location = strstr($video_location, 'http') ? $video_location : $host . $video_location;

            return $video_location;
        }
        elseif ($video->type == 6 || $video->type == 'embedcode' || $video->type == 'iframely') {

            if (isset($video->code) && !empty($video->code))
                return $video->code;
            else
                return '';
        }
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
            case 'upload':
                return 3;
            case 4:
            case 'dailymotion':
                return 4;
            case 5:
            case 'embedcode':
                return 5;
            case 'iframely' :
                return 6;
            default : return $type;
        }
    }

    /**
     * Getting the content URL
     * 
     * @param type $subject: Object of content
     * @return array
     */
    public function getOrder($options) {
        $i = 1;
        foreach ($options as $key => $value) {
            $orderedArray[$i] = $key;
            $i++;
        }
        return $orderedArray;
    }

    public function getPhotoTag($photo) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (empty($photo))
            return;
        foreach ($photo->tags()->getTagMaps() as $tagmap) {
            if (($viewer->getIdentity() == $tagmap->tag_id) || ($photo->owner_id == $viewer->getIdentity())) {
                $isRemove = 1;
            } else
                $isRemove = 0;

            $tags = array_merge($tagmap->toArray(), array(
                'id' => $tagmap->getIdentity(),
                'text' => $tagmap->getTitle(),
                'href' => $tagmap->getHref(),
                'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id,
                "isRemove" => $isRemove
            ));
            try {
                $subject = Engine_Api::_()->getItem('user', $tagmap->tag_id);
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
                $tags = array_merge($tags, $getContentImages);
                if (!empty($isRemove)) {
                    $menu['menus'] = array(
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Remove Tag'),
                        'name' => 'delete_tag',
                        'url' => 'tags/remove',
                        'urlParams' => array(
                            "subject_type" => $photo->getType(),
                            "subject_id" => $photo->getIdentity(),
                            "tagmap_id" => $tagmap->tagmap_id
                        )
                    );
                    $tags = array_merge($tags, $menu);
                }
            } catch (Exception $ex) {
                
            }
            $response[] = $tags;
        }
        return $response;
    }

}

?>
