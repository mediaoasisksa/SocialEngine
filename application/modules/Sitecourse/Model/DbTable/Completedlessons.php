<?php   
class Sitecourse_Model_DbTable_Completedlessons extends Engine_Db_Table
{
	protected $_rowClass = 'Sitecourse_Model_Completedlesson';

	/**
	 * @param {int} course id,lesson id,topic id,viewer id
	 * @return {boolean} topic is completed by the user or not
	 */
	public function isCompleted($course_id,$lesson_id,$topic_id,$viewer_id){
		$stmt = $this->select()->from($this,array('COUNT(*) As count'))
		->where('course_id = ?',$course_id)
		->where('lesson_id = ?',$lesson_id)
		->where('topic_id = ?',$topic_id)
		->where('user_id = ?',$viewer_id)
		->query();

		$result = $stmt->fetch();
		return (isset($result['count']) && $result['count'] == 1)?true:false;
	}


	public function fetchCompletedLessons($course_id,$user_id) {
		$stmt = $this->select()->from($this)
				->where('course_id = ?',$course_id)
				->where('user_id = ?',$user_id)
				->query();
		return $stmt->fetchAll();
	}

	public function getCompletedLessonsCount($course_id, $user_id) {
		$stmt = $this->select()->from($this, array('COUNT(*) As total'))
				->where('course_id = ?',$course_id)
				->where('user_id = ?',$user_id)
				->query();
		$result = $stmt->fetch();

		return $result['total'];
	}


}
?>
