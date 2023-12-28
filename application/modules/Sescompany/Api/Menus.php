<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Menus.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Api_Menus extends Core_Api_Abstract
{
  public function getMenus($params = array()) {

    $coreMenuItemsTable = Engine_Api::_()->getDbTable('menuitems', 'core');
    $coreMenuItemsTableName = $coreMenuItemsTable->info('name');
    $select = $coreMenuItemsTable->select()
            ->from($coreMenuItemsTableName, array('id', 'file_id', 'label', 'icon_type', 'font_icon', 'module'))
            ->where('enabled = ?', 1)
            ->where('menu = ?', $params['menu'])
            ->order('order ASC');
    return $coreMenuItemsTable->fetchAll($select);
  }

  public function getMenuObject($id) {

    $db = Engine_Db_Table::getDefaultAdapter();

    $select = new Zend_Db_Select($db);
    return $select->from('engine4_core_menuitems')
            ->where('id = ?', $id)
            ->query()
            ->fetchObject();
  }
  
  public function getMenuId($menuName) {

    $coreMenuItemsTable = Engine_Api::_()->getDbTable('menuitems', 'core');
    $coreMenuItemsTableName = $coreMenuItemsTable->info('name');
    return $coreMenuItemsTable->select()
                    ->from($coreMenuItemsTableName, 'id')
                    ->where('name =?', $menuName)
                    ->query()
                    ->fetchColumn();
  }

  public function getIconsMenu($menuName) {

    $coreMenuItemsTable = Engine_Api::_()->getDbTable('menuitems', 'core');
    $coreMenuItemsTableName = $coreMenuItemsTable->info('name');
    return $coreMenuItemsTable->select()
                    ->from($coreMenuItemsTableName, 'file_id')
                    ->where('name =?', $menuName)
                    ->query()
                    ->fetchColumn();
  }

}