<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    BlockController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_BlockController extends Siteapi_Controller_Action_Standard {

    /**
     * Block the user
     * 
     * @return array
     */
    public function addAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get id of friend to add
        if (($user_id = $this->getRequestParam('user_id', null)) && empty($user_id)) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        if (!$this->getRequest()->isPost())
            $this->respondWithError('invalid_method');

        // Process
        $db = Engine_Api::_()->getDbtable('block', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $user = Engine_Api::_()->getItem('user', $user_id);

            $viewer->addBlock($user);
            if ($user->membership()->isMember($viewer, null)) {
                $user->membership()->removeMember($viewer);
            }

            try {
                // Set the requests as handled
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                        ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = 1;
                    $notification->save();
                }
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                        ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = 1;
                    $notification->save();
                }
            } catch (Exception $e) {
                
            }

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Unblock the block user
     * 
     * @return array
     */
    public function removeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get id of friend to add
        if (($user_id = $this->getRequestParam('user_id', null)) && empty($user_id)) {
            $this->respondWithValidationError('parameter_missing', 'user_id');
        }

        if (!$this->getRequest()->isPost())
            $this->respondWithError('invalid_method');

        // Process
        $db = Engine_Api::_()->getDbtable('block', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $user = Engine_Api::_()->getItem('user', $user_id);
            $viewer->removeBlock($user);

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

}
