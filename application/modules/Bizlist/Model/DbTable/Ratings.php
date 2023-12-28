<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Ratings.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Bizlist_Model_DbTable_Ratings extends Engine_Db_Table
{
  protected $_rowClass = "Bizlist_Model_Rating";
  
  public function checkRated($bizlist_id, $user_id) {

    $rName = $this->info('name');
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->where('bizlist_id = ?', $bizlist_id)
        ->where('user_id = ?', $user_id)
        ->limit(1);
    $row = $this->fetchAll($select);

    if (engine_count($row)>0) return true;
    return false;
  }

  public function getRating($bizlist_id) {

    $rating_sum = $this->select()
        ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
        ->group('bizlist_id')
        ->where('bizlist_id = ?', $bizlist_id)
        ->query()
        ->fetchColumn(0);

    $total = $this->ratingCount($bizlist_id);
    if ($total) 
      $rating = $rating_sum/$this->ratingCount($bizlist_id);
    else 
      $rating = 0;

    return $rating;
  }
  
  public function ratingCount($bizlist_id) {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.bizlist_id = ?', $bizlist_id);
    $row = $this->fetchAll($select);
    $total = engine_count($row);
    return $total;
  }

  public function setRating($bizlist_id, $user_id, $rating) {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.bizlist_id = ?', $bizlist_id)
        ->where($rName.'.user_id = ?', $user_id);
    $row = $this->fetchRow($select);
    if (empty($row)) {
      // create rating
      Engine_Api::_()->getDbTable('ratings', 'bizlist')->insert(array(
        'bizlist_id' => $bizlist_id,
        'user_id' => $user_id,
        'rating' => $rating
      ));
    }
  }
}
