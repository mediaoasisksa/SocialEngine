<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldOptions.php 9784 2012-09-25 02:54:59Z pamela $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldOptions extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldOptions($subject, $field, $value, $view)
  {
    $info = Engine_Api::_()->fields()->getFieldInfo($field->type, 'multiOptions');

    // Single
    if( is_object($value) ) {
      if( isset($info[$value->value]) ) {
        $label =  $view->translate($info[$value->value]);
        return $this->encloseInLink($subject, $field, $value->value, $label);
      }
    }

    // Multi
    else if( is_array($value) ) {
      $first = true;
      $content = '';
      foreach( $value as $sVal ) {
        if( !isset($info[$sVal->value]) ) 
          continue;
        if( !$first ) $content .= ', ';
        $first = false;

        $label = $view->translate($info[$sVal->value]);
        $content .= $this->encloseInLink($subject, $field, $sVal->value, $label);
      }
      return $content;
    }

    return '';
  }
}