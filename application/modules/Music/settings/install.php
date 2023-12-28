<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: install.php 9878 2013-02-13 03:18:43Z shaun $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Installer extends Engine_Package_Installer_Module
{
  protected $_dropColumnsOnPreInstall = array(
    '4.9.0' => array(
      'engine4_music_playlists' => array('like_count')
    ),
    '4.9.3' => array(
     'engine4_music_playlists' => array('view_privacy'),
    )
  );

  public function onInstall()
  {
    //if($this->_databaseOperationType != 'upgrade'){
      $this->_addUserProfileContent();
      $this->_addMusicBrowsePage();
      $this->_addMusicViewPage();
      $this->_addMusicCreatePage();
      $this->_addMusicManagePage();
      $this->_addPrivacyColumn();
    //}
    parent::onInstall();
  }


  protected function _addMusicManagePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'music_index_manage')
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !$pageId ) {

      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'music_index_manage',
        'displayname' => 'Music Manage Page',
        'title' => 'My Music',
        'description' => 'This page is the music manage page.',
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
        'name' => 'music.browse-menu',
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
        'name' => 'music.browse-search',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'order' => 1,
      ));

      // Insert gutter menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'music.browse-menu-quick',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'order' => 2,
      ));

    }
  }

  protected function _addMusicCreatePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'music_index_create')
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !$pageId ) {

      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'music_index_create',
        'displayname' => 'Music Create Page',
        'title' => 'Upload Music',
        'description' => 'This page is the music create page.',
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
        'name' => 'music.browse-menu',
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

  protected function _addMusicBrowsePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'music_index_browse')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'music_index_browse',
        'displayname' => 'Music Browse Page',
        'title' => 'Music Browse',
        'description' => 'This page lists music.',
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

      // Insert banner
     $db->insert('engine4_core_banners', array(
        'name' => 'music',
        'module' => 'music',
        'title' => 'Create Your Own Playlists',
        'body' => 'Dance; relax; workout with Music!',
        'photo_id' => 0,
        'params' => '{"label":"Upload Music","route":"music_general","routeParams":{"action":"create"}}',
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
        'name' => 'music.browse-menu',
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
        'name' => 'music.browse-search',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'order' => 1,
      ));

      // Insert gutter menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'music.browse-menu-quick',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'order' => 2,
      ));
    }
  }

  protected function _addMusicViewPage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'music_playlist_view')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'music_playlist_view',
        'displayname' => 'Music Playlist View Page',
        'title' => 'Music View',
        'description' => 'This page displays a playlist.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
      ));
      $mainId = $db->lastInsertId();

      // Insert middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
      ));
      $middleId = $db->lastInsertId();
      
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'music.breadcrumb',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'order' => 2,
      ));

      // Insert comments
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.comments',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'order' => 3,
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
          ->where('name = ?', 'music.breadcrumb')
          ->limit(1);
      $content_id = $select_content->query()->fetchObject()->content_id;
      
      if(empty($content_id)) {
        $db->query('UPDATE `engine4_core_content` SET `order` = `order`+1 WHERE `engine4_core_content`.`page_id` = "'.$pageId.'" AND `engine4_core_content`.`type` = "widget";');
        // Insert content
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'music.breadcrumb',
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

    //INSERT INTO `engine4_core_content` (`content_id`, `page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $isPageExist = $select->query()->fetchObject();
    if(empty($isPageExist)){
      return false;
    }
    $pageId = $isPageExist->page_id;
    // music.profile-musics

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $pageId)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'music.profile-music')
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



      // music tab on profile
      $db->insert('engine4_core_content', array(
        'page_id' => $pageId,
        'type'    => 'widget',
        'name'    => 'music.profile-music',
        'parent_content_id' => ($tabId ? $tabId : $middleId),
        'order'   => 10,
        'params'  => '{"title":"Music","titleCount":true}',
      ));
    }

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $pageId)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'music.profile-player')
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $pageId)
        ->where('type = ?', 'container')
        ->where('name = ?', 'main')
        ->limit(1);
      $containerId = $select->query()->fetchObject()->content_id;

//       // middle_id (will always be there)
//       $select = new Zend_Db_Select($db);
//       $select
//         ->from('engine4_core_content')
//         ->where('parent_content_id = ?', $containerId)
//         ->where('type = ?', 'container')
//         ->where('name = ?', 'middle')
//         ->limit(1);
//       $middleId = $select->query()->fetchObject()->content_id;

      // left_id (may not always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $containerId)
        ->where('type = ?', 'container')
        ->where('name = ?', 'left')
        ->limit(1);
      $leftId = $select->query()->fetchObject();
      if( $leftId && $leftId->content_id ) {
        $leftId = $leftId->content_id;
      } else {
        $leftId = null;
      }

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

      // music player for profile page
      $db->insert('engine4_core_content', array(
        'page_id' => $pageId,
        'type'    => 'widget',
        'name'    => 'music.profile-player',
        'parent_content_id' => $leftId,
        'order'   => 10,
        'params'  => '',
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
    $sql = "ALTER TABLE `engine4_music_playlists` ADD `view_privacy` VARCHAR(24) NOT NULL DEFAULT 'owner' AFTER `play_count`";
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
      ->where('resource_type = ?', 'music_playlist')
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

      $db->update('engine4_music_playlists',array(
            'view_privacy' => $viewPrivacy,
            ), array(
            'playlist_id = ?' => $privacy['resource_id'],
          ));
    }

    return $this;
  }
}
?>
