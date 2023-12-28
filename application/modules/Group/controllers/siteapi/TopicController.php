<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    TopicController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Group_TopicController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (0 !== ($topic_id = (int) $this->getRequestParam('topic_id')) &&
                null !== ($topic = Engine_Api::_()->getItem('group_topic', $topic_id))) {
            Engine_Api::_()->core()->setSubject($topic);
        } else if (0 !== ($group_id = (int) $this->getRequestParam('group_id')) &&
                null !== ($group = Engine_Api::_()->getItem('group', $group_id))) {
            Engine_Api::_()->core()->setSubject($group);
        }
    }

    /**
     * Show discussion on group profile page.
     * 
     * @return array
     */
    public function indexAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get subject and check auth
        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject('group');
            if (!$subject->authorization()->isAllowed($viewer, 'view'))
                $this->respondWithError('unauthorized');
        }else {
            $this->respondWithError('no_record');
        }

        $limit = $this->getRequestParam('limit', 10);
        $page = $this->getRequestParam('page', 1);

        // Get paginator
        $table = Engine_Api::_()->getItemTable('group_topic');
        $select = $table->select()
                ->where('group_id = ?', Engine_Api::_()->core()->getSubject()->getIdentity())
                ->order('sticky DESC')
                ->order('modified_date DESC');
        ;
        $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);


        foreach ($paginator as $dicussion) {
            $response['response'][] = $dicussion->toArray();
        }

        $response['canPost'] = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment');
        $response['getTotalItemCount'] = $paginator->getTotalItemCount();

        $this->respondWithSuccess($response, true);
    }

    /**
     * Create new topic
     * 
     * @return array
     */
    public function createAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireSubject('group')->isValid())
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid())
            $this->respondWithError('unauthorized');

        $group = Engine_Api::_()->core()->getSubject('group');
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($this->getRequest()->isGet()) {
            $response = $tempResponse = array();

            $tempResponse[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => $this->translate('Title')
            );

            $tempResponse[] = array(
                'type' => 'Textarea',
                'name' => 'body',
                'label' => $this->translate('Body')
            );

            $tempResponse[] = array(
                'type' => 'Checkbox',
                'name' => 'watch',
                'label' => $this->translate('Send me notifications when other members reply to this topic.')
            );

            $tempResponse[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Post Reply')
            );

            $this->respondWithSuccess($tempResponse);
        } else if ($this->getRequest()->isPost()) {
            $values = array();
            $getFormKeys = array("title", "body", "watch");
            foreach ($getFormKeys as $element) {
                if (isset($_REQUEST[$element]))
                    $values[$element] = $_REQUEST[$element];
            }

            if (isset($values['watch']) && empty($values['watch']))
                unset($values['watch']);

            $values['user_id'] = $viewer->getIdentity();
            $values['group_id'] = $group->getIdentity();

            $topicTable = Engine_Api::_()->getDbtable('topics', 'group');
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
            $postTable = Engine_Api::_()->getDbtable('posts', 'group');

            $db = $group->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                // Create topic
                $topic = $topicTable->createRow();
                $topic->setFromArray($values);
                $topic->save();

                // Create post
                $values['topic_id'] = $topic->topic_id;

                $post = $postTable->createRow();
                $post->setFromArray($values);
                $post->save();

                // Create topic watch
                $topicWatchesTable->insert(array(
                    'resource_id' => $group->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $values['watch'],
                ));

                // Add activity
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($viewer, $group, 'group_topic_create', null, array('child_id' => $topic->getIdentity()));
                if ($action) {
                    $action->attach($topic, Activity_Model_Action::ATTACH_DESCRIPTION);
                }

                $db->commit();
                $this->successResponseNoContent('created', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Profile page of group discussion.
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        // Settings
        $settings = Engine_Api::_()->getApi('settings', 'core');

//    $response = array();
        $group = $topic->getParentGroup();

        $canEdit = false;
        $canDelete = false;
        if (Engine_Api::_()->authorization()->isAllowed($group, null, 'topic.edit')) {
            $canEdit = true;
        }
        if (Engine_Api::_()->authorization()->isAllowed($group, null, 'topic.delete')) {
            $canDelete = true;
        }

//    $response['canEdit'] = $topic->canEdit(Engine_Api::_()->user()->getViewer());
//    $response['officerList'] = $group->getOfficerList();
//
//    $canPost = $group->authorization()->isAllowed($viewer, 'comment');
        // Views
        if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
            $topic->view_count = new Zend_Db_Expr('view_count + 1');
            $topic->save();
        }

        $bodyParams = array();
        $bodyParams['canPost'] = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment');

        // Check watching
        $isWatching = null;
        if ($viewer->getIdentity()) {
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
            $isWatching = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $group->getIdentity())
                    ->where('topic_id = ?', $topic->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0)
            ;
            if (false === $isWatching) {
                $isWatching = null;
            } else {
                $isWatching = (bool) $isWatching;
            }
        }


        if ($this->getRequestParam('gutter_menu', 1)) {
            if ($viewer->getIdentity()) {
                if (!$isWatching) {
                    $bodyParams['gutterMenu'][] = array(
                        'label' => $this->translate('Watch Topic'),
                        'name' => 'watch_topic',
                        'url' => 'groups/topic/watch/topic_id/' . $topic->getIdentity() . '/watch/1',
                        'urlParams' => array(
                        )
                    );
                } else {
                    $bodyParams['gutterMenu'][] = array(
                        'label' => $this->translate('Stop Watching Topic'),
                        'name' => 'stop_watch_topic',
                        'url' => 'groups/topic/watch/topic_id/' . $topic->getIdentity() . '/watch/0',
                        'urlParams' => array(
                        )
                    );
                }

                if (!empty($canEdit)) {
                    if (!$topic->sticky) {
                        $bodyParams['gutterMenu'][] = array(
                            'label' => $this->translate('Make Sticky'),
                            'name' => 'make_sticky',
                            'url' => 'groups/topic/sticky/topic_id/' . $topic->getIdentity() . '/sticky/' . 1,
                            'urlParams' => array(
                            )
                        );
                    } else {
                        $bodyParams['gutterMenu'][] = array(
                            'label' => $this->translate('Remove Sticky'),
                            'name' => 'remove_sticky',
                            'url' => 'groups/topic/sticky/topic_id/' . $topic->getIdentity() . '/sticky/' . 0,
                            'urlParams' => array(
                            )
                        );
                    }

                    if (!$topic->closed) {
                        $bodyParams['gutterMenu'][] = array(
                            'label' => $this->translate('Close'),
                            'name' => 'close',
                            'url' => 'groups/topic/close/topic_id/' . $topic->getIdentity() . '/closed/' . 1,
                            'urlParams' => array(
                            )
                        );
                    } else {
                        $bodyParams['gutterMenu'][] = array(
                            'label' => $this->translate('Open'),
                            'name' => 'open',
                            'url' => 'groups/topic/close/topic_id/' . $topic->getIdentity() . '/closed/' . 0,
                            'urlParams' => array(
                            )
                        );
                    }

                    $bodyParams['gutterMenu'][] = array(
                        'label' => $this->translate('Rename'),
                        'name' => 'rename',
                        'url' => 'groups/topic/rename/topic_id/' . $topic->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }

                if (!empty($canDelete)) {
                    $bodyParams['gutterMenu'][] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'groups/topic/delete/topic_id/' . $topic->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }
            }
        }

        // ADD GUTTER MENU
        // @todo implement scan to post
        $post_id = (int) $this->getRequestParam('post');

        $table = Engine_Api::_()->getDbtable('posts', 'group');
        $select = $table->select()
                ->where('group_id = ?', $group->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity())
                ->order('creation_date ASC');

        $paginator = Zend_Paginator::factory($select);

        // Skip to page of specified post
        if (0 !== ($post_id = (int) $this->getRequestParam('post_id')) &&
                null !== ($post = Engine_Api::_()->getItem('group_post', $post_id))) {
            $icpp = $paginator->getItemCountPerPage();
            $page = ceil(($post->getPostIndex() + 1) / $icpp);
            $paginator->setCurrentPageNumber($page);
        }

        // Use specified page
        else if (0 !== ($page = (int) $this->getRequestParam('page'))) {
            $paginator->setCurrentPageNumber($this->getRequestParam('page'));
        }

        $bodyParams['totalItemCount'] = $paginator->getTotalItemCount();

        foreach ($paginator as $post) {
            $tempTopic = $post->toArray();
            $tempTopic['body'] = str_replace('src="/', 'src="' . $this->getHost . '/', $tempTopic['body']);

            if (!empty($post->file_id)) {
                $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl');

                $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
                $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
                $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

                $this->getHost = '';
                if($getDefaultStorageType == 'local')
                    $this->getHost = !empty($staticBaseUrl)? $staticBaseUrl: $serverHost; 
                
                $tempTopic['body'] .= '<img src="' . $this->getHost . $post->getPhotoUrl('thumb.main') . '" alt="Not Get Image"/>';
            }

            $posted_by = Engine_Api::_()->getItem('user', $post->user_id);
            $tempTopic['posted_by'] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($posted_by);

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($posted_by);
            $tempTopic['posted_by'] = array_merge($tempTopic['posted_by'], $getContentImages);

            // Create post menus.
            if (!empty($canPost)) {
                $tempTopic['menu'][] = array(
                    'label' => $this->translate('Quote'),
                    'name' => 'quote',
                    'url' => 'groups/topic/post-create',
                    'urlParams' => array(
                        "quote_id" => $post->getIdentity(),
                        "topic_id" => $topic->getIdentity()
                    )
                );
            }

            if (!empty($canEdit)) {
                $tempTopic['menu'][] = array(
                    'label' => $this->translate('Edit'),
                    'name' => 'edit',
                    'url' => 'groups/post/edit',
                    'urlParams' => array(
                        "post_id" => $post->getIdentity()
                    )
                );

                $tempTopic['menu'][] = array(
                    'label' => $this->translate('Delete'),
                    'name' => 'delete',
                    'url' => 'groups/post/delete',
                    'urlParams' => array(
                        "post_id" => $post->getIdentity()
                    )
                );
            } elseif ($post->user_id != 0 && $post->isOwner($viewer) && !$topic->closed) {
                if (!empty($canEdit_Post)) {
                    $tempTopic['menu'][] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'groups/post/edit',
                        'urlParams' => array(
                            "post_id" => $post->getIdentity()
                        )
                    );
                }

                if (!empty($canDelete_Post)) {
                    $tempTopic['menu'][] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'groups/post/delete',
                        'urlParams' => array(
                            "post_id" => $post->getIdentity()
                        )
                    );
                }
            }

            $bodyParams['response'][] = $tempTopic;
        }

        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Reply on any topic.
     * 
     * @return array
     */
    public function postAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject('group_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $group = $topic->getParentGroup();

        if ($topic->closed)
            $this->respondWithError('unauthorized');


        if ($this->getRequest()->isGet()) {
            $response = $tempResponse = array();

            $tempResponse[] = array(
                'type' => 'Textarea',
                'name' => 'body',
                'label' => $this->translate('Body')
            );

            $tempResponse[] = array(
                'type' => 'Checkbox',
                'name' => 'watch',
                'label' => $this->translate('Send me notifications when other members reply to this topic.')
            );

            $tempResponse[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Post Reply')
            );

//      $allowHtml = ( bool ) Engine_Api::_()->getApi('settings', 'core')->getSetting('group_html', 0);
//      $allowBbcode = ( bool ) Engine_Api::_()->getApi('settings', 'core')->getSetting('group_bbcode', 0);
//      $quote_id = $this->getRequestParam('quote_id', null);
//      if ( !empty($quote_id) ) {
//        $quote = Engine_Api::_()->getItem('group_post', $quote_id);
//        if ( $quote->user_id == 0 )
//          $owner_name = 'Deleted Member';
//        else
//          $owner_name = $quote->getOwner()->__toString();
//
//        if ( !$allowHtml && !$allowBbcode ) {
//          $response['formValue']['body'] = strip_tags($owner_name . ' said:') . " ''" . strip_tags($quote->body) . "''\n-------------\n";
//        } elseif ( $allowHtml && !$allowBbcode ) {
//          $response['formValue']['body'] = "<blockquote><strong>" . $owner_name . ' said:' . "</strong><br />" . $quote->body . "</blockquote><br />";
//        } else {
//          $response['formValue']['body'] = "[blockquote][b]" . strip_tags($owner_name . ' said:') . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n";
//        }
//
//        $response['form'] = $tempResponse;
//      } else {
//        $response['response'] = $tempResponse;
//      }

            $response = $tempResponse;
            $this->respondWithSuccess($response);
        } else if ($this->getRequest()->isPost()) {
            // CONVERT POST DATA INTO THE ARRAY.
            $values = array();
            $getFormKeys = array("body", "watch");
            foreach ($getFormKeys as $element) {
                if (isset($_REQUEST[$element]))
                    $values[$element] = $_REQUEST[$element];
            }

            $values['user_id'] = $viewer->getIdentity();
            $values['topic_id'] = $topic->getIdentity();
            $values['group_id'] = $group->getIdentity();

            $topicTable = Engine_Api::_()->getDbtable('topics', 'group');
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
            $postTable = Engine_Api::_()->getDbtable('posts', 'group');
            $userTable = Engine_Api::_()->getItemTable('user');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

            $viewer = Engine_Api::_()->user()->getViewer();
            $topicOwner = $topic->getOwner();
            $isOwnTopic = $viewer->isSelf($topicOwner);

            $watch = (bool) $values['watch'];
            $isWatching = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $group->getIdentity())
                    ->where('topic_id = ?', $topic->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0)
            ;

            $db = $postTable->getAdapter();
            $db->beginTransaction();

            try {
                $post = $postTable->createRow();
                $post->setFromArray($values);
                $post->save();

                // Watch
                if (false === $isWatching) {
                    $topicWatchesTable->insert(array(
                        'resource_id' => $group->getIdentity(),
                        'topic_id' => $topic->getIdentity(),
                        'user_id' => $viewer->getIdentity(),
                        'watch' => (bool) $watch,
                    ));
                } else if ($watch != $isWatching) {
                    $topicWatchesTable->update(array(
                        'watch' => (bool) $watch,
                            ), array(
                        'resource_id = ?' => $group->getIdentity(),
                        'topic_id = ?' => $topic->getIdentity(),
                        'user_id = ?' => $viewer->getIdentity(),
                    ));
                }

                // Activity
                $action = $activityApi->addActivity($viewer, $topic, 'group_topic_reply');
                if ($action) {
                    $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
                }

                // Notifications
                $notifyUserIds = $topicWatchesTable->select()
                        ->from($topicWatchesTable->info('name'), 'user_id')
                        ->where('resource_id = ?', $group->getIdentity())
                        ->where('topic_id = ?', $topic->getIdentity())
                        ->where('watch = ?', 1)
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN)
                ;

                foreach ($userTable->find($notifyUserIds) as $notifyUser) {
                    // Don't notify self
                    if ($notifyUser->isSelf($viewer)) {
                        continue;
                    }
                    if ($notifyUser->isSelf($topicOwner)) {
                        $type = 'group_topic_response';
                    } else {
                        $type = 'group_topic_reply';
                    }
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($notifyUser, $viewer, $topic, $type, array(
                        'message' => $post->body, // @todo make sure this works
                        'url' => $this->getRequest()->getServer('HTTP_REFERER'),
                    ));
                }

                $db->commit();
                $this->successResponseNoContent('created', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Make sticky to any topic
     * 
     * @return array
     */
    public function stickyAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject('group_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireSubject('group_topic')->isValid())
            $this->respondWithError('unauthorized');

        if ($viewer->getIdentity() != $topic->user_id) {
            if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'topic.edit')->isValid())
                $this->respondWithError('unauthorized');
        }

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->sticky = ( null === $this->getRequestParam('sticky') ? !$topic->sticky : (bool) $this->getRequestParam('sticky') );
            $topic->save();

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Close any topics
     * 
     * @return array
     */
    public function closeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject('group_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $group = $topic->getParent();

        if (!$this->_helper->requireSubject('group_topic')->isValid())
            $this->respondWithError('unauthorized');

        if ($viewer->getIdentity() != $topic->user_id) {
            if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'topic.edit')->isValid())
                $this->respondWithError('unauthorized');
        }

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->closed = ( null === $this->getRequestParam('closed') ? !$topic->closed : (bool) $this->getRequestParam('closed') );
            $topic->save();

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Rename any topics
     * 
     * @return array
     */
    public function renameAction() {
        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject('group_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireSubject('group_topic')->isValid())
            $this->respondWithError('unauthorized');

        if ($viewer->getIdentity() != $topic->user_id) {
            if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'topic.edit')->isValid())
                $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isGet()) {
            $accountForm = array();

            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => $this->translate('Title')
            );

            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Rename Topic')
            );

            $this->respondWithSuccess(array(
                'form' => $accountForm,
                'formValues' => array('title' => $topic->getTitle())
            ));
        } else if ($this->getRequest()->isPost()) {
            if (isset($_REQUEST['title']) && !empty($_REQUEST['title']))
                $values['title'] = $_REQUEST['title'];

            if (!isset($values["title"]) || empty($values["title"])) {
                $this->respondWithValidationError("parameter_missing", "title");
            }

            $table = $topic->getTable();
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                $title = htmlspecialchars($values["title"]);
                $topic = Engine_Api::_()->core()->getSubject();
                $topic->title = $title;
                $topic->save();
                $db->commit();

                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Make watchable to any post topics
     * 
     * @return array
     */
    public function watchAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject();

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $group = Engine_Api::_()->getItem('group', $topic->group_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        $watch = $this->getRequestParam('watch', true);

        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
        $db = $topicWatchesTable->getAdapter();
        $db->beginTransaction();
        try {
            $isWatching = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $group->getIdentity())
                    ->where('topic_id = ?', $topic->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0)
            ;

            if (false === $isWatching) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $group->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $watch,
                ));
            } else if ($watch != $isWatching) {
                $topicWatchesTable->update(array(
                    'watch' => (bool) $watch,
                        ), array(
                    'resource_id = ?' => $group->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Delete group topic
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
            $topic = Engine_Api::_()->core()->getSubject('group_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireSubject('group_topic')->isValid())
            $this->respondWithError('unauthorized');

        if ($viewer->getIdentity() != $topic->user_id) {
            if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'topic.edit')->isValid())
                $this->respondWithError('unauthorized');
        }

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $group = $topic->getParent('group');
            $topic->delete();

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

}
