<?php

class CustomTheme_Installer extends Engine_Package_Installer_Module
{

  public function onInstall()
  {
    //$this->_memberHomePage();
    parent::onInstall();
  }

  protected function _memberHomePage()
  {

    $db = $this->getDb();

    // home page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'user_index_home')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( $pageId ) {

      $topId = $db->select()
      ->from('engine4_core_content', 'content_id')
      ->where('page_id = ?', $pageId)
       ->where('name = ?', 'top')
      ->limit(1)
      ->query()
      ->fetchColumn();
      if(!$topId) {
          // Insert top
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $pageId,
            'order' => 1,
          ));
          $topId = $db->lastInsertId();
      }
      
      $topMiddleId = $db->select()
      ->from('engine4_core_content', 'content_id')
      ->where('page_id = ?', $pageId)
       ->where('name = ?', 'middle')
       ->where('parent_content_id = ?', $topId)
      ->limit(1)
      ->query()
      ->fetchColumn();
      if(!$topMiddleId) {
                // Insert top-middle
                  $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $pageId,
                    'parent_content_id' => $topId,
                  ));
                  $topMiddleId = $db->lastInsertId();
      }

 $hpbblock = $db->select()
      ->from('engine4_core_content', 'content_id')
      ->where('page_id = ?', $pageId)
       ->where('name = ?', 'hpbblock.banner')
       ->where('parent_content_id = ?', $topMiddleId)
      ->limit(1)
      ->query()
      ->fetchColumn();

      if(!$hpbblock) {
           $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'hpbblock.banner',
          'page_id' => $pageId,
          'parent_content_id' => $topMiddleId,
          'params' => '{"title":"","name":"hpbblock.banner"}',
          'order' => 1,
        ));
      }
      
    }
     
    return $this;
  }
}
?>
