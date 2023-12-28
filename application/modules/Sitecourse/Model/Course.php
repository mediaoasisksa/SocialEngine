<?php  
class Sitecourse_Model_Course extends Core_Model_Item_Abstract
{ 
    protected $_parent_type = 'user';

    // protected $_parent_is_owner = true;
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

    public function setPhoto($photo, $parent_type='sitecourse')
    {
      if( $photo instanceof Zend_Form_Element_File ) {
        $file = $photo->getFileName();
      } elseif( is_array($photo) && !empty($photo['tmp_name']) ) {
        $file = $photo['tmp_name'];
      } elseif( is_string($photo) && file_exists($photo) ) {
        $file = $photo;
      } else {
        throw new Sitecourse_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
      }

      $name = basename($file);
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
      $params = array(
        'parent_type' => $parent_type,
        'parent_id' => $this->getIdentity()
      );

      // Save
      $storage = Engine_Api::_()->storage();

      // Resize image (main)
      $image = Engine_Image::factory();
      $image->open($file)
      ->resize(720, 720)
      ->write($path . '/m_' . $name)
      ->destroy();

      // Resize image (profile)
      $image = Engine_Image::factory();
      $image->open($file)
      ->resize(200, 400)
      ->write($path . '/p_' . $name)
      ->destroy();

      // Resize image (normal)
      $image = Engine_Image::factory();
      $image->open($file)
      ->resize(140, 160)
      ->write($path . '/in_' . $name)
      ->destroy();

      // Resize image (icon)
      $image = Engine_Image::factory();
      $image->open($file);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 48, 48)
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
      if($parent_type == 'sitecourse_signature'){
        $this->signaturePhoto_id = $iMain->getIdentity();
      }else{
        $this->photo_id = $iMain->getIdentity();  
      }
      $this->save();

      return $this;
    }

    public function getHref($params = array())
    {
      $params = array_merge(array(
        'route' => 'sitecourse_entry_profile',
        'reset' => true,
        'action' => 'profile',
        'user_id' => $this->owner_id,
        'url' => $this->url,
      ), $params);
      $route = $params['route'];
      $reset = $params['reset'];
      unset($params['route']);
      unset($params['reset']);
      return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
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

  }
  ?>
