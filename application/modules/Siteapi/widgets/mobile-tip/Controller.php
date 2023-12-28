<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Controller.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Widget_MobileTipController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->logo = $logo = $this->_getParam('logo', '');
        $coreDirectoryPath = '';
        $selectedLanguage = $this->view->translate()->getLocale();
        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting("core.general.site.title");
        $defaultTipMessages = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteapi_tip_messages");
        $defaultTipMessages = !empty($defaultTipMessages) ? @unserialize($defaultTipMessages) : '';
        $defaultMessages = isset($defaultTipMessages['siteapi_tip_message_' . $selectedLanguage]) ? $defaultTipMessages['siteapi_tip_message_' . $selectedLanguage] : '<h3>Connect and Share</h3><br />Download <b>[SITE_TITLE]</b> App Now.';
        $this->view->defaultMessages = str_replace("[SITE_TITLE]", $siteTitle, $defaultMessages);

        $this->view->user_agent = $user_agent = $this->mobile_user_agent_switch();
        $getHost = $_SERVER['HTTP_HOST'];
        $this->view->getHost = $getHost = str_replace('www.', '', $getHost);
        $this->view->getHost = $getHost = str_replace(".", "-", $getHost);

        if ($user_agent == 'android') {
            $this->view->parentDirectoryPath = $parentDirectoryPath = 'public/android-' . $getHost . '-app-builder';
            $this->view->coreDirectoryPath = $coreDirectoryPath = APPLICATION_PATH . '/' . $parentDirectoryPath;
        } elseif (($user_agent == 'ipad') || ($user_agent == 'iphone')) {
            $this->view->parentDirectoryPath = $parentDirectoryPath = 'public/ios-' . $getHost . '-app-builder';
            $this->view->coreDirectoryPath = $coreDirectoryPath = APPLICATION_PATH . '/' . $parentDirectoryPath;
        }

        if (!is_dir($coreDirectoryPath))
            return $this->setNoRender();

        include $coreDirectoryPath . '/settings.php';

        if (!empty($appBuilderParams))
            $this->view->appBuilderParams = $appBuilderParams;

        if (!isset($appBuilderParams['package_name']) || empty($appBuilderParams['package_name']))
            return $this->setNoRender();
    }

    /*
     * 	Mobile device detection
     */

    function mobile_user_agent_switch() {
        $device = '';

        if (stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
            $device = "ipad";
        } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
            $device = "iphone";
        } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'blackberry')) {
            $device = "blackberry";
        } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'android')) {
            $device = "android";
        }

        if ($device) {
            return $device;
        } return false;
    }

}

?>