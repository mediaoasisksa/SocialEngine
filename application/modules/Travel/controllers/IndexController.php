<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: IndexController.php 9893 2013-02-14 00:00:53Z shaun $
 * @author     Donna
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 */
class Travel_IndexController extends Core_Controller_Action_Standard
{
    public function init()
    {
        if( !$this->_helper->requireAuth()->setAuthParams('travel', null, 'view')->isValid() ) return;
    }
    public function rateAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();

        $rating = $this->_getParam('rating');
        $travel_id =  $this->_getParam('resource_id');


        $table = Engine_Api::_()->getDbtable('ratings', 'travel');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            Engine_Api::_()->getDbtable('ratings', 'travel')->setRating($travel_id, $user_id, $rating);

            $travel = Engine_Api::_()->getItem('travel', $travel_id);
            $travel->rating = Engine_Api::_()->getDbtable('ratings', 'travel')->getRating($travel->getIdentity());
            $travel->save();
            
            $owner = Engine_Api::_()->getItem('user', $travel->owner_id);
            if($owner->user_id != $user_id)
            Engine_Api::_()->getDbTable('notifications', 'activity')->addNotification($owner, $viewer, $travel, 'travel_rating');
            
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $total = Engine_Api::_()->getDbtable('ratings', 'travel')->ratingCount($travel->getIdentity());

        $data = array();
        $data[] = array(
            'total' => $total,
            'rating' => $rating,
        );
        return $this->_helper->json($data);
    }
    // NONE USER SPECIFIC METHODS
    public function indexAction()
    {
        // Check auth
        if( !$this->_helper->requireAuth()->setAuthParams('travel', null, 'view')->isValid() ) return;

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('travel', null, 'create')->checkRequire();


        // Prepare form
        $this->view->form = $form = new Travel_Form_Search();

        if( !$viewer->getIdentity() ) {
            $form->removeElement('show');
        }

        // Populate form
        $categories = Engine_Api::_()->getDbtable('categories', 'travel')->getCategoriesAssoc();
        if( !empty($categories) && is_array($categories) && $form->getElement('category') ) {
            $form->getElement('category')->addMultiOptions($categories);
        }

        // Process form
        if( $form->isValid($this->_getAllParams()) ) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = array_filter($values);


        $customFieldValues = array_intersect_key($values, $form->getFieldElements());

        // Process options
        $tmp = array();
        foreach( $customFieldValues as $k => $v ) {
            if( null == $v || '' == $v || (is_array($v) && engine_count(array_filter($v)) == 0) ) {
                continue;
            } elseif( false !== strpos($k, '_field_') ) {
                list($null, $field) = explode('_field_', $k);
                $tmp['field_' . $field] = $v;
            } elseif( false !== strpos($k, '_alias_') ) {
                list($null, $alias) = explode('_alias_', $k);
                $tmp[$alias] = $v;
            } else {
                $tmp[$k] = $v;
            }
        }
        $customFieldValues = $tmp;

        // Do the show thingy
        if( @$values['show'] == 2 ) {
            // Get an array of friend ids to pass to getTravelsPaginator
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach( $friends as $friend ) {
                $ids[] = $friend->user_id;
            }
            //unset($values['show']);
            $values['users'] = $ids;
        }


        // check to see if request is for specific user's listings
        if( ($userId = $this->_getParam('user_id')) ) {
            $values['user_id'] = $userId;
        }

        $this->view->assign($values);

        // items needed to show what is being filtered in browse page
        if( !empty($values['tag']) ) {
            $this->view->tag_text = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
        }
        if(isset($_GET['tag_id']) && !empty($_GET['tag_id']) ) {
          $values['tag'] = $_GET['tag_id'];
        }
        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        $paginator = Engine_Api::_()->getItemTable('travel')->getTravelsPaginator($values, $customFieldValues);
        $itemsCount = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('travel.page', 10);
        $paginator->setItemCountPerPage($itemsCount);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        if( !empty($values['category']) ) {
            $this->view->categoryObject = Engine_Api::_()->getDbtable('categories', 'travel')
                ->find($values['category'])->current();
        }

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;
    }

    public function viewAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->travel = $travel = Engine_Api::_()->getItem('travel', $this->_getParam('travel_id'));
        if( $travel ) {
            Engine_Api::_()->core()->setSubject($travel);
        }

        // Check auth
        if( !$this->_helper->requireAuth()->setAuthParams($travel, null, 'view')->isValid() ) {
            return;
        }


        // Network check
        $networkPrivacy = Engine_Api::_()->network()->getViewerNetworkPrivacy($travel);
        if(empty($networkPrivacy))
            return $this->_forward('requireauth', 'error', 'core');

        $this->view->canEdit = $canEdit = $travel->authorization()->isAllowed(null, 'edit');
        $this->view->canDelete = $canDelete = $travel->authorization()->isAllowed(null, 'delete');
        $this->view->canUpload = $canUpload = $travel->authorization()->isAllowed(null, 'photo');

        // Get navigation
        $this->view->gutterNavigation = $gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('travel_gutter');

        if( $travel ) {
            $this->view->owner = $owner = Engine_Api::_()->getItem('user', $travel->owner_id);
            $this->view->viewer = $viewer;

            if( !$owner->isSelf($viewer) ) {
                $travel->view_count++;
                $travel->save();
            }

            $this->view->travel = $travel;
            if( $travel->photo_id ) {
                $this->view->main_photo = $travel->getPhoto($travel->photo_id);
            }

            // get tags
            $this->view->travelTags = $travel->tags()->getTagMaps();
            $this->view->userTags = $travel->tags()->getTagsByTagger($travel->getOwner());

            // get custom field values
            //$this->view->fieldsByAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($travel);
            // Load fields view helpers
            $view = $this->view;
            $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
            $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($travel);

            // album material
            $this->view->album = $album = $travel->getSingletonAlbum();
            $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));
            $paginator->setItemCountPerPage(100);

            if( $travel->category_id ) {
                $this->view->categoryObject = Engine_Api::_()->getDbtable('categories', 'travel')
                    ->find($travel->category_id)->current();
            }
        }

        $this->view->viewer_id = $viewer->getIdentity();
        $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'travel')->ratingCount($travel->getIdentity());
        $this->view->rated = Engine_Api::_()->getDbTable('ratings', 'travel')->checkRated($travel->getIdentity(), $viewer->getIdentity());
        
        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;
    }

    // USER SPECIFIC METHODS
    public function manageAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('travel', null, 'create')->checkRequire();
        $this->view->allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'travel', 'photo');

        $this->view->form = $form = new Travel_Form_Search();
        $form->removeElement('show');

        // Populate form
        $categories = Engine_Api::_()->getDbtable('categories', 'travel')->getCategoriesAssoc();
        if( !empty($categories) && is_array($categories) && $form->getElement('category') ) {
            $form->getElement('category')->addMultiOptions($categories);
        }

        // Process form
        if( $form->isValid($this->getRequest()->getPost()) ) {
            $values = $form->getValues();
        } else {
            $values = array();
        }

        $this->view->formValues = array_filter($values);

        $values['user_id'] = $viewer->getIdentity();
        $customFieldValues = array_intersect_key($values, $form->getFieldElements());

        // Process options
        $tmp = array();
        foreach( $customFieldValues as $k => $v ) {
            if( null == $v || '' == $v || (is_array($v) && engine_count(array_filter($v)) == 0) ) {
                continue;
            }

            if( false !== strpos($k, '_field_') ) {
                list($null, $field) = explode('_field_', $k);
                $tmp['field_' . $field] = $v;
            } elseif( false !== strpos($k, '_alias_') ) {
                list($null, $alias) = explode('_alias_', $k);
                $tmp[$alias] = $v;
            } else {
                $tmp[$k] = $v;
            }
        }
        $customFieldValues = $tmp;
        // Get paginator
        $values['closed'] = @$_GET['closed'];
        $values = array_merge($values, $_GET);
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('travel')->getTravelsPaginator($values, $customFieldValues);
        $itemsCount = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('travel.page', 10);
        $paginator->setItemCountPerPage($itemsCount);
        $this->view->paginator = $paginator->setCurrentPageNumber( $this->_getParam('page', 1) );

        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        // maximum allowed travels
        $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'travel', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();
    }

    public function createAction()
    {
        // Check auth
        if( !$this->_helper->requireUser()->isValid() ) return;
        if( !$this->_helper->requireAuth()->setAuthParams('travel', null, 'create')->isValid() ) return;

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled();
            
        $this->view->category_id = (isset($_POST['category_id']) && $_POST['category_id'] != 0) ? $_POST['category_id'] : 0;
        $this->view->subcat_id = (isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0) ? $_POST['subcat_id'] : 0;
        $this->view->subsubcat_id = (isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0) ? $_POST['subsubcat_id'] : 0;
        $this->view->form = $form = new Travel_Form_Create();

        // set up data needed to check quota
        $viewer = Engine_Api::_()->user()->getViewer();
        $values['user_id'] = $viewer->getIdentity();
        $paginator = Engine_Api::_()->getItemTable('travel')->getTravelsPaginator($values);

        $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'travel', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();
        // If not post or form not valid, return
        if( !$this->getRequest()->isPost() ) {
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('travel', $this->view->viewer()->level_id, 'flood');
        if(!empty($itemFlood[0])){
            //get last activity
            $tableFlood = Engine_Api::_()->getDbTable("travels",'travel');
            $select = $tableFlood->select()->where("owner_id = ?",$this->view->viewer()->getIdentity())->order("creation_date DESC");
            if($itemFlood[1] == "minute"){
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
            }else if($itemFlood[1] == "day"){
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 DAY)");
            }else{
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 HOUR)");
            }
            $floodItem = $tableFlood->fetchAll($select);
            if(engine_count($floodItem) && $itemFlood[0] <= engine_count($floodItem)){
                $message = Engine_Api::_()->core()->floodCheckMessage($itemFlood,$this->view);
                $form->addError($message);
                return;
            }
        }

        // Process
        $table = Engine_Api::_()->getItemTable('travel');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            // Create travel
            $values = $form->getValues();


            if (isset($values['networks'])) {
                $network_privacy = 'network_'. implode(',network_', $values['networks']);
                $values['networks'] = implode(',', $values['networks']);
            }


            if( empty($values['auth_view']) ) {
                $values['auth_view'] = 'everyone';
            }
            if( empty($values['auth_comment']) ) {
                $values['auth_comment'] = 'everyone';
            }

            $values = array_merge($values, array(
                'owner_type' => $viewer->getType(),
                'owner_id' => $viewer->getIdentity(),
                'view_privacy' => $values['auth_view'],
            ));

            $travel = $table->createRow();
            if (is_null($values['subcat_id']))
              $values['subcat_id'] = 0;
            if (is_null($values['subsubcat_id']))
              $values['subsubcat_id'] = 0;
            $travel->setFromArray($values);
            $travel->save();

            // Set photo
            if( !empty($values['photo']) ) {
                $travel->setPhoto($form->photo);
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $tags = array_filter(array_map("trim", $tags));
            $travel->tags()->addTagMaps($viewer, $tags);

            // Add fields
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($travel);
            $customfieldform->saveValues();

            // Set privacy
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach( $roles as $i => $role ) {
                $auth->setAllowed($travel, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($travel, $role, 'comment', ($i <= $commentMax));
            }

            // Commit
            $db->commit();
        } catch( Exception $e ) {
            return $this->exceptionWrapper($e, $form, $db);
        }

        $db->beginTransaction();
        try {

            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $travel, 'travel_new', '', array('privacy' => isset($values['networks'])? $network_privacy : null));

            if( $action != null ) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $travel);
            }
            $db->commit();
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        // Redirect
        $allowedUpload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'travel', 'photo');
        if( $allowedUpload ) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'success', 'travel_id' => $travel->travel_id), 'travel_specific', true);
        } else {
            return $this->_helper->redirector->gotoUrl($travel->getHref(), array('prependBase' => false));
        }
    }

    public function editAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $travel = Engine_Api::_()->getItem('travel', $this->_getParam('travel_id'));
        if( !Engine_Api::_()->core()->hasSubject('travel') ) {
            Engine_Api::_()->core()->setSubject($travel);
        }
        $this->view->travel = $travel;
        $this->view->category_id = (isset($travel->category_id) && $travel->category_id != 0) ? $travel->category_id : ((isset($_POST['category_id']) && $_POST['category_id'] != 0) ? $_POST['category_id'] : 0);
        $this->view->subcat_id = (isset($travel->subcat_id) && $travel->subcat_id != 0) ? $travel->subcat_id : ((isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0) ? $_POST['subcat_id'] : 0);
        $this->view->subsubcat_id = (isset($travel->subsubcat_id) && $travel->subsubcat_id != 0) ? $travel->subsubcat_id : ((isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0) ? $_POST['subsubcat_id'] : 0);
        // Check auth
        if( !$this->_helper->requireSubject()->isValid() ) {
            return;
        }
        if( !$this->_helper->requireAuth()->setAuthParams($travel, $viewer, 'edit')->isValid() ) {
            return;
        }

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('travel_main', array(), 'travel_main_manage');


        // Prepare form
        $this->view->form = $form = new Travel_Form_Edit(array(
            'item' => $travel
        ));

        $form->removeElement('photo');

        /*
        if( isset($travel->photo_id) &&
            $travel->photo_id != 0 &&
            !$travel->getPhoto($travel->photo_id) ) {
          $travel->addPhoto($travel->photo_id);
        }
        */

        $this->view->album = $album = $travel->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(100);

        foreach( $paginator as $photo ) {
            $subform = new Travel_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
            $subform->removeElement('title');
            $subform->populate($photo->toArray());
            $form->addSubForm($subform, $photo->getGuid());
            $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
        }

        // Save travel entry
        $saved = $this->_getParam('saved');
        if( !$this->getRequest()->isPost() || $saved ) {

            if( $saved ) {
                $url = $this->_helper->url->url(array('user_id' => $viewer->getIdentity(), 'travel_id' => $travel->getIdentity()), 'travel_entry_view');
                $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved. Click %s to view your listing.", '<a href="' . $url . '">here</a>');
                $form->addNotice($savedChangesNotice);
            }

            // prepare tags
            $travelTags = $travel->tags()->getTagMaps();
            //$form->getSubForm('custom')->saveValues();

            $tagString = '';
            foreach( $travelTags as $tagmap ) {
                if( $tagString !== '' ) $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            $this->view->tagNamePrepared = $tagString;
            $form->tags->setValue($tagString);

            // etc
            $form->populate($travel->toArray());


            if (Engine_Api::_()->authorization()->isAllowed('travel', Engine_Api::_()->user()->getViewer(), 'allow_network'))
                $form->networks->setValue(explode(',', $travel->networks));

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach( $roles as $role ) {
                if( $form->auth_view && 1 === $auth->isAllowed($travel, $role, 'view') ) {
                    $form->auth_view->setValue($role);
                }
                if( $form->auth_comment && 1 === $auth->isAllowed($travel, $role, 'comment') ) {
                    $form->auth_comment->setValue($role);
                }
            }

            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        // Process
        // handle save for tags
        $values = $form->getValues();
        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            if (isset($values['networks'])) {
                $network_privacy = 'network_'. implode(',network_', $values['networks']);
                $values['networks'] = implode(',', $values['networks']);
            }

            if( empty($values['auth_view']) ) {
                $values['auth_view'] = 'everyone';
            }
            $values['view_privacy'] = $values['auth_view'];
            $travel->setFromArray($values);
            $travel->modified_date = date('Y-m-d H:i:s');

            $travel->tags()->setTagMaps($viewer, $tags);
            $travel->save();

            $cover = $values['cover'];

            // Process
            foreach( $paginator as $photo ) {
                $subform = $form->getSubForm($photo->getGuid());
                $subValues = $subform->getValues();
                $subValues = $subValues[$photo->getGuid()];
                unset($subValues['photo_id']);

                if( isset($cover) && $cover == $photo->photo_id ) {
                    $travel->photo_id = $photo->file_id;
                    $travel->save();
                }

                if( isset($subValues['delete']) && $subValues['delete'] == '1' ) {
                    if( $travel->photo_id == $photo->file_id ) {
                        $travel->photo_id = 0;
                        $travel->save();
                    }
                    $photo->delete();
                } else {
                    $photo->setFromArray($subValues);
                    $photo->save();
                }
            }

            // Save custom fields
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($travel);
            $customfieldform->saveValues();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if( !empty($values['auth_view']) ) {
                $authView = $values['auth_view'];
            } else {
                $authView = "everyone";
            }
            $viewMax = array_search($authView, $roles);

            foreach( $roles as $i => $role ) {
                $auth->setAllowed($travel, $role, 'view', ($i <= $viewMax));
            }

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if( !empty($values['auth_comment']) ) {
                $authComment = $values['auth_comment'];
            } else {
                $authComment = "everyone";
            }
            $commentMax = array_search($authComment, $roles);

            foreach( $roles as $i=>$role ) {
                $auth->setAllowed($travel, $role, 'comment', ($i <= $commentMax));
            }

            $db->commit();

        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach( $actionTable->getActionsByObject($travel) as $action ) {
                $action->privacy = isset($values['networks'])? $network_privacy : null;
                $action->save();
                $actionTable->resetActivityBindings($action);
            }

            $db->commit();
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'travel_general', true);
    }

    public function deleteAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $travel = Engine_Api::_()->getItem('travel', $this->getRequest()->getParam('travel_id'));
        if( !$this->_helper->requireAuth()->setAuthParams($travel, null, 'delete')->isValid() ) return;

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Travel_Form_Delete();

        if( !$travel ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Travel listing doesn't exist or not authorized to delete");
            return;
        }

        if( !$this->getRequest()->isPost() ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $db = $travel->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $travel->delete();
            $db->commit();
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your travel listing has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'travel_general', true),
            'messages' => Array($this->view->message)
        ));
    }

    public function closeAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $travel = Engine_Api::_()->getItem('travel', $this->_getParam('travel_id'));
        if( !Engine_Api::_()->core()->hasSubject('travel') ) {
            Engine_Api::_()->core()->setSubject($travel);
        }
        $this->view->travel = $travel;

        // Check auth
        if( !$this->_helper->requireSubject()->isValid() ) {
            return;
        }
        if( !$this->_helper->requireAuth()->setAuthParams($travel, $viewer, 'edit')->isValid() ) {
            return;
        }

        // @todo convert this to post only

        $table = $travel->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $travel->closed = $this->_getParam('closed');
            $travel->save();

            $db->commit();
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        if( !($returnUrl = $this->_getParam('return_url')) ) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'travel_general', true);
        } else {
            return $this->_redirect($returnUrl, array('prependBase' => false));
        }
    }

    public function successAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('travel_main', array(), 'travel_main_manage');


        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->travel = $travel = Engine_Api::_()->getItem('travel', $this->_getParam('travel_id'));

        if( $viewer->getIdentity() != $travel->owner_id ) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if( $this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true ) {
            return $this->_redirect("travels/photo/upload/subject/travel_".$this->_getParam('travel_id'));
        }
    }

    public function uploadPhotoAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->_helper->layout->disableLayout();

        if( !Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') ) {
            return false;
        }

        if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

        if( !$this->_helper->requireUser()->checkRequire() ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if( !$this->getRequest()->isPost() ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        if( !isset($_FILES['userfile']) || !is_uploaded_file($_FILES['userfile']['tmp_name']) ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            ));
            $photo->save();

            $photo->setPhoto($_FILES['userfile']);

            $this->view->status = true;
            $this->view->name = $_FILES['userfile']['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            $table = Engine_Api::_()->getDbtable('albums', 'album');
            $album = $table->getSpecialAlbum($viewer, 'travel');

            $photo->album_id = $album->album_id;
            $photo->save();

            if( !$album->photo_id ) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($photo, 'everyone', 'view', true);
            $auth->setAllowed($photo, 'everyone', 'comment', true);
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);

            $photo->order = $photo->photo_id;
            $photo->save();

            $db->commit();

        } catch( Album_Model_Exception $e ) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = $this->view->translate($e->getMessage());
            throw $e;
            return;

        } catch( Exception $e ) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            throw $e;
            return;
        }
    }
    
    public function subcategoryAction() {

      $category_id = $this->_getParam('category_id', null);
      $CategoryType = $this->_getParam('type', null);
      $selected = $this->_getParam('selected', null);
      if ($category_id) {
        $categoryTable = Engine_Api::_()->getDbtable('categories', 'travel');
        $category_select = $categoryTable->select()
                                        ->from($categoryTable->info('name'))
                                        ->where('subcat_id = ?', $category_id);
        $subcategory = $categoryTable->fetchAll($category_select);
        $count_subcat = engine_count($subcategory->toarray());

        $data = '';
        if ($subcategory && $count_subcat) {
          if ($CategoryType == 'search') {
            $data .= '<option value="0">' . Zend_Registry::get('Zend_Translate')->_("Choose 2nd Level Category") . '</option>';
            foreach ($subcategory as $category) {
              $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '" >' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
            }
          } else {
            $data .= '<option value=""></option>';
            foreach ($subcategory as $category) {
              $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '" >' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
            }

          }
        }
      } else
        $data = '';
      echo $data;die;
    }

    public function subsubcategoryAction() {

      $category_id = $this->_getParam('subcategory_id', null);
      $CategoryType = $this->_getParam('type', null);
      $selected = $this->_getParam('selected', null);
      if ($category_id) {
        $categoryTable = Engine_Api::_()->getDbtable('categories', 'travel');
        $category_select = $categoryTable->select()
          ->from($categoryTable->info('name'))
          ->where('subsubcat_id = ?', $category_id);
        $subcategory = $categoryTable->fetchAll($category_select);
        $count_subcat = engine_count($subcategory->toarray());

        $data = '';
        if ($subcategory && $count_subcat) {
          $data .= '<option value=""></option>';
          foreach ($subcategory as $category) {
            $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '">' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
          }

        }
      } else
        $data = '';
      echo $data;
      die;
    }
}
