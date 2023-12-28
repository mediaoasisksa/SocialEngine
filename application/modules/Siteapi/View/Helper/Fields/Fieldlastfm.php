<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldLastfm.php 9747 2017-11-10 02:08:08Z john $
 * @author     Donna
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Donna
 */
class Siteapi_View_Helper_Fields_Fieldlastfm extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldLastfm($subject, $field, $value, $view)
  {
   $regex = '/^((http(s|):\/\/|)(www\.|)|)last\.fm\/user\//i';
    
    $username = preg_replace($regex, '', trim($value->value));
    $lastfmUrl = 'https://www.last.fm/user/' .  $username;
    
    return $view->htmlLink($lastfmUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
    
  }
}
