<?php

class Sitecourse_Model_DbTable_Transactions extends Engine_Db_Table {

    protected $_rowClass = 'Sitecourse_Model_Transaction';

    public function getTransactionsSelect($params = array()) {
        $select = $this->select()->from($this);
        $ordercourses_table = Engine_Api::_()->getDbtable('ordercourses','sitecourse');
        $ordercourses_table_name = $ordercourses_table->info('name');
        $curr_table_name = $this->info('name');

        $user_table = Engine_Api::_()->getItemTable('user');
        $user_table_name = $user_table->info('name');

        $select->setIntegrityCheck(false);
        // join condition
        $query_cond_str = sprintf(
            "%s.`order_id` = %s.`order_id`",
            $ordercourses_table_name,
            $curr_table_name
        );
        // inner join with order courses table
        $select->joinInner($ordercourses_table_name,$query_cond_str);
        // inner join with users table
        $select->joinInner(
            $user_table_name,sprintf("%s.`user_id` = %s.`user_id`",
            $curr_table_name,
            $user_table_name),array($user_table_name.'.displayname')
        );
        // query by order direction
        if(!empty($params['order'])) {
            $order_direction = $params['order_direction'] == 'DESC'?'DESC':'ASC';
            $select->order(sprintf('%s %s',$params['order'],$order_direction));
        }
        // like user name
        if(!empty($params['search']['user_name'])) {
            $select->where(
                $user_table_name.
                '.`displayname` like "%'.
                $params['search']['user_name'].
                '%"'
            );
        }
        // like course name
        if(!empty($params['search']['course_name'])) {
            $select->where("{$ordercourses_table_name}.`title` LIKE \"%{$params['search']['course_name']}%\"");
        }
        // start date
        if(!empty($params['search']['from'])) {
            $select->where(
                sprintf('DATEDIFF("%s",%s) <= 0',
                $params['search']['from'],
                $curr_table_name.'.`date`')
            );
        }
        // end date
        if(!empty($params['search']['to'])) {
            $select->where(
                sprintf('DATEDIFF("%s",%s) >= 0',
                $params['search']['to'],
                $curr_table_name.'.`date`')
            );
        }
        // query specific course 
        if(!empty($params['course_id'])) {
            $select->where(
                "{$ordercourses_table_name}.`course_id` = ?",
                $params['course_id']
            );
        }

        return $select;
    }

    /**
     * Gets a paginator for transactions
     *
     * @param {array} query params
     * @return Zend_Paginator
     */
    public function getTransactionsPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getTransactionsSelect($params));
        return $paginator;
    } 

    public function getCourseTransactionsPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getTransactionsSelect($params));
        return $paginator;
    }
}

?>
