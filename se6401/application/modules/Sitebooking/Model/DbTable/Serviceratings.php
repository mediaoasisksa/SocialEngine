<?php

class Sitebooking_Model_DbTable_Serviceratings extends Engine_Db_Table
{
  protected $_rowClass = "Sitebooking_Model_Servicerating";
  public function getRating($ser_id)
  {
    $table  = Engine_Api::_()->getDbTable('serviceratings', 'sitebooking');
    $rating_sum = $table->select()
      ->from($table->info('name'), new Zend_Db_Expr('SUM(rating)'))
      ->group('ser_id')
      ->where('ser_id = ?', $ser_id)
      ->query()
      ->fetchColumn(0)
      ;

    $total = $this->ratingCount($ser_id);
    if ($total) $rating = $rating_sum/$this->ratingCount($ser_id);
    else $rating = 0;
  
    return $rating;
  }


  public function getMyRating($ser_id, $user_id)
  {
    $ratingTable = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');

    $rating = $ratingTable->fetchAll($ratingTable->select()->where('ser_id = ?',  $ser_id)
      ->where('user_id = ?', $user_id));

    return $rating;
  }

  public function checkRated($ser_id, $user_id)
  {
    $table  = Engine_Api::_()->getDbTable('serviceratings', 'sitebooking');

    $rName = $table->info('name');
    $select = $table->select()
           ->setIntegrityCheck(false)
            ->where('ser_id = ?', $ser_id)
            ->where('user_id = ?', $user_id)
            ->limit(1);
    $row = $table->fetchAll($select);
    
    if (count($row)>0) return true;
    return false;
  }

  public function setRating($ser_id, $user_id, $rating){
    $table  = Engine_Api::_()->getDbTable('serviceratings', 'sitebooking');
    $rName = $table->info('name');
    $select = $table->select()
            ->from($rName)
            ->where($rName.'.ser_id = ?', $ser_id)
            ->where($rName.'.user_id = ?', $user_id);
    $row = $table->fetchRow($select);

    if (empty($row)) {
      // create rating
      Engine_Api::_()->getDbTable('serviceratings', 'sitebooking')->insert(array(
      'ser_id' => $ser_id,
      'user_id' => $user_id,
      'rating' => $rating
      ));
    }    
  }

  public function ratingCount($ser_id){
    $table  = Engine_Api::_()->getDbTable('serviceratings', 'sitebooking');

    $rName = $table->info('name');
    $select = $table->select()
            ->from($rName)
            ->where($rName.'.ser_id = ?', $ser_id);
    $row = $table->fetchAll($select);
    $total = count($row);

    return $total;
  }

  public function show_rating_by_star($ser_id) {

    $table = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');

    //1 star
    $select = $table->select()->where('ser_id = ?', $ser_id)
      ->where('rating = ?', 1);
    
    $one_star = Zend_Paginator::factory($select)->getTotalItemCount();
    
    //2 star
    $select = $table->select()->where('ser_id = ?', $ser_id)
      ->where('rating = ?', 2);
    
    $two_star = Zend_Paginator::factory($select)->getTotalItemCount();

    //3 star
    $select = $table->select()->where('ser_id = ?', $ser_id)
      ->where('rating = ?', 3);
    
    $three_star = Zend_Paginator::factory($select)->getTotalItemCount();

    //4 star
    $select = $table->select()->where('ser_id = ?', $ser_id)
      ->where('rating = ?', 4);
    
    $four_star = Zend_Paginator::factory($select)->getTotalItemCount();

    //5 star
    $select = $table->select()->where('ser_id = ?', $ser_id)
      ->where('rating = ?', 5);
    
    $five_star = Zend_Paginator::factory($select)->getTotalItemCount();

    $star_arr = array();
    $star_arr[0] = $one_star;
    $star_arr[1] = $two_star;
    $star_arr[2] = $three_star;
    $star_arr[3] = $four_star;
    $star_arr[4] = $five_star;

    return $star_arr;

  }
}