<?php

class Sitecourse_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
    public function init()
    {
        parent::init();

        // My stuff
        $this
        ->setTitle('Member Level Settings')
        ->setDescription("SITECOURSE_FORM_ADMIN_LEVEL_DESCRIPTION");

        // Element: view
        $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing of Courses?',
            'description' => 'Do you want to allow members to view courses? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow viewing of all courses, even private ones.',
                1 => 'Yes, allow viewing of courses.',
                0 => 'No, do not allow courses to be viewed.',
            ),
            'value' => ($this->isModerator() ? 2 : 1),
        ));
        if (!$this->isModerator()) {
            unset($this->view->options[2]);
        }

          if( !$this->isPublic() ) {

        // Element: create
            $this->addElement('Radio', 'create', array(
                'label' => 'Allow Creation of Courses?',
                'description' => ' Do you want to allow members to create courses? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view courses, but only want certain levels to be able to create courses.',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of courses.',
                    0 => ' No, do not allow courses to be created.'
                ),
                'value' => 1,
            ));


        // Element: delete
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow Deletion of Courses?',
                'description' => 'Do you want to allow members to delete courses? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    1 => 'Yes, allow members to delete their own courses.',
                    0 => 'No, do not allow members to delete their courses.',
                ),
                'value' => ( $this->isModerator() ? 1 : 0 ),
            ));


            // Element: comment
            $this->addElement('Radio', 'comment', array(
                'label' => 'Allow Commenting on Courses?',
                'description' => 'Do you want to let members of this level comment on courses?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to comment on all courses, including private ones.',
                    1 => 'Yes, allow members to comment on courses.',
                    0 => 'No, do not allow members to comment on courses.',
                ),
                'value' => ($this->isModerator() ? 1 : 0),
            ));
            if( !$this->isModerator() ) {
                unset($this->comment->options[2]);
            }


        // Element: approve
            $this->addElement('Radio', 'approve', array(
                'label' => 'Automatically Mark Course as Approved',
                'description' => 'Do you want a new course to be automatically approved?',
                'multiOptions' => array(
                    1 => 'Yes, automatically approve the course.',
                    0 => 'No, site admin approval will be required for all courses.',
                ),
                'value' => ( $this->isModerator() ? 1: 0 ),
            ));

        // Element: reviews & ratings
            $this->addElement('Radio', 'reviews_ratings', array(
                'label' => 'Allow Reviews and Ratings?',
                'description' => 'Do you want to allow users of this member level to write a review?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to write a review.',
                    0 => 'No, do not allow members to write a review.',
                ),
                'value' => ( $this->isModerator() ? 1: 0 ),
            ));

            $this->addElement('Radio', 'auto_review_approve', array(
                'label' => 'Automatically Mark Review as Approved',
                'description' => 'Do you want a new Review to be automatically approved?',
                'multiOptions' => array(
                    1 => 'Yes, automatically approve the reviews.',
                    0 => 'No, site admin approval will be required for all reviews.',
                ),
                'value' => ( $this->isModerator() ? 1: 0 ),
            ));

        // Element: review_deletion
            $this->addElement('Radio', 'review_deletion', array(
                'label' => 'Allow Deletion of Reviews? ',
                'description' => ' Do you want to allow users of this member level to delete their reviews?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to delete their own reviews.',
                    0 => 'No, do not allow members to delete their reviews.',
                ),
                'value' => ( $this->isModerator() ? 1: 0 ),
            ));

        // Element: certification
            $this->addElement('Radio', 'certification', array(
                'label' => 'Allow Issuing of Certificates? ',
                'description' => 'Do you want to issue a certificate for this member level?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to issue certificates',
                    0 => 'No, do not allow members to issue certificates.',
                ),
                'value' => ( $this->isModerator() ? 1: 0 ),
            ));

        // Element: auth_view
            $this->addElement('MultiCheckbox', 'auth_view', array(
                'label' => 'Course View Privacy',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their course. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
                'multiOptions' => array(
                    'everyone'            => 'Everyone',
                    'registered'          => 'All Registered Members',
                    'owner_network'       => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member'        => 'Friends Only',
                    'owner'               => 'Only Me'
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));

            // Element: auth_view
            $this->addElement('MultiCheckbox', 'auth_view', array(
                'label' => 'Course View Privacy',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their course. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
                'multiOptions' => array(
                    'everyone'            => 'Everyone',
                    'registered'          => 'All Registered Members',
                    'owner_network'       => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member'        => 'Friends Only',
                    'owner'               => 'Only Me'
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));

            // Element: auth_comment
            $this->addElement('MultiCheckbox', 'auth_comment', array(
                'label' => 'Course Comment Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their courses. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
                'multiOptions' => array(
                    'everyone'            => 'Everyone',
                    'registered'          => 'All Registered Members',
                    'owner_network'       => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member'        => 'Friends Only',
                    'owner'               => 'Just Me'
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));


        // Element: max courses
            $this->addElement('Text', 'max_courses', array(
                'label' => 'Maximum Allowed Courses',
                'allowEmpty'=> false,
                'description' => 'Enter the maximum number of allowed courses. This field must contain an integer, use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));



            // Element: max enrollment
            $this->addElement('Text', 'max_enrollment', array(
                'label' => 'Maximum Enrollment Limit',
                'allowEmpty' => false,
                'description' => 'Enter the maximum number of allowed enrollments for this member level for each course.The field must contain an integer, or use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));

            // Element: max enrollment
            $this->addElement('Text', 'approval_reminders', array(
                'label' => 'Maximum Reminders for Approval',
                'allowEmpty' => false,
                'description' => 'Enter the maximum times a user can send reminders for approval to admin.The field must contain an integer, or use zero for unlimited.',
                'value' => 0,
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));
        }
    }
}
