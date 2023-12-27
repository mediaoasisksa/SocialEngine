<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Location.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Share extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Share')
      ->setDescription('Share this by re-posting it with your own message.')
      ->setMethod('POST')
      ->setAttrib('id','seaocore_share_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $shareTypeList = array('timeline'=>'Share on your Timeline','event'=>'Share on any Event\'s Profile','group'=>'Share on any Group\'s Profile','video'=>'Share on any Video\'s Profile','sitepage'=>'Share on any Page\'s Profile' ,'album'=>'Share on any Album\'s Profile');
    $shareTypeSelectedOptions = array_intersect_key($shareTypeList, array_flip($coreSettingsApi->getSetting('advancedactivity_share_options',$shareTypeList)));
    
    $this->addElement('Select', 'type', array(
        'label' => '',
        'multiOptions' => $shareTypeSelectedOptions,
        'onchange' => "changeType(this)",
        'value' => 'timeline',
    ));

     $this->addElement('Text', 'title', array(
        'label' => 'Title',
        'autocomplete' => 'off',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
    ));
     $this->addElement('hidden', 'item_id', array(
        'label' => 'Itme',
    ));
     
    $this->type->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));
    
    $this->addElement('Textarea', 'body', array(
      //'required' => true,
      //'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));

    // Buttons
    $buttons = array();

    $translate = Zend_Registry::get('Zend_Translate');

    // Facebook
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    if( 'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable &&
        $facebookTable->getApi() &&
        $facebookTable->isConnected() ) {
      $this->addElement('Dummy', 'post_to_facebook', array(
        'content' => '
          <span href="javascript:void(0);" class="composer_facebook_toggle" onclick="toggleFacebookShareCheckbox();">
            <span class="composer_facebook_tooltip">
              ' . $translate->translate('Publish this on Facebook') . '
            </span>
            <input type="checkbox" name="post_to_facebook" value="1" style="display:none;">
          </span>',
      ));
      $this->getElement('post_to_facebook')->clearDecorators();
      $buttons[] = 'post_to_facebook';
    }

    // Twitter
    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    if( 'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable &&
        $twitterTable->getApi() &&
        $twitterTable->isConnected() ) {
      $this->addElement('Dummy', 'post_to_twitter', array(
        'content' => '
          <span href="javascript:void(0);" class="composer_twitter_toggle" onclick="toggleTwitterShareCheckbox();">
            <span class="composer_twitter_tooltip">
              ' . $translate->translate('Publish this on Twitter') . '
            </span>
            <input type="checkbox" name="post_to_twitter" value="1" style="display:none;">
          </span>',
      ));
      $this->getElement('post_to_twitter')->clearDecorators();
      $buttons[] = 'post_to_twitter';
    }


    $this->addElement('Button', 'submit', array(
      'label' => 'Share',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    $buttons[] = 'submit';

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $buttons[] = 'cancel';
    $this->addDisplayGroup($buttons, 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
}