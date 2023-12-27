<?php   
class Sitecourse_Model_DbTable_Buyerdetails extends Engine_Db_Table
{
    protected $_rowClass = 'Sitecourse_Model_Buyerdetail';
    
    /**
     * @param {int} course id
     * @return {array} buyers details 
     * 
     */
    public function getBuyersDetails($course_id){
        $transactionTb = Engine_Api::_()->getDbtable('transactions', 'sitecourse');
        $transactionTbName = $transactionTb->info('name');
        $buyerDetailTbName = $this->info('name');

        $stmt = $this->select()->from($this)
        ->setIntegrityCheck(false)
        ->joinInner($transactionTbName, "$buyerDetailTbName.`order_id` = $transactionTbName.`order_id`")
        ->where('course_id = ?', $course_id)
        ->query();
        return $stmt->fetchAll();
    }

    /**
     * @param {int} course id,buyer id
     * @return {boolean} course is purchased by user or not
     * 
     */
    public  function validatePurchase($course_id,$buyer_id){
        $stmt = $this->select()->from($this,array('COUNT(*) as total'))
                ->where('course_id = ?',$course_id)
                ->where('buyer_id = ?',$buyer_id)
                ->query();
        $result = $stmt->fetch();
        return $result['total']?true:false;
    }

    /**
     * @param {int} course id
     * @return {int} enrolled users count for given course
     */
    public function courseEnrollementCount($course_id){
        $stmt = $this->select()->from($this,array('COUNT(*) as count'))
                ->where('course_id = ?',$course_id)
                ->query();
        $result = $stmt->fetch();
        return $result['count'];
    }
    
     public function getEnrolledPaginator($course_id){
        $paginator = Zend_Paginator::factory($this->getBuyersDetails($course_id));
        return $paginator;
    }
}
?>
