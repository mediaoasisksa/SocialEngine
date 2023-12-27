<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Customthemes.php 2022-06-21
 */

class Elpis_Model_DbTable_Customthemes extends Engine_Db_Table {

  protected $_rowClass = "Elpis_Model_Customtheme";

  public function getThemeKey($params = array()) {

    $select = $this->select()->from($this->info('name'));
    
    if(!empty($params['theme_id']))
        $select->where('`theme_id` =?',$params['theme_id']);
    if(!empty($params['column_key']))
        $select->where('`column_key` =?',$params['column_key']);
    if(!empty($params['customtheme_id']))
        $select->where('`customtheme_id` =?',$params['customtheme_id']);
    if(!empty($params['default']))
        $select->where('`default` =?',$params['default']);
    return $this->fetchAll($select);
  }

  public function getCustomThemes($param = array()) {

    $select = $this->select()->from($this->info('name'));
    
    if(empty($param['all']))
      $select->where('`default` = ?', '1');
    if(!empty($param['all']) && isset($param['all']))
      $select->where('theme_id <> ?', 0)->group('theme_id')->group('name');
    if(!empty($param['customtheme_id']))
      $select->where('theme_id =?', $param['customtheme_id']);
    return $this->fetchAll($select);
  }

  public function getThemeValues($param = array()) {
    $select = $this->select()->from($this->info('name'));
    if(!empty($param['customtheme_id']))
      $select->where('theme_id =?', $param['customtheme_id']);
    return $this->fetchAll($select);
  }
}
