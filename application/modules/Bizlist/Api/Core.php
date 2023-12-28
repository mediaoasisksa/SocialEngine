<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developmentsedsafd
 * @license    http://www.socialengine.com/license/
 */
 class Bizlist_Api_Core extends Core_Api_Abstract
 {

   const IMAGE_WIDTH = 720;
   const IMAGE_HEIGHT = 720;

   const THUMB_WIDTH = 140;
   const THUMB_HEIGHT = 160;

   public function createPhoto($params, $file)
   {
     if( $file instanceof Storage_Model_File )
     {
       $params['file_id'] = $file->getIdentity();
     }

     else
     {
       // Get image info and resize
       $name = basename($file['tmp_name']);
       $path = dirname($file['tmp_name']);
       $extension = ltrim(strrchr($file['name'], '.'), '.');

       $mainName = $path.'/m_'.$name . '.' . $extension;
       $thumbName = $path.'/t_'.$name . '.' . $extension;

       $image = Engine_Image::factory();
       $image->open($file['tmp_name'])
           ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
           ->write($mainName)
           ->destroy();

       $image = Engine_Image::factory();
       $image->open($file['tmp_name'])
           ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
           ->write($thumbName)
           ->destroy();

       // Store photos
       $photo_params = array(
         'parent_id' => $params['bizlist_id'],
         'parent_type' => 'bizlist',
       );

       $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
       $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
       $photoFile->bridge($thumbFile, 'thumb.normal');

       $params['file_id'] = $photoFile->file_id; // This might be wrong
       $params['photo_id'] = $photoFile->file_id;

       // Remove temp files
       @unlink($mainName);
       @unlink($thumbName);

       /*
       $param['owner_type'] = $params['parent_type'];
       $param['owner_id'] = $params['parent_id'];
       unset($params['parent_type']);
       unset($params['parent_id']);
       */
     }

     $row = Engine_Api::_()->getDbtable('photos', 'bizlist')->createRow();
     $row->setFromArray($params);
     $row->save();
     return $row;
   }
 }
