<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Edit.php 9802 2012-10-20 16:56:13Z pamela $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_Form_Post_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Post');
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $allowHtml = (bool) $settings->getSetting('group_html', 0);
    $allowBbcode = (bool) $settings->getSetting('group_bbcode', 0);
    
    if( !$allowHtml ) {
      $filter = new Engine_Filter_HtmlSpecialChars();
    } else {
      $filter = new Engine_Filter_Html();
      $filter->setForbiddenTags();
      $allowedTags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'group', 'commentHtml')));
      $filter->setAllowedTags($allowedTags);
    }
    
    if( $allowHtml || $allowBbcode ) {
      $uploadUrl = "";
      if( Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') ) {
        $uploadUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller'=>'index', 'action'=>'upload-photo'), 'group_extended', true);
      }
      $editorOptions = array(
        'uploadUrl' => $uploadUrl
      );
      if( $allowHtml ) {
        $editorOptions = array_merge($editorOptions, array('html' => 1));
      } else {
        $editorOptions = array_merge($editorOptions, array('html' => 0, 'bbcode' => 1));
      }
      $this->addElement('TinyMce', 'body', array(
        'disableLoadDefaultDecorators' => true,
        'required' => true,
        'allowEmpty' => false,
        'decorators' => array(
          'ViewHelper'
        ),
        'editorOptions' => $editorOptions,
        'filters' => array(
          new Engine_Filter_Censor(),
        )
      ));        
    } else {    
      $this->addElement('textarea', 'body', array(
        'filters' => array(
          new Engine_Filter_Censor(),
        )
      ));
    }
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Post',
      'ignore' => true,
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'type' => 'link',
      'link' => true,
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
