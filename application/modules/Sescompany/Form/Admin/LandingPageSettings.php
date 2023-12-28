<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: LandingPageSettings.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sescompany_Form_Admin_LandingPageSettings extends Engine_Form {

  public function init() {
  
    $files[] = '';
    $path = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach ($path as $file) {
      if ($file->isDot() || !$file->isFile())
        continue;
      $base_name = basename($file->getFilename());
      if (!($pos = strrpos($base_name, '.')))
        continue;
      $extension = strtolower(ltrim(substr($base_name, $pos), '.'));
      if (!in_array($extension, array('gif', 'jpg', 'jpeg', 'png')))
        continue;
      $files['public/admin/' . $base_name] = $base_name;
    }
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $fileLink = $view->baseUrl() . '/admin/files/';

    $this->setTitle('Landing Page Settings')
            ->setDescription('These settings affect on your community landing page.');

    $this->addElement('Radio', 'sescompany_chooselandingdesign', array(
      'label' => 'Landing Page Design',
      'description' => 'Choose Landing Page Design',
      'multiOptions' => array(
          1 => 'Design - 1',
          2 => 'Design - 2',
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.chooselandingdesign', 1),
    ));
    
    $this->addElement('Radio', 'sescompany_rightsidenavigation', array(
      'label' => 'Floating Navigation Icons',
      'description' => 'Do you want to add Floating Navigation Icons or not?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.rightsidenavigation', 1),
    ));


    $this->addElement('Dummy', 'sescompany_la1slider', array(
      'label' => 'Slider For Design 1 and Design 2',
      'description' => 'You can configure detailed settings from here: <a href="admin/sescompany/manage-slides" target="_blank">Manage Slides</a>',
    ));
    $this->sescompany_la1slider->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    $this->addElement('Text', 'sescompany_sliderheading', array(
      'label' => 'Slider Heading',
      'description' => 'Enter slider heading.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderheading', 'World\'s Strongest Professional Network'),
    ));

    $this->addElement('Textarea', 'sescompany_sliderdescription', array(
      'label' => 'Slider Description',
      'description' => 'Enter Slider Description.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderdescription', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. '),
    ));

    $this->addElement('Text', 'sescompany_slidermorebtntext', array(
      'label' => 'Slider View More Button Text',
      'description' => 'Enter slider view more button text.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtntext', 'View More'),
    ));
            
    $this->addElement('Text', 'sescompany_slidermorebtnlink', array(
      'label' => 'Slider View More Button Link',
      'description' => 'Enter slider view more button link.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtnlink', ''),
    ));
    
    $this->addElement('Text', 'sescompany_slidermorebtnlink', array(
      'label' => 'Slider View More Button Link',
      'description' => 'Enter slider view more button link.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtnlink', ''),
    ));
    
    
    $this->addElement('Radio', 'sescompany_slidersharelink', array(
      'label' => 'Slider Share Link',
      'description' => 'Do you want to show share link in slider section?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => 'slidersharelink(this.value)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidersharelink', 1),
    ));
    
    $this->addElement('Text', 'sescompany_sliderfacebooklink', array(
      'label' => 'Share Facebook link',
      'description' => 'Enter Facebook link to share.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderfacebooklink', ''),
    ));
    $this->addElement('Text', 'sescompany_slidertwitterlink', array(
      'label' => 'Share Twitter Link',
      'description' => 'Enter Twitter link to share.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidertwitterlink', ''),
    ));
    $this->addElement('Text', 'sescompany_slidergooglelink', array(
      'label' => 'Share Google Link',
      'description' => 'Enter Google link to share.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidergooglelink', ''),
    ));
    
    $this->addElement('Dummy', 'sescompany_la1aboutus', array(
      'label' => 'Introduction (Video & Features Section) For Design 1',
      'description' => 'You can configure detailed settings from here: <a href="admin/sescompany/manage-abouts" target="_blank">Manage Introduction (Video & Features Section)</a>',
    ));
    $this->sescompany_la1aboutus->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Radio', 'sescompany_la1aboutshow', array(
      'label' => 'Enable',
      'description' => 'Do you want to Enable this section?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => "aboutus(this.value);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1aboutshow', 1),
    ));
    
    $this->addElement('Text', 'sescompany_la1abtheading', array(
      'label' => 'Heading for Video',
      'description' => 'Enter heading for Video section.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtheading', 'About Us'),
    ));

    $this->addElement('Select', 'sescompany_la1abtbgimage1', array(
      'label' => 'Background image for Video section',
      'description' => 'Choose from below the background image for video section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtbgimage1', ''),
    ));
    $this->sescompany_la1abtbgimage1->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
  
    $this->addElement('Text', 'sescompany_la1abtvideourl', array(
      'label' => 'YouTube Video URL',
      'description' => 'Enter YouTube video url.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtvideourl', ''),
    ));
    
    $this->addElement('Select', 'sescompany_la1abtbgimage2', array(
      'label' => 'Background image for feature section',
      'description' => 'Choose from below the the background image for feature section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtbgimage2', ''),
    ));
    $this->sescompany_la1abtbgimage2->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    
    $this->addElement('Dummy', 'sescompany_la1counter', array(
      'label' => 'Statistics For Design 1',
      'description' => 'You can configure detailed settings from here: <a href="admin/sescompany/manage-counters" target="_blank">Manage Statistics</a>',
    ));
    $this->sescompany_la1counter->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Radio', 'sescompany_la1countershow', array(
      'label' => 'Show Statistics',
      'description' => 'Do you want to show statistics?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => "counter(this.value);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1countershow', 1),
    ));
    
    $this->addElement('Text', 'sescompany_la1countersheading', array(
      'label' => 'Right Side Icon Text',
      'description' => 'Enter right side icon tip text.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1countersheading', 'Statistics'),
    ));
    
    $this->addElement('Select', 'sescompany_la1cntbgimage', array(
      'label' => 'Background Image',
      'description' => 'Choose from below the background image for statistics section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1cntbgimage', ''),
    ));
    $this->sescompany_la1cntbgimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    
    $this->addElement('Dummy', 'sescompany_la1features', array(
      'label' => 'Features For Design 1 & Design 2',
      'description' => 'You can configure detailed settings from here: <a href="admin/sescompany/manage-features" target="_blank">Manage Features</a>',
    ));
    $this->sescompany_la1features->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Text', 'sescompany_la1featuresheading', array(
      'label' => 'Heading',
      'description' => 'Enter features heading.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresheading', 'Highlighted Features'),
    ));
    
    $this->addElement('Radio', 'sescompany_la1featuresshow', array(
      'label' => 'Show Features',
      'description' => 'Do you want to show Features?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => "features(this.value);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresshow', 1),
    ));
    
    $this->addElement('Select', 'sescompany_la1fetbgimage', array(
      'label' => 'Background Image',
      'description' => 'Choose from below the background image for features section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1fetbgimage', ''),
    ));
    $this->sescompany_la1fetbgimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    $this->addElement('Dummy', 'sescompany_la1clients', array(
      'label' => 'Clients For Design 1',
      'description' => 'You can configure detailed settings from here: <a href="admin/sescompany/manage-clients" target="_blank">Manage Clients</a>',
    ));
    $this->sescompany_la1clients->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Text', 'sescompany_la1clientsheading', array(
      'label' => 'Heading',
      'description' => 'Enter our clients heading.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1clientsheading', 'Our Clients'),
    ));

    $this->addElement('Radio', 'sescompany_la1clientsshow', array(
      'label' => 'Show Clients',
      'description' => 'Do you want to show Clients?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => "clients(this.value);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1clientsshow', 1),
    ));
    
    $this->addElement('Select', 'sescompany_la1clientsbgimage', array(
      'label' => 'Background Image',
      'description' => 'Choose from below the background image for clients section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1clientsbgimage', ''),
    ));
    $this->sescompany_la1clientsbgimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    

    $this->addElement('Dummy', 'sescompany_mngtestimonials', array(
      'label' => 'Testimonials For Design 1 and Design 2',
      'description' => 'You can configure detailed settings from here: <a href="admin/sescompany/manage-testimonials" target="_blank">Testimonials</a>',
    ));
    $this->sescompany_mngtestimonials->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Radio', 'sescompany_la1testimonialssshow', array(
      'label' => 'Show Testimonials',
      'description' => 'Do you want to show testimonials?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => 'testimonials(this.value);',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1testimonialssshow', 1),
    ));
    
    $this->addElement('Text', 'sescompany_la1testimonialsheading', array(
      'label' => 'Right Side Icon Text',
      'description' => 'Enter right side icon text for tip.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1testimonialsheading', 'Testimonials'),
    ));
    
    
    $this->addElement('Dummy', 'sescompany_mngcontents', array(
      'label' => 'Manage Contents For Design 1 and Design 2',
    ));
    
    $this->addElement('Radio', 'sescompany_la1contentssshow', array(
      'label' => 'Show Contents',
      'description' => 'Do you want to show contents?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => "contents(this.value);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1contentssshow', 1),
    ));
    
    
    $moduleEnable = Engine_Api::_()->sescompany()->getModulesEnable();

    $this->addElement('Select', "sescompany_contmodule", array(
      'label' => 'Choose the Module to be shown in this widget.',
      'description' => 'Choose the Module to be shown in this widget.',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => $moduleEnable,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.contmodule', ''),
    ));

    $this->addElement('Text', 'sescompany_contheading', array(
      'label' => 'Heading',
      'description' => 'Enter Heading.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.contheading', 'Blog'),
    ));


    
    $this->addElement('Select', "sescompany_contpopularitycriteria", array(
      'label' => 'Choose the popularity criteria in this widget.',
      'description' => 'Choose the popularity criteria in this widget.',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => array(
        'creation_date' => 'Recently Created',
        'view_count' => 'View Count',
        'like_count' => 'Most Liked',
        'comment_count' => 'Most Commented',
        'modified_date' => 'Recently Modified'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.contpopularitycriteria', 'creation_date'),
    ));
    
    $this->addElement('Text', 'sescompany_contlimit', array(
      'label' => 'Limit',
      'description' => 'Enter limit.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.contlimit', 4),
    ));
    
    
    $this->addElement('Select', 'sescompany_mngcontentsbgimage', array(
      'label' => 'Background Image',
      'description' => 'Choose from below the background image for contents section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.mngcontentsbgimage', ''),
    ));
    $this->sescompany_mngcontentsbgimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    
    
    $this->addElement('Dummy', 'sescompany_la2photos', array(
      'label' => 'Photo Gallery (Landing Page Design 2)',
    ));
    
    $this->addElement('Radio', 'sescompany_la2photosshow', array(
      'label' => 'Show Photo Gallery',
      'description' => 'Do you want to show photo gallery?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => "photos(this.value);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photosshow', 1),
    ));
    
    $this->addElement('Text', 'sescompany_la2photosheading', array(
      'label' => 'Heading',
      'description' => 'Enter Heading.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photosheading', 'Photo Gallery'),
    ));
    
    
    $this->addElement('Text', 'sescompany_la2photoslimit', array(
      'label' => 'Limit',
      'description' => 'Enter limit.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photoslimit', 8),
    ));
    
    
    
    $this->addElement('Dummy', 'sescompany_la2contacts', array(
      'label' => 'Contact Us (Landing Page Design 2)',
    ));
    
    $this->addElement('Radio', 'sescompany_la2contactsshow', array(
      'label' => 'Show Contact Us',
      'description' => 'Do you want to show contact us?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => "contactus(this.value);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsshow', 1),
    ));
    
    
    $this->addElement('Text', 'sescompany_la2contactsheading', array(
      'label' => 'Heading',
      'description' => 'Enter Heading.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsheading', 'Contact Us'),
    ));
    
    
    $this->addElement('Select', 'sescompany_la2contactsbgimage', array(
      'label' => 'Background Image',
      'description' => 'Choose from below the background image for contact us section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsbgimage', ''),
    ));
    $this->sescompany_la2contactsbgimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));    
    
    
    $this->addElement('Select', 'sescompany_la2contactsmainimage', array(
      'label' => 'Main Image',
      'description' => 'Choose from below the main image for contact us section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsmainimage', ''),
    ));
    $this->sescompany_la2contactsmainimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false)); 
    

    $editorOptions['plugins'] = array(
      'table', 'fullscreen','preview', 'paste',
      'code', 'textcolor',  'link'
    );
    $editorOptions['toolbar1'] = array('undo', 'redo', 'removeformat', 'pastetext', '|', 'code', 'media', 'image',  'link', 'fullscreen', 'preview');
    $editorOptions['toolbar2'] = array(
      'fontselect','fontsizeselect','bold','italic','underline','strikethrough','forecolor','backcolor','|','alignleft','aligncenter','alignright','alignjustify','|','bullist','numlist','|','outdent','indent','blockquote',
    );
  
    $this->addElement('TinyMce', 'sescompany_la2contactsdescription', array(
      'label' => 'Contact Us Description',
      'required' => false,
      'allowEmpty' => true,
      'class'=>'tinymce',
      'editorOptions' => $editorOptions,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsdescription', '<h3>HEAD OFFICE</h3><p>70 abc road,India&nbsp;<br>Phone: 2122454485&nbsp;<br>Fax: 2122454485&nbsp;<br>Zip Code:20692&nbsp;<br>Email: support@mail.com</p><h3>CUSTOMER CARE</h3><p>1800-1234-5678</p><h3>VISIT US</h3><p>www.abc.com</p>'),
    ));
    
    $this->addElement('Text', 'sescompany_la2contactslocation', array(
      'label' => 'Location For Map',
      'description' => 'Enter location for map.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactslocation', ''),
    ));
    
    $this->addElement('Dummy', 'sescompany_la2teams', array(
      'label' => 'Team For Landing Page Design 1 & Design 2',
      'description' => 'You can configure detailed settings from here: <a href="admin/sescompany/manage-teams" target="_blank">Manage Teams</a>',
    ));
    $this->sescompany_la2teams->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Radio', 'sescompany_la2teamsshow', array(
      'label' => 'Show Teams Section',
      'description' => 'Do you want to show teams section?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => "teams(this.value);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsshow', 1),
    ));
    
    
    $this->addElement('Text', 'sescompany_la2teamsheading', array(
      'label' => 'Heading',
      'description' => 'Enter Heading.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsheading', 'Our Teams'),
    ));

    $this->addElement('Select', 'sescompany_la2teamsbgimage', array(
      'label' => 'Background Image',
      'description' => 'Choose from below the background image for teams us section. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
      'multiOptions' => $files,
      'escape' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsbgimage', ''),
    ));
    $this->sescompany_la2teamsbgimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false)); 
    
    
    
    
    

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}