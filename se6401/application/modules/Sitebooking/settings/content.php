<?php

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

return array(

  array(
    'title' => 'Service Booking Navigation Tabs',
    'description' => 'Displays navigation tabs including Services Home, Providers Home, Browse Services, Browse Providers, My Appointments, Create Providers & Wishlists for Service Booking Plugin.',
    'category' =>  $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Service Search Form',
    'description' => 'Displays a search form in the sitebooking browse/manage page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-search',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Provider Search Form',
    'description' => 'Displays the form for searching service providers.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-search',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Service Info',
    'description' => 'Displays a service related information like rating, description, comment and like etc.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-info',
    'defaultParams' => array(
      'title' => 'More Info'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Service Rating',
    'description' => 'Displays a Service rating.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-rating',
    'defaultParams' => array(
      'title' => 'Rating'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Provider Rating',
    'description' => 'Displays a Provider\'s rating.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-rating',
    'defaultParams' => array(
      'title' => 'Rating'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'User Review',
    'description' => 'Displays a User Review.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.user-review',
    'defaultParams' => array(
      'title' => 'Reviews'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Service Suggestion',
    'description' => 'Displays services list similar to the service being viewed at service view/profile page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-suggestion',
    'defaultParams' => array(
      'title' => 'Similar Services'
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ItemLimit',
  ),

  array(
    'title' => 'Service Options',
    'description' => 'Displays a list of options for a service at view page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-options',
    'defaultParams' => array(
      'title' => 'Options'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Services Carousel',
    'description' => 'Displays recently created services in carousel.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-carousel',
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ItemLimit',
  ),

  array(
    'title' => 'Services Category Carousel',
    'description' => 'Display all the categories in carousel. This widget can only be placed on Service and Provider Home page only, It will not display on other pages if placed.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.category-carousel',
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ItemLimit',
  ),

  array(
    'title' => 'Service List Tabs',
    'description' => 'Display Filtered Services based on widget settings.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-list-tabs',
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ServiceListTabs',
  ),
   array(
    'title' => 'Service List Tabs for Offices',
    'description' => 'Display Filtered Services for Offices based on widget settings.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-list-tabs-classrooms',
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ServiceListTabs',
  ),
  array(
    'title' => 'Provider Info',
    'description' => 'Displays a service provider\'s details at view page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-info',
    'defaultParams' => array(
      'title' => 'Info'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Provider Overview',
    'description' => 'Displays a service provider\'s description at view page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-overview',
    'defaultParams' => array(
      'title' => 'Overview',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Provider Suggestions For Provider View Page',
    'description' => 'Displays a list of similar service providers at provider view page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-suggestion',
    'defaultParams' => array(
      'title' => 'Similar Providers'
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ItemLimit'
  ),

  array(
    'title' => 'Provider Options',
    'description' => 'Displays a list of options for service providers at view page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-options',
    'defaultParams' => array(
      'title' => 'Options'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Provider Services',
    'description' => 'Displays a list of service provider\'s services for at view page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-services',
    'defaultParams' => array(
      'title' => 'Services'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Dashboard Menu',
    'description' => 'Displays a list of tabs for service Provider to manage the particular provider and its services.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.dashboard-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Provider List Tabs',
    'description' => 'Displays Filtered Provider based on the widget settings.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-list-tabs',
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ProviderListTabs',
  ), 
  
  array(
    'title' => 'Provider Cover',
    'description' => 'Displays cover photo of the Service Provider along with other info like no. of likes, comments and social share icons. Place this widget on the Service Provider View Page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-cover',
    'requirements' => array(
      'no-subject',
    ),
  ),
  
  array(
    'title' => 'Provider Location',
    'description' => 'Displays Service Provider\'s location on map .',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-location',
    'requirements' => array(
      'no-subject',
    ),
  ),
  
  array(
    'title' => 'Service Top Cover',
    'description' => 'Display Service profile photo along with other information like its cost, category, rating etc.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-top-cover',
    'requirements' => array(
      'no-subject',
    ),
  ), 

  array(
    'title' => 'Providers Carousel',
    'description' => 'Displays recently created service providers in carousel.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-carousel',
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ItemLimit',
  ),

  array(
    'title' => 'Service Sidelisting',
    'description' => 'Displays filtered services based on the widget settings in the left or right column. This widget should not be placed in middle column.  ',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-sidelisting',
    'defaultParams' => array(
      'title' => 'Featured Services',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ServiceSidelisting',
  ),

  array(
    'title' => 'Provider Sidelisting',
    'description' => 'Displays filtered providers based on the widget settings in the left or right column. This widget should not be placed in middle column. ',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-sidelisting',
    'defaultParams' => array(
      'title' => 'Featured Providers',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ProviderSidelisting',
  ),

  array(
    'title' => 'Service Of The Day',
    'description' => 'Displays service of the day according to the admin selected date. ',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-of-the-day',
    'defaultParams' => array(
      'title' => 'Service of the Day'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Provider Of The Day',
    'description' => 'Displays Provider of the day according to the admin selected date. ',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-of-the-day',
    'defaultParams' => array(
      'title' => 'Provider of the Day'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Service Breadcrumb',
    'description' => 'Displays the path of service in Service view/profile page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-breadcrumb',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Service Providers Suggestions For Service View Page',
    'description' => 'Displays provider list similar to service\'s provider at service view/profile page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-provider-suggestion',
    'defaultParams' => array(
      'title' => 'Similar Providers'
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ItemLimit'
  ),

  array(
    'title' => 'Parent Service Provider',
    'description' => 'Displays the provider of the service at service view/profile page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-provider',
    'defaultParams' => array(
      'title' => 'Service Provider'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Review and Contact Button',
    'description' => 'Displays Review and Contact button at provider\'s or service\'s view/profile page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.review-button',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Service Overview',
    'description' => 'Displays overview at service view/profile page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-overview',
    'defaultParams' => array(
      'title' => 'Overview'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Popular Services Tags',
    'description' => 'Displays popular tags of services on Service Home Page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-tag',
    'requirements' => array(
      'no-subject',
    ),
    'defaultParams' => array(
      'title' => 'Popular Service Tags',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ItemLimit',

  ),

  array(
    'title' => 'Popular Providers Tags',
    'description' => 'Displays popular tags of Providers on Provider Home Page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-tag',
    'requirements' => array(
      'no-subject',
    ),
    'defaultParams' => array(
      'title' => 'Popular Provider Tags',
    ),
    'adminForm' => 'Sitebooking_Form_Widgets_ItemLimit',
  
  ),

  array(
    'title' => 'Browse Services',
    'description' => 'Displays all the services existing on the site. This widget should be placed on the Service Browse Page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-browse',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Browse Providers',
    'description' => 'Displays all the service providers existing on the site. This widget should be placed on the Service Provider Browse Page.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.provider-browse',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Service Timing',
    'description' => 'Displays the opening and closing time of service on service view/profile page only.',
    'category' => $view->translate('Services Booking & Appointments'),
    'type' => 'widget',
    'name' => 'sitebooking.service-timing',
    'defaultParams' => array(
      'title' => 'Service Timings',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

)
  
  

?>