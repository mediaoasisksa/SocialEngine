<?php   
class Sitecourse_Model_DbTable_Lessons extends Engine_Db_Table
{
	protected $_rowClass = 'Sitecourse_Model_Lesson';
	/**
	 * @param topic id 
	 * @return Lesson array belongs to topic id
	 */

	public function fetchAllLessons($course_id){
		$select = $this->select()->from($this)->where('course_id = ?',$course_id)->order('order ASC')->query();
		$result = $select->fetchAll();
		return $result;
	}


	/**
	 * @param topic id 
	 * @return Lesson array belongs to topic id
	 */

	public function fetchAllVideoLessons($topic_id){
		$select = $this->select()->from($this)->where('topic_id = ? AND type = "video"',$topic_id)->order('order ASC')->query();
		$result = $select->fetchAll();
		return $result;
	}


	/**
	 * @return last entry id
	 * @param topic id for which required the last entry
	 * 
	 */
	public function getLastEntry($topic_id){
		$stmt = $this->select()->from($this,'Max(lesson_id) as Id')
		->where('topic_id = ?',$topic_id)->query();
		$result = $stmt->fetch();
		return $result['Id'];
	}

	/**
	 * @return all lessons under given course
	 * @param course id
	 */
	public function getLessonsBelongsToCourse($course_id){
		$stmt = $this->select()->from($this)->where('course_id=?',$course_id)->query();
		return $stmt->fetchAll();
	}


	/**
	 * @param {int} lesson id
	 * @return {array} lesson
	 */
	public function getLesson($lesson_id){
		$stmt = $this->select()->from($this)->where('lesson_id = ?',$lesson_id)->query();
		return $stmt->fetch();
	}

	public function getLessonsCount($course_id) {
		$stmt = $this->select()->from($this, array('COUNT(*) As total'))
				->where('course_id = ?',$course_id)
				->query();
		$result = $stmt->fetch();

		return $result['total'];
	}

}


?>
