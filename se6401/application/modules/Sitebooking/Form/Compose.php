<?php
/**
 * 
 */
class Sitebooking_Form_Compose extends Engine_Form
{

  protected $_name;

  public function setname($flage) {
  $this->_name = $flage;
  return $this;
  }
  
  public function init() {


    $this->setTitle('Compose Message');
        $this->setDescription('Create your message with the form below.')
                ->setAttrib('id', 'messages_compose');

            // init to
        $this->addElement('Text', 'to', array(
            'label'=>'Send To',
            'value' => $this->_name,
            'readonly' => true,
            'autocomplete'=>'off'));

        Engine_Form::addDefaultDecorators($this->to);


          // Init to Values
          $this->addElement('Hidden', 'toValues', array(
              'label' => 'Send To',
              // 'required' => true,
              // 'allowEmpty' => false,
              'order' => 200,
              'validators' => array(
                  'NotEmpty'
              ),
              'filters' => array(
                  'HtmlEntities'
              ),
          ));

          // init title
          $this->addElement('Text', 'title', array(
              'label' => 'Subject',
              'order' => 6,
        'required' => true,
              'filters' => array(
                  'StripTags',
                  new Engine_Filter_Censor(),
                  new Engine_Filter_HtmlSpecialChars(),
              ),
          ));

       
            // init body - plain text
            $this->addElement('Textarea', 'body', array(
                'label' => 'Message',
                'order' => 7,
                'required' => true,
                'allowEmpty' => false,
                'filters' => array(
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_Censor(),
                    new Engine_Filter_EnableLinks(),
                ),
            ));
        
          // init submit
          $this->addElement('Button', 'submit', array(
              'label' => 'Send Message',
              'order' => 8,
              'type' => 'submit',
              'ignore' => true
          ));

  }
}

?>