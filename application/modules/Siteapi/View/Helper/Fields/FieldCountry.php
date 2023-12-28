<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldCountry.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldCountry extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldCountry($subject, $field, $value)
  {
    // Set the translations for zend library.
    if (!Zend_Registry::isRegistered('Zend_Translate'))
        Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
            
    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $territories = Zend_Locale::getTranslationList('territory', $locale, 2);

    if( !isset($territories[$value->value]) ) {
      return '';
    }

    return $this->encloseInLink($subject, $field, $value->value, $territories[$value->value]);
  }
}