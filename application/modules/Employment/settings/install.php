<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: install.php 9893 2013-02-14 00:00:53Z shaun $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Employment_Installer extends Engine_Package_Installer_Module
{
    protected $_dropColumnsOnPreInstall = array(
        '4.9.0' => array(
            'engine4_employment_employments' => array('like_count'),
            'engine4_employment_albums' => array('like_count'),
            'engine4_employment_photos' => array('like_count')
        ),
        '4.9.3' => array(
            'engine4_employment_employments' => array('view_privacy')
        )
    );

    public function onInstall()
    {
        //if($this->_databaseOperationType != 'upgrade'){
            $this->_addUserProfileContent();
            $this->_addHashtagSearchContent();
            $this->_addBrowsePage();
            $this->_addViewPage();

            $this->_addCreatePage();
            $this->_addManagePage();

            $this->_addPrivacyColumn();
        //}
        parent::onInstall();
    }

    protected function _addHashtagSearchContent()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // hashtag search page
        $pageId = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'core_hashtag_index')
        ->limit(1)
        ->query()
        ->fetchColumn();
        if(!$pageId){
            return;
        }

        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $pageId)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'employment.hashtag-search-results')
        ;
        $info = $select->query()->fetch();

        if( empty($info) ) {

            // container_id (will always be there)
            $select = new Zend_Db_Select($db);
            $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $pageId)
                ->where('type = ?', 'container')
                ->limit(1);
            $containerId = $select->query()->fetchObject()->content_id;

            // middle_id (will always be there)
            $select = new Zend_Db_Select($db);
            $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $containerId)
                ->where('type = ?', 'container')
                ->where('name = ?', 'middle')
                ->limit(1);
            $middleId = $select->query()->fetchObject()->content_id;

            // tab_id (tab container) may not always be there
            $select
                ->reset('where')
                ->where('type = ?', 'widget')
                ->where('name = ?', 'core.container-tabs')
                ->where('page_id = ?', $pageId)
                ->limit(1);
            $tabId = $select->query()->fetchObject();
            if( $tabId && @$tabId->content_id ) {
                $tabId = $tabId->content_id;
            } else {
                $tabId = null;
            }

            // tab on profile
            $db->insert('engine4_core_content', array(
                'page_id' => $pageId,
                'type'    => 'widget',
                'name'    => 'employment.hashtag-search-results',
                'parent_content_id' => ($tabId ? $tabId : $middleId),
                'order'   => 200,
                'params'  => '{"title":"Employment","titleCount":true}',
            ));
        }
    }

    protected function _addManagePage()
    {
        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'employment_index_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();

        // insert if it doesn't exist yet
        if( !$pageId ) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'employment_index_manage',
                'displayname' => 'Employment Manage Page',
                'title' => 'My Listings',
                'description' => 'This page lists a user\'s employment listings.',
                'custom' => 0,
            ));
            $pageId = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $pageId,
                'order' => 1,
            ));
            $topId = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $mainId = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $topId,
            ));
            $topMiddleId = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 2,
            ));
            $mainMiddleId = $db->lastInsertId();

            // Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 1,
            ));
            $mainRightId = $db->lastInsertId();


            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'employment.browse-menu',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 1,
            ));

            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'employment.browse-search',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 1,
            ));

            // Insert gutter menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'employment.browse-menu-quick',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 2,
            ));
        }
    }

    protected function _addCreatePage()
    {

        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'employment_index_create')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if( !$pageId ) {

            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'employment_index_create',
                'displayname' => 'Employment Create Page',
                'title' => 'Post a New Listing',
                'description' => 'This page is the employment create page.',
                'custom' => 0,
            ));
            $pageId = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $pageId,
                'order' => 1,
            ));
            $topId = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $mainId = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $topId,
            ));
            $topMiddleId = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 2,
            ));
            $mainMiddleId = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'employment.browse-menu',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 1,
            ));
        }
    }

    protected function _addBrowsePage()
    {
        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'employment_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

        // insert if it doesn't exist yet
        if( !$pageId ) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'employment_index_index',
                'displayname' => 'Employment Browse Page',
                'title' => 'Employment Browse',
                'description' => 'This page lists employment listings.',
                'custom' => 0,
            ));
            $pageId = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $pageId,
                'order' => 1,
            ));
            $topId = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $mainId = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $topId,
            ));
            $topMiddleId = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 2,
            ));
            $mainMiddleId = $db->lastInsertId();

            // Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 1,
            ));
            $mainRightId = $db->lastInsertId();

            // Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 1,
            ));
            $mainLeftId = $db->lastInsertId();

            // Insert banner
            $db->insert('engine4_core_banners', array(
                'name' => 'employment',
                'module' => 'employment',
                'title' => 'Find Jobs',
                'body' => 'Find a job or post your own in the Employment listings!',
                'photo_id' => 0,
                'params' => '{"label":"Post a New Listing","route":"employment_general","routeParams":{"action":"create"}}',
                'custom' => 0
            ));
            $bannerId = $db->lastInsertId();

            if( $bannerId ) {
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'core.banner',
                    'page_id' => $pageId,
                    'parent_content_id' => $topMiddleId,
                    'params' => '{"title":"","name":"core.banner","banner_id":"'. $bannerId .'","nomobile":"0"}',
                    'order' => 1,
                ));
            }

            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'employment.browse-menu',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 2,
            ));

            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 1,
            ));

            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'employment.browse-search',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 1,
            ));

            // Insert gutter menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'employment.browse-menu-quick',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 2,
            ));

            // Insert list categories
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'employment.list-categories',
                'page_id' => $pageId,
                'parent_content_id' => $mainLeftId,
                'order' => 3,
            ));
        }
    }

    protected function _addViewPage() {
    
      $db = $this->getDb();
      $select = new Zend_Db_Select($db);

      // Check if it's already been placed
      $pageId = $db->select()
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'employment_index_view')
          ->limit(1)
          ->query()
          ->fetchColumn(0);
          
      if( !$pageId ) {
        $db->insert('engine4_core_pages', array(
            'name' => 'employment_index_view',
            'displayname' => 'Employment View Page',
            'title' => 'View Employment',
            'description' => 'This is the view page for a employment.',
            'custom' => 0,
            'provides' => 'subject=employment',
        ));
        $pageId = $db->lastInsertId('engine4_core_pages');

        // containers
        $db->insert('engine4_core_content', array(
            'page_id' => $pageId,
            'type' => 'container',
            'name' => 'main',
            'parent_content_id' => null,
            'order' => 1,
            'params' => '',
        ));
        $containerId = $db->lastInsertId('engine4_core_content');

        $db->insert('engine4_core_content', array(
            'page_id' => $pageId,
            'type' => 'container',
            'name' => 'middle',
            'parent_content_id' => $containerId,
            'order' => 3,
            'params' => '',
        ));
        $middleId = $db->lastInsertId('engine4_core_content');
        
        $db->insert('engine4_core_content', array(
            'page_id' => $pageId,
            'type' => 'widget',
            'name' => 'employment.breadcrumb',
            'parent_content_id' => $middleId,
            'order' => 1,
            'params' => '',
        ));

        // middle column content
        $db->insert('engine4_core_content', array(
            'page_id' => $pageId,
            'type' => 'widget',
            'name' => 'core.content',
            'parent_content_id' => $middleId,
            'order' => 2,
            'params' => '',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $pageId,
            'type' => 'widget',
            'name' => 'core.comments',
            'parent_content_id' => $middleId,
            'order' => 3,
            'params' => '',
        ));

        // Insert left
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'left',
            'page_id' => $pageId,
            'parent_content_id' => $containerId,
            'order' => 1,
        ));
        $leftId = $db->lastInsertId();
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'employment.list-recent-employments',
            'page_id' => $pageId,
            'parent_content_id' => $leftId,
            'order' => 3,
            'params' => '{"title":"Recent Employment Listings","recentType":"creation","nomobile":"0","itemCountPerPage":"3","name":"employment.list-recent-employments"}',
        ));

        // Insert right
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $pageId,
            'parent_content_id' => $containerId,
            'order' => 1,
        ));
        $rightId = $db->lastInsertId();
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'employment.list-popular-employments',
            'page_id' => $pageId,
            'parent_content_id' => $rightId,
            'order' => 3,
            'params' => '{"title":"Popular Employment Listings","popularType":"view","nomobile":"0","itemCountPerPage":"3","name":"employment.list-popular-employments"}',
        ));
      } else if($pageId) {
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $pageId)
            ->where('type = ?', 'container')
            ->limit(1);
        $containerId = $select->query()->fetchObject()->content_id;

        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_content')
            ->where('parent_content_id = ?', $containerId)
            ->where('type = ?', 'container')
            ->where('name = ?', 'middle')
            ->limit(1);
        $middleId = $select->query()->fetchObject()->content_id;
        
        $select = new Zend_Db_Select($db);
        $select_content = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $pageId)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'employment.breadcrumb')
            ->limit(1);
        $content_id = $select_content->query()->fetchObject()->content_id;
        
        if(empty($content_id)) {
          $db->query('UPDATE `engine4_core_content` SET `order` = `order`+1 WHERE `engine4_core_content`.`page_id` = "'.$pageId.'" AND `engine4_core_content`.`type` = "widget";');
          // Insert content
          $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'employment.breadcrumb',
            'page_id' => $pageId,
            'parent_content_id' => $middleId,
            'order' => 1,
          ));
        }
      }
    }

    protected function _addUserProfileContent()
    {
        //
        // install content areas
        //
        $db     = $this->getDb();
        $select = new Zend_Db_Select($db);

        // profile page
        $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
        $pageId = $select->query()->fetchObject()->page_id;


        // employment.profile-employments

        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $pageId)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'employment.profile-employments')
        ;
        $info = $select->query()->fetch();
        if( empty($info) ) {

            // container_id (will always be there)
            $select = new Zend_Db_Select($db);
            $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $pageId)
                ->where('type = ?', 'container')
                ->limit(1);
            $containerId = $select->query()->fetchObject()->content_id;

            // middle_id (will always be there)
            $select = new Zend_Db_Select($db);
            $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $containerId)
                ->where('type = ?', 'container')
                ->where('name = ?', 'middle')
                ->limit(1);
            $middleId = $select->query()->fetchObject()->content_id;

            // tab_id (tab container) may not always be there
            $select
                ->reset('where')
                ->where('type = ?', 'widget')
                ->where('name = ?', 'core.container-tabs')
                ->where('page_id = ?', $pageId)
                ->limit(1);
            $tabId = $select->query()->fetchObject();
            if( $tabId && @$tabId->content_id ) {
                $tabId = $tabId->content_id;
            } else {
                $tabId = null;
            }

            // tab on profile
            $db->insert('engine4_core_content', array(
                'page_id' => $pageId,
                'type'    => 'widget',
                'name'    => 'employment.profile-employments',
                'parent_content_id' => ($tabId ? $tabId : $middleId),
                'order'   => 6,
                'params'  => '{"title":"Employments","titleCount":true}',
            ));

        }
    }

    // Create and populate `view_privacy` column
    protected function _addPrivacyColumn()
    {
        if( $this->_databaseOperationType != 'upgrade' || version_compare('4.9.3', $this->_currentVersion, '<=') ) {
            return $this;
        }

        $db = $this->getDb();
        $sql = "ALTER TABLE `engine4_employment_employments` ADD `view_privacy` VARCHAR(24) NOT NULL DEFAULT 'owner' AFTER `closed`";
        try {
            $db->query($sql);
        } catch( Exception $e ) {
            return $this->_error('Query failed with error: ' . $e->getMessage());
        }

        // populate `view_privacy` column
        $select = new Zend_Db_Select($db);

        try {
            $select
                ->from('engine4_authorization_allow', array('resource_id' => 'resource_id', 'privacy_values' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT role)')))
                ->where('resource_type = ?', 'employment')
                ->where('action = ?', 'view')
                ->group('resource_id');

            $privacyList = $select->query()->fetchAll();
        } catch( Exception $e ) {
            return $this->_error('Query failed with error: ' . $e->getMessage());
        }

        foreach( $privacyList as $privacy ) {
            $viewPrivacy = 'owner';
            $privacyVal = explode(",", $privacy['privacy_values']);
            if( engine_in_array('everyone', $privacyVal) ) {
                $viewPrivacy = 'everyone';
            } elseif( engine_in_array('registered', $privacyVal) ) {
                $viewPrivacy = 'registered';
            } elseif( engine_in_array('owner_network', $privacyVal) ) {
                $viewPrivacy = 'owner_network';
            } elseif( engine_in_array('owner_member_member', $privacyVal) ) {
                $viewPrivacy = 'owner_member_member';
            } elseif( engine_in_array('owner_member', $privacyVal) ) {
                $viewPrivacy = 'owner_member';
            }

            $db->update('engine4_employment_employments',array(
                'view_privacy' => $viewPrivacy,
            ), array(
                'employment_id = ?' => $privacy['resource_id'],
            ));
        }

        return $this;
    }
}
