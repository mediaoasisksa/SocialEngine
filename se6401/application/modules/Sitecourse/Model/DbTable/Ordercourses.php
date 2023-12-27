<?php   
class Sitecourse_Model_DbTable_Ordercourses extends Engine_Db_Table
{
    protected $_rowClass = 'Sitecourse_Model_Ordercourse';

    public function getOrderCourses($order_id) {
        $select = $this->select()->from($this)
                ->where('order_id = ?',$order_id);

        return $select->query()->fetch();
    }

}
?>
