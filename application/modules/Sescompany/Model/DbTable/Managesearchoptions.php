<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Managesearchoptions.php 2015-10-28 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Model_DbTable_Managesearchoptions extends Engine_Db_Table {

  protected $_rowClass = "Sescompany_Model_Managesearchoption";

  public function hasType($params = array()) {

    return $this->select()
                    ->from($this->info('name'), array('managesearchoption_id'))
                    ->where('type =?', $params['type'])
                    ->query()
                    ->fetchColumn();
  }

  public function getAllSearchOptions($params = array()) {

    $select = $this->select()->order('order ASC');

    if (isset($params['enabled']) && !empty($params['enabled'])) {
      $select = $select->where('enabled = ?', 1);
    }
    if (isset($params['limit']) && !empty($params['limit'])) {
      $select->limit($params['limit']);
    }
    return $this->fetchAll($select);
  }

}
