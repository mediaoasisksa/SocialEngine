<?php
class Sitebooking_Form_ServiceProvider_TellAFriend extends Engine_Form {

  public $_error = array();

<<<<<<< HEAD
    public function init() {
        $this->setTitle('Tell a friend')
                ->setDescription('Please fill the form given below and send it to your friends. Through this you can let them know about this Sevice Provider.')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'service_provider_create');
        $this->setAttrib('class', 'global_form seaocore_form_comment');
        $viewr_name = "";
        $viewr_email = "";
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() > 0) {
            $viewr_name = $viewer->getTitle();
            $viewr_email = $viewer->email;
        }
        // TITLE
        $this->addElement('Text', 'provider_sender_name', array(
            'label' => 'Your Name',
            'allowEmpty' => false,
            'required' => true,
            'value' => $viewr_name,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));

        //SENDER EMAIL
        $this->addElement('Text', 'provider_sender_email', array(
            'label' => 'Your Email',
            'allowEmpty' => false,
            'required' => true,
            'value' => $viewr_email,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));

        // RECIVER EMAILS
        $this->addElement('Text', 'provider_reciver_emails', array(
            'label' => 'To',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Separate multiple addresses with commas.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        $text_value = Zend_Registry::get('Zend_Translate')->_('Thought you would be interested in this.');
        $this->provider_reciver_emails->getDecorator("Description")->setOption("placement", "append");
        // MESSAGE
        $this->addElement('textarea', 'provider_message', array(
            'label' => 'Message',
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'height:120px;'),
            'value' => $text_value,
            'description' => 'You can send a personal note in the mail.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));
        $this->provider_message->getDecorator("Description")->setOption("placement", "append");
        // SEND COPY TO ME
        $this->addElement('Checkbox', 'provider_send_me', array(
            'label' => "Send a copy to my email address.",
        ));
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('provider.captcha.post', 1) && empty($viewer_id)) {
            if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
                Zend_Registry::get('Zend_View')->recaptcha($this);
            } else {
                $this->addElement('captcha', 'captcha', array(
                    'description' => 'Please type the characters you see in the image.',
                    'captcha' => 'image',
                    'required' => true,
                    'captchaOptions' => array(
                        'wordLen' => 6,
                        'fontSize' => '30',
                        'timeout' => 300,
                        'imgDir' => APPLICATION_PATH . '/public/temporary/',
                        'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
                        'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
                )));
                $this->captcha->getDecorator("Description")->setOption("placement", "append");
            }
        }
=======
  public function init() {
    $this->setTitle('Tell a friend')
        ->setDescription('Please fill the form given below and send it to your friends. Through this you can let them know about this Sevice Provider.')
        ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        ->setAttrib('name', 'service_provider_create');
    $this->setAttrib('class', 'global_form_popup seaocore_form_comment');
    $viewr_name = "";
    $viewr_email = "";
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() > 0) {
      $viewr_name = $viewer->getTitle();
      $viewr_email = $viewer->email;
    }
    // TITLE
    $this->addElement('Text', 'provider_sender_name', array(
      'label' => 'Your Name',
      'allowEmpty' => false,
      'required' => true,
      'value' => $viewr_name,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
    )));

    //SENDER EMAIL
    $this->addElement('Text', 'provider_sender_email', array(
      'label' => 'Your Email',
      'allowEmpty' => false,
      'required' => true,
      'value' => $viewr_email,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
    )));
>>>>>>> 69f11430b2f400c5c772b5d1a737621034f44dab

    // RECIVER EMAILS
    $this->addElement('Text', 'provider_reciver_emails', array(
      'label' => 'To',
      'allowEmpty' => false,
      'required' => true,
      'description' => 'Separate multiple addresses with commas.',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    $text_value = Zend_Registry::get('Zend_Translate')->_('Thought you would be interested in this.');
    $this->provider_reciver_emails->getDecorator("Description")->setOption("placement", "append");
    // MESSAGE
    $this->addElement('textarea', 'provider_message', array(
      'label' => 'Message',
      'required' => true,
      'allowEmpty' => false,
      'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'width:230px; max-width:400px;height:120px;'),
      'value' => $text_value,
      'description' => 'You can send a personal note in the mail.',
      'filters' => array(
        'StripTags',
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));
    $this->provider_message->getDecorator("Description")->setOption("placement", "append");
    // SEND COPY TO ME
    $this->addElement('Checkbox', 'provider_send_me', array(
      'label' => "Send a copy to my email address.",
    ));
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('provider.captcha.post', 1) && empty($viewer_id)) {
      if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
        Zend_Registry::get('Zend_View')->recaptcha($this);
      } else {
        $this->addElement('captcha', 'captcha', array(
          'description' => 'Please type the characters you see in the image.',
          'captcha' => 'image',
          'required' => true,
          'captchaOptions' => array(
            'wordLen' => 6,
            'fontSize' => '30',
            'timeout' => 300,
            'imgDir' => APPLICATION_PATH . '/public/temporary/',
            'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
            'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
        )));
        $this->captcha->getDecorator("Description")->setOption("placement", "append");
      }
    }

    // Element: SEND
    $this->addElement('Button', 'provider_send', array(
      'label' => 'Tell a friend',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    // Element: cancel
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $this->addElement('Cancel', 'provider_cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
          'ViewHelper',
        ),
      ));
    } else {
      $this->addElement('Cancel', 'provider_cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'history.go(-1); return false;',
        'decorators' => array(
          'ViewHelper',
        ),
      ));
    }

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'provider_send',
      'provider_cancel',
        ), 'provider_buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }

}

?>