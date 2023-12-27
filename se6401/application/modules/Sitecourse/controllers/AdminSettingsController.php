<?php 
class Sitecourse_AdminSettingsController extends Core_Controller_Action_Admin
{
	// filter categories
	private function getCategories($param) {
		$values = array();
		foreach($param as $key => $value){
			if(!$value['cat_dependency']) $values[] = $value;
		}
		return $values;
	}
	// filter subcategories
	private function getSubCategories($param) {
		$values = array();
		foreach($param as $key => $value){
			if($value['cat_dependency']) $values[] = $value;
		}
		return $values;
	}

	public function categoriesAction() {
		$params = $this->_getAllParams();
		$table = Engine_Api::_()->getItemTable('sitecourse_category');
		// order change task
		if(isset($params['task']) && $params['task'] == 'changeorder' ) {

			if(empty($params['sitecourseorder'])) {
				return;
			}
			$orders = explode(",", $params['sitecourseorder']);
			
			foreach($orders as $key => $order) {
				$str = explode("-", $order);
				if(isset($str[1])) {
					$table->update(
						array('cat_order'=> ($key + 1)), 
						array('category_id = ?' => $str[1])
					);
				}
			}
			return;
		}
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_categories');

        // category and subcategory data;
		$data = Engine_Api::_()->getItemTable('sitecourse_category')
				->fetchAll(NULL, 'cat_order');
		$this->view->categories=$categories = $this->getCategories($data);
		$this->view->subCategories=$subcategories = $this->getSubCategories($data);
	}


	public function addCategoryAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('admin-simple');
    	// Generate and assign form
		$form = $this->view->form = new Sitecourse_Form_Admin_Category();
		$form->setAction($this->view->url(array()));

    	// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('admin-settings/form.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('admin-settings/form.tpl');
			return;
		}
    	// Process
		$values = $form->getValues();
		$values['label'] = ucwords($values['label']);
		$categoryTable = Engine_Api::_()->getItemTable('sitecourse_category');
		$cat_order = $categoryTable->getMaxCatOrder();
		$max_cat_order = $cat_order[0]['cat_order'];
		if($categoryTable->categoryPresent($values['label'])) {
			$form->addError("Category name already exists.");
			$this->renderScript('admin-settings/form.tpl');
			return;
		}
		$db = $categoryTable->getAdapter();
		$db->beginTransaction();
		$viewer = Engine_Api::_()->user()->getViewer();
		try {
			$categoryTable->insert(array(
				'category_name' => $values['label'],
				'cat_order' => $max_cat_order + 1,
			));
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh'=> 10,
			'messages' => array('')
		));
	}

	public function addSubcategoryAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('admin-simple');
    	//parent category
		$parentCategory = $this->_getParam('id');
    	// Generate and assign form
		$form = $this->view->form = new Sitecourse_Form_Admin_SubCategory();
		$form->setAction($this->view->url(array()));
    	// Check post
		if( !$this->getRequest()->isPost() ) {
			$this->renderScript('admin-settings/form.tpl');
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			$this->renderScript('admin-settings/form.tpl');
			return;
		}
   		// Process
		$values = $form->getValues();
		$values['label'] = ucwords($values['label']);
		$categoryTable = Engine_Api::_()->getItemTable('sitecourse_category');
		if(
			$categoryTable->subCategoryPresent($values['label'],$parentCategory) ||
			$categoryTable->categoryPresent($values['label'])
		) {
			$form->addError("Category name already exists.");
			$this->renderScript('admin-settings/form.tpl');
			return;
		}
		$db = $categoryTable->getAdapter();
		$db->beginTransaction();
		$viewer = Engine_Api::_()->user()->getViewer();
		try {
			$categoryTable->insert(array(
				'category_name' => $values['label'],
				'cat_dependency' => $parentCategory,
			));
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh'=> 10,
			'messages' => array('')
		));
	}

	public function deleteCategoryAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('admin-simple');
		$category_id = $this->_getParam('id');
		$this->view->sitecourse_id = $this->view->category_id = $category_id;
		$categoriesTable = Engine_Api::_()->getItemTable('sitecourse_category');
		$category = $categoriesTable->find($category_id)->current();

		if( !$category ) {
			return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('')
			));
		} else {
			$category_id = $category->getIdentity();
		}

    	// get the categoreis apart from current category
		$this->view->categories = $remainingCategory = $categoriesTable->getRemainingCategory($category_id);
		if(count($remainingCategory) == 0){
			$this->renderScript('admin-settings/delete.tpl');
			return;
		}

    	//check post request
		if( !$this->getRequest()->isPost() ) {
      		// Output
			$this->renderScript('admin-settings/delete.tpl');
			return;
		}

		if($categoriesTable->containsSubCategory($category_id)){
			return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 1000,
				'parentRefresh'=> 1000,
				'messages' => array('Please delete all sub categories before deleting main category.')
			));
		}
    	//post data
		$formValues = $this->getRequest()->getPost();
    	// Process
		$db = $categoriesTable->getAdapter();
		$db->beginTransaction();

		try {
      		//update the courses category with the form category
			Engine_Api::_()->getItemTable('sitecourse_course')->update(array('category_id' => $formValues['category']),
				array(
					'category_id = ?'=> $category_id,
				)
			);
      		// delete category
			$category->delete();
			$sitecourseTable = Engine_Api::_()->getItemTable('sitecourse_category');
			$sitecourseTable->update(array(
				'category_id' => 0,
			), array(
				'category_id = ?' => $category_id,
			));
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh'=> 10,
			'messages' => array('')
		));
	}


	public function deleteSubcategoryAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('admin-simple');
		$category_id = $this->_getParam('id');
		$this->view->sitecourse_id = $this->view->category_id = $category_id;
		$categoriesTable = Engine_Api::_()->getItemTable('sitecourse_category');
		$category = $categoriesTable->find($category_id)->current();

		if( !$category ) {
			return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('')
			));
		} else {
			$category_id = $category->getIdentity();
		}
		$parentId = $category->cat_dependency;
    	//check post request
		if( !$this->getRequest()->isPost() ) {
			return;
		}
    	//post data
		$formValues = $this->getRequest()->getPost();
    	// Process
		$db = $categoriesTable->getAdapter();
		$db->beginTransaction();

		try {
      		//update the courses category with the form category
			Engine_Api::_()->getItemTable('sitecourse_course')->update(array('subcategory_id' => 0),
				array(
					'subcategory_id = ?'=> $category_id,
				)
			);
      		// delete category
			$category->delete();
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh'=> 10,
			'messages' => array('')
		));
	}

	public function editCategoryAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('admin-simple');
		$category_id = $this->_getParam('id');
		$this->view->sitecourse_id = $this->view->category_id = $id;
		$categoriesTable = Engine_Api::_()->getItemTable('sitecourse_category');
		$category = $categoriesTable->find($category_id)->current();

		if( !$category ) {
			return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('')
			));
		} else {
			$category_id = $category->getIdentity();
		}

		$form = $this->view->form = new Sitecourse_Form_Admin_Category();
		$form->setAction($this->getFrontController()->getRouter()->assemble(array()));
		$form->setField($category);


    	// post request
		if( !$this->getRequest()->isPost() ) {
      	// Output
			$this->renderScript('admin-settings/form.tpl');
			return;
		}
    	// validation
		if( !$form->isValid($this->getRequest()->getPost()) ) {
      	// Output
			$this->renderScript('admin-settings/form.tpl');
			return;
		}

    	// Process
		$values = $form->getValues();
		$values['label'] = ucwords($values['label']);

    	// is already available or not
		if($categoriesTable->categoryPresent($values['label'])) {
			$form->addError("Category name already exists.");
			$this->renderScript('admin-settings/form.tpl');
			return;
		}

		$db = $categoriesTable->getAdapter();
		$db->beginTransaction(); 
		try {
			$category->category_name = $values['label'];
			$category->save();

			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh'=> 10,
			'messages' => array('')
		));
	}

	public function editSubcategoryAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('admin-simple');
		$category_id = $this->_getParam('id');
		$this->view->sitecourse_id = $this->view->category_id = $id;
		$categoriesTable = Engine_Api::_()->getItemTable('sitecourse_category');
		$category = $categoriesTable->find($category_id)->current();

		if( !$category ) {
			return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('')
			));
		} else {
			$category_id = $category->getIdentity();
		}

		$form = $this->view->form = new Sitecourse_Form_Admin_SubCategory();
		$form->setAction($this->getFrontController()->getRouter()->assemble(array()));
		$form->setField($category);


    	// post request
		if( !$this->getRequest()->isPost() ) {
      	// Output
			$this->renderScript('admin-settings/form.tpl');
			return;
		}
    	// validation
		if( !$form->isValid($this->getRequest()->getPost()) ) {
      	// Output
			$this->renderScript('admin-settings/form.tpl');
			return;
		}

    	// Process
		$values = $form->getValues();
		$values['label'] = ucwords($values['label']);

    	// is already available or not
		if($categoriesTable->categoryPresent($values['label'])) {
			$form->addError("Category name already exists.");
			$this->renderScript('admin-settings/form.tpl');
			return;
		}

		$db = $categoriesTable->getAdapter();
		$db->beginTransaction(); 
		try {
			$category->category_name = $values['label'];
			$category->save();

			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh'=> 10,
			'messages' => array('')
		));
	}

  	//ACTION FOR GLOBAL SETTINGS
	public function indexAction() {
		// $this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitecourse')->hasDirectoryPermissions();

		// $course = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.course", "course");
		// $courses = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.courses", "courses");    

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_settings');

		$this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitecourse')->hasDirectoryPermissions();

		$this->view->form = $form = new Sitecourse_Form_Admin_Global();

		if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
			$values = $form->getValues();
			if ($values['sitecourse_UrlP'] == 'stores' || $values['sitecourse_UrlP'] == 'Stores') {
				$form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter the different words for course URL in place of "courses" respectively.'));
				return;
			}
			if ($values['sitecourse_UrlP'] == $values['sitecourse_UrlS']) {
				$form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter two different words for course URLs in place of "courses" and "course" respectively.'));
				return;
			}

			foreach ($values as $key => $value){
				Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
			}

      		//singular and plural title change
			// if (isset($_POST['language_phrases_courses']) && $_POST['language_phrases_courses'] != $courses && isset($_POST['language_phrases_course']) && $_POST['language_phrases_course'] != $course && !empty($hasLanguageDirectoryPermissions) ) {

			// 	$db = Zend_Db_Table_Abstract::getDefaultAdapter();

			// 	$db->query('UPDATE  `engine4_core_menuitems` SET  `label` =  \''.ucfirst($_POST['language_phrases_courses']).'\' WHERE  `engine4_core_menuitems`.`name` = "core_main_sitecourse";');

			// 	$language_pharse = array('text_courses' => '$plural$' , 'text_course' => '$singular$');

			// 	Engine_Api::_()->getApi('language', 'sitecourse')->setTranslateForListType($language_pharse, '', '$singular$', '$plural$');

			// 	$language_pharse = array('text_courses' => $_POST['language_phrases_courses'] , 'text_course' => $_POST['language_phrases_course']);

			// 	Engine_Api::_()->getApi('language', 'sitecourse')->setTranslateForListType($language_pharse, '', $course, $courses);
			// }
			$form->addNotice('Your changes have been saved.');
		}
	}

  	//youtube path and ffmpeg path settings
	public function videoSettingsAction() {
		$this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitecourse')->hasDirectoryPermissions();
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_utilities');
		$this->view->form = $form = new Sitecourse_Form_Admin_VideoSettings();
		$this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecourse_admin_main_settings', array(), 'sitecourse_admin_main_video');

		if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
			$values = $form->getValues();
			if (!empty($values['sitecourse_ffmpeg_path'])) {
				if (function_exists('exec')) {
					$ffmpeg_path = $values['sitecourse_ffmpeg_path'];
					$output = null;
					$return = null;
					exec($ffmpeg_path . ' -version', $output, $return);
					if (empty($output) || ($output != NULL && is_array($output) && count($output) == 0)) {
						$form->addError('FFMPEG path is not valid or does not exist');
						$values['sitecourse_ffmpeg_path'] = '';
						return;
					}
				} else {
					$form->addError('The exec() function is not available. The ffmpeg path has not been saved.');
					$values['sitecourse_ffmpeg_path'] = '';
					return;
				}
			}
			foreach ($values as $key => $value){
				Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
			}

			$form->addNotice('Your changes have been saved.');
		}

	}

	public function utilityAction() {

		if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
			return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
		}

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_utilities');

		$this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main_settings', array(), 'sitecourse_admin_main_general');

		$ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitecourse_ffmpeg_path;
		if (function_exists('shell_exec')) {
            // Get version
			$this->view->version = $version = shell_exec(escapeshellcmd($ffmpeg_path) . ' -version 2>&1');
			$command = "$ffmpeg_path -formats 2>&1";
			$this->view->format = $format = shell_exec(escapeshellcmd($ffmpeg_path) . ' -formats 2>&1')
			. shell_exec(escapeshellcmd($ffmpeg_path) . ' -codecs 2>&1');
		}
	}
	
	//ACTION FOR SHOWING THE FAQ
	public function faqAction() {

        //GET NAGIGATION
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_faqs');
	}

	

	public function certificateAction() {
    	//GET NAVIGATION
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('sitecourse_admin_main', array(), 'sitecourse_admin_main_certificate');

        //FORM GENERATION
		$this->view->form = $form = new Sitecourse_Form_Admin_Settings_Format();

        // Get language
		$this->view->language = $language = preg_replace('/[^a-zA-Z_-]/', '.', $this->_getParam('language', 'en'));
		if (!Zend_Locale::isLocale($language)) {
			$form->removeElement('submit');
			return $form->addError('Please select a valid language.');
		}
		//get default format from settings table
		$content = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.format.bodyhtmldefault');
		$language = str_replace("_", ".", $language);
		
        // Check method/valid
		if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {

			$values = $form->getValues();

            //RESET TO DEFAULT
			if ( isset($_POST['default']) && Engine_Api::_()->getApi('settings', 'core')->getSetting("sitecourse_format_bodyhtmldefault") ) {
				Engine_Api::_()->getApi('settings', 'core')->removeSetting("sitecourse_format_bodyhtml");
				Engine_Api::_()->getApi('settings', 'core')->setSetting("sitecourse_format_bodyhtml", $content);
			} else {
				foreach ( $values as $key => $value ) {
					if ( $key != 'submit' && $key != 'default' && $key != 'dummy_text') {
							Engine_Api::_()->getApi('settings', 'core')->setSetting($key , $value);					
					}
				}
			}
		}

		$bodyHTML = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.format.bodyhtml', $content);
		$form->populate(array(
			'sitecourse_format_bodyhtml' => $bodyHTML,
		));
	}

	public function previewCertificateAction(){
		
		$this->_helper->layout->setLayout('admin-simple');
		
		$viewer_title = 'Student';		

		$bodyHTML = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.format.bodyhtml');

		$company_logo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.companylogo');

		$background_image = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.cbackground');


		if(!$background_image){
			$background_image = 'application/modules/Sitecourse/externals/images/backgroundImage.jpg';
		}
		if(!$company_logo){
			$company_logo = 'application/modules/Sitecourse/externals/images/companyLogo.png';
		}


		$completion_date = date("d-m-Y");

		$course_name = 'Course Title';

		$owner = 'Owner'; 

		$src= 'application/modules/Sitecourse/externals/images/transparent.png';

		$placehoders = array("[Student_Name]", "[Hours]", "[Course_Name]", "[Date]", "[Creator_Name]", "[Signature]", "[Company_Logo]","[Background_Image]");

		$commonValues = array($viewer_title, 'Y Hours', $course_name, $completion_date, $owner, $src, $company_logo, $background_image);

		$this->view->bodyHTML = str_replace($placehoders, $commonValues, $bodyHTML);
		
	}
}
?>
