<?php
class Sitebooking_Form_Admin_Settings_Ratingandreview extends Engine_Form
{   
  public function init()
  {	

    $this
      ->setTitle('Review & Ratings Settings')
      ->setDescription('Reviews & ratings are an extremely useful feature that enables you to gather refined ratings, reviews and feedback for the Services & Providers in your community. Below, you can enable / disable the display of reviews & ratings on your site.');

    $ServiceReview = array();
    $ServiceReview["service_none"] = "None";
    $ServiceReview["service_onlyRating"] = "Only Rating";
    $ServiceReview["service_Rating&Review"] = "Rating & Review Both";

    $ProviderReview = array();
    $ProviderReview["provider_none"] = "None";
    $ProviderReview["provider_onlyRating"] = "Only Rating";
    $ProviderReview["provider_Rating&Review"] = "Rating & Review Both";

   	$coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('radio', 'sitebooking_serviceReview', array(
      'label' => 'Service Review & Rating',
      'description' => 'Do you want to give users the options to post review & rating on services?',
      'multiOptions' => $ServiceReview,
      'value' => $coreSettings->getSetting('sitebooking.serviceReview',"service_Rating&Review"),
    ));

    $this->addElement('radio', 'sitebooking_providerReview', array(
      'label' => 'Service Provider Review & Rating',
      'description' => 'Do you want to give users the options to post review & rating on service providers?',
      'multiOptions' => $ProviderReview,
      'value' => $coreSettings->getSetting('sitebooking.providerReview',"provider_Rating&Review"),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}