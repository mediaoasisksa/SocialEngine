<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldText.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldInterests extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldInterests($subject, $field, $value,$view)
  {
    return $this->encloseInLink($subject, $field, htmlspecialchars_decode($value->value), htmlspecialchars_decode($value->value));
  }
}