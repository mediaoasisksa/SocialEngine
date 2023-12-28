<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldTwitter.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldLinkedinc extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldLinkedinc($subject, $field, $value, $view)
  {
   $regex = '/^((http(s|):\/\/|)(www\.|)|)linkedin\.com\/company\//i';
    
    $username = preg_replace($regex, '', trim($value->value));
    $licUrl = 'https://www.linkedin.com/company/' .  $username;
    
    return $view->htmlLink($licUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
    
  }
}