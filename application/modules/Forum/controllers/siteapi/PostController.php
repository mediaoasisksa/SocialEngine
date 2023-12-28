<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PostController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Forum_PostController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (0 !== ($post_id = (int) $this->getRequestParam('post_id')) &&
                null !== ($post = Engine_Api::_()->getItem('forum_post', $post_id)) &&
                $post instanceof Forum_Model_Post) {
            Engine_Api::_()->core()->setSubject($post);
        }
    }

    /**
     * Delete forum post
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
            $post = Engine_Api::_()->core()->getSubject('forum_post');

        // Return, if there are no subject set.
        if (empty($post))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $post = Engine_Api::_()->core()->getSubject('forum_post');
        $topic = $post->getParent();
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($post, null, 'delete')->checkRequire() &&
                !$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.delete')->checkRequire())
            $this->respondWithError('unauthorized');

        // Process
        $table = Engine_Api::_()->getItemTable('forum_post');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $post->delete();
            $db->commit();

            $this->successResponseNoContent('no_content', 'forum_index_index');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Edit forum post
     * 
     * @return array
     */
    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $post = Engine_Api::_()->core()->getSubject('forum_post');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($post))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $post = Engine_Api::_()->core()->getSubject('forum_post');
        $topic = $post->getParent();
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($post, null, 'edit')->checkRequire() &&
                !$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->checkRequire())
            $this->respondWithError('unauthorized');

        // CHECK FORUM FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE FORUM EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            $response = array();
            $response['formValues'] = $post->toArray();
            $response['form'][] = array(
                'type' => 'Textarea',
                'name' => 'body',
                'label' => $this->translate('Body')
            );

            if (!empty($post->file_id)) {
                $response['form'][] = array(
                    'type' => 'Checkbox',
                    'name' => 'photo_delete',
                    'label' => $this->translate('This post has a photo attached. Do you want to delete it?')
                );
            } else {
                $response['form'][] = array(
                    'type' => 'File',
                    'name' => 'photo',
                    'label' => $this->translate('Attach a New Photo (optional)')
                );
            }

            $response['form'][] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Post Reply')
            );

            $this->respondWithSuccess($response);
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* UPDATE THE FORUM INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO THE ARRAY.
            $values = array();
            $getFormKeys = array("photo_delete", "body");
            foreach ($getFormKeys as $element) {
                if (isset($_REQUEST[$element]))
                    $values[$element] = $_REQUEST[$element];
            }

            // Process
            $table = Engine_Api::_()->getItemTable('forum_post');
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                $post->body = $values['body'];
                $post->edit_id = $viewer->getIdentity();

                //DELETE photo here.
                if (!empty($values['photo_delete']) && $values['photo_delete']) {
                    $post->deletePhoto();
                }

                if (!empty($_FILES['photo']))
                    Engine_Api::_()->getApi('Siteapi_Core', 'forum')->setPhoto($_FILES['photo'], $post);

                $post->save();
                $db->commit();

                $this->successResponseNoContent('no_content', 'forum_index_index');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

}
