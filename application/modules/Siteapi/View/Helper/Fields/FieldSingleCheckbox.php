<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldMultiCheckbox.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldSingleCheckbox extends Siteapi_View_Helper_Fields_FieldAbstract
{
	protected $_options = array(
	    0 => 'FIELD_SINGLE_CHECKBOX_UNCHECKED',
	    1 => 'FIELD_SINGLE_CHECKBOX_CHECKED'
	);

	public function fieldSingleCheckbox($subject, $field, $value, $view)
	{
	    if( is_object($value) ) {

	      	if( $value->field_id != $field->field_id ) {
	        	return '';
	      	}
	      	$value = (int) $value->value;
	      	$label = $view->translate($this->_options[$value]);
	      	return $this->encloseInLink($subject, $field, $value, $label);
	    }
  	}
}