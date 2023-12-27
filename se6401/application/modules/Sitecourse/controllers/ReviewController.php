<?php
class Sitecourse_ReviewController extends Core_Controller_Action_Standard {
	protected $checks ='';

	public function init() {
		if( !$this->_helper->requireUser()->isValid() ) return;
		$this->checks = new Sitecourse_Api_Checks();
	}

	public function indexAction() {
		$this->_helper->layout->setLayout('default-simple');

		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->course_id = $course_id = $this->_getParam('course_id');
		$course = Engine_Api::_()->getItem('sitecourse_course', $this->_getParam('course_id'));
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();
		if($course['owner_id'] == $viewer_id){
			$this->view->isOwner = true;
		} else{
			$this->view->isOwner = false;
		}
		$this->view->canRate = $this->checks->_canRate($course_id);
		$this->view->update_permission = $this->checks->_canRate($course_id);;

		$ratingTable = Engine_Api::_()->getDbtable('reviews', 'sitecourse');
		$this->view->rating_count = $ratingTable->ratingCount(array('course_id' => $course_id));
		$this->view->rated = $rated = $ratingTable->checkRated(array('course_id' => $course_id,'viewer_id'=> $viewer_id));
		if( $rated ){
			$review = $ratingTable->getUserReview($course_id);
			$this->view->review = $review['review'];
			$this->view->review_title = $review['review_title'];
			$this->view->subject_pre_rate = $review['rating'];
		}else{
			$this->view->subject_pre_rate = 0;
		}

		
		$course_title=$course['title'];
		$review='';
		$rating='';
		$review_title='';
		$flag1 = $flag2 = $flag3 =false;

		if (isset($_POST['submit'])) {
			if(isset($_POST['rating'])&&(!empty($_POST['rating']))){
				$rating=$_POST['rating'];
				$flag1 = true;
			}
			if(isset($_POST['review'])&&(!empty($_POST['review']))){
				$review=$_POST['review'];
				$flag2 = true;
			}
			if(isset($_POST['review_title'])&&(!empty($_POST['review_title']))){
				$review_title=$_POST['review_title'];
				$flag3 = true;
			}
		}

		if(!$flag1 || !$flag3 || !$flag2)
			return;

		$ratingTable = Engine_Api::_()->getDbtable('reviews', 'sitecourse');
		$db = $ratingTable->getAdapter();
		$db->beginTransaction();
		try {
			$ratingTable->setRating($course_id, $rating, $review, $review_title);
			$db->commit();
			$avgRating=$ratingTable->getRating($course_id);
			Engine_Api::_()->getItemTable('sitecourse_course')->update(array('rating' => $avgRating),array('course_id =?'=>$course_id));
			$owner = Engine_Api::_()->getItem("user" , $course->owner_id);
			Engine_Api::_()->getDbtable('notifications', 'activity')
			->addNotification($owner,$viewer , $course, 'sitecourse_review',array(
				'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
				'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Successful'),
		));
	}

    //ACTION TO ADD REVIEWS TO THE REVIEW WIDGET AND SEND DATA ARRAY
	public function addReviewAction() {
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$course_id = $params['course_id'];
		$start = $params['start'];
		$limit = $params['limit'];


		$reviews = Engine_Api::_()->getItemTable('sitecourse_review')->fetchReviews($course_id,$start,$limit);
		foreach($reviews as $key => $review){

			$owner = Engine_Api::_()->getItem('user',$review['user_id']);

			$reviews[$key]['owner'] = $owner['displayname'];
			$storage_file = Engine_Api::_()->getItem('storage_file', $owner['photo_id']);

			$src='';
			if(!empty($storage_file)){
				$src=$storage_file->map(); 
			}
			
			$reviews[$key]['owner_image'] =  $src;
			$date = strtotime($review['creation_date']);
			$todayDate = date_create(date('Y-m-d'));
			$diff = '';

			$time = time() - $date; // to get the time since that moment
			$time = ($time<1)? 1 : $time;

			
			$dtF = new DateTime('@0');
			$dtT = new DateTime("@$time");
			$time1='';
			
			if(intval($dtF->diff($dtT)->format('%y')) > 0 ){
				if(intval($dtF->diff($dtT)->format('%y')) > 1 ){
					$time1 = $dtF->diff($dtT)->format('%y years');
				} else {
					$time1 = $dtF->diff($dtT)->format('%y year');
				}
			} elseif (intval($dtF->diff($dtT)->format('%m')) > 0 ){
				if(intval($dtF->diff($dtT)->format('%m')) > 1 ){
					$time1 = $dtF->diff($dtT)->format('%m months');
				} else {
					$time1 = $dtF->diff($dtT)->format('%m month');
				}
			} elseif (intval($dtF->diff($dtT)->format('%a')) > 0 ){
				if(intval($dtF->diff($dtT)->format('%a')) > 1 ){
					$time1 = $dtF->diff($dtT)->format('%a days');
				} else {
					$time1 = $dtF->diff($dtT)->format('%a day');
				}
			} elseif (intval($dtF->diff($dtT)->format('%h')) > 0){
				if(intval($dtF->diff($dtT)->format('%h')) > 1 ){
					$time1 = $dtF->diff($dtT)->format('%h hours');
				} else {
					$time1 = $dtF->diff($dtT)->format('%h hour');
				}
			} elseif (intval($dtF->diff($dtT)->format('%i')) > 0){
				if(intval($dtF->diff($dtT)->format('%i')) > 1 ){
					$time1 = $dtF->diff($dtT)->format('%i minutes');
				} else {
					$time1 = $dtF->diff($dtT)->format('%i minute');
				}
			} else {
				$time1 = $dtF->diff($dtT)->format('%s seconds');
			}
			
			$reviews[$key]['diff'] = $time1;
			$reviews[$key]['delUrl'] = $this->view->url(array('action' => 'delete-review', 'review_id' => $review['review_id']),'sitecourse_review_specific',array());
		}
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$like = array();
		foreach($reviews as $review){
			$like[$review['review_id']]=Engine_Api::_()->getDbtable('reviewlikes','sitecourse')->checkReviewLike($review['review_id'],$viewer_id);
		}
		//print_r($reviews);
		$this->view->likeArr = $like;
		$this->view->reviews = count($reviews) ? $reviews : 'false';
	}

    //ACTION TO CHECK PREVIOUS LIKE/UNLIKE BY THE USER
	public function likeAction() {
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$course_id = $params['course_id'];
		$review_id = $params['review_id'];
		$value = $params['value'];
		$viewer = Engine_Api::_()->user()->getViewer();
		$review=Engine_Api::_()->getItem('sitecourse_review',$review_id);
		$course=Engine_Api::_()->getItem('sitecourse_course',$course_id);

		$reviewLikeTable = Engine_Api::_()->getDbtable('reviewlikes', 'sitecourse');
		$db = $reviewLikeTable->getAdapter();
		$db->beginTransaction();
		$likeDislikeCnt = array();
		try {
			$likeDislikeCnt = $reviewLikeTable->setReviewLike($review_id,$value);
			$db->commit();
			$owner = Engine_Api::_()->getItem("user" , $review['user_id']);
			if($value==1){
				Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($owner,$viewer , $course, 'sitecourse_reviewlike',array(
					'object_link' => Engine_Api::_()->getItem("sitecourse_course",$course->course_id)->getHref(),
					'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));
			}

		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		$this->view->likeCnt = $likeDislikeCnt['like_count'];
		$this->view->dislikeCnt = $likeDislikeCnt['dislike_count'];

	}

	public function deleteReviewAction(){
      // In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$review_id = $this->_getParam('review_id',0);
		if(!$this->getRequest()->isPost()){
			return;
		}

		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();		
		$reviewLike = Engine_Api::_()->getDbTable('reviewlikes','sitecourse');
		try{
			$reviewLike->delete(array('review_id =?'=>$review_id));
			/**
		 		* set avg rating in course table
			  * delete the review form review table
			*/
		 		Engine_Api::_()->getItemTable('sitecourse_review')->deductRating($review_id);
		 		$db->commit();
		 	}catch(Exception $e){
		 		$db->rollBack();
		 		throw $e;
		 	}
		 	return $this->_forward('success', 'utility', 'core', array(
		 		'smoothboxClose' => 20,
		 		'parentRefresh'=> 10,
		 		'messages' => array('Success'),
		 	));
		 }

		 public function deleteAction() {
		 	$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		 	$course_id = $params['course_id'];
		 	$review_id = $params['review_id'];

		 	$db = Engine_Db_Table::getDefaultAdapter();
		 	$db->beginTransaction();
		 	$reviewLike = Engine_Api::_()->getItemTable('sitecourse_reviewlike');
		 	try{
		 		$reviewLike->delete(array('review_id =?'=>$review_id));
		 		Engine_Api::_()->getItemTable('sitecourse_review')->deductRating($review_id);
		 		$db->commit();
		 	}catch(Exception $e){
		 		$db->rollBack();
		 		throw $e;
		 	}
		 }
		}
		?>
