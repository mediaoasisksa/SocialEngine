<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManageClientsController.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_AdminManageClientsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_landingpagesettings');
    
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main_landingpagesettings', array(), 'sescompany_admin_main_landingpageclients');

    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('clients', 'sescompany')->getClients();
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $db->query("DELETE FROM engine4_sescompany_clients WHERE banner_id = " . $value);
        }
      }
    }
    $page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
  }
  
  public function createAction() {
  
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_landingpagesettings');
    
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main_landingpagesettings', array(), 'sescompany_admin_main_landingpageclients');
    
    $this->view->client_id = $client_id = $this->_getParam('client_id', false);
      
    $this->view->form = $form = new Sescompany_Form_Admin_Client_Create();
    
    if ($client_id) {
      //$form->setTitle("Edit HTML5 Video Background");
      $form->submit->setLabel('Save Changes');
      $form->setTitle("Edit Client");
      $form->setDescription("Below, edit the details for the client.");
      $client = Engine_Api::_()->getItem('sescompany_client', $client_id);
      $form->populate($client->toArray());
    }
    
    if ($this->getRequest()->isPost()) {
    
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $table = Engine_Api::_()->getDbtable('clients', 'sescompany');
      $db = $table->getAdapter();
      $db->beginTransaction();
      try {
        
        $values = $form->getValues();
        if (!isset($client))
          $client = $table->createRow();
				$client->enabled = 1;
        $client->setFromArray($values);
				$client->save();
				
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
          $storage = Engine_Api::_()->getItemTable('storage_file');
          $filename = $storage->createFile($form->file, array(
              'parent_id' => $client->client_id,
              'parent_type' => 'sescompany_client',
              'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
          ));
          // Remove temporary file
          @unlink($file['tmp_name']);
          $client->file_id = $filename->file_id;
        }
        $client->save();
        $db->commit();
        
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('client is successfully created.')
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
    $client_id = $this->_getParam('client_id');

    // Check post
    if ($this->getRequest()->isPost()) {
      $client = Engine_Api::_()->getItem('sescompany_client', $client_id);
      if ($client->file_id) {
        $item = Engine_Api::_()->getItem('storage_file', $client->file_id);
        if ($item->storage_path) {
          @unlink($item->storage_path);
          $item->remove();
        }
      }
      $client->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Client Deleted Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage-clients/delete.tpl');
  }
  
  public function enabledAction() {

    $id = $this->_getParam('id');

    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sescompany_client', $id);
      $item->enabled = !$item->enabled;
      $item->save();
    }
    $this->_redirect('admin/sescompany/manage-clients');
  }
}