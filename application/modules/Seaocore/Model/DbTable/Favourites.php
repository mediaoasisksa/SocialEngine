<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Favourites.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Model_DbTable_Favourites extends Engine_Db_Table
{
  protected $_rowClass = 'Seaocore_Model_Favourite';

  protected $_custom = false;

  public function __construct($config = array())
  {
//    if( get_class($this) !== 'Core_Model_DbTable_Favourites' ) {
//      $this->_custom = true;
//    }

    parent::__construct($config);
  }

  public function getFavouriteTable()
  {
    return $this;
  }

  public function addFavourite(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster)
  {
    $row = $this->getFavourite($resource, $poster);
    if( null !== $row )
    {
      throw new Core_Model_Exception('Already favourited');
    }

    $table = $this->getFavouriteTable();
    $row = $table->createRow();

    if( isset($row->resource_type) )
    {
      $row->resource_type = $resource->getType();
    }

    $row->resource_id = $resource->getIdentity();
    $row->poster_type = $poster->getType();
    $row->poster_id = $poster->getIdentity();
    $row->save();

    if( isset($resource->favourite_count) )
    {
      $resource->favourite_count++;
      $resource->save();
    }

    return $row;
  }

  public function removeFavourite(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster)
  {
    $row = $this->getFavourite($resource, $poster);
    if( null === $row )
    {
      throw new Core_Model_Exception('No favourite to remove');
    }

    $row->delete();

    if( isset($resource->favourite_count) )
    {
      $resource->favourite_count--;
      $resource->save();
    }

    return $this;
  }

  public function isFavourite(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster)
  {
    return ( null !== $this->getFavourite($resource, $poster) );
  }

  public function getFavourite(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster)
  {
    $table = $this->getFavouriteTable();
    $select = $this->getFavouriteSelect($resource)
      ->where('poster_type = ?', $poster->getType())
      ->where('poster_id = ?', $poster->getIdentity())
      ->limit(1);
    return $table->fetchRow($select);
  }

  public function getFavouriteSelect(Core_Model_Item_Abstract $resource)
  {
    $select = $this->getFavouriteTable()->select();

    if( !$this->_custom )
    {
      $select->where('resource_type = ?', $resource->getType());
    }

    $select
      ->where('resource_id = ?', $resource->getIdentity())
      ->order('favourite_id ASC');

    return $select;
  }

  public function getFavouritePaginator(Core_Model_Item_Abstract $resource)
  {
    $paginator = Zend_Paginator::factory($this->getFavouriteSelect($resource));
    $paginator->setItemCountPerPage(3);
    $paginator->count();
    $pages = $paginator->getPageRange();
    $paginator->setCurrentPageNumber($pages);
    return $paginator;
  }

  public function getFavouriteCount(Core_Model_Item_Abstract $resource)
  {
    if( isset($resource->favourite_count) )
    {
      return $resource->favourite_count;
    }

    $select = new Zend_Db_Select($this->getFavouriteTable()->getAdapter());
    $select
      ->from($this->getFavouriteTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

    if( !$this->_custom )
    {
      $select->where('resource_type = ?', $resource->getType());
    }

    $select->where('resource_id = ?', $resource->getIdentity());

    $data = $select->query()->fetchAll();
    return (int) $data[0]['count'];
  }

  public function getAllFavourites(Core_Model_Item_Abstract $resource)
  {
    return $this->getFavouriteTable()->fetchAll($this->getFavouriteSelect($resource));
  }

  public function getAllFavouritesUsers(Core_Model_Item_Abstract $resource)
  {
    $table = $this->getFavouriteTable();
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), array('poster_type', 'poster_id'));

    if( !$this->_custom )
    {
      $select->where('resource_type = ?', $resource->getType());
    }

    $select->where('resource_id = ?', $resource->getIdentity());

    $users = array();
    foreach( $select->query()->fetchAll() as $data )
    {
      if( $data['poster_type'] == 'user' )
      {
        $users[] = $data['poster_id'];
      }
    }
    $users = array_values(array_unique($users));

    return Engine_Api::_()->getItemMulti('user', $users);
  }
}