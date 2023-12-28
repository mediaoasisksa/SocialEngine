<?php   
class Sitecourse_Model_DbTable_Completedcourses extends Engine_Db_Table
{
    protected $_rowClass = 'Sitecourse_Model_Completedcourse';

    /**
     * @param {int} course id
     */
    public function setInfo($course_id){
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();
        $date = date('Y-m-d H:i:s');

        $completedCourseTableName = $this->info('name');
        $select = $this->select()
        ->from($completedCourseTableName)
        ->where($completedCourseTableName . '.course_id = ?', $course_id)
        ->where($completedCourseTableName . '.user_id = ?', $user_id);
        $row = $this->fetchRow($select);
        if (empty($row)) {
            // create entry
            $this->insert(array(
                'course_id' => $course_id,
                'user_id' => $user_id,
                'completion_date' => $date,
                'certificate_issued' => 1,
            ));
        }
    }
    /**
     * @param {int} course id
     */
    public function checkIssuedCertificate($course_id) {
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $checkIssued = $this->select()
        ->from($this->info('name'))
        ->where('course_id = ?', $course_id )
        ->where('user_id = ?', $user_id)
        ->query()
        ->fetchColumn();

        if ($checkIssued)
            return true;
        else
            return false;
    }
}
?>
