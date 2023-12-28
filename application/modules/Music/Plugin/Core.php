<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('playlists', 'music');
    $select  = new Zend_Db_Table_Select($table);
    $select->from($table->info('name'), array('COUNT(*) AS count'));
    $event->addResponse($select->query()->fetchColumn(0), 'playlist');
    
    $table   = Engine_Api::_()->getDbTable('playlistSongs', 'music');
    $select  = new Zend_Db_Table_Select($table);
    $select->from($table->info('name'), array('COUNT(*) AS count'));
    $event->addResponse($select->query()->fetchColumn(0), 'song');
  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete polls
      $playlistTable = Engine_Api::_()->getDbtable('playlists', 'music');
      $playlistSelect = $playlistTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach( $playlistTable->fetchAll($playlistSelect) as $playlist ) {
        foreach ($playlist->getSongs() as $song)
          $song->deleteUnused();
        $playlist->delete();
      }
    }
  }
}
