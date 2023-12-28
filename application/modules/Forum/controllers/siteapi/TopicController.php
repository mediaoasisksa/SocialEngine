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
class Forum_TopicController extends Siteapi_Controller_Action_Standard {

    protected $_filters = array(
        'Basic',
        'Extended',
        'Links',
        'Images',
        'Lists',
        'Email'
    );

    public function init() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        if (0 !== ($topic_id = (int) $this->getRequestParam('topic_id')) && null !== ($topic = Engine_Api::_()->getItem('forum_topic', $topic_id)) && $topic instanceof Forum_Model_Topic) {
            Engine_Api::_()->core()->setSubject($topic);
        }
    }

    /**
     * Get forum topic view page.
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject('forum_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        // Settings
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $decode_bbcode = $settings->getSetting('forum_bbcode');

        // Views
        if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
            $topic->view_count = new Zend_Db_Expr('view_count + 1');
            $topic->save();
        }

        $bodyParams = array();

        // Check watching
        $isWatching = null;
        if ($viewer->getIdentity()) {
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
            $isWatching = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $forum->getIdentity())
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

        // Auth for topic
        $canPost = false;
        $canEdit = false;
        $canDelete = false;
        if (!$topic->closed && Engine_Api::_()->authorization()->isAllowed($forum, null, 'post.create')) {
            $canPost = true;
        }
        if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.edit')) {
            $canEdit = true;
        }
        if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.delete')) {
            $canDelete = true;
        }

        // Auth for posts
        $canEdit_Post = false;
        $canDelete_Post = false;
        if ($viewer->getIdentity()) {
            $canEdit_Post = Engine_Api::_()->authorization()->isAllowed('forum', $viewer->level_id, 'post.edit');
            $canDelete_Post = Engine_Api::_()->authorization()->isAllowed('forum', $viewer->level_id, 'post.delete');
        }

        if ($this->getRequestParam("gutter_menu", 1) && $viewer->getIdentity()) {
            if (!empty($canPost)) {
                $bodyParams['gutterMenu'][] = array(
                    'label' => $this->translate('Post Reply'),
                    'name' => 'post_reply',
                    'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/post-create',
                    'urlParams' => array(
                        "topic_id" => $topic->getIdentity(),
                        "slug" => $topic->getSlug()
//                "forum_id" => $forum->getIdentity(),
                    )
                );
            }

            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
            $isWatching = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $forum->getIdentity())
                    ->where('topic_id = ?', $topic->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0)
            ;

            if (!$isWatching) {
                $bodyParams['gutterMenu'][] = array(
                    'label' => $this->translate('Watch Topic'),
                    'name' => 'watch_topic',
                    'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/watch',
                    'urlParams' => array(
                        "topic_id" => $topic->getIdentity(),
                        "slug" => $topic->getSlug(),
//                "forum_id" => $forum->getIdentity(),
                        "watch" => 1
                    )
                );
            } else {
                $bodyParams['gutterMenu'][] = array(
                    'label' => $this->translate('Stop Watching Topic'),
                    'name' => 'stop_watch_topic',
                    'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/watch',
                    'urlParams' => array(
                        "topic_id" => $topic->getIdentity(),
                        "slug" => $topic->getSlug(),
//                "forum_id" => $forum->getIdentity(),
                        "watch" => 0
                    )
                );
            }

            if (!empty($canEdit)) {
                if (!$topic->sticky) {
                    $bodyParams['gutterMenu'][] = array(
                        'label' => $this->translate('Make Sticky'),
                        'name' => 'make_sticky',
                        'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/sticky',
                        'urlParams' => array(
                            "topic_id" => $topic->getIdentity(),
                            "slug" => $topic->getSlug(),
//                  "forum_id" => $forum->getIdentity(),
                            "sticky" => 1
                        )
                    );
                } else {
                    $bodyParams['gutterMenu'][] = array(
                        'label' => $this->translate('Remove Sticky'),
                        'name' => 'remove_sticky',
                        'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/sticky',
                        'urlParams' => array(
                            "topic_id" => $topic->getIdentity(),
                            "slug" => $topic->getSlug(),
//                  "forum_id" => $forum->getIdentity(),
                            "sticky" => 0
                        )
                    );
                }

                if (!$topic->closed) {
                    $bodyParams['gutterMenu'][] = array(
                        'label' => $this->translate('Close'),
                        'name' => 'close',
                        'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/close',
                        'urlParams' => array(
                            "topic_id" => $topic->getIdentity(),
                            "slug" => $topic->getSlug(),
//                  "forum_id" => $forum->getIdentity(),
                            "close" => 1
                        )
                    );
                } else {
                    $bodyParams['gutterMenu'][] = array(
                        'label' => $this->translate('Open'),
                        'name' => 'open',
                        'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/close',
                        'urlParams' => array(
                            "topic_id" => $topic->getIdentity(),
                            "slug" => $topic->getSlug(),
//                  "forum_id" => $forum->getIdentity(),
                            "close" => 0
                        )
                    );
                }

                $bodyParams['gutterMenu'][] = array(
                    'label' => $this->translate('Rename'),
                    'name' => 'rename',
                    'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/rename',
                    'urlParams' => array(
                        "topic_id" => $topic->getIdentity(),
                        "slug" => $topic->getSlug()
//                "forum_id" => $forum->getIdentity()
                    )
                );

                $bodyParams['gutterMenu'][] = array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Move'),
                    'name' => 'move',
                    'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/move',
                    'urlParams' => array(
                        "topic_id" => $topic->getIdentity(),
                        "slug" => $topic->getSlug()
//                "forum_id" => $forum->getIdentity()
                    )
                );
            }

            if (!empty($canDelete)) {
                $bodyParams['gutterMenu'][] = array(
                    'label' => $this->translate('Delete'),
                    'name' => 'delete',
                    'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/delete',
                    'urlParams' => array(
                        "topic_id" => $topic->getIdentity(),
                        "slug" => $topic->getSlug()
//                "forum_id" => $forum->getIdentity()
                    )
                );
            }
        }

        // Keep track of topic user views to show them which ones have new posts
        if ($viewer->getIdentity()) {
            $topic->registerView($viewer);
        }

        $table = Engine_Api::_()->getItemTable('forum_post');
        $select = $topic->getChildrenSelect('forum_post', array('order' => 'post_id ASC'));
        $requestLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_topic_pagelength', 10);
        $requestPage = $this->getRequestParam("page", 1);

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($requestPage);
        $paginator->setItemCountPerPage($requestLimit);
        $bodyParams['totalItemCount'] = $paginator->getTotalItemCount();
        $bodyParams['isClosed'] = $topic->closed;
        foreach ($paginator as $post) {
            $tempTopic = $post->toArray();
            if ($post->edit_id) {
                $editUser = Engine_Api::_()->getItem('user', $post->edit_id);
                $tempTopic['editBy'] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($editUser);
            }

            if ($decode_bbcode) {
                $options = array('link_no_preparse' => true);
                $parser = new HTML_BBCodeParser2(array_merge(array(
                            'filters' => join(',', $this->_filters)
                                ), $options));
                $parser->setText($tempTopic['body']);
                $parser->parse();
                $tempTopic['body'] = $parser->getParsed();
            }

            $tempTopic['body'] = str_replace('src="/', 'src="' . $this->getHost . '/', $tempTopic['body']);

            if (!empty($post->file_id)) {
                $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);
                $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
                $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
                $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

                $this->getHost = '';
                if ($getDefaultStorageType == 'local')
                    $this->getHost = !empty($staticBaseUrl) ? $staticBaseUrl : $serverHost;

                $tempTopic['body'] .= '<img src="' . $this->getHost . $post->getPhotoUrl('thumb.main') . '" alt="Not Get Image"/>';
            }

            $tempTopic['body'] = str_replace('"', "'", $tempTopic['body']);

            $posted_by = Engine_Api::_()->getItem('user', $post->user_id);
            $tempTopic['posted_by'] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($posted_by);

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($posted_by);
            $tempTopic['posted_by'] = array_merge($tempTopic['posted_by'], $getContentImages);

            //Member verification Work...............
            $tempTopic['posted_by']['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($posted_by);
            $signature = $post->getSignature();
            $tempTopic['posted_by']['post_count'] = !empty($signature) ? $signature->post_count : 0;

            $isModeratorPost = $forum->isModerator($post->getOwner());
            $tempTopic['posted_by']['is_moderator'] = !empty($isModeratorPost) ? $isModeratorPost : 0;

            // Create post menus.
            if (!empty($canPost)) {
                $tempTopic['menu'][] = array(
                    'label' => $this->translate('Quote'),
                    'name' => 'quote',
                    'url' => 'forums/topic/' . $topic->getIdentity() . '/' . $topic->getSlug() . '/post-create',
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
                    'url' => 'forums/post/' . $post->getIdentity() . '/edit',
                    'urlParams' => array(
                        "post_id" => $post->getIdentity()
                    )
                );

                $tempTopic['menu'][] = array(
                    'label' => $this->translate('Delete'),
                    'name' => 'delete',
                    'url' => 'forums/post/' . $post->getIdentity() . '/delete',
                    'urlParams' => array(
                        "post_id" => $post->getIdentity()
                    )
                );
            } elseif ($post->user_id != 0 && $post->isOwner($viewer) && !$topic->closed) {
                if (!empty($canEdit_Post)) {
                    $tempTopic['menu'][] = array(
                        'label' => $this->translate('Edit'),
                        'name' => 'edit',
                        'url' => 'forums/post/' . $post->getIdentity() . '/edit',
                        'urlParams' => array(
                            "post_id" => $post->getIdentity()
                        )
                    );
                }

                if (!empty($canDelete_Post)) {
                    $tempTopic['menu'][] = array(
                        'label' => $this->translate('Delete'),
                        'name' => 'delete',
                        'url' => 'forums/post/' . $post->getIdentity() . '/delete',
                        'urlParams' => array(
                            "post_id" => $post->getIdentity()
                        )
                    );
                }
            }

            if ($viewer->getIdentity() && $post->user_id != $viewer->getIdentity()) {
                $tempTopic['menu'][] = array(
                    'label' => $this->translate('Report'),
                    'name' => 'report',
                    'url' => 'report/create/subject/' . $post->getGuid(),
                    'urlParams' => array(
                        "type" => $post->getType(),
                        "id" => $post->getIdentity()
                    )
                );
            }

            $bodyParams['response'][] = $tempTopic;
        }

        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Delete forum topic
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
            $topic = Engine_Api::_()->core()->getSubject('forum_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.delete')->isValid())
            $this->respondWithError('unauthorized');

        // Process
        $table = Engine_Api::_()->getItemTable('forum_topic');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $topic->delete();
            $db->commit();

            $this->successResponseNoContent('no_content', 'forum_index_index');
        } catch (Exception $e) {
            $db->rollBack();

            $this->respondWithValidationError('internal_server_error', $e->getMessage());
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
            $topic = Engine_Api::_()->core()->getSubject('forum_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid())
            $this->respondWithError('unauthorized');

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->sticky = ( null === $this->getRequestParam('sticky') ? !$topic->sticky : (bool) $this->getRequestParam('sticky') );
            $topic->save();
            $db->commit();

            $this->successResponseNoContent('no_content');
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
            $topic = Engine_Api::_()->core()->getSubject('forum_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid())
            $this->respondWithError('unauthorized');

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->closed = ( null === $this->getRequestParam('closed') ? !$topic->closed : (bool) $this->getRequestParam('closed') );
            $topic->save();
            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Rename forum topic
     * 
     * @return array
     */
    public function renameAction() {
        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject('forum_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid())
            $this->respondWithError('unauthorized');

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
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
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

                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Move topic to another post
     * 
     * @return array
     */
    public function moveAction() {
        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject('forum_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid())
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $accountForm = $multiOptions = array();
            foreach (Engine_Api::_()->getItemTable('forum')->fetchAll() as $forum) {
                $multiOptions[$forum->getIdentity()] = $forum->getTitle();
            }

            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'forum_id',
                'label' => $this->translate('Forum'),
                'multiOptions' => $multiOptions
            );

            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Move Topic')
            );

            $this->respondWithSuccess(array(
                'form' => $accountForm,
                'formValues' => array("forum_id" => $topic->forum_id)
            ));
        } else if ($this->getRequest()->isPost()) {
            if (isset($_REQUEST['forum_id']) && !empty($_REQUEST['forum_id']))
                $values['forum_id'] = $_REQUEST['forum_id'];

            if (!isset($values['forum_id']) || empty($values['forum_id'])) {
                $this->respondWithValidationError("parameter_missing", "forum_id");
            }

            $table = $topic->getTable();
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                // Update topic
                $topic->forum_id = $values['forum_id'];
                $topic->save();
                $db->commit();

                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Create forum post create
     * 
     * @return array
     */
    public function postCreateAction() {
        if (Engine_Api::_()->core()->hasSubject())
            $topic = Engine_Api::_()->core()->getSubject('forum_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'post.create')->isValid())
            $this->respondWithError('unauthorized');

        if ($topic->closed)
            $this->respondWithError('unauthorized');



        // CHECK FORUM FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE FORUM EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            $response = $tempResponse = array();

            $tempResponse[] = array(
                'type' => 'Textarea',
                'name' => 'body',
                'label' => $this->translate('Body')
            );

            $tempResponse[] = array(
                'type' => 'Checkbox',
                'name' => 'watch',
                'label' => $this->translate('Send me notifications when other members reply to this topic.'),
                'checked' => 'checked'
            );

            $tempResponse[] = array(
                'type' => 'File',
                'name' => 'photo',
                'label' => $this->translate('Add Photo')
            );

            $tempResponse[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Post Reply')
            );

            $allowHtml = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_html', 0);
            $allowBbcode = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_bbcode', 0);
            $quote_id = $this->getRequestParam('quote_id', null);
            if (!empty($quote_id)) {
                $quote = Engine_Api::_()->getItem('forum_post', $quote_id);
                if ($quote->user_id == 0)
                    $owner_name = 'Deleted Member';
                else
                    $owner_name = $quote->getOwner()->__toString();

                if (!$allowHtml && !$allowBbcode) {
                    $response['formValues']['body'] = strip_tags($owner_name . ' said:') . " ''" . strip_tags($quote->body) . "''\n-------------\n";
                } elseif ($allowHtml && !$allowBbcode) {
                    $response['formValues']['body'] = "<blockquote><strong>" . $owner_name . ' said:' . "</strong><br />" . $quote->body . "</blockquote><br />";
                } else {
                    $response['formValues']['body'] = "[blockquote][b]" . strip_tags($owner_name . ' said:') . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n";
                }

                $response['form'] = $tempResponse;
            } else {
                $response = $tempResponse;
            }

            $this->respondWithSuccess($response);
        } else if ($this->getRequest()->isPost()) {
            /* UPDATE THE FORUM INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */
            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('forum', $viewer->level_id, 'post.flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("posts",'forum');
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
            $getFormKeys = array("body", "watch");
            foreach ($getFormKeys as $element) {
                if (isset($_REQUEST[$element]))
                    $values[$element] = $_REQUEST[$element];
            }

            $values['user_id'] = $viewer->getIdentity();
            $values['topic_id'] = $topic->getIdentity();
            $values['forum_id'] = $forum->getIdentity();

            $topicTable = Engine_Api::_()->getDbtable('topics', 'forum');
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
            $postTable = Engine_Api::_()->getDbtable('posts', 'forum');
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
                    ->where('resource_id = ?', $forum->getIdentity())
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

                if (!empty($_FILES['photo'])) {
                    try {
                        Engine_Api::_()->getApi('Siteapi_Core', 'forum')->setPhoto($_FILES['photo'], $post);
                    } catch (Engine_Image_Adapter_Exception $e) {
                        
                    }
                }

                // Watch
                if (false === $isWatching) {
                    $topicWatchesTable->insert(array(
                        'resource_id' => $forum->getIdentity(),
                        'topic_id' => $topic->getIdentity(),
                        'user_id' => $viewer->getIdentity(),
                        'watch' => (bool) $watch,
                    ));
                } else if ($watch != $isWatching) {
                    $topicWatchesTable->update(array(
                        'watch' => (bool) $watch,
                            ), array(
                        'resource_id = ?' => $forum->getIdentity(),
                        'topic_id = ?' => $topic->getIdentity(),
                        'user_id = ?' => $viewer->getIdentity(),
                    ));
                }

                // Activity
                $action = $activityApi->addActivity($viewer, $topic, 'forum_topic_reply');
                if ($action) {
                    $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
                }

                // Notifications
                $notifyUserIds = $topicWatchesTable->select()
                        ->from($topicWatchesTable->info('name'), 'user_id')
                        ->where('resource_id = ?', $forum->getIdentity())
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
                        $type = 'forum_topic_response';
                    } else {
                        $type = 'forum_topic_reply';
                    }
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($notifyUser, $viewer, $topic, $type, array(
                        'message' => $post->body, // @todo make sure this works
                        'url' => $this->getRequest()->getServer('HTTP_REFERER'),
                    ));
                }

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
            $topic = Engine_Api::_()->core()->getSubject('forum_topic');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($topic))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, $viewer, 'view')->isValid())
            $this->respondWithError('unauthorized');

        $watch = $this->getRequestParam('watch', 1);

        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
        $db = $topicWatchesTable->getAdapter();
        $db->beginTransaction();

        try {
            $isWatching = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $forum->getIdentity())
                    ->where('topic_id = ?', $topic->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0)
            ;

            if (false === $isWatching) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $forum->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $watch,
                ));
            } else if ($watch != $isWatching) {
                $topicWatchesTable->update(array(
                    'watch' => (bool) $watch,
                        ), array(
                    'resource_id = ?' => $forum->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }

            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

}
