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
class Blog_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if ($this->getRequestParam("blog_id") && (0 !== ($blog_id = (int) $this->getRequestParam("blog_id")) &&
                null !== ($blog = Engine_Api::_()->getItem('blog', $blog_id))))
            Engine_Api::_()->core()->setSubject($blog);
        
        Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
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
        if (!Engine_Api::_()->authorization()->isAllowed('blog', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'blog')->getBrowseSearchForm(array(
                    'draft' => 0,
                    'visible' => 1
                )), true);
    }

    /**
     * Return the "Browse Blog" page. 
     * 
     * @return array
     */
    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('blog', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $bodyParams = array();
        $getRequest = $this->getRequestAllParams;
        $getRequest['draft'] = 0;
        $getRequest['visible'] = 1;

        if (isset($getRequest['user_id']) && !empty($getRequest['user_id']))
            unset($getRequest['visible']);

        $response = $this->_getBlogLists($getRequest);

        $this->respondWithSuccess($response, true);
    }

    /**
     * Return the "My Blog" page. 
     * 
     * @return array
     */
    public function manageAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!Engine_Api::_()->authorization()->isAllowed('blog', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $getRequest = $this->getRequestAllParams;
        $getRequest['manage'] = 1;
        $response = $this->_getBlogLists($getRequest);

        $this->respondWithSuccess($response);
    }

    /**
     * Return the Blog Profile page.
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods

        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $siteapiBlogView = Zend_Registry::isRegistered('siteapiBlogView') ? Zend_Registry::get('siteapiBlogView') : null;

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('blog');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        if (!Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'view')) {
            $module_error_type = @ucfirst($subject->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }


        $bodyParams = $tagArray = array();
        // GETTING THE GUTTER-MENUS.
        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($subject);

        $bodyParams['response'] = $subject->toArray();

        $contentURL = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($subject);

        if (!empty($contentURL))
            $bodyParams['response'] = array_merge($bodyParams['response'], $contentURL);

        // Add blog images
        $getContentImages1 = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages1);


        // Add owner images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

        $bodyParams['response']["owner_title"] = $subject->getOwner()->getTitle();

        // Getting viewer like or not to content.
        $bodyParams['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($subject);

        // Getting like count.
        $bodyParams['response']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($subject);

        // Get tags
        $blogTags = $subject->tags()->getTagMaps();
        if (!empty($blogTags)) {
            foreach ($blogTags as $tag) {
                $tagArray[$tag->getTag()->tag_id] = $tag->getTag()->text;
            }
        }

        // Blog View Count Increment
        if (!$subject->isOwner($viewer)) {
            Engine_Api::_()->getDbtable('blogs', 'blog')->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
                    ), array(
                'blog_id = ?' => $subject->getIdentity(),
            ));
        }

        $bodyParams['response']['tags'] = $tagArray;

        $categoryObj = Engine_Api::_()->getItem('blog_category', $bodyParams['response']['category_id']);
        if (!empty($categoryObj))
            $bodyParams['response']['category_title'] = $categoryObj->getTitle();


        $bodyParams['response']['body'] = @str_replace('src="/', 'src="' . $this->getHost . '/', $bodyParams['response']['body']);

        if (!empty($siteapiBlogView))
            $this->respondWithSuccess($bodyParams, true);
    }

    /**
     * Return the "Create Blog" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function createAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $level_id = $viewer->level_id;



        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToCreate = $permissionsTable->getAllowed('blog', $level_id, 'create');
        }

        if (empty($allowToCreate))
            $this->respondWithError('unauthorized');

        $siteapiBlogCreate = Zend_Registry::isRegistered('siteapiBlogCreate') ? Zend_Registry::get('siteapiBlogCreate') : null;
        $quota = Engine_Api::_()->authorization()->getPermission($level_id, 'blog', 'max');
        $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator(array('user_id' => $viewer->getIdentity()));
        $current_count = $paginator->getTotalItemCount();
        if (($current_count >= $quota) && !empty($quota))
            $this->respondWithError('unauthorized', 'You have already uploaded the maximum number of entries allowed. If you would like to upload a new entry, please delete an old one first.');

        /* RETURN THE BLOG CREATE FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'blog')->getForm());
        } else if (!empty($siteapiBlogCreate) && $this->getRequest()->isPost()) {
            /* CREATE THE BLOG IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */
            if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
                $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('blog', $viewer->level_id, 'flood');
                if(!empty($itemFlood[0])){
                    //get last activity
                    $tableFlood = Engine_Api::_()->getDbTable("blogs",'blog');
                    $select = $tableFlood->select()->where("owner_id = ?",$viewer->getIdentity())->order("creation_date DESC");
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
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'blog')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }


            $viewer = Engine_Api::_()->user()->getViewer();
            $data = $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));

            if($values['draft'] == 'Published'){
                $data['draft'] = 0;
            }
            else{
                $data['draft'] = 1;
            }

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'blog')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('blog', $viewer, 'auth_view');
            $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
            if (!empty($viewOptions) && count($viewOptions) == 1) {
                $values['auth_view'] = key($viewOptions);
            }

            $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('blog', $viewer, 'auth_comment');
            $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
            if (!empty($commentOptions) && count($commentOptions) == 1) {
                $values['auth_view'] = key($commentOptions);
            }

            $table = Engine_Api::_()->getItemTable('blog');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $blog = $table->createRow();
                $blog->setFromArray($values);
                $blog->save();

                // Auth
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'everyone';
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
                }

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $blog->tags()->addTagMaps($viewer, $tags);

                // Add activity only if blog is published
                if ($values['draft'] == 0) {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'blog_new');

                    // make sure action exists before attaching the blog to the activity
                    if ($action) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                    }
                }

                // Send notifications for subscribers
                Engine_Api::_()->getApi('Siteapi_Core', 'blog')->sendNotifications($blog);

                // Commit
                $db->commit();

                // Change request method POST to GET
                $this->setRequestMethod();

                $this->_forward('view', 'index', 'blog', array(
                    'blog_id' => $blog->getIdentity()
                ));
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Return the "Edit Blog" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject('blog');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $isAllowedView = $subject->authorization()->isAllowed($viewer, 'edit');

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO EDIT BLOG.
        if (empty($isAllowedView))
            $this->respondWithError('unauthorized');

        // FIND OUT THE AUTH COMMENT AND AOUTH VIEW VALUE.
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        // CHECK BLOG FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE BLOG EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            // IF THERE ARE NO FORM POST YET THEN RETURN THE BLOG FORM.
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'blog')->getForm($subject);
            $formValues = $subject->toArray();

            foreach ($roles as $role) {
                if ($auth->isAllowed($subject, $role, 'view'))
                    $formValues['auth_view'] = $role;

                if ($auth->isAllowed($subject, $role, 'comment'))
                    $formValues['auth_comment'] = $role;
            }

            // SET THE TAGS  
            $tagStr = '';
            foreach ($subject->tags()->getTagMaps() as $tagMap) {
                $tag = $tagMap->getTag();
                if (!isset($tag->text))
                    continue;
                if ('' !== $tagStr)
                    $tagStr .= ', ';
                $tagStr .= $tag->text;
            }
            $formValues['tags'] = $tagStr;

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));
            return;
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* UPDATE THE BLOG INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO THE ARRAY.
            $data = $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'blog')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values;

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'blog')->getFormValidators($subject);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $subject->setFromArray($values);
                $subject->modified_date = date('Y-m-d H:i:s');
                $subject->save();

                // Auth
                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'everyone';
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($subject, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($subject, $role, 'comment', ($i <= $commentMax));
                }

                // handle tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $subject->tags()->setTagMaps($viewer, $tags);

                // insert new activity if blog is just getting published
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($subject);
                if (count($action->toArray()) <= 0 && $values['draft'] == '0') {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'blog_new');
                    // make sure action exists before attaching the blog to the activity
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $subject);
                    }
                }

                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($subject) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                Engine_Api::_()->getApi('Siteapi_Core', 'blog')->sendNotifications($subject);

                $db->commit();

                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Delete the Blog.
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
            $subject = Engine_Api::_()->core()->getSubject('blog');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        if (!empty($viewer_id))
            $level_id = $viewer->level_id;

        // GET LOGGED-IN USER LEVEL ID.
        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToDelete = $permissionsTable->getAllowed('blog', $viewer->level_id, 'delete');
        }

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO DELETE BLOG.
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

    /**
     * Return the Blog Categories for respective Blog Owner.
     * 
     * @return array
     */
    public function categoryAction() {
        // Validate request methods
        $this->validateRequestMethod();

        if (!($owner_id = $this->getRequestParam('owner_id'))) {
            $this->respondWithValidationError('parameter_missing', 'owner_id');
        }

        $owner = Engine_Api::_()->getItem('user', $owner_id);
        if (empty($owner)) {
            $this->respondWithError('no_record');
        } else {
            $this->respondWithSuccess(Engine_Api::_()->getDbtable('categories', 'blog')->getUserCategoriesAssoc($owner->getIdentity()), true);
        }
    }

    /**
     * Return TAG for respective Blog Owner.
     * 
     * @return array
     */
    public function tagAction() {
        // Validate request methods
        $this->validateRequestMethod();

        if (!($owner_id = $this->getRequestParam('owner_id'))) {
            $this->respondWithValidationError('parameter_missing', 'owner_id');
        }

        $owner = Engine_Api::_()->getItem('user', $owner_id);
        if (empty($owner)) {
            $this->respondWithError('no_record');
        } else {
            $getTagByTagger = Engine_Api::_()->getDbtable('tags', 'core')->getTagsByTagger('blog', $owner);
            $getTagByTaggerArray = $getTagByTagger->toArray();
            $this->respondWithSuccess($getTagByTaggerArray, true);
        }
    }

    /**
     * Subscribe to owner_id member's blog?
     * 
     * @return array
     */
    public function subscribeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (!($owner_id = $this->getRequestParam('owner_id'))) {
            $this->respondWithValidationError('parameter_missing', 'owner_id');
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $user = Engine_Api::_()->getItem("user", $owner_id);

        if (empty($viewer) || empty($user))
            $this->respondWithError('unauthorized');

        // Get subscription table
        $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'blog');

        // Check if they are already subscribed
        if ($subscriptionTable->checkSubscription($user, $viewer))
            $this->respondWithError('subscription_already_exist');

        // Process
        $db = $user->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subscriptionTable->createSubscription($user, $viewer);
            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Unsubscribe to owner_id member's blog?
     * 
     * @return array
     */
    public function unsubscribeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (!($owner_id = $this->getRequestParam('owner_id'))) {
            $this->respondWithValidationError('parameter_missing', 'owner_id');
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $user = Engine_Api::_()->getItem("user", $owner_id);

        if (empty($viewer) || empty($user))
            $this->respondWithError('unauthorized');

        // Get subscription table
        $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'blog');

        // Check if they are already subscribed
        if (!$subscriptionTable->checkSubscription($user, $viewer))
            $this->respondWithError('subscription_already_not_exist');

        // Process
        $db = $user->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subscriptionTable->removeSubscription($user, $viewer);
            $db->commit();

            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Getting the blog list for "Browse Blog" and "My Blog"
     * 
     * @return array
     */
    private function _getBlogLists($params = array()) {
        $getSearchValue = $response = $tempParams = $value = $tempResponse = array();
        $imageType = 'thumb.icon';

        $viewer = Engine_Api::_()->user()->getViewer();

        $siteapiBlogBrowse = Zend_Registry::isRegistered('siteapiBlogBrowse') ? Zend_Registry::get('siteapiBlogBrowse') : null;
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();

        // Set the parameters for filtering.
        if (!empty($params['manage']))
            $tempParams['user_id'] = $viewer->getIdentity();

        if (isset($params['user_id']))
            $tempParams['user_id'] = $params['user_id'];

        if (isset($params['draft']))
            $tempParams['draft'] = $params['draft'];

        if (isset($params['visible']))
            $tempParams['visible'] = $params['visible'];

        if (!empty($params['search']))
            $tempParams['search'] = $params['search'];

        if (!empty($params['orderby']))
            $tempParams['orderby'] = $params['orderby'];

        if (!empty($params['category']))
            $tempParams['category'] = $params['category'];

        if (empty($params['manage']) && isset($params['show']) && $params['show'] == 2) {
            // Get an array of friend ids
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $tempParams['users'] = $ids;
        }

        if (!empty($params['image_type']))
            $imageType = $params['image_type'];

        $tableObj = Engine_Api::_()->getDbtable('blogs', 'blog');
        $getBlogSelect = Engine_Api::_()->getItemTable('blog')->getBlogsSelect($tempParams);

        if (Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
            // set items per page
            $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
        } elseif (!empty($params['limit'])) {
            $items_per_page = $params['limit'];
        } else {
            $items_per_page = 20;
        }

        // If get the 'page' and 'limit' in request then apply the pagination.
        if (isset($params['page']) && !empty($params['page']) && isset($params['limit']) && !empty($params['limit'])) {
            $params['limit'] = $items_per_page;
            $params = array_merge($tempParams, $params);
            $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator($params);

            $blogsObj = $paginator;
            $paginator->clearPageItemCache();
            $getTempBlogCount = $paginator->getTotalItemCount();
        } else {
            $blogsObj = $tableObj->fetchAll($getBlogSelect);
            $getTempBlogCount = COUNT($blogsObj);
        }

        $response['totalItemCount'] = $getTempBlogCount;

        // Set the 'side menus' for 'My Blog' 
        if ($getTempBlogCount) {
            foreach ($blogsObj as $blogObj) {
                $value = $blogObj->toArray();

                if (!empty($params['manage'])) {
                    $tempMenu = array();
                    if ($blogObj->isOwner($viewer)) {
                        $tempMenu[] = array(
                            'label' => $this->translate('Edit Blog'),
                            'name' => 'edit',
                            'url' => 'blogs/edit/' . $blogObj->getIdentity(),
                            'urlParams' => array(
                            )
                        );

                        $tempMenu[] = array(
                            'label' => $this->translate('Delete Blog'),
                            'name' => 'delete',
                            'url' => 'blogs/delete/' . $blogObj->getIdentity(),
                            'urlParams' => array(
                            )
                        );
                    }

                    $value["menu"] = $tempMenu;
                }

                $categoryObj = Engine_Api::_()->getItem('blog_category', $value['category_id']);
                if (!empty($categoryObj))
                    $value['category_title'] = $categoryObj->getTitle();

                // Add blog images
                $getContentImages1 = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($blogObj);
                $value = array_merge($value, $getContentImages1);

                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($blogObj, true);
                $value = array_merge($value, $getContentImages);

                $value["owner_title"] = $blogObj->getOwner()->getTitle();

                //Member verification Work...............
                $value['showVerifyIcon'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVerifyInfo($blogObj->getOwner());

                $isAllowedView = $blogObj->authorization()->isAllowed($viewer, 'view');
                $value["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $isAllowedEdit = $blogObj->authorization()->isAllowed($viewer, 'edit');
                if (isset($params['is_edit']) && !empty($params['is_edit']))
                    $value["edit"] = empty($isAllowedEdit) ? 0 : 1;

                $isAllowedDelete = $blogObj->authorization()->isAllowed($viewer, 'delete');
                if (isset($params['is_delete']) && !empty($params['is_delete']))
                    $value["delete"] = empty($isAllowedDelete) ? 0 : 1;

                $tempResponse[] = $value;
            }

            if (!empty($tempResponse))
                $response['response'] = $tempResponse;
        }

        if (!empty($siteapiBlogBrowse))
            return $response;
    }

    /**
     * Gutter menu show on the blog profile page.
     * 
     * @return array
     */
    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();

        // CREATE BLOG LINK
        if (($viewer->getIdentity() == $owner->getIdentity()) && Engine_Api::_()->authorization()->isAllowed('blog', $viewer, 'create')) {
            $menus[] = array(
                'label' => $this->translate('Write New Entry'),
                'name' => 'create',
                'url' => 'blogs/create',
                'urlParams' => array(
                )
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'label' => $this->translate('Edit This Entry'),
                'name' => 'edit',
                'url' => 'blogs/edit/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'label' => $this->translate('Delete This Entry'),
                'name' => 'delete',
                'url' => 'blogs/delete/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

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

        try {
            if ($viewer->getIdentity() && ($viewer->getIdentity() != $owner->getIdentity())) {
                $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'blog');
                if (!$subscriptionTable->checkSubscription($owner, $viewer)) {
                    $menus[] = array(
                        'label' => $this->translate('Subscribe'),
                        'name' => 'subscribe',
                        'url' => 'blogs/subscribe',
                        'urlParams' => array(
                            "owner_id" => $owner->getIdentity()
                        )
                    );
                } else {
                    $menus[] = array(
                        'label' => $this->translate('Unsubscribe'),
                        'name' => 'unsubscribe',
                        'url' => 'blogs/unsubscribe',
                        'urlParams' => array(
                            "owner_id" => $owner->getIdentity()
                        )
                    );
                }
            }
        } catch (Exception $ex) {
//Blank Exception
        }
        return $menus;
    }

}
