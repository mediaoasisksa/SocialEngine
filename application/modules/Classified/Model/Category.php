<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Category.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Model_Category extends Core_Model_Category
{
  protected $_searchTriggers = false;
  protected $_route = 'classified_general';

  public function getTitle()
  {
    return $this->category_name;
  }

  public function getUsedCount()
  {
    $classifiedTable = Engine_Api::_()->getItemTable('classified');
    return $classifiedTable->select()
        ->from($classifiedTable, new Zend_Db_Expr('COUNT(classified_id)'))
        ->where('category_id = ?', $this->category_id)
        ->query()
        ->fetchColumn();
  }

  public function isOwner($owner)
  {
    return false;
  }

  public function getOwner($recurseType = null)
  {
    return $this;
  }

  public function getHref($params = array())
  {
    return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $this->_route, true) . '?category=' . $this->category_id;
  }
}
