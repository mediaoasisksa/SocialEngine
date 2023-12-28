<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldSpotify.php 9747 2017-11-10 02:08:08Z john $
 * @author     Donna
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Donna
 */
class Siteapi_View_Helper_Fields_FieldSpotify extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldSpotify($subject, $field, $value, $view)
  {
   $regex = '/^((http(s|):\/\/|)(www\.|)|)open\.spotify\.com\/user\//i';
    
    $username = preg_replace($regex, '', trim($value->value));
    $spotUrl = 'https://open.spotify.com/user/' .  $username;
    
    return $view->htmlLink($spotUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
    
  }
}