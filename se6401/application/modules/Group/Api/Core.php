<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_Api_Core extends Core_Api_Abstract
{
  public function groupMembers($group_id) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $table = Engine_Api::_()->getDbTable('users', 'user');
    $tableName = $table->info('name');

    $membershiptable = Engine_Api::_()->getDbTable('membership', 'group');
    $membershiptableName = $membershiptable->info('name');

    $select = $table->select()
            ->from($tableName)
            ->setIntegrityCheck(false)
            ->join($membershiptableName, '`' . $membershiptableName . '`.`user_id` = `' . $tableName . '`.`user_id`', null)
            ->where('`' . $membershiptableName . '`.`resource_id` = ?', $group_id)
            ->where($membershiptableName.'.resource_approved =?', 1)
            ->where($membershiptableName.'.user_approved =?', 1)
            ->where($membershiptableName.'.user_id <> ?', $viewer_id)
            ->where($membershiptableName.'.notification <> ?', 0)
            ->where($membershiptableName . '.active =?', 1);
    return $table->fetchAll($select);
  }
}
