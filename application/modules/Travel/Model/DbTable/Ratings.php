<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Ratings.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Travel_Model_DbTable_Ratings extends Engine_Db_Table
{
  protected $_rowClass = "Travel_Model_Rating";
  
  public function checkRated($travel_id, $user_id) {

    $rName = $this->info('name');
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->where('travel_id = ?', $travel_id)
        ->where('user_id = ?', $user_id)
        ->limit(1);
    $row = $this->fetchAll($select);

    if (engine_count($row)>0) return true;
    return false;
  }

  public function getRating($travel_id) {

    $rating_sum = $this->select()
        ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
        ->group('travel_id')
        ->where('travel_id = ?', $travel_id)
        ->query()
        ->fetchColumn(0);

    $total = $this->ratingCount($travel_id);
    if ($total) 
      $rating = $rating_sum/$this->ratingCount($travel_id);
    else 
      $rating = 0;

    return $rating;
  }
  
  public function ratingCount($travel_id) {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.travel_id = ?', $travel_id);
    $row = $this->fetchAll($select);
    $total = engine_count($row);
    return $total;
  }

  public function setRating($travel_id, $user_id, $rating) {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.travel_id = ?', $travel_id)
        ->where($rName.'.user_id = ?', $user_id);
    $row = $this->fetchRow($select);
    if (empty($row)) {
      // create rating
      Engine_Api::_()->getDbTable('ratings', 'travel')->insert(array(
        'travel_id' => $travel_id,
        'user_id' => $user_id,
        'rating' => $rating
      ));
    }
  }
}
