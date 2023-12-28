<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldCurrency.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldCurrency extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldCurrency($subject, $field, $value, $view)
  {
    //TODO, We need to convert the currency using locale. 
    // $label = $view->locale()->toCurrency($value->value, $field->config['unit']);
    $label = $value->value;

    return $this->encloseInLink($subject, $field, $value->value, $label, true);
  }
}