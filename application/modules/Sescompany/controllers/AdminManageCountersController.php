<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManageCountersController.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_AdminManageCountersController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_landingpagesettings');
    
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main_landingpagesettings', array(), 'sescompany_admin_main_landingpagecounters');

    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('counters', 'sescompany')->getCounters();
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $db->query("DELETE FROM engine4_sescompany_counters WHERE banner_id = " . $value);
        }
      }
    }
    $page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
  }
  
  public function createAction() {
  
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_landingpagesettings');
    
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main_landingpagesettings', array(), 'sescompany_admin_main_landingpagecounters');
    
    $this->view->counter_id = $counter_id = $this->_getParam('counter_id', false);
      
    $this->view->form = $form = new Sescompany_Form_Admin_Counter_Create();
    
    if ($counter_id) {
      //$form->setTitle("Edit HTML5 Video Background");
      $form->submit->setLabel('Save Changes');
      $form->setTitle("Edit Counter");
      $form->setDescription("Below, edit the details for the counter.");
      $counter = Engine_Api::_()->getItem('sescompany_counter', $counter_id);
      $form->populate($counter->toArray());
    }
    
    if ($this->getRequest()->isPost()) {
    
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $table = Engine_Api::_()->getDbtable('counters', 'sescompany');
      $db = $table->getAdapter();
      $db->beginTransaction();
      try {
        
        $values = $form->getValues();
        if (!isset($counter))
          $counter = $table->createRow();
				$counter->enabled = 1;
        $counter->setFromArray($values);
				$counter->save();
				
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
          $storage = Engine_Api::_()->getItemTable('storage_file');
          $filename = $storage->createFile($form->file, array(
              'parent_id' => $counter->counter_id,
              'parent_type' => 'sescompany_counter',
              'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
          ));
          // Remove temporary file
          @unlink($file['tmp_name']);
          $counter->file_id = $filename->file_id;
        }
        $counter->save();
        $db->commit();
        
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('counter is successfully created.')
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function deleteAction() {

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $counter_id = $this->_getParam('counter_id');

    // Check post
    if ($this->getRequest()->isPost()) {
      $counter = Engine_Api::_()->getItem('sescompany_counter', $counter_id);
      if ($counter->file_id) {
        $item = Engine_Api::_()->getItem('storage_file', $counter->file_id);
        if ($item->storage_path) {
          @unlink($item->storage_path);
          $item->remove();
        }
      }
      $counter->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Counter Deleted Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage-counters/delete.tpl');
  }
  
  public function enabledAction() {

    $id = $this->_getParam('id');

    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sescompany_counter', $id);
      $item->enabled = !$item->enabled;
      $item->save();
    }
    $this->_redirect('admin/sescompany/manage-counters');
  }
}