<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Tokens.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Model_DbTable_Tokens extends Engine_Db_Table {

    protected $_name = 'siteapi_oauth_tokens';
    protected $_rowClass = "Siteapi_Model_Token";

    public function getPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        if (empty($params['limit'])) {
            $paginator->setItemCountPerPage(10);
        }

        return $paginator;
    }

    public function getSelect($params = array()) {
        $name = $this->info('name');
        $userTableName = Engine_Api::_()->getItemtable('user')->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($name)
                ->join($userTableName, "$userTableName.user_id = $name.user_id", array('email', 'displayname'));

        $select->order(!empty($params['orderby']) ? $params['orderby'] . ' DESC' : $name . '.token_id DESC' );

        $select->where($name . ".consumer_id <> ?", 0);

        if (isset($params['displayname']) && !empty($params['displayname'])) {
            $select->where($userTableName . ".displayname LIKE ?", '%' . $params['displayname'] . '%');
        }

        if (isset($params['email']) && !empty($params['email'])) {
            $select->where($userTableName . ".email =?", $params['email']);
        }

        if (!empty($params['consumer_id'])) {
            $select->where($name . ".consumer_id =?", $params['consumer_id']);
        }

        if (!empty($params['user_id'])) {
            $select->where($name . ".user_id =?", $params['user_id']);
        }

        if (!empty($params['type'])) {
            $select->where($name . ".type LIKE ?", $params['type']);
        }

        if (!empty($params['token'])) {
            $select->where($name . ".token LIKE ?", $params['token']);
        }

        if (!empty($params['secret'])) {
            $select->where($name . ".secret LIKE ?", $params['secret']);
        }

        if (!empty($params['verifier'])) {
            $select->where($name . ".verifier LIKE ?", $params['verifier']);
        }

        if (isset($params['revoke']) && ($params['revoke'] < 2)) {
            $select->where($name . ".revoked LIKE ?", $params['revoke']);
        }

        if (!empty($params['authorized'])) {
            $select->where($name . ".authorized LIKE ?", $params['authorized']);
        }

        return $select;
    }

    /**
     * Create request token
     */
    public function createToken($consumerId, $callbackUrl = '') {
        $row = $this->createRow();
        $row->consumer_id = $consumerId;
        $row->type = 'access'; // @Todo: We will use 'request' here in future. 
        $row->token = Engine_Api::_()->getApi('oauth', 'siteapi')->generateRandomString();
        $row->secret = Engine_Api::_()->getApi('oauth', 'siteapi')->generateRandomString();
        $row->callback_url = $callbackUrl;
        $row->save();

        return $row;
    }

    public function validateToken($token) {
        $tokenTable = Engine_Api::_()->getDbTable('tokens', 'siteapi');
        $select = $tokenTable->getSelect(array('token' => $token));
        return $tokenTable->fetchRow($select);
    }

}
