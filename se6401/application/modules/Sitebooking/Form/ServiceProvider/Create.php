<?php

class Sitebooking_Form_ServiceProvider_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
    $this->setTitle('Become a Service Provider')
      ->setDescription('Fill all the entries, then submit the form to add a service provider.')
      ->setAttrib('name', 'become_service_provider');
    $user = Engine_Api::_()->user()->getViewer();
    $userLevel = Engine_Api::_()->user()->getViewer()->level_id;

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'id' => 'provider_title',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
      'autofocus' => 'autofocus',
    ));

    $this->addElement('Text', 'slug', array(
      'label' => 'URL',
      'allowEmpty' => false,
      'required' => true,
      'id' => 'provider_slug',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
      'autofocus' => 'autofocus',
    ));
    
    // init to
    $this->addElement('Text', 'tags', array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Text', 'designation', array(
      'label' => 'Designation',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '252'))
      ),
      'autofocus' => 'autofocus',
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'allowEmpty' => false,
      'description' => 'Description length cannot exceed 300 characters.',
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '300'))
      ),
      'autofocus' => 'autofocus',
    ));

    $this->addElement('File', 'photo', array(
      'label' => 'Choose Profile Photo',
    ));

    $this->addElement('File', 'coverPhoto', array(
      'label' => 'Choose Cover Photo',
    ));

    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array("1"=>"Published", "0"=>"Saved As Draft"),
      'description' => 'If this entry is published, it cannot be switched back to draft mode.'
    ));
    $this->status->getDecorator('Description')->setOption('placement', 'append');

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->addElement('Select', 'timezone', array(
      'label' => 'Timezone',
      'value' => Engine_Api::_()->getItem('user',$viewer)->timezone,
      'multiOptions' => array(
        'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
        'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
        'US/Central' => '(UTC-6) Central Time (US & Canada)',
        'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
        'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
        'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
        'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
        'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
        'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
        'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
        'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
        'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
        'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
        'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
        'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
        'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
        'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
        'Iran' => '(UTC+3:30) Tehran',
        'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
        'Asia/Kabul' => '(UTC+4:30) Kabul',
        'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
        'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
        'Asia/Katmandu' => '(UTC+5:45) Nepal',
        'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
        'Indian/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
        'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
        'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
        'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
        'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
        'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
        'Asia/Magadan' => '(UTC+11) Magadan, Solomon Is., New Caledonia',
        'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
      ),
    ));
    $this->timezone->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    
    $locationFieldcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.locationfield",'yes');

    if($locationFieldcoreSettings === "yes") {
      $this->addElement('Text', 'location', array(
        'label' => 'Location',
        'id'=>'location',
        'placeholder'=> 'Enter Location',
        'allowEmpty' => false,
        'required' => true,
      )); 
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $viewOptions = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'auth_view');
   
    $this->addElement('Hidden', 'view', array(
      'label' => 'View Privacy',
      'value' => $viewOptions,
      'order' => 996,
    ));

    $viewOptions = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitebooking_pro', $viewer, 'auth_comment');

   
    $this->addElement('Hidden', 'comment', array(
      'label' => 'View Privacy',
      'value' => $viewOptions,
      'order' => 997,
    ));
    
    $this->addElement('Hidden', 'location_region', array(
      'id' => 'locationParams',
      'order' => 999,
    ));

    // // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
    ));

    
  }
  
}
?>