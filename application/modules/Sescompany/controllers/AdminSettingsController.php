<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminSettingsController.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sescompany_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_settings');

    $this->view->form = $form = new Sescompany_Form_Admin_Global();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //Start Make extra file for company theme custom css and constant xml file
      $this->makeCompanyFile($form);
    
      $values = $form->getValues();
      include_once APPLICATION_PATH . "/application/modules/Sescompany/controllers/License.php";
      
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.pluginactivated')) {

				if (!empty($values['sescompany_layout_enable'])) {
          //Landing Page
					$this->landingpageDefault();
				}
        unset($values['popup_settings']);

				//Here we have set the value of theme active.
				if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.themeactive')) {
					
					$db = Engine_Db_Table::getDefaultAdapter();
					
					Engine_Api::_()->getApi('settings', 'core')->setSetting('sescompany.themeactive', 1);
					
					$db->query("INSERT IGNORE INTO `engine4_core_themes` (`name`, `title`, `description`, `active`) VALUES ('sescompany', 'Company', '', 0)");

					$themeName = 'sescompany';
					$themeTable = Engine_Api::_()->getDbtable('themes', 'core');
					$themeSelect = $themeTable->select()
									->orWhere('theme_id = ?', $themeName)
									->orWhere('name = ?', $themeName)
									->limit(1);
					$theme = $themeTable->fetchRow($themeSelect);

					if ($theme) {

						$db = $themeTable->getAdapter();
						$db->beginTransaction();

						try {
							$themeTable->update(array('active' => 0), array('1 = ?' => 1));
							$theme->active = true;
							$theme->save();

							// clear scaffold cache
							Core_Model_DbTable_Themes::clearScaffoldCache();

							// Increment site counter
							$settings = Engine_Api::_()->getApi('settings', 'core');
							$settings->core_site_counter = $settings->core_site_counter + 1;

							$db->commit();
						} catch (Exception $e) {
							$db->rollBack();
							throw $e;
						}
					}
				}
				
				foreach ($values as $key => $value) {
          if (in_array($key, array('company_body_background_image', 'company_left_columns_width', 'company_right_columns_width', 'company_main_width', 'company_mobilehideleftrightcolumn'))) {
            if(empty($value) && $key == 'company_body_background_image') {
              $value = 'public/admin/blank.png';
            }
            Engine_Api::_()->sescompany()->readWriteXML($key, $value);
          }
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
				}
        $form->addNotice('Your changes have been saved.');
        if($error)
          $this->_helper->redirector->gotoRoute(array());
      }
    }
  }
  
  public function landingPageSettingAction() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_landingpagesettings');
    
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main_landingpagesettings', array(), 'sescompany_admin_main_landingpagesetting');
    
    
    $this->view->form = $form = new Sescompany_Form_Admin_LandingPageSettings();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      unset($values['sescompany_la1aboutus']);
      unset($values['sescompany_la1slider']);
      unset($values['sescompany_la1counter']);
      unset($values['sescompany_la1features']);
      unset($values['sescompany_la1clientbgimage']);
      unset($values['sescompany_mngtestimonials']);
      unset($values['sescompany_la2photos']);
      foreach ($values as $key => $value) { 
        if($value != '') {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
      }
      $form->addNotice('Your changes have been saved.');
      $this->_helper->redirector->gotoRoute(array());
    }
  }
  
  public function contentsAction() {

    $db = Engine_Db_Table::getDefaultAdapter();
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_landingpagesettings');

    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main_landingpagesettings', array(), 'sescompany_admin_main_managecontents');
    
    $this->view->form = $form = new Sescompany_Form_Admin_ManageContents();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
      $this->_helper->redirector->gotoRoute(array());
    }
  }
  

  public function typographyAction() {

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_typography');

    $this->view->form = $form = new Sescompany_Form_Admin_Typography();
    
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
    
      $values = $form->getValues(); 
      unset($values['company_body']);
      unset($values['company_heading']);
      unset($values['company_mainmenu']);
      unset($values['company_tab']);

      $db = Engine_Db_Table::getDefaultAdapter();
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.pluginactivated')) {
        
        foreach ($values as $key => $value) {
        
          if($values['sescompany_googlefonts']) {
            unset($values['company_body_fontfamily']);
            unset($values['company_heading_fontfamily']);
            unset($values['company_mainmenu_fontfamily']);
            unset($values['company_tab_fontfamily']);
            
            unset($values['company_body_fontsize']);
            unset($values['company_heading_fontsize']);
            unset($values['company_mainmenu_fontsize']);
            unset($values['company_tab_fontsize']);
            
            if($values['company_googlebody_fontfamily'])
              Engine_Api::_()->sescompany()->readWriteXML('company_body_fontfamily', $values['company_googlebody_fontfamily']);
              
            if($values['company_googlebody_fontsize'])
              Engine_Api::_()->sescompany()->readWriteXML('company_body_fontsize', $values['company_googlebody_fontsize']);
              
            if($values['company_googleheading_fontfamily'])
              Engine_Api::_()->sescompany()->readWriteXML('company_heading_fontfamily', $values['company_googleheading_fontfamily']);
                            
            if($values['company_googleheading_fontsize'])
              Engine_Api::_()->sescompany()->readWriteXML('company_heading_fontsize', $values['company_googleheading_fontsize']);
              
            if($values['company_googlemainmenu_fontfamily'])
              Engine_Api::_()->sescompany()->readWriteXML('company_mainmenu_fontfamily', $values['company_googlemainmenu_fontfamily']);
                            
            if($values['company_googlemainmenu_fontsize'])
              Engine_Api::_()->sescompany()->readWriteXML('company_mainmenu_fontsize', $values['company_googlemainmenu_fontsize']);
              
            if($values['company_googletab_fontfamily'])
              Engine_Api::_()->sescompany()->readWriteXML('company_tab_fontfamily', $values['company_googletab_fontfamily']);
              
            if($values['company_googletab_fontsize'])
              Engine_Api::_()->sescompany()->readWriteXML('company_tab_fontsize', $values['company_googletab_fontsize']);
              
            //Engine_Api::_()->sescompany()->readWriteXML($key, $value);
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
          } else {
            unset($values['company_googlebody_fontfamily']);
            unset($values['company_googleheading_fontfamily']);
            unset($values['company_googleheading_fontfamily']);
            unset($values['company_googletab_fontfamily']);
            
            unset($values['company_googlebody_fontsize']);
            unset($values['company_googleheading_fontsize']);
            unset($values['company_googlemainmenu_fontsize']);
            unset($values['company_googletab_fontsize']);
            
            Engine_Api::_()->sescompany()->readWriteXML($key, $value);
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
          }
        }
        $form->addNotice('Your changes have been saved.');
        $this->_helper->redirector->gotoRoute(array());
      }
    }
  }
  
  
  public function stylingAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_admin_main', array(), 'sescompany_admin_main_styling');

    $this->view->form = $form = new Sescompany_Form_Admin_Styling();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      unset($values['header_settings']); 
      unset($values['footer_settings']); 
      unset($values['body_settings']);
      $db = Engine_Db_Table::getDefaultAdapter();
      
      $settingsTable = Engine_Api::_()->getDbTable('settings', 'core');
      $settingsTableName = $settingsTable->info('name');
      
      foreach ($values as $key => $value) {
        Engine_Api::_()->sescompany()->readWriteXML($key, $value, '');
        if ($values['theme_color'] == '5') {
          $stringReplace = str_replace('_', '.', $key);
          if($stringReplace == 'company.mainmenu.background.color') { 
						$stringReplace = 'company.mainmenu.backgroundcolor';
          } elseif($stringReplace == 'company.mainmenu.link.color') {
						$stringReplace = 'company.mainmenu.linkcolor';
          } elseif($stringReplace == 'company.minimenu.links.color') {
	          $stringReplace = 'company.minimenu.linkscolor';
           } elseif($stringReplace == 'company.font.color') {
	          $stringReplace = 'company.fontcolor';
           } elseif($stringReplace == 'company.link.color') {
	          $stringReplace = 'company.linkcolor';
           } elseif($stringReplace == 'company.content.border.color') {
	          $stringReplace = 'company.content.bordercolor';
           } elseif($stringReplace == 'company.button.background.color') {
	          $stringReplace = 'company.button.backgroundcolor';
           } else {
						$stringReplace = str_replace('_', '.', $key);
          }
          $columnVal = $settingsTable->select()
									   ->from($settingsTableName, array('value'))
                    ->where('name = ?', $stringReplace)
                    ->query()
                    ->fetchColumn(); 
          if($columnVal) {
            if($stringReplace == 'company.mainmenu.background.color') {
	            $db->query('UPDATE `engine4_core_settings` SET `value` = "'.$value.'" WHERE `engine4_core_settings`.`name` = "company.mainmenu.backgroundcolor";');
            
            } elseif($stringReplace == 'company.mainmenu.link.color') {
	            $db->query('UPDATE `engine4_core_settings` SET `value` = "'.$value.'" WHERE `engine4_core_settings`.`name` = "company.mainmenu.linkcolor";');
           } elseif($stringReplace == 'company.minimenu.links.color') {
	            $db->query('UPDATE `engine4_core_settings` SET `value` = "'.$value.'" WHERE `engine4_core_settings`.`name` = "company.minimenu.linkscolor";');
           } elseif($stringReplace == 'company.font.color') {
	            $db->query('UPDATE `engine4_core_settings` SET `value` = "'.$value.'" WHERE `engine4_core_settings`.`name` = "company.fontcolor";');
           } elseif($stringReplace == 'company.link.color') {
	            $db->query('UPDATE `engine4_core_settings` SET `value` = "'.$value.'" WHERE `engine4_core_settings`.`name` = "company.linkcolor";');
           } elseif($stringReplace == 'company.content.border.color') {
	            $db->query('UPDATE `engine4_core_settings` SET `value` = "'.$value.'" WHERE `engine4_core_settings`.`name` = "company.content.bordercolor";');
           } elseif($stringReplace == 'company.button.background.color') {
	            $db->query('UPDATE `engine4_core_settings` SET `value` = "'.$value.'" WHERE `engine4_core_settings`.`name` = "company.button.backgroundcolor";');
           } 
           else {
		          $db->query('UPDATE `engine4_core_settings` SET `value` = "'.$value.'" WHERE `engine4_core_settings`.`name` = "'.$stringReplace.'";');
	          }
          } else {
            if($stringReplace == 'company.mainmenu.background.color') {
	            $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ("company.mainmenu.backgroundcolor", "'.$value.'");');
            } elseif($stringReplace == 'company.mainmenu.link.color') {
	            $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ("company.mainmenu.linkcolor", "'.$value.'");');
           } elseif($stringReplace == 'company.minimenu.links.color') {
	            $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ("company.minimenu.linkscolor", "'.$value.'");');
           } elseif($stringReplace == 'company.font.color') {
	            $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ("company.fontcolor", "'.$value.'");');
           } elseif($stringReplace == 'company.link.color') {
	            $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ("company.linkcolor", "'.$value.'");');
           } elseif($stringReplace == 'company.content.border.color') {
	            $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ("company.content.bordercolor", "'.$value.'");');
           } elseif($stringReplace == 'company.button.background.color') {
	            $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ("company.button.backgroundcolor", "'.$value.'");');
           } 
            else {
		          $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ("'.$stringReplace.'", "'.$value.'");');
	          }
          
          }
        }
        
      }

      //Clear scaffold cache
      Core_Model_DbTable_Themes::clearScaffoldCache();
      //Increment site counter
      $settings->core_site_counter = Engine_Api::_()->getApi('settings', 'core')->core_site_counter + 1;

      $form->addNotice('Your changes have been saved.');
      $this->_helper->redirector->gotoRoute(array());
    }
  }
  
  public function widgetCheck($params = array()) {

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    return $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('type = ?', 'widget')
                    ->where('page_id = ?', $params['page_id'])
                    ->where('name = ?', $params['widget_name'])
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }
  
  public function makeCompanyFile() {
  
    //Start Make extra file for company theme custom css
    $themeDirName = APPLICATION_PATH . '/application/themes/sescompany';
    @chmod($themeDirName, 0777);
    if (!is_readable($themeDirName)) {
      $itemError = Zend_Registry::get('Zend_Translate')->_("You have not read permission on below file path. So, please give chmod 777 recursive permission to continue this process. Path Name: %s", $themeDirName);
      $form->addError($itemError);
      return;
    }
    $fileName = $themeDirName . '/sescompany-custom.css';
    $fileexists = @file_exists($fileName);
    if (empty($fileexists)) {
      @chmod($themeDirName, 0777);
      if (!is_writable($themeDirName)) {
        $itemError = Zend_Registry::get('Zend_Translate')->_("You have not writable permission on below file path. So, please give chmod 777 recursive permission to continue this process. <br /> Path Name: $themeDirName");
        $form->addError($itemError);
        return;
      }
      $fh = @fopen($fileName, 'w');
      @fwrite($fh, '/* ADD YOUR CUSTOM CSS HERE */');
      @chmod($fileName, 0777);
      @fclose($fh);
      @chmod($fileName, 0777);
      @chmod($fileName, 0777);
    }
    
    
    //Start Make extra file for sescompany constant
    $moduleDirName = APPLICATION_PATH . '/application/modules/Sescompany/externals/styles/';
    @chmod($moduleDirName, 0777);
    if (!is_readable($moduleDirName)) {
      $itemError = Zend_Registry::get('Zend_Translate')->_("You have not read permission on below file path. So, please give chmod 777 recursive permission to continue this process. Path Name: %s", $moduleDirName);
      $form->addError($itemError);
      return;
    }
    $fileNameXML = $moduleDirName . '/sescompany.xml';
    $fileexists = @file_exists($fileNameXML);
    if (empty($fileexists)) {
      @chmod($moduleDirName, 0777);
      if (!is_writable($moduleDirName)) {
        $itemError = Zend_Registry::get('Zend_Translate')->_("You have not writable permission on below file path. So, please give chmod 777 recursive permission to continue this process. <br /> Path Name: $moduleDirName");
        $form->addError($itemError);
        return;
      }
      $fh = @fopen($fileNameXML, 'w');
      @fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?><root></root>');
      @chmod($fileNameXML, 0777);
      @fclose($fh);
      @chmod($fileNameXML, 0777);
      @chmod($fileNameXML, 0777);
    }
    //Start Make extra file for sescompany constant
  
  }
  
  
	public function uploadBanner(){

		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$slideData = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
		foreach($slideData as $data) {
      $data1 = explode('_', $data);
      $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sescompany' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "banners" . DIRECTORY_SEPARATOR;

      if (is_file($PathFile . $data . '.jpg')) {
        $storage = Engine_Api::_()->getItemTable('storage_file');
        $filename = $storage->createFile($PathFile . $data.'.jpg', array(
            'parent_id' => $data1[1],
            'parent_type' => 'sescompany_slide',
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        ));
        $file_id = $filename->file_id;
        $db->query("UPDATE `engine4_sescompany_slides` SET `file_id` = '" . $file_id . "' WHERE slide_id = " . $data);
      }
		}
	}
	
	public function landingpageDefault() {
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` =3;");
    $page_id = $pageId = 3;
    $LandingPageOrder = 1;
    // Insert top
    $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $pageId,
      'order' => $LandingPageOrder++,
    ));
    $topId = $db->lastInsertId();

    // Insert main
    $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $pageId,
      'order' => $LandingPageOrder++,
    ));
    $mainId = $db->lastInsertId();

    // Insert main-middle
    $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $pageId,
      'parent_content_id' => $mainId,
      'order' => $LandingPageOrder++,
    ));
    $mainMiddleId = $db->lastInsertId();
  
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sescompany.landing-page',
        'page_id' => 3,
        'order' => $LandingPageOrder++,
        'parent_content_id' => $mainMiddleId,
    ));
	}
}