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
class Core_IndexController extends Siteapi_Controller_Action_Standard {

    protected $_getAPPBuilderBaseURL = 'public/app-builder';
    protected $_getAPPBuilderSettingsFileName = 'settings.php';

    /**
     * Getting the enabled and allowed modules.
     *
     * @return array
     */
    public function getEnabledModulesAction() {
// Validate request methods
        $this->validateRequestMethod();
        $getEnabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $this->respondWithSuccess($getEnabledModuleNames);
    }

    public function getDefaultLanguageAction() {
        $this->validateRequestMethod();
        $getDefaultLanguages = $this->getLanguages();
        $this->respondWithSuccess($getDefaultLanguages);
    }

    public function getServerSettingsAction() {
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;
        // member level quota
        if (null !== $user_id && null !== $level_id) {
            $space_limit = (int) Engine_Api::_()->authorization()->getPermission($level_id, 'user', 'quota');
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $tableName = $storage->info('name');
            $space_used = (int) $storage->select()
                            ->from($tableName, new Zend_Db_Expr('SUM(size) AS space_used'))
                            ->where("user_id = ?", (int) $user_id)
                            ->query()
                            ->fetchColumn(0);

            if ($space_limit > 0) {
                $getDefaultSettings['rest_space'] = ($space_limit - $space_used) > 0 ? $space_limit - $space_used : 0;
            } else {
                $getDefaultSettings['rest_space'] = -1;
            }
        }
        
        
        $max_upload = self::_convert_size(ini_get('upload_max_filesize'));
        $max_post = self::_convert_size(ini_get('post_max_size'));
        $memory_limit = self::_convert_size(ini_get('memory_limit'));
        $limit = min($max_upload, $max_post, $memory_limit);
        $getDefaultSettings['upload_max_size_limit'] = $limit;
        $getDefaultSettings['upload_max_size'] = $limit.'M';
        $this->respondWithSuccess($getDefaultSettings);
    }

    /**
     * Get dashboard menus
     *
     * @return array $response
     */
    public function getDashboardMenusAction() {
        $this->validateRequestMethod();
        $categoryName = '';
        $response = $menuArray = array();
        $type = $this->getRequestParam('type', 'android');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $browse_as_guest = $this->getRequestParam('browse_as_guest', false);
        try {
// Getting the dashboard menus.
            $getLocations = $this->getLocations();
            $viewer = Engine_Api::_()->user()->getViewer();
            $table = ($type === 'ios') ? Engine_Api::_()->getDbtable('menus', 'siteiosapp') : Engine_Api::_()->getDbtable('menus', 'siteandroidapp');

// Synchroniz liting type to menu table
            if (($type == 'android') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview") && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereviewlistingtype"))
                Engine_Api::_()->getApi('core', 'siteandroidapp')->synchroniseDashboardMenus();


// Synchroniz liting type to menu table
            if (($type == 'ios') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview") && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereviewlistingtype"))
                Engine_Api::_()->getApi('core', 'siteiosapp')->synchroniseDashboardMenus();

            $select = $table->getSelect(array('status' => 1));
            $mneuObj = $table->fetchAll($select);

            foreach ($mneuObj as $menu) {

//                if ($menu->type == 'category') {
//                    $categoryName = $menu->dashboard_label;
//                    continue;
//                }
// By passin case if version lessthen the set versions.
                if (isset($menu->params) && !empty($menu->params)) {
                    $params = @unserialize($menu->params);
                    if (
                            ($type == 'android') && isset($params['version']) && !empty($params['version']) && _ANDROID_VERSION && _ANDROID_VERSION < $params['version']
                    )
                        continue;

                    if (
                            ($type == 'ios') && isset($params['version']) && !empty($params['version']) && _IOS_VERSION
                    ) {
                        if (version_compare(_IOS_VERSION, $params['version']) < 1)
                            continue;
                    }
                }

// Remove the following query whenever Sitepage release in our Android App                
                if (($menu->name == 'core_main_sitepage') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android') || (_CLIENT_TYPE == 'both')) && _ANDROID_VERSION && _ANDROID_VERSION < 1.7)
                    continue;

                if (($menu->name == 'core_main_provider' || $menu->name == 'service_wishlist' || $menu->name == 'core_main_services' || $menu->name == 'provider_wishlist' || $menu->dashboard_label == 'My Appointments' || $menu->dashboard_label == 'Service Bookings') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android') || (_CLIENT_TYPE == 'both')) && _ANDROID_VERSION && _ANDROID_VERSION < '4.3.0' )
                    continue;

                if (($menu->name == 'core_main_provider' || $menu->name == 'service_wishlist' || $menu->name == 'core_main_services' || $menu->name == 'provider_wishlist' || $menu->dashboard_label == 'My Appointments' || $menu->dashboard_label == 'Service Bookings') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'ios') || (_CLIENT_TYPE == 'both')) && _IOS_VERSION && _IOS_VERSION < '3.1.0' )
                    continue;

                if (($menu->name == 'core_main_cometchat') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android') || (_CLIENT_TYPE == 'both')) && _ANDROID_VERSION && _ANDROID_VERSION < '1.7.8')
                    continue;

                if (($menu->name == 'core_main_sitepage') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'ios') || (_CLIENT_TYPE == 'both')) && _IOS_VERSION) {
                    if (version_compare(_IOS_VERSION, '1.5.6') < 1)
                        continue;
                }
                
               if (($menu->name == 'core_main_friends') && _CLIENT_TYPE && (_CLIENT_TYPE == 'android') && _ANDROID_VERSION) {
                    if (version_compare(_ANDROID_VERSION, '3.4.2') < 1)
                        continue;
                }

// spread_the_word icon will not come in case of 1.4 OR less versions.
                if ($type == 'ios') {
                    $version = $this->getRequestParam('version', '1.4');
                    $version = (int) @str_replace(".", "", $version);
                    if (($version <= 14) && ($menu->name === 'spread_the_word'))
                        continue;
                }

                if (($menu->show == 'login') && !$viewer->getIdentity())
                    continue;

                if (($menu->show == 'logout') && $viewer->getIdentity())
                    continue;
                
                $canSendMessage='';
                if($viewer && $viewer->getIdentity())
                     $canSendMessage = Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth');
            if($menu->name == 'core_mini_messages' && (empty($canSendMessage) || $canSendMessage =='none')){
                continue;
            }

// If available module not enabled.
                if (isset($menu->module) && !empty($menu->module) && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($menu->module))
                    continue;

// Validate authrization view permission.
                if (isset($menu->module) && !empty($menu->module) && !in_array($menu->module, array("core", "user", "activity", "messages", "advancedactivity", "sitetagcheckin", "sitereview", "siteevent", "sitegroup", "sitepage", "sitestore", "sitestoreproduct", "sitestoreoffer", "siteapi", "sitevideo"))
                ) {
                    $itemType = ($menu->module == 'music') ? 'music_playlist' : $menu->module;
                    //   if (!Engine_Api::_()->authorization()->isAllowed($itemType, $viewer, 'view'))
                    //    continue;
                }


                switch ($menu->module) {
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
                        $listingtype_id = $params['listingtype_id'];
                        break;
                    case 'sitegroup':
                        $modName = 'sitegroup_group';
                        break;
                    case 'sitevideo':
                        $modName = 'video';
                        break;
                    case 'sitestoreproduct':
                    case 'sitestoreoffer':
                    case 'sitestore':
                        $modName = 'sitestore_store';
                        break;
                    default :
                        $modName = $menu->module;
                }

                if ($type == 'android' && isset($menu->siteandroidapp_menucolor) && !empty($menu->siteandroidapp_menucolor)) {
                    $color = $menu->siteandroidapp_menucolor;
                } elseif ($type == 'ios' && isset($menu->siteiosapp_menucolor) && !empty($menu->siteiosapp_menucolor)) {
                    $color = $menu->siteiosapp_menucolor;
                } else
                    $color = '';

                if ($menu->module == 'sitereview') {
                    $memberView = $this->_helper->requireAuth()->setAuthParams('sitereview_listing', null, "view_listtype_$listingtype_id")->isValid();
                } else
                    $memberView = $this->_helper->requireAuth()->setAuthParams($modName, null, 'view')->isValid();

                $memberView = !empty($memberView) ? 1 : 0;

                if (strstr($menu->dashboard_label, 'Terms Of Service')) {
                    $menu->dashboard_label = Engine_Api::_()->getApi('Core', 'siteapi')->translate(str_replace('Terms Of Service', 'Terms of Service', $menu->dashboard_label));
                }

// Version condition for Advanced Event 
                if (($type == 'ios') && _CLIENT_TYPE && (_CLIENT_TYPE == 'both')) {
                    if ($menu->name == 'core_main_siteevent')
                        continue;
                }
                $ticket = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1);
               
                if (($menu->name == 'siteevent_ticket') && (empty($ticket) || empty($viewer_id))) {
                    continue;
                }
                
                if(($menu->name == 'core_main_calendars' || $menu->name == 'core_main_calendar') && (!$this->_helper->requireAuth()->setAuthParams('calendar', $viewer, 'view')->isValid()))
                    continue; 

// Version condition for Advanced Event 
                if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION < '1.5.2') {
                    if ($menu->name == 'sitereview_wishlist')
                        continue;
                }


                if (($menu->name == 'core_main_sitegroup') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android') || (_CLIENT_TYPE == 'both')) && _ANDROID_VERSION && _ANDROID_VERSION < '1.7.1')
                    continue;

                if (($menu->name == 'core_main_sitegroup') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'ios') || (_CLIENT_TYPE == 'both'))) {
                    if (version_compare(_IOS_VERSION, '1.7.9') < 1)
                        continue;
                }

                // Version condition for Advanced Event 
                if (($type == 'ios') && _CLIENT_TYPE && (_CLIENT_TYPE == 'both')) {
                    if ($menu->name == 'core_main_siteevent' || $menu->name == 'sitereview_listing')
                        continue;
                }

                // Version condition for Sitereview Plugin 
                if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION < '1.5.2') {
                    if ($menu->name == 'sitereview_wishlist' || $menu->name == 'sitereview_listing')
                        continue;
                }

                //rate us menu for ios
                if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION < '2.3.9') {
                    if ($menu->name == 'core_main_rate')
                        continue;
                }

                if (($menu->name == 'crowd_funding_main') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android') || (_CLIENT_TYPE == 'both')) && _ANDROID_VERSION && _ANDROID_VERSION < '3.3')
                    continue;
                
                if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && _IOS_VERSION && version_compare(_IOS_VERSION,'2.5.6') == -1) {
                    if ($menu->name == 'core_main_app_tour')
                        continue;
                }

                if (($menu->name == 'core_main_app_tour') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android') || (_CLIENT_TYPE == 'both')) && _ANDROID_VERSION && _ANDROID_VERSION < '3.0')
                    continue;
                
                if (($menu->name == 'core_main_multicurrency') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android') || (_CLIENT_TYPE == 'both')) && _ANDROID_VERSION && _ANDROID_VERSION < '3.5')
                    continue;
                
                 if (($menu->name == 'core_main_multicurrency') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'ios') || (_CLIENT_TYPE == 'both')) && _IOS_VERSION && _IOS_VERSION < '2.6')
                    continue;
                 

                $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
                $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                $baseParentUrl = @trim($baseParentUrl, "/");
                if ($menu->module == 'sitereview' && $menu->name != 'sitereview_wishlist') {
                    if (isset($menu->params) && !empty($menu->params)) {
                        $params = @unserialize($menu->params);
                        $menuArray[] = array(
                            'name' => $menu->name,
                            'type' => $menu->type,
                            'label' => $this->translate($menu->dashboard_label),
                            'color' => $color,
                            'headerLabel' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($menu->header_label),
                            'header_label_singular' => (isset($params['header_label_singular']) && !empty($params['header_label_singular'])) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($params['header_label_singular']) : Engine_Api::_()->getApi('Core', 'siteapi')->translate($menu->header_label),
                            'icon' => (!empty($menu->icon)) ? $menu->icon : "",
                            'url' => (!empty($menu->url)) ? $menu->url : "",
                            'listingtype_id' => $params['listingtype_id'],
                            'viewBrowseType' => $this->_getViewTypeLabel($params['listingtype_id'], 2),
                            'viewProfileType' => $this->_getViewTypeLabel($params['listingtype_id'], 1),
                            'mapViewType' => $this->_getViewTypeLabel($params['listingtype_id'], 4),
                            'anotherViewBrowseType' => $this->_getViewTypeLabel($params['listingtype_id'], 4),
                            'canCreate' => Engine_Api::_()->getApi('Core', 'siteapi')->getCreateAuthSitereviewArray($menu),
                            "memberView" => $memberView,
                        );
                    }
                } else {

                    if (($menu->name == 'core_main_sitestoreproduct_orders' || $menu->name == 'core_main_wishlist') && !$viewer_id)
                        continue;

                    if ($menu->module == "siteapi" && $menu->name == 'core_main_wishlist') {
                        $isFavouriteEnable = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.favourite', 0) ? 1 : 0;
                        if ((empty($isFavouriteEnable) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')) {

                            $menuArray[] = array(
                                'name' => $menu->name,
                                'type' => $menu->type,
                                'label' => $this->translate($menu->dashboard_label),
                                'headerLabel' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($menu->header_label),
                                'color' => $color,
                                "memberView" => $memberView,
                                'icon' => (!empty($menu->icon)) ? $menu->icon : "",
                                'url' => (!empty($menu->url)) ? $menu->url : "",
                                'canCreate' => array(
                                    'sitestore' => (int) (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore') && (((_CLIENT_TYPE == 'android') && _ANDROID_VERSION >= '1.7.8') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.6.6'))),
                                    'sitereview' => (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview') && empty($isFavouriteEnable) ? 1 : 0,

                                ),
                            );
                        }
                        continue;
                    }

                    $tempResultArray = array(
                        'name' => $menu->name,
                        'type' => $menu->type,
                        'label' => $this->translate($menu->dashboard_label),
                        'headerLabel' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($menu->header_label),
                        'module' => $menu->module,

                        'color' => $color,
                        'icon' => (!empty($menu->icon)) ? $menu->icon : "",
                        'url' => (!empty($menu->url)) ? $menu->url : "",
                    );

                    //Added Member view type
                    if ($menu->name == 'core_main_user') {
                        $viewType = ($type == 'android') ? Engine_Api::_()->getApi('settings', 'core')->getSetting("android.member.view", 1) : Engine_Api::_()->getApi('settings', 'core')->getSetting("ios.member.view", 1);
                        $tempResultArray['memberViewType'] = $viewType;
                    }

                    if ($menu->name == 'crowd_funding_main') {
                        $tempResultArray['otherInfo'] = array(
                            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Projects'),
                            'module' => $menu->module,
                            "url" => 'crowdfunding/search-form'
                        );
                    }
                    if ($menu->name == 'core_main_app_tour') {
                        $tempResultArray['isAppTourView'] = isset($params['isAppTourView']) ? $params['isAppTourView'] : 0;
                    }
                    
                    if ($menu->name == 'core_main_multicurrency') {
                          $tempResultArray['currencyDetails'] = $this->getCurrencyDetails();
                          $tempResultArray['headerLabel'] = $tempResultArray['label'] = $tempResultArray['currencyDetails']['title'];
                     }

                    if ($menu->name == 'seaocore_location' && $viewer->getIdentity()) {
                        $value = array();
                        $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
                        $value['id'] = $viewer->seao_locationid;
                        $location = $seLocationsTable->getLocations($value);
                        if (!empty($location)) {
                            $tempResultArray['label'] = $location->location;
                            $response['defaultlocation'] = $location->location;
                        }
                    }

                    // Start work to add "canCreate" variable value.
                    if (
                            isset($menu['default']) && isset($menu['module']) && !empty($menu['default']) && !empty($menu['module']) && !in_array($menu['module'], array('core', 'activity', 'user'))
                    ) {
                        $tempResultArray['memberView'] = $memberView;

                        $tempResultArray['canCreate'] = Engine_Api::_()->getApi('Core', 'siteapi')->getCreateAuthArray($menu['module']);
                        if ($menu->name == 'core_main_siteevent') {
                            $ticket = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1);
                            $enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventticket');
                            // $response['eventPackagesEnabled'] = $tempResultArray['packagesEnabled'];
                            if (!empty($ticket) && !empty($enabled)) {
                                $tempResultArray['canCreate']['myTicketEnabled'] = 1;
                                $tempResultArray['canCreate']['coupontEnabled'] = 1;
                            }
                        }
                    }
                    // End work to add "canCreate" variable value.

                    if (!empty($getLocations) && isset($menu->name) && ($menu->name == 'seaocore_location'))
                        $tempResultArray['data'] = $getLocations;
                    
                    if(($menu->name == 'core_main_multicurrency') || ($menu->name == 'home')){
                        $tempResultArray['memberView'] = 1;
                    }

                    if(($menu->name == 'core_main_provider') || ($menu->name == 'core_main_services')){
                        $tempResultArray['canCreate']['default'] = ($menu->name == 'core_main_services') ? Engine_Api::_()->authorization()->isAllowed('sitebooking_ser', null, 'create') : Engine_Api::_()->authorization()->isAllowed('sitebooking_pro', null, 'create');
                    }

                    if ($menu->name == 'core_main_calendars' || $menu->name == 'core_main_calendar' || $menu->name == 'calendar_groups') {
                        $tempResultArray['memberView'] = Engine_Api::_()->authorization()->isAllowed('calendar', null, 'view');
                    }

                    if(($menu->name == 'core_main_calendars') || ($menu->name == 'calendar_groups')) {
                        $tempResultArray['canCreate']['default'] = ($menu->name == 'core_main_calendars') ? Engine_Api::_()->authorization()->isAllowed('calendar', null, 'create') : 1 ;
                    }
                    
                    $menuArray[] = $tempResultArray;
                }
            }

            $response['menus'] = $menuArray;

            // Getting the available languages.
            $response['languages'] = $this->getLanguages($type);

            $response['location'] = ($type == 'android') ? Engine_Api::_()->getApi('settings', 'core')->getSetting("android.enable.location", 1) : Engine_Api::_()->getApi('settings', 'core')->getSetting("ios.enable.location", 1);

            $viewType = ($type == 'android') ? Engine_Api::_()->getApi('settings', 'core')->getSetting("android.member.view", 1) : Engine_Api::_()->getApi('settings', 'core')->getSetting("ios.member.view", 1);

            $response['memberViewType'] = $viewType;

            $response['app_tour'] = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteandroidapp.app.tour", 1);
            $response['isOTPEnable'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->hasEnableLoginOtp() ? 1 : 0;
            if(!empty($response['isOTPEnable']))
            $response['loginoption'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');


            $response['isShowAppName'] = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteiosapp.app.name", 0);
            $response['showFilterType'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.all.update.show', 1);
            $response['storyDuration'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestories_max_allowed_days', 1);
            $response['is_show_greeting_announcement'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.greeting.announcement', 1);

            $autodetectLocation = ($type == 'android') ? Engine_Api::_()->getApi('settings', 'core')->getSetting("siteandroid.autodetect.enable", 0) : Engine_Api::_()->getApi('settings', 'core')->getSetting("siteios.autodetect.enable", 0);

            $isChangeManually = ($type == 'android') ? Engine_Api::_()->getApi('settings', 'core')->getSetting("siteandroid.change.location", 0) : Engine_Api::_()->getApi('settings', 'core')->getSetting("siteios.change.location", 0);
            // @Todo: Remove the following condition after iOS and Android App upgrade.
            if (!empty($getLocations)) {
                $response['restapilocation'] = $this->getLocations();
                $response['restapilocation']['autodetectLocation'] = $autodetectLocation;
                $response['restapilocation']['isChangeManually'] = $isChangeManually;
                $response['seaolocation'] = $this->getSeaoLocations();
            }

            if (!empty($browse_as_guest)) {
                $browse_as_guest = ($type === 'ios') ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.browse.guest', 1) : Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.browse.guest', 1);

                $response['browse_as_guest'] = $browse_as_guest;
            }
            //Member verification Work...............
            if($viewer && !empty($viewer->getIdentity()))
                $response['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($viewer);
            else
                $response['showVerifyIcon'] = 0;

            $response['isFavouriteEnable'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.favourite', 0) ? 1 : 0;

            //IOS Subscription work starts here
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');

            // Get available subscriptions
            $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
            $packagesSelect = $packagesTable
                    ->select()
                    ->from($packagesTable)
                    ->where('enabled = ?', true)
                    ->where('signup = ?', true);

            $multiOptions = array();
            $packagesObj = $packagesTable->fetchAll($packagesSelect);
            $package_count = count($packagesObj);

            $sitesubscriptionModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitesubscription');

            if($sitesubscriptionModuleEnable){
                $subscriptionClass = "Sitesubscription_Plugin_Signup_Subscription";
            }
            else{
                $subscriptionClass = "Payment_Plugin_Signup_Subscription";
            }
            $stepTable = Engine_Api::_()->getDbtable('signup', 'user');
            $stepSelect = $stepTable->select()->where('class = ?', $subscriptionClass);

            $row = $stepTable->fetchRow($stepSelect);

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitequicksignup')) {
                $isEnabledSubscription = $coreSettings->getSetting('sitequicksignup.subscription.enabled', 0);

                $response['isNewSubscriptionForIOS'] = (!empty($isEnabledSubscription) && !empty($row->enable) && $package_count > 0) ? 1 : 0 ;
            }else{
                $response['isNewSubscriptionForIOS'] = (!empty($row->enable) && $package_count > 0) ? 1 : 0 ;
            }
            // IOS Subscription work ends here
            
             $response['video_quality'] = ($type == 'android') ? Engine_Api::_()->getApi('settings', 'core')->getSetting("siteandroidapp.video.quality", 1) : Engine_Api::_()->getApi('settings', 'core')->getSetting("siteios.video.quality", 1);

            if ($type === 'ios') {
                $response['siteiosappMode'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.current.mode', 1);
                $response['siteiosappSharedSecretKey'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.shared.secret');
            }

            //check playlist and channel enabled or not
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                $response['isChannelEnable'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1);
                $response['isPlaylistEnable'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1);
            } else {
                $response['isChannelEnable'] = 0;
                $response['isPlaylistEnable'] = 0;
            }
            
            if($viewer && $viewer->getIdentity())
            $response['canSendMessage'] = Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth');


                $feedPostPermission = Engine_Api::_()->getApi('Siteapi_Core', 'advancedactivity')->statusBoxSettings();
                $response = array_merge($response, $feedPostPermission);

            // Set isPrimemessengerActive key in response
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('primemessenger')) {
                $response['isPrimeMessengerActive'] = Engine_Api::_()->primemessenger()->isPrimeMessengerActive();
            }
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestories')) {
                $isSitestoriesEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestories_enable_stories');
                $response['isSitestoriesEnable'] = $isSitestoriesEnable ? 1 : 0;
            }

            $response['enable_modules'] = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
        $this->respondWithSuccess($response);
    }
    
    /**
     * Get locations array
     *
     * @return array $locationMultiOptions
     */
    protected function getLocations() {
        $locationResponse = array();
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $locationDefault = $settings->getSetting('seaocore.locationdefault', '');
        $seaocore_locationspecific = $settings->getSetting('seaocore.locationspecific', '');
        $seaocore_locationspecificcontent = $settings->getSetting('seaocore.locationspecificcontent', '');
        if (!empty($locationDefault))
            $locationResponse['default'] = $locationDefault;

        if (Engine_Api::_()->seaocore()->getLocationsTabs()) {
            if ($seaocore_locationspecific) {
                $locationResponse['locationType'] = 'specific';
                $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
                $locationsArray = array();
                foreach ($locations as $location) {
                    $locationsArray[$location->location] = $location->title;
                }
                if ($locations) {
                    $locationResponse['restapilocation'] = $locationsArray;
                    return $locationResponse;
                }
            } else {
                $locationResponse['locationType'] = 'notspecific';
                return $locationResponse;
            }
        }

        //Default value
        if (!isset($locationResponse['locationType']) || empty($locationResponse['locationType']))
            $locationResponse['locationType'] = 'notspecific';
        return $locationResponse;
    }

    /**
     * Get locations array
     *
     * @return array $locationMultiOptions
     */
    protected function getSeaoLocations() {
        $locationResponse = array();
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $locationDefault = $settings->getSetting('seaocore.locationdefault', '');
        $seaocore_locationspecific = $settings->getSetting('seaocore.locationspecific', '');
        $seaocore_locationspecificcontent = $settings->getSetting('seaocore.locationspecificcontent', '');
        if (!empty($locationDefault))
            $locationResponse['default'] = $locationDefault;

        if (Engine_Api::_()->seaocore()->getLocationsTabs()) {
            if ($seaocore_locationspecific) {
                $locationResponse['locationType'] = 'specific';
                $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
                $locationsArray = array();
                foreach ($locations as $location) {
                    $locationsArray[$location->location] = $location->title;
                }
                if ($locations) {
                    $locationResponse['seaolocation'] = $locationsArray;
                    return $locationResponse;
                }
            } else {
                $locationResponse['locationType'] = 'notspecific';
                return $locationResponse;
            }
        }

        return;
    }

    public function locationSuggestAction() {

        $locationResponse = array();
        $search = $this->getParam('suggest', null);
        $latitude = $this->getParam('latitude', 0);
        $longitude = $this->getParam('longitude', 0);

        $local = Engine_Api::_()->getApi('Location', 'siteapi')->getSuggestGooglePalces($search);

        $this->respondWithSuccess($local);
    }

    /**
     * Get Setting for Guest User Browse 
     *
     * @return array $response
     */
    public function browseAsGuestAction() {
        $this->validateRequestMethod();

        $type = $this->getRequestParam('type', 'android');

        $browse_as_guest = ($type === 'ios') ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.browse.guest', 1) : Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.browse.guest', 1);

        $response['browse_as_guest'] = $browse_as_guest;

        $this->respondWithSuccess($response);
    }

    /**
     * Get account setting menus
     *
     * @return array $menuArray
     */
        public function getUserAccountMenuAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->validateRequestMethod();
        $menuArray = array();
        $enableMenuArray = Array();
        $subscriptionMenu=array();
        $user = Engine_Api::_()->user()->getViewer();
        $auth_delete = $this->_helper->requireAuth()->setAuthParams($user, null, 'delete')->isValid();
        $isLastSuperAdmin = false;
        if (1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $user->level_id)
            $isLastSuperAdmin = true;
        $menuArray[] = array(
            'name' => 'general',
            'label' => $this->translate('General'),
            'url' => '/members/settings/general'
        );
        $menuArray[] = array(
            'name' => 'privacy',
            'label' => $this->translate('Privacy'),
            'url' => '/members/settings/privacy'
        );
        $menuArray[] = array(
            'name' => 'network',
            'label' => $this->translate('Networks'),
            'url' => '/members/settings/network'
        );
        $menuArray[] = array(
            'name' => 'notification',
            'label' => $this->translate('Notifications'),
            'url' => '/members/settings/notifications'
        );
        if ((_IOS_VERSION >= '3.0.0' || _ANDROID_VERSION >= '4.3.0') && Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
            $menuArray[] = array(
                'name' => 'email',
                'label' => $this->translate('Emails'),
                'url' => '/members/settings/emails'
            );
        }
        $menuArray[] = array(
            'name' => 'password',
            'label' => $this->translate('Change Password'),
            'url' => '/members/settings/password'
        );

        if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION >= '1.8') || ((_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION >= '1.8.0')) {
            $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
            if (!in_array($level->type, array('admin', 'moderator'))) {

                // If there are enabled gateways or packages,
                if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() > 0 && Engine_Api::_()->getDbtable('packages', 'payment')->getEnabledNonFreePackageCount() > 0) {
                    $subscriptionMenu = array(
                        'name' => 'subscription',
                        'label' => $this->translate('Subscription'),
                        'url' => '/members/settings/subscriptions'
                    );
                }
            }
        }
        
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')) {
            $menuArray[] = array(
                'name' => 'mystore',
                'label' => $this->translate('My Store Account'),
                'url' => '/sitestore/orders',
            );
        }

        $table = Engine_Api::_()->getDbtable('menuItems', 'core');
        $select = $table->select()
                ->where('enabled = ?', 1)
                ->where('name like ' . "'%user_settings%'");

        $menus = $table->fetchAll($select);
        foreach ($menus as $menu) {
            $labelArray[] = $this->translate($menu['label']);
        }
        foreach ($menuArray as $menu) {
            if (!in_array($menu['label'], $labelArray)) {

                continue;
            } else
                $enableMenuArray[] = $menu;
        }
        
        if(!empty($subscriptionMenu)){
             $enableMenuArray[] = $subscriptionMenu;
        }
        
        if (_CLIENT_TYPE && (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION >= '1.7.3') {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting("enable.siteandroidapp.sound", 1))
                $enableMenuArray[] = array(
                    'name' => 'sound',
                    'label' => $this->translate('Sounds'),
                );
        }

        $otpType = $settings->getSetting('siteotpverifier.type');
        $otpLength = $settings->getSetting('siteotpverifier.length');
        $enableOtp = Engine_Api::_()->getApi('Siteapi_Core', 'user')->hasEnableLoginOtp();
        if ($enableOtp)
        {   $populatearray = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier');
            $enableMenuArray[] = array(
                'name' => 'mobileinfo',
                'label' => $this->translate('Phone Number Details'),
                'url' => '/otpverifier/add-mobileno',
                'type' => $otpType,
                'length' => $otpLength,
            );
        }

        $isTwoStepEnable = array();
        if ((_IOS_VERSION >= '3.0.0' || _ANDROID_VERSION >= '4.3.0') && Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
            // two step verification for delete account
            $isTwoStepEnable['isTwoStepEnable'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.spam.otpfeatures',1);
            $isTwoStepEnable['generateOtpUrl'] = '/members/settings/generate-otp';
        }

        if ($auth_delete && !$isLastSuperAdmin)
            $enableMenuArray[] = array(
                'name' => 'delete',
                'label' => $this->translate('Delete Account'),
                'url' => '/members/settings/delete',
                'delete_account' => $isTwoStepEnable
            );
        
        if ((_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION >= '2.5.3')) {
            $enableMenuArray[] = array(
                'name' => 'video_autoplay',
                'label' => $this->translate('Video Auto-Play'),
            );
        }

        if ((_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION >= '2.7.19')) {
            $enableMenuArray[] = array(
                'name' => 'picture_in_picture',
                'label' => $this->translate('Picture-in-Picture'),
            );
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestories')) {
            $enableMenuArray[] = array(
                'name' => 'stories_privacy_settings',
                'label' => $this->translate('Stories Privacy'),
                'url' => '/advancedactivity/stories/stories-privacy',
            );
        }

        $this->respondWithSuccess($enableMenuArray);

    }

    /**
     * Report to site administrator.
     *
     * @return array
     */
    public function reportCreateAction() {
        if ($this->getRequest()->isGet() && !$this->getRequestParam("category") && !$this->getRequestParam("description")) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'core')->getReportForm());
        } else if ($this->getRequest()->isPost()) {
            $data = array();
            $data["category"] = $this->getRequestParam("category");
            $data["description"] = $this->getRequestParam("description");
            $type = $this->getRequestParam('type');
            $id = $this->getRequestParam('id');

            // Make a subject
            $subject = Engine_Api::_()->getItem($type, $id);

            // Form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'core')->getReportFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Process
            $table = Engine_Api::_()->getItemTable('core_report');
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                $viewer = Engine_Api::_()->user()->getViewer();

                $report = $table->createRow();
                $report->category = $data["category"];
                $report->description = $data["description"];
                $report->subject_type = $subject->getType();
                $report->subject_id = $subject->getIdentity();
                $report->user_id = $viewer->getIdentity();
                $report->save();

                // Increment report count
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.reports');

                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        } else {
            $this->respondWithValidationError('internal_server_error');
        }
    }

    /*
     *  ******************* START APIS OF [Like and Comments] *********************
     */

    /**
     * Get the likes and comment information respective to any content.
     *
     * @return array
     */
    public function likesCommentsAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $allComments = array();
        $type = $this->getRequestParam('subject_type');
        $siteapiLikeComments = Zend_Registry::isRegistered('siteapiLikeComments') ? Zend_Registry::get('siteapiLikeComments') : null;
        $type = empty($type) ? $this->getRequestParam('content_type') : $type;
        if (empty($type) || empty($siteapiLikeComments))
            $this->respondWithError('no_record');

        $id = $this->getRequestParam('subject_id');
        $id = empty($id) ? $this->getRequestParam('content_id') : $id;
        if (empty($id) || empty($siteapiLikeComments))
            $this->respondWithError('no_record');

        $limit = $this->getRequestParam('limit', 10);
        $page = $this->getRequestParam('page');
        $comment_id = $this->getRequestParam('comment_id');
        $commentLikes = false;
        $lastCommentId = $this->getRequestParam('lastCommentId', 0);

        $subject = Engine_Api::_()->getItem($type, $id);
        $bodyParams = $likeUsersArray = array();

        if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() || (!method_exists($subject, 'comments') && !method_exists($subject, 'likes')))
            $this->respondWithError('no_record');

        // Perms
        $viewer = Engine_Api::_()->user()->getViewer();
        $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        // $canComment & $canDelete variable need to be assigned diffrent values for sitegroup
        if (strpos($subject->getType(), "sitegroup") !== false) {
            if ($subject->getType() == 'sitegroup_group') {
                $groupSubject = $subject;
            } elseif ($subject->getType() == 'sitegroupmusic_playlist') {
                $groupSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitegroupnote_photo') {
                $groupSubject = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitegroupevent_photo') {
                $groupSubject = $subject->getEvent()->getParentPage();
            } else {
                $groupSubject = $subject->getParent();
            }
            $groupApi = Engine_Api::_()->sitegroup();
            $canComment = $groupApi->isManageAdmin($groupSubject, 'comment');
            $canDelete = $groupApi->isManageAdmin($groupSubject, 'edit');
        }

        // Likes    
        $likes = $subject->likes()->getLikePaginator();
        $isLike = Engine_Api::_()->getDbTable("likes", "core")->isLike($subject, $viewer);

        // RETURN THE LIKES USERS ARRAY.
        if ($this->getRequestParam('viewAllLikes', 1) && !empty($comment_id)) {
            $commentLikes = true;
            $tableName = (strstr($type, "activity")) ? "activity_comment" : "core_comment";
            $comment = Engine_Api::_()->getItem($tableName, $comment_id);
            if (empty($comment))
                $this->respondWithError('no_record');

            $viewAllLikes = $this->getRequestParam('viewAllLikes', 1);
            if (!empty($viewAllLikes)) {
                $userObject = $comment->likes()->getAllLikesUsers();
                foreach ($userObject as $user) {
                    $tempUserArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                    $tempUserArray = array_merge($tempUserArray, $getContentImages);
                    $likeUsersArray[] = $tempUserArray;
                }
                $bodyParams['viewAllLikesBy'] = $likeUsersArray;
            }
        }

        if (empty($commentLikes)) {
            // If has a page, display oldest to newest
            if (null !== $page) {
                $commentSelect = $subject->comments()->getCommentSelect();
                $commentSelect->order('comment_id ' . $this->getRequestParam('order', 'ASC'));
                if($lastCommentId)
                    $commentSelect->where('comment_id > ?' , $lastCommentId);
                $comments = Zend_Paginator::factory($commentSelect);
                $comments->setCurrentPageNumber($page);
                $comments->setItemCountPerPage($limit);
            } else {
                // If not has a page, show the
                $commentSelect = $subject->comments()->getCommentSelect();
                $commentSelect->order('comment_id DESC');
                if($lastCommentId)
                    $commentSelect->where('comment_id > ?' , $lastCommentId);
                $comments = Zend_Paginator::factory($commentSelect);
                $comments->setCurrentPageNumber(1);
                $comments->setItemCountPerPage(4);
            }

            // Hide if can't post
            if (!$canComment && !$canDelete)
                $this->respondWithError('unauthorized');

            $getTotalCommentCount = $comments->getTotalItemCount();

            // RETURN THE LIKES USERS ARRAY.
            $viewAllLikes = $this->getRequestParam('viewAllLikes', 1);
            if (!empty($viewAllLikes)) {
                $userObject = $subject->likes()->getAllLikesUsers();
                foreach ($userObject as $user) {
                    $tempUserArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                    $tempUserArray = array_merge($tempUserArray, $getContentImages);
                    $likeUsersArray[] = $tempUserArray;
                }
                $bodyParams['viewAllLikesBy'] = $likeUsersArray;
            }

            // RETURN THE COMMENTS ARRAY.
            $viewAllComments = $this->getRequestParam('viewAllComments', 1);
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

                    $a = $comment->likes()->getAllLikesUsers();
                    foreach ($a as $user) {
                        $tempUserArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                        $tempUserArray = array_merge($tempUserArray, $getContentImages);
                        $likeUsersArray[] = $tempUserArray;
                    }

                    $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
                    $commentInfo["comment_id"] = $comment->comment_id;
                    $commentInfo["user_id"] = $poster->getIdentity();
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, false, 'author');
                    $commentInfo = array_merge($commentInfo, $getContentImages);
                    $commentInfo["author_title"] = $poster->getTitle();
                    $commentInfo["comment_body"] = $comment->body;
                    $commentInfo["comment_date"] = $comment->creation_date;
                    $commentInfo["like_count"] = $comment->likes()->getLikeCount();


                    if ($poster->isSelf($viewer)) {
                        $commentInfo["delete"] = array(
                            "name" => "delete",
                            "label" => $this->translate('Delete'),
                            "url" => "comment-delete",
                            'urlParams' => array(
                                "subject_type" => $subject->getType(),
                                "subject_id" => $subject->getIdentity(),
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
                            $likeInfo["label"] = $this->translate('Like');
                            $likeInfo["url"] = "like";
                            $likeInfo['urlParams'] = array(
                                "subject_type" => $subject->getType(),
                                "subject_id" => $subject->getIdentity(),
                                "comment_id" => $comment->getIdentity()
                            );

                            $likeInfo["isLike"] = 0;
                        } else {
                            $likeInfo["name"] = "unlike";
                            $likeInfo["label"] = $this->translate('Unlike');
                            $likeInfo["url"] = "unlike";
                            $likeInfo['urlParams'] = array(
                                "subject_type" => $subject->getType(),
                                "subject_id" => $subject->getIdentity(),
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
        }

        // FOLLOWING ARE THE GENRAL INFORMATION OF THE PLUGIN, WHICH WILL RETURN IN EVERY CALLING.
        $bodyParams['isLike'] = !empty($isLike) ? 1 : 0;
        $bodyParams['canComment'] = $canComment;
        $bodyParams['canDelete'] = $canDelete;
        $bodyParams['getTotalComments'] = $getTotalCommentCount;
        $bodyParams['getTotalLikes'] = $likes->getTotalItemCount();

        if (!empty($siteapiLikeComments))
            $this->respondWithSuccess($bodyParams);
    }

    /**
     * Like to content and comment
     *
     * @return array
     */
    public function likeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        $type = $this->getRequestParam('subject_type');
        $id = $this->getRequestParam('subject_id');
        $sendAppNotification = $this->getRequestParam('sendNotification', 1);


        $siteapiGlobalView = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.global.view', 0);
        $siteapiLSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.lsettings', 0);
        $siteapiInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.androiddevice.type', 0);
        $siteapiGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.global.type', 0);
        $siteapiLikeComments = Zend_Registry::isRegistered('siteapiLikeComments') ? Zend_Registry::get('siteapiLikeComments') : null;
        $subject = Engine_Api::_()->getItem($type, $id);

        if (empty($subject) || empty($siteapiLikeComments))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $comment_id = $this->getRequestParam('comment_id');

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
            $isLike = Engine_Api::_()->getDbTable("likes", "core")->isLike($commentedItem, $viewer);
            if (!empty($isLike))
                $this->respondWithError('already_liked');
        } else {
            $commentedItem = $subject;
            $isLike = Engine_Api::_()->getDbTable("likes", "core")->isLike($subject, $viewer);
            if (!empty($isLike))
                $this->respondWithError('already_liked');
        }

        if (empty($siteapiGlobalType)) {
            for ($check = 0; $check < strlen($siteapiLSettings); $check++) {
                $tempSitemenuLtype += @ord($siteapiLSettings[$check]);
            }
            $tempSitemenuLtype = $tempSitemenuLtype + $siteapiGlobalView;
        }


        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();
        try {
            $commentedItem->likes()->addLike($viewer);

            // Add notification
            $owner = $commentedItem->getOwner();

            if (isset($sendAppNotification) && !empty($sendAppNotification)) {
                if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                        'label' => $commentedItem->getShortType()
                    ));
                }
            }
            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

//    $bodyArray["name"] = "unlike";
//    $bodyArray["label"] = $this->translate('Unlike');
//    $bodyArray["isLike"] = 1;
//
//    if ( !empty($comment_id) ) {
//      $bodyArray["url"] = "unlike";
//      $bodyArray["urlParams"] = array(
//          "subject_type" => $subject->getType(),
//          "subject_id" => $subject->getIdentity(),
//          "comment_id" => $comment_id
//      );
//    } else {
//      $bodyArray["url"] = "unlike";
//      $bodyArray["urlParams"] = array(
//          "subject_type" => $subject->getType(),
//          "subject_id" => $subject->getIdentity()
//      );
//    }
//
//    $this->respondWithSuccess($bodyArray);

        if (!empty($tempSitemenuLtype) && ($tempSitemenuLtype != $siteapiInfoType)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteapi.viewtypeinfo.type', 1);
        } else {
            if (!empty($siteapiLikeComments)) {
                $this->successResponseNoContent('no_content');
            }
        }
    }

    /**
     * Unlike to content and comment
     *
     * @return array
     */
    public function unlikeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $type = $this->getRequestParam('subject_type');
        $id = $this->getRequestParam('subject_id');

        $siteapiLikeComments = Zend_Registry::isRegistered('siteapiLikeComments') ? Zend_Registry::get('siteapiLikeComments') : null;
        $subject = Engine_Api::_()->getItem($type, $id);

        if (empty($subject) || empty($siteapiLikeComments))
            $this->respondWithError('no_record');


        $viewer = Engine_Api::_()->user()->getViewer();
        $comment_id = $this->getRequestParam('comment_id');

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
            $isLike = Engine_Api::_()->getDbTable("likes", "core")->isLike($commentedItem, $viewer);
            if (empty($isLike))
                $this->respondWithError('already_unliked');
        } else {
            $commentedItem = $subject;
            $isLike = Engine_Api::_()->getDbTable("likes", "core")->isLike($subject, $viewer);
            if (empty($isLike))
                $this->respondWithError('already_unliked');
        }
        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();
        try {
            $commentedItem->likes()->removeLike($viewer);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

//    $bodyArray["name"] = "like";
//    $bodyArray["label"] = $this->translate('Like');
//    $bodyArray["isLike"] = 0;
//
//    if ( !empty($comment_id) ) {
//      $bodyArray["url"] = "like";
//      $bodyArray["urlParams"] = array(
//          "subject_type" => $subject->getType(),
//          "subject_id" => $subject->getIdentity(),
//          "comment_id" => $comment_id
//      );
//    } else {
//      $bodyArray["url"] = "like";
//      $bodyArray["urlParams"] = array(
//          "subject_type" => $subject->getType(),
//          "subject_id" => $subject->getIdentity()
//      );
//    }
//
//    $this->respondWithSuccess($bodyArray);
        if (!empty($siteapiLikeComments))
            $this->successResponseNoContent('no_content');
    }

    /**
     * Comment post to content
     *
     * @return array
     */
    public function commentCreateAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');
        Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $type = $this->getRequestParam('subject_type');
        $id = $this->getRequestParam('subject_id');
        $body = $this->getRequestParam('body');

        $viewer = Engine_Api::_()->user()->getViewer();

        $subject = Engine_Api::_()->getItem($type, $id);
        $siteapiCommentCreate = Zend_Registry::isRegistered('siteapiCommentCreate') ? Zend_Registry::get('siteapiCommentCreate') : null;
        $send_notification = $this->getRequestParam('send_notification', 1);

        if (!empty($subject))
            $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

        if (empty($siteapiCommentCreate) || empty($canComment) || empty($subject) || empty($body))
            $this->respondWithError('no_record');

        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $filter->filter($body);

        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            $comment = $subject->comments()->addComment($viewer, $body);

            if (isset($send_notification) && !empty($send_notification)) {

                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $subjectOwner = $subject->getOwner('user');

                // Activity
                $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
                    'owner' => $subjectOwner->getGuid(),
                    'body' => $body
                ));

                // Notifications
                // Add notification for owner (if user and not viewer)
                if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
                        'label' => $subject->getShortType()
                    ));
                }

                // Add a notification for all users that commented or like except the viewer and poster
                // @todo we should probably limit this
                $commentedUserNotifications = array();
                foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
                    if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
                        continue;

                    // Don't send a notification if the user both commented and liked this
                    $commentedUserNotifications[] = $notifyUser->getIdentity();

                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
                        'label' => $subject->getShortType()
                    ));
                }

                // Add a notification for all users that liked
                // @todo we should probably limit this
                foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
                    // Skip viewer and owner
                    if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
                        continue;

                    // Don't send a notification if the user both commented and liked this
                    if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
                        continue;

                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
                        'label' => $subject->getShortType()
                    ));
                }
            }

            // Increment comment count
            $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');
            $commentInfo = array();
            if (!empty($comment)) {
//        $getHosts = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
                $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
                $commentInfo["comment_id"] = $comment->comment_id;
                $commentInfo["user_id"] = $poster->getIdentity();
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster);
                $commentInfo = array_merge($commentInfo, $getContentImages);
                //to provide the same image names as in likes-comment response
                $getContentImages = array();
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, false, 'author');
                $commentInfo = array_merge($commentInfo, $getContentImages);
                $commentInfo["author_title"] = $poster->getTitle();
                $commentInfo["comment_body"] = $comment->body;
                $commentInfo["comment_date"] = $comment->creation_date;

                //Member verification Work...............
                $commentInfo['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($poster);

//                if (!empty($canDelete) || $poster->isSelf($viewer)) {
                if ($poster->isSelf($viewer)) {
                    $commentInfo["delete"] = array(
                        "name" => "delete",
                        "label" => $this->translate('Delete'),
                        "url" => "comment-delete",
                        'urlParams' => array(
                            "subject_type" => $subject->getType(),
                            "subject_id" => $subject->getIdentity(),
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
                        $likeInfo["label"] = $this->translate('Like');
                        $likeInfo["url"] = "like";
                        $likeInfo["urlParams"] = array(
                            "subject_type" => $subject->getType(),
                            "subject_id" => $subject->getIdentity(),
                            "comment_id" => $comment->getIdentity()
                        );
                        $likeInfo["isLike"] = 0;
                    } else {
                        $likeInfo["name"] = "unlike";
                        $likeInfo["label"] = $this->translate('Unlike');
                        $likeInfo["url"] = "unlike";
                        $likeInfo["urlParams"] = array(
                            "subject_type" => $subject->getType(),
                            "subject_id" => $subject->getIdentity(),
                            "comment_id" => $comment->getIdentity()
                        );
                        $likeInfo["isLike"] = 1;
                    }
                    $commentInfo["like_count"] = $comment->likes()->getLikeCount();
                    $commentInfo["like"] = $likeInfo;
                } else {
                    $commentInfo["like"] = null;
                }

                $db->commit();
                $this->respondWithSuccess($commentInfo);
            } else {
                $this->respondWithValidationError('internal_server_error', 'Problem in comment');
            }
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function addCommentNotificationsAction() {

        // Validate request methods
        $this->validateRequestMethod('POST');

        $type = $this->getRequestParam('subject_type');
        $id = $this->getRequestParam('subject_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        $subject = Engine_Api::_()->getItem($type, $id);

        $comment_id = $this->getRequestParam('comment_id');

        if ($comment_id)
            $comment = $subject->comments()->getComment($comment_id);

        $siteapiCommentCreate = Zend_Registry::isRegistered('siteapiCommentCreate') ? Zend_Registry::get('siteapiCommentCreate') : null;

        if (!empty($subject))
            $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

        if (empty($siteapiCommentCreate) || empty($canComment) || empty($subject) || empty($comment))
            $this->respondWithError('no_record');
        try {
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $subjectOwner = $subject->getOwner('user');

            // Notifications
            // Add notification for owner (if user and not viewer)
            if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                $commentedUserNotifications[] = $notifyUser->getIdentity();

                Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Add a notification for all users that liked
            // @todo we should probably limit this
            foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
                // Skip viewer and owner
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
                    continue;

                Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function isSitevideoPluginEnabledAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $videoModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        $videoIntegrationModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideointegration');

        if (empty($videoModuleEnabled) || empty($videoIntegrationModuleEnabled)) {
            $response['sitevideoPluginEnabled'] = 0;
            $response['canCreateVideo'] = 0;
            $this->respondWithSuccess($response, true);
        }

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject_type = $this->_getParam('subject_type');
        $subject_id = $this->_getParam('subject_id');

        if (empty($subject_id) || empty($subject_type))
            $this->respondWithError('no_record');

        //GET VIDEO SUBJECT
        $subject = Engine_Api::_()->getItem($subject_type, $subject_id);
        Engine_Api::_()->core()->setSubject($subject);

        $moduleName = $moduleName = strtolower($subject->getModuleName());
        $getShortType = ucfirst($subject->getShortType());

        if ($moduleName == 'sitereview' && isset($subject->listingtype_id)) {
            if (!(Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $subject->listingtype_id, 'item_module' => 'sitereview', 'checked' => 'enabled')))) {
                $response['sitevideoPluginEnabled'] = 0;
                $response['canCreateVideo'] = 0;
                $this->respondWithSuccess($response, true);
            }
        } else {
            if (!(Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName()), 'checked' => 'enabled')))) {
                $response['sitevideoPluginEnabled'] = 0;
                $response['canCreateVideo'] = 0;
                $this->respondWithSuccess($response, true);
            }
        }
        $params['parent_type'] = $subject->getType();
        $params['parent_id'] = $subject->getIdentity();

        if ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore') {
            $isModuleOwnerAllow = 'is' . $getShortType . 'OwnerAllow';
            $videoCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitevideo', 'videos');

            //START PACKAGE WORK
            if (Engine_Api::_()->$moduleName()->hasPackageEnable()) {
                if (!Engine_Api::_()->$moduleName()->allowPackageContent($subject->package_id, "modules", $moduleName . 'video')) {
                    $response['sitevideoPluginEnabled'] = 0;
                    $response['canCreateVideo'] = 0;
                    $this->respondWithSuccess($response, true);
                }
            } else {
                $isOwnerAllow = Engine_Api::_()->$moduleName()->$isModuleOwnerAllow($subject, 'svcreate');
                if (empty($isOwnerAllow)) {
                    $response['sitevideoPluginEnabled'] = 0;
                    $response['canCreateVideo'] = 0;
                    $this->respondWithSuccess($response, true);
                }
            }

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'view');
            if (empty($isManageAdmin)) {
                $response['sitevideoPluginEnabled'] = 0;
                $response['canCreateVideo'] = 0;
                $this->respondWithSuccess($response, true);
            }

            if (empty($videoCount)) {
                $response['sitevideoPluginEnabled'] = 0;
                $response['canCreateVideo'] = 0;
                $this->respondWithSuccess($response, true);
            }
        } else if ($moduleName == 'siteevent') {
            $videoCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitevideo', 'videos');
            //AUTHORIZATION CHECK
            if (empty($videoCount)) {
                $response['sitevideoPluginEnabled'] = 0;
                $response['canCreateVideo'] = 0;
                $this->respondWithSuccess($response, true);
            }
        } else if ($moduleName == 'sitereview') {
            //AUTHORIZATION CHECK
            $table = Engine_Api::_()->getDbtable('videos', 'sitevideo');

            $videoCount = $count = $table
                    ->select()
                    ->from($table->info('name'), array('count(*) as count'))
                    ->where("parent_type = ?", 'sitereview_listing_' . $subject->listingtype_id)
                    ->where("parent_id =?", $subject->getIdentity())
                    ->query()
                    ->fetchColumn();

            if (empty($videoCount)) {
                $response['sitevideoPluginEnabled'] = 0;
                $response['canCreateVideo'] = 0;
                $this->respondWithSuccess($response, true);
            }
        }

        if (isset($viewer_id) && !empty($viewer_id))
            $response['canCreateVideo'] = 1;

        $response['sitevideoPluginEnabled'] = 1;
        $response['totalItemCount'] = $videoCount;
        $this->respondWithSuccess($response, true);
    }

    /**
     * Delete posted comment
     *
     * @return array
     */
    public function commentDeleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $type = $this->getRequestParam('subject_type');
        $id = $this->getRequestParam('subject_id');
        $comment_id = $this->getRequestParam('comment_id');

        if (empty($type) || empty($id) || empty($comment_id))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem($type, $id);
        $comment = Engine_Api::_()->getItem("core_comment", $comment_id);
        $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);

        if (!empty($subject))
            $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        if (!$poster->isSelf($viewer) && empty($canDelete))
            $this->respondWithError('unauthorized');

        // Process
        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();
        try {
            $subject->comments()->removeComment($comment_id);
            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /*
     *  ******************* END APIS OF [Like and Comments] *********************
     */

    /**
     * Get language array
     *
     * @return array $localeMultiOptions
     */
    protected function getLanguages($type) {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        //PREPARE LANGUAGE LIST
        $languageList = Zend_Registry::get('Zend_Translate')->getList();
        $appConfiguredLanguage = $this->_languageMultioptions($type);

        //PREPARE DEFAULT LANGUAGE
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = '';
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
            if (_CLIENT_TYPE && (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION > '1.7') {
                if (array_key_exists($key, $appConfiguredLanguage)) {
                    if ($languageName) {
                        $localeMultiOptions[$key] = $languageName;
                    } else {
                        $localeMultiOptions[$key] = Zend_Registry::get('Zend_Translate')->_('Unknown');
                    }
                }
            } else {
                if ($languageName) {
                    $localeMultiOptions[$key] = $languageName;
                } else {
                    $localeMultiOptions[$key] = Zend_Registry::get('Zend_Translate')->_('Unknown');
                }
            }
        }

        // Get default language
        $defaultLanguage = ($viewer->getIdentity()) ? $viewer->language : $defaultLanguage;

        return array(
            'default' => $defaultLanguage,
            'languages' => $localeMultiOptions
        );
    }

    /*
     * Get the view type for browse and profile page
     * 
     * @param $listingtype_id int
     * @param $viewType int
     * @return int
     */

    private function _getViewTypeLabel($listingtype_id, $viewType = 2) {
        // Make a object of listing type
        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios')) {
            $select->from('engine4_siteiosapp_listingtypeViewMaps')
                    ->where('listingtype_id = ?', $listingtype_id);
        } else {
            $select->from('engine4_siteandroidapp_listingtypeViewMaps')
                    ->where('listingtype_id = ?', $listingtype_id);
        }
        $row = $select->query()->fetchObject();

        /*
         * 1 for listing profile type
         * 2 for listing browse type
         */
        if (isset($viewType) && $viewType == 1) {
            return (!isset($row->profileView_id)) ? 3 : $row->profileView_id;
        } else if (isset($viewType) && $viewType == 2) {
            return (!isset($row->browseView_id)) ? 2 : $row->browseView_id;
        } else if (isset($viewType) && $viewType == 4) {
            return (!isset($row->map_view_type)) ? 0 : $row->map_view_type;
        }

        return;
    }

    private function _languageMultioptions($type = 'android') {
        $getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $websiteStr = str_replace(".", "-", $getWebsiteName);

        $this->_directoryName = ($type == 'ios') ? 'ios-' . $websiteStr . '-app-builder' : 'android-' . $websiteStr . '-app-builder';
        $this->_getAPPBuilderBaseURL = 'public/' . $this->_directoryName;

        //Get available language file.
        if ($type == 'ios') {
            foreach ($this->_getAPPLanguageDetailsForUpload($type) as $key => $values) {
                @chmod(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.strings', 0777);

                if (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.strings'))
                    $getDefaultAvailableLanguages[$key] = $values;
                else if (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.csv'))
                    $getDefaultAvailableLanguages[$key] = $values;
            }
        }
        else {
            foreach ($this->_getAPPLanguageDetailsForUpload($type) as $key => $values) {
                @chmod(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.xml', 0777);

                if (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.xml'))
                    $getDefaultAvailableLanguages[$key] = $values;
                else if (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.csv'))
                    $getDefaultAvailableLanguages[$key] = $values;
            }
        }

        $getAPPLanguageDetailsForUpload = !empty($getDefaultAvailableLanguages) ? $getDefaultAvailableLanguages : array();
        return $getAPPLanguageDetailsForUpload;
    }

    /*
     * Get default available languages.
     */

    private function _getAPPLanguageDetailsForUpload($type = 'android') {
        $appType = ($type == 'android') ? '_language_android_mobileapp' : '_language_ios_mobileapp';
        $getLanguages = Engine_Api::_()->getApi('Core', 'siteapi')->getLanguages(true);
        if (isset($getLanguages)) {
            $languageArray = array();
            foreach ($getLanguages as $key => $label) {
                $languageArray[$key] = array(
                    'title' => 'Upload Language File For: [' . $label . ']',
                    'directoryName' => 'Languages_App',
                    'fileName' => $key . $appType,
                );
            }
        }

        return $languageArray;
    }

    public function sendNotificationAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $type = $this->getRequestParam('subject_type');
        $id = $this->getRequestParam('subject_id');

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem($type, $id);

        // Process
        try {
            // Add notification
            $owner = $subject->getOwner();

            if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($owner, $viewer, $subject, 'liked', array(
                    'label' => $subject->getShortType()
                ));
            }
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function getNewVersionAction() {
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        $response = array();

        $type = $this->getRequestParam('type', 'android');
        $version = $this->getRequestParam('version', null);

        if (
                ($type == 'android') && _ANDROID_VERSION
        ) {
            $popupEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting("android.popup.enable", 0);
            $latestVersion = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.version.upgrade');
            $versionDescription = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.version.description');
            $isForceUpgrade = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.force.upgrade');
            $response['isForceUpgrade'] = $isForceUpgrade ? true : false;
            $response['latestVersion'] = $latestVersion;
            $response['description'] = $versionDescription;
            $response['isPopUpEnabled'] = $popupEnable;
        }

        if (
                ($type == 'ios') && _IOS_VERSION
        ) {
            $popupEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting("ios.popup.enable", 0);
            $latestVersion = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.version.upgrade');
            $versionDescription = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.version.description');
            $isForceUpgrade = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.force.upgrade');
            $response['isForceUpgrade'] = $isForceUpgrade ? true : false;
            $response['latestVersion'] = $latestVersion;
            $response['description'] = $versionDescription;
            $response['isPopUpEnabled'] = $popupEnable;
        }

        if (!empty($response)) {
            $this->respondWithSuccess($response);
        }
    }

    public function getErrorPageContentAction() {
        $this->validateRequestMethod();
        $language = $this->getRequestParam('language');

        // Android App Details
        $imagePath = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        //PREPARE DEFAULT LANGUAGE
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = '';
            }
        }
        if (!empty($language))
            $defaultLanguage = $language;

        if (_ANDROID_VERSION) {
            $formValues = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteandroidapp_error_params", array());
            $values = @unserialize($formValues);
            $response['title'] = (isset($values["siteandroidapp_error_title_$defaultLanguage"]) && !empty($values["siteandroidapp_error_title_$defaultLanguage"])) ? $values["siteandroidapp_error_title_$defaultLanguage"] : "Oops! ";
            $response['description'] = (isset($values["siteandroidapp_error_description_$defaultLanguage"]) && !empty($values["siteandroidapp_error_description_$defaultLanguage"])) ? $values["siteandroidapp_error_description_$defaultLanguage"] : "Something went wrong, try one of these instead.";
            $response['image'] = (isset($values["siteandroidapp_error_image"]) && !empty($values["siteandroidapp_error_image"])) ? $imagePath . $fileName : $imagePath . '/application/modules/Siteapi/externals/images/error.gif';
            ;
        } else if (_IOS_VERSION) {
            $formValues = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteiosapp_error_params", array());
            $values = @unserialize($formValues);

            $response['title'] = (isset($values["siteiosapp_error_title_$defaultLanguage"]) && !empty($values["siteiosapp_error_title_$defaultLanguage"])) ? $values["siteiosapp_error_title_$defaultLanguage"] : "Oops! ";
            $response['description'] = (isset($values["siteiosapp_error_description_$defaultLanguage"]) && !empty($values["siteiosapp_error_description_$defaultLanguage"])) ? $values["siteiosapp_error_description_$defaultLanguage"] : "Something went wrong, try one of these instead.";
            $response['image'] = (isset($values["siteiosapp_error_image"]) && !empty($values["siteiosapp_error_image"])) ? $imagePath . $fileName : $imagePath . '/application/modules/Siteapi/externals/images/error.gif';
        }
        $this->respondWithSuccess($response);
    }

    public function setAppTourAction() {

        $table = Engine_Api::_()->getDbtable('menus', 'siteandroidapp');
        try {
            $select = $table->select();
            $select->where("name=?", "core_main_app_tour");
            $appTourMenu = $table->fetchRow($select);
            if (!empty($appTourMenu)) {
                $TourObj = Engine_Api::_()->getItem('siteandroidapp_menus', $appTourMenu->menu_id);
                $params = @unserialize($TourObj->params);
                $params['isAppTourView'] = 1;
                $TourObj->params = @serialize($params);
                $TourObj->save();
            }
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }
    
    public static function _convert_size($val)
    {
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val = trim($val, 'G');
                $val *= 1024; //1073741824
                break;
            case 'm':
                $val = trim($val, 'M');
                break;
            case 'k':
                trim($val, 'K');
                $val /= 1024;
                break;
        }
        return $val;
    }

    public function getSettingsAction()
    {   
        $settingsResponse = array();
        $response = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Quick Signup Settings
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitequicksignup')) 
            {
                
                $isQuickSignUp = $settings->getSetting('sitequicksignup_allow_quick_signup');
                $settingsResponse['isQuickSignUp'] = $isQuickSignUp ? true : false;
                $isAllowTitle = $settings->getSetting('sitequicksignup_allow_title');
                $settingsResponse['isAllowTitle'] = $isAllowTitle ? true : false;
                $isAllowDescription = $settings->getSetting('sitequicksignup_allow_description');
                $settingsResponse['isAllowDescription'] = $isAllowDescription ? true : false;
                $isAllowFieldDescription = $settings->getSetting('sitequicksignup_field_description');
                $settingsResponse['isAllowFieldDescription'] = $isAllowFieldDescription ? true : false;
                $isAllowConfirmEmail = $settings->getSetting('sitequicksignup_confirm_email');
                $settingsResponse['isAllowConfirmEmail'] = $isAllowConfirmEmail ? true : false;
                $isAllowConfirmPassword = $settings->getSetting('sitequicksignup_confirm_password');
                $settingsResponse['isAllowConfirmPassword'] = $isAllowConfirmPassword ? true : false;
                $isEnabledSubscription = $settings->getSetting('sitequicksignup_subscription_enabled');
                $settingsResponse['isEnabledSubscription'] = $isEnabledSubscription ? true : false;
                $isEnabledPopUp = $settings->getSetting('sitequicksignup_welcome_popup_enabled');
                $settingsResponse['isEnabledPopUp'] = $isEnabledPopUp ? true : false;
                $response['sitequicksignup'] = $settingsResponse;
            }
            
        //Livestreaming settings
        $livestreamModuleVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('livestreamingvideo')->version;

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('livestreamingvideo') && !empty($viewer->getIdentity())) { 
            if (Engine_Api::_()->siteapi()->checkVersion($livestreamModuleVersion, '5.5.0')) {
                $enabledBoradCast = Engine_Api::_()->getApi('Siteapi_Core', 'livestreamingvideo')->hasPackageEnable();
                $response['livestreamingvideo']['enableBroadcast'] = !empty($enabledBoradCast)? true : false;
                $response['isNewLiveCompatible'] = true;
                $response['new_agora_url'] = $settings->getSetting('livestreamingvideo.url');
                $response['new_app_id'] = $settings->getSetting('livestreamingvideo_agora_app_id', '');
            } else {
                $enabledBoradCast = Engine_Api::_()->authorization()->isAllowed('livestreamingvideo', $viewer, 'go_live');
                $response['livestreamingvideo']['enableBroadcast'] = !empty($enabledBoradCast)? true : false;
                $response['livestreamingvideo']['duration'] =  $settings->getSetting('livestreamingvideo.max.allowed.duration', 2);
            }
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitead')) 
            $response['isSiteadEnabled'] = 1;
        else
             $response['isSiteadEnabled'] = 0;

        // Location Api key

        if(_CLIENT_TYPE && (_CLIENT_TYPE == 'android'))
        {
            $getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
            $websiteStr = str_replace(".", "-", $getWebsiteName);
            $this->_directoryName = 'android-' . $websiteStr . '-app-builder';
            $this->_getAPPBuilderTarFileName = $this->_directoryName . '.tar';
            $this->_getAPPBuilderBaseURL = 'public/' . $this->_directoryName;
            // Set the app builder settings file url.
            $getAPPBuilderParentPath = APPLICATION_PATH . DIRECTORY_SEPARATOR
                    . $this->_getAPPBuilderBaseURL;

            $getAPPBuilderSettingsFilePath = $getAPPBuilderParentPath . DIRECTORY_SEPARATOR
                    . $this->_getAPPBuilderSettingsFileName;
            if (@file_exists($getAPPBuilderSettingsFilePath))
                include $getAPPBuilderSettingsFilePath;
            if (isset($appBuilderParams) && !empty($appBuilderParams) && isset($appBuilderParams['map_key']) && !empty($appBuilderParams['map_key'])) {
                $response['map_key'] = $appBuilderParams['map_key'];
            }
        }
        // GIF Player Plugin Settings Integration
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegifplayer') && !empty($viewer->getIdentity()) && ((_IOS_VERSION > '3.0.0') || (_ANDROID_VERSION > '5.0.0')))
        {
            $response['sitegifplayer']['allowGIF'] = Engine_Api::_()->authorization()->isAllowed('sitegifplayer', $viewer, 'allow_gif_feed');
            $response['sitegifplayer']['allowGIFcomment'] = Engine_Api::_()->authorization()->isAllowed('sitegifplayer', $viewer, 'allow_gifcomment');
            $response['sitegifplayer']['giphySearch'] = $settings->getSetting('sitegifplayer.giphy.search', 3);
            $response['sitegifplayer']['duration'] =  $settings->getSetting('sitegifplayer.duration', 60);
        }

        // profile page blocks work
        if (_IOS_VERSION && _ANDROID_VERSION) {// check work pending
            $response['profile']['friends_block'] = $settings->getSetting('siteapi.profile.friends.block',1);
            $response['profile']['photos_block'] = $settings->getSetting('siteapi.profile.photos.block',1);
        }
         
        if (!empty($response)) {
            $this->respondWithSuccess($response);
        }
    }
    
    public function getCurrencyDetails(){
        if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitemulticurrency"))
                return array();
         $baseCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
         $defaultCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemulticurrency.default.display.currency', $baseCurrency);
         Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
         $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $local = ($viewer_id) ? $viewer->locale : 'auto';
        $localeObject = new Zend_Locale($local);
         $name = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $defaultCurrency);
                    
        return array(
            "default"=>$defaultCurrency,
            'title'=>$name
        );
    }

    public function favouriteAction() {
        //GET THE VIEWER.
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET THE VALUE OF RESOURCE ID AND RESOURCE TYPE AND FAVOURITE ID.
        $resource_id = $this->_getParam('resource_id');
        $resource_type = $this->_getParam('resource_type');
        $favourite_id = $this->_getParam('favourite_id');

        //GET THE RESOURCE.
        if ($resource_type == 'member') {
            $resource = Engine_Api::_()->getItem('user', $resource_id);
        } else {
            $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
            $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
        }
        //GET THE CURRENT UESRID AND SETTINGS.
        $viewer_id = $loggedin_user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ((empty($loggedin_user_id))) {
            return;
        }
        //CHECK THE FAVOURITE ID.
        if (empty($favourite_id)) {

            //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF FAVOURITING AN APPLICATION.
            $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($resource_type, $resource_id);
            //CHECK THE THE ITEM IS FAVOURITED OR NOT.
            if (empty($favourite_id_temp[0]['favourite_id'])) {

                $favouriteTable = Engine_Api::_()->getItemTable('seaocore_favourite');
                $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
                $db = $favouriteTable->getAdapter();
                $db->beginTransaction();
                try {

                    //START NOTIFICATION WORK.
                    if ($resource_type == 'forum_topic') {
                        $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->user_id;
                        $label = '{"label":"forum topic"}';
                        $object_type = $resource_type;
                    } else if ($resource_type == 'user') {
                        $getOwnerId = $resource_id;
                        $label = '{"label":"profile"}';
                        $object_type = 'user';
                    } else {
                        if ($resource_type == 'album_photo') {
                            $label = '{"label":"photo"}';
                        } else if ($resource_type == 'group_photo') {
                            $label = '{"label":"group photo"}';
                        } else if ($resource_type == 'sitepageevent_event') {
                            $label = '{"label":"page event"}';
                        } else if ($resource_type == 'sitepage_page') {
                            $label = '{"label":"page"}';
                        } else if ($resource_type == 'sitebusiness_business') {
                            $label = '{"label":"business"}';
                        } else if ($resource_type == 'video') {
                            $label = '{"label":"video"}';
                        } else {
                            $label = '{"label":"' . $resource->getShortType() . '"}';
                        }
                        // if (!strstr($resource_type, 'siteestore_product')) {
                        //     $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->getOwner()->user_id;
                        // }
                        $object_type = $resource_type;
                    }
                    if ($object_type == 'sitestore_store')
                        $label = '';

                    if (!empty($resource)) {
                        // if ($resource->getOwner()->getIdentity() != $loggedin_user_id) {
                        //     //ADD NOTIFICATION 
                        //     $notifyData = $notify_table->createRow();
                        //     $notifyData->user_id = $getOwnerId;
                        //     $notifyData->subject_type = $viewer->getType();
                        //     $notifyData->subject_id = $viewer->getIdentity();
                        //     $notifyData->object_type = $object_type;
                        //     $notifyData->object_id = $resource_id;
                        //     $notifyData->type = 'favourited';
                        //     $notifyData->params = $label;
                        //     $notifyData->date = date('Y-m-d h:i:s', time());
                        //     $notifyData->save();
                        // }
                        //ADD FAVOURITE
                        $favourite_id = $favouriteTable->addFavourite($resource, $viewer)->favourite_id;
                    }
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    $this->respondWithValidationError('internal_server_error', $e->getMessage());
                }
                $this->successResponseNoContent('no_content', true);
            } else {
                $response = array();
                $response['response']['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
                $this->respondWithSuccess($response);
            }
        } else {

            //START DELETE NOTIFICATION
            // Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?' => 'favourited', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $resource_type, 'object_id = ?' => $resource_id));
            //END DELETE NOTIFICATION
            ////START UNFAVOURITE WORK.
            //HERE 'PAGE OR LIST PLUGIN' CHECK WHEN UNFAVOURITE
            if (!empty($resource) && isset($resource->favourite_count)) {
                $resource->favourite_count--;
                $resource->save();
            }
            $contentTable = Engine_Api::_()->getDbTable('favourites', 'seaocore')->delete(array('favourite_id =?' => $favourite_id));
            //END UNFAVOURITE WORK.
            $this->successResponseNoContent('no_content', true);
        }
    }   

    public function getProfilePhotoMenusAction() {

        $bodyParams = array();
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithSuccess($bodyParams, true);

        $viewer = Engine_Api::_()->user()->getViewer();

        // $coverPhoto = $this->_getParam('cover_photo');
        $profilePhoto = $this->_getParam('profile_photo');
        $type = $this->_getParam('special', 'profile');

        $subject_id = $this->_getParam('subject_id');
        $subject_type = $this->_getParam('subject_type');
        $subject = Engine_Api::_()->getItem($subject_type, $subject_id);

        if (empty($subject))
            $this->respondWithError('unauthorized');

        try {
            $getUserCoverPhotoMenu = Engine_Api::_()->getApi('Siteapi_Core', 'core')->getProfilePhotoMenu($subject, $profilePhoto, $type);
            if (!empty($getUserCoverPhotoMenu))
                $bodyParams['response'] = $getUserCoverPhotoMenu;
            $this->respondWithSuccess($bodyParams);
        } catch (Exception $e) {
            echo $e;
            die;
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

    } 

    public function uploadProfilePhotoAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $subject_id = $this->_getParam('subject_id');
        $resource_type = $subject_type = $this->_getParam('subject_type');
        $subject = Engine_Api::_()->getItem($subject_type, $subject_id);
        $photo_id = $this->_getParam('photo_id');

        $special = $this->_getParam('special', 'profile');
        $level_id = $subject->getOwner()->level_id;

        $cover_photo_preview = 0;
        $can_edit = 0;

        if (empty($subject))
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $moduleName = strtolower($subject->getModuleName());

        $fieldName = strtolower($subject->getShortType()) . '_cover';

        //START MANAGE-ADMIN CHECK
        if (Engine_Api::_()->getApi('Siteapi_Core', 'core')->checkConditionsForAlbum($moduleName)) {
            $can_edit = $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'edit');
            if (empty($isManageAdmin)) {
                $this->respondWithError('unauthorized');
            }
        } else {
            if ($moduleName == 'sitereview') {
                $can_edit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");
            } else {
                $can_edit = $subject->authorization()->isAllowed($viewer, 'edit');
            }
        }

        //CHECK FORM VALIDATION
        $file = '';
        $notNeedToCreate = false;

        $db = Engine_Db_Table::getDefaultAdapter();
        if ($resource_type != 'sitereview_listing') {
            $tableName = Engine_Api::_()->getItemtable($resource_type)->info('name');
            $field = $db->query("SHOW COLUMNS FROM $tableName LIKE '$fieldName'")->fetch();
            if (empty($field)) {
                $db->query("ALTER TABLE `$tableName` ADD `$fieldName` INT( 11 ) NOT NULL DEFAULT '0'");
            }
        } else {
            $tableName = 'engine4_sitereview_otherinfo';
            $field = $db->query("SHOW COLUMNS FROM $tableName LIKE '$fieldName'")->fetch();
            if (empty($field)) {
                $db->query("ALTER TABLE `$tableName` ADD `$fieldName` INT( 11 ) NOT NULL DEFAULT '0'");
            }
        }

        if ($photo_id) {
            $photo = Engine_Api::_()->getItem("$moduleName" . "_photo", $photo_id);
            if ($moduleName != 'album') {
                $album = Engine_Api::_()->getItem("$moduleName" . "_album", $photo->album_id);
            } else {
                $album = Engine_Api::_()->getItem("album", $photo->album_id);
            }

            if ($moduleName == 'album' || $moduleName == 'siteevent' || $moduleName == 'sitestoreproduct' || $moduleName == 'sitereview' || $moduleName == 'sitevideo' || $moduleName == 'sitecrowdfunding') {
                $notNeedToCreate = true;
            }
            if ($photo->file_id && !$notNeedToCreate)
                $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
        }

        //UPLOAD PHOTO
        if ($_FILES['photo'] !== null || $photo || ($notNeedToCreate && $file)) {

            //PROCESS
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                //CREATE PHOTO
                $tablePhoto = Engine_Api::_()->getItemTable($moduleName . "_photo");
                $getShortType = ucfirst($subject->getShortType());

                $primaryTableKey = Engine_Api::_()->getItemtable($subject->getType())->info('primary');
                $tablePrimaryFieldName = $primaryTableKey[1];
                if (!$notNeedToCreate) {
                    $photo = $tablePhoto->createRow();

                    if (isset($photo->user_id)) {
                        $user_id = 'user_id';
                    } elseif (isset($photo->owner_id)) {
                        $user_id = 'owner_id';
                    }
                    $photo->setFromArray(array(
                        $user_id => Engine_Api::_()->user()->getViewer()->getIdentity(),
                        $tablePrimaryFieldName => $subject->getIdentity()
                    ));
                    $photo->save();

                    if ($file) {
                        $this->setMainPhoto($file, $photo, $moduleName);   
                    } else {
                        $this->setMainPhoto($_FILES['photo'], $photo, $moduleName);
                    }
                }

                // if ($special == 'cover') {
                    // if ($moduleName != 'sitereview') {
                    //     $subject->$fieldName = $photo->photo_id;
                    // } else {
                    //     $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitereview');
                    //     $row = $tableOtherinfo->getOtherinfo($subject->listing_id);
                    //     if (empty($row)) {
                    //         Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->insert(array(
                    //             'listing_id' => $subject->listing_id,
                    //             $fieldName => $photo->photo_id
                    //         )); //COMMIT  
                    //     } else {
                    //         $tableOtherinfo->update(array($fieldName => $photo->photo_id), array('listing_id = ?' => $subject->listing_id));
                    //     }
                    // }
                // } else {
                    if ($moduleName != 'album') {
                        if (isset($subject->photo_id)) {
                            $subject->photo_id = $photo->file_id;
                        } elseif (isset($subject->file_id)) {
                            $subject->file_id = $photo->file_id;
                        }
                    } else {
                        $subject->photo_id = $photo->getIdentity();
                    }
                    if (Engine_Api::_()->getApi('Siteapi_Core', 'core')->checkConditionsForAlbum($moduleName)) {
                        if ($moduleName != 'album') {
                            $photo->album_id = $photo->collection_id = Engine_Api::_()->getItemTable($moduleName . "_album")->getDefaultAlbum($subject->getIdentity())->album_id;
                        } else {
                            $photo->album_id = $photo->collection_id = Engine_Api::_()->getItemTable("album")->getDefaultAlbum($subject->getIdentity())->album_id;
                        }

                        $photo->save();
                    } else {

                        if ($moduleName != 'album') {
                            $album_id = Engine_Api::_()->getItemTable($moduleName . "_album")->select()
                                    ->from(Engine_Api::_()->getItemTable($moduleName . "_album")->info('name'), array('album_id'))
                                    ->where("$tablePrimaryFieldName = ?", $subject->getIdentity())
                                    ->query()
                                    ->fetchColumn();
                        } else {
                            $album_id = Engine_Api::_()->getItemTable("album")->select()
                                    ->from(Engine_Api::_()->getItemTable("album")->info('name'), array('album_id'))
                                    ->where("$tablePrimaryFieldName = ?", $subject->getIdentity())
                                    ->query()
                                    ->fetchColumn();
                        }
                        $photo->album_id = $album_id;
                        if (isset($photo->collection_id)) {
                            $photo->collection_id = $album_id;
                        }
                        $photo->save();
                    }
                // }

                $subject->save();
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                //ADD ACTIVITY
                if (Engine_Api::_()->getApi('Siteapi_Core', 'core')->checkConditionsForAlbum($moduleName)) {

                    $activityFeedType = null;
                    $ownerFunction = 'is' . $getShortType . 'Owner';
                    $feedTypeFunction = 'isFeedType' . $getShortType . 'Enable';
                    if (Engine_Api::_()->$moduleName()->$ownerFunction($subject) && Engine_Api::_()->$moduleName()->$feedTypeFunction()) {
                        $activityFeedType = $moduleName . '_admin_profile_photo';
                    }
                    elseif ($subject->all_post || Engine_Api::_()->$moduleName()->$ownerFunction($subject)) {
                        $activityFeedType = $moduleName . '_profile_photo_update';
                    }

                    if ($activityFeedType) {
                        $action = $activityApi->addActivity($viewer, $subject, $activityFeedType);
                    }

                    if ($action) {
                        Engine_Api::_()->getApi('subCore', $moduleName)->deleteFeedStream($action);
                        if ($photo)
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                    }
                }
                else {
                    if ($moduleName == 'siteevent') {
                        $activityFeedType = $moduleName . '_change_photo';
                        $action = $activityApi->addActivity($viewer, $subject, Engine_Api::_()->siteevent()->getActivtyFeedType($subject, $activityFeedType));
                        if ($action) {
                            if ($photo)
                                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                        }
                    } else if ($moduleName == 'album') {
                        $activityFeedType = $moduleName . '_cover_update';
                        $action = $activityApi->addActivity($viewer, $subject, $activityFeedType);
                        if ($action) {
                            if ($photo)
                                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                        }
                    }
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    public function removeProfilePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject_id = $this->_getParam('subject_id');
        $subject_type = $this->_getParam('subject_type');
        $subject = Engine_Api::_()->getItem($subject_type, $subject_id);

        if (empty($subject))
            $this->respondWithError('unauthorized');

        $moduleName = strtolower($subject->getModuleName());

        $level_id = $subject->getOwner()->level_id;

        if ($this->getRequest()->isPost()) {
            try {
                $db = Engine_Db_Table::getDefaultAdapter();
                $resource_type = $subject->getType();
                $tableName = Engine_Api::_()->getItemtable($resource_type)->info('name');

                $field = $db->query("SHOW COLUMNS FROM ".$tableName." LIKE 'file_id'")->fetch();
                if (empty($field))
                    $subject->photo_id = 0;
            else
                $subject->file_id = 0;
                $subject->save();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {echo $e;die;
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Set a photo
     *
     * @param array photo
     * @return photo object
     */
    public function setMainPhoto($photo, $photoObject, $moduleName) {

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
        if (isset($photoObject->user_id)) {
            $user_id = 'user_id';
            $user_id_value = $photoObject->user_id;
        } elseif (isset($photoObject->owner_id)) {
            $user_id = 'owner_id';
            $user_id_value = $photoObject->owner_id;
        }
        $params = array(
            'parent_type' => $photoObject->getType(),
            'parent_id' => $photoObject->getIdentity(),
            $user_id => $user_id_value,
            'name' => basename($fileName),
        );
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        if ($moduleName != 'album') {

            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;

            $image = Engine_Image::factory();
            if (!empty($hasVersion)) {
                $image->open($file)
                        ->resize(720, 720)
                        ->write($mainPath)
                        ->destroy();

                // Resize image (profile)
                $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(200, 400)
                        ->write($profilePath)
                        ->destroy();

                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(140, 160)
                        ->write($normalPath)
                        ->destroy();
            } else {
                $image->open($file)->autoRotate()
                        ->resize(720, 720)
                        ->write($mainPath)
                        ->destroy();

                // Resize image (profile)
                $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize(200, 400)
                        ->write($profilePath)
                        ->destroy();

                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize(140, 160)
                        ->write($normalPath)
                        ->destroy();
            }
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
            $iMain = $filesTable->createFile($mainPath, $params);
            $iProfile = $filesTable->createFile($profilePath, $params);
            $iIconNormal = $filesTable->createFile($normalPath, $params);
            $iSquare = $filesTable->createFile($squarePath, $params);

            $iMain->bridge($iProfile, 'thumb.profile');
            $iMain->bridge($iIconNormal, 'thumb.normal');
            $iMain->bridge($iSquare, 'thumb.icon');

            // Remove temp files
            @unlink($mainPath);
            @unlink($profilePath);
            @unlink($normalPath);
            @unlink($squarePath);
        } else {
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
            $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);

            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();
            if (!empty($hasVersion)) {
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
            } else {
                $image->open($file)->autoRotate()
                        ->resize($mainWidth, $mainHeight)
                        ->write($mainPath)
                        ->destroy();

                $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
                $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize($normalWidth, $normalHeight)
                        ->write($normalPath)
                        ->destroy();

                $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
                $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
                // Resize image (normal)
                $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize($normalLargeWidth, $normalLargeHeight)
                        ->write($normalLargePath)
                        ->destroy();
            }
            // Store
            try {
                $iMain = $filesTable->createFile($mainPath, $params);
                $iIconNormal = $filesTable->createFile($normalPath, $params);
                $iMain->bridge($iIconNormal, 'thumb.normal');
                $iIconNormalLarge = $filesTable->createFile($normalLargePath, $params);
                $iMain->bridge($iIconNormalLarge, 'thumb.large');
            } catch (Exception $e) {
                // Remove temp files
                @unlink($mainPath);
                @unlink($normalPath);
                @unlink($normalLargePath);
                // Throw
                if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                    throw new Album_Model_Exception($e->getMessage(), $e->getCode());
                } else {
                    throw $e;
                }
            }
        }

        $photoObject->modified_date = date('Y-m-d H:i:s');
        $photoObject->file_id = $iMain->file_id;
        $photoObject->save();
        if (!empty($tmpRow)) {
            $tmpRow->delete();
        }

        return $photoObject;
    } 
}
