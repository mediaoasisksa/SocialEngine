<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Nonce.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Model_DbTable_Nonce extends Engine_Db_Table {

    protected $_name = 'siteapi_oauth_nonce';

    public function getSelect($params = array()) {
        $name = $this->info('name');

        $select = $this->select();

        if (!empty($params['nonce'])) {
            $select->where($name . ".nonce =?", $params['nonce']);
        }

        return $select;
    }

    public function insertRow($params) {
        $row = $this->createRow();

        if (isset($params['nonce']) && !empty($params['nonce']))
            $row->nonce = $params['nonce'];

        if (isset($params['timestamp']) && !empty($params['timestamp']))
            $row->timestamp = $params['timestamp'];

        $row->save();

        return $row;
    }

}
