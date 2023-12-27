<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Photos.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_Model_DbTable_Photos extends Core_Model_Item_DbTable_Abstract
{
  protected $_rowClass = 'Album_Model_Photo';
  protected $_temporyPath = DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR . 'temporary'. DIRECTORY_SEPARATOR .'album_photos';
  public function getPhotoSelect(array $params)
  {
    $tablePhotoName = $this->info('name');
    $tableAlbum = Engine_Api::_()->getItemTable('album');
    $tableAlbumName = $tableAlbum->info('name');
    $select = $this->select()
      ->from($this->info('name'));
    if( !empty($params['album']) && $params['album'] instanceof Album_Model_Album ) {
      $select->where('album_id = ?', $params['album']->getIdentity());
    } else if( !empty($params['album_id']) && is_numeric($params['album_id']) ) {
      $select->where('album_id = ?', $params['album_id']);
    } else if (!empty($params['album_ids']) && is_array($params['album_ids'])) {
      $select->where('album_id IN (?)', $params['album_ids']);
    }
    if(empty($params['showprivatephoto'])) {
      $select->where($tableAlbum->select()
        ->from($tableAlbumName,new Zend_Db_Expr('COUNT(*) > 0'))->where($tableAlbumName.".album_id = ".$tablePhotoName.".album_id")->where("type NOT IN ('group','event') OR type IS NULL"));
    }

    if(isset($params['albumvieworder'])) {
      if($params['albumvieworder'] == 'newest')
        $select->order('photo_id DESC');
      else if($params['albumvieworder'] == 'oldest')
        $select->order('photo_id ASC');
      else
        $select->order('order ASC');
    } else {
      if( !isset($params['order']) ) {
        $select->order('order DESC');
      } else if( is_string($params['order']) ) {
        $select->order($params['order'] . ' DESC');
      }
    }

    if (!empty($params['search'])) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $params['search'] . '%');
    }

    if(!empty($params['tag'])) {
      $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
      $tmName = $tmTable->info('name');
      $rName = $this->info('name');
      $select
        ->joinLeft($tmName, "$tmName.resource_id = $rName.photo_id", NULL)
        ->where($tmName.'.resource_type = ?', 'album_photo')
        ->where($tmName.'.tag_id = ?', $params['tag']);
    }

    return $select;
  }
  
  public function getPhotoPaginator(array $params)
  {
    return Zend_Paginator::factory($this->getPhotoSelect($params));
  }
  public function uploadTemPhoto($photo){
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
        throw new User_Model_Exception('invalid argument passed to setPhoto');
    }
    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    if (!is_dir(APPLICATION_PATH.$this->_temporyPath)) {
      mkdir(APPLICATION_PATH.$this->_temporyPath, 0777, true);
    }
    $uploadFileName = md5(time().rand(1,19234876)).'.'.$extension;
    $uploadFilePath = $this->_temporyPath.DIRECTORY_SEPARATOR.$uploadFileName;
    if(copy($file,APPLICATION_PATH.$uploadFilePath)){
      return base64_encode($uploadFileName);
    }
    return false;       
  }
  public function getTemPhoto($photoId,$fullPath = 0){
    $filePath = $this->_temporyPath.DIRECTORY_SEPARATOR.base64_decode($photoId);
    if(file_exists(APPLICATION_PATH.$filePath)){
      if($fullPath){
        return APPLICATION_PATH.$filePath;
      }
      return str_replace('\\','/',$filePath);
    }
    return false;
  }
}
