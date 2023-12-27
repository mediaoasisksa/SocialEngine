<?php 


class Sitecourse_Model_DbTable_Gateways extends Engine_Db_Table {

    protected $_name = 'sitecourse_gateways';
    protected $_rowClass = 'Sitecourse_Model_Gateway';
    protected $_serializedColumns = array('config');
    protected $_cryptedColumns = array('config');
    static private $_cryptKey;

    /**
     * Return PayPal gateway id, if exist
     *
     * @param int $course_id
     * @return int
     */
    public function isPayPalGatewayEnable($course_id) {
        $select = $this->select()
        ->from($this->info('name'), 'gateway_id')
        ->where("plugin = 'Payment_Plugin_Gateway_PayPal'")
        ->where("enabled = 1")
        ->where("course_id = ?", $course_id);

        return $select->query()->fetchColumn();
    }

    public function getPayPalGatewayId($course_id, $plugin = 'Payment_Plugin_Gateway_PayPal') {
        return $this->select()
        ->from($this->info('name'), 'gateway_id')
        ->where('enabled = ?', 1)
        ->where('course_id = ?', $course_id)
        ->where("plugin = ?", $plugin)
        ->query()
        ->fetchColumn()
        ;
    }  


    public function getGatewayDetails($course_id) {
        $select = $this->select()
        ->from($this)
        ->where("plugin = 'Payment_Plugin_Gateway_PayPal'")
        ->where("enabled = 1")
        ->where("course_id = ?", $course_id);

        return $select->query()->fetch();
    }


}

?>

