<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FeedController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_FeedController extends Core_Controller_Action_Standard
{

  //TO SHOW INFORMATION OF CONTENT.
  public function showTooltipInfoAction()
  {
    $this->_helper->layout->disableLayout();
    //$this->_helper->layout->setLayout('default-simple');
    //CHECK FOR USER IS VALID OR NOT.
//     if (!$this->_helper->requireUser()->isValid())
//       return;
    //GET THE RESOURCE.
    $resource = $this->_getParam('resource', null);
    if( empty($resource) ) {
      exit(0);
    }
    //SPLIT A SPACE BY SPACE.
    $resourceArray = explode(" ", $resource);

    //FOR CONTENT OBJECT ACCRODING TO RESOURCE TYPE AND RESOURCE ID .
    if( $resourceArray[0] == 'siteevent_event' ) {

      if( !isset($resourceArray[2]) ) {
        //GET THE NEXT UPCOMING OCCURRENCE ID
        $resourceArray[2] = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($resourceArray[1]);
      }
      $resource = Engine_Api::_()->getDbTable('events', 'siteevent')->getItem($resourceArray[1], 'feedtooltip', $resourceArray[2]);
      $this->view->occurrence_id = $resourceArray[2];
    } elseif( $resourceArray[0] == 'siteevent_occurrence' ) {
      $this->view->occurrence_id = $resourceArray[1];
      $resource = Engine_Api::_()->getItem('siteevent_occurrence', $resourceArray[1])->getParent();
      $resourceArray[2] = $resourceArray[1];
      $resource = Engine_Api::_()->getDbTable('events', 'siteevent')->getItem($resource->getIdentity(), 'feedtooltip', $resourceArray[2]);
    } else
      $resource = Engine_Api::_()->getItem($resourceArray[0], $resourceArray[1]);
    if( !empty($resource) && (!empty($resourceArray[1])) ) {
      $this->view->result = $resource;
      $this->view->resource_type = $resource_type = $resourceArray[0]; // $this->_getParam('resource_type', null);
      $this->view->resource_id = $resource_id = $resourceArray[1];
      $this->setResourceInfo();
      if( $resource_type == 'siteevent_occurrence' ) {
        $this->view->resource_type = $resource_type = 'siteevent_event';

        $this->view->resource_id = $resource->event_id;
      }// $this->_getParam('resource_id', null);

      if( $resource_type == 'recipe' || $resource_type == 'list_listing' ) {
        switch( $resource_type ) {
          case 'list_listing':
            $dbtable = Engine_Api::_()->getDbtable('locations', 'list');
            $id = 'listing_id';
            break;
          case 'recipe':
            $dbtable = Engine_Api::_()->getDbtable('locations', 'recipe');
            $id = 'recipe_id';
            break;
        }
        $select = $dbtable->select()->from($dbtable->info('name'))
          ->where($dbtable->info('name') . ".$id = ?", $resource_id);
        $item = $dbtable->fetchRow($select);
        if( !empty($item) ) {
          $this->view->locationItem = $item;
        }
      }

      if( $resource_type == 'sitereview_listing' ) {
        Engine_Api::_()->sitereview()->setListingTypeInRegistry($resource->listingtype_id);
        $this->view->listingtypeArray = $listingtypeArray = Zend_Registry::get('listingtypeArray' . $resource->listingtype_id);
      }
// 			    //GET REVIEW TABLE
//          $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitereview');
//
//
// 			      //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
//       $noReviewCheck = $reviewTable->getAvgRecommendation($resource_id);
//       if (!empty($noReviewCheck)) {
//
//         //$this->view->noReviewCheck = $noReviewCheck->toArray();
//         $this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
//       }
//
      if( $resource_type == 'classified' ) {

        $this->view->locationItem = Engine_Api::_()->seaocore()->getCustomFieldLocation($resource);
// 				$table_option =  Engine_Api::_()->fields()->getTable('classified', 'search');
// 				$table_option_name = $table_option->info('name');
// 				$select_options = $table_option->select()
// 				->from($table_option_name)
// 				->where($table_option_name. '.item_id =?', $resource_id);
// 				$item = $table_option->fetchRow($select_options);
// 				if (!empty($item)) {
// 				  $this->view->locationItem = $item;
// 				}
      }

      if( $resourceArray[0] == 'user' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember') ) {
        $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        $this->view->featured = $tableUserInfo->getColumnValue($resource_id, 'featured');
        $this->view->sponsored = $tableUserInfo->getColumnValue($resource_id, 'sponsored');
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
      }

      //GET THE BASE URL.
      $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

      //GET THE CURRENT VIEWER.
      $this->view->viewer = $user = Engine_Api::_()->user()->getViewer();

      //GET THE FRIEND OF LOGIN USER.
      $user_id = $user->membership()->getMembershipsOfIds();
      $this->view->viewer_id = $viewerId = $user->getIdentity();

      //start work of join and request page member.
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
        if( $resource_type == 'sitepage_page' ) {
          $flag = true;
          $requestFlag = true;
          $sitepage = $resource;
          $this->view->member_approval = $sitepage->member_approval;
          if( !empty($sitepage->member_approval) ) {

            // PACKAGE BASE PRIYACY START
            if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
              if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember") ) {
                return !$flag;
              }
            } else {
              $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smecreate');
              if( empty($isPageOwnerAllow) ) {
                return !$flag;
              }
            }
            // PACKAGE BASE PRIYACY END

            if( $viewerId == $sitepage->owner_id ) {
              return !$flag;
            }
            $select = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewerId, $sitepage->page_id);
            if( !empty($select) ) {
              return !$flag;
            }
            $this->view->joinFlag = $flag;
          } else {
            // PACKAGE BASE PRIYACY START
            if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
              if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember") ) {
                return !$requestFlag;
              }
            } else {
              $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smecreate');
              if( empty($isPageOwnerAllow) ) {
                return !$requestFlag;
              }
            }
            // PACKAGE BASE PRIYACY END
            if( $viewerId == $sitepage->owner_id ) {
              return !$requestFlag;
            }
            $select = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewerId, $sitepage->page_id);
            if( !empty($select) ) {
              return !$requestFlag;
            }
          }
          $this->view->requestFlag = $requestFlag;
        }
      }
      //end join and request page link.
      //start work of join and request business member.
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember') ) {
        if( $resource_type == 'sitebusiness_business' ) {
          $flag = true;
          $requestFlag = true;
          $sitebusiness = $resource;
          $this->view->member_approval = $sitebusiness->member_approval;
          if( !empty($sitebusiness->member_approval) ) {

            // PACKAGE BASE PRIYACY START
            if( Engine_Api::_()->sitebusiness()->hasPackageEnable() ) {
              if( !Engine_Api::_()->sitebusiness()->allowPackageContent($sitebusiness->package_id, "modules", "sitebusinessmember") ) {
                return !$flag;
              }
            } else {
              $isBusinessOwnerAllow = Engine_Api::_()->sitebusiness()->isBusinessOwnerAllow($sitebusiness, 'smecreate');
              if( empty($isBusinessOwnerAllow) ) {
                return !$flag;
              }
            }
            // PACKAGE BASE PRIYACY END

            if( $viewerId == $sitebusiness->owner_id ) {
              return !$flag;
            }
            $select = Engine_Api::_()->getDbTable('membership', 'sitebusiness')->hasMembers($viewerId, $sitebusiness->business_id);
            if( !empty($select) ) {
              return !$flag;
            }
            $this->view->joinFlag = $flag;
          } else {
            // PACKAGE BASE PRIYACY START
            if( Engine_Api::_()->sitebusiness()->hasPackageEnable() ) {
              if( !Engine_Api::_()->sitebusiness()->allowPackageContent($sitebusiness->package_id, "modules", "sitebusinessmember") ) {
                return !$requestFlag;
              }
            } else {
              $isBusinessOwnerAllow = Engine_Api::_()->sitebusiness()->isBusinessOwnerAllow($sitebusiness, 'smecreate');
              if( empty($isBusinessOwnerAllow) ) {
                return !$requestFlag;
              }
            }
            // PACKAGE BASE PRIYACY END
            if( $viewerId == $sitebusiness->owner_id ) {
              return !$requestFlag;
            }
            $select = Engine_Api::_()->getDbTable('membership', 'sitebusiness')->hasMembers($viewerId, $sitebusiness->business_id);
            if( !empty($select) ) {
              return !$requestFlag;
            }
          }
          $this->view->requestFlag = $requestFlag;
        }
      }
      //end join and request business link.
      //start work of join and request group member.
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember') ) {
        if( $resource_type == 'sitegroup_group' ) {
          $flag = true;
          $requestFlag = true;
          $sitegroup = $resource;
          $this->view->member_approval = $sitegroup->member_approval;
          if( !empty($sitegroup->member_approval) ) {

            // PACKAGE BASE PRIYACY START
            if( Engine_Api::_()->sitegroup()->hasPackageEnable() ) {
              if( !Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember") ) {
                return !$flag;
              }
            } else {
              $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
              if( empty($isGroupOwnerAllow) ) {
                return !$flag;
              }
            }
            // PACKAGE BASE PRIYACY END

            if( $viewerId == $sitegroup->owner_id ) {
              return !$flag;
            }
            $select = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewerId, $sitegroup->group_id);
            if( !empty($select) ) {
              return !$flag;
            }
            $this->view->joinFlag = $flag;
          } else {
            // PACKAGE BASE PRIYACY START
            if( Engine_Api::_()->sitegroup()->hasPackageEnable() ) {
              if( !Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember") ) {
                return !$requestFlag;
              }
            } else {
              $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
              if( empty($isGroupOwnerAllow) ) {
                return !$requestFlag;
              }
            }
            // PACKAGE BASE PRIYACY END
            if( $viewerId == $sitegroup->owner_id ) {
              return !$requestFlag;
            }
            $select = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewerId, $sitegroup->group_id);
            if( !empty($select) ) {
              return !$requestFlag;
            }
          }
          $this->view->requestFlag = $requestFlag;
        }
      }
      //end join and request group link.
      //CHECK LOGIN USER FRIEND OR NOT.
      if( $resource_type == 'user' )
        $this->view->getMemberFriend = Engine_Api::_()->seaocore()->isMember($resource_id);

      //FOR MUTUAL FRIEND.
      $this->view->muctualFriend = $paginator = Engine_Api::_()->seaocore()->getMutualFriend($resource_id, $limit = 5);
      $paginator->setItemCountPerPage(5);
      // $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      $this->view->muctualfriendLikeCount = $paginator->getTotalItemCount();

      //THIS IS FOR GET THE MODULE NAME ACCRODING TO OBJECT.
      $this->view->moduleNmae = $moduleNmae = strtolower($resource->getModuleName());

      //CHECK "SUGGESTION" AND "POKE" PLUGIN IS ENABLED.
      $this->view->suggestionEnabled = $suggestionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
      $this->view->pokeEnabled = $pokeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('poke');

      //START POKE LEVEL SETTINGS WORK.
      if( $resource_type == 'user' ) {
        if( !empty($pokeEnabled) && (!empty($viewerId)) ) {
          $subject = Engine_Api::_()->getItem('user', $resource_id);
          $this->view->getpokeFriend = Engine_Api::_()->poke()->levelSettings($subject);
        }
      }
      //END POKE LEVEL SETTINGS WORK.
      //START SUGGESTION SETTINGS WORK.
      if( !empty($suggestionEnabled) ) {
        if( $moduleNmae == 'user' ) {
          $moduleNmae = 'friend';
        }
        $this->view->suggestion_frienf_link_show = Engine_Api::_()->suggestion()->getModSettings("$moduleNmae", "link");
        //Engine_Api::_()->getApi('settings', 'core')->getSetting("$moduleNmae.sugg.link");
      }
      //END SUGGESTION SETTINGS WORK.
      //FOR CATEGORY SHOW ACCRODING TO RESOURCE TYPE.
      $this->view->getCategoryText = Engine_Api::_()->seaocore()->getCategory($resource_type, $resource);

      //FOR LIKE COUNT ACCRODING TO THE RESOURCE TYPE AND RESOURCE ID FORM CORE LIKE TABLE.
      $this->view->likeCount = Engine_Api::_()->getDbtable('likes', 'core')->getLikeCount($resource, $user);

      //CONDITION OF EVENT, GROUP AND ALL RESOURCE TYPE.(FOR FRIEND)

      if( $resource_type == 'group' || $resource_type == 'event' || $resource_type == 'siteevent_event' ) {
        if( $resource_type == 'group' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup') ) {
          $Table = Engine_Api::_()->getDbtable('membership', 'advgroup'); //new Advgroup_Model_DbTable_Membership();
          $getContentElement = array('user_id');
        } else if( $resource_type == 'event' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ynevent') ) {
          $Table = Engine_Api::_()->getDbtable('membership', 'ynevent'); //new Advgroup_Model_DbTable_Membership();
          $getContentElement = array('user_id');
        } else if( $resource_type == 'siteevent_event' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent') ) {
          $Table = Engine_Api::_()->getDbtable('membership', 'siteevent'); //new Advgroup_Model_DbTable_Membership();
          $getContentElement = array('user_id', 'rsvp');
        } else {
          $Table = Engine_Api::_()->getDbtable('membership', $resource_type);
          $getContentElement = array('user_id');
        }
        $TableName = $Table->info('name');
      } else {
        $Table = Engine_Api::_()->getItemTable('core_like');
        $TableName = $Table->info('name');
        $getContentElement = array('poster_id', 'poster_type');
      }

      if( !empty($user_id) ) {
        $count_select = $Table->select()
          ->from($TableName, $getContentElement)
          ->where($TableName . '.resource_id = ?', $resource_id);

        if( $resource_type == 'group' || $resource_type == 'event' || $resource_type == 'siteevent_event' ) {
          $count_select->where($TableName . '.user_id IN (?)', (array) $user_id);
          if( $resource_type == 'event' ) {
            $count_select->where($TableName . '.rsvp = ?', '2');
          } elseif( $resource_type == 'siteevent_event' ) {
            $count_select->where($TableName . '.occurrence_id = ?', $resourceArray[2])
              ->where($TableName . '.rsvp = ?', '2');
          } elseif( $resource_type == 'group' ) {
            $count_select->where($TableName . '.user_approved = ?', '1')
              ->where($TableName . '.active = ?', '1');
          }
        } else {
          $count_select->where($TableName . '.resource_type = ?', $resource_type);
          //if (!empty($user_id)) {
          $count_select->where($TableName . '.poster_id IN (?)', (array) $user_id)
            ->where($TableName . '.poster_id != ?', $viewerId);
          //}
        }
        $count_select->order('RAND()');
        $this->view->activity_result = $result = $Table->fetchAll($count_select);
        $friendLikeCount = count($result);
        if( !empty($friendLikeCount) ) {
          $this->view->friendLikeCount = $friendLikeCount;
        }
      }

      $this->view->isHidden = $isHidden = false;
      if( $resource->getIdentity() && $resource->getType() == 'user' ) {
        $fields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($resource);
        $field_id = '';
        foreach( $fields as $value ) {
          if( isset($value['type']) && $value['type'] == 'location' ) {
            $field_id = $value['field_id'];
          }
        }
        if( $field_id ) {
          $values = Engine_Api::_()->fields()->getFieldsValues($resource);
          $valueRows = $values->getRowsMatching(array(
            'field_id' => $field_id,
            'item_id' => $resource->getIdentity()
          ));
          foreach( $valueRows as $valueRow ) {
            $prevPrivacy = $valueRow->privacy;
          }
        }
        $usePrivacy = ($resource instanceof User_Model_User);
        if( $usePrivacy ) {
          $relationship = 'everyone';
          if( $user && $user->getIdentity() ) {
            if( $user->getIdentity() == $resource->getIdentity() ) {
              $relationship = 'self';
            } else if( $user->membership()->isMember($resource, true) ) {
              $relationship = 'friends';
            } else {
              $relationship = 'registered';
            }
          }
        }
        if( $usePrivacy && !empty($prevPrivacy) && $relationship != 'self' ) {
          if( $prevPrivacy == 'self' && $relationship != 'self' ) {
            $this->view->isHidden = $isHidden = true; //continue;
          } else if( $prevPrivacy == 'friends' && ($relationship != 'friends' && $relationship != 'self') ) {
            $this->view->isHidden = $isHidden = true; //continue;
          } else if( $prevPrivacy == 'registered' && $relationship == 'everyone' ) {
            $this->view->isHidden = $isHidden = true; //continue;
          }
        }

        if( $user && $user->getIdentity() && !$resource->authorization()->isAllowed($user, 'view') ) {
          $this->view->isHidden = $isHidden = true; //continue;
        }
      }
      $this->view->level_id = 0;
      if( $user->getIdentity() ) {
        $this->view->level_id = $level_id = $user->level_id;
      } else {
        $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
      }
    } else {
      echo '<div class="uiOverlay info_tip" style="width: 300px; top: 0px; "><div class="info_tip_content_wrapper" ><div class="info_tip_content">' . $this->view->translate("This item / member has been deleted.") . '</div></div></div>';
      exit();
    }
  }

  //THIS FUNCTION CALLING WHEN CLICK ON THE VIEW MORE LINK.
  public function moreMutualFriendAction()
  {

    //GET THE BASE URL.
    $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    //GET THE FRIEND ID AND OBJECT OF USER.
    $this->view->showViewMore = $this->_getParam('showViewMore', 0);
    $this->view->friend_id = $friend_id = $this->_getParam('id');
    $this->view->result = Engine_Api::_()->getItem('user', $friend_id);

    //FOR MUTUAL FRIEND.
    $this->view->paginator = $paginator = Engine_Api::_()->seaocore()->getMutualFriend($friend_id);

    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->muctualfriendLikeCount = $paginator->getTotalItemCount();
    $this->view->count = $paginator->getTotalItemCount();
  }

  public function commonMemberListAction()
  {

    //GET THE BASE URL.
    $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $this->view->showViewMore = $this->_getParam('showViewMore', 0);

    $user = Engine_Api::_()->user()->getViewer();
    $viewerId = $user->getIdentity();

    //GET THE FRIEND OF LOGIN USER.
    $user_id = $user->membership()->getMembershipsOfIds();

    //GET THE RESOURCE TYPE AND RESOURCE ID AND VIEWER.
    $this->view->resouce_type = $resource_type = $this->_getParam('resouce_type');
    $this->view->resouce_id = $resource_id = $this->_getParam('resource_id');

    //CONDITION OF EVENT, GROUP AND ALL RESOURCE TYPE.(FOR FRIEND)
    if( $resource_type == 'group' || $resource_type == 'event' || $resource_type == 'siteevent_event' ) {
      if( $resource_type == 'group' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup') ) {
        $Table = Engine_Api::_()->getDbtable('membership', 'advgroup'); //new Advgroup_Model_DbTable_Membership();
      } else if( $resource_type == 'event' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ynevent') ) {
        $Table = Engine_Api::_()->getDbtable('membership', 'ynevent'); //new Advgroup_Model_DbTable_Membership();
      } else if( $resource_type == 'siteevent_event' ) {
        $Table = Engine_Api::_()->getDbtable('membership', 'siteevent');
      } else
        $Table = Engine_Api::_()->getDbtable('membership', $resource_type);
      $TableName = $Table->info('name');
      $getContentElement = array('user_id');
    } else {
      $Table = Engine_Api::_()->getItemTable('core_like');
      $TableName = $Table->info('name');
      $getContentElement = array('poster_id', 'poster_type');
    }

    if( !empty($user_id) ) {
      $count_select = $Table->select()
        ->from($TableName, $getContentElement)
        ->where($TableName . '.resource_id = ?', $resource_id);

      if( $resource_type == 'group' || $resource_type == 'event' || $resource_type == 'siteevent_event' ) {
        $count_select->where($TableName . '.user_id IN (?)', (array) $user_id);
        if( $resource_type == 'event' || $resource_type == 'siteevent_event' ) {
          $count_select->where($TableName . '.rsvp = ?', '2');
          $count_select->group("user_id");
        } elseif( $resource_type == 'group' ) {
          $count_select->where($TableName . '.user_approved = ?', '1')
            ->where($TableName . '.active = ?', '1');
        }
      } else {
        $count_select->where($TableName . '.resource_type = ?', $resource_type);
        $count_select->where($TableName . '.poster_id IN (?)', (array) $user_id)
          ->where($TableName . '.poster_id != ?', $viewerId);
      }

      $this->view->paginator = $paginator = Zend_Paginator::factory($count_select);
      $this->view->page = $this->_getParam('page', 1);
      $paginator->setItemCountPerPage(15);
      $paginator->setCurrentPageNumber($this->view->page);
      $this->view->count = $paginator->getTotalItemCount();
    }
  }

  public function addfriendrequestAction()
  {

    //GET THE FRIEND ID.
    $this->view->friend_id = $friend_id = (int) $this->_getParam('resource_id');
    if( !empty($friend_id) ) {
      $this->addAction($friend_id);
    }
  }

  //THIS FUNCTION IS USED TO SAVE THE FRIEND REQUEST, AND PERFORM ALLIED ACTIONS FOR NOTIFICATION UPDATES, ETC.
  public function addAction($id)
  {

    if( !$this->_helper->requireUser()->isValid() )
      return;

    // Disable Layout.
    //$this->_helper->layout->disableLayout(true);
    // Get id of friend to add
    $user_id = $id;
    if( null == $user_id ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified.');
      return;
    }

    //GET THE VIEWER.
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = Engine_Api::_()->user()->getUser($user_id);

    // CHECK THEAT USER IS NOT TRYING TO BE FRIEND 'SELF'.
    if( $viewer->isSelf($user) ) {
      return;
    }

    // CHECK THAT USER IS ALREADY FRIEND WITH THE MEMBER.
    if( $user->membership()->isMember($viewer) ) {
      return;
    }

    // CHECK THAT USER HAS NOT BLOCKED THE MEMBER.
    if( $viewer->isBlocked($user) ) {
      return;
    }

    // PROCESS
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      // check friendship verification settings
      // add membership if allowed to have unverified friendships
      //$user->membership()->setUserApproved($viewer);
      // else send request
      $user->membership()->addMember($viewer)->setUserApproved($viewer);


      // send out different notification depending on what kind of friendship setting admin has set
      /* ('friend_accepted', 'user', 'You and {item:$subject} are now friends.', 0, ''),
        ('friend_request', 'user', '{item:$subject} has requested to be your friend.', 1, 'user.friends.request-friend'),
        ('friend_follow_request', 'user', '{item:$subject} has requested to add you as a friend.', 1, 'user.friends.request-friend'),
        ('friend_follow', 'user', '{item:$subject} has added you as a friend.', 1, 'user.friends.request-friend'),
       */


      // if one way friendship and verification not required
      if( !$user->membership()->isUserApprovalRequired() && !$user->membership()->isReciprocal() ) {
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends_follow', '{item:$subject} is now following {item:$object}.');

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow');

        $message = "You are now following this member.";
      }

      // if two way friendship and verification not required
      else if( !$user->membership()->isUserApprovalRequired() && $user->membership()->isReciprocal() ) {
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
      }

      // if one way friendship and verification required
      else if( !$user->membership()->isReciprocal() ) {
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');
      }

      // if two way friendship and verification required
      else if( $user->membership()->isReciprocal() ) {
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
      }
      $this->view->status = true;
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->exception = $e->__toString();
    }
  }

  //TO SHOW INFORMATION OF CONTENT.
  public function showTooltipLocationInfoAction()
  {
    //GET USER SUBJECT
    $this->view->subject = $this->_getParam('subject');
  }

  //TO GET PHOTO URL OF SUBJECT
  public function setResourceCoverPhoto($param = array())
  {
    $setCoverPhoto = false;
    $this->view->coreSettings = $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->coreModules = $coreModules = Engine_Api::_()->getDbtable('modules', 'core');
    $this->view->informationArray = $informationArray = $coreSettings->getSetting('seaocore.information.link', array("category" => "category", "like" => "like", "eventmember" => "eventmember", "groupmember" => "groupmember", "mutualfriend" => "mutualfriend", "friendcommon" => "friendcommon", "joingroupfriend" => "joingroupfriend", "attendingeventfriend" => "attendingeventfriend", "price" => "price", "review_count" => "review_count", "rating_count" => "rating_count", "recommend" => "recommend", "review_helpful" => "review_helpful", "rwcreated_by" => "rwcreated_by", "rewishlist_item" => "rewishlist_item", "location" => "location", "sitecontentcoverphoto_cover" => "Content Cover Photo (For Content Items)", "siteusercoverphoto_cover" => "User Cover Photo", "rating_star" => "Rating Star"));
    $this->view->sitemusicItemsArray = $sitemusicItemsArray = array('sitemusic_pl', 'sitemusic_playlist_song', 'sitemusic_artist', 'sitemusic_userpl');
    if( in_array($this->view->resource_type, $sitemusicItemsArray) ) {
      $item = Engine_Api::_()->getItem($this->view->resource_type, $this->view->resource_id);
      $cover = Engine_Api::_()->storage()->get($item->cover_photo_id, '');
      if( $cover ) {
        $this->view->sitemusicCoverPhoto = $cover->getPhotoUrl();
      }
      $setting = Engine_Api::_()->getApi('settings', 'core');
      if( empty($item->cover_photo_id) ) {
        if( $this->view->resource_type == 'sitemuisc_playlist' ) {
          $defaultBackground = $setting->getSetting('sitemusic.albumcoverPhoto');
        } elseif( $this->view->resource_type == 'sitemusic_userpl' ) {
          $defaultBackground = $setting->getSetting('sitemusic.playlistcoverPhoto');
        } elseif( $this->view->resource_type == 'sitemuisc_artist' ) {
          $defaultBackground = $setting->getSetting('sitemusic.artistcoverPhoto');
        } elseif( $this->view->resource_type == 'sitemuisc_playlist_song' ) {
          $defaultBackground = $setting->getSetting('sitemusic.songcoverPhoto');
        }

        $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $defaultBackground = $baseUrl . 'application/modules/Sitemusic/externals/images/cover-photo.jpeg';
        $this->view->sitemusicCoverPhoto = $defaultBackground = ($backgroupImage) ? $backgroupImage : $defaultBackground;
      }
    }

    if( $this->view->resource_type != 'user' && !empty($informationArray) && in_array("sitecontentcoverphoto_cover", $informationArray) ) {
      if( $coreModules->isModuleEnabled('sitecontentcoverphoto') ) {
        if( isset($this->view->result->listingtype_id) ) {
          $setCoverPhoto = (bool) Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->checkEnableModule(array('resource_type' => $this->view->result->getType() . '_' . $this->view->result->listingtype_id));
        } else {
          $setCoverPhoto = (bool) Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->checkEnableModule(array('resource_type' => $this->view->result->getType()));
        }
      }
    } elseif( $this->view->resource_type == 'user' && !empty($informationArray) && in_array("siteusercoverphoto_cover", $informationArray) ) {
      if( $coreModules->isModuleEnabled('siteusercoverphoto') ) {
        $setCoverPhoto = 1;
      }
    }
    if( $this->view->resource_type == 'user' && $setCoverPhoto ) {
      $user = Engine_Api::_()->getItem('user', $this->view->result->getIdentity());
      $has_advalbum = Engine_Api::_()->hasModuleBootstrap('advalbum');
      if( isset($user->user_cover) && $user->user_cover ) {
        if( $has_advalbum ) {
          $this->view->coverPhoto = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
        } else {
          $this->view->coverPhoto = Engine_Api::_()->getItem('album_photo', $user->user_cover);
        }
      } elseif( !empty($coverId = Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user->level_id.id")) ) {
        $this->view->coverPhoto = Engine_Api::_()->storage()->get($coverId, 'thumb.cover');
      }

      if( $coreModules->isModuleEnabled('siteverify') ) {
        $this->view->verify_count = $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($this->view->result->user_id);
        $this->view->verify_limit = $verify_limit = Engine_Api::_()->authorization()->getPermission($this->view->result->level_id, 'siteverify', 'verify_limit');
        if( !empty($informationArray) && in_array("verify", $informationArray) && ($verify_count >= $verify_limit) ) {
          $this->view->showVerified = 1;
        }
      }
    } elseif( $setCoverPhoto ) {

      $fieldName = strtolower($this->view->result->getShortType()) . '_cover';
      if( strtolower($this->view->result->getModuleName()) == 'album' || strtolower($this->view->result->getModuleName()) == 'sitealbum' ) {
        if( $coreModules->isModuleEnabled('album') && isset($this->view->result->$fieldName) && !empty($this->view->result->$fieldName) ) {
          $this->view->coverPhoto = Engine_Api::_()->getItem("album_photo", $this->view->result->$fieldName);
        }
      } else {
        if( $this->view->resource_type != 'sitereview_listing' ) {
          if( isset($this->view->result->$fieldName) && !empty($this->view->result->$fieldName) ) {
            $this->view->coverPhoto = Engine_Api::_()->getItem(strtolower($this->view->result->getModuleName()) . "_photo", $this->view->result->$fieldName);
          }
        } else {

          $sitereviewOtherinfoTable = Engine_Api::_()->getDbTable('otherinfo', 'sitereview');
          $sitereviewOtherinfoQuery = $sitereviewOtherinfoTable->select()
                                              ->from($sitereviewOtherinfoTable->info('name'), array("$fieldName"))
                                              ->where('listing_id = ?', $this->view->result->listing_id);
          $fieldNameValue = $sitereviewOtherinfoQuery->limit(1)->query()->fetchColumn();
          $fieldNameValue = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')
                                           ->getColumnValue( $this->view->result->listing_id, $fieldName);
          if( $fieldNameValue ) {
            $this->view->coverPhoto = $cover_photo = Engine_Api::_()->getItem( "sitereview_photo", $fieldNameValue);
          }
        }
      }
    }
  }

  public function setResourceMainPhotoUrl()
  {
    $sitemusicItemsArray = array('sitemusic_pl', 'sitemusic_playlist_song', 'sitemusic_artist', 'sitemusic_userpl');
    if( in_array($this->view->resource_type, $sitemusicItemsArray) ) {
      $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
      if( $this->view->resource_type == 'sitemusic_pl' ) {
        $this->view->mainPhotoUrl = $baseurl . '/application/modules/Sitemusic/externals/images/nophoto_playlist_thumb_icon.png';
      } elseif( $this->view->resource_type == 'sitemusic_userpl' ) {
        $this->view->mainPhotoUrl = $baseurl . '/application/modules/Sitemusic/externals/images/nophoto_user_playlist_thumb_icon.png';
      } elseif( $this->view->resource_type == 'sitemusic_artist' ) {
        $this->view->mainPhotoUrl = $baseurl . '/application/modules/Sitemusic/externals/images/nophoto_artist_thumb_icon.png';
      } elseif( $this->view->resource_type == 'sitemusic_playlist_song' ) {
        $this->view->mainPhotoUrl = $baseurl . '/application/modules/Sitemusic/externals/images/nophoto_playlist_song_thumb_icon.png';
      }
    } elseif( $this->view->resource_type == 'user' ) {
      $this->view->mainPhotoUrl = $this->view->result->getPhotoUrl('thumb.profile') ? $this->view->result->getPhotoUrl('thumb.profile') : '';
    } elseif( in_array($this->view->resource_type, array('blog', 'forum_topic', 'poll', 'feedback', 'sitefaq_faq', 'sitereview_wishlist', 'sitereview_review', 'sitestoreproduct_review', 'sitestoreproduct_wishlist', 'sitereview_topic', 'sitemember_review')) ) {
      $this->view->mainPhotoUrl = $this->view->result->getOwner()->getPhotoUrl('thumb.profile');
    } elseif( in_array($this->view->resource_type, array('document', 'groupdocument_document', 'eventdocument_document', 'sitepagedocument_document', 'sitebusinessdocument_document')) ) {
      $this->view->mainPhotoUrl = $this->view->result->getPhotoUrl('thumb.profile');
    } elseif( empty($this->view->result->photo_id) && ($this->view->resource_type == 'music_playlist') ) {
      $this->view->mainPhotoUrl = $this->view->result->getOwner()->getPhotoUrl('thumb.profile');
    } else {
      $this->view->mainPhotoUrl = $this->view->result->getPhotoUrl('thumb.profile');
    }
  }

  public function setResourceRoute()
  {
    $route_name = '';
    $category_id = '';
    switch( $this->view->resource_type ) {
      case 'sitepage_page':
        $route_name = 'sitepage_general_category';
        $category_id = 'category_id';
        break;
      case 'sitebusiness_business':
        $route_name = 'sitebusiness_general_category';
        $category_id = 'category_id';
        break;
      case 'sitegroup_group':
        $route_name = 'sitegroup_general_category';
        $category_id = 'category_id';
        break;
      case 'sitestore_store':
        $route_name = 'sitestore_general_category';
        $category_id = 'category_id';
        break;
      case 'siteevent_event':
        $route_name = 'siteevent_general_category';
        $category_id = 'category_id';
        break;
      case 'list_listing':
        $route_name = 'list_general_category';
        $category_id = 'category';
        break;
      case 'recipe':
        $route_name = 'recipe_general_category';
        $category_id = 'category_id';
        break;
      case 'sitestoreproduct_product':
        $route_name = 'sitestoreproduct_general_category';
        $category_id = 'category_id';
        break;
      case 'sitefaq_faq':
        $route_name = 'sitefaq_general_category';
        $category_id = 'category';
        break;
      case 'sitetutorial_tutorial':
        $route_name = 'sitetutorial_general_category';
        $category_id = 'category';
        break;
      case 'sitereview_listing':
        $route_name = 'sitereview_general_category_listtype_' . $this->view->result->listingtype_id;
        $category_id = 'category_id';
        break;
    }
    $this->view->route_name = $route_name;
    $this->view->category_id = $category_id;
  }

  public function setResourceInfo()
  {
    $this->setResourceCoverPhoto();
    $this->setResourceMainPhotoUrl();
    $this->setResourceRoute();
    if( $this->view->resource_type != 'user' && !empty($this->view->informationArray) && in_array("category", $this->view->informationArray) ) {
      $getShortType = $this->view->result->getShortType();
      if( $getShortType == 'playlist' ) {
        $getShortType = 'Music';
      } elseif( $getShortType == 'topic' ) {
        $getShortType = 'Forum Topic';
      } elseif( $getShortType == 'business' ) {
        $getShortType = $this->view->translate(' Business ');
      } elseif( $getShortType == 'group' ) {
        $getShortType = $this->view->translate(' Group ');
      } elseif( $getShortType == 'page' ) {
        $getShortType = $this->view->translate(' Page ');
      } elseif( $getShortType == 'store' ) {
        $getShortType = $this->view->translate(' Store ');
      } elseif( $getShortType == 'event' ) {
        $getShortType = $this->view->translate(' Event ');
      } else {
        $getShortTypeArray = explode('_', $this->view->result->getShortType());
        foreach( $getShortTypeArray as $k => $str ) {
          $getShortTypeArray[$k] = ucfirst($str);
        }
        $getShortType = implode(' ', $getShortTypeArray);
      }
      $this->view->resourceCategoryType = $getShortType;
    }
    $this->view->info_values = $this->view->coreSettings->getSetting('seaocore.action.link', array("poke" => "poke", "share" => "share", "message" => "message", "addfriend" => "addfriend", "suggestion" => "suggestion", "joinpage" => "joinpage", "requestpage" => "requestpage", "review_wishlist" => "review_wishlist", "joinevent" => "joinevent", "editevent" => "editevent", "inviteevent" => "inviteevent"));
    $this->view->customfieldHeading = $this->view->coreSettings->getSetting('seaocore.customfieldheading', 0);
    $this->view->customParams = $this->view->coreSettings->getSetting('seaocore.customParams', 5);
    $this->view->customfieldtitle = $this->view->coreSettings->getSetting('seaocore.customfieldtitle', 0);
  }

}
