<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Ratings.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Employment_Model_DbTable_Ratings extends Engine_Db_Table
{
  protected $_rowClass = "Employment_Model_Rating";
  
  public function checkRated($employment_id, $user_id) {

    $rName = $this->info('name');
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->where('employment_id = ?', $employment_id)
        ->where('user_id = ?', $user_id)
        ->limit(1);
    $row = $this->fetchAll($select);

    if (engine_count($row)>0) return true;
    return false;
  }

  public function getRating($employment_id) {

    $rating_sum = $this->select()
        ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
        ->group('employment_id')
        ->where('employment_id = ?', $employment_id)
        ->query()
        ->fetchColumn(0);

    $total = $this->ratingCount($employment_id);
    if ($total) 
      $rating = $rating_sum/$this->ratingCount($employment_id);
    else 
      $rating = 0;

    return $rating;
  }
  
  public function ratingCount($employment_id) {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.employment_id = ?', $employment_id);
    $row = $this->fetchAll($select);
    $total = engine_count($row);
    return $total;
  }

  public function setRating($employment_id, $user_id, $rating) {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.employment_id = ?', $employment_id)
        ->where($rName.'.user_id = ?', $user_id);
    $row = $this->fetchRow($select);
    if (empty($row)) {
      // create rating
      Engine_Api::_()->getDbTable('ratings', 'employment')->insert(array(
        'employment_id' => $employment_id,
        'user_id' => $user_id,
        'rating' => $rating
      ));
    }
  }
}
