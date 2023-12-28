<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldLocation.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldLocation extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldLocation($subject, $field, $value, $view)
  {
    return $value->value
      // . ' ['
      // . $view->htmlLink('http://maps.google.com/?q=' . urlencode($value->value), $view->translate('map'), array('target' => '_blank'))
      // . ']'
    ;
  }
}