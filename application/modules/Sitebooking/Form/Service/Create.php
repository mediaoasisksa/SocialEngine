<?php

class Sitebooking_Form_Service_Create extends Engine_Form
{
  
  public function init()
  {   
    
    $categoryFiles = 'application/modules/Sitebooking/views/scripts/_category.tpl';
    $sub_sub_categoryFiles = 'application/modules/Sitebooking/views/scripts/_sub_sub_category.tpl';

    $this->setTitle('Create New Service')
      ->setDescription('Fill the form and click “Post Service” button to create your new service.')
      ->setAttrib('name', 'Service_Create_Form');


    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'id' => 'service_title',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
      'autofocus' => 'autofocus',
    ));
    // $this->addElement('Text', 'slug', array(
    //   'label' => 'URL',
    //   'allowEmpty' => false,
    //   'required' => true,
    
    //   'id' => 'service_slug',
    //   'filters' => array(
    //     new Engine_Filter_Censor(),
    //     'StripTags',
    //     new Engine_Filter_StringLength(array('max' => '63'))
    //   ),
    //   'autofocus' => 'autofocus',
    // ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'allowEmpty' => false,
      'description' => 'Description length cannot exceed 300 characters.',
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '300'))
      )
    ));
    $this->description->getDecorator("Description")->setOption("placement", "append");

   /* $this->addElement('Textarea', 'longDescription', array(
      'label' => "Long Description",
      'required' => false,
      'allowEmpty' => false,
      'editorOptions' => array(),
      'filters' => array(
        new Engine_Filter_Censor()),
    ));*/

    $status = array(
      '1'   => 'Published',
    );

    // $this->addElement('Select', 'status', array(
    //   'label' => 'Status',
    //   'allowEmpty' => false,
    //   'required' => true,
    //   'multiOptions' => $status,
    //   'description' => 'If this entry is published, it cannot be switched back to draft mode.',
    //   'filters' => array(
    //     new Engine_Filter_Censor()
    //   ),
    // ));


    $currency_type = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');
    $this->addElement('Text', 'price', array(
      'label' => "Price",
      'allowEmpty' => false,
      'required' => true,
      'description' => "Above entered price will reflect in '$currency_type' only.",
      'filters' => array(
        new Engine_Filter_Censor()
      ),
      'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
      ),
    ));
    $this->price->getDecorator("Description")->setOption("placement", "append");

    
    // tags
    // $this->addElement('Text', 'tags', array(
    //   'label'=>'Tags (Keywords)',
    //   'autocomplete' => 'off',
    //   'description' => 'Separate tags with commas.',
    //   'filters' => array(
    //     'StripTags',
    //     new Engine_Filter_Censor(),
    //   ),
    // ));
    // $this->tags->getDecorator("Description")->setOption("placement", "append");

    $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray();

    $categories["-1"] = null;
    foreach ($table as $key => $value) { 
      if($value['first_level_category_id'] == 0 && $value['second_level_category_id'] == 0)
        $categories[$value['category_id']] = $value['category_name'];
    }

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => $categories,
      'required' => true,
      'id' => 'category_id-wrapper1',
      'onchange' => "sitebooking_addOptions(this.value);  var profile_type = getProfileType($(this).value);
                                                    if(profile_type == 0) profile_type = '';
                                                    $('0_0_1').value = profile_type;
                                                    changeFields($('0_0_1'));",
    )); 

    $this->addElement('Select', 'first_level_category_id', array(
      'label' => 'Sub-Category-1',
      'RegisterInArrayValidator' => false,
      'allowEmpty' => true,
      'required' => false,
      'decorators' => array(array('ViewScript', array(
        'viewScript' => $categoryFiles,
        'class' => 'form element'))),
    ));

    $this->addElement('Select', 'second_level_category_id', array(
      'label' => 'Sub-Category-2',
       'RegisterInArrayValidator' => false,
       'allowEmpty' => true,
       'required' => false,
       'decorators' => array(array('ViewScript', array(
        'viewScript' => $sub_sub_categoryFiles,
        'class' => 'form element'))),
    )); 

    if (!$this->_item) {
      $customFields = new Sitebooking_Form_Custom_Standard(array(
          'item' => 'sitebooking_ser',
          'decorators' => array(
              'FormElements'
      )));
    } else {

      $customFields = new Sitebooking_Form_Custom_Standard(array(
          'item' => $this->getItem(),
          'decorators' => array(
              'FormElements'
      )));

    }

    $customFields->removeElement('submit');

    $customFields->getElement("0_0_1")
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true);

    $this->addSubForms(array(
        'fields' => $customFields
    ));
    
    //Time Duration Slots
    $durationTable = Engine_Api::_()->getDbTable('durations','sitebooking');
    $durationItems = $durationTable->fetchAll($durationTable->select()->where('action = ?',1));
    foreach ($durationItems as $key => $value) {
      $seconds = $value->duration;
      $duration[$seconds] = Engine_Api::_()->sitebooking()->showServiceDuration($value->duration);
    }
    $this->addElement('Select', 'duration', array(
      'label' => 'Duration in minutes / hours',
      'multiOptions' => $duration,
      'required' => true,
    ));

    $viewer = Engine_Api::_()->user()->getViewer();

    $viewOptions = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_ser', $viewer, 'auth_view');

   
    $this->addElement('Hidden', 'view', array(
       'label' => 'View Privacy',
       'value' => $viewOptions,
       'order' => 997,
     ));
   
    
    $viewOptions = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_ser', $viewer, 'auth_comment');


    $this->addElement('Hidden', 'comment', array(
       'label' => 'View Privacy',
       'value' => $viewOptions,
       'order' => 998,
     ));
    
    $this->addElement('File', 'photo', array(
      'label' => 'Choose Profile Photo',
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $this->addElement('Checkbox', 'commission', array(
      'label' => '5% commission will be deducted from your total booking amount, remaining payment will be transfer. Default payment gateway will be paypal.',
      //'description' => 'Commission',
      'required' => true,
    ));
    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Service',
      'type' => 'submit',
    ));
  }

}
