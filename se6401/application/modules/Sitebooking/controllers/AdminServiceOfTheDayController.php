<?php

class Sitebooking_AdminServiceOfTheDayController extends Core_Controller_Action_Admin {

	//ACTION FOR SERVICE OF THE DAY
  public function dayAction() {

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_serviceoftheday');

		//FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitebooking_Form_Admin_Filter();
    $page = $this->_getParam('page', 1); 

    $values = array(); 
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($values as $key => $value) {
      if (null == $value) {
        unset($values[$key]);
      }
    }
    $values = array_merge(array(
                'order' => 'start_date',
                'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

		//FETCH DATA
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('itemofthedays', 'sitebooking')->getServiceOfDayList($values, 'ser_id', 'sitebooking_ser');
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
  }

	//ACTION FOR ADDING SERVICE OF THE DAY
  public function addItemAction() {

		//SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');
    $viewer = Engine_Api::_()->user()->getViewer();
    //FORM GENERATION
    $form = $this->view->form = new Sitebooking_Form_Admin_ServiceOfTheDay_Serviceoftheday();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

			//BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        
        $table = Engine_Api::_()->getDbtable('itemofthedays', 'sitebooking');
        $select = $table->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitebooking_ser');
        $row = $table->fetchRow($select);

        if (empty($row)) {
          $row = $table->createRow();
          $row->resource_id = $values["resource_id"];
        }

        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $start = strtotime($values['starttime']);
        $end = strtotime($values['endtime']);
        date_default_timezone_set($oldTz);
        $values['starttime'] = date('Y-m-d H:i:s', $start);
        $values['endtime'] = date('Y-m-d H:i:s', $end);
    
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitebooking_ser';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Service of the Day has been added successfully.'))
      ));
    }
  }

	//ACTION FOR SERVICE SUGGESTION DROP-DOWN
  public function getitemAction() {

      
		$search_text = $this->_getParam('text', null);
		$limit = $this->_getParam('limit', 40);
		
    $data = array();

		$moduleServices = Engine_Api::_()->getItemTable('sitebooking_ser')->getDayItems($search_text, $limit=10);
    foreach ($moduleServices as $moduleService) {

			$content_photo = $this->view->itemPhoto($moduleService, 'thumb.icon');

      $data[] = array(
              'id' => $moduleService->ser_id,
              'label' => $moduleService->title,
              'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

	//ACTION FOR SERVICE DELETE ENTRY
  public function deleteItemAction() {

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $itemofthedaysTable = Engine_Api::_()->getDbtable('itemofthedays', 'sitebooking')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin-service-of-the-day/delete.tpl');
  }

  //ACTION FOR MULTI DELETE SERVICE ENTRIES
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $sitebookingitemofthedays = Engine_Api::_()->getItem('sitebooking_itemofthedays', (int) $value);
          if (!empty($sitebookingitemofthedays)) {
            $sitebookingitemofthedays->delete();
          }
        }
      }
    }
		return $this->_helper->redirector->gotoRoute(array('action' => 'day'));
  }
}
?>