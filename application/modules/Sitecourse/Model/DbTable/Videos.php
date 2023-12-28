<?php   
class Sitecourse_Model_DbTable_Videos extends Engine_Db_Table
{
	protected $_rowClass = 'Sitecourse_Model_Video';


	/**
	 * @param parent type | parent id
	 * @return video item
	 * 
	 */
	public function getVideoItem($parent_id,$parent_type){
		$stmt = $this->select()->from($this)->where('parent_type = "'.$parent_type.'" AND parent_id = ?',$parent_id)->query();
		return $stmt->fetch();
	}
	/**
	 * @param video lessons id array
	 * @return storage file ids
	 * 
	 */

	public function getStorageFileIds($ids){
		if(count($ids) <= 0)return array();
		$inString = "(".$ids[0];
		for($i=1;$i<count($ids);$i++) $inString .= ",".$ids[$i];
		$inString .= ")";
		$stmt = $this->select()->from($this,array('file_id'))->where("parent_id IN ".$inString." AND type = 'upload' AND parent_type = 'lesson' ")->query();
		return $stmt->fetchAll();
	}
}
?>
