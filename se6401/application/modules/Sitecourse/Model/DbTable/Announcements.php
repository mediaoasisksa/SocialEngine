<?php   
class Sitecourse_Model_DbTable_Announcements extends Engine_Db_Table
{
    protected $_rowClass = 'Sitecourse_Model_Announcement';

    /**
     * @param course id
     * @return {array} annoucements
     * 
     */
    public function getAnnouncements($course_id){
        $todayDate = date('Y-m-d');
        $tName = $this->info('name');
        $stmt = $this->select()->from($this)
        ->where('course_id = ?',$course_id)
        ->query();
        return $stmt->fetchAll();
    }

    public function getActiveAnnouncements($course_id){
        $todayDate = date('Y-m-d');
        $tName = $this->info('name');
        $stmt = $this->select()->from($this)
                ->where('course_id = ?',$course_id)
                ->where(sprintf("DATEDIFF('%s',`start_date`) >= 0",$todayDate))
                ->where(sprintf("DATEDIFF(`end_date`,'%s') >= 0",$todayDate))
                ->where('enable = 1')
                ->query();   
        return $stmt->fetchAll();
    }

    public function getTotalActiveAnnouncementsCount($course_id){
        $tName = $this->info('name');
        return $this->select()
        ->from($tName, array('COUNT(*) AS count'))
        ->where($tName . '.course_id = ?', $course_id)
        ->where('enable =?', 1)
        ->query()
        ->fetchColumn();
    }

    public function getAnnouncementPaginator($course_id){
        $paginator = Zend_Paginator::factory($this->getAnnouncements($course_id));
        return $paginator;
    }

    public function getAnnouncement($announcement_id){
        $stmt = $this->select()->from($this)
        ->where('announcement_id = ?',$announcement_id)
        ->query();
        return $stmt->fetch();
    }

}
?>
