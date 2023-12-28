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
class Poll_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        // Get subject
        $poll = null;
        if (null !== ($pollIdentity = $this->getRequestParam('poll_id'))) {
            $poll = Engine_Api::_()->getItem('poll', $pollIdentity);
            if (null !== $poll) {
                Engine_Api::_()->core()->setSubject($poll);
            }
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // only show polls if authorized
        $resource = ( $poll ? $poll : 'poll' );
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer = ( $viewer && $viewer->getIdentity() ? $viewer : null );
        if (!$this->_helper->requireAuth()->setAuthParams($resource, $viewer, 'view')->isValid()) {
           $this->respondWithError('unauthorized');
        }

        $siteapiPollAPIEnabled = Zend_Registry::isRegistered('siteapiPollAPIEnabled') ? Zend_Registry::get('siteapiPollAPIEnabled') : null;
        if (empty($siteapiPollAPIEnabled)) {
           $this->respondWithError('unauthorized');
        }
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'poll')->getBrowseSearchForm(), true);
    }

    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('poll', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $response = array();
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('poll', null, 'create');

        // Process form
        $values = array();
        $values = $this->getRequestAllParams;
        $values['browse'] = 1;

        if (@$values['show'] == 2 && $viewer->getIdentity()) {
            // Get an array of friend ids
            $values['users'] = $viewer->membership()->getMembershipsOfIds();
        }
        unset($values['show']);

        $user_id = $this->getRequestParam('user_id', null);
        if (!empty($user_id))
            $values['user_id'] = $user_id;

        $closed = $this->getRequestParam('closed', 0);
        if (isset($closed))
            $values['closed'] = $closed;

         if (!empty($user_id)) {
            unset($values['browse']);
        }
        
        // Make paginator
        $currentPageNumber = $this->getRequestParam('page', 1);
        $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perpage', 10);

        $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator($values);
        $paginator
                ->setItemCountPerPage($itemCountPerPage)
                ->setCurrentPageNumber($currentPageNumber);

        $response['totalItemCount'] = $paginator->getTotalItemCount();

        foreach ($paginator as $poll) {
            $tempPoll = $poll->toArray();

            // Add owner image
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poll, true);
            $tempPoll = array_merge($tempPoll, $getContentImages);

            $tempPoll["owner_title"] = $poll->getOwner()->getTitle();
            //Member verification Work...............
            $tempPoll['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($poll->getOwner());

            $isAllowedView = $poll->authorization()->isAllowed($viewer, 'view');
            $tempPoll["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

            $response['response'][] = $tempPoll;
        }

        $this->respondWithSuccess($response, true);
    }

    public function manageAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams('poll', null, 'create')->isValid())
            $this->respondWithError('unauthorized');

        $values = array();
        $values = $this->getRequestAllParams;

        $values['user_id'] = $this->getRequestParam('user_id', $viewer_id);
        $closed = $this->getRequestParam('closed', 0);
        if (isset($closed))
            $values['closed'] = $closed;

        // Make paginator
        $currentPageNumber = $this->getRequestParam('page', 1);
        $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perpage', 10);

        $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator($values);
        $paginator
                ->setItemCountPerPage($itemCountPerPage)
                ->setCurrentPageNumber($currentPageNumber)
        ;

        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('poll', null, 'create')->checkRequire();
        $response['totalItemCount'] = $paginator->getTotalItemCount();

        foreach ($paginator as $poll) {
            $tempPoll = $poll->toArray();

            // Add owner image
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poll, true);
            $tempPoll = array_merge($tempPoll, $getContentImages);

            $tempPoll["owner_title"] = $poll->getOwner()->getTitle();

            $isAllowedView = $poll->authorization()->isAllowed($viewer, 'view');
            $tempPoll["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

            //Member verification Work...............
            $tempPoll['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($poll->getOwner());

            $tempPoll['menu'][] = array(
                'label' => $this->translate('Edit Privacy'),
                'name' => 'edit_privacy',
                'url' => 'polls/edit/' . $poll->getIdentity(),
                'urlParams' => array(
                )
            );

            if (!$poll->closed) {
                $tempPoll['menu'][] = array(
                    'label' => $this->translate('Close Poll'),
                    'name' => 'close_poll',
                    'url' => 'polls/close/' . $poll->getIdentity(),
                    'urlParams' => array(
                        'closed' => 1
                    )
                );
            } else {
                $tempPoll['menu'][] = array(
                    'label' => $this->translate('Open Poll'),
                    'name' => 'open_poll',
                    'url' => 'polls/close/' . $poll->getIdentity(),
                    'urlParams' => array(
                        'closed' => 0
                    )
                );
            }

            $tempPoll['menu'][] = array(
                'label' => $this->translate('Delete Poll'),
                'name' => 'delete_poll',
                'url' => 'polls/delete/' . $poll->getIdentity(),
                'urlParams' => array(
                )
            );


            $response['response'][] = $tempPoll;
        }

        $this->respondWithSuccess($response);
    }

    public function createAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams('poll', null, 'create')->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();

        /* RETURN THE POLL CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'poll')->getForm());
        } else if ($this->getRequest()->isPost()) {
            /* CREATE THE POLL IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('poll', $viewer->level_id, 'flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("polls",'poll');
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

            // CONVERT POST DATA INTO AN ARRAY.
            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'poll')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values = @array_merge($values, array(
                        'user_id' => $viewer_id
            ));

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'poll')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $max_options = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.maxoptions', 15);
            for ($option = 1; $option <= $max_options; $option++) {
                if (isset($values['options_' . $option])) {
                    if (!empty($values['options_' . $option])) {
                        $optionsArray[$option] = $values['options_' . $option];
                    }
                    unset($values['options_' . $option]);
                }
            }

            if (COUNT($optionsArray) < 2)
                $this->respondWithError('answers_not_possible');

            // Process
            $pollTable = Engine_Api::_()->getItemTable('poll');
            $pollOptionsTable = Engine_Api::_()->getDbtable('options', 'poll');
            $db = $pollTable->getAdapter();
            $db->beginTransaction();

            try {
                // Create poll
                $poll = $pollTable->createRow();
                $poll->setFromArray($values);
                $poll->save();

                // Create options
                $censor = new Engine_Filter_Censor();
                $html = new Engine_Filter_Html(array('AllowedTags' => array('a')));
                foreach ($optionsArray as $option) {
                    $option = $censor->filter($html->filter($option));
                    $pollOptionsTable->insert(array(
                        'poll_id' => $poll->getIdentity(),
                        'poll_option' => $option,
                    ));
                }

                // Privacy
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = array('everyone');
                }
                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = array('everyone');
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($poll, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($poll, $role, 'comment', ($i <= $commentMax));
                }

                $auth->setAllowed($poll, 'registered', 'vote', true);

                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            // Process activity
            $db = Engine_Api::_()->getDbTable('polls', 'poll')->getAdapter();
            $db->beginTransaction();
            try {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(Engine_Api::_()->user()->getViewer(), $poll, 'poll_new');
                if ($action) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $poll);
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            // Change request method POST to GET
            $this->setRequestMethod();

            $this->_forward('view', 'poll', 'poll', array(
                'poll_id' => $poll->getIdentity()
            ));
        }
    }

}
