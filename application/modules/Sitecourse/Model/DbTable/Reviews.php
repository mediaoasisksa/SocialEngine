<?php   
class Sitecourse_Model_DbTable_Reviews extends Engine_Db_Table
{

	protected $_rowClass = 'Sitecourse_Model_Review';

	/**
     * @param {int} course id
     * @return {int} total count of approved likes 
     */
	public function ratingCount($params = array()) {
		$ratingTableName = $this->info('name');
		return $this->select()
		->from($ratingTableName, array('COUNT(*) AS count'))
		->where($ratingTableName . '.course_id = ?', $params['course_id'])
		->where('status =?', 1)
		->query()
		->fetchColumn();
	}

	/**
     * @param {int} course id,user id
     * @return {bool} already rated by user or not 
     */
	public function checkRated($params = array()) {
		
		$checkRated = $this->select()
		->from($this->info('name'), array('review_id'))
		->where('course_id = ?', $params['course_id'])
		->where('user_id = ?', $params['viewer_id'])
		->query()
		->fetchColumn();

		if ($checkRated)
			return true;
		else
			return false;
	}


	public function setRating($course_id, $rating, $review, $review_title) {
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
		$date = date('Y-m-d H:i:s');
		$status = 0;
		$autoApprove =Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecourse_course', 'auto_review_approve');
		if($autoApprove){
			$status = 1; 
		}

		$ratingTableName = $this->info('name');
		$select = $this->select()
		->from($ratingTableName)
		->where($ratingTableName . '.course_id = ?', $course_id)
		->where($ratingTableName . '.user_id = ?', $user_id);
		$row = $this->fetchRow($select);
		if (empty($row)) {
            // create rating
			$this->insert(array(
				'course_id' => $course_id,
				'user_id' => $user_id,
				'rating' => $rating,
				'review' => $review,
				'review_title' => $review_title,
				'creation_date' => $date,
				'status' => $status,
			));
		} else {
			$this->update(array(
				'rating' => $rating, 
				'review' => $review, 
				'review_title' => $review_title,
			), array('course_id=?' => $course_id,'user_id=?' => $user_id));	
		}
	}

	/**
     * @param {int} course id
     * @return {int} avg rating 
     */
	public function getRating($course_id) {

		$rating_sum = $this->select()
		->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
		->group('course_id')
		->where('course_id = ?', $course_id)
		->where('status =?' ,1)
		->query()
		->fetchColumn(0);

		$total = $this->ratingCount(array('course_id' => $course_id));
		if ($total)
			$rating = $rating_sum / $this->ratingCount(array('course_id' => $course_id,));
		else
			$rating = 0;
		return $rating;
	}   

	public function reviewsCount($course_id){
		$result = $this->select()->from($this,array('COUNT(*) as count'))->where('course_id = ?',$course_id)->query()->fetch();
		return $result['count'];
	}

	/**
     * @param {int} course id
     * @return user review 
     */
	public function getUserReview($course_id) {
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		$ratingTableName = $this->info('name');
		$select = $this->select()
		->from($ratingTableName)
		->where($ratingTableName . '.course_id = ?', $course_id)
		->where($ratingTableName . '.user_id = ?', $user_id)
		->query()
		->fetch();

		return $select;

	}

	public function getAllReviews($course_id) {
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$ratingTableName = $this->info('name');
		$select = $this->select()
		->from($ratingTableName)
		->where($ratingTableName . '.course_id = ?', $course_id)
		->order('review_id DESC')
		->query()
		->fetchAll();
		
		return $select;
	}

	/**
     * @param {int} course id, start, limit
     * @return all the reviews from start to start + limit 
     */
	public function fetchReviews($course_id,$start,$limit){
		$ratingTableName = $this->info('name');
		$select = $this->select()
		->from($ratingTableName)
		->where($ratingTableName . '.course_id = ?', $course_id)
		->order('review_id ASC')
		->limit($limit,$start)
		->query()
		->fetchAll();
		return $select;
	}
	public function deductRating($review_id){
		$review = Engine_Api::_()->getItem('sitecourse_review', $review_id);
		$course_id = $review['course_id'];
		if(!$review){
			return;
		}
		$review->delete();
		$rating_sum = $this->select()
		->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
		->group('course_id')
		->where('course_id = ?', $course_id)
		->where('status =?', 1)
		->query()
		->fetchColumn(0); 

		$total = $this->ratingCount(array('course_id' => $course_id));
		
		$avgRating = $rating_sum/$total;

		Engine_Api::_()->getItemTable('sitecourse_course')->update(array('rating' => $avgRating),array('course_id =?'=>$course_id));
	}


}
?>
