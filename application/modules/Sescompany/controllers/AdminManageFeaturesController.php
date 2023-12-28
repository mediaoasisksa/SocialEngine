<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManageFeaturesController.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_AdminManageFeaturesController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_landingpagesettings');
    
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main_landingpagesettings', array(), 'sescompany_admin_main_landingpagefeatures');

    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('features', 'sescompany')->getFeatures();
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $db->query("DELETE FROM engine4_sescompany_features WHERE banner_id = " . $value);
        }
      }
    }
    $page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
  }
  
  public function createAction() {
  
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_landingpagesettings');
    
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main_landingpagesettings', array(), 'sescompany_admin_main_landingpagefeatures');
    
    $this->view->feature_id = $feature_id = $this->_getParam('feature_id', false);
      
    $this->view->form = $form = new Sescompany_Form_Admin_Feature_Create();
    
    if ($feature_id) {
      //$form->setTitle("Edit HTML5 Video Background");
      $form->submit->setLabel('Save Changes');
      $form->setTitle("Edit Feature");
      $form->setDescription("Below, edit the details for the feature.");
      $feature = Engine_Api::_()->getItem('sescompany_feature', $feature_id);
      $form->populate($feature->toArray());
    }
    
    if ($this->getRequest()->isPost()) {
    
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $table = Engine_Api::_()->getDbtable('features', 'sescompany');
      $db = $table->getAdapter();
      $db->beginTransaction();
      try {
        
        $values = $form->getValues();
        if (!isset($feature))
          $feature = $table->createRow();
				$feature->enabled = 1;
        $feature->setFromArray($values);
				$feature->save();
				
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
          $storage = Engine_Api::_()->getItemTable('storage_file');
          $filename = $storage->createFile($form->file, array(
              'parent_id' => $feature->feature_id,
              'parent_type' => 'sescompany_feature',
              'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
          ));
          // Remove temporary file
          @unlink($file['tmp_name']);
          $feature->file_id = $filename->file_id;
        }
        $feature->save();
        $db->commit();
        
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('feature is successfully created.')
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
    $feature_id = $this->_getParam('feature_id');

    // Check post
    if ($this->getRequest()->isPost()) {
      $feature = Engine_Api::_()->getItem('sescompany_feature', $feature_id);
      if ($feature->file_id) {
        $item = Engine_Api::_()->getItem('storage_file', $feature->file_id);
        if ($item->storage_path) {
          @unlink($item->storage_path);
          $item->remove();
        }
      }
      $feature->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Feature Deleted Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage-features/delete.tpl');
  }
  
  public function enabledAction() {

    $id = $this->_getParam('id');

    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sescompany_feature', $id);
      $item->enabled = !$item->enabled;
      $item->save();
    }
    $this->_redirect('admin/sescompany/manage-features');
  }
}