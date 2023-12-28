<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_SeaocoresUpgradeController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //Delete widget if install seaocore plugin.
        $select = new Zend_Db_Select($db);
        $content_id = $select
                ->from('engine4_core_content')
                ->where('name =?', 'socialengineaddon.socialengineaddones-lightbox')
                ->query()
                ->fetchColumn();

        if (!empty($content_id)) {
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'socialengineaddon.socialengineaddones-lightbox' LIMIT 1;");
        }

        //Delete socialengineaddons modules folder message.
        $pathmodule = APPLICATION_PATH . "/application/modules/Socialengineaddon";
//     if (@is_dir($pathmodule)) {
// 			$this->view->flag = 1;
//     }

        $notInclude = array('advancedactivity', 'sitelike', 'sitepageoffer', 'sitepagebadge', 'sitepagediscussion', 'sitepagelikebox', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'poke', 'sitealbum', 'sitepageinvite', 'siteslideshow', 'suggestion', 'userconnection', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitebusinessoffer', 'sitebusinessdiscussion', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'document', 'list', 'recipe', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitebusiness', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview');

        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->hasModule("socialengineaddon");

        $module_table = Engine_Api::_()->getDbTable('modules', 'core');
        $module_name = $module_table->info('name');
        $select = $module_table->select()
                        ->from($module_name)
                        ->where($module_name . '.version < ?', '4.2.3')
                        ->where($module_name . '.name IN (?)', (array) $notInclude)
                        ->limit(1)
                        ->query()->fetchColumn();

        if (empty($select) && !empty($moduleEnabled)) {
            //for delete mesage.
            $this->view->flag_delete = 1;
        } else if (empty($moduleEnabled) && @is_dir($pathmodule)) {
            $this->view->flag_delete = 2;
        }



        if (!empty($_POST['level_id'])) {
            $show_table = $_POST['level_id'];
        } else {
            $show_table = 1;
        }
        $sitereviewListingTypeVersion = null;
        $this->view->show_table = $show_table;
        $rss = Zend_Feed::import('http://www.socialengineaddons.com/plugins/feed');
        $channel = array(
            'title' => $rss->title(),
            'link' => $rss->link(),
            'description' => $rss->description(),
            'items' => array()
        );
        // Loop over each channel item and store relevant data
        foreach ($rss as $item) {
            $tempReviewListingTypePlugin = array();
            $product_type = $item->ptype();

            if ($product_type == 'sitemobileandroidapp')
                $product_type = 'siteandroidapp';

            if ($product_type == 'sitemobileiosapp')
                $product_type = 'siteiosapp';

            $modules_info = $this->module_info($product_type);
            if (!empty($modules_info['name']) && strstr($modules_info['name'], 'sitepage')) {
                $this->view->isSitepage = TRUE;
                $this->view->enabledPagePlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
            }
            if (!empty($modules_info['name']) && strstr($modules_info['name'], 'sitebusiness')) {
                $this->view->isSitebusiness = TRUE;
                $this->view->enabledBusinessPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
            }
            if (!empty($modules_info['name']) && strstr($modules_info['name'], 'sitegroup')) {
                $this->view->isSitegroup = TRUE;
                $this->view->enabledGroupPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');
            }
            if (!empty($modules_info['name']) && strstr($modules_info['name'], 'siteevent')) {
                $this->view->isSiteevent = TRUE;
                $this->view->enabledEventPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
            }
            if (!empty($modules_info['name']) && strstr($modules_info['name'], 'sitereview')) {
                $this->view->isSitereview = TRUE;
                $this->view->enabledReviewPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview');
            }
            $license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting($modules_info['key']);
            $plugin_info['title'] = $item->title();
            $plugin_info['ptype'] = $product_type;
            $plugin_info['product_version'] = $item->version();
            $plugin_info['key'] = $license_key;
            $plugin_info['link'] = $item->link();
            $plugin_info['price'] = $item->price();
            $plugin_info['socialengine_url'] = $item->socialengine_url();
            $plugin_info['running_version'] = !empty($modules_info['status']) ? $modules_info['version'] : 0;
            $plugin_info['name'] = $modules_info['name'];
            $product_images = explode("::", $item->image());

            $tempVersion = $item->version();
            if ($product_type === 'sitereview') {
                $sitereviewListingTypeVersion = $tempVersion;
            }

            if (isset($plugin_info['ptype']) && strstr($plugin_info['ptype'], 'sitevideo')) {
                $sitevideoIntegration['title'] = 'Advanced Videos - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension';
                $sitevideoIntegration['ptype'] = 'sitevideointegration';
//        $sitevideoIntegration['product_version'] = $item->version();
                $sitevideoIntegration['key'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideointegration.lsettings');
                $sitevideoIntegration['link'] = 'https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension';
                $listingTypeGetModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitevideointegration');
                $sitevideoIntegration['running_version'] = !empty($listingTypeGetModule) ? $listingTypeGetModule->version : '';
                $sitevideoIntegration['name'] = 'sitevideointegration';
                $channel['items'][$tempReviewListingTypePlugin['ptype']] = $tempReviewListingTypePlugin;
            }

            if (isset($plugin_info['ptype']) && strstr($plugin_info['ptype'], 'document')) {
                $documentintegration['title'] = 'Documents Sharing - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension';
                $documentintegration['ptype'] = 'documentintegration';
                $documentintegration['product_version'] = $plugin_info['version'];
                $documentintegration['key'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('documentintegration.lsettings');
                $documentintegration['link'] = 'https://www.socialengineaddons.com/documentextensions/socialengine-documents-sharing-pages-businesses-groups-listings-events-stores-extension';
                $listingTypeGetModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('documentintegration');
                $documentintegration['running_version'] = !empty($listingTypeGetModule) ? $listingTypeGetModule->version : '';
                $documentintegration['name'] = 'documentintegration';
                $channel['items']['documentintegration'] = $documentintegration;
            }

            $channel['items'][$plugin_info['ptype']] = $plugin_info;
            if (!empty($tempReviewListingTypePlugin)) {
                $channel['items'][$tempReviewListingTypePlugin['ptype']] = $tempReviewListingTypePlugin;
            }
        }

        if (isset($channel['items']['document']['product_version']) && !empty($documentintegration) && !empty($channel['items']['document']['product_version'])) {
            $documentintegration['product_version'] = $channel['items']['document']['product_version'];
            $channel['items']['documentintegration'] = $documentintegration;
        }

        $doWeHaveOldDocumentVersion = $this->_doWeHaveOldDocument();
        if(!empty($doWeHaveOldDocumentVersion) && isset($channel['items']['document']))
            unset($channel['items']['document']);
        
        $this->view->sitereviewListingTypeVersion = $sitereviewListingTypeVersion;
        $this->view->channel = $channel['items'];
    }

    public function module_info($product_type) {
        if ($product_type == "sponsoredstories")
            $product_type = 'communityadsponsored';

        switch ($product_type) {
            case 'userconnection':
                $name = 'userconnection';
                $key_firld = 'user.licensekey';
                break;
            case 'feedbacks':
                $name = 'feedback';
                $key_firld = 'feedback.license_key';
                break;
            case 'suggestion':
                $name = 'suggestion';
                $key_firld = 'suggestion.controllersettings';
                break;
            case 'peopleyoumayknow':
                $name = 'peopleyoumayknow';
                $key_firld = 'pymk.controllersettings';
                break;
            case 'siteslideshow':
                $name = 'siteslideshow';
                $key_firld = 'siteslideshow.controllersettings';
                break;
            case 'mapprofiletypelevel':
                $name = 'mapprofiletypelevel';
                $key_firld = 'mapprofiletypelevel.controllersettings';
                break;
            case 'documentsv4':
                $name = 'document';
                $key_firld = 'document.controllersettings';
                break;
            case 'groupdocumentsv4':
                $name = 'groupdocument';
                $key_firld = 'groupdocument.controllersettings';
                break;
            case 'backup':
                $name = 'dbbackup';
                $key_firld = 'dbbackup.controllersettings';
                break;
            case 'mcard':
                $name = 'mcard';
                $key_firld = 'mcard.controllersettings';
                break;
            case 'like':
                $name = 'sitelike';
                $key_firld = 'sitelike.controllersettings';
                break;
            case 'seaddons-core':
                $name = 'seaocore';
                $key_firld = '';
                break;
            default:
                $name = $product_type;
                $key_firld = $product_type . '.lsettings';
                break;
        }
        $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
        $moduleName = $moduleTable->info('name');
        $select = $moduleTable->select()
                ->setIntegrityCheck(false)
                ->from($moduleName, array('name', 'version'))
                ->where('name = ?', $name)
                ->where('enabled = ?', 1)
                ->limit(1);
        $module_info = $select->query()->fetchAll();
        if (!empty($module_info)) {
            $module_info_array['version'] = $module_info[0]['version'];
            $module_info_array['name'] = $module_info[0]['name'];
            $module_info_array['status'] = 1;
        } else {
            $module_info_array['status'] = 0;
            $module_info_array['name'] = 0;
        }
        $module_info_array['key'] = $key_firld;

        return $module_info_array;
    }

    private function _doWeHaveOldDocument() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'document' => '4.8.12'
        );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                        ->where('name = ?', "$key")
                        ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

//				$isModSupport = strcasecmp($getModVersion->version, $value);
                $running_version = $getModVersion->version;
                $product_version = $value;
                $shouldUpgrade = false;
                if (!empty($running_version) && !empty($product_version)) {
                    $temp_running_verion_2 = $temp_product_verion_2 = 0;
                    if (strstr($product_version, "p")) {
                        $temp_starting_product_version_array = @explode("p", $product_version);
                        $temp_product_verion_1 = $temp_starting_product_version_array[0];
                        $temp_product_verion_2 = $temp_starting_product_version_array[1];
                    } else {
                        $temp_product_verion_1 = $product_version;
                    }
                    $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


                    if (strstr($running_version, "p")) {
                        $temp_starting_running_version_array = @explode("p", $running_version);
                        $temp_running_verion_1 = $temp_starting_running_version_array[0];
                        $temp_running_verion_2 = $temp_starting_running_version_array[1];
                    } else {
                        $temp_running_verion_1 = $running_version;
                    }
                    $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


                    if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
                        $shouldUpgrade = true;
                    }
                }

                if (!empty($shouldUpgrade)) {
                    $finalModules[$key] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

}
