<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldYouTubeChannel.php 9747 2017-11-10 02:08:08Z john $
 * @author     Donna
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Donna
 */
class Siteapi_View_Helper_Fields_FieldYoutubechannel extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldYoutubechannel($subject, $field, $value, $view)
  {
   $regex = '/^((http(s|):\/\/|)(www\.|)|)youtube\.com\/channel\//i';
    
    $username = preg_replace($regex, '', trim($value->value));
    $ytcUrl = 'https://www.youtube.com/channel/' .  $username;
    
    return $view->htmlLink($ytcUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
    
  }
}
