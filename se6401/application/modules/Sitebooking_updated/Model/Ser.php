<?php

class Sitebooking_Model_Ser extends Core_Model_Item_Abstract
{

  public function getHref($params = array())
    {
      $slug = $this->slug;

      $params = array_merge(array(
        'route' => 'sitebooking_service_entry_view',
        'reset' => true,
        'pro_id' => $this->parent_id,
        'ser_id' => $this->ser_id,
        'slug' => $slug,
      ), $params);
      $route = $params['route'];
      $reset = $params['reset'];
      unset($params['route']);
      unset($params['reset']);
      return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset);
    }

  public function tags()
    {
      return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    public function getPhotoUrl($type = null)
  {
      if( $this->photo_id ) {
        return parent::getPhotoUrl($type);
      }
      return "application/modules/Sitebooking/externals/images/default_service_profile.png";
  }

  public function setPhoto($photo , $isApi = 0)
    {
      if( $photo instanceof Zend_Form_Element_File ) {
        $file = $photo->getFileName();
      } elseif( is_array($photo) && !empty($photo['tmp_name']) ) {
        $file = $photo['tmp_name'];
      } elseif( is_string($photo) && file_exists($photo) ) {
        $file = $photo;
      } else {
        throw new Sitebooking_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
      }

      if (!empty($isApi))
        $name = $photo['name'];
      else
        $name = basename($file);
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
      $params = array(
        'parent_type' => 'sitebooking',
        'parent_id' => $this->getIdentity()
      );

      // Save
      $storage = Engine_Api::_()->storage();

      // Resize image (main)
      $image = Engine_Image::factory();
      $image->open($file)
        ->resize(1024, 1024)
        ->write($path . '/m_' . $name)
        ->destroy();

      // Resize image (profile)
      $image = Engine_Image::factory();
      $image->open($file);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 480, 480)
        ->write($path . '/p_' . $name)
        ->destroy();

      // Resize image (normal)
      $image = Engine_Image::factory();
      $image->open($file);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 250, 250)
        ->write($path . '/in_' . $name)
        ->destroy();

      // Resize image (icon)
      $image = Engine_Image::factory();
      $image->open($file);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 72, 72)
        ->write($path . '/is_' . $name)
        ->destroy();

      // Store
      $iMain = $storage->create($path . '/m_' . $name, $params);
      $iProfile = $storage->create($path . '/p_' . $name, $params);
      $iIconNormal = $storage->create($path . '/in_' . $name, $params);
      $iSquare = $storage->create($path . '/is_' . $name, $params);

      $iMain->bridge($iProfile, 'thumb.profile');
      $iMain->bridge($iIconNormal, 'thumb.normal');
      $iMain->bridge($iSquare, 'thumb.icon');

      // Remove temp files
      @unlink($path . '/p_' . $name);
      @unlink($path . '/m_' . $name);
      @unlink($path . '/in_' . $name);
      @unlink($path . '/is_' . $name);

      // Update row
      $this->modified_date = date('Y-m-d H:i:s');
      $this->photo_id = $iMain->getIdentity();
      $this->save();

      return $this;
    }

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

    // public function fields() {
   //   return new Engine_ProxyObject($this, Engine_Api::_()->getApi('core', 'fields'));
    // }
}

?>