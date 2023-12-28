<?php


class Sitecourse_Form_Admin_Global extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.

    public function init() {

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this
        ->setTitle('Global Settings')
        ->setDescription('These settings affect all members in your community.');

        // $this->addElement('Text', 'language_phrases_course', array(
        //     'label' => 'Singular Course Title',
        //     'description' => 'Please enter a Singular Title for the Course. This text will come in places like feeds generated, widgets, etc',
        //     'allowEmpty' => FALSE,
        //     'validators' => array(
        //         array('NotEmpty', true),
        //     ),
        //     'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.course", "course"),
        // ));

        // $this->addElement('Text', 'language_phrases_courses', array(
        //     'label' => 'Plural Course Title',
        //     'description' => ' Please enter Plural Title for Courses. This text will come in places like Main Navigation Menu, Course Main Navigation Menu, widgets, etc.',
        //     'allowEmpty' => FALSE,
        //     'validators' => array(
        //         array('NotEmpty', true),
        //     ),
        //     'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.courses", "courses"),
        // ));

        $this->addElement('Text', 'sitecourse_UrlS', array(
            'label' => 'Courses URL alternate text for "course"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "course" in the URLs of this plugin.',
            'value' => $coreSettings->getSetting('sitecourse.UrlS', "course"),
        ));

        $this->addElement('Text', 'sitecourse_UrlP', array(
            'label' => 'Courses URL alternate text for "courses"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "courses" in the URLs of this plugin.            ',
            'value' => $coreSettings->getSetting('sitecourse.UrlP', "courses"),
        ));
        
        $this->addElement('Radio', 'sitecourse_allow_editcategory', array(
            'label' => 'Edit Course Category',
            'description' => 'Do you want to allow course owners to edit the category of their courses?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.editcategory',0),
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
        ));

        $this->addElement('Radio', 'sitecourse_allow_announcements', array(
            'label' => 'Announcements',
            'description' => 'Do you want announcements to be enabled for courses?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.announcements',0),
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
        ));

        $this->addElement('Radio', 'sitecourse_allow_tags', array(
            'label' => 'Allow Tags/Keywords',
            'description' => 'Do you want to enable course owners to add tags for their courses?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.tags',0),
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
        ));

        $this->addElement('Radio', 'sitecourse_allow_report', array(
            'label' => 'Report as Inappropriate',
            'description' => 'Do you want to allow logged-in members to be able to report courses as inappropriate? (Members will also be able to mention the reason why they find the courses inappropriate.)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.report', 0),
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
        ));

        $this->addElement('Text', 'sitecourse_latest_threshold', array(
            'label' => 'Most Newest Threshold Value',
            'description' => ' Enter the threshold value (in days) of a course till which it will be marked as “Newest”.',
            'value' => $coreSettings->getSetting('sitecourse.latest.threshold', '0'),
            'allowEmpty' => false,
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(1),
            ),
        ));

        $this->addElement('Text', 'sitecourse_bestseller_threshold', array(
            'label' => 'BestSeller Threshold Value',
            'description' => 'Enter the threshold limit of enrollments count after which a course will be marked as "Best Seller".',
            'value' => $coreSettings->getSetting('sitecourse.bestseller.threshold', '0'),
            'allowEmpty' => false,
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(1),
            ),
        ));

        $this->addElement('Text', 'sitecourse_mostrated_threshold', array(
            'label' => 'Most Rated Threshold Value',
            'description' => 'Enter the average threshold value of ratings after which a course will be marked as “Most Rated”.',
            'value' => $coreSettings->getSetting('sitecourse.mostrated.threshold', '0'),
            'allowEmpty' => false,
            'validators' => array(
                array('Float', true),
                new Engine_Validate_AtLeast(0.1),
                array('LessThan', true, array(5.1)),
            ),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}

?>
