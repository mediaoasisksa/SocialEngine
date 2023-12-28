<?php

class Sescompany_Widget_LandingPageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    $settings = Engine_Api::_()->getApi('settings', 'core');
        
    $this->view->chooselandingdesign = $chooselandingdesign = $settings->getSetting('sescompany.chooselandingdesign', 1);
    
    $this->view->slides = Engine_Api::_()->getDbtable('slides', 'sescompany')->getSlides(1);
    $this->view->testimonials = Engine_Api::_()->getDbtable('testimonials', 'sescompany')->getTestimonials(1);
    
        // Should we consider views or comments popular?
    $popularType = $this->_getParam('popularType', 'member');
    if( !in_array($popularType, array('view', 'member')) ) {
      $popularType = 'view';
    }
    $this->view->popularType = $popularType;
    $this->view->popularCol = $popularCol = $popularType . '_count';
    
    // Get paginator
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()
      ->where('search = ?', 1)
      ->where('enabled = ?', 1)
    //  ->where('photo_id <>?', 0)
      ->where($popularCol . ' >= ?', 0)
      ->order($popularCol . ' DESC')
      ;
    $this->view->popularMembers = Zend_Paginator::factory($select);
     $this->view->popularMembers->setItemCountPerPage(4);

    if($chooselandingdesign == 1) {
      $this->view->teams = Engine_Api::_()->getDbtable('teams', 'sescompany')->getTeams(1);
      $this->view->clients = Engine_Api::_()->getDbtable('clients', 'sescompany')->getClients(1);
      $this->view->features = Engine_Api::_()->getDbtable('features', 'sescompany')->getFeatures(1);
      $this->view->counters = Engine_Api::_()->getDbtable('counters', 'sescompany')->getCounters(1);
      $this->view->abouts = Engine_Api::_()->getDbtable('abouts', 'sescompany')->getAbouts(1);
    } else if($chooselandingdesign == 2) {
      $this->view->teams = Engine_Api::_()->getDbtable('teams', 'sescompany')->getTeams(1);
      $this->view->features = Engine_Api::_()->getDbtable('features', 'sescompany')->getFeatures(1);
      // Photos Work
      switch($this->_getParam('sort', 'recent')) {
        case 'popular':
          $order = 'view_count';
          break;
        case 'recent':
        default:
          $order = 'modified_date';
          break;
      }
      $excludedLevels = array(1, 2, 3);   // level_id of Superadmin,Admin & Moderator
      $registeredPrivacy = array('everyone', 'registered');
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
      if( $viewer->getIdentity() && !in_array($viewer->level_id, $excludedLevels) ) {
        $viewerId = $viewer->getIdentity();
        $netMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $this->view->viewerNetwork = $viewerNetwork = $netMembershipTable->getMembershipsOfIds($viewer);
        if( !empty($viewerNetwork) ) {
          array_push($registeredPrivacy,'owner_network');
        }

        $friendsIds = $viewer->membership()->getMembersIds();
        $friendsOfFriendsIds = $friendsIds;
        foreach( $friendsIds as $friendId ) {
          $friend = Engine_Api::_()->getItem('user', $friendId);
          $friendMembersIds = $friend->membership()->getMembersIds();
          $friendsOfFriendsIds = array_merge($friendsOfFriendsIds, $friendMembersIds);
        }
      }

      // Prepare data
      $albumTable = Engine_Api::_()->getItemTable('album');
      $select = $albumTable->select()->from($albumTable->info('name'), 'album_id');

      if( !$viewer->getIdentity() ) {
        $select->where("view_privacy = ?", 'everyone');
      } elseif( !in_array($viewer->level_id, $excludedLevels) ) {
        $select->Where("owner_id = ?", $viewerId)
          ->orwhere("view_privacy IN (?)", $registeredPrivacy);
        if( !empty($friendsIds) ) {
          $select->orWhere("view_privacy = 'owner_member' AND owner_id IN (?)", $friendsIds);
        }
        if( !empty($friendsOfFriendsIds) ) {
          $select->orWhere("view_privacy = 'owner_member_member' AND owner_id IN (?)", $friendsOfFriendsIds);
        }
        if( empty($viewerNetwork) && !empty($friendsOfFriendsIds) ) {
          $select->orWhere("view_privacy = 'owner_network' AND owner_id IN (?)", $friendsOfFriendsIds);
        }

        $subquery = $select->getPart(Zend_Db_Select::WHERE);
        $select ->reset(Zend_Db_Select::WHERE);
        $select ->where(implode(' ',$subquery));
      }

      $select->where("search = 1");
      $albums = $albumTable->fetchAll($select);
      $albumIds = array();
      foreach ($albums as $album) {
        $albumIds[] = $album->album_id;
      }
      $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photoslimit', 8);
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
      $select = $photoTable->getPhotoSelect(array('album_ids' => $albumIds, 'order' => $order));
      $select = $select->limit($limit);
      
      
      $this->view->photos = $photos = $photoTable->fetchAll($select);
    }
    
    //Manage Contents
    $this->view->contmodule = $module = $settings->getSetting('sescompany.contmodule', '');
    $this->view->contheading = $settings->getSetting('sescompany.contheading', 'Blog');
    $this->view->conbgimage = $settings->getSetting('sescompany.conbgimage', '');
    $popularitycriteria = $settings->getSetting('sescompany.contpopularitycriteria', 'creation_date');
    $limit = $settings->getSetting('sescompany.contlimit', 4);
    if($module) {
      $table = Engine_Api::_()->getItemTable($module);
      $tableName = $table->info('name');
      $select = $table->select()->from($tableName)->limit($limit);
      
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $popularitycriteria_exist = $db->query("SHOW COLUMNS FROM ".$tableName." LIKE '".$popularitycriteria."'")->fetch();
      if (!empty($popularitycriteria_exist)) {
        $select->order("$popularitycriteria DESC");
      } else {
        $select->order('creation_date DESC');
      }			
      $column_exist = $db->query("SHOW COLUMNS FROM ".$tableName." LIKE 'is_delete'")->fetch();
      if (!empty($column_exist)) {
        $select->where('is_delete =?',0);
      }
      $this->view->contents = $result = $table->fetchAll($select);
    }
	}
}