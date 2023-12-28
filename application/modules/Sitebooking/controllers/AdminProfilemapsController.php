<?php
/**
 * 
 */
class Sitebooking_AdminProfilemapsController extends Core_Controller_Action_Admin
{
  
  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_categories');

    $this->view->childNavigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_categories', array(), 'sitebooking_admin_main_profilemaps');              

    //GET FIELD OPTION TABLE NAME
    $tableFieldOptions = Engine_Api::_()->getDbtable('options', 'sitebooking');

    //GET TOTAL PROFILES
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitebooking_ser');
    $this->view->totalProfileTypes = 1;
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $this->view->totalProfileTypes = Count($options);
    }

    $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitebooking');
    $categories = array();
    $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order', 'profile_type'), null, 0, 0, 1);
    foreach ($category_info as $value) {

      $cat_profile_type_label = '---';
      if (!empty($value->profile_type)) {
        $cat_profile_type_label = $tableFieldOptions->getProfileTypeLabel($value->profile_type);
      }

      $sub_cat_array = array();

      $categories[] = $category_array = array(
        'category_id' => $value->category_id,
        'category_name' => $value->category_name,
        'order' => $value->cat_order,
        'sub_categories' => $sub_cat_array,
        'cat_profile_type_id' => $value->profile_type,
        'cat_profile_type_label' => $cat_profile_type_label,
      );
       
      
    }
    $this->view->categories = $categories;
  }

  public function mapAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET CATEGORY ID
    $this->view->category_id = $category_id = $this->_getParam('category_id');

    //GENERATE THE FORM
    $this->view->form = $form = new Sitebooking_Form_Admin_Profilemaps_Map();

    //GET MAPPING ITEM
    $category = Engine_Api::_()->getItem('sitebooking_category', $category_id);

    //POST DATA
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET DATA
      $values = $form->getValues();

      //GET SERVICE TABLE
      $serviceTable = Engine_Api::_()->getDbtable('sers', 'sitebooking');

      //BEGIN TRANSCATION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        $category->profile_type = $values['profile_type'];
        $category->save();
         
        //IF YES BUTTON IS CLICKED THEN CHANGE MAPPING OF ALL SERVICES
        if (isset($_POST['yes_button'])) {

          
          //SELECT SERVICES WHICH HAVE THIS CATEGORY AND THIS PROFILE TYPE
          $servicesIds = $serviceTable->getMappedSiteservice($category_id);
          

          if (!empty($servicesIds)) {
            foreach ($servicesIds as $service) {
              $ser_id = $service['ser_id'];

              //GET FIELD VALUE TABLE
              $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitebooking_ser', 'values');

              //DELETE ALL MAPPING VALUES FROM FIELD TABLES
              Engine_Api::_()->fields()->getTable('sitebooking_ser', 'values')->delete(array('item_id = ?' => $ser_id));
              Engine_Api::_()->fields()->getTable('sitebooking_ser', 'search')->delete(array('item_id = ?' => $ser_id));

              //PUT NEW PROFILE TYPE
              $fieldvalueTable->insert(array(
                'item_id' => $ser_id,
                'field_id' => Engine_Api::_()->getDbTable('metas', 'sitebooking')->defaultProfileId(),
                'index' => 0,
                'value' => $category->profile_type,
              ));

              $serviceTable->update(array('profile_type' => $category->profile_type), array('ser_id = ?' => $ser_id));
            }
          }
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been done successfully.'))
      ));
    }

    $this->renderScript('admin-profilemaps/map.tpl');
  }   


  public function editAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET CATEGORY ID
    $this->view->category_id = $category_id = $this->_getParam('category_id');

    //GET PROFILE TYPE
    $old_profile_type_id = $this->_getParam('profile_type');

    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitebooking_ser');

    $this->view->totalProfileTypes = 1;
    

    //GENERATE THE FORM
    $this->view->form = $form = new Sitebooking_Form_Admin_Profilemaps_Edit();

    //POST DATA
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET DATA
      $values = $form->getValues();
      $new_profile_type_id = $values['profile_type'];

      if ($old_profile_type_id != $new_profile_type_id) {

        //BEGIN TRANSCATION
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {

          //GET MAPPING ITEM
          $category = Engine_Api::_()->getItem('sitebooking_category', $category_id);

          //GET SERVICE TABLE
          $sitebookingTable = Engine_Api::_()->getDbtable('sers', 'sitebooking');

          //FOR CATEGORY
          if ($category->first_level_category_id == 0 && $category->second_level_category_id == 0) {
            $select = $sitebookingTable->select()
                ->from($sitebookingTable->info('name'), array('ser_id'))
                ->where('category_id = ?', $category->category_id)
                ->where('first_level_category_id = ?', 0)
            ;
          }
          

          $category->profile_type = $new_profile_type_id;
          $category->save();

          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been edited successfully.'))
      ));
    }
  }


  public function removeAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET MAPPING ID
    $this->view->category_id = $category_id = $this->_getParam('category_id');

    //GET CHILD CATEGORIES
    $categoryTable = Engine_Api::_()->getDbTable('categories', 'sitebooking');
    

    //GET MAPPING ITEM
    $this->view->category = $category = Engine_Api::_()->getItem('sitebooking_category', $category_id);

    //POST DATA
    if ($this->getRequest()->isPost()) {

      //BEGIN TRANSCATION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //finding all services affiliated to removed profile_type and insert 0 in all found service profile type 
        $service = Engine_Api::_()->getItemTable('sitebooking_ser');
        $serviceTableName  = $service->info('name');
        $select = $service->select();
        $sql = $select->where($serviceTableName . '.profile_type = ? ', $category->profile_type)
          ->where($serviceTableName . '.category_id = ? ', $category->category_id);

        $data = $service->fetchAll($sql);    

        foreach ($data as $key => $value) {
          $value->profile_type = 0;
          $value->save();
        }
        //DELETE MAPPING
        $category->profile_type = 0;
        $category->save();


        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been deleted successfully!'))
      ));
    }
    $this->renderScript('admin-profilemaps/remove.tpl');
  }


}
?>