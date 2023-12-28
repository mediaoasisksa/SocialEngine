
<?php

class Sitebooking_AdminSettingsController extends Core_Controller_Action_Admin {


  public function indexAction(){

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_global');

    $this->view->form = $form = new Sitebooking_Form_Admin_Settings_Global();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
      $shareArray = array();
      $share = 0;

      if(!empty($values["sitebooking_share"])) {
        $count = 0;
        foreach ($values["sitebooking_share"] as $value) {
          if($value == "facebook")
            $shareArray[$count] = "facebook";
          if($value == "twitter")
            $shareArray[$count] = "twitter";
          if($value == "linkedin")
            $shareArray[$count] = "linkedin";
          if($value == "pinterest")
            $shareArray[$count] = "pinterest";
          if($value == "share")
            $shareArray[$count] = "share";
          $count++;
        }
        $share = implode(",",$shareArray);
      }

      foreach ($values as $key => $value){
        if($key === "sitebooking_share")
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $share);
        else
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }

  }
  public function durationAction(){

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_manage_duration');
        
    $durationTable = Engine_Api::_()->getItemTable('sitebooking_duration');
    $this->view->durationItems = $durationItems = $durationTable->fetchAll($durationTable->select()->order('duration ASC'));

  }

  public function addDurationAction(){

    $this->view->form = $form = new Sitebooking_Form_Admin_Settings_Duration();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
      $values['action'] = 1;

      // checking duration 
      $durationTable = Engine_Api::_()->getItemTable('sitebooking_duration');
      $durationItem = $durationTable->fetchRow($durationTable->select()->where('duration = ?',$values['duration']));
      if(!empty($durationItem->duration)){
        return $form->addNotice('This is already exist. Try another');
      }

      $durationRow = $durationTable->createRow();
      $durationRow->setFromArray($values);
      $durationRow->save();

      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh'=> true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Duration has been added successfully.')),
        'format' => 'smoothbox'
      ));
    }
  }



  public function disableDurationAction(){
    $id = $this->_getParam('id');
    $durationItem = Engine_Api::_()->getItem('sitebooking_duration',$id);
    $durationItem->action = 0;
    $durationItem->save();

    $this->_redirect('admin/sitebooking/settings/duration');

  }

  public function enableDurationAction(){
    $id = $this->_getParam('id');
    $durationItem = Engine_Api::_()->getItem('sitebooking_duration',$id);
    $durationItem->action = 1;
    $durationItem->save();

    $this->_redirect('admin/sitebooking/settings/duration');

  }


  //ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES AND 3RD LEVEL CATEGORIES

  public function categoriesAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_categories');

    $this->view->childNavigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_categories', array(), 'sitebooking_admin_main_categories');

    //GET TASK
    if (isset($_POST['task'])) {
      $task = $_POST['task'];
    } elseif (isset($_GET['task'])) {
      $task = $_GET['task'];
    } else {
      $task = "main";
    }

    $orientation = $this->view->layout()->orientation;
    if ($orientation == 'right-to-left') {
      $this->view->directionality = 'rtl';
    } else {
      $this->view->directionality = 'ltr';
    }

    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitebooking');
    $tableCategoryName = $tableCategory->info('name');

    //GET STORAGE API
    $this->view->storage = Engine_Api::_()->storage();

    //GET SERVICE TABLE
    $tableSitebooking = Engine_Api::_()->getDbtable('sers', 'sitebooking');

    if ($task == "changeorder") {
      $divId = $_GET['divId'];
      $sitebookingOrder = explode(",", $_GET['sitebookingorder']);
      //RESORT CATEGORIES
      if ($divId == "categories") {
        for ($i = 0; $i < count($sitebookingOrder); $i++) {
          $category_id = substr($sitebookingOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      } elseif (substr($divId, 0, 7) == "subcats") {
        for ($i = 0; $i < count($sitebookingOrder); $i++) {
          $category_id = substr($sitebookingOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      } elseif (substr($divId, 0, 11) == "treesubcats") {
        for ($i = 0; $i < count($sitebookingOrder); $i++) {
          $category_id = substr($sitebookingOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      }
    }

    $categories = array();
    $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order', 'file_id'), null, 0, 0, 1);
    foreach ($category_info as $value) {
      $sub_cat_array = array();
      $subcategories = $tableCategory->getSubCategories($value->category_id);
      foreach ($subcategories as $subresults) {
        $subsubcategories = $tableCategory->getSubSubCategories($subresults->category_id);
        $treesubarrays[$subresults->category_id] = array();

        foreach ($subsubcategories as $subsubcategoriesvalues) {

          //GET TOTAL SERVICE COUNT
          $subsubcategory_sitebooking_count = $tableSitebooking->getServicesCount($subsubcategoriesvalues->category_id, 'second_level_category_id');

          $treesubarrays[$subresults->category_id][] = $treesubarray = array(
            'tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
            'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
            'count' => $subsubcategory_sitebooking_count,
            'file_id' => $subsubcategoriesvalues->file_id,
            // 'banner_id' => $subsubcategoriesvalues->banner_id,
            'order' => $subsubcategoriesvalues->cat_order
            // 'sponsored' => $subsubcategoriesvalues->sponsored
          );
        }

        //GET TOTAL SERVICES COUNT
        $subcategory_sitebooking_count = $tableSitebooking->getServicesCount($subresults->category_id, 'first_level_category_id');

        $sub_cat_array[] = $tmp_array = array(
          'sub_cat_id' => $subresults->category_id,
          'sub_cat_name' => $subresults->category_name,
          'tree_sub_cat' => $treesubarrays[$subresults->category_id],
          'count' => $subcategory_sitebooking_count,
          'file_id' => $subresults->file_id,
          // 'banner_id' => $subresults->banner_id,
          'order' => $subresults->cat_order,
          // 'sponsored' => $subresults->sponsored
        );
      }

      //GET TOTAL SERVICES COUNT
      $category_sitebooking_count = $tableSitebooking->getServicesCount($value->category_id, 'category_id');

      $categories[] = $category_array = array('category_id' => $value->category_id,
        'category_name' => $value->category_name,
        'order' => $value->cat_order,
        'count' => $category_sitebooking_count,
        'file_id' => $value->file_id,
        // 'banner_id' => $value->banner_id,
        // 'sponsored' => $value->sponsored,
        'sub_categories' => $sub_cat_array);
    }

    $this->view->categories = $categories;

    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitebooking');
    $tableCategoryName = $tableCategory->info('name');
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->category_id = $category_id = $request->getParam('category_id', 0);
    $perform = $request->getParam('perform', 'add');
    $first_level_category_id = 0;
    $second_level_category_id = 0;
    if ($category_id) {
      $category = Engine_Api::_()->getItem('sitebooking_category', $category_id);
      if ($category && empty($category->first_level_category_id)) {
        $first_level_category_id = $category->category_id;
      } elseif ($category && !empty($category->first_level_category_id)) {
        $first_level_category_id = $category['first_level_category_id'];
        $second_level_category_id = $category->category_id;
      }
    }

    if ($perform == 'add') {
      $this->view->form = $form = new Sitebooking_Form_Admin_Categories_Add();

      //CHECK POST
      if (!$this->getRequest()->isPost()) {
        return;
      }

      //CHECK VALIDITY
      if (!$form->isValid($this->getRequest()->getPost())) {

        if (empty($_POST['category_name'])) {
          $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
        }
        return;
      }

      //PROCESS
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $row_info = $tableCategory->fetchRow($tableCategory->select()->from($tableCategoryName, 'max(cat_order) AS cat_order'));
        $cat_order = $row_info['cat_order'] + 1;

        //GET CATEGORY TITLE
        $category_name = str_replace("'", "\'", trim($values['category_name']));
        $values['cat_order'] = $cat_order;
        $values['category_name'] = $category_name;

        $values['first_level_category_id'] = $first_level_category_id;
        $values['second_level_category_id'] = $second_level_category_id;

        $row = $tableCategory->createRow();
        $row->setFromArray($values);

        //UPLOAD CATEGORY PHOTO
        if (isset($_FILES['photo'])) {
          $photoFile = $row->setPhoto($form->photo, true);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $row->photo_id = $photoFile->file_id;
          }
        }

        $category_id = $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('module' => 'sitebooking', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
    } else {
      $this->view->form = $form = new Sitebooking_Form_Admin_Categories_Edit();
      $category = Engine_Api::_()->getItem('sitebooking_category', $category_id);
      $form->populate($category->toArray());

      //CHECK POST
      if (!$this->getRequest()->isPost()) {
        return;
      }

      //CHECK VALIDITY
      if (!$form->isValid($this->getRequest()->getPost())) {

        if (empty($_POST['category_name'])) {
          $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
        }
        return;
      }
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET CATEGORY TITLE
        $category_name = str_replace("'", "\'", trim($values['category_name']));

        $category->category_name = $category_name;

        $category->meta_title = $values['meta_title'];
        $category->meta_description = $values['meta_description'];
        $category->meta_keywords = $values['meta_keywords'];
        $category->category_slug = $values['category_slug'];
        $first_level_category_id = $category->first_level_category_id;
        $second_level_category_id = $category->second_level_category_id;
        if ($category_id && empty($second_level_category_id) && !empty($first_level_category_id)) {
          $first_level_category_id = $first_level_category_id;
          $second_level_category_id = 0;
        } elseif ($category_id && !empty($second_level_category_id) && !empty($first_level_category_id)) {
          $first_level_category_id = $first_level_category_id;
          $second_level_category_id = $second_level_category_id;
        }

        $category->first_level_category_id = $first_level_category_id;
        $category->second_level_category_id = $second_level_category_id;

        //UPLOAD CATEGORY PHOTO
        if (isset($_FILES['photo'])) {
          $previous_photo_id = $category->photo_id;
          $photoFile = $category->setPhoto($form->photo, true);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $category->photo_id = $photoFile->file_id;

            //DELETE PREVIOUS CATEGORY ICON
            if ($previous_photo_id) {
              $file = Engine_Api::_()->getItem('storage_file', $previous_photo_id);
              $file->delete();
            }
          }
        }

        $category->save();

        if (isset($values['removephoto']) && !empty($values['removephoto'])) {
          //DELETE CATEGORY ICON
          $file = Engine_Api::_()->getItem('storage_file', $category->photo_id);

          //UPDATE FILE ID IN CATEGORY TABLE
          $category->photo_id = 0;
          $category->save();
          $file->delete();
        }

        $db->commit();
        // return $form->addNotice("Your changes has been saved, To see the saved changes please refresh the page.");

      } catch (Exception $e) {
        $db->rollBack();
        // return $form->addError("Something Went Wrong, please refresh the page.");
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('module' => 'sitebooking', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
    }
  }
  
  //ACTION FOR MAPPING OF SERVICES
  Public function mappingCategoryAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET CATEGORY ID AND OBJECT
    $this->view->catid = $catid = $this->_getParam('category_id');

    $category = Engine_Api::_()->getItem('sitebooking_category', $catid);

    //GET CATEGORY DEPENDANCY
    $this->view->second_level_category_id = $second_level_category_id = $this->_getParam('second_level_category_id');

    //CREATE FORM
    $this->view->form = $form = new Sitebooking_Form_Admin_Settings_Mapping();

    $this->view->close_smoothbox = 0;

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    if ($this->getRequest()->isPost()) {

      //GET FORM VALUES
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET SERVICE TABLE
        $tableSitebooking = Engine_Api::_()->getDbtable('sers', 'sitebooking');
        $tableSitebookingName = $tableSitebooking->info('name');

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
        $reviewTableName = $reviewTable->info('name');

        //GET SERVICEOVERVIEW TABLE
        $serviceoverviewTable = Engine_Api::_()->getDbtable('serviceoverviews', 'sitebooking');
        $serviceoverviewTableName = $serviceoverviewTable->info('name');

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');
        $ratingTableName = $ratingTable->info('name');

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitebooking');

        //ON CATEGORY DELETE
        $rows = $tableCategory->getSubCategories($catid);
        foreach ($rows as $row) {
          $subrows = $tableCategory->getSubSubCategories($row->category_id);
          foreach ($subrows as $subrow) {
            $subrow->delete();
          }
          $row->delete();
        }

        $previous_cat_profile_type = $tableCategory->getProfileType($catid);
        $new_cat_profile_type = $tableCategory->getProfileType($values['new_category_id']);

        // SERVICES WHICH HAVE THIS CATEGORY
        if ($previous_cat_profile_type != $new_cat_profile_type && !empty($values['new_category_id'])) {
          $services = $tableSitebooking->getCategoryList($catid, 'category_id');

          foreach ($services as $service) {

            //DELETE ALL MAPPING VALUES FROM FIELD TABLES
            Engine_Api::_()->fields()->getTable('sitebooking_ser', 'values')->delete(array('item_id = ?' => $service->ser_id));
            Engine_Api::_()->fields()->getTable('sitebooking_ser', 'search')->delete(array('item_id = ?' => $service->ser_id));
            //UPDATE THE PROFILE TYPE OF ALREADY CREATED SERVICES
            $tableSitebooking->update(array('profile_type' => $new_cat_profile_type), array('ser_id = ?' => $service->ser_id));

            //REVIEW PROFILE TYPE UPDATION WORK
            $reviewIds = $reviewTable->select()
                ->from($reviewTableName, 'review_id')
                ->where('resource_id = ?', $service->ser_id)
                ->where('resource_type = ?', 'sitebooking_ser')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
            if (!empty($reviewIds)) {
              foreach ($reviewIds as $reviewId) {
                //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                Engine_Api::_()->fields()->getTable('sitebooking_ser', 'values')->delete(array('item_id = ?' => $reviewId));
                Engine_Api::_()->fields()->getTable('sitebooking_ser', 'search')->delete(array('item_id = ?' => $reviewId));
              }
            }
          }
        }

        //SERVICE TABLE CATEGORY DELETE WORK
        if (isset($values['new_category_id']) && !empty($values['new_category_id'])) {
          $tableSitebooking->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
        } else {

          $selectServices = $tableSitebooking->select()
              ->from($tableSitebooking->info('name'))
              ->where('category_id = ?', $catid);
    
          //SERVICE DELETION
          foreach ($tableSitebooking->fetchAll($selectServices) as $service) {
            $service->delete();
          }

        }

        $category->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    $this->view->close_smoothbox = 1;
  }

  //ACTION FOR DELETE THE CATEGORY
  public function deleteCategoryAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $category_id = $this->_getParam('category_id');

    $first_level_category_id = $this->_getParam('first_level_category_id');

    $this->view->category_id = $category_id;

    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitebooking');
    $tableCategoryName = $tableCategory->info('name');

    //GET SERVICE TABLE
    $tableSitebooking = Engine_Api::_()->getDbtable('sers', 'sitebooking');

    if ($this->getRequest()->isPost()) {

      //SITEBOOKING TABLE SUB-CATEGORY/3RD LEVEL DELETE WORK
      $tableSitebooking->update(array('first_level_category_id' => 0, 'second_level_category_id' => 0), array('first_level_category_id = ?' => $category_id));
      $tableSitebooking->update(array('second_level_category_id' => 0), array('second_level_category_id = ?' => $category_id));

      // $tableCategory
      $row = Engine_Api::_()->getItem('sitebooking_category', $category_id);

      $tableCategory->delete(array('first_level_category_id = ?' => $row['first_level_category_id'], 'second_level_category_id = ?' => $category_id));
      $tableCategory->delete(array('category_id = ?' => $category_id));

      //GET URL
      $url = $this->_helper->url->url(array('action' => 'categories', 'controller' => 'settings', 'perform' => 'add', 'category_id' => 0));
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRedirect' => $url,
        'parentRedirectTime' => 1,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }

    $this->renderScript('admin-settings/delete-category.tpl');
  }

  public function ratingAndReviewAction()
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_ratingandreview');

    $this->view->form = $form = new Sitebooking_Form_Admin_Settings_Ratingandreview();
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function reviewServiceManageAction()
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_review');

    $this->view->childNavigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main_review', array(), 'sitebooking_admin_main_servicemanagereviewandrating');        

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Sitebooking_Form_Admin_Settings_Servicereviewmanage();

    //REVIEWS TABLE
    $review = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
    $reviewName = $review->info('name');

    $serviceRating = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');
    $serviceRatingName = $serviceRating->info('name');

    $service = Engine_Api::_()->getDbtable('sers', 'sitebooking');
    $serviceName = $service->info('name');

    $select = $serviceRating->select();
    $select
      ->setIntegrityCheck(false)
      ->from($serviceRatingName,array("*"))
      ->joinleft($reviewName, "$serviceRatingName.ser_id = $reviewName.resource_id AND $serviceRatingName.user_id = $reviewName.user_id",array("$reviewName.resource_id","$reviewName.resource_type","$reviewName.review","$reviewName.review_id",'creation_date as review_creation_date'))
      ->joinleft($serviceName, "$serviceRatingName.ser_id = $serviceName.ser_id",array("$serviceName.title","$serviceName.parent_id","$serviceName.slug","$serviceName.creation_date"))
      ->where("$reviewName.resource_type is NULL OR $reviewName.resource_type = 'sitebooking_ser'");
    $select->group($serviceRatingName.'.rating_id')
    ->order($reviewName . '.creation_date DESC');

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {

      if(!empty($_POST["serviceTitle"])) {
      $serviceTitle = $_POST["serviceTitle"];
      $select->where("$serviceName.title like '%$serviceTitle%'");
      }
      if($_POST["rating"] > 0) {
      $rating = $_POST["rating"];
      $select->where("$serviceRatingName.rating = $rating");
      }

    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    $items_per_page = 10;

    $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);


  }

  public function reviewProviderManageAction()
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_review');

    $this->view->childNavigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main_review', array(), 'sitebooking_admin_main_providermanagereviewandrating');
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Sitebooking_Form_Admin_Settings_Providerreviewmanage();

    //REVIEWS TABLE
    $review = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
    $reviewName = $review->info('name');
    $providerRating = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');
    $providerRatingName = $providerRating->info('name');
    $provider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $providerName = $provider->info('name');

    $select = $providerRating->select();
    $select
      ->setIntegrityCheck(false)
      ->from($providerRatingName,array("*"))
      ->joinleft($reviewName, "$providerRatingName.pro_id = $reviewName.resource_id AND $providerRatingName.user_id = $reviewName.user_id",array("$reviewName.resource_id","$reviewName.resource_type","$reviewName.review","$reviewName.review_id",'creation_date as review_creation_date'))
      ->joinleft($providerName, "$providerRatingName.pro_id = $providerName.pro_id",array("$providerName.title","$providerName.owner_id","$providerName.slug","$providerName.creation_date"))
      ->where("$reviewName.resource_type is NULL OR $reviewName.resource_type = 'sitebooking_pro'");
    $select->group($providerRatingName.'.rating_id')
    ->order($reviewName . '.creation_date DESC');
    
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {

      if(!empty($_POST["providerTitle"])) {
      $providerTitle = $_POST["providerTitle"];
      $select->where("$providerName.title like '%$providerTitle%'");
      }
      if($_POST["rating"] > 0) {
      $rating = $_POST["rating"];
      $select->where("$providerRatingName.rating = $rating");
      }
      
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    
    $items_per_page = 10;
    $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);
  }

  public function reviewServiceDeleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->rating_id = $id;

    // Check post
    if( $this->getRequest()->isPost() )
    {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    $review = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
    $reviewName = $review->info('name');
    $serviceRating = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');
    $serviceRatingName = $serviceRating->info('name');
    $service = Engine_Api::_()->getDbtable('sers', 'sitebooking');
    $serviceName = $service->info('name');

    try
    {
      $ratingItem = $serviceRating->fetchRow($serviceRating->select()->where('rating_id = ?', $id));

      $reviewItem = $review->fetchRow($review->select()->where('resource_id = ?', $ratingItem->ser_id)->where('user_id = ?', $ratingItem->user_id)->where('resource_type = ?', "sitebooking_ser"));

      if(!empty($reviewItem))
        $reviewItem->delete();

      $serviceItem = $service->fetchRow($service->select()->where('ser_id = ?', $ratingItem->ser_id));

      $ser_id = $ratingItem->ser_id;
      $ratingItem->delete();

      if(!empty($serviceItem)) {

        if($serviceItem->review_count > 0) {
        $serviceItem->review_count--;
        }
        if($serviceItem->rating_count > 0) {
        $serviceItem->rating_count--;
        }

        $serviceItem->rating = $serviceRating->getRating($ser_id);

        $serviceItem->save();

      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh'=> true,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Review has been deleted successfully.')),
      'format' => 'smoothbox'
    ));
    }
  }

  public function reviewProviderDeleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->rating_id = $id;
    // Check post
    if( $this->getRequest()->isPost() )
    {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    $review = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
    $reviewName = $review->info('name');
    $providerRating = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');
    $providerRatingName = $providerRating->info('name');

    $provider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $providerName = $provider->info('name');

    try
    {
      $ratingItem = $providerRating->fetchRow($providerRating->select()->where('rating_id = ?', $id));

      $reviewItem = $review->fetchRow($review->select()->where('resource_id = ?', $ratingItem->pro_id)->where('user_id = ?', $ratingItem->user_id)->where('resource_type = ?', "sitebooking_pro"));

      if(!empty($reviewItem))
        $reviewItem->delete();

      $providerItem = $provider->fetchRow($provider->select()->where('pro_id = ?', $ratingItem->pro_id));

      $pro_id = $ratingItem->pro_id;
      $ratingItem->delete();

      if(!empty($providerItem)) {

        if($providerItem->review_count > 0) {
        $providerItem->review_count--;
        }
        if($providerItem->rating_count > 0) {
        $providerItem->rating_count--;
        }

        $providerItem->rating = $providerRating->getRating($pro_id);

        $providerItem->save();

      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh'=> true,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Review has been deleted successfully.')),
      'format' => 'smoothbox'
    ));
    }
  }

  public function faqAction() 
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_faq');
  }

  function multiServiceReviewDeleteAction()
  {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      
      foreach ($values as $key => $value) {
      if ($key == 'delete_' . $value) {

        $id = $value;
        $this->view->rating_id = $id;
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $review = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
        $reviewName = $review->info('name');
        $serviceRating = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');
        $serviceRatingName = $serviceRating->info('name');
        $service = Engine_Api::_()->getDbtable('sers', 'sitebooking');
        $serviceName = $service->info('name');

        try
        {
          $ratingItem = $serviceRating->fetchRow($serviceRating->select()->where('rating_id = ?', $id));

          $reviewItem = $review->fetchRow($review->select()->where('resource_id = ?', $ratingItem->ser_id)->where('user_id = ?', $ratingItem->user_id)->where('resource_type = ?', "sitebooking_ser"));

          if(!empty($reviewItem))
            $reviewItem->delete();

          $serviceItem = $service->fetchRow($service->select()->where('ser_id = ?', $ratingItem->ser_id));

          $ser_id = $ratingItem->ser_id;
          $ratingItem->delete();

          if(!empty($serviceItem)) {

            if($serviceItem->review_count > 0) {
            $serviceItem->review_count--;
            }
            if($serviceItem->rating_count > 0) {
            $serviceItem->rating_count--;
            }

            $serviceItem->rating = $serviceRating->getRating($ser_id);

            $serviceItem->save();

          }

          $db->commit();
        }

        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }

      }        
      }      
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'review-service-manage'));
  }

  function multiProviderReviewDeleteAction()
  {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      
      foreach ($values as $key => $value) {
      if ($key == 'delete_' . $value) {

        $id = $value;
        $this->view->rating_id = $id;
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $review = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
        $reviewName = $review->info('name');
        $providerRating = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');
        $providerRatingName = $providerRating->info('name');

        $provider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
        $providerName = $provider->info('name');

        try
        {
          $ratingItem = $providerRating->fetchRow($providerRating->select()->where('rating_id = ?', $id));

          $reviewItem = $review->fetchRow($review->select()->where('resource_id = ?', $ratingItem->pro_id)->where('user_id = ?', $ratingItem->user_id)->where('resource_type = ?', "sitebooking_pro"));

          if(!empty($reviewItem))
            $reviewItem->delete();

          $providerItem = $provider->fetchRow($provider->select()->where('pro_id = ?', $ratingItem->pro_id));

          $pro_id = $ratingItem->pro_id;
          $ratingItem->delete();

          if(!empty($providerItem)) {

            if($providerItem->review_count > 0) {
            $providerItem->review_count--;
            }
            if($providerItem->rating_count > 0) {
            $providerItem->rating_count--;
            }

            $providerItem->rating = $providerRating->getRating($pro_id);

            $providerItem->save();

          }

          $db->commit();
        }

        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }

      }        
      }      
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'review-provider-manage'));
  }

}
