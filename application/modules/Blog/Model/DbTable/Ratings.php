<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Ratings.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Blog_Model_DbTable_Ratings extends Engine_Db_Table
{
  protected $_rowClass = "Blog_Model_Rating";
  
  public function checkRated($blog_id, $user_id) {

    $rName = $this->info('name');
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->where('blog_id = ?', $blog_id)
        ->where('user_id = ?', $user_id)
        ->limit(1);
    $row = $this->fetchAll($select);

    if (engine_count($row)>0) return true;
    return false;
  }

  public function getRating($blog_id) {

    $rating_sum = $this->select()
        ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
        ->group('blog_id')
        ->where('blog_id = ?', $blog_id)
        ->query()
        ->fetchColumn(0);

    $total = $this->ratingCount($blog_id);
    if ($total) 
      $rating = $rating_sum/$this->ratingCount($blog_id);
    else 
      $rating = 0;

    return $rating;
  }
  
  public function ratingCount($blog_id) {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.blog_id = ?', $blog_id);
    $row = $this->fetchAll($select);
    $total = engine_count($row);
    return $total;
  }

  public function setRating($blog_id, $user_id, $rating) {

    $rName = $this->info('name');
    $select = $this->select()
        ->from($rName)
        ->where($rName.'.blog_id = ?', $blog_id)
        ->where($rName.'.user_id = ?', $user_id);
    $row = $this->fetchRow($select);
    if (empty($row)) {
      // create rating
      Engine_Api::_()->getDbTable('ratings', 'blog')->insert(array(
        'blog_id' => $blog_id,
        'user_id' => $user_id,
        'rating' => $rating
      ));
    }
  }
}
