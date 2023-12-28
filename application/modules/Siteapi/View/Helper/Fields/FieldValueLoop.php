<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldValueLoop.php 10103 2013-10-25 14:33:33Z ivan $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldValueLoop extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldValueLoop($subject, $partialStructure, $params = array())
  {
    if( empty($partialStructure) ) {
      return '';
    }
    $view = Zend_Registry::get('Zend_View');
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() ) {
      return '';
    }
    
    // Calculate viewer-subject relationship
    $usePrivacy = ($subject instanceof User_Model_User);
    if( $usePrivacy ) {
      $relationship = 'everyone';
      if( $viewer && $viewer->getIdentity() ) {
        if( $viewer->getIdentity() == $subject->getIdentity() ) {
          $relationship = 'self';
        } else if( $viewer->membership()->isMember($subject, true) ) {
          $relationship = 'friends';
        } else {
          $relationship = 'registered';
        }
      }
    }
    
    // Generate
    $content = '';
    $lastContents = '';
    $profileFields = array();
    $lastHeadingTitle = null; //Zend_Registry::get('Zend_Translate')->_("Missing heading");
    $show_hidden = $viewer->getIdentity()
                 ? ($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type)
                 : false;
    $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
    foreach( $partialStructure as $map ) {
      $tempProfileFields = array();
      // Get field meta object
      $field = $map->getChild();
      $type = $field->type;
      $value = $field->getValue($subject);
      if(isset($params['category']) && $params['category']== 'specific' && isset($getFieldInfo['fields'][$type]['category']) && $getFieldInfo['fields'][$type]['category'] != 'specific')
        continue;
      if( !$field || $field->type == 'profile_type' || ($field->type == 'heading'
       && !empty($params['noHeading'])) ) 
        continue;
      if( !$field->display && !$show_hidden ) 
        continue;
      $isHidden = !$field->display;
      
      // Get first value object for reference
      $firstValue = $value;
      if( is_array($value) && !empty($value) ) {
        $firstValue = $value[0];
      }
      
      // Evaluate privacy
      if( $usePrivacy && !empty($firstValue->privacy) && $relationship != 'self' ) {
        if( $firstValue->privacy == 'self' && $relationship != 'self' ) {
          $isHidden = true; //continue;
        } else if( $firstValue->privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self') ) {
          $isHidden = true; //continue;
        } else if( $firstValue->privacy == 'registered' && $relationship == 'everyone' ) {
          $isHidden = true; //continue;
        }
      }
      
      // Render
      if( $field->type == 'heading' ) {
        // Heading
        if( !empty($lastContents) ) {
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
          $lastContents = '';
        }
        $lastHeadingTitle = $view->translate($field->label);
      } else {
        // Normal fields
        $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);

        if( !empty($firstValue->value) && !empty($tmp) ) {

          $notice = $isHidden && $show_hidden
                  ? sprintf('<div class="tip"><span>%s</span></div>',
                      $view->translate('This field is hidden and only visible to you and admins:'))
                  : '';
          if( !$isHidden || $show_hidden ) {
            $label = $view->translate($field->label);
            $tempProfileFields['notice'] = $notice;
            $tempProfileFields['label'] = $label;
            $tempProfileFields['type'] = $field->type;
            if(isset($tmp['link_value'])){
              $tempProfileFields['link'] = $tmp['link_value'];
            }
            if(empty($tmp['value']))
              $tempProfileFields['value'] = "";
            else
              $tempProfileFields['value'] = $tmp['value'];
            
            if (!empty($params['noHeading'])) {
               $profileFields[] = $tempProfileFields;
            } else {
               $profileFields[$lastHeadingTitle][] = $tempProfileFields;
            }
          }
        }
      }
    }

    return $profileFields;
  }

  public function getFieldValueString($field, $value, $subject, $map = null,
      $partialStructure = null)
  { $view = Zend_Registry::get('Zend_View');
    if( (!is_object($value) || !isset($value->value)) && !is_array($value) ) {
      return null;
    }
    
    // @todo This is not good practice:
    // if($field->type =='textarea'||$field->type=='about_me') $value->value = nl2br($value->value);

    $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');
    if( !$helperName ) {
      return null;
    }
    $helper = Engine_Api::_()->loadClass('Siteapi_View_Helper_Fields_' . ucfirst($helperName));    

     // $helper = $a->getHelper($helperName);
    // $helper = $this->view->getHelper($helperName);
    if( !$helper ) {
      return null;
    }
    $valuesArray = array();
    $helper->structure = $partialStructure;
    $helper->map = $map;
    $helper->field = $field;
    $helper->subject = $subject;
    try {
      $tmp = $helper->$helperName($subject, $field, $value, $view);
      $valuesArray['value'] = $tmp;
      if( $helperName == 'fieldWebsite'){
        if( strpos($value->value, 'http://') === false && strpos( $value->value, 'https://') === false) {
          $valuesArray['link_value'] = 'http://' . $value->value;
        } else
          $valuesArray['link_value'] = $value->value;
      }
    } catch (Exception $e) {
      echo " Exception ".$e;
    }
    unset($helper->structure);
    unset($helper->map);
    unset($helper->field);
    unset($helper->subject);

    return $valuesArray;
  }

  protected function _buildLastContents($content, $title)
  {
    if( !$title ) {
      return '<ul>' . $content . '</ul>';
    }
    return <<<EOF
        <div class="profile_fields">
          <h4>
            <span>{$title}</span>
          </h4>
          <ul>
            {$content}
          </ul>
        </div>
EOF;
  }
}
