<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminSettingsController.php 2022-06-21
 */

class Elpis_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
  
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('elpis_admin_main', array(), 'elpis_admin_main_settings');

    $this->view->form = $form = new Elpis_Form_Admin_Settings_Global();

    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
    
      $values = $form->getValues();
      
      $changelanding = Engine_Api::_()->getApi('settings', 'core')->getSetting('elpis.changelanding', 0);
      if (isset($values['elpis_changelanding']) && !empty($values['elpis_changelanding']) && $changelanding != $values['elpis_changelanding']) {
        $this->landingpageSet($values['elpis_changelanding']);
      }
      
      if (isset($values['elpis_headernonloggedinoptions']))
        $values['elpis_headernonloggedinoptions'] = serialize($values['elpis_headernonloggedinoptions']);
      else
        $values['elpis_headernonloggedinoptions'] = serialize(array());

      if (isset($values['elpis_headerloggedinoptions']))
        $values['elpis_headerloggedinoptions'] = serialize($values['elpis_headerloggedinoptions']);
      else
        $values['elpis_headerloggedinoptions'] = serialize(array());

      if(@$values['theme_widget_radius'])
        Engine_Api::_()->elpis()->readWriteXML('theme_widget_radius', @$values['theme_widget_radius']. 'px');

      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }
  
  public function stylingAction() {
  
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('elpis_admin_main', array(), 'elpis_admin_main_styling');

    $this->view->customtheme_id = $this->_getParam('customtheme_id', 1);

    $this->view->form = $form = new Elpis_Form_Admin_Settings_Styling();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $db = Engine_Db_Table::getDefaultAdapter();
      
      $values = $form->getValues();
      unset($values['header_settings']);
      unset($values['footer_settings']);
      unset($values['body_settings']);
      unset($values['custom_themes']);
      $theme_id = $values['theme_color'];
      
      foreach ($values as $key => $value) {
      
        if (isset($_POST['save'])) {
          Engine_Api::_()->elpis()->readWriteXML($key, $value, '');
        }

        if ((isset($_POST['submit']) || isset($_POST['save'])) && $values['theme_color'] > '3') {
          foreach($values as $key => $value) {
            $db->query("UPDATE `engine4_elpis_customthemes` SET `value` = '".$value."' WHERE `engine4_elpis_customthemes`.`theme_id` = '".$theme_id."' AND  `engine4_elpis_customthemes`.`column_key` = '".$key."';");
          }
        }
      }
      
      //Clear scaffold cache
      Core_Model_DbTable_Themes::clearScaffoldCache();

      $form->addNotice('Your changes have been saved.');
      $this->_helper->redirector->gotoRoute(array('module' => 'elpis', 'controller' => 'settings', 'action' => 'styling', 'customtheme_id' => $values['theme_color']),'admin_default',true);
    }
    $this->view->activatedTheme = Engine_Api::_()->elpis()->getContantValueXML('theme_color');
  }

  public function addAction() {

    $this->_helper->layout->setLayout('admin-simple');
    
    $customtheme_id = $this->_getParam('customtheme_id', 0);
    
    $this->view->form = $form = new Elpis_Form_Admin_Settings_CustomTheme();
    if ($customtheme_id) {
      $form->setTitle("Edit Custom Theme Name");
      $form->submit->setLabel('Save Changes');
      $customtheme_id = $customtheme_id + 1;
      $customtheme = Engine_Api::_()->getItem('elpis_customthemes', $customtheme_id);
      $form->populate($customtheme->toArray());
    }
    
    if ($this->getRequest()->isPost()) {
    
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      
      $table = Engine_Api::_()->getDbtable('customthemes', 'elpis');
      
      $db = $table->getAdapter();
      $db->beginTransaction();
      try {
        
        $values = $form->getValues();

        if(!$customtheme_id) {
            $customtheme = $table->createRow();
            $customtheme->setFromArray($values);
            $customtheme->save();

            $theme_id = $customtheme->customtheme_id;

            if(!empty($values['customthemeid'])) {

                $dbInsert = Engine_Db_Table::getDefaultAdapter();

                $getThemeValues = $table->getThemeValues(array('customtheme_id' => $values['customthemeid']));
                foreach($getThemeValues as $key => $value) {
                    $dbInsert->query("INSERT INTO `engine4_elpis_customthemes` (`name`, `value`, `column_key`,`default`,`theme_id`) VALUES ('".$values['name']."','".$value->value."','".$value->column_key."','1','".$theme_id."') ON DUPLICATE KEY UPDATE `value`='".$value->value."';");
                }
                $db->query("UPDATE `engine4_elpis_customthemes` SET `value` = '" . $theme_id . "' WHERE theme_id = " . $theme_id . " AND column_key = 'custom_theme_color';");
                $db->query('DELETE FROM `engine4_elpis_customthemes` WHERE `engine4_elpis_customthemes`.`theme_id` = "0";');
            }
        } else if(!empty($customtheme_id)) {
          $theme_id = $customtheme_id = $customtheme_id - 1;
          $db->query("UPDATE `engine4_elpis_customthemes` SET `name` = '" . $values['name'] . "' WHERE theme_id = " . $customtheme_id);
        }
        $db->commit();
        if(!$customtheme_id) {
          $message = array('New Custom theme created successfully.');
        } else {
          $message = array('New Custom theme edited successfully.');
        }
        return $this->_forward('success', 'utility', 'core', array(
          'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'elpis', 'controller' => 'settings', 'action' => 'styling', 'customtheme_id' => $theme_id),'admin_default',true),
          'messages' => $message,
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->customtheme_id = $customtheme_id = $this->_getParam('customtheme_id', 0);

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $dbQuery = Zend_Db_Table_Abstract::getDefaultAdapter();
        $dbQuery->query("DELETE FROM engine4_elpis_customthemes WHERE theme_id = ".$customtheme_id);
        $db->commit();
        $activatedTheme = Engine_Api::_()->elpis()->getContantValueXML('custom_theme_color');
        $this->_forward('success', 'utility', 'core', array(
          'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'elpis', 'controller' => 'settings', 'action' => 'styling', 'customtheme_id' => $activatedTheme),'admin_default',true),
          'messages' => array('You have successfully delete custom theme.')
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } else {
      $this->renderScript('admin-settings/delete.tpl');
    }
  }
  
  public function getcustomthemecolorsAction() {

    $customtheme_id = $this->_getParam('customtheme_id', null);
    if(empty($customtheme_id))
      return;
    
    if(engine_in_array($customtheme_id, array(1,2,3)))
      $default = 0;
    else
      $default = 1;
      
    $themecustom = Engine_Api::_()->getDbTable('customthemes', 'elpis')->getThemeKey(array('theme_id'=>$customtheme_id, 'default' => $default));
    $customthecolorArray = array();
    foreach($themecustom as $value) {
      $customthecolorArray[] = $value['column_key'].'||'.$value['value'];
    }
    echo json_encode($customthecolorArray);die;
  }

  public function landingpageSet($value) {

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    // Get page param
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    
    // Make new page
    $pageObject = $pageTable->createRow();
    $pageObject->displayname = "Backup - Landing Page";
    $pageObject->provides = 'no-subject';
    $pageObject->save();
    $new_page_id = $pageObject->page_id;
    
    $old_page_content = $db->select()
        ->from('engine4_core_content')
        ->where('`page_id` = ?', 3)
        ->order(array('type', 'content_id'))
        ->query()
        ->fetchAll();
    
    $content_count = engine_count($old_page_content);
    for($i = 0; $i < $content_count; $i++){
      $contentRow = $contentTable->createRow();
      $contentRow->page_id = $new_page_id;
      $contentRow->type = $old_page_content[$i]['type'];
      $contentRow->name = $old_page_content[$i]['name'];
      if( $old_page_content[$i]['parent_content_id'] != null ) {
        $contentRow->parent_content_id = $content_id_array[$old_page_content[$i]['parent_content_id']];            
      }
      else{
        $contentRow->parent_content_id = $old_page_content[$i]['parent_content_id'];
      }
      $contentRow->order = $old_page_content[$i]['order'];
      $contentRow->params = $old_page_content[$i]['params'];
      $contentRow->attribs = $old_page_content[$i]['attribs'];
      $contentRow->save();
      $content_id_array[$old_page_content[$i]['content_id']] = $contentRow->content_id;
    }

    $widgetOrder = 1;
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = "3";');

    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => 3,
        'order' => 1,
    ));
    $mainId = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => 3,
        'parent_content_id' => $mainId,
        'order' => 2,
    ));
    $mainMiddleId = $db->lastInsertId();

    if($value == 1) {

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.landing-page-banner',
        'page_id' => 3,
        'parent_content_id' => $mainMiddleId,
        'order' => $widgetOrder++,
        'params' => '{"height":"600","title":"","nomobile":"0","name":"core.landing-page-banner"}',
      ));
      
      $db->query('UPDATE `engine4_core_content` SET `params` = \'{"bannerId":"2","height":"550","title":"","nomobile":"0","name":"core.landing-page-banner"}\' WHERE `engine4_core_content`.`name` = "core.landing-page-banner" AND `engine4_core_content`.`page_id` = "3";');
      
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.landing-page-features',
        'page_id' => 3,
        'parent_content_id' => $mainMiddleId,
        'order' => $widgetOrder++,
        'params' => '{"dummy1":null,"fe1img":"0","fe1heading":"Easy Login \/ Signup","fe1description":"You can easily sign up on our community or simply login, if you already have an account to get started !","dummy2":null,"fe2img":"0","fe2heading":"Post Content","fe2description":"Quickly start by posting your status updates, photos, videos, groups, blogs, classifieds, etc inside.","dummy3":null,"fe3img":"0","fe3heading":"Responsive","fe3description":"Our community is 100% responsive, so you can use it anywhere, & anytime from any device.","dummy4":null,"fe4img":"0","fe4heading":"Flexible","fe4description":"Our community is available 24x7, so you can use it as per your flexibility and requirement.","title":"Why Choose Us?","nomobile":"0","name":"core.landing-page-features"}', 
      ));
      
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.parallax',
        'page_id' => 3,
        'parent_content_id' => $mainMiddleId,
        'order' => $widgetOrder++,
        'params' => '{"bgphoto":"","heading":"Engage with people of your interests","height":"300","title":"","nomobile":"0","name":"core.parallax"}', 
      ));
      
      if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('blog')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'elpis.landing-page-blogs',
          'page_id' => 3,
          'parent_content_id' => $mainMiddleId,
          'order' => $widgetOrder++,
          'params' => '{"title":"Explore Popular Blogs","popularType":"view","itemCountPerPage":"2","nomobile":"0","name":"elpis.landing-page-blogs"}', 
        ));
      }
      
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'elpis.landing-page-members',
        'page_id' => 3,
        'parent_content_id' => $mainMiddleId,
        'order' => $widgetOrder++,
        'params' => '{"title":"Popular Members","name":"elpis.landing-page-members","itemCountPerPage":"12"}',
      ));
    } else if($value == 2) {
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'user.login-page',
        'page_id' => 3,
        'parent_content_id' => $mainMiddleId,
        'order' => $widgetOrder++,
      ));
    }
  }
  
  public function manageFontsAction() {

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('elpis_admin_main', array(), 'elpis_admin_main_managefonts');

    $this->view->form = $form = new Elpis_Form_Admin_Settings_Fonts();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();

      foreach ($values as $key => $value) {

        if($values['elpis_googlefonts']) {
          unset($values['elpis_body_fontfamily']);
          unset($values['elpis_heading_fontfamily']);
          unset($values['elpis_mainmenu_fontfamily']);
          unset($values['elpis_tab_fontfamily']);

          if($values['elpis_googlebody_fontfamily'])
            Engine_Api::_()->elpis()->readWriteXML('elpis_body_fontfamily', $values['elpis_googlebody_fontfamily']);

          if($values['elpis_googleheading_fontfamily'])
            Engine_Api::_()->elpis()->readWriteXML('elpis_heading_fontfamily', $values['elpis_googleheading_fontfamily']);
            
          if($values['elpis_googlemainmenu_fontfamily'])
            Engine_Api::_()->elpis()->readWriteXML('elpis_mainmenu_fontfamily', $values['elpis_googlemainmenu_fontfamily']);

          if($values['elpis_googletab_fontfamily'])
            Engine_Api::_()->elpis()->readWriteXML('elpis_tab_fontfamily', $values['elpis_googletab_fontfamily']);

          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        } else {
          unset($values['elpis_googlebody_fontfamily']);
          unset($values['elpis_googleheading_fontfamily']);
          unset($values['elpis_googlemainmenu_fontfamily']);
          unset($values['elpis_googletab_fontfamily']);

          Engine_Api::_()->elpis()->readWriteXML($key, $value);
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
      }
      $form->addNotice('Your changes have been saved.');
      $this->_helper->redirector->gotoRoute(array());
    }
  }
}
