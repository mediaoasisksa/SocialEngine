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
class Siteapi_View_Helper_Fields_FieldTwitter extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldTwitter($subject, $field, $value, $view)
  {
    $regex = '/^((http(s|):\/\/|)(www\.|)|)twitter\.com\/(#!|)/i';
    $username = preg_replace($regex, '', trim($value->value));
    $twitterUrl = 'https://www.twitter.com/#!/' .  $username;
    
    return $view->htmlLink($twitterUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
  }
}