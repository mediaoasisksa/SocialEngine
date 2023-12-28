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
class Event_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if ($this->getRequestParam('event_id') && (0 !== ($event_id = (int) $this->getRequestParam('event_id')) &&
                null !== ($event = Engine_Api::_()->getItem('event', $event_id))))
            Engine_Api::_()->core()->setSubject($event);
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
    }

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('event', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'event')->getBrowseSearchForm(), true);
    }

    /**
     * Return the Event View page.
     * 
     * @return array
     */
    public function viewAction() {
        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('event');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $isAllowedView = $subject->authorization()->isAllowed($viewer, 'view');
        $siteapiEventView = Zend_Registry::isRegistered('siteapiEventView') ? Zend_Registry::get('siteapiEventView') : null;

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO VIEW EVENT.
        if (empty($isAllowedView) || empty($siteapiEventView)) {
            $module_error_type = @ucfirst($subject->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }

        $bodyParams = array();

        // GETTING THE GUTTER-MENUS.
        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($subject);

        // GETTING THE EVENT PROFILE TABS.
        if ($this->getRequestParam('profile_tabs', true))
            $bodyParams['profile_tabs'] = $this->_profileTAbsContainer($subject);

        // GETTING THE RSVP.
        if ($this->getRequestParam('profile_rsvp', null)) {
            if ($subject->membership()->isMember($viewer, true)) {
                $getRSVP = $this->getRequestParam('rsvp', 2);
                if ($this->getRequest()->isPost() && isset($getRSVP)) {
                    $subject->membership()
                            ->getMemberInfo($viewer)
                            ->setFromArray(array('rsvp' => $getRSVP))
                            ->save();

                    $this->successResponseNoContent('no_content');
                    return;
                } else {
                    $bodyParams['profile_rsvp_form'] = $this->_getProfileRSVP($subject);
                    $row = $subject->membership()->getRow($viewer);
                    $bodyParams['profile_rsvp_value'] = $row->rsvp;
                }
            }
        }

        // Validate request methods
        $this->validateRequestMethod();

        // PREPARE RESPONSE ARRAY
        $bodyParams['response'] = $subject->toArray();
        $bodyParams['response']['guid'] = $subject->getGuid();
        $bodyParams['response']['isowner'] = $subject->isOwner($viewer);

        if (isset($viewer->timezone))
            $tz = $viewer->timezone;

        if (isset($subject->starttime) && !empty($subject->starttime) && isset($tz)) {
            $startDateObject = new Zend_Date(strtotime($subject->starttime));
            $startDateObject->setTimezone($tz);
            $bodyParams['response']['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
        }
        if (isset($subject->endtime) && !empty($subject->endtime) && isset($tz)) {
            $endDateObject = new Zend_Date(strtotime($subject->endtime));
            $endDateObject->setTimezone($tz);
            $bodyParams['response']['endtime'] = $endDateObject->get('YYYY-MM-dd HH:mm:ss');
        }


        if ($viewer->getIdentity()) {
            $is_member = $subject->membership()->isMember($viewer, null);
            $can_upload = $subject->authorization()->isAllowed(null, 'photo');
            $bodyParams['response']['isMember'] = (!empty($is_member)) ? 1 : 0;
            $bodyParams['response']['canUpload'] = (!empty($can_upload)) ? 1 : 0;
        }

        // Add Image
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $bodyParams['response']["owner_title"] = $subject->getOwner()->getTitle();
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $bodyParams['response']['category_title'] = (!empty($subject->category_id)) ? $subject->categoryName() : '';
        $bodyParams['response']['get_attending_count'] = $subject->getAttendingCount();
        $bodyParams['response']['get_maybe_count'] = $subject->getMaybeCount();
        $bodyParams['response']['get_not_attending_count'] = $subject->getNotAttendingCount();
        $bodyParams['response']['get_awaiting_reply_count'] = $subject->getAwaitingReplyCount();

        // Increment view count
        if (!$subject->getOwner()->isSelf($viewer)) {
            $subject->view_count++;
            $subject->save();
        }

        if (!empty($siteapiEventView))
            $this->respondWithSuccess($bodyParams);
    }

    /**
     * Delete the Event.
     * 
     * @return array
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('event');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        // GET LOGGED-IN USER LEVEL ID.
        if (!empty($viewer_id))
            $level_id = $viewer->level_id;

        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToDelete = $permissionsTable->getAllowed('event', $level_id, 'delete');
        }

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO DELETE EVENT.
        if (empty($allowToDelete))
            $this->respondWithError('unauthorized');

        $db = $subject->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $subject->delete();
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('event');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $isAllowedView = $subject->authorization()->isAllowed($viewer, 'edit');

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO EDIT EVENT.
        if (empty($isAllowedView))
            $this->respondWithError('unauthorized');


        // CHECK Event FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE EVENT EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            $parent_type = $this->getRequestParam('parent_type');
            $parent_id = $this->getRequestParam('parent_id', $this->getRequestParam('subject_id'));

            if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
                $group = Engine_Api::_()->getItem('group', $parent_id);
                $isEventAllowed = $group->authorization()->isAllowed($viewer, 'event');
                if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'event')->isValid())
                    $this->respondWithError('unauthorized');
            } else {
                $parent_type = 'user';
                $parent_id = $viewer->getIdentity();
            }

            // IF THERE ARE NO FORM POST YET THEN RETURN THE Event FORM.
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'event')->getForm($subject, $parent_type, $parent_id);
            $formValues = $subject->toArray();

            // FIND OUT THE PRIVACY.
            $auth = Engine_Api::_()->authorization()->context;
            if ($subject->parent_type == 'group') {
                $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            $formValues['auth_invite'] = ($auth->isAllowed($event, 'member', 'invite')) ? 1 : 0;

            foreach ($roles as $role) {
                if ($auth->isAllowed($subject, $role, 'view')) {
                    $formValues['auth_view'] = $role;
                }
                if ($auth->isAllowed($subject, $role, 'comment')) {
                    $formValues['auth_comment'] = $role;
                }
                if ($auth->isAllowed($subject, $role, 'photo')) {
                    $formValues['auth_photo'] = $role;
                }
            }
            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* UPDATE THE EVENT INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO THE ARRAY.
            $data = $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'event')->getForm($subject, $this->getRequestParam("parent_type", null), $this->getRequestParam("parent_id", null));
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values;

            // START FORM VALIDATION
            $viewer = Engine_Api::_()->user()->getViewer();
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'event')->getFormValidators($subject);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Convert times
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $start = strtotime($values['starttime']);
            $end = strtotime($values['endtime']);
            date_default_timezone_set($oldTz);
            $values['starttime'] = date('Y-m-d H:i:s', $start);
            $values['endtime'] = date('Y-m-d H:i:s', $end);

            // Check parent
            if (!isset($values['host']) && $subject->parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
                $group = Engine_Api::_()->getItem('group', $subject->parent_id);
                $values['host'] = $group->getTitle();
            }

            // Process
            $db = Engine_Api::_()->getItemTable('event')->getAdapter();
            $db->beginTransaction();

            try {
                // Set event info
                $subject->setFromArray($values);
                $subject->save();

                // SET PHOTO
                if (!empty($_FILES['photo']))
                    Engine_Api::_()->getApi('Siteapi_Core', 'event')->setPhoto($_FILES['photo'], $subject);

                // Process privacy
                $auth = Engine_Api::_()->authorization()->context;

                if ($subject->parent_type == 'group') {
                    $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);
                $photoMax = array_search($values['auth_photo'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($subject, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($subject, $role, 'comment', ($i <= $commentMax));
                    $auth->setAllowed($subject, $role, 'photo', ($i <= $photoMax));
                }

                $auth->setAllowed($subject, 'member', 'invite', $values['auth_invite']);

                // Commit
                $db->commit();
            } catch (Engine_Image_Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }


            $db->beginTransaction();
            try {
                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($subject) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $this->successResponseNoContent('no_content', true);
        }
    }

    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('event', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        // Prepare
        $values = $response = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
        $siteapiEventBrowse = Zend_Registry::isRegistered('siteapiEventBrowse') ? Zend_Registry::get('siteapiEventBrowse') : null;


        $filter = $this->getRequestParam('filter', 'future');
        if ($filter != 'past' && $filter != 'future')
            $filter = 'future';

        if ($viewer->getIdentity() && @$values['view'] == 1) {
            $values['users'] = array();
            foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
                $values['users'][] = $memberinfo->user_id;
            }
        }

        $values['search'] = 1;

        if ($filter == "past") {
            $values['past'] = 1;
        } else {
            $values['future'] = 1;
        }

// check to see if request is for specific user's listings
        $user_id = $this->getRequestParam('user_id', null);
        if (!empty($user_id)) {
            $values['user_id'] = $user_id;
        }


        if ($this->getRequestParam('search', null)) {
            $values['search_text'] = $this->getRequestParam('search');
        }

        if ($this->getRequestParam('search_text', null)) {
            $values['search_text'] = $this->getRequestParam('search_text');
        }

        if ($this->getRequestParam('category_id', null)) {
            $values['category_id'] = $this->getRequestParam('category_id');
        }

//    if ( $this->getRequestParam('view', null) ) {
//      $values['view'] = $this->getRequestParam('view');
//    }

        if ($this->getRequestParam('order', null)) {
            $values['order'] = $this->getRequestParam('order');
        }

// Get paginator
        // In case of user_id sent in parameter for all events of a paticular user
        if (isset($values['user_id']) && !empty($values['user_id'])) {
//            $table = Engine_Api::_()->getDbtable('events', 'event');
//            $tableName = $table->info('name');
//
//            $select = $table->select()
//                    ->where('user_id = ?', $values['user_id']);
//
//            if (!empty($values['search_text'])) {
//                $select->where(
//                        $table->getAdapter()->quoteInto("`{$tableName}`.`title` LIKE ?", '%' . $values['search_text'] . '%') . ' OR ' .
//                        $table->getAdapter()->quoteInto("`{$tableName}`.`description` LIKE ?", '%' . $values['search_text'] . '%')
//                );
//            }
//
//            if (!empty($values['category_id']))
//                $select->where('category_id = ?', $values['category_id']);
//
//            // Order
//            if (!empty($values['order'])) {
//                $select->order($values['order']);
//            } else {
//                $select->order('starttime');
//            }
//
//            $paginator = Zend_Paginator::factory($select);

            $member = Engine_Api::_()->getItem('user', $values['user_id']);
            if (isset($member) && !empty($member)) {
                $membership = Engine_Api::_()->getDbtable('membership', 'event');
                $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($member)->order('starttime DESC'));
            }
        } else {
            $paginator = Engine_Api::_()->getItemTable('event')->getEventPaginator($values);
        }

        $requestLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('event_page', 12);
        $requestPage = $this->getRequestParam("page", 1);

        $paginator->setCurrentPageNumber($requestPage);
        $paginator->setItemCountPerPage($requestLimit);
        $response["getTotalItemCount"] = $getTotalItemCount = $paginator->getTotalItemCount();
        if (!empty($getTotalItemCount)) {
            foreach ($paginator as $eventObj) {
                $event = $eventObj->toArray();

                if (isset($viewer->timezone))
                    $tz = $viewer->timezone;

                if (isset($eventObj->starttime) && !empty($eventObj->starttime) && isset($tz)) {
                    $startDateObject = new Zend_Date(strtotime($eventObj->starttime));
                    $startDateObject->setTimezone($tz);
                    $event['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
                }
                if (isset($eventObj->endtime) && !empty($eventObj->endtime) && isset($tz)) {
                    $endDateObject = new Zend_Date(strtotime($eventObj->endtime));
                    $endDateObject->setTimezone($tz);
                    $event['endtime'] = $endDateObject->get('YYYY-MM-dd HH:mm:ss');
                }

                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj, true);
                $event["owner_image"] = array_merge($event["owner_image"], $getContentImages);

                $event["owner_title"] = $eventObj->getOwner()->getTitle();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj);
                $event = array_merge($event, $getContentImages);

                $isAllowedView = $eventObj->authorization()->isAllowed($viewer, 'view');
                $event["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $isAllowedEdit = $eventObj->authorization()->isAllowed($viewer, 'edit');
                $event["edit"] = empty($isAllowedEdit) ? 0 : 1;

                $isAllowedDelete = $eventObj->authorization()->isAllowed($viewer, 'delete');
                $event["delete"] = empty($isAllowedDelete) ? 0 : 1;

                $tempResponse[] = $event;
            }

            if (!empty($tempResponse))
                $response['response'] = $tempResponse;
        }

        if (!empty($siteapiEventBrowse))
            $this->respondWithSuccess($response, true);
    }

    public function manageAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

// RETURN IF NO SUBJECT AVAILABLE.
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $response = $values = array();

        $table = Engine_Api::_()->getDbtable('events', 'event');
        $tableName = $table->info('name');

        if ($this->getRequestParam('user_id', null))
            $subject = Engine_Api::_()->getItem('user', $this->getRequestParam('user_id'));

        $subject = !empty($subject) ? $subject : $viewer;
        $siteapiEventBrowse = Zend_Registry::isRegistered('siteapiEventBrowse') ? Zend_Registry::get('siteapiEventBrowse') : null;

// Only mine
        if (@$values['view'] == 2) {
            $select = $table->select()
                    ->where('user_id = ?', $subject->getIdentity());
        }
// All membership
        else {
            $membership = Engine_Api::_()->getDbtable('membership', 'event');
            $select = $membership->getMembershipsOfSelect($subject);
            $select->where('event_id IS NOT NULL');
        }

        if ($this->getRequestParam('search', null)) {
            $values['text'] = $this->getRequestParam('search');
        }

        if (empty($values['text']) && $this->getRequestParam('search_text', null)) {
            $values['text'] = $this->getRequestParam('search_text');
        }

        if (!empty($values['text'])) {
            $select->where(
                    $table->getAdapter()->quoteInto("`{$tableName}`.`title` LIKE ?", '%' . $values['text'] . '%') . ' OR ' .
                    $table->getAdapter()->quoteInto("`{$tableName}`.`description` LIKE ?", '%' . $values['text'] . '%')
            );
        }

        if ($this->getRequestParam('category_id', null)) {
            $select->where('category_id = ?', $this->getRequestParam('category_id'));
        }

        if ($this->getRequestParam('past', null)) {
            $select->where("`{$tableName}`.`endtime` <= FROM_UNIXTIME(?)", time());
        } elseif ($this->getRequestParam('future', null)) {
            $select->where("`{$tableName}`.`endtime` > FROM_UNIXTIME(?)", time());
        }

        // Order
        if ($this->getRequestParam('order', null)) {
            $requestOrder = $this->getRequestParam('order');
            $select->order($requestOrder);
        } else {
            $select->order('starttime');
        }

        $requestLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('event_page', 12);
        $requestPage = $this->getRequestParam("page", 1);
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($requestPage);
        $paginator->setItemCountPerPage($requestLimit);
        $response["getTotalItemCount"] = $getTotalItemCount = $paginator->getTotalItemCount();
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
        if (!empty($getTotalItemCount)) {
            foreach ($paginator as $eventObj) {
                $event = $eventObj->toArray();

                if (isset($viewer->timezone))
                    $tz = $viewer->timezone;

                if (isset($eventObj->starttime) && !empty($eventObj->starttime) && isset($tz)) {
                    $startDateObject = new Zend_Date(strtotime($eventObj->starttime));
                    $startDateObject->setTimezone($tz);
                    $event['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
                }
                if (isset($eventObj->endtime) && !empty($eventObj->endtime) && isset($tz)) {
                    $endDateObject = new Zend_Date(strtotime($eventObj->endtime));
                    $endDateObject->setTimezone($tz);
                    $event['endtime'] = $endDateObject->get('YYYY-MM-dd HH:mm:ss');
                }

                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj, true);
                $event = array_merge($event, $getContentImages);

                $event["owner_title"] = $eventObj->getOwner()->getTitle();

                // Add image
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj);
                $event = array_merge($event, $getContentImages);

                $isAllowedView = $eventObj->authorization()->isAllowed($viewer, 'view');
                $event["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $isAllowedEdit = $eventObj->authorization()->isAllowed($viewer, 'edit');
                $event["edit"] = empty($isAllowedEdit) ? 0 : 1;

                $isAllowedDelete = $eventObj->authorization()->isAllowed($viewer, 'delete');
                $event["delete"] = empty($isAllowedDelete) ? 0 : 1;


//        if ( !empty($params['manage']) ) {
                $tempMenu = array();
                if ($eventObj->isOwner($viewer)) {
                    $tempMenu[] = array(
                        'label' => $this->translate('Edit Event'),
                        'name' => 'edit',
                        'url' => 'events/edit/' . $eventObj->getIdentity(),
                        'urlParams' => array(
                        )
                    );

                    $tempMenu[] = array(
                        'label' => $this->translate('Delete Event'),
                        'name' => 'delete_event',
                        'url' => 'events/delete/' . $eventObj->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                } elseif (!$eventObj->membership()->isMember($viewer, null)) {
                    $tempMenu[] = array(
                        'label' => $this->translate('Join Event'),
                        'name' => 'join_event',
                        'url' => 'events/member/join/' . $eventObj->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                } else if ($eventObj->membership()->isMember($viewer, true)) {
                    $tempMenu[] = array(
                        'label' => $this->translate('Leave Event'),
                        'name' => 'leave_event',
                        'url' => 'events/member/leave/' . $eventObj->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }

                $event["menu"] = $tempMenu;
//        }

                $tempResponse[] = $event;
            }

            if (!empty($tempResponse))
                $response['response'] = $tempResponse;
        }

        if (!empty($siteapiEventBrowse))
            $this->respondWithSuccess($response);
    }

    public function createAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id)) {
            $this->respondWithError('unauthorized');
        } else {
            $level_id = $viewer->level_id;
        }

        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToCreate = $permissionsTable->getAllowed('event', $level_id, 'create');
        }

        if (empty($allowToCreate))
            $this->respondWithError('unauthorized');

        $parent_type = $this->getRequestParam('parent_type');
        $parent_id = $this->getRequestParam('parent_id', $this->getRequestParam('subject_id'));
        $siteapiEventCreate = Zend_Registry::isRegistered('siteapiEventCreate') ? Zend_Registry::get('siteapiEventCreate') : null;

        if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
            $group = Engine_Api::_()->getItem('group', $parent_id);
            $isEventAllowed = $group->authorization()->isAllowed($viewer, 'event');
            if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'event')->isValid())
                $this->respondWithError('unauthorized');
        } else {
            $parent_type = 'user';
            $parent_id = $viewer->getIdentity();
        }

        /* RETURN THE EVENT CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'event')->getForm(null, $parent_type, $parent_id));
        } else if (!empty($siteapiEventCreate) && $this->getRequest()->isPost()) {
            /* CREATE THE EVENT IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */
            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('event', $viewer->level_id, 'flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("events",'event');
                    $select = $tableFlood->select()->where("user_id = ?",$viewer->getIdentity())->order("creation_date DESC");
                    if($itemFlood[1] == "minute"){
                        $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
                    }else if($itemFlood[1] == "day"){
                        $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 DAY)");
                    }else{
                        $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 HOUR)");
                    }
                    $floodItem = $tableFlood->fetchAll($select);
                    if(count($floodItem) && $itemFlood[0] <= count($floodItem)){
                        $type = $itemFlood[1];
                        $time =  "1 ".$type;
                        $message = 'You have reached maximum limit of posting in '.$time.'. Try again after this duration expires.';
                        $this->respondWithError('unauthorized', $message);
                    }
                }
            }

            // CONVERT POST DATA INTO THE ARRAY.
            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'event')->getForm(null, $parent_type, $parent_id);
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $viewer = Engine_Api::_()->user()->getViewer();
            $data = $values = @array_merge($values, array(
                        'user_id' => $viewer->getIdentity(),
                        'parent_type' => $parent_type,
                        'parent_id' => $parent_id
            ));

            if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group') && empty($values['host'])) {
                $values['host'] = $group->getTitle();
            }

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'event')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Convert times
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $start = strtotime($values['starttime']);
            $end = strtotime($values['endtime']);
            date_default_timezone_set($oldTz);
            $values['starttime'] = date('Y-m-d H:i:s', $start);
            $values['endtime'] = date('Y-m-d H:i:s', $end);


            // Privacy
            $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_view');
            $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_comment');
            $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_photo');

            if ($parent_type == 'user') {
                $availableLabels = array(
                    'everyone' => $this->translate('Everyone'),
                    'registered' => $this->translate('All Registered Members'),
                    'owner_network' => $this->translate('Friends and Networks'),
                    'owner_member_member' => $this->translate('Friends of Friends'),
                    'owner_member' => $this->translate('Friends Only'),
                    'member' => $this->translate('Event Guests Only'),
                    'owner' => $this->translate('Just Me')
                );
                $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
                $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
                $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
            } else if ($parent_type == 'group') {

                $availableLabels = array(
                    'everyone' => $this->translate('Everyone'),
                    'registered' => $this->translate('All Registered Members'),
                    'parent_member' => $this->translate('Group Members'),
                    'member' => $this->translate('Event Guests Only'),
                    'owner' => $this->translate('Just Me'),
                );
                $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
                $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
                $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
            }

            if (!empty($viewOptions) && count($viewOptions) == 1)
                $values['auth_view'] = key($viewOptions);

            if (!empty($commentOptions) && count($commentOptions) == 1)
                $values['auth_comment'] = key($commentOptions);

            if (!empty($photoOptions) && count($photoOptions) == 1)
                $values['auth_photo'] = key($photoOptions);


            $db = Engine_Api::_()->getDbtable('events', 'event')->getAdapter();
            $db->beginTransaction();

            try {
                // Create event
                $table = Engine_Api::_()->getDbtable('events', 'event');
                $event = $table->createRow();

                $event->setFromArray($values);
                $event->save();

                // Add owner as member
                $event->membership()->addMember($viewer)
                        ->setUserApproved($viewer)
                        ->setResourceApproved($viewer);

                // Add owner rsvp
                $event->membership()
                        ->getMemberInfo($viewer)
                        ->setFromArray(array('rsvp' => 2))
                        ->save();

                // SET PHOTO
                if (!empty($_FILES['photo']))
                    Engine_Api::_()->getApi('Siteapi_Core', 'event')->setPhoto($_FILES['photo'], $event);

                // Set auth
                $auth = Engine_Api::_()->authorization()->context;

                if ($values['parent_type'] == 'group') {
                    $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'everyone';
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);
                $photoMax = array_search($values['auth_photo'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($event, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
                    $auth->setAllowed($event, $role, 'photo', ($i <= $photoMax));
                }

                $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

                // Add an entry for member_requested
                $auth->setAllowed($event, 'member_requested', 'view', 1);

                // Add action
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

                $action = $activityApi->addActivity($viewer, $event, 'event_create');

                if ($action) {
                    $activityApi->attachActivity($action, $event);
                }
                // Commit
                $db->commit();

                // Change request method POST to GET
                $this->setRequestMethod();

                $this->_forward('view', 'index', 'event', array(
                    'event_id' => $event->getIdentity()
                ));
                return;
            } catch (Engine_Image_Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Getting the "Gutter Menus" array.
     * 
     * @return array
     */
    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();

        // EDIT EVENT DETAILS
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'label' => $this->translate('Edit Event Details'),
                'name' => 'edit',
                'url' => 'events/edit/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        // EVENT PROFILE MEMBERS
        if ($viewer->getIdentity()) {
            $row = $subject->membership()->getRow($viewer);

            // Not yet associated at all
            if (null === $row) {
                if ($subject->membership()->isResourceApprovalRequired()) {
                    $menus[] = array(
                        'label' => $this->translate('Request Invite'),
                        'name' => 'request_member',
                        'url' => 'events/member/request/' . $subject->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                } else {
                    $menus[] = array(
                        'label' => $this->translate('Join Event'),
                        'name' => 'join',
                        'url' => 'events/member/join/' . $subject->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }
            }

            // Full member
            // @todo consider owner
            else if ($row->active) {
                if (!$subject->isOwner($viewer)) {
                    $menus[] = array(
                        'label' => $this->translate('Leave Event'),
                        'name' => 'leave',
                        'url' => 'events/member/leave/' . $subject->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }
            } else if (!$row->resource_approved && $row->user_approved) {
                $menus[] = array(
                    'label' => $this->translate('Cancel Invite Request'),
                    'name' => 'cancel_request',
                    'url' => 'events/member/cancel/' . $subject->getIdentity(),
                    'urlParams' => array(
                    )
                );
            } else if (!$row->user_approved && $row->resource_approved) {
                $menus[] = array(
                    'label' => $this->translate('Accept Event Invite'),
                    'name' => 'accept_request',
                    'url' => 'events/member/accept/' . $subject->getIdentity(),
                    'urlParams' => array(
                    )
                );

                $menus[] = array(
                    'label' => $this->translate('Ignore Event Invite'),
                    'name' => 'reject_request',
                    'url' => 'events/member/reject/' . $subject->getIdentity(),
                    'urlParams' => array(
                    )
                );
            }
        }


        // INVITE EVENT
        if ($subject->authorization()->isAllowed($viewer, 'invite')) {
            $menus[] = array(
                'label' => $this->translate('Invite Guests'),
                'name' => 'invite',
                'url' => 'events/member/invite/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        // SHARE EVENT
        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Share This Event'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        // MESSAGE TO MEMBERS [TODO LATER]
        if ($viewer->getIdentity() && $subject->isOwner($viewer)) {
//      $menus[] = array(
//          'label' => 'Message Members',
//          'name' => 'message_members',
//          'url' => 'message/index/members',
//          'urlParams' => array(
//              "type" => $subject->getType(),
//              "id" => $subject->getIdentity()
//          )
//      );
        }

        // DELETE EVENT
        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'label' => $this->translate('Delete Event'),
                'name' => 'delete',
                'url' => 'events/delete/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        return $menus;
    }

    /**
     * Getting the event profile page rsvp form array.
     * 
     * @return array
     */
    private function _getProfileRSVP() {
        $rsvpForm = array();
        $rsvpForm[] = array(
            'type' => 'Radio',
            'name' => 'rsvp',
            'multiOptions' => array(
                2 => $this->translate('Attending'),
                1 => $this->translate('Maybe Attending'),
                0 => $this->translate('Not Attending'),
            //3 => 'Awaiting Reply',
            )
        );

        $rsvpForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $this->translate('Send Invites')
        );

        return $rsvpForm;
    }

    /**
     * Get the list of container tabs.
     * 
     * @return array
     */
    private function _profileTAbsContainer($subject) {
        $response[] = array(
            'label' => $this->translate('Updates'),
            'name' => 'update',
            'totalItemCount' => 0
        );

        $response[] = array(
            'label' => $this->translate('Guests'),
            'name' => 'members',
            'totalItemCount' => $subject->member_count,
            'url' => 'events/member/list/' . $subject->getIdentity(),
            'urlParams' => array(
            )
        );

        $response[] = array(
            'label' => $this->translate('Photos'),
            'name' => 'photos',
            'totalItemCount' => $subject->getSingletonAlbum()->getCollectiblesPaginator()->getTotalItemCount(),
            'url' => 'events/photos/lists',
            'urlParams' => array(
                "event_id" => $subject->getIdentity()
            )
        );

//    $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("event");
//    if (!empty($isModEnabled)) {
//      $response[] = array(
//          'label' => 'Events',
//          'name' => 'events',
//          'body' => array(
//          ),
//          'totalItemCount' => $subject->getEventsPaginator()->getTotalItemCount()
//      );
//    }
//    // Get paginator
//    $table = Engine_Api::_()->getItemTable('group_topic');
//    $select = $table->select()
//            ->where('group_id = ?', $subject->getIdentity())
//            ->order('sticky DESC')
//            ->order('modified_date DESC');
//
//    $paginator = Zend_Paginator::factory($select);
//
//    $response[] = array(
//        'label' => 'Discussions',
//        'name' => 'discussion',
//        'body' => array(
//        ),
//        'totalItemCount' => $paginator->getTotalItemCount()
//    );

        return $response;
    }

}
