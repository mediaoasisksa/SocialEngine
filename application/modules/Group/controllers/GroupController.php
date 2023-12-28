<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: GroupController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_GroupController extends Core_Controller_Action_Standard
{
    public function init()
    {
        if( 0 !== ($group_id = (int) $this->_getParam('group_id')) &&
            null !== ($group = Engine_Api::_()->getItem('group', $group_id)) ) {
            Engine_Api::_()->core()->setSubject($group);
        }

        $this->_helper->requireUser();
        $this->_helper->requireSubject('group');
    }

    public function editAction()
    {
        if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
            return;
        }

        $group = Engine_Api::_()->core()->getSubject();
        
        $this->view->category_id = (isset($group->category_id) && $group->category_id != 0) ? $group->category_id : ((isset($_POST['category_id']) && $_POST['category_id'] != 0) ? $_POST['category_id'] : 0);
        $this->view->subcat_id = (isset($group->subcat_id) && $group->subcat_id != 0) ? $group->subcat_id : ((isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0) ? $_POST['subcat_id'] : 0);
        $this->view->subsubcat_id = (isset($group->subsubcat_id) && $group->subsubcat_id != 0) ? $group->subsubcat_id : ((isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0) ? $_POST['subsubcat_id'] : 0);
        
        $officerList = $group->getOfficerList();
        $this->view->form = $form = new Group_Form_Edit();

        // Populate with categories
        $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
        asort($categories, SORT_LOCALE_STRING);
        $categoryOptions = array('0' => '');
        foreach( $categories as $k => $v ) {
            $categoryOptions[$k] = $v;
        }
        $form->category_id->setMultiOptions($categoryOptions);

        if( engine_count($form->category_id->getMultiOptions()) <= 1 ) {
            $form->removeElement('category_id');
        }

        if( !$this->getRequest()->isPost() ) {
            // Populate auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('officer', 'member', 'registered', 'everyone');
            $actions = array('event', 'view', 'comment', 'invite', 'photo', 'blog', 'video', 'poll');
            $perms = array();
            foreach( $roles as $roleString ) {
                $role = $roleString;
                if( $role === 'officer' ) {
                    $role = $officerList;
                }

                foreach( $actions as $action ) {
                    if( $auth->isAllowed($group, $role, $action) ) {
                        $perms['auth_' . $action] = $roleString;
                    }
                }
            }

            $form->populate($group->toArray());
            $form->populate($perms);
            if (Engine_Api::_()->authorization()->isAllowed('group', Engine_Api::_()->user()->getViewer(), 'allow_network'))
                $form->networks->setValue(explode(',', $group->networks));
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        // Process
        $db = Engine_Api::_()->getItemTable('group')->getAdapter();
        $db->beginTransaction();

        try {
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

            $values['view_privacy'] =  $values['auth_view'];

            // Set group info
            $group->setFromArray($values);
            $group->save();

            if( !empty($values['photo']) ) {
                $group->setPhoto($form->photo);
            }

            // Process privacy
            $auth = Engine_Api::_()->authorization()->context;

            $roles = array('officer', 'member', 'registered', 'everyone');

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_photo'], $roles);
            $eventMax = array_search(@$values['auth_event'], $roles);
            $blogMax = array_search(@$values['auth_blog'], $roles);
            $pollMax = array_search(@$values['auth_poll'], $roles);
            $videoMax = array_search(@$values['auth_video'], $roles);
            $inviteMax = array_search($values['auth_invite'], $roles);

            foreach( $roles as $i => $role ) {
                if( $role === 'officer' ) {
                    $role = $officerList;
                }
                $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
                $auth->setAllowed($group, $role, 'event', ($i <= $eventMax));
                $auth->setAllowed($group, $role, 'blog', ($i <= $blogMax));
                $auth->setAllowed($group, $role, 'poll', ($i <= $pollMax));
                $auth->setAllowed($group, $role, 'video', ($i <= $videoMax));
                $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
                // Create some auth stuff for all officers
                $auth->setAllowed($group, $role, 'topic_create', ($i <= $commentMax));
                $auth->setAllowed($group, $role, 'topic_edit', ($i <= $commentMax));
                $auth->setAllowed($group, $role, 'topic_delete', ($i <= $commentMax));
                $auth->setAllowed($group, $role, 'post_create', ($i <= $commentMax));
                $auth->setAllowed($group, $role, 'post_edit', ($i <= $commentMax));
                $auth->setAllowed($group, $role, 'post_delete', ($i <= $commentMax));
            }

            // Add auth for invited users
            $auth->setAllowed($group, 'member_requested', 'view', 1);

            // Commit
            $db->commit();
        } catch( Engine_Image_Exception $e ) {
            $db->rollBack();
            $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }


        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach( $actionTable->getActionsByObject($group) as $action ) {
                $action->privacy = isset($values['networks'])? $network_privacy : null;
                $action->save();
                $actionTable->resetActivityBindings($action);
            }

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }



        // Redirect
        if( $this->_getParam('ref') === 'profile' ) {
            $this->_redirectCustom($group);
        } else {
            $this->_redirectCustom(array('route' => 'group_general', 'action' => 'manage'));
        }
    }

    public function deleteAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $group = Engine_Api::_()->getItem('group', $this->getRequest()->getParam('group_id'));
        if( !$this->_helper->requireAuth()->setAuthParams($group, null, 'delete')->isValid()) return;

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        // Make form
        $this->view->form = $form = new Group_Form_Delete();

        if( !$group )
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Group doesn't exists or not authorized to delete");
            return;
        }

        if( !$this->getRequest()->isPost() )
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $db = $group->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $group->delete();

            $db->commit();
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected group has been deleted.');
        return $this->_forward('success' ,'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'group_general', true),
            'messages' => Array($this->view->message)
        ));
    }

    public function styleAction()
    {
        if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() )
            return;
        if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'style')->isValid() )
            return;

        $user = Engine_Api::_()->user()->getViewer();
        $group = Engine_Api::_()->core()->getSubject('group');

        // Make form
        $this->view->form = $form = new Group_Form_Style();

        // Get current row
        $table = Engine_Api::_()->getDbtable('styles', 'core');
        $select = $table->select()
            ->where('type = ?', 'group')
            ->where('id = ?', $group->getIdentity())
            ->limit(1);

        $row = $table->fetchRow($select);

        // Check post
        if( !$this->getRequest()->isPost() ) {
            $form->populate(array(
                'style' => ( null === $row ? '' : $row->style )
            ));
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        // Cool! Process
        $style = $form->getValue('style');

        // Save
        if( null == $row ) {
            $row = $table->createRow();
            $row->type = 'group';
            $row->id = $group->getIdentity();
        }

        $row->style = $style;
        $row->save();

        $this->view->draft = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.');
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => false,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'))
        ));
    }

}
