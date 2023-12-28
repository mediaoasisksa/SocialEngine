<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Create.php 9802 2012-10-20 16:56:13Z pamela $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Form_Create extends Engine_Form
{   
    protected $_customAjaxLoad = false;
    public function setCustomAjaxLoad($flag = false)
    {
        $this->_customAjaxLoad = (bool) $flag;
        return $this;
    }
    public function getCustomAjaxLoad()
    {
        return $this->_customAjaxLoad;
    }
    public function init()
    {
        $this->setTitle('Post New Listing')
            ->setDescription('Compose your new classified listing below, then click "Post Listing" to publish the listing.')
            ->setAttrib('name', 'classifieds_create');

        $this->addElement('Text', 'title', array(
            'label' => 'Listing Title',
            'allowEmpty' => false,
            'required' => true,
            'maxlength' => '63',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            ),
        ));

        $user = Engine_Api::_()->user()->getViewer();
        $userLevel = Engine_Api::_()->user()->getViewer()->level_id;

        // init to
        $this->addElement('Text', 'tags', array(
            'label' => 'Tags (Keywords)',
            'autocomplete' => 'off',
            'description' => 'Separate tags with commas.',
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
            ),
        ));
        $this->tags->getDecorator("Description")->setOption("placement", "append");
        
        // prepare categories
        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
        if (engine_count($categories) > 0) {
          $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $categories,
            'onchange' => "showSubCategory(this.value);",
          ));
          $this->addElement('Select', 'subcat_id', array(
            'label' => "2nd-level Category",
            'allowEmpty' => true,
            'required' => false,
            'multiOptions' => array('0' => ''),
            'registerInArrayValidator' => false,
            'onchange' => "showSubSubCategory(this.value);"
          ));
          $this->addElement('Select', 'subsubcat_id', array(
            'label' => "3rd-level Category",
            'allowEmpty' => true,
            'registerInArrayValidator' => false,
            'required' => false,
            'multiOptions' => array('0' => '')
          ));
        }

        // Element: description
        $allowedHtml = Engine_Api::_()->authorization()->getPermission($userLevel, 'classified', 'auth_html');
        $uploadUrl = "";
        $viewer = Engine_Api::_()->user()->getViewer();
        if( $allowedHtml && Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') ) {
            $uploadUrl = Zend_Controller_Front::getInstance()->getRouter()
                ->assemble(array('controller' => 'index', 'action' => 'upload-photo'), 'classified_general', true);
        }

        $this->addElement('TinyMce', 'body', array(
            'label' => 'Description',
            'disableLoadDefaultDecorators' => true,
            'required' => true,
            'allowEmpty' => false,
            'decorators' => array(
                'ViewHelper'
            ),
            'editorOptions' => array(
                'uploadUrl' => $uploadUrl,
                'html' => (bool) $allowedHtml,
            ),
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags'=> $allowedHtml))),
        ));

        // Element: upload photo
        $allowedUpload = Engine_Api::_()->authorization()->getPermission($userLevel, 'classified', 'photo');
        if( $allowedUpload ) {
            $this->addElement('File', 'photo', array(
                'label' => 'Main Photo',
                'accept' => 'image/*',
            ));
            $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        }

        // Add subforms
        if( !$this->_item ) {
            $customFields = new Classified_Form_Custom_Fields([
                'enableAjaxLoad' => $this->getCustomAjaxLoad(),
                'ajaxUrl'=>Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'fields'))
            ]);
        } else {
            $customFields = new Classified_Form_Custom_Fields([
                'item' => $this->getItem(),
                'enableAjaxLoad' => $this->getCustomAjaxLoad(),
                'ajaxUrl'=>Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'fields'))
            ]);
        }
        if( get_class($this) == 'Classified_Form_Create' ) {
            $customFields->setIsCreation(true);
        }

        $this->addSubForms(array(
            'fields' => $customFields
        ));

        if (Engine_Api::_()->authorization()->isAllowed('classified', $user, 'allow_network')) {
            $networkOptions = array();
            foreach (Engine_Api::_()->getDbTable('networks', 'network')->fetchAll() as $network) {
                $networkOptions[$network->network_id] = $network->getTitle();
            }
            //Networks
            $this->addElement('Multiselect', 'networks', array(
                'label' => 'Networks',
                'description' => 'Choose the Networks to which this Classified will be displayed.',
                'multiOptions' => $networkOptions,
            ));
            $this->networks->getDecorator('Description')->setOption('placement', 'append');
        }

        // Privacy
        $availableLabels = array(
            'everyone'            => 'Everyone',
            'registered'          => 'All Registered Members',
            'owner_network'       => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member'        => 'Friends Only',
            'owner'               => 'Just Me',
        );

        // View
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('classified', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

        if( !empty($viewOptions) && engine_count($viewOptions) >= 1 ) {
            // Make a hidden field
            if( engine_count($viewOptions) == 1 ) {
                $this->addElement('hidden', 'auth_view', array('order' => 101, 'value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who may see this classified listing?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('classified', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if( !empty($commentOptions) && engine_count($commentOptions) >= 1 ) {
            // Make a hidden field
            if( engine_count($commentOptions) == 1 ) {
                $this->addElement('hidden', 'auth_comment', array('order' => 102, 'value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this classified listing?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }

//    $this->addElement('Hash', 'token', array(
//      'salt' => 'classifieds-' . crc32(__FILE__)
//    ));

//         $spamSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam;
//         $recaptchaVersionSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam_recaptcha_version;
//         if($recaptchaVersionSettings == 0  && $spamSettings['recaptchaprivatev3'] && $spamSettings['recaptchapublicv3']) {
//           $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions());
//         }

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Post Listing',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'classified_general', true),
            'onclick' => '',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array(
            'execute',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
