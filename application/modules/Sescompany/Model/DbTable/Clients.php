<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Clients.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sescompany_Model_DbTable_Clients extends Engine_Db_Table {

	protected $_rowClass = "Sescompany_Model_Client";

  public function getClients($show_type = 1) {
  
    $tableName = $this->info('name');
    $select = $this->select()->from($tableName);
    
    if(empty($show_type))
      $select->where('enabled =?', 1);

    $select ->order('client_id DESC');

    return Zend_Paginator::factory($select);
  }

}
