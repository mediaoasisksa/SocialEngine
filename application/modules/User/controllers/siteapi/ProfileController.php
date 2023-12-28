<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    ProfileController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_ProfileController extends Siteapi_Controller_Action_Standard {
    public function init() {
        if ($this->getRequestParam('user_id') && (0 !== ($user_id = (int) $this->getRequestParam('user_id')) && null !== ($user = Engine_Api::_()->getItem('user', $user_id)))) {
            Engine_Api::_()->core()->setSubject($user);
        }
        else if ($this->getRequestParam("user_id") && (null !== ($username = (string) $this->getRequestParam("user_id")))) {
            $user = Engine_Api::_()->getApi('Core','siteapi')->getSubjectByModuleUrl('user','users','username',$username);
            Engine_Api::_()->core()->setSubject($user);
        }
        
        if (!empty($viewer) && !Engine_Api::_()->core()->hasSubject())
            Engine_Api::_()->core()->setSubject($viewer);
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
    }

    /**
     * Get the user profile page.
     * 
     * @return array
     */
    public function indexAction() {
// Validate request methods
        $this->validateRequestMethod();
        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('user');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (empty($subject))
            $this->respondWithError('no_record');

        if (!$subject->authorization()->isAllowed($viewer, 'view'))
            $this->respondWithError('unauthorized', "You don't have permission to view member's profile");

// check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
        if (!$require_check && !$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized', "You don't have permission to view member's profile");

// Check enabled
        if (!$subject->enabled && !$viewer->isAdmin())
            $this->respondWithError('unauthorized', "You don't have permission to view member's profile");

// Check block
        if ($viewer->isBlockedBy($subject) && !$viewer->isAdmin())
            $this->respondWithError('user_blocked');

// Increment view count
        if (!$subject->isSelf($viewer)) {
            $subject->view_count++;
            $subject->save();
        }

        $bodyParams = array();

// Getting the gutter vmenus.
        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus();

// Getting the group profile page.
        if ($this->getRequestParam('profile_tabs', true))
            $bodyParams['profile_tabs'] = $this->_profileTAbsContainer($subject);

// Prepare response array
        $bodyParams['response'] = $subject->toArray();
        $bodyParams['response'] = Engine_Api::_()->getApi('Core', 'siteapi')->sanitizeArray($bodyParams['response'], array('email'));

        $bodyParams['response']['displayname'] = $subject->getTitle();
        $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location', 1);
        if (empty($locationEnabled))
            $bodyParams['response']['location'] = '';

        if (isset($bodyParams['response']['creation_ip']))
            unset($bodyParams['response']['creation_ip']);

        if (isset($bodyParams['response']['lastlogin_ip']))
            unset($bodyParams['response']['lastlogin_ip']);

// Getting viewer like or not to content.
        $bodyParams['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($subject);

        //checking follow table exist or not.
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
        if (!empty($seocoreFollowTable)) {
            $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($subject, $viewer);
            $bodyParams['response']["is_follow"] = ($follow) ? 1 : 0;
        }

        //Member verification Work............... 
        $bodyParams['response']['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($subject);
        
// Getting like count.
        $bodyParams['response']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($subject);

// Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

// FIND OUT THE MEMBER TYPE.
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            if ($optionId) {
                $optionObj = Engine_Api::_()->fields()
                        ->getFieldsOptions($subject)
                        ->getRowMatching('option_id', $optionId->value);
                if ($optionObj) {
                    $memberType = $optionObj->label;
                }
            }
        }
        $bodyParams['response']["member_type"] = !empty($memberType) ? $memberType : '';
$bodyParams['response']["profile_fields"] = Engine_Api::_()->getApi('Siteapi_Core','user')->getSubjectFieldsInfo($subject, array('noHeading' => true, 'category' => 'generic'));

// Networks
        $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($subject)
                ->where('hide = ?', 0);
        $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
        if (!empty($networks) && count($networks) > 0) {
            $networkFluentList = Engine_Api::_()->getApi('Core', 'siteapi')->fluentList($networks);
        }

// Add cover photo in case of user cover photo.
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteusercoverphoto')) {
            $getUserCoverPhoto = Engine_Api::_()->getApi('Siteapi_Core', 'siteusercoverphoto')->getCoverPhoto($subject);
            if (!empty($getUserCoverPhoto))
                $bodyParams['response']['cover'] = $getUserCoverPhoto;
            else
                $bodyParams['response']['cover'] = $getContentImages['image'];

            $getUserCoverPhotoMenu = Engine_Api::_()->getApi('Siteapi_Core', 'siteusercoverphoto')->getCoverPhotoMenu($subject);
            if (!empty($getUserCoverPhotoMenu))
                $bodyParams['response']['coverPhotoMenu'] = $getUserCoverPhotoMenu;

            $getMainPhotoMenu = Engine_Api::_()->getApi('Siteapi_Core', 'siteusercoverphoto')->getMainPhotoMenu($subject);
            if (!empty($getMainPhotoMenu))
                $bodyParams['response']['mainPhotoMenu'] = $getMainPhotoMenu;
        }

        $bodyParams['response']["network_list"] = !empty($networkFluentList) ? $networkFluentList : '';

        $bodyParams['response']["friend_count"] = $subject->membership()->getMemberCount($subject);

        $this->respondWithSuccess($bodyParams, true);
    }

// Getting the member information.
    public function getMemberInfoAction() {
// Validate request methods
        $this->validateRequestMethod();

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('user');

        if (!empty($subject))
            $getProfileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getProfileInfo($subject);

        if (isset($_REQUEST['field_order']) && !empty($_REQUEST['field_order'])) {
            foreach ($getProfileInfo as $key => $value) {
                $getProfileInfo[$key] = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($value);
            }
            $getProfileInfo = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($getProfileInfo);
        }

        if (empty($subject) || empty($getProfileInfo)) {
            $getProfileInfo = '';
            $this->respondWithSuccess($getProfileInfo);
        }

        $this->respondWithSuccess($getProfileInfo);
    }

    /**
     * Get the friends array respective of user_id.
     * 
     * @return array
     */
    public function getFriendListAction() {
// Validate request methods
        $this->validateRequestMethod();

// Get subject and check auth
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject('user');
        $viewer_id = $viewer->getIdentity();
        $response['canAddToList'] = 0;

// Don't render this if friendships are disabled
        if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible)
            $this->respondWithError('friendship_disabled');

        if (!$subject->authorization()->isAllowed($viewer, 'view'))
            $this->respondWithError('unauthorized');

// Multiple friend mode
        $select = $subject->membership()->getMembersOfSelect();
        $friends = $paginator = Zend_Paginator::factory($select);

        $requestLimit = $this->getRequestParam("limit", 10);
        $requestPage = $this->getRequestParam("page", 1);

// Set item count per page and current page number
        $paginator->setItemCountPerPage($requestLimit);
        $paginator->setCurrentPageNumber($requestPage);

        if (!empty($viewer_id) && isset($subject->user_id) && !empty($subject->user_id) && $viewer_id == $subject->user_id)
            $response['canAddToList'] = 1;

        $response['totalItemCount'] = $paginator->getTotalItemCount();

        if (!empty($viewer_id) && isset($subject->user_id) && !empty($subject->user_id) && $viewer_id == $subject->user_id)
            $response['canAddToList'] = 1;
        foreach ($paginator as $membership) {
            $membershipArray = $membership->toArray();
            $friendObj = Engine_Api::_()->getItem('user', $membership->resource_id);

            if (isset($friendObj->user_id) && !empty($friendObj->user_id)) {
                $friendArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($friendObj);
                //Follow work for friend list.........
                $canFollow = $this->canFollowUser();
                $friendArray['isVerified'] = $canFollow;
                if (!empty($canFollow)) {
                    $friendArray['isfollow'] = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($friendObj, $viewer);
                }
// Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($friendObj);
                $friendArray = array_merge($friendArray, $getContentImages);

                //Member verification Work...............
                $friendArray['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($friendObj);

                $getFriendshipButton = Engine_Api::_()->getApi('Siteapi_Core', 'user')->userFriendship($friendObj);
                $friendArray['menus'] = !empty($getFriendshipButton) ? $getFriendshipButton : '';

                $response['friends'][] = $friendArray;
            }
        }

        $this->respondWithSuccess($response);
    }

    /**
     * Get the posts done by respective users
     * 
     * @return array
     */
    public function forumPostsAction() {
// Validate request methods
        $this->validateRequestMethod();

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('user');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (empty($subject))
            $this->respondWithError('no_record');

        if (!$subject->authorization()->isAllowed($viewer, 'view'))
            $this->respondWithError('unauthorized');

// Get forums allowed to be viewed by current user
        $forumIds = array();
        $authTable = Engine_Api::_()->getDbtable('allow', 'authorization');
        $perms = $authTable->select()
                ->where('resource_type = ?', 'forum')
                ->where('action = ?', 'view')
                ->query()
                ->fetchAll();
        foreach ($perms as $perm) {
            if ($perm['role'] == 'everyone') {
                $forumIds[] = $perm['resource_id'];
            } else if ($viewer && $viewer->getIdentity() && $perm['role'] == 'authorization_level' && $perm['role_id'] == $viewer->level_id) {
                $forumIds[] = $perm['resource_id'];
            }
        }

        if (empty($forumIds))
            $this->respondWithError('no_record');

        $postsTable = Engine_Api::_()->getDbtable('posts', 'forum');
        $postsSelect = $postsTable->select()
                ->where('forum_id IN(?)', $forumIds)
                ->where('user_id = ?', $subject->getIdentity())
                ->order('creation_date DESC')
        ;

        $paginator = Zend_Paginator::factory($postsSelect);
// Set item count per page and current page number
        $paginator->setItemCountPerPage($this->getRequestParam('limit', 10));
        $paginator->setCurrentPageNumber($this->getRequestParam('page', 1));

        $response['totalItemCount'] = $paginator->getTotalItemCount();

        foreach ($paginator as $post) {
            $tempResponse['post'] = $post->toArray();

            $topic = $post->getParent();
            if (!empty($topic) && is_object($topic))
                $tempResponse['topic'] = $topic->toArray();

            $forum = $topic->getParent();
            if (!empty($forum) && is_object($forum))
                $tempResponse['forum'] = $forum->toArray();

            $response[] = $tempResponse;
        }

        $this->respondWithSuccess($response);
    }

    /**
     * For User Profile: Get the list of gutter menus.
     *
     * @return array
     */
    private function _gutterMenus() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject = Engine_Api::_()->core()->getSubject();
        $subject_id = $subject->getIdentity();

        if (empty($viewer_id))
            return;

        if ($viewer_id == $subject_id) {
            $menus[] = array(
                'label' => $this->translate('Edit My Profile'),
                'name' => 'user_home_edit',
                'url' => 'members/edit/profile',
                'urlParams' => array()
            );
        } else {
            $menu = Engine_Api::_()->getApi('Siteapi_Core', 'user')->userFriendship($subject);
            if (!empty($menu)) {
                $menus[] = $menu;
            }
        }

// Block User
        $blockUsers = true;
        if (!$viewer->getIdentity() || $viewer->getGuid() == $subject->getGuid()) {
            $blockUsers = false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('user', $viewer, 'block')) {
            $blockUsers = false;
        }

        if (!empty($blockUsers)) {
            if (!$subject->isBlockedBy($viewer)) {
                $menus[] = array(
                    'label' => $this->translate('Block Member'),
                    'name' => 'user_profile_block',
                    'url' => 'block/add',
                    'urlParams' => array(
                        "user_id" => $subject->getIdentity()
                    )
                );
            } else {
                $menus[] = array(
                    'label' => $this->translate('Unblock Member'),
                    'name' => 'user_profile_unblock',
                    'url' => 'block/remove',
                    'urlParams' => array(
                        "user_id" => $subject->getIdentity()
                    )
                );
            }
        }

// Report User
        $reportUsers = true;
        if (!$viewer->getIdentity() || !$subject->getIdentity() || $viewer->isSelf($subject)) {
            $reportUsers = false;
        }

        if (!empty($reportUsers)) {
            $menus[] = array(
                'label' => $this->translate('Report'),
                'name' => 'user_profile_report',
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

// Send Message
        $sendMessage = true;
        if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
            $sendMessage = false;
        }

        $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
        if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
            $sendMessage = false;
        }

        $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
        if ($messageAuth == 'none') {
            $sendMessage = false;
        } else if ($messageAuth == 'friends') {
// Get data
            $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
            if (!$direction) {
//one way
                $friendship_status = $viewer->membership()->getRow($subject);
            } else
                $friendship_status = $subject->membership()->getRow($viewer);

            if (!$friendship_status || $friendship_status->active == 0) {
                $sendMessage = false;
            }
        }

        if (!empty($sendMessage)) {
            $menus[] = array(
                'label' => $this->translate('Send Message'),
                'name' => 'user_profile_send_message',
                'url' => 'user/profile/send-message',
                'urlParams' => array(
                    "to" => $subject->getIdentity()
                )
            );
        }

        $subject_id = $subject->getIdentity();

        if (_IOS_VERSION >= '2.0.5' && _IOS_VERSION < '2.8.6' && $viewer_id == $subject_id) {

            $select = $subject->membership()->getMembersOfSelect();
            $menus[] = array(
                'label' => $this->translate('Friends'),
                'name' => 'friends',
                'totalItemCount' => Zend_Paginator::factory($select)->getTotalItemCount()
            );
        }

        //follow feature cutomization Member profile page................................................
        //checking follow table exist or not.
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();


        if ($viewer_id != $subject_id && (_IOS_VERSION >= '2.1.5' || _ANDROID_VERSION >= '2.3') && !empty($seocoreFollowTable)) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitemember");
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $enabledFollow = $coreSettings->getSetting('sitemember.user.follow.enable', 0);
            $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
            if (!empty($isModEnabled) && !empty($direction) && !empty($enabledFollow)) {
                $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($subject, $viewer);
                if (!empty($follow)) {
                    $label = $this->translate('Following');
                    $name = 'following';
                } else {
                    $label = $this->translate('Follow');
                    $name = 'follow';
                }
                $menus[] = array(
                    'label' => $label,
                    'name' => $name,
                    'url' => 'advancedmember/follow',
                    'urlParams' => array(
                        "resource_id" => $subject->getIdentity(),
                        "resource_type" => $subject->getType()
                    )
                );
            }
        }

        //..................follow feature cutomization......................................


        return $menus;
    }

    /*
     * Getting the blog count
     */
    private function _getBlogCount() {
        $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator(array(
            'orderby' => 'creation_date',
            'draft' => '0',
            'user_id' => Engine_Api::_()->core()->getSubject()->getIdentity(),
        ));

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting the group count
     */
    private function _getGroupCount() {
        $subject = Engine_Api::_()->core()->getSubject('user');
        $membership = Engine_Api::_()->getDbtable('membership', 'group');
        $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($subject));

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting the channel count
     */
    private function _getChannelCount() {
        $subject = Engine_Api::_()->core()->getSubject('user');
        $params = array(
            'status' => 1,
            'owner_id' => $subject->getIdentity()
        );
        $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelPaginator($params);

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting the event count
     */
    private function _getEventCount() {
        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("event");
        $isAdvModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("siteevent");
        if (isset($isModEnabled) && !empty($isModEnabled)) {
            $subject = Engine_Api::_()->core()->getSubject('user');
            $membership = Engine_Api::_()->getDbtable('membership', 'event');
            $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($subject)->order('starttime DESC'));
        }

        if (isset($isAdvModEnabled) && !empty($isAdvModEnabled)) {
            $subject = Engine_Api::_()->core()->getSubject('user');
            $membership = Engine_Api::_()->getDbtable('membership', 'siteevent');
            if (_CLIENT_TYPE == 'ios') {
                $values['type'] = 'browse';
                $values['user_id'] = $subject->getIdentity();
                $values['action'] = 'upcoming';
                $values['ratingType'] = 'rating_both';
                $values['orderbystarttime'] = 1;
            } else {
                $values['action'] = 'manage';
                $values['controller'] = 'index';
                $values['module'] = 'siteevent';
                $values['type'] = 'manage';
                $values['orderby'] = 'event_id';
                $values['user_id'] = $subject->getIdentity();
                $values['showEventType'] = 'all';
                $values['viewtype'] = '';
                $values['rsvp'] = -1;
            }
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
        }


        return $paginator->getTotalItemCount();
    }

    /*
     * Remove the cover photo OR profile photo of the user.
     */
    public function removeProfilePhotoAction() {
        $this->validateRequestMethod('DELETE');

//CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $this->getRequestParam('user_id');
        $user = Engine_Api::_()->getItem('user', $user_id);
        if (empty($user))
            $this->respondWithError('no_record');

        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $level_id = $this->getRequestParam("level_id", 0);
        }

        $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
        if ($can_edit) {
            $can_edit = 1;
        } else {
            $can_edit = 0;
        }
        if (!$can_edit) {
            $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isPost()) {
            $user->photo_id = 0;
            $user->save();
        }

        $this->successResponseNoContent('no_content');
    }

    /*
     * Getting the album count
     */
    private function _getAlbumCount() {
        try {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("album");
            if (!empty($isModEnabled)) {
                $subject = Engine_Api::_()->core()->getSubject('user');
                $paginator = Engine_Api::_()->getItemTable('album')->getAlbumPaginator(array('owner' => $subject));
                return $paginator->getTotalItemCount();
            } else {
                return 0;
            }
        } catch (Exception $ex) {
            
        }
    }

    /*
     * Getting the classified count
     */
    private function _getClassifiedCount() {
        $subject = Engine_Api::_()->core()->getSubject('user');
        $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator(array(
            'orderby' => 'creation_date',
            'user_id' => $subject->getIdentity(),
        ));

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting the music count
     */
    private function _getMusicCount() {
        $subject = Engine_Api::_()->core()->getSubject('user');
        $paginator = Engine_Api::_()->music()->getPlaylistPaginator(array(
            'user' => $subject->getIdentity(),
            'sort' => 'creation_date',
            'searchBit' => 1
        ));

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting the poll count
     */
    private function _getPollCount() {
        $subject = Engine_Api::_()->core()->getSubject('user');
        $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator(array(
            'user_id' => $subject->getIdentity(),
            'sort' => "creation_date",
        ));

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting the video count
     */
    private function _getVideoCount() {
        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("video");
        $isAdvModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitevideo");
        if (isset($isModEnabled) && !empty($isModEnabled)) {
            $subject = Engine_Api::_()->core()->getSubject('user');
            $paginator = Engine_Api::_()->video()->getVideosPaginator(array(
                'user_id' => $subject->getIdentity(),
                'status' => 1,
                'search' => 1
            ));
        }


        if (isset($isAdvModEnabled) && !empty($isAdvModEnabled)) {
            $subject = Engine_Api::_()->core()->getSubject('user');
            if (!empty($subject))
                $params['owner_id'] = $subject->user_id;
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            if (isset($params['owner_id']) && $viewer_id && !empty($params['owner_id']) && $viewer_id != $params['owner_id'] && $viewer->level_id != 1) {
                $params['status'] = 1;
                $params['search'] = 1;
            }

            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        }

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting sitepage count 
     */
    private function _getSitepageCount() {
        $subject = Engine_Api::_()->core()->getSubject('user');
        $sitepageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $userPagesCount = $sitepageTable->countUserPages($subject->getIdentity());
        return $userPagesCount;
    }

    /*
     * Getting the listing count
     */
    private function _getListingCount($listingTypeId) {
        $subject = Engine_Api::_()->core()->getSubject('user');
        $params = array();
        $paginator = Engine_Api::_()->getDbTable('listings', 'sitereview')->getSitereviewsPaginator(array(
            'type' => 'browse',
            'orderby' => 'listing_id',
            'user_id' => $subject->getIdentity(),
            'listingtype_id' => $listingTypeId,
            'show' => 1
        ));

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting the forum topics count
     */
    private function _getForumTopicsCount() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject('user');

// Get forums allowed to be viewed by current user
        $forumIds = array();
        $authTable = Engine_Api::_()->getDbtable('allow', 'authorization');
        $perms = $authTable->select()
                ->where('resource_type = ?', 'forum')
                ->where('action = ?', 'view')
                ->query()
                ->fetchAll();
        foreach ($perms as $perm) {
            if ($perm['role'] == 'everyone') {
                $forumIds[] = $perm['resource_id'];
            } else if ($viewer && $viewer->getIdentity() && $perm['role'] == 'authorization_level' && $perm['role_id'] == $viewer->level_id) {
                $forumIds[] = $perm['resource_id'];
            }
        }

        $postsTable = Engine_Api::_()->getDbtable('posts', 'forum');
        $postsSelect = $postsTable->select()
                ->where('forum_id IN(?)', $forumIds)
                ->where('user_id = ?', $subject->getIdentity())
                ->order('creation_date DESC')
        ;

        $paginator = Zend_Paginator::factory($postsSelect);

        return $paginator->getTotalItemCount();
    }

    /*
     * Getting sitepage count 
     */
    private function _getSitegroupCount() {
        $subject = Engine_Api::_()->core()->getSubject('user');
        $siteGroupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
        $params['draft'] = 1;
        $params['visible'] = 1;
        $params['type'] = 'browse';
        $params['type_location'] = 'manage';
        $params['user_id'] = $subject->getIdentity();
        $groupsObj = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($params);

        //$userGroupsCount = $siteGroupTable->countUserGroups($subject->getIdentity());
        return $groupsObj->getTotalItemCount();
    }

    /**
     * For User Profile: Get the list of tabs.
     *
     * @return array
     */
    private function _profileTAbsContainer($subject) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $subject_id = $subject->getIdentity();

        $response[] = array(
            'label' => $this->translate('Updates'),
            'name' => 'update'
        );

        $response[] = array(
            'label' => $this->translate('About'),
            'name' => 'info'
        );

        if (_CLIENT_TYPE != 'ios') {
            $select = $subject->membership()->getMembersOfSelect();
            $response[] = array(
                'label' => $this->translate('Friends'),
                'name' => 'friends',
                'totalItemCount' => Zend_Paginator::factory($select)->getTotalItemCount()
            );
        } elseif (_IOS_VERSION >= '2.8.6' || $viewer_id != $subject_id) {
            $select = $subject->membership()->getMembersOfSelect();
            $response[] = array(
                'label' => $this->translate('Friends'),
                'name' => 'friends',
                'totalItemCount' => Zend_Paginator::factory($select)->getTotalItemCount()
            );
        }

        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("blog");
        if (!empty($isModEnabled)) {
            $blogCount = $this->_getBlogCount();
            $response[] = array(
                'label' => ($blogCount == 1) ? $this->translate('Blog') : $this->translate('Blogs'),
                'name' => 'blog',
                'totalItemCount' => $blogCount
            );
        }

        // sitegroup module code
        if ((_ANDROID_VERSION && (_ANDROID_VERSION >= '1.7.1')) || (_IOS_VERSION && (_IOS_VERSION >= '1.7.3'))) {
            $isSitegroupModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitegroup");
            if (!empty($isSitegroupModEnabled)) {
                $siteGroupCount = $this->_getSitegroupCount();
                $response[] = array(
                    'label' => ($siteGroupCount == 1) ? $this->translate('Group') : $this->translate('Groups'),
                    'name' => 'sitegroup',
                    'totalItemCount' => $siteGroupCount
                );
            } else {
                $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("group");
                if (!empty($isModEnabled)) {
                    $groupCount = $this->_getGroupCount();
                    $response[] = array(
                        'label' => ($groupCount == 1) ? $this->translate('Group') : $this->translate('Groups'),
                        'name' => 'group',
                        'totalItemCount' => $groupCount
                    );
                }
            }
        } else {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("group");
            if (!empty($isModEnabled)) {
                $groupCount = $this->_getGroupCount();
                $response[] = array(
                    'label' => ($groupCount == 1) ? $this->translate('Group') : $this->translate('Groups'),
                    'name' => 'group',
                    'totalItemCount' => $groupCount
                );
            }
        }

        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("event");
        $isAdvModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("siteevent");
        if (!empty($isModEnabled) || !empty($isAdvModEnabled)) {
            $eventCount = $this->_getEventCount();
            $response[] = array(
                'label' => ($eventCount == 1) ? $this->translate('Event') : $this->translate('Events'),
                'name' => 'event',
                'totalItemCount' => $eventCount,
                'isAdvancedModuleEnabled' => $isAdvModEnabled
            );
        }

        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("album");
        if (!empty($isModEnabled)) {
            $albumCount = $this->_getAlbumCount();
            $response[] = array(
                'label' => ($albumCount == 1) ? $this->translate('Album') : $this->translate('Albums'),
                'name' => 'album',
                'totalItemCount' => $albumCount
            );
        }

        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("classified");
        if (!empty($isModEnabled)) {
            $classifiedCount = $this->_getClassifiedCount();
            $response[] = array(
                'label' => ($classifiedCount == 1) ? $this->translate('Classified') : $this->translate('Classifieds'),
                'name' => 'classified',
                'totalItemCount' => $classifiedCount
            );
        }


        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("music");
        if (!empty($isModEnabled)) {
            $musicCount = $this->_getMusicCount();
            $response[] = array(
                'label' => $this->translate('Music'),
                'name' => 'music',
                'totalItemCount' => $musicCount
            );
        }

        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("poll");
        if (!empty($isModEnabled)) {
            $pollCount = $this->_getPollCount();
            $response[] = array(
                'label' => ($pollCount == 1) ? $this->translate('Poll') : $this->translate('Polls'),
                'name' => 'poll',
                'totalItemCount' => $pollCount
            );
        }

        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("video");
        $isAdvModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitevideo");
        if (!empty($isModEnabled) || !empty($isAdvModEnabled)) {
            $videoCount = $this->_getVideoCount();
            $response[] = array(
                'label' => ($videoCount == 1) ? $this->translate('Video') : $this->translate('Videos'),
                'name' => 'video',
                'totalItemCount' => $videoCount,
                'isAdvancedModuleEnabled' => $isAdvModEnabled
            );
        }

        $isAdvModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitevideo");
        if (!empty($isAdvModEnabled)) {
            $channelCount = $this->_getChannelCount();
            $response[] = array(
                'label' => ($channelCount == 1) ? $this->translate('Channel') : $this->translate('Channels'),
                'name' => 'channel',
                'totalItemCount' => $channelCount,
                'isAdvancedModuleEnabled' => $isAdvModEnabled
            );
        }


        if ((_IOS_VERSION && (_IOS_VERSION >= '1.5.2')) || (_ANDROID_VERSION && (_ANDROID_VERSION >= '1.6.2'))) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview");
            $params['visible'] = 1;
            if (!empty($isModEnabled)) {
                $listingTypes = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypes(-1, $params);
                $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitereview');
                foreach ($listingTypes as $listingType) {
                    $categories = $tableCategory->getCategories(null, 0, $listingType->listingtype_id, 0, 1, 0, 0, 1, array('category_id'));
                    if (count($categories) == 0)
                        continue;
                    $listingCount = $this->_getListingCount($listingType->listingtype_id);
                    $response[] = array(
                        'label' => $this->translate($listingType->title_plural),
                        'name' => 'sitereview_listing',
                        'listingtype_id' => $listingType->listingtype_id,
                        'totalItemCount' => $listingCount
                    );
                }
            }
        }

// sitepage module code
        if ((_IOS_VERSION && (_IOS_VERSION >= '1.5.6')) || (_ANDROID_VERSION && (_ANDROID_VERSION >= '1.7'))) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitepage");
            if (!empty($isModEnabled)) {
                $sitepageCount = $this->_getSitepageCount();
                $response[] = array(
                    'label' => ($sitepageCount == 1) ? $this->translate('Page') : $this->translate('Pages'),
                    'name' => 'sitepage',
                    'totalItemCount' => $sitepageCount
                );
            }
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();

        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitemember");
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $enabledFollow = $coreSettings->getSetting('sitemember.user.follow.enable', 0);
        $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
        if (!empty($isModEnabled) && !empty($direction) && !empty($enabledFollow) && !empty($seocoreFollowTable)) {

            $params['user_id'] = $subject->getIdentity();
            $totalFollowers = $this->_followerCount($subject, $params);
            $response[] = array(
                'label' => $this->translate('Followers'),
                'name' => 'followers',
                'totalItemCount' => $totalFollowers
            );

            $totalFollowingCount = $this->_followingCount($subject, $params);
            $response[] = array(
                'label' => $this->translate('Following'),
                'name' => 'following',
                'totalItemCount' => $totalFollowingCount
            );
        }

        $isModEnabledCrowdfunding = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitecrowdfunding");
        if ($isModEnabledCrowdfunding) {
            $params['projectType'] = 'All';
            $params['orderby'] = 'startDate';
            $params['owner_id'] = $subject->getIdentity();
            $projectDbTables = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
            $paginator = $projectDbTables->getProjectPaginator($params);
            $paginator->setItemCountPerPage(20);
            $paginator->setCurrentPageNumber(1);
            $totalProjectCount = $paginator->getTotalItemCount();
            if ($totalProjectCount > 0)
                $response[] = array(
                    'label' => $this->translate('Projects'),
                    'name' => 'project',
                    'totalItemCount' => $totalProjectCount,
                    'url' => 'crowdfunding/browse',
                    'module' => 'sitecrowdfunding'
                );
        }
        return $response;
    }

    public function userProfileTabAction() {
        $this->validateRequestMethod();
        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('user');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (empty($subject))
            $this->respondWithError('no_record');

        if (!$subject->authorization()->isAllowed($viewer, 'view'))
            $this->respondWithError('unauthorized', "You don't have permission to view member's profile");

// check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
        if (!$require_check && !$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized', "You don't have permission to view member's profile");

// Check enabled
        if (!$subject->enabled && !$viewer->isAdmin())
            $this->respondWithError('unauthorized', "You don't have permission to view member's profile");

// Check block
        if ($viewer->isBlockedBy($subject) && !$viewer->isAdmin())
            $this->respondWithError('user_blocked');
        $bodyParams['profile_tabs'] = $this->_profileTAbsContainer($subject);
        $this->respondWithSuccess($bodyParams);
    }

    private function _followingCount($subject, $params = array()) {
        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $select1 = $followTable->getFollowingSelect($subject, $params);
        $followingMembers = Zend_Paginator::factory($select1);
        $follwingCount = 0;
        foreach ($followingMembers as $following) {
            if ($following->resource_type == 'user') {
                $user = Engine_Api::_()->getItem('user', $following->resource_id);
                if (!isset($user->user_id)) {
                    continue;
                }
                if ($subject->getType() == 'user') {
                    $friendshipType = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($user, $subject);
                    if ($friendshipType == 'remove_friend') {
                        continue;
                    }
                }
                $follwingCount++;
            }
        }
        return $follwingCount;
    }

    private function _followerCount($subject, $params = array()) {
        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $select = $followTable->getFollowersSelect($subject, $params);
        $followers = Zend_Paginator::factory($select);
        $followerCount = 0;
        foreach ($followers as $following) {
            if ($following->poster_type == 'user') {
                $followuser = Engine_Api::_()->getItem('user', $following->poster_id);

                if ($subject->getType() == 'user') {
                    $friendshipType = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($followuser, $subject);

                    if ($friendshipType == 'remove_friend') {
                        continue;
                    }
                }
                $followerCount++;
            }
        }
        return $followerCount;
    }

    public function canFollowUser() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
        $canFollow = 0;
        if (!empty($seocoreFollowTable)) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitemember");
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $enabledFollow = $coreSettings->getSetting('sitemember.user.follow.enable', 0);
            $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
            if (!empty($isModEnabled) && !empty($direction) && !empty($enabledFollow)) {
                $canFollow = 1;
            }
        }
        return $canFollow;
    }

}
