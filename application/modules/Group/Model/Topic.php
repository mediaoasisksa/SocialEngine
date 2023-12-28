<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_Model_Topic extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'group';

  protected $_owner_type = 'user';

  protected $_children_types = array('group_post');
  
  public function isSearchable()
  {
    $group = $this->getParentGroup();
    if( !($group instanceof Core_Model_Item_Abstract) ) {
      return false;
    }
    return $group->isSearchable();
  }

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'group_topic',
      'controller' => 'topic',
      'action' => 'view',
      'group_id' => $this->group_id,
      'topic_id' => $this->getIdentity(),
      'slug' => $this->getSlug(),
    ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getDescription()
  {
    $firstPost = $this->getFirstPost();
    $content = '';
    if (null !== $firstPost) {        
        $content = $firstPost->body;
        // strip HTML and BBcode
        $content = strip_tags($content);
        $content = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $content);
        $content = ( Engine_String::strlen($content) > 255 ? Engine_String::substr($content, 0, 255) . '...' : $content );
    }
    return $content;
  }
  
  public function getParentGroup()
  {
    return Engine_Api::_()->getItem('group', $this->group_id);
  }

  public function getFirstPost()
  {
    $table = Engine_Api::_()->getDbtable('posts', 'group');
    $select = $table->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('post_id ASC')
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getLastPost()
  {
    $table = Engine_Api::_()->getItemTable('group_post');
    $select = $table->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('post_id DESC')
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getLastPoster()
  {
    return Engine_Api::_()->getItem('user', $this->lastposter_id);
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('group');
  }



  // Internal hooks

  protected function _insert()
  {
    if( $this->_disableHooks ) return;
    
    if( !$this->group_id )
    {
      throw new Exception('Cannot create topic without group_id');
    }

    /*
    $this->getParentGroup()->setFromArray(array(

    ))->save();
    */

    parent::_insert();
  }

  protected function _delete()
  {
    if( $this->_disableHooks ) return;
    
    // Delete all child posts
    $postTable = Engine_Api::_()->getItemTable('group_post');
    $postSelect = $postTable->select()->where('topic_id = ?', $this->getIdentity());
    foreach( $postTable->fetchAll($postSelect) as $groupPost ) {
      $groupPost->disableHooks()->delete();
    }
    
    // delete group_topic_creat and group_topic_reply
    
    parent::_delete();
  }

  public function canEdit($user)
  {
    return $this->authorization()->isAllowed($user, 'topic_edit'); //$this->getParent()->authorization()->isAllowed($user, 'edit') || $this->getParent()->authorization()->isAllowed($user, 'topic.edit') || $this->isOwner($user);
  }
  
  public function canDelete($user)
  {
    return $this->authorization()->isAllowed($user, 'topic_delete'); //$this->getParent()->authorization()->isAllowed($user, 'delete') || $this->getParent()->authorization()->isAllowed($user, 'topic.delete') || $this->isOwner($user);
  }

  public function canPostCreate($user)
  {
    return $this->authorization()->isAllowed($user, 'post_create');
  }

  public function canPostEdit($user)
  {
    return $this->authorization()->isAllowed($user, 'post_edit');
  }

  public function canPostDelete($user)
  {
    return $this->authorization()->isAllowed($user, 'post_delete');
  }

}
