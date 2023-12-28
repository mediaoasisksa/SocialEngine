<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: install.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Installer extends Engine_Package_Installer_Module {

  public function onPreinstall() {

    $db = $this->getDb();
    $plugin_currentversion = '4.10.3p3';
    
    //Check: Basic Required Plugin
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sesbasic');
    $results = $select->query()->fetchObject();
    if (empty($results)) {
      return $this->_error('<div class="global_form"><div><div><p style="color:red;">The required SocialEngineSolutions Basic Required Plugin is not installed on your website. Please download the latest version of this FREE plugin from <a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions.com</a> website.</p></div></div></div>');
    } else {
      $error = include APPLICATION_PATH . "/application/modules/Sesbasic/controllers/checkPluginVersion.php";
      if($error != '1') {
        return $this->_error($error);
      }
		}
    parent::onPreinstall();
  }
  
  public function onInstall() {

    $db = $this->getDb();
    parent::onInstall();
  }

  function onEnable() {

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content', 'name')
            ->where('page_id = ?', 1)
            ->where('name LIKE ?', '%menu-main%')
            ->limit(1);
    $info = $select->query()->fetch();
    if (!empty($info)) {
      $db->update('engine4_core_content', array('name' => 'sescompany.menu-main'), array('name = ?' => $info['name']));
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content', 'name')
            ->where('page_id = ?', 1)
            ->where('name LIKE ?', '%menu-mini%')
            ->limit(1);
    $info = $select->query()->fetch();
    if (!empty($info)) {
      $db->update('engine4_core_content', array('name' => 'sescompany.menu-mini', 'order' => 2), array(       'name = ?' => $info['name']));
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content', 'name')
            ->where('page_id = ?', 2)
            ->where('name LIKE ?', '%menu-footer%')
            ->limit(1);
    $info = $select->query()->fetch();
    if (!empty($info)) {
      $db->update('engine4_core_content', array('name' => 'sescompany.menu-footer'), array('name = ?' => $info['name']));
    }

    $db->query("UPDATE  `engine4_core_menuitems` SET  `enabled` =  '1' WHERE  `engine4_core_menuitems`.`name` ='core_mini_friends';");
    $db->query("UPDATE  `engine4_core_menuitems` SET  `enabled` =  '1' WHERE  `engine4_core_menuitems`.`name` ='core_mini_notification';");
    
    //For upgradation only
    $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('sescompany.themeactive', '1');");
    parent::onEnable();
  }

  public function onDisable() {
    $db = $this->getDb();
    $db->query("UPDATE  `engine4_core_menuitems` SET  `enabled` =  '0' WHERE  `engine4_core_menuitems`.`name` ='core_mini_friends';");
    $db->query("UPDATE  `engine4_core_menuitems` SET  `enabled` =  '0' WHERE  `engine4_core_menuitems`.`name` ='core_mini_notification';");
    
    
    //Header Work
    $db->query("UPDATE  `engine4_core_content` SET  `name` =  'core.menu-mini' WHERE  `engine4_core_content`.`name` ='sescompany.header' LIMIT 1");
    $parent_content_id = $db->select()
		        ->from('engine4_core_content', 'content_id')
		        ->where('type = ?', 'container')
		        ->where('page_id = ?', '1')
		        ->where('name = ?', 'main')
		        ->limit(1)
		        ->query()
		        ->fetchColumn();
		if($parent_content_id) {
			$db->insert('engine4_core_content', array(
		      'type' => 'widget',
		      'name' => 'core.menu-logo',
		      'page_id' => 1,
		      'parent_content_id' => $parent_content_id,
		      'order' => 10,
		  ));
		  $db->insert('engine4_core_content', array(
		      'type' => 'widget',
		      'name' => 'core.menu-main',
		      'page_id' => 1,
		      'parent_content_id' => $parent_content_id,
		      'order' => 20,
		  ));
	  }
	  
	  //Footer Work
    $db->query("UPDATE  `engine4_core_content` SET  `name` =  'core.menu-footer' WHERE  `engine4_core_content`.`name` ='sescompany.menu-footer' LIMIT 1");
    
    $db->query("UPDATE  `engine4_core_themes` SET  `active` =  '0' WHERE  `engine4_core_themes`.`name` ='sescompany' LIMIT 1");
    $db->query("UPDATE  `engine4_core_themes` SET  `active` =  '1' WHERE  `engine4_core_themes`.`name` ='default' LIMIT 1");

    parent::onDisable();
  }
}