<?php   

class Sitecourse_Model_DbTable_Reports extends Engine_Db_Table
{
    protected $_rowClass = 'Sitecourse_Model_Report';

    public function getReportsSelect($params){
        // course table
        $csTable = Engine_Api::_()->getDbtable('courses','sitecourse');
        // course table name
        $csName = $csTable->info('name');
        $select = $this->select()->from($this)->setIntegrityCheck(false)
        ->joinInner($csName,sprintf("%s.course_id = %s.course_id",$csName,$this->info('name')),array($csName.'.title'));
        if(isset($params['order']) && isset($params['order_direction']) 
            && !empty($params['order']) && !empty($params['order'])){
            $select->order($params['order']." ".$params['order_direction']);
        }
        else
        $select->order("course_id ASC");
        return $select;
    }

    public function getReportsPaginator($params = null){
        $paginator = Zend_Paginator::factory($this->getReportsSelect($params));
        if( !empty($params['page']) )
        {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
            $paginator->setItemCountPerPage($params['limit']);
        }

        if( empty($params['limit']) )
        {
            $page = 10;
            $paginator->setItemCountPerPage($page);
        }

        return $paginator;
    }

	public function getReport($viewer, $course_id){
		$stmt = $this->select()->from($this)
		->where('reporter_id = ?', $viewer)
		->where('course_id = ?',$course_id)
		->query();
		return $stmt->fetch();
	}
}
?>
