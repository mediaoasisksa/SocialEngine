<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldInstagram.php 9747 2017-11-10 02:08:08Z john $
 * @author     Donna
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Donna
 */

class Siteapi_View_Helper_Fields_FieldInstagram extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldInstagram($subject, $field, $value,$view)

  {
    $regex = '/^((http(s|):\/\/|)(www\.|)|)instagram\.com\//i';
    $username = preg_replace($regex, '', trim($value->value));
    $instagramUrl = 'https://www.instagram.com/' .  $username;
    
    return $view->htmlLink($instagramUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
  }
}
