<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Employment.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Employment_Model_Employment extends Core_Model_Item_Abstract
{
  // Properties

  protected $_parent_type = 'user';

  protected $_searchTriggers = array('title', 'body', 'search');

  protected $_parent_is_owner = true;

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $slug = $this->getSlug();

    $params = array_merge(array(
      'route' => 'employment_entry_view',
      'reset' => true,
      'user_id' => $this->owner_id,
      'employment_id' => $this->employment_id,
      'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getDescription()
  {
    // @todo decide how we want to handle multibyte string functions
    $tmpBody = strip_tags($this->body);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  public function getKeywords($separator = ' ')
  {
    $keywords = array();
    foreach( $this->tags()->getTagMaps() as $tagmap ) {
          $tag = $tagmap->getTag();
          if ($tag === null) {
              continue;
          }
          $keywords[] = $tag->getTitle();
      }

    if( null === $separator ) {
      return $keywords;
    }

    return join($separator, $keywords);
  }

  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new Employment_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = basename($file);
    }
    
    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    
    $params = array(
      'parent_type' => 'employment',
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->owner_id,
      'name' => $fileName,
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (profile)
    $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(400, 400)
      ->write($profilePath)
      ->destroy();

    // Store
    $iMain = $filesTable->createFile($mainPath, $params);
    $iProfile = $filesTable->createFile($profilePath, $params);

    $iMain->bridge($iProfile, 'thumb.profile');

    // Remove temp files
    @unlink($mainPath);
    @unlink($profilePath);
    
    // Add to album
    $viewer = Engine_Api::_()->user()->getViewer();
    $photoTable = Engine_Api::_()->getItemTable('employment_photo');
    $employmentAlbum = $this->getSingletonAlbum();
    $photoItem = $photoTable->createRow();
    $photoItem->setFromArray(array(
      'employment_id' => $this->getIdentity(),
      'album_id' => $employmentAlbum->getIdentity(),
      'user_id' => $viewer->getIdentity(),
      'file_id' => $iMain->getIdentity(),
      'collection_id' => $employmentAlbum->getIdentity(),
    ));
    $photoItem->save();

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $photoItem->file_id;
    $this->save();

    return $this;
  }

  public function addPhoto($file_id){
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id); 
    $album = $this->getSingletonAlbum();

    $params = array(
    // We can set them now since only one album is allowed
    'collection_id' => $album->getIdentity(),
    'album_id' => $album->getIdentity(),

    'employment_id' => $this->getIdentity(),
    'user_id' => $file->user_id,
        
    'file_id' => $file_id
    );
       
    $photo = Engine_Api::_()->getDbtable('photos', 'employment')->createRow();
    $photo->setFromArray($params);
    $photo->save();
    return $photo;
  }
  
  public function getPhoto($file_id)
  {
    $photoTable = Engine_Api::_()->getItemTable('employment_photo');
    $select = $photoTable->select()
      ->where('file_id = ?', $file_id)
      ->limit(1);

    $photo = $photoTable->fetchRow($select);
    return $photo;
  }
  
  public function getSingletonAlbum()
  {
    $table = Engine_Api::_()->getItemTable('employment_album');
    $select = $table->select()
      ->where('employment_id = ?', $this->getIdentity())
      ->order('album_id ASC')
      ->limit(1);

    $album = $table->fetchRow($select);

    if( null === $album )
   {
      $album = $table->createRow();
      $album->setFromArray(array(
        'title' => $this->getTitle(),
        'employment_id' => $this->getIdentity()
      ));
      $album->save();
    }

    return $album;
  }



  // Interfaces
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
  
  protected function _insert()
  {
    if( null === $this->search ) {
      $this->search = 1;
    }

    parent::_insert();
  }
  
  public function getCategoryItem()
  {
      if(!$this->category_id)
        return false;
      return Engine_Api::_()->getItem('employment_category',$this->category_id);
  }
  

  /**
   * Get a generic media type. Values:
   * employment
   *
   * @return string
   */
  public function getMediaType() {
    return 'employment listing';
  }
  
  // General
  public function getShortType($inflect = false) {
    if ($inflect)
        return 'employment';
    return 'employment';
  }
}
