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
class Sitecore_Widget_SeaocoresUpgradeController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
//    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $this->view->type = $type = $this->_getParam('type', null);
    //Delete widget if install seaocore plugin.
//    $select = new Zend_Db_Select($db);
//    $content_id = $select
//      ->from('engine4_core_content')
//      ->where('name =?', 'socialengineaddon.socialengineaddones-lightbox')
//      ->query()
//      ->fetchColumn();
//
//    if( !empty($content_id) ) {
//      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'socialengineaddon.socialengineaddones-lightbox' LIMIT 1;");
//    }
    //Delete socialengineaddons modules folder message.
//    $pathmodule = APPLICATION_PATH . "/application/modules/Socialengineaddon";
//     if (@is_dir($pathmodule)) {
//          $this->view->flag = 1;
//     }
//    $notInclude = array('advancedactivity', 'sitelike', 'sitepageoffer', 'sitepagebadge', 'sitepagediscussion', 'sitepagelikebox', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'poke', 'sitealbum', 'sitepageinvite', 'siteslideshow', 'suggestion', 'userconnection', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitebusinessoffer', 'sitebusinessdiscussion', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'document', 'list', 'recipe', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitebusiness', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview');

//    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->hasModule("socialengineaddon");
//
//    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
//    $module_name = $module_table->info('name');
//    $select = $module_table->select()
//        ->from($module_name)
//        ->where($module_name . '.version < ?', '4.2.3')
//        ->where($module_name . '.name IN (?)', (array) $notInclude)
//        ->limit(1)
//        ->query()->fetchColumn();

//    if( empty($select) && !empty($moduleEnabled) ) {
//      //for delete mesage.
//      $this->view->flag_delete = 1;
//    } else if( empty($moduleEnabled) && @is_dir($pathmodule) ) {
//      $this->view->flag_delete = 2;
//    }



    if( !empty($_POST['level_id']) ) {
      $show_table = $_POST['level_id'];
    } else {
      $show_table = 1;
    }
    $purchasedProdcuts = Engine_Api::_()->sitecore()->getPurchasedPluginsInfo();
    $purchasedProdcutsSKU = array_keys($purchasedProdcuts);
    // SEAO core PLUGIN DO NOT COME IN UPGRADE SOME TIME
    if(!in_array('seaddons-core', $purchasedProdcutsSKU)) {
      $purchasedProdcutsSKU[] = 'seaddons-core';
    }
    $sitereviewListingTypeVersion = null;
    $this->view->show_table = $show_table;
    $enabled = 1;
    $pluginInfoUrls = array('http://www.socialengineaddons.com/plugins/feed');
    if( $type == 'disabled' ) {
      $enabled = 0;
      $pluginInfoUrls = array('http://www.socialengineaddons.com/plugins/feed',
        'http://www.socialengineaddons.com/groupextensions/feed',
        'http://www.socialengineaddons.com/bizextensions/feed',
        'http://www.socialengineaddons.com/eventextensions/feed',
        'http://www.socialengineaddons.com/extensions/feed',
        'http://www.socialengineaddons.com/reviewextensions/feed',
        'http://www.socialengineaddons.com/themes/feed');
    }
    //Zend_Feed::setHttpClient(new Zend_Http_Client(null, array('timeout' => 60, 'adapter' => 'Zend_Http_Client_Adapter_Curl')));
    $rss = Zend_Feed::import('http://www.socialengineaddons.com/plugins/feed'); //THIS IS JUST TO TAKE COMMON TITLE AND DESC. 
    $channel = array(
      'title' => $rss->title(),
      'link' => $rss->link(),
      'description' => $rss->description(),
      'items' => array()
    );
    // Loop over each channel item and store relevant data
    $session = new Zend_Session_Namespace('SEAOSITE_UserAuth');
    foreach( $pluginInfoUrls as $url ) {
      $rss = Zend_Feed::import($url);
      foreach( $rss as $item ) {
        $tempReviewListingTypePlugin = array();
        $product_type = $item->ptype();
        if( $item->product_grade() == 'se_un_certifeid' && in_array('certified-' . $product_type, $purchasedProdcutsSKU) ) {
          continue;
        }
        if( !in_array($product_type, $purchasedProdcutsSKU) || (!$session->isAllowAll && $item->product_grade() == 'se_certifeid' )) {
          continue;
        }
        if( $item->product_grade() == 'se_certifeid' ) {
          $product_type = str_replace('certified-', '', $product_type);
        }

        if( in_array($product_type, array('sitereaction', 'sitetagcheckin', 'sitehashtag')) ) {
          continue;
        }
        if( $product_type == 'sitemobileandroidapp' ) {
          $product_type = 'siteandroidapp';
        }

        if( $product_type == 'sitemobileiosapp' ) {
          $product_type = 'siteiosapp';
        }

        $modules_info = $this->module_info($product_type, $enabled);
        if( !empty($modules_info['name']) && strstr($modules_info['name'], 'sitepage') ) {
          $this->view->isSitepage = TRUE;
          $this->view->enabledPagePlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
        }
        if( !empty($modules_info['name']) && strstr($modules_info['name'], 'sitebusiness') ) {
          $this->view->isSitebusiness = TRUE;
          $this->view->enabledBusinessPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
        }
        if( !empty($modules_info['name']) && strstr($modules_info['name'], 'sitegroup') ) {
          $this->view->isSitegroup = TRUE;
          $this->view->enabledGroupPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');
        }
        if( !empty($modules_info['name']) && strstr($modules_info['name'], 'siteevent') ) {
          $this->view->isSiteevent = TRUE;
          $this->view->enabledEventPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
        }
        if( !empty($modules_info['name']) && strstr($modules_info['name'], 'sitereview') ) {
          $this->view->isSitereview = TRUE;
          $this->view->enabledReviewPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview');
        }
        $license_key = $item->product_grade() !== 'se_certifeid' ? Engine_Api::_()->getApi('settings', 'core')->getSetting($modules_info['key']) : null;
        $plugin_info['title'] = $item->title();
        $plugin_info['ptype'] = $item->ptype();
        $plugin_info['product_version'] = $item->version();
        $plugin_info['key'] = $license_key;
        $plugin_info['link'] = $item->link();
        $plugin_info['price'] = $item->price();
        $plugin_info['socialengine_url'] = $item->socialengine_url();
        $plugin_info['running_version'] = !empty($modules_info['status']) ? $modules_info['version'] : 0;
        $plugin_info['name'] = $modules_info['name'];
        $product_images = explode("::", $item->image());

        $tempVersion = $item->version();
        if( $product_type === 'sitereview' ) {
          $sitereviewListingTypeVersion = $tempVersion;
        }

        if( strstr($product_type, 'sitevideo') ) {
          $sitevideoIntegration['title'] = 'Advanced Videos - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension';
          $sitevideoIntegration['ptype'] = 'sitevideointegration';
          //        $sitevideoIntegration['product_version'] = $item->version();
          $sitevideoIntegration['key'] = $item->product_grade() !== 'se_certifeid' ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideointegration.lsettings') : null;
          $sitevideoIntegration['link'] = 'https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension';
          $listingTypeGetModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitevideointegration');
          $sitevideoIntegration['running_version'] = !empty($listingTypeGetModule) ? $listingTypeGetModule->version : '';
          $sitevideoIntegration['name'] = 'sitevideointegration';
          $channel['items'][$tempReviewListingTypePlugin['ptype']] = $tempReviewListingTypePlugin;
        }

        if( strstr($product_type, 'document') ) {
          $documentintegration['title'] = 'Documents Sharing - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension';
          $documentintegration['ptype'] = 'documentintegration';
          $documentintegration['product_version'] = $plugin_info['version'];
          $documentintegration['key'] = $item->product_grade() !== 'se_certifeid' ? Engine_Api::_()->getApi('settings', 'core')->getSetting('documentintegration.lsettings'): null;
          $documentintegration['link'] = 'https://www.socialengineaddons.com/documentextensions/socialengine-documents-sharing-pages-businesses-groups-listings-events-stores-extension';
          $listingTypeGetModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('documentintegration');
          $documentintegration['running_version'] = !empty($listingTypeGetModule) ? $listingTypeGetModule->version : '';
          $documentintegration['name'] = 'documentintegration';
          $channel['items']['documentintegration'] = $documentintegration;
        }

        $channel['items'][$item->ptype()] = $plugin_info;
        if( !empty($tempReviewListingTypePlugin) ) {
          $channel['items'][$tempReviewListingTypePlugin['ptype']] = $tempReviewListingTypePlugin;
        }
      }
    }

    if( isset($channel['items']['document']['product_version']) && !empty($documentintegration) && !empty($channel['items']['document']['product_version']) ) {
      $documentintegration['product_version'] = $channel['items']['document']['product_version'];
      $channel['items']['documentintegration'] = $documentintegration;
    }
    if( empty($enabled) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('documentintegration') ) {
      unset($channel['items']['documentintegration']);
    }
    if( empty($enabled) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideointegration') ) {
      unset($channel['items']['sitevideointegration']);
    }

    // DO NOT UPGRADE DOCUMENT IF ITS VERSION IS LESS THAN 4.8.12 [OLDER DOCUMET PLUGIN]
    if( isset($channel['items']['document']) && Engine_Api::_()->seaocore()->checkVersion($channel['items']['document']['running_version'], '4.8.12') != 1 ) {
      unset($channel['items']['document']);
    }
    $this->view->sitereviewListingTypeVersion = $sitereviewListingTypeVersion;
    $this->view->channel = $channel['items'];
  }

  public function module_info($product_type, $enabled)
  {
    if( $product_type == "sponsoredstories" )
      $product_type = 'communityadsponsored';

    switch( $product_type ) {
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
        $name = trim($product_type);
        $key_firld = $product_type . '.lsettings';
        break;
    }
    $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
    $moduleName = $moduleTable->info('name');
    $select = $moduleTable->select()
      ->setIntegrityCheck(false)
      ->from($moduleName, array('name', 'version'))
                ->where('name = ?', $name)
      ->where('enabled = ?', $enabled)
      ->limit(1);
    $module_info = $select->query()->fetchAll();
    if( !empty($module_info) ) {
      $module_info_array['version'] = $module_info[0]['version'];
      $module_info_array['name'] = $module_info[0]['name'];
      $module_info_array['status'] = 1;
    } else {
      $module_info_array['status'] = 0;
      $module_info_array['name'] = 'Not found:'.$name;
    }
    $module_info_array['key'] = $key_firld;

    return $module_info_array;
  }
}