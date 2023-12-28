<?php 
$module = null;
$controller = null;
$action = null;
$getURL = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
$routes = array();
if (!empty($request)) {
    $module = $request->getModuleName();
    $action = $request->getActionName();
    $controller = $request->getControllerName();
    $getURL = $request->getRequestUri();
}

if (empty($request) || !($module == "default" && ( strpos( $getURL, '/install') !== false))) {

  $db = Engine_Db_Table::getDefaultAdapter();

  $booking_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.bookingplural','bookings');
  $booking_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.bookingsingular', 'booking');
  $provider_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerplural', 'providers');
  $provider_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providersingular', 'provider');
  $service_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceplural', 'services');
  $service_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.servicesingular', 'service');

  $routes = array(
    'sitebooking_service_specific' => array(
      'route' => $booking_plural.'/'.$provider_plural.'/:pro_id/'.$service_singular.'/:action/:ser_id/*',
      'defaults' => array(
        'module' => 'sitebooking',
        'controller' => 'service',
        'action' => 'dashboard',
      ),
      'reqs' => array(
        'ser_id' => '\d+',
        'pro_id' => '\d+',
        'action' => '(delete|edit|tell-a-friend|available)',
      ),
    ),

    'sitebooking_provider_specific' => array(
      'route' => $booking_plural.'/'.$provider_plural.'/:action/:pro_id/*',
      'defaults' => array(
        'module' => 'sitebooking',
        'controller' => 'service-provider',
        'action' => 'index',
      ),
      'reqs' => array(
        'pro_id' => '\d+',
        'action' => '(delete|edit|overview|available|tell-a-friend|booked|contact|disable|enable)',
      ),
    ),
    
    'sitebooking_service_general' => array(
      'route' => $booking_plural.'/'.$provider_plural.'/:pro_id/'.$service_plural.'/:action/*',
      'defaults' => array(
      'module' => 'sitebooking',
      'controller' => 'service',
      'action' => 'index',
      ),
      'reqs' => array(
        'pro_id' => '\d+',
        'action' => '(create|service-manage)',
      ),
    ),

    'sitebooking_service_browse' => array(
      'route' => $booking_plural.'/'.$service_plural.'/:action/*',
      'defaults' => array(
      'module' => 'sitebooking',
      'controller' => 'service',
      'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(home|index|manage|service-wishlist)',
      ),
    ),

    'sitebooking_provider_general' => array(
      'route' => $booking_plural.'/'.$provider_plural.'/:action/*',
      'defaults' => array(
      'module' => 'sitebooking',
      'controller' => 'service-provider',
      'action' => 'index',
      ),
      'reqs' => array(
      'action' => '(index|create|manage|home|provider-wishlist)',
      ),
    ),

    'sitebooking_service_entry_view' => array(
      'route' => $booking_plural.'/:pro_id/'.$service_singular.'/:ser_id/:slug',
      'defaults' => array(
        'module' => 'sitebooking',
        'controller' => 'service',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'pro_id' => '\d+',
        'ser_id' => '\d+'

      ),
    ),

    'sitebooking_provider_view' => array(
      'route' => $booking_plural.'/:user_id/'.$provider_singular.'/:pro_id/:slug',
      'defaults' => array(
        'module' => 'sitebooking',
        'controller' => 'service-provider',
        'action' => 'view',
        'slug' => '',
        
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'pro_id' => '\d+',
      ),
    ),

    'sitebooking_booking_specific' => array(
      'route' => $booking_plural.'/'.$service_singular.'/:action/:ser_id/*',
      'defaults' => array(
        'module' => 'sitebooking',
        'controller' => 'index',
        'action' => 'book-service',
      ),
      'reqs' => array(
        'ser_id' => '\d+',
        'action' => '(book-service|find-availability)',
      ),
    ),

    'sitebooking_booking_general' => array(
      'route' => $booking_plural.'/booked/'.$service_plural.'/:action/*',
      'defaults' => array(
      'module' => 'sitebooking',
      'controller' => 'index',
      'action' => 'booked-services',
      ),
      'reqs' => array(
      'action' => '(booked-services)',
      ),
    ),
    'sitebooking_transaction' => array(
        'route' => $booking_plural.'/transaction/:action/*',
        'defaults' => array(
            'module' => 'sitebooking',
            'controller' => 'transaction',
            'action' => 'index',
        ),
         'reqs' => array(
      'action' => '(index|process|return|finish)',
      ),
    ),
        
    'sitebooking_messages_general' => array(
      'route' => $booking_plural.'/booked/'.$service_plural.'message/:action/*',
      'defaults' => array(
        'module' => 'sitebooking',
        'controller' => 'message',
        'action' => 'compose',
      ),
      'reqs' => array(
        'action' => '\D+',
        'action' => '(inbox|outbox|delete|compose)',
      )
    ),
  );
  
}




return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sitebooking',
    'version' => '5.0.1',
    'seao-sku' => 'sat-sitebooking',
    'path' => 'application/modules/Sitebooking',
    'title' => 'Services Booking & Appointments Plugin',
    'category' => 'Services Booking & Appointments',
    'description' => 'Services Booking & Appointments Plugin',
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Sitebooking/settings/install.php',
      'class' => 'Sitebooking_Installer',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Sitebooking',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/sitebooking.csv',
    ),
  ),

  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onSitebookingSerDeleteBefore',
      'resource' => 'Sitebooking_Plugin_Core',
    ),
    array(
      'event' => 'onSitebookingProDeleteBefore',
      'resource' => 'Sitebooking_Plugin_Core',
    ),
  ),

  'items' => array(
    'sitebooking_ser',
    'sitebooking_category',
    'sitebooking_pro',
    'sitebooking_schedule',
    'sitebooking_providersoverview',
    'sitebooking_providerlocation',
    'sitebooking_servicebooking',
    'sitebooking_itemofthedays',
    'sitebooking_duration',
    'sitebooking_order',
    'token'
  ),

  'routes' => $routes,
            
); ?>

