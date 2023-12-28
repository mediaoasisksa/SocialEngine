<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Invite.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Plugin_Task_Invite extends Core_Plugin_Task_Abstract {

    protected $_max;
    protected $_break;
    protected $_offset;

    public function execute() {

        $this->_max = 40;
        $this->_break = false;
        $this->_offset = 0;

        while ($this->_offset <= $this->_max && !$this->_break) {
            $this->_processOne();
            $this->_offset++;
        }

        if ($this->_break) {
            $this->_setWasIdle();
        }
    }

    protected function _processOne() {

        $inviteTable = Engine_Api::_()->getDbtable('invites', 'seaocore');

        $inviteSelect = $inviteTable->select()->limit(1);

        $inviteRow = $inviteTable->fetchRow($inviteSelect);

        if (null === $inviteRow) {
            $this->_break = true;
            return;
        }

        $resource = Engine_Api::_()->getItem($inviteRow->resource_type, $inviteRow->resource_id);
        if (!$resource instanceof Core_Model_Item_Abstract) {
            $inviteRow->delete();
            return;
        }

        $moduleName = explode('_', $inviteRow->resource_type);
        if (!Engine_Api::_()->hasModuleBootstrap($moduleName[0])) {
            $inviteRow->delete();
            return;
        }

        switch ($inviteRow->resource_type) {
            case 'siteevent_event':
                $recipient = Engine_Api::_()->getItem('user', $inviteRow->recipient_id);
                
                if (($recipient instanceof User_Model_User) && !empty($recipient->email)) {
                    $friendsToJoin[] = $recipient->email . '#' . $recipient->displayname;
                    Engine_Api::_()->getApi('Invite', 'Seaocore')->sendPageInvites($friendsToJoin, $inviteRow->resource_id, $moduleName[0], '', $inviteRow->resource_type, '', $inviteRow->inviter_id, $inviteRow->occurrence_id);
                }

                break;
        }

        $inviteRow->delete();

        return;
    }

}
