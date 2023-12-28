<?php   

class Sitecourse_Model_DbTable_Favourites extends Engine_Db_Table
{
    protected $_rowClass = 'Sitecourse_Model_Favourite';

    /**
     * @param {Int} owner_id
     * @param {Int} course_id
     * @return {boolean} course is in favourite or not
     */
    public function isFavourite($course_id,$owner_id){
        $stmt = $this->select()->from($this,array('COUNT(*) As count'))
                ->where('course_id = ?',$course_id)
                ->where('owner_id = ?',$owner_id)
                ->query();
        $result = $stmt->fetch();
        return (isset($result['count']) && $result['count'] == 1)?true:false;
    }

    /**
     * @param {Int} owner_id
     * @return {array} favourite courses
     */
    public function getFavouriteCourses($viewer_id){
        $stmt = $this->select()->from($this)
                ->where('owner_id = ?',$viewer_id)
                ->query();
        return $stmt->fetchAll();
    }

    public function getFavouriteCount($course_id) {
        $stmt = $this->select()->from($this, array("COUNT(*) as total"))->where('course_id = ?', $course_id)->query();
        $result = $stmt->fetch();
        return $result['total']; 
    }


}

?>
