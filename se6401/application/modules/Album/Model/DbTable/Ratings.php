<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Ratings.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_Model_DbTable_Ratings extends Engine_Db_Table
{
  protected $_rowClass = "Album_Model_Rating";
  
  public function checkRated($album_id, $user_id, $type = 'album') {

    $rName = $this->info('name');
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->where('album_id = ?', $album_id)
        ->where('type = ?', $type)
        ->where('user_id = ?', $user_id)
        ->limit(1);
    $row = $this->fetchAll($select);

    if (engine_count($row)>0) return true;
    return false;
  }

  public function getRating($album_id, $type = 'album') {

    $rating_sum = $this->select()
        ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
        ->group('album_id')
        ->where('album_id = ?', $album_id)
        ->where('type = ?', $type)
        ->query()
        ->fetchColumn(0);

    $total = $this->ratingCount($album_id, $type);
    if ($total) 
      $rating = $rating_sum/$this->ratingCount($album_id, $type);
    else 
      $rating = 0;

    return $rating;
  }
  
  public function ratingCount($album_id, $type = 'album') {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.type = ?', $type)
        ->where($rName.'.album_id = ?', $album_id);
    $row = $this->fetchAll($select);
    $total = engine_count($row);
    return $total;
  }

  public function setRating($album_id, $user_id, $rating, $type = 'album') {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.type = ?', $type)
        ->where($rName.'.album_id = ?', $album_id)
        ->where($rName.'.user_id = ?', $user_id);
    $row = $this->fetchRow($select);
    if (empty($row)) {
      // create rating
      Engine_Api::_()->getDbTable('ratings', 'album')->insert(array(
        'type' => $type,
        'album_id' => $album_id,
        'user_id' => $user_id,
        'rating' => $rating
      ));
    }
  }
}
