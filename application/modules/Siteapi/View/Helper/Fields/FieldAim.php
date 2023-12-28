<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldAim.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldAim extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldAim($subject, $field, $value, $view)
  {
    return $view->htmlLink('aim:goim?screenname=' . $value->value, $value->value, array(
      //'target' => '_blank',
      'ref' => 'nofollow',
    ));
  }
}