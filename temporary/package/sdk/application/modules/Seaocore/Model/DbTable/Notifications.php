<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Comment.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Seaocore_Model_DbTable_Notifications extends Engine_Db_Table
{
  // protected $_rowClass = 'Seaocore_Model_UserInfo';
  public function select($withFromPart = self::SELECT_WITHOUT_FROM_PART)
  {
    $select = parent::select($withFromPart);
    $mapTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $select->from($this->info('name'), '*')->setIntegrityCheck(false)->joinLeft($mapTable->info('name'), $this->info('name') . '.notification_id = ' . $mapTable->info('name') . '.notification_id');
    return $select;
  }
  public function update(array $data, $where)
  {
    $updateData = array();
    foreach( $this->_getCols() as $col ) {
      if( isset($data[$col]) ) {
        $updateData[$col] = $data[$col];
      }
    }
    if( empty($updateData) ) {
      return;
    }
    return parent::update($updateData, $where);
  } 

}