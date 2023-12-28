<?php

class Sitebooking_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.
      ");
    
    // Element: view
    $viewOptions = array(
        2 => 'Yes, allow viewing of all services, even private ones.',
        1 => 'Yes, allow viewing of services.',
        0 => 'No, do not allow services to be viewed.',
      );

    $this->addElement('Radio', 'sitebooking_ser_view', array(
      'label' => 'Allow Viewing of Services?',
      'description' => 'Do you want to let members view Services? If set to no, some other settings on this page may not apply.',
      'multiOptions' => $viewOptions,
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    
    if( !$this->isModerator() ) {
      unset($this->sitebooking_ser_view->options[2]);
    }

   //Element: Provider view
    $providerViewOptions = array(
      2 => 'Yes, allow viewing of all service providers, even private ones.',
      1 => 'Yes, allow viewing of service providers.',
      0 => 'No, do not allow service providers to be viewed.',
    );

    $this->addElement('Radio', 'sitebooking_pro_view', array(
      'label' => 'Allow Viewing of Service Providers?',
      'description' => 'Do you want to let members view Service Providers? If set to no, some other settings on this page may not apply.',
      'multiOptions' => $providerViewOptions,
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    
    if( !$this->isModerator() ) {
      unset($this->sitebooking_pro_view->options[2]);
    }




    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'sitebooking_ser_create', array(
        'label' => 'Allow Creation of Services?',
        'description' => 'Do you want to let members create services? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view services, but only want certain levels to be able to create services.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of services.',
          0 => 'No, do not allow services to be created.'
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'sitebooking_ser_edit', array(
        'label' => 'Allow Editing of Services?',
        'description' => 'Do you want to let members edit services? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all services.',
          1 => 'Yes, allow members to edit their own services.',
          0 => 'No, do not allow members to edit their services.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->sitebooking_ser_edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'sitebooking_ser_delete', array(
        'label' => 'Allow Deletion of Services?',
        'description' => 'Do you want to let members delete services? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all services.',
          1 => 'Yes, allow members to delete their own services.',
          0 => 'No, do not allow members to delete their services.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->sitebooking_ser_delete->options[2]);
      }

        
      $availableLabels = array(
        'everyone'            => 'Everyone',
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Friends and Networks',
      );

      // Element: auth privacy
      $this->addElement('Select', 'sitebooking_ser_auth_view', array(
        'label' => 'Privacy',
        'description' => 'Who may see the services created by members of this level? Any change done in this setting will apply for newly added services only.',
        'multiOptions' => $availableLabels,
      ));
      // $this->privacy->getDecorator("Description")->setOption("placement", "append");

      // Element: comment
      $this->addElement('Radio', 'sitebooking_ser_comment', array(
        'label' => 'Allow Commenting on Services?',
        'description' => 'Do you want to let members of this level comment on services?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all services, including private ones.',
          1 => 'Yes, allow members to comment on services.',
          0 => 'No, do not allow members to comment on services.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->sitebooking_ser_comment->options[2]);
      }

      $availableLabels = array(
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Friends and Networks',
      );

      // Element: comment privacy
      $this->addElement('Select', 'sitebooking_ser_auth_comment', array(
        'label' => 'Services Comment Privacy',
        'description' => 'Who may comment on services created by member of this level? Any change done in this setting will apply for newly added services only.',
        'multiOptions' => $availableLabels,
      ));

      // Maximum service allowed to create
      $this->addElement('Text', 'sitebooking_ser_max', array(
        'label' => 'Maximum Services Allowed',
        'description' => 'Maximum services allowed to be created by a member.',
        'allowEmpty' => false,
        'required' => true,
        'value' => 0,
        'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
        ),
        'filters' => array(
            new Engine_Filter_Censor(),
            'StripTags',
      ),
      ));

      // Element: service service auto approving
      $this->addElement('Radio', 'sitebooking_ser_approve', array(
        'label' => 'Allow Auto Approval of Services?',
        'description' => 'Do you want services created by the members of this level to be automatically approved?',
        'multiOptions' => array(
          1 => 'Yes, automatically approve services.',
          0 => 'No, admin will need to approve services.'
        ),
        'value' => 1,
      ));


      // Element: Provider create
      $this->addElement('Radio', 'sitebooking_pro_create', array(
        'label' => 'Allow Creation of Service Providers?',
        'description' => 'Do you want to let members create Service Providers? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view Service Providers, but only want certain levels to be able to create Service Providers.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of Service Providers.',
          0 => 'No, do not allow Service Providers to be created.'
        ),
        'value' => 1,
      ));

      // Element: Provider edit
      $this->addElement('Radio', 'sitebooking_pro_edit', array(
        'label' => 'Allow Editing of Service Providers?',
        'description' => 'Do you want to let members edit Service Providers? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all service providers.',
          1 => 'Yes, allow members to edit their own service providers.',
          0 => 'No, do not allow members to edit their service providers.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->sitebooking_pro_edit->options[2]);
      }

      // Element: Provider delete
      $this->addElement('Radio', 'sitebooking_pro_delete', array(
        'label' => 'Allow Deletion of Service Providers?',
        'description' => 'Do you want to let members delete service providers? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all service providers.',
          1 => 'Yes, allow members to delete their own service providers.',
          0 => 'No, do not allow members to delete their service providers.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->sitebooking_pro_delete->options[2]);
      }


      $providerAvailableLabels = array(
        'everyone'            => 'Everyone',
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Friends and Networks',
      );

      // Element:Provider auth view
      $this->addElement('Select', 'sitebooking_pro_auth_view', array(
        'label' => 'Privacy',
        'description' => 'Who may see the service providers created by the members of this level? Any change done in this setting will apply for newly added service providers only.',
        'multiOptions' => $providerAvailableLabels,
      ));

      // Element: comment
      $this->addElement('Radio', 'sitebooking_pro_comment', array(
        'label' => 'Allow Commenting on Service Providers?',
        'description' => 'Do you want to let members of this level comment on service providers?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all service providers, including private ones.',
          1 => 'Yes, allow members to comment on service providers.',
          0 => 'No, do not allow members to comment on service providers.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->sitebooking_pro_comment->options[2]);
      }

      $availableLabels = array(
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Friends and Networks',
      );

      // Element: provider comment privacy
      $this->addElement('Select', 'sitebooking_pro_auth_comment', array(
        'label' => 'Service Provider Comment Privacy',
        'description' => 'Who may comment on service providers created by members of this level? Any change done in this setting will apply for newly added service providers only.',
        'multiOptions' => $availableLabels,
      ));



      // Maximum service provider to create per user
      $this->addElement('Text', 'sitebooking_pro_max', array(
        'label' => 'Maximum Service Providers Allowed',
        'description' => 'Maximum Service Provider allowed to be created by a member.',
        'allowEmpty' => false,
        'required' => true,
        'value' => 0,
        'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
        ),
        'filters' => array(
            new Engine_Filter_Censor(),
            'StripTags',
      ),
      ));
      // Element: service provider auto approving
      $this->addElement('Radio', 'sitebooking_pro_approve', array(
        'label' => 'Auto Approval of Service Providers?',
        'description' => 'Do you want service providers created by members of this level to be automatically approved?',
        'multiOptions' => array(
          1 => 'Yes, automatically approve service providers.',
          0 => 'No, admin will need to approve service providers.'
        ),
        'value' => 1,
      ));
    }
  }
}