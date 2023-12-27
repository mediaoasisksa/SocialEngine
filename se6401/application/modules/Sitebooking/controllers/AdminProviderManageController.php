<?php
class Sitebooking_AdminProviderManageController extends Core_Controller_Action_Admin
{
	function indexAction()
	{

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_provider_manage');
    $this->view->form = $form = new Sitebooking_Form_Admin_Manage_Filter();
    $this->view->search = 0;
    $this->view->owner = '';
    $this->view->title = '';
    $this->view->sponsored = '';
    $this->view->newlabel = '';
    $this->view->approved = '';
    $this->view->verified = '';
    $this->view->featured = '';
    $this->view->status = '';
    $this->view->providerbrowse = '';
    
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    $tableProvider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $providerTableName = $tableProvider->info('name');

    //MAKE QUERY
    $select = $tableProvider->select()
            ->order($providerTableName . '.creation_date DESC')
            ->setIntegrityCheck(false)
            ->from($providerTableName)
            ->joinLeft($tableUserName, "$providerTableName.owner_id = $tableUserName.user_id", 'username')
            ->group("$providerTableName.pro_id");

    if(isset($_GET['search'])){

      if (!empty($_GET['owner'])) {
        $owner = $this->view->owner = $_GET['owner'];
        $select->where("$tableUserName.username  LIKE '%$owner%' OR $tableUserName.displayname  LIKE '%$owner%'");
      }

     if (!empty($_GET['title'])) {
        $this->view->title = $_GET['title'];
        $select->where($providerTableName . '.title  LIKE ?', '%' . $_GET['title'] . '%');
      }

      if (!empty($_GET['sponsored'])) {
          $this->view->sponsored = $_GET['sponsored'];
          $_GET['sponsored']--;

          $select->where($providerTableName . '.sponsored = ? ', $_GET['sponsored']);
      }

      if (!empty($_GET['approved'])) {
          $this->view->approved = $_GET['approved'];
          $_GET['approved']--;
          $select->where($providerTableName . '.approved = ? ', $_GET['approved']);
      }

      if (!empty($_GET['verified'])) {
          $this->view->verified = $_GET['verified'];
          $_GET['verified']--;
          $select->where($providerTableName . '.verified = ? ', $_GET['verified']);
      }

      if (!empty($_GET['featured'])) {
          $this->view->featured = $_GET['featured'];
          $_GET['featured']--;
          $select->where($providerTableName . '.featured = ? ', $_GET['featured']);
      }

      if (!empty($_GET['newlabel'])) {
          $this->view->newlabel = $_GET['newlabel'];
          $_GET['newlabel']--;
          $select->where($providerTableName . '.newlabel = ? ', $_GET['newlabel']);
      }

      if (!empty($_GET['status'])) {
          $this->view->status = $_GET['status'];
          $_GET['status']--;
          $select->where($providerTableName . '.status = ? ', $_GET['status']);
      }

      if (!empty($_GET['providerbrowse'])) {
          $this->view->eventbrowse = $_GET['providerbrowse'];
          $_GET['providerbrowse']--;
          if ($_GET['providerbrowse'] == 0) {
              $select->order($providerTableName . '.view_count DESC');
          } else {
              $select->order($providerTableName . '.pro_id DESC');
          }
      }
      $this->view->search = 1;

    }
    $this->view->formValues = array_filter($_GET);
    $this->view->paginator = Zend_Paginator::factory($select);
    $items_per_page = 10;
    $this->view->paginator->setItemCountPerPage($items_per_page);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
	}

  function multiDeleteAction()
  {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $provider = Engine_Api::_()->getItem('sitebooking_pro', $value);
          $provider->delete();      
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

	public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->pro_id=$id;
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $provider = Engine_Api::_()->getItem('sitebooking_pro', $id);
  			$provider->delete();
      	$db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        
      }

      $this->_forward('success', 'utility', 'core', array(
          'closeSmoothbox' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('This service provider has been deleted successfully.')),
          'format' => 'smoothbox'
      ));
    }
  }
  
  function approvedAction()
  {
    $pro_id = $this->_getParam('pro_id');
    
    // if provider disapproved than its all service will be disapproved also
    if(!empty($pro_id)) {
      $service = Engine_Api::_()->getItemTable('sitebooking_ser');
      $serviceTableName  = $service->info('name');
      $select = $service->select();
      $sql = $select->where($serviceTableName . '.parent_id = ? ', $pro_id);
      $data = $service->fetchAll($sql);    
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);
      $owner = Engine_Api::_()->getItem('user', $provider->owner_id);
      $viewer = Engine_Api::_()->user()->getViewer();
      if($provider->approved == "1"){
        $provider->approved = "0";
         // Send mail and notifications to provider
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $provider, 'sitebooking_provider_disapproved');
        Engine_Api::_()->sitebooking()->sendProviderDisapproveMail($owner,$provider,$viewer);
      
      if(!empty($pro_id)) {
        foreach ($data as $key => $value) {
          $value->approved = 0;
          $value->save();
        }
        $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
        $bookingTableName = $bookingTable->info('name');

        //rejecting this service
        $select = $bookingTable->select();
        $sql = $select->where($bookingTableName . ".pro_id = ?", $pro_id)
               ->where($bookingTableName . ".status = 'booked' OR $bookingTableName.status = 'pending'");

        $bookingData = $bookingTable->fetchAll($sql);

        foreach( $bookingData as $item ) {
          $item->status = "rejected";
          $item->save();
        }
      }

      }
      else{
        $provider->approved = "1";
        // Send mail and notifications to provider
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $provider, 'sitebooking_provider_approved');
        Engine_Api::_()->sitebooking()->sendProviderApproveMail($owner,$provider,$viewer);

        if(!empty($pro_id)) {
          foreach ($data as $key => $value) {
            $value->approved = 1;
            $value->save();
          }
        }

      }
      $provider->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/provider-manage');
  }

  function verifiedAction()
  {
    $pro_id = $this->_getParam('pro_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);
      if($provider->verified == "1"){
        $provider->verified = "0";

      }
      else{
        $provider->verified = "1";
      }
      $provider->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/provider-manage');
  }

  function featuredAction()
  {
    $pro_id = $this->_getParam('pro_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);
      if($provider->featured == "1"){
        $provider->featured = "0";

      }
      else{
        $provider->featured = "1";
      }
      $provider->save();
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/provider-manage');
  }

  function sponsoredAction()
  {
    $pro_id = $this->_getParam('pro_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);
      if($provider->sponsored == "1"){
        $provider->sponsored = "0";

      }
      else{
        $provider->sponsored = "1";
      }
      $provider->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/provider-manage');
  }

  function newlabelAction()
  {
    $pro_id = $this->_getParam('pro_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);
      if($provider->newlabel == "1"){
        $provider->newlabel = "0";

      }
      else{
        $provider->newlabel = "1";
      }
      $provider->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/provider-manage');
  }

  function hotAction()
  {
    $pro_id = $this->_getParam('pro_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $provider = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);
      if($provider->hot == "1"){
        $provider->hot = "0";

      }
      else{
        $provider->hot = "1";
      }
      $provider->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitebooking/provider-manage');
  }
}
?>