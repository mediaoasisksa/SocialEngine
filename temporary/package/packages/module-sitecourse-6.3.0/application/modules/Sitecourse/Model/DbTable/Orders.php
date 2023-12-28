<?php   
class Sitecourse_Model_DbTable_Orders extends Engine_Db_Table
{
    protected $_rowClass = 'Sitecourse_Model_Order';


    public function getAllOrders($order_id) {
        $orderCourseTable = Engine_Api::_()->getDbtable('ordercourses','sitecourse');
        $tName = $orderCourseTable->info('name');

        $joinCond = $this->info('name').'.order_id = '.$tName.'.order_id';
        $select = $this->select()
            ->from($this->info('name'))
            ->setIntegrityCheck(false)
            ->joinInner($tName,$joinCond)
            ->where($this->info('name').'.order_id = ?',$order_id);

        return $select->query()->fetchAll();
    }

}
?>
