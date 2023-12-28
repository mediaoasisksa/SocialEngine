<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_IndexController extends Core_Controller_Action_Standard {
    public function appPageAction() {
        $this->view->user_agent = $user_agent = $this->mobile_user_agent_switch();
        $getHost = $_SERVER['HTTP_HOST'];
        $this->view->getHost = $getHost = str_replace('www.', '', $getHost);
        $this->view->getHost = $getHost = str_replace(".", "-", $getHost);
        $this->view->hasAndroidApp = 0;
        $this->view->hasIosApp = 0;
        $this->view->values = array();

        $formValues = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteapi_tip_messages");
        if (isset($formValues) && !empty($formValues))
            $this->view->values = @unserialize($formValues);
                
        $viewer = Engine_Api::_()->user()->getViewer();
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        $this->view->defaultLanguage = ($viewer->getIdentity()) ? $viewer->language : $defaultLanguage;

        // Android App Details
        $this->view->androidParentDirectoryPath = $androidParentDirectoryPath = 'public/android-' . $getHost . '-app-builder';
        $this->view->androidCoreDirectoryPath = $androidCoreDirectoryPath = APPLICATION_PATH . '/' . $androidParentDirectoryPath;
        include $androidCoreDirectoryPath . '/settings.php';

        if (!empty($appBuilderParams)) {
            $this->view->androidAppBuilderParams = $androidAppBuilderParams = $appBuilderParams;

            if (isset($androidAppBuilderParams['package_name']) && !empty($androidAppBuilderParams['package_name'])) {
                $this->view->hasAndroidApp = 1;
                $this->view->androidCallingURL = $androidCallingURL = "https://play.google.com/store/apps/details?id=" . $androidAppBuilderParams['package_name'];

                //Redirect to Google Play if android device is detected
                if ($user_agent == 'android' && isset($androidCallingURL) && !empty($androidCallingURL)) {
                    header("Location: $androidCallingURL");
                }
            }
        }

        // IOS App Details
        $appBuilderParams = array();
        $this->view->iosParentDirectoryPath = $iosParentDirectoryPath = 'public/ios-' . $getHost . '-app-builder';
        $this->view->iosCoreDirectoryPath = $iosCoreDirectoryPath = APPLICATION_PATH . '/' . $iosParentDirectoryPath;
        include $iosCoreDirectoryPath . '/settings.php';
        if (!empty($appBuilderParams)) {
            $this->view->iosAppBuilderParams = $iosAppBuilderParams = $appBuilderParams;

            if (isset($iosAppBuilderParams['package_name']) && !empty($iosAppBuilderParams['package_name'])) {
                $this->view->hasIosApp = 1;
                $getAppleResult = file_get_contents("https://itunes.apple.com/lookup?bundleId=" . $iosAppBuilderParams['package_name']);
                if (isset($getAppleResult) && !empty($getAppleResult)) {
                    $getDecodedAppleResponce = Zend_Json::decode($getAppleResult);
                    $this->view->iosCallingURL = $iosCallingURL = $getDecodedAppleResponce['results'][0]['trackViewUrl'];

                    // Redirect to App Store if iphone or ipad is detected
                    if (($user_agent == 'iphone' || $user_agent == 'ipad') && isset($iosCallingURL) && !empty($iosCallingURL)) {
                        header("Location: $iosCallingURL");
                    }
                }
            }
        }
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
