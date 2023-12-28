<?php   

class Sitecourse_Model_DbTable_Topics extends Engine_Db_Table
{
	protected $_rowClass = 'Sitecourse_Model_Topic';


	/**
	 * @param course id 
	 * @return topics array belongs to course id
	 */

	public function fetchAllTopics($course_id){
		$select = $this->select()->from($this)->where('course_id = ?',$course_id)->order('order ASC')->query();
		$result = $select->fetchAll();
		return $result;
	}

	/**
	 * @return order value for topic
	 */

	public function getOrder($course_id){
		$select = $this->select()->from($this,array('MAX(`order`) as Order'))->where('course_id = ?',$course_id)->query();
		$result = $select->fetch();
		$order = 1;
		if($result['Order']) $order += $result['Order'];
		return $order;
	}



	public function updateOrder($src,$dest){
		$stmt = $this->select()->from($this,array('order'))->where('topic_id = ?',$src)->query();
		$srcOrder = $stmt->fetch();
		$stmt = $this->select()->from($this,array('order'))->where('topic_id = ?',$dest)->query();
		$destOrder = $stmt->fetch();

		// update the courses category with the form category
		Engine_Api::_()->getItemTable('sitecourse_topic')->update(array('order' => $destOrder['order']),
			array(
				'topic_id = ?'=> $src,
			)
		);

		Engine_Api::_()->getItemTable('sitecourse_topic')->update(array('order' => $srcOrder['order']),
			array(
				'topic_id = ?'=> $dest,
			)
		);




		return true;

	}

	/**
	 * @param course id
	 * @return whether topic belongs to the course
	 * 
	 */

	public function belongsToCourse($course_id){
		$stmt = $this->select()->from($this)->where('course_id = ?',$course_id)->query();
		return $stmt->fetch();
	}
}

?>