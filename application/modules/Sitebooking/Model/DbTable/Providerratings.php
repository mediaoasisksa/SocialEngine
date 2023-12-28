<?php

class Sitebooking_Model_DbTable_Providerratings extends Engine_Db_Table
{
  protected $_rowClass = "Sitebooking_Model_Providerrating";
    public function getRating($pro_id)
  {
    $table  = Engine_Api::_()->getDbTable('providerratings', 'sitebooking');
    $rating_sum = $table->select()
      ->from($table->info('name'), new Zend_Db_Expr('SUM(rating)'))
      ->group('pro_id')
      ->where('pro_id = ?', $pro_id)
      ->query()
      ->fetchColumn(0)
      ;

    $total = $this->ratingCount($pro_id);
    if ($total) $rating = $rating_sum/$this->ratingCount($pro_id);
    else $rating = 0;
  
    return $rating;
  }

  public function getMyRating($pro_id, $user_id)
  {
    $providerRatingtable = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');

    $rating = $providerRatingtable->fetchAll($providerRatingtable->select()->where('pro_id = ?',  $pro_id)
      ->where('user_id = ?', $user_id));
  
    return $rating;
  }
  
  public function checkRated($pro_id, $user_id)
  {
    $table  = Engine_Api::_()->getDbTable('providerratings', 'sitebooking');

    $rName = $table->info('name');
    $select = $table->select()
           ->setIntegrityCheck(false)
            ->where('pro_id = ?', $pro_id)
            ->where('user_id = ?', $user_id)
            ->limit(1);
    $row = $table->fetchAll($select);
    
    if (count($row)>0) return true;
    return false;
  }

  public function setRating($pro_id, $user_id, $rating){

    $table  = Engine_Api::_()->getDbTable('providerratings', 'sitebooking');
    $rName = $table->info('name');
    $select = $table->select()
            ->from($rName)
            ->where($rName.'.pro_id = ?', $pro_id)
            ->where($rName.'.user_id = ?', $user_id);
    $row = $table->fetchRow($select);

    if (empty($row)) {
      // create rating
      Engine_Api::_()->getDbTable('providerratings', 'sitebooking')->insert(array(
      'pro_id' => $pro_id,
      'user_id' => $user_id,
      'rating' => $rating
      ));
    }    
  }

  public function ratingCount($pro_id){
    $table  = Engine_Api::_()->getDbTable('providerratings', 'sitebooking');

    $rName = $table->info('name');
    $select = $table->select()
            ->from($rName)
            ->where($rName.'.pro_id = ?', $pro_id);
    $row = $table->fetchAll($select);
    $total = count($row);

    return $total;
  }

  public function show_rating_by_star($pro_id) {

    $table = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');

    //1 star
    $select = $table->select()->where('pro_id = ?', $pro_id)
      ->where('rating = ?', 1);
    
    $one_star = Zend_Paginator::factory($select)->getTotalItemCount();
    
    //2 star
    $select = $table->select()->where('pro_id = ?', $pro_id)
      ->where('rating = ?', 2);
    
    $two_star = Zend_Paginator::factory($select)->getTotalItemCount();

    //3 star
    $select = $table->select()->where('pro_id = ?', $pro_id)
      ->where('rating = ?', 3);
    
    $three_star = Zend_Paginator::factory($select)->getTotalItemCount();

    //4 star
    $select = $table->select()->where('pro_id = ?', $pro_id)
      ->where('rating = ?', 4);
    
    $four_star = Zend_Paginator::factory($select)->getTotalItemCount();

    //5 star
    $select = $table->select()->where('pro_id = ?', $pro_id)
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