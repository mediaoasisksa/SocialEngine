<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    ForumController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Forum_ForumController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (0 !== ($forum_id = (int) $this->getRequestParam('forum_id')) &&
                null !== ($forum = Engine_Api::_()->getItem('forum_forum', $forum_id))) {
            Engine_Api::_()->core()->setSubject($forum);
        } else if (0 !== ($category_id = (int) $this->getRequestParam('category_id')) &&
                null !== ($category = Engine_Api::_()->getItem('forum_category', $category_id))) {
            Engine_Api::_()->core()->setSubject($category);
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
    }

    /**
     * Get the forum view.
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        if (Engine_Api::_()->core()->hasSubject())
            $forum = Engine_Api::_()->core()->getSubject();

        if (empty($forum))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth->setAuthParams($forum, null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        $response = array();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_()->user()->getViewer();

        // Increment view count
        $forum->view_count = new Zend_Db_Expr('view_count + 1');
        $forum->save();

        $response['can_post'] = $can_post = $forum->authorization()->isAllowed(null, 'topic.create');

        // Get params
        switch ($this->getRequestParam('sort', 'recent')) {
            case 'popular':
                $order = 'view_count';
                break;
            case 'recent':
            default:
                $order = 'modified_date';
                break;
        }

        // Make paginator
        $table = Engine_Api::_()->getItemTable('forum_topic');
        $select = $table->select()
                ->where('forum_id = ?', $forum->getIdentity())
                ->order('sticky DESC')
                ->order($order . ' DESC');
        ;

        if ($this->getRequestParam('search', false)) {
            $select->where('title LIKE ? OR description LIKE ?', '%' . $this->getRequestParam('search') . '%');
        }

        $requestLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_forum_pagelength', 25);
        $requestPage = $this->getRequestParam("page", 1);

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($requestPage);
        $paginator->setItemCountPerPage($requestLimit);
        $response['totalItemCount'] = $paginator->getTotalItemCount();
        foreach ($paginator as $topic) {
            $tempTopic = $topic->toArray();
            $last_post = $topic->getLastCreatedPost();
            if ($last_post) {
                $last_user = Engine_Api::_()->getItem('user', $last_post->user_id);
            } else {
                $last_user = Engine_Api::_()->getItem('user', $topic->user_id);
            }
            if(empty($last_user) ||!$last_user->getIdentity()){
                $response['totalItemCount']--;
                continue;
            }
            $tempTopic['slug'] = $topic->getSlug();
            $tempTopic['post_count'] = $tempTopic['post_count'] - 1;
            $tempTopic['last_posted_by'] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($last_user);

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($last_user);
            $tempTopic['last_posted_by'] = array_merge($tempTopic['last_posted_by'], $getContentImages);

            //Member verification Work...............
            $tempTopic['last_posted_by']['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($last_user);
 
            $isAllowedView = $topic->authorization()->isAllowed($viewer, 'view');
            $tempTopic["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
            $response['response'][] = $tempTopic;
        }

        $this->respondWithSuccess($response);
    }

    /**
     * Create forum topics
     * 
     * @return array
     */
    public function topicCreateAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $forum = Engine_Api::_()->core()->getSubject();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();


        if (empty($forum))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.create')->isValid())
            $this->respondWithError('unauthorized');


        /* RETURN THE FORUM CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'forum')->getForm());
        } else if ($this->getRequest()->isPost()) {
            /* CREATE THE FORUM IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */
            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('forum', $viewer->level_id, 'topic.flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("topics",'forum');
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
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'forum')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $values['user_id'] = $viewer->getIdentity();
            $values['forum_id'] = $forum->getIdentity();

            $data = $values;
            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'forum')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $topicTable = Engine_Api::_()->getDbtable('topics', 'forum');
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
            $postTable = Engine_Api::_()->getDbtable('posts', 'forum');

            $db = $topicTable->getAdapter();
            $db->beginTransaction();

            try {
                // Create topic
                $topic = $topicTable->createRow();
                $topic->setFromArray($values);
                $topic->title = htmlspecialchars($values['title']);
                $topic->description = $values['body'];
                $topic->save();

                // Create post
                $values['topic_id'] = $topic->getIdentity();

                $post = $postTable->createRow();
                $post->setFromArray($values);
                $post->save();

                // Set Photo
                if (!empty($_FILES['photo']))
                    Engine_Api::_()->getApi('Siteapi_Core', 'forum')->setPhoto($_FILES['photo'], $post);

                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($topic, 'registered', 'create', true);

                // Create topic watch
                $topicWatchesTable->insert(array(
                    'resource_id' => $forum->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $values['watch'],
                ));

                // Add activity
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($viewer, $topic, 'forum_topic_create');
                if ($action) {
                    $action->attach($topic);
                }

                $db->commit();

                // Change request method POST to GET
                $this->setRequestMethod();

                $this->successResponseNoContent('no_content');
//        $this->_forward('view' ,'topic', 'forum', array(
//          'topic_id' => $topic->getIdentity()
//        ));
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $errorMessage);
            }
        }
    }

}
