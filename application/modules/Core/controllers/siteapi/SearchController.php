<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    SearchController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Core_SearchController extends Siteapi_Controller_Action_Standard {
    /*
     * SocialEngine default search apis
     */

    public function indexAction() {
        $response = null;
        $searchApi = Engine_Api::_()->getApi('search', 'core');
        $forumSearch = $this->getRequestParam('forumSearch', 0);
        $hasItem = array();
        // check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
        if (!$require_check) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
            if (empty($viewer_id))
                $this->respondWithError('unauthorized');
        }

        // Search page form
        if ($this->getRequestParam('form', true)) {
            $searchForm = array();
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'query',
                'label' => $this->translate('Search'),
            );

            if (isset($forumSearch) && !empty($forumSearch)) {
                $forumOptions = array(
                    'forum' => 'Forums',
                    'forum_post' => 'Forum Posts',
                    'forum_topic' => 'Forum Topics',
                );
                $searchForm[] = array(
                    'type' => 'Select',
                    'name' => 'type',
                    'multiOptions' => $forumOptions,
                    'value' => 'forum_topic'
                );
            } else {
                // Get available types
                $availableTypes = $this->getAvailableTypes();

                if (is_array($availableTypes) && count($availableTypes) > 0) {
                    $options = array('' => 'Everything');
                    $enabledModName = @explode(",", DEFAULT_APP_MODULES);
                    if (!empty($enabledModName)) {
                        try {
                            foreach ($availableTypes as $key => $value) {
                                if (in_array($value['type'], $hasItem))
                                    continue;
                                $isItem = Engine_Api::_()->hasItemType($value['type']);
                                if (!empty($isItem)) {
                                    $item = Engine_Api::_()->getItem($value['type'], $value['id']);
                                    if (!empty($item)) {
                                        $hasItem[] = $value['type'];
                                        $modName = $item->getModuleName();
                                        $modName = @strtolower($modName);
                                        if (in_array($modName, $enabledModName)) {
                                            $options[$value['type']] = $this->translate(strtoupper('ITEM_TYPE_' . $value['type']));
                                        }
                                    }
                                }
                            }
                        } catch (Exception $ex) {
                            // Blank Exception
                        }
                    }

                    if ((_ANDROID_VERSION && _ANDROID_VERSION >= '1.7.7.1') || (_IOS_VERSION && _IOS_VERSION >= '1.6.5')) {
                        $options = array();
                        if (_ANDROID_VERSION && _ANDROID_VERSION != '1.6.1.1')
                            $type = 'android';
                        else if (_IOS_VERSION && _IOS_VERSION != '1.4.3')
                            $type = 'ios';

                        $table = ($type === 'ios') ? Engine_Api::_()->getDbtable('menus', 'siteiosapp') : Engine_Api::_()->getDbtable('menus', 'siteandroidapp');
                        $select = $table->select();
                        $select->where('module IS NOT NULL');
                        $select->where('status = ?', 1);
                        $select->order('order asc');
                        $menuObj = $table->fetchAll($select);
                        $notIncludeArray = array('activity', 'messages', 'core', 'cometchat',"primemessenger");
                        $options[] = $this->translate('Everything');
                        foreach ($menuObj as $menu) {
                            if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($menu->module))
                                continue;

                            if (in_array($menu->module, $notIncludeArray))
                                continue;

                            if ($menu->name == 'sitereview_wishlist')
                                continue;

                            if ($menu->module == 'user' && $menu->name != 'core_main_user')
                                continue;
                            
                            if ($menu->module == 'siteevent' && $menu->name == 'core_main_diaries')
                                continue;
                            
                            $moduleTitle = Engine_Api::_()->getDbTable('integrated', 'seaocore')->getModuleTitle($menu->module);
                            if (strstr($moduleTitle, 'Extension'))
                                continue;

                            if ($menu->module == 'sitereview') {
                                if (isset($menu->params) && !empty($menu->params)) {
                                    $params = @unserialize($menu->params);
                                    $options[$menu->module . '_' . $params['listingtype_id']] = $this->translate($menu->dashboard_label);
                                    continue;
                                }
                            }
    
                        $options[$menu->module] = $this->translate($menu->dashboard_label);
                        }
                    }

                    $searchForm[] = array(
                        'type' => 'Select',
                        'name' => 'type',
                        'multiOptions' => $options,
                        'value'=>0
                    );
                }
            }
        }

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $this->translate('Search')
        );

        $response['form'] = $searchForm;


        // Get the content information array in post request.
        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getApi('Core', 'siteapi')->setView();
            Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
            $query = (string) $_REQUEST['query'];
            $type = (string) $_REQUEST['type'];

            $page = (int) $this->getRequestParam('page', 1);
            $limit = (int) $this->getRequestParam('limit', 20);
            if ($query) {
                $paginator = $this->getPaginator($query, $type);
                $paginator->setCurrentPageNumber($page);
                $paginator->setItemCountPerPage($limit);
                $response['totalItemCount'] = $paginator->getTotalItemCount();

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteadvsearch')) {
                    $values['text'] = $query;
                    $values['pagination'] = 1;
                    $values['resource_type'] = $type;
                    $paginator = Engine_Api::_()->siteadvsearch()->getCoreSearchData($values);
                    $paginator->setCurrentPageNumber($page);
                    $paginator->setItemCountPerPage($limit);
                    $response['totalItemCount'] = $paginator->getTotalItemCount();
                }
            }

            foreach ($paginator as $item) {
                $isItemTypeAvailable = Engine_Api::_()->hasItemType($item->type);
                if (empty($isItemTypeAvailable))
                    continue;

                //@todo classified_album get href issue
                if ($item->type == 'classified_album')
                    continue;

                $item = Engine_Api::_()->getItem($item->type, $item->id);
                if (!$item)
                    continue;

                $getItemType = $item->getType();
                if ($getItemType == 'user')
                    $itemArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($item);
                else
                    $itemArray = $item->toArray();

                //Member verification Work............... 
                $itemArray['showVerifyIcon'] = ($getItemType == 'user') ? Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($item) : Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($item->getOwner());

                $itemArray['type'] = (($type == 'forum_post') || ($type == 'forum_topic')) ? $type : $item->getModuleName();

                $itemType = $item->getType();
                if($itemType=='video'){
                    $itemType = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')?'sitevideo':'video';
                }
                $itemArray['item_type'] = (isset($itemType) && !empty($itemType)) ? $itemType : "";
                $itemArray['type'] = ($itemArray['type'] == 'Sitealbum') ? 'Album' : $itemArray['type'];
                $itemArray['type'] = ($itemArray['type'] == 'Core') ? '' : $itemArray['type'];

                if (isset($itemType) && $itemType == 'forum_topic')
                    $itemArray['slug'] = $item->getSlug();

                if (isset($itemType) && $itemType == 'forum_post')
                    $itemArray['slug'] = $item->getSlug();

                if (isset($itemType) && !empty($itemType) && $itemType == 'sitereview_review') {
                    if (isset($item->resource_type) && !empty($item->resource_type) && isset($item->resource_id) && !empty($item->resource_id)) {
                        $listingObj = Engine_Api::_()->getItem($item->resource_type, $item->resource_id);
                        if (isset($listingObj) && !empty($listingObj)) {
                            $itemArray['listingtype_id'] = $listingObj->listingtype_id;
                            $itemArray['listing_title'] = $listingObj->title;
                        }
                    }
                }

                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($item);
                $itemArray = array_merge($itemArray, $getContentImages);

                $module_name = $item->getModuleName();
               if(strtolower($module_name) == 'video'){
                   $itemArray['video_type'] = Engine_Api::_()->getApi('Core', 'siteapi')->videoType($item->type);
               }
                $itemArray['module_name'] = ($module_name == 'Sitealbum') ? 'album' : strtolower($module_name);
                $itemArray['module_title'] = @ucfirst($item->getShortType());
                $itemArray['module_title'] = ($itemArray['module_title'] == 'Core') ? '' : $itemArray['module_title'];

                if (!isset($itemArray['url']) && isset($itemArray['uri']))
                    $itemArray['url'] = $itemArray['uri'];

                if (isset($itemArray['content_url']) && !isset($itemArray['url']))
                    $itemArray['url'] = $itemArray['content_url'];

                if (isset($itemArray['content_url']) && !isset($itemArray['uri']))
                    $itemArray['uri'] = $itemArray['content_url'];

                 if (strstr($itemArray['item_type'], 'video')&& !strstr($itemArray['item_type'], 'channel') && !strstr($itemArray['item_type'], 'playlist')){
                          $itemArray['content_url'] =Engine_Api::_()->getApi('Core', 'siteapi')->getVideoURL($item);
                }
                
                if ($itemType == 'forum_post')
                    $topic = $item->getParent();
                else if ($itemType == 'forum_topic')
                    $topic = $item;

                if (!empty($topic))
                    $itemArray['slug'] = $topic->getSlug();

                if (isset($itemArray['title']))
                    $itemArray['title'] = strip_tags($itemArray['title']);

                if (isset($itemArray['body'])){
                    $itemArray['body'] = $item->getDescription();
                    $itemArray['body'] = strip_tags($itemArray['body']);
                }

                $itemsInormationArray[] = $itemArray;
            }
            $response['actualCount'] = count($itemsInormationArray);
            $response['result'] = $itemsInormationArray;
        }
        $this->respondWithSuccess($response);
    }

    /*
     * Check search result availability
     */

    private function getAvailableTypes($type = null) {
        if (empty($type)) {
            $type = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
                    ->query('SELECT DISTINCT `type`, `id` FROM `engine4_core_search`')
                    ->fetchAll();
//            $type = array_intersect($type, Engine_Api::_()->getApi('Core', 'siteapi')->getItemTypes());
        }

        return $type;
    }

    /*
     * Get paginator for searhc result
     */

    private function getPaginator($text, $type = null) {
        return Zend_Paginator::factory($this->getSelect($text, $type));
    }

    /*
     * Make search query
     */

    private function getSelect($text, $type = null) {
        // Build base query
        $table = Engine_Api::_()->getDbtable('search', 'core');
        $db = $table->getAdapter();
        $select = $table->select()
                ->where(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (? IN BOOLEAN MODE)', $text)))
                ->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)));

        // Filter by item types
        if (!empty($type))
            $select->where('type = ?', $type);

//        $availableTypes = Engine_Api::_()->getApi('Core', 'siteapi')->getItemTypes();
//        if ($type && in_array($type, $availableTypes)) {
//            $select->where('type = ?', $type);
//        } else {
//            $select->where('type IN(?)', $availableTypes);
//        }

        return $select;
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
            default : return $type;
        }
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
    }

}
