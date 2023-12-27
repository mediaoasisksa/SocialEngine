<?php

class Sitecourse_Plugin_Core
{

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {

      $userTable = Engine_Api::_()->getItemTable('user');
      $user = $userTable->select()->where('level_id = ?',1)->limit(1)->query()->fetchAll();

      Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitecourse_delete_user', array(
        'owner_id' => $payload->getIdentity(),
        'user_id' => $user[0]['user_id'],
      ));
    }
  }
}
