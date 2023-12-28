<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldWebsite.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldWebsite extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldWebsite($subject, $field, $value, $view, $params = array())
  {
    $str = $value->value;
    if( strpos($str, 'http://') === false && strpos($str, 'https://') === false) {
      $str = 'http://' . $str;
    }

    if (!isset($params['target'])) {
      $params['target'] = '_blank';
    }

    return $view->htmlLink($str, $str, $params);
  }
}