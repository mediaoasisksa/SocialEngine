<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PollController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Poll_PollController extends Siteapi_Controller_Action_Standard {

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
            $this->_forward('throw-error', 'poll', 'poll', array(
                "error_code" => "unauthorized"
            ));
            return;
        }

        $siteapiPollAPIEnabled = Zend_Registry::isRegistered('siteapiPollAPIEnabled') ? Zend_Registry::get('siteapiPollAPIEnabled') : null;
        if (empty($siteapiPollAPIEnabled)) {
            $this->_forward('throw-error', 'poll', 'poll', array(
                "error_code" => "unauthorized"
            ));
            return;
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

    public function closeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $poll = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($poll))
            $this->respondWithError('no_record');

        $table = $poll->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $poll->closed = (bool) $this->getRequestParam('closed', 0);
            $poll->save();
            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $poll = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($poll))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams($poll, null, 'delete')->isValid())
            $this->respondWithError('unauthorized');

        $db = $poll->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $poll->delete();
            $db->commit();

            $this->successResponseNoContent('no_content', 'siteapi_poll');
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
            $poll = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($poll))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();

        // Prepare privacy
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        /* RETURN THE POLL CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            // IF THERE ARE NO FORM POST YET THEN RETURN THE VIDEO FORM.
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'poll')->getForm($poll);
            $formValues = $poll->toArray();

            foreach ($roles as $role) {
                if ($auth->isAllowed($poll, $role, 'view'))
                    $formValues['auth_view'] = $role;

                if ($auth->isAllowed($poll, $role, 'comment'))
                    $formValues['auth_comment'] = $role;
            }

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));

            return;
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* CREATE THE POLL IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO AN ARRAY.      
            $data = $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'poll')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values;

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'poll')->getFormValidators($poll);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Process
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                // CREATE AUTH STUFF HERE
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

                if (isset($values['search']))
                    $poll->search = (bool) $values['search'];

                $poll->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($poll) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }

        $this->successResponseNoContent('no_content', 'siteapi_poll');
    }

    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        if (Engine_Api::_()->core()->hasSubject())
            $poll = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($poll))
            $this->respondWithError('no_record');

        $bodyParams = array();
        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($poll);

        $bodyParams['response'] = $poll->toArray();

        //Content URL
        $contentURL = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($poll);

        if (!empty($contentURL))
            $bodyParams['response'] = array_merge($bodyParams['response'], $contentURL);

        // Add owner image
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poll, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

        $bodyParams['response']["owner_title"] = $poll->getOwner()->getTitle();

        // Get Poll Options with Vote Percentage.
        if ($poll->getOptions()) {
            $options = $poll->getOptions()->toArray();
            foreach ($options as $key => $option) {
                $options[$key]['percentage'] = $poll->vote_count ? floor(100 * ($option['votes'] / $poll->vote_count)) : 0;
            }

            $bodyParams['response']['options'] = $options;
        }

        $poll_option_id =$poll->viewerVoted();
        
        $bodyParams['response']['hasVoted'] = !empty($poll_option_id)?true:false;
        $bodyParams['response']['showPieChart'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.showpiechart', false);
        $bodyParams['response']['canVote'] = $poll->authorization()->isAllowed(null, 'vote');
        $bodyParams['response']['canChangeVote'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);

        // Getting viewer like or not to content.
        $bodyParams['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($poll);

        // Getting like count.
        $bodyParams['response']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($poll);

        $owner = $poll->getOwner();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$owner->isSelf($viewer)) {
            $poll->view_count++;
            $poll->save();
        }

        $this->respondWithSuccess($bodyParams);
    }

    public function voteAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $option_id = $this->getRequestParam('option_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($option_id) || empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $poll = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($poll))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'vote')->isValid())
            $this->respondWithError('unauthorized');

        $canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);

        $viewer = Engine_Api::_()->user()->getViewer();

        if ($poll->closed)
            $this->respondWithError('poll_closed');

        if ($poll->hasVoted($viewer) && !$canChangeVote)
            $this->respondWithError('already_voted');

        $db = Engine_Api::_()->getDbtable('polls', 'poll')->getAdapter();
        $db->beginTransaction();
        try {
            $poll->vote($viewer, $option_id);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        // Get Poll Options with Vote Percentage.
        if ($poll->getOptions()) {
            $options = $poll->getOptions()->toArray();
            foreach ($options as $key => $option) {
                $options[$key]['percentage'] = $poll->vote_count ? floor(100 * ($option['votes'] / $poll->vote_count)) : 0;
            }
        }

        $this->respondWithSuccess($options);
    }

    // GUTTER MENUS SHOW ON THE POLL PROFILE PAGE.
    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();

        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        if ($viewer->getIdentity() && ($viewer->getIdentity() != $owner->getIdentity())) {
            $menus[] = array(
                'label' => $this->translate('Report'),
                'name' => 'report',
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        return $menus;
    }

}
