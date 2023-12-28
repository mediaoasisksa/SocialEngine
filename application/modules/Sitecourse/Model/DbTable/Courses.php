<?php   

class Sitecourse_Model_DbTable_Courses extends Core_Model_Item_DbTable_Abstract
{
	protected $_rowClass = 'Sitecourse_Model_Course';

	/**
	 * @param {int} user id
	 * @return {int} course count of current user
	 */
	public function getCourseCount($user_id){
		$select = $this->select()->from($this,'COUNT(owner_id) As total')->where('owner_id = ?',$user_id)->query();
		$result = $select->fetch();

		return $result['total'];
	}

	/**
	 * @param {int} user id
	 * @return {int} approved course count of current user
	 */
	public function getApprovedCourseCount($user_id){
		$select = $this->select()
		->from($this,'COUNT(owner_id) As total')
		->where('owner_id = ?',$user_id)
		->where('approved = ?',1)
		->where('draft = ?',0)
		->query();
		$result = $select->fetch();

		return $result['total'];
	}

	/**
	 * @param {string} course url
	 * @return {array} whether url is already used or not
	 * 
	 */
	public function urlExists($url){
		$stmt = $this->select()->from($this)
		->where("url = ?",$url)->query();
		$result = $stmt->fetch();
		return $result;
	}

	/**
	 * @param {int} number of courses to fetch
	 * @param {int} course selection criteria {0,1,2}
	 * @param {int} newest threshold
	 * @return {array} course
	 * 
	 */
	public function getNewestCourses($count,$criteria,$newestThreshold){
		$todayDate = date('Y-m-d');
		$table = Engine_Api::_()->getDbtable('courses', 'sitecourse');
		$rName = $table->info('name');
		// 0 -> based on creation date ascending
		if($criteria == 0){
			$stmt = $this->select()->from($this)->where('DATEDIFF("'.$todayDate.'" ,'.$rName.'.`creation_date`'.') BETWEEN 0 AND '.$newestThreshold)
			->where('draft = 0 AND approved = 1')
			->order('creation_date ASC')->limit($count)->query();
		}

		// 1 -> random
		if($criteria == 1){
			$stmt = $this->select()->from($this)->where('DATEDIFF("'.$todayDate.'" ,'.$rName.'.`creation_date`'.') BETWEEN 0 AND '.$newestThreshold)
			->where('draft = 0 AND approved = 1')
			->order('Rand()')->limit($count)->query();
		}	
		$result = $stmt->fetchAll();
		
		return $result;
	}

	public function getCoursesSelect($params = array())
	{ 
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		$table = Engine_Api::_()->getDbtable('courses', 'sitecourse');
		$rName = $table->info('name');

		$tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
		$tmName = $tmTable->info('name');

		$buyTable = Engine_Api::_()->getDbtable('buyerdetails', 'sitecourse');
		$byName = $buyTable->info('name');

		
		$select = $table->select();

		if(empty($params['user_id']) && empty($params['buyer_id'])) {
			$select = $this->getItemsSelect($params, $select);
		}
		if( !empty($params['my_purchased']) )
		{
			$select
			->setIntegrityCheck(false)
			->from($rName)
			->joinLeft($byName, "$byName.course_id = $rName.course_id")
			->where($byName.'.buyer_id = ?', $params['buyer_id']);
		}
		

		if( !empty($params['category_id']) )
		{
			$select->where($rName.'.category_id = ?', $params['category_id']);
		}

		if( !empty($params['subcategory_id']) && $params['category_id'])
		{
			$select->where($rName.'.subcategory_id = ?', $params['subcategory_id']);
		}

		if( !empty($params['title']) )
		{	
			$select->where($rName.".title LIKE ? ", '%'.$params['title'].'%');
		}
		if( isset($params['difficulty_level']) && $params['difficulty_level'] != 3 )
		{
			$select->where($rName.'.difficulty_level = ?', $params['difficulty_level']);
		}
		if( !empty($params['minprice']) )
		{
			$select->where($rName.'.price >= ?', $params['minprice']);
		}

		if( !empty($params['maxprice']) )
		{
			$select->where($rName.'.price <= ?', $params['maxprice']);
		}
		if( !empty($params['user_id']) )
		{
			$select->where($rName.'.owner_id = ?', $params['user_id']);
		}
		if( !empty($params['tag']) )
		{
			$select
			->setIntegrityCheck(false)
			->from($rName)
			->joinLeft($tmName, "$tmName.resource_id = $rName.course_id")
			->where($tmName.'.resource_type = ?', 'sitecourse_course')
			->where($tmName.'.tag_id = ?', $params['tag']);
		}

		if (!empty($params['orderby'])) {
			if($params['orderby'] == 1){
				$select->order($rName . '.creation_date DESC');
			}else if($params['orderby'] == 2){
				$select->order($rName . '.rating DESC');
			} else if($params['orderby'] == 3){
				$favTable = Engine_Api::_()->getDbtable('favourites', 'sitecourse');
				$favName = $favTable->info('name');

				$select
				->setIntegrityCheck(false)
				->from($rName)
				->joinLeft($favName, "$favName.course_id = $rName.course_id")
				->where($favName.'.owner_id = ?', $viewer_id);
			}
		}

		
		// browse courses
		if(empty($params['user_id']) && !isset($params['buyer_id']) && !empty($params['is_ajax'])) {
			$select->where($rName.'.draft = 0 AND '.$rName.'.approved = 1');
		} 
//echo $select;
		// privacy check code
		
		if(!empty($params['is_ajax'])) {
			$select->limit($params['limit'],$params['offset']);
			return $select->query()->fetchAll();
		}
//echo $select; die;
		return $select;

	}

	/**
	 * @param {int} number of courses to fetch
	 * @param {int} sorting criteria {0,1,2}
	 * @return {array} courses 
	 */

	public function getBestsellerCourses($count,$criteria){
		// based on creation date
		if($criteria == 0){
			$stmt= $this->select()->from($this)
			->where('bestseller = 1')
			->order('creation_date ASC');
		}

		// based on enrollement count
		if($criteria == 1){
			$stmt = $this->select()->from($this)->where('bestseller = 1')->order('enrolled_count ASC');
		}

		// random
		if($criteria == 2){
			$stmt = $this->select()->from($this)->where('bestseller = 1')->order('Rand()');
		}
		$stmt->where('draft = 0 AND approved = 1')
		->limit($count);
		return $stmt->query()->fetchAll();

	}


	/**
	 * @param {int} count number of courses to fetch
	 * @param {int} course selection criteria {0,1,2}
	 * @return {array}courses
	 */

	public function getFullSliderCourses($count,$criteria,$newestThreshold){
		$todayDate = date('Y-m-d');
		$table = Engine_Api::_()->getDbtable('courses', 'sitecourse');
		$rName = $table->info('name');

		// newest courses only
		if($criteria == 0){
			$stmt = $this->select()->from($this)->where('DATEDIFF("'.$todayDate.'" ,'.$rName.'.`creation_date`'.') BETWEEN 0 AND '.$newestThreshold)
			->where('draft = 0 AND approved = 1')
			->order('creation_date ASC')->limit($count)->query();
		}

		//toprated courses only
		if($criteria == 1){
			$topRatedThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.mostrated.threshold', '2');
			return $this->getTopratedCourses($count,$criteria,$topRatedThreshold);
		}
		//bestseller courses only
		if($criteria == 2){
			return $this->getBestsellerCourses($count,0);
		}
		return $stmt->fetchAll();
	}

	/**
	 * @param  {int} count number of courses to fetch
	 * @param  {int} criteria by which arrange the rows
	 * @param  {int} ratingThreshold Columns to select where rating is equal or greaterthen
	 * @return {array} courses
	 * 
	 */

	public function getTopratedCourses($count,$criteria,$ratingThreshold) {
		$stmt = $this->select();

		$stmt->from($this)->where('rating >= ?', $ratingThreshold);
		$stmt->where('draft = 0 AND approved = 1');
		// order by creation date
		if($criteria == 0){
			$stmt->order('creation_date ASC');
		}
		// order by avg rating
		if($criteria == 1){
			$stmt->order('rating DESC');
		}
		// order randomly
		if($criteria == 2){
			$stmt->order('Rand()');
		}	
		$stmt->limit($count);
		$stmt = $stmt->query();
		return $stmt->fetchAll();		
	}

	/**
	 * @param {int} itemCount number of courses to fetch
	 * @param {int} courseType 0->bestseller,1->newest,2->toprated
	 * @param {int} sortingCriteria 0->creation_date,1->random
	 * @return {array} courses
	 * 
	 */
	public function getCarouselCourses($itemCount,$courseType,$sortingCriteria){
		$courses = array();
		$topRatedThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.mostrated.threshold', '2');
		$newestThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.latest.threshold', '5');
		switch($courseType){
			case 0:
			$courses = $this->getBestsellerCourses($itemCount,($sortingCriteria)?2:0);
			break;
			case 2:
			$courses = $this->getTopratedCourses($itemCount,($sortingCriteria)?2:0,$topRatedThreshold);
			break;
			case 1:
			$courses = $this->getNewestCourses($itemCount,$sortingCriteria,$newestThreshold);
		}
		return $courses;
	}

	/**
	 * @param {int} course id
	 * @return {boolean} can user publish course
	 * 
	 */
	public function canPublish($course_id){
		$table = Engine_Api::_()->getDbtable('lessons', 'sitecourse');
		$lessons = $table->getLessonsBelongsToCourse($course_id);
		return (count($lessons))?true:false;
	}


    /**
     * Gets a paginator for courses
     *
     * @param Core_Model_Item_Abstract $user The user to get the messages for
     * @return Zend_Paginator
     */
    public function getCoursesPaginator($params = array())
    {
    	$paginator = Zend_Paginator::factory($this->getCoursesSelect($params));
    	return $paginator;
    }

    /**
     * @return courses count
     */
    public function getCoursesCount(){
    	$result = $this->select()->from($this,array('COUNT(*) AS count'))
    	->where('draft = 0 AND approved = 1')->query()->fetch();
    	return $result['count'];
    }

    /**
     * @param {int} course id
     * @return {string} course_benefits column
     */
    public function getCourseBenefits($course_id){
    	$select = $this->select()->from($this,array('course_benefits'))->where('course_id = ?',$course_id)->query();
    	$result = $select->fetch();

    	return $result['course_benefits'];
    }
    /**
     * @param {int} course id
     * @return {string} course_overview column
     */
    public function getCourseOverview($course_id){
    	$select = $this->select()->from($this,array('overview'))->where('course_id = ?',$course_id)->query();
    	$result = $select->fetch();

    	return $result['overview'];
    }
    /**
     * @param {int} course id
     * @return {string} course_prerequisites column
     */
    public function getCourseRequirements($course_id){
    	$select = $this->select()->from($this,array('prerequisites'))->where('course_id = ?',$course_id)->query();
    	$result = $select->fetch();

    	return $result['prerequisites'];
    }
    /**
     * @param {int} course id
     * @return {string} about_instructor column
     */
    public function getAboutInstructor($course_id){
    	$select = $this->select()->from($this,array('about_instructor'))->where('course_id = ?',$course_id)->query();
    	$result = $select->fetch();

    	return $result['about_instructor'];
    }

    /**
     * @param {int} limit number of rows to fetch
     * @param {boolean} order by aplhabetical
     * @return {array} tags
     */
    public function getTagCloud($limit = 100,$alphabetical = false) {
    	$tableTagmaps = 'engine4_core_tagmaps';
    	$tableTags = 'engine4_core_tags';
    	$tableSitecourse = $this->info('name');
    	$select = $this->select()
    	->setIntegrityCheck(false)
    	->from($tableSitecourse, array(''))
    	->joinInner($tableTagmaps, "$tableSitecourse.course_id = $tableTagmaps.resource_id", array('COUNT(engine4_core_tagmaps.resource_id) AS Frequency'))
    	->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'))
    	->where($tableSitecourse . '.draft = ?', "0")
    	->where($tableSitecourse . '.approved = ?', "1")
    	->where($tableTagmaps . '.resource_type = ?', 'sitecourse_course')
    	->group($tableTags.".text")
    	->order("Frequency DESC");
    	if($alphabetical)
    		$select->order($tableTags.".text ASC");

    	if($limit)
    		$select = $select->limit($limit);
    	return $select->query()->fetchAll();
    }

    public function getCourseTags($limit = 5,$alphabetical = false,$course_id) {
    	$tableTagmaps = 'engine4_core_tagmaps';
    	$tableTags = 'engine4_core_tags';
    	$tableSitecourse = $this->info('name');
    	$select = $this->select()
    	->setIntegrityCheck(false)
    	->from($tableSitecourse, array(''))
    	->joinInner($tableTagmaps, "$tableSitecourse.course_id = $tableTagmaps.resource_id", array('COUNT(engine4_core_tagmaps.resource_id) AS Frequency'))
    	->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'))
    	->where($tableSitecourse . '.course_id = ?', $course_id)
    	->where($tableTagmaps . '.resource_type = ?', 'sitecourse_course')
    	->group($tableTags.".text")
    	->order("Frequency DESC");
    	if($alphabetical)
    		$select->order($tableTags.".text ASC");

    	if($limit)
    		$select = $select->limit($limit);
    	return $select->query()->fetchAll();
    }


    public function isTopRatedCourse($course_id) {
    	$ratingThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.mostrated.threshold', 4);
    	$stmt = $this->select();

    	$stmt->from($this, array('COUNT(*) as total'))->where('rating >= ?', $ratingThreshold);
    	$stmt->where('course_id = ?', $course_id)->where('draft = 0 AND approved = 1');

    	$result = $stmt->query()->fetch();

    	return $result['total']; 
    }

    public function isNewestCourse($course_id) {
    	$newestThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.latest.threshold', '5');    	 
    	$todayDate = date('Y-m-d');
    	$table = Engine_Api::_()->getDbtable('courses', 'sitecourse');
    	$rName = $table->info('name');
    	$stmt = $this->select()->from($this, array('COUNT(*) as total'))
    	->where('DATEDIFF("'.$todayDate.'" ,'.$rName.'.`creation_date`'.') BETWEEN 0 AND '.$newestThreshold)
    	->where('draft = 0 AND approved = 1')
    	->where('course_id = ?', $course_id);

    	$result = $stmt->query()->fetch();

    	return $result['total'];

    }

}

?>
