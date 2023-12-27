
<?php 
require_once realpath(dirname(__FILE__)) . '/seaocore_install.php';

class Sitebooking_Installer extends Sitecore_License_Installer
{

  protected $_installConfig = array(
    'sku' => 'sitebooking',
  );

  public function onInstall()
  {
    $this->_addProviderHomePage();
    $this->_addProviderViewPage();
    $this->_addProviderBrowsePage();
    $this->_addProviderCreatePage();
    $this->_addProviderManagePage();  
    $this->_addProviderEditPage();
    $this->_addProviderOverviewPage();
    $this->_addProviderAvailablePage();
    $this->_addProviderContactPage();
    $this->_addProviderBookigRequestPage();

    $this->_addServiceHomePage();
    $this->_addServiceViewPage();
    $this->_addServiceBrowsePage();
    $this->_addServiceCreatePage();
    $this->_addServiceEditPage();
    $this->_addProviderServiceManagePage();

    $this->_addUserMyAppointmentPage();

    parent::onInstall();
  }

  public function onEnable()
  {

    $this->_addProviderHomePage();
    $this->_addProviderViewPage();
    $this->_addProviderBrowsePage();
    $this->_addProviderCreatePage();
    $this->_addProviderManagePage();  
    $this->_addProviderEditPage();
    $this->_addProviderOverviewPage();
    $this->_addProviderAvailablePage();
    $this->_addProviderContactPage();
    $this->_addProviderBookigRequestPage();

    $this->_addServiceHomePage();
    $this->_addServiceViewPage();
    $this->_addServiceBrowsePage();
    $this->_addServiceCreatePage();
    $this->_addServiceEditPage();
    $this->_addProviderServiceManagePage();

    $this->_addUserMyAppointmentPage();


    parent::onEnable();
  }

  protected function _addProviderManagePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_manage')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_manage',
        'displayname' => 'Service Booking - Service Provider Manage Page',
        'title' => 'Manage Service Providers',
        'description' => 'This page lists a user\'s service provider entries.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainRightId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

    }
  }

  protected function _addProviderCreatePage()
  {

    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_create')
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !$pageId ) {

      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_create',
        'displayname' => 'Service Booking - Service Provider Create Page',
        'title' => 'Create New Service Provider',
        'description' => 'This page is the service provider create page.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));
    }
  }

  protected function _addProviderBrowsePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_index')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_index',
        'displayname' => 'Service Booking - Service Provider Browse Page',
        'title' => 'Service Provider Browse',
        'description' => 'This page lists service provider entries.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert provider browse
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-search',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert provider browse
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-browse',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 2,
      ));
    }
  }

  protected function _addProviderViewPage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_view')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_view',
        'displayname' => 'Service Booking - Service Provider View Page',
        'title' => 'Service Provider View',
        'description' => 'This page displays a service provider.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
      ));
      $mainId = $db->lastInsertId();

      // Insert right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $rightId = $db->lastInsertId();

      // Insert middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $middleId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert provider-cover
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-cover',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 2,
      ));

      // Insert container-tab
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'params' => '{"max":"4","nomobile":"0","name":"core.container-tabs"}',
        'order' => 1,
      ));
      $container_id = $db->lastInsertId();

      // Insert provider-services
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-services',
        'page_id' => $pageId,
        'parent_content_id' => $container_id,
        'params' => '{"title":"Services","name":"sitebooking.provider-services"}',
        'order' => 3,
      ));

      // Insert provider-info
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-info',
        'page_id' => $pageId,
        'parent_content_id' => $container_id,
        'params' => '{"title":"Info","name":"sitebooking.provider-info"}',
        'order' => 1,
      ));

      // Insert provider-overview
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-overview',
        'page_id' => $pageId,
        'parent_content_id' => $container_id,
        'params' => '{"title":"Overview","name":"sitebooking.provider-overview"}',
        'order' => 2,
      ));

      // Insert user-review
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.user-review',
        'page_id' => $pageId,
        'parent_content_id' => $container_id,
        'params' => '{"title":"Reviews","name":"sitebooking.user-review"}',
        'order' => 4,
      ));

      // insert comment
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.comments',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'order' => 2,
      ));

      // Insert provider-rating
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-rating',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'params' => '{"title":"Rating","name":"sitebooking.provider-rating"}',
        'order' => 1,
      ));
      
      // Insert review-button
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.review-button',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'order' => 2,
      ));

      // Insert provider-options
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-options',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'params' => '{"title":"Options","name":"sitebooking.provider-options"}',
        'order' => 3,
      ));


      // Insert provider-location
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-location',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'order' => 4,
      ));

      // insert simmilar-provider
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-suggestion',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'params' => '{"title":"Similar Providers","name":"sitebooking.provider-suggestion"}',
        'order' => 5,
      ));

    }
  }

  protected function _addProviderHomePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_home')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_home',
        'displayname' => 'Service Booking - Service Provider Home Page',
        'title' => 'Service Provider Home',
        'description' => 'This is home page of service provider.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainRightId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      //PROVIDER CAROUSEL
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-carousel',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 2,
      ));

      // category-carousel
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.category-carousel',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 3,
      ));     

      // provider list tabs
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-list-tabs',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'params' => '{"limit":"5","list_id":["featured","newlabel","sponsored","hot"],"view_id":["list","grid"],"name":"sitebooking.provider-list-tabs"}',
        'order' => 2,
      ));
      
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-of-the-day',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'params' => '{"title":"Provider Of The Day","name":"sitebooking.provider-of-the-day"}',
        'order' => 1,
      ));

      // provider sidelisting
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-sidelisting',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'params' => '{"title":"Featured Providers","list_id":"featured","limit":"5","name":"sitebooking.provider-sidelisting"}',
        'order' => 2,
      ));

      // provider provider-tag
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-tag',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'params' => '{"title":"Popular Provider Tags","name":"sitebooking.provider-tag"}',
        'order' => 3,
      ));

    }
  }

  protected function _addProviderEditPage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_edit')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_edit',
        'displayname' => 'Service Booking - Service Provider Edit Page',
        'title' => 'Edit Service Provider',
        'description' => 'This page allow to edit service provider. ',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainLeftId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert dashboard
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.dashboard-menu',
        'page_id' => $pageId,
        'parent_content_id' => $mainLeftId,
        'order' => 1,
      ));

    }
  }

  protected function _addProviderOverviewPage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_overview')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_overview',
        'displayname' => 'Service Booking - Service Provider Overview Page',
        'title' => 'Edit Service Provider\'s Overview',
        'description' => 'This page allow to edit overview. ',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainLeftId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert dashboard
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.dashboard-menu',
        'page_id' => $pageId,
        'parent_content_id' => $mainLeftId,
        'order' => 1,
      ));
    }
  }

  protected function _addProviderAvailablePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_available')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_available',
        'displayname' => 'Service Booking - Service Provider Available Page',
        'title' => 'Edit Service Provider\'s Availabliyi',
        'description' => 'This page allow to see availablitye of service provider. ',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainLeftId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert dashboard
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.dashboard-menu',
        'page_id' => $pageId,
        'parent_content_id' => $mainLeftId,
        'order' => 1,
      ));

    }
  }

  protected function _addProviderBookigRequestPage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_booked')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_booked',
        'displayname' => 'Service Booking - Service Provider Booked Services Page',
        'title' => 'View Booking Requests',
        'description' => 'This page allow to see booked services to service provider. ',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainLeftId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert dashboard
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.dashboard-menu',
        'page_id' => $pageId,
        'parent_content_id' => $mainLeftId,
        'order' => 1,
      ));

    }
  }

  protected function _addProviderContactPage()
  {

    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service-provider_contact')
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !$pageId ) {

      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service-provider_contact',
        'displayname' => 'Service Booking - Service Provider Contact Page',
        'title' => 'Add Service Provider Contact',
        'description' => 'This page is the service provider contact page.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainLeftId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert dashboard
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.dashboard-menu',
        'page_id' => $pageId,
        'parent_content_id' => $mainLeftId,
        'order' => 1,
      ));
    }
  }


  protected function _addProviderServiceManagePage()
  {

    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service_service-manage')
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !$pageId ) {

      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service_service-manage',
        'displayname' => 'Service Booking - Service Manage Page',
        'title' => 'Provider Manage Service',
        'description' => 'This page is the display for services of particular provider.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainLeftId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert dashboard
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.dashboard-menu',
        'page_id' => $pageId,
        'parent_content_id' => $mainLeftId,
        'order' => 1,
      ));
    }
  }

  protected function _addServiceCreatePage()
  {

    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service_create')
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !$pageId ) {

      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service_create',
        'displayname' => 'Service Booking - Service Create Page',
        'title' => 'Create New Service',
        'description' => 'This page is the service create page.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainLeftId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert dashboard
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.dashboard-menu',
        'page_id' => $pageId,
        'parent_content_id' => $mainLeftId,
        'order' => 1,
      ));
    }
  }

  protected function _addServiceEditPage()
  {

    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service_edit')
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !$pageId ) {

      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service_edit',
        'displayname' => 'Service Booking - Service Edit Page',
        'title' => ' Edit Service',
        'description' => 'This page is the service edit page.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainLeftId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert dashboard
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.dashboard-menu',
        'page_id' => $pageId,
        'parent_content_id' => $mainLeftId,
        'order' => 1,
      ));
    }
  }

  protected function _addServiceBrowsePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service_index')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service_index',
        'displayname' => 'Service Booking - Service Browse Page',
        'title' => 'Service Browse',
        'description' => 'This page lists service entries.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert service browse
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-search',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));

      // Insert service browse
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-browse',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 2,
      ));
    }
  }

  protected function _addServiceHomePage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service_home')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service_home',
        'displayname' => 'Service Booking - Service Home Page',
        'title' => 'Service Home',
        'description' => 'This is home page of service.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainRightId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      //SERVICE CAROUSEL
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-carousel',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 2,
      ));

      // category-carousel
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.category-carousel',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 3,
      ));     

      // service list tabs
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-list-tabs',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'params' => '{"limit":"5","list_id":["featured","newlabel","sponsored","hot"],"view_id":["list","grid"],"name":"sitebooking.service-list-tabs"}',
        'order' => 2,
      ));

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-of-the-day',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'params' => '{"title":"Service Of The Day","name":"sitebooking.service-of-the-day"}',
        'order' => 1,
      ));

      // service sidelisting
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-sidelisting',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'params' => '{"title":"Featured Services","list_id":"featured","limit":"5","name":"sitebooking.service-sidelisting"}',
        'order' => 2,
      ));
      // service-tag
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-tag',
        'page_id' => $pageId,
        'parent_content_id' => $mainRightId,
        'params' => '{"title":"Popular Service Tags","name":"sitebooking.service-tag"}',
        'order' => 3,
      ));

    }
  }

  protected function _addServiceViewPage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_service_view')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_service_view',
        'displayname' => 'Service Booking - Service View Page',
        'title' => 'Service View',
        'description' => 'This page displays a service.',
        // 'provides' => 'subject=provider',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
      ));
      $mainId = $db->lastInsertId();

      // Insert right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $rightId = $db->lastInsertId();

      // Insert middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $middleId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert service-breadcrumb
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-breadcrumb',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'order' => 1,
      ));

      // Insert provider-cover
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-top-cover',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'order' => 2,
      ));

      // Insert container-tab
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'params' => '{"max":"4","nomobile":"0","name":"core.container-tabs"}',
        'order' => 3,
      ));
      $container_id = $db->lastInsertId();
      
      // Insert service-overview
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-overview',
        'page_id' => $pageId,
        'parent_content_id' => $container_id,
        'params' => '{"title":"Overview","name":"sitebooking.service-overview"}',
        'order' => 1,
      ));

      // Insert service-info
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-info',
        'page_id' => $pageId,
        'parent_content_id' => $container_id,
        'params' => '{"title":"More Info","name":"sitebooking.service-info"}',
        'order' => 2,
      ));


      // Insert user-review
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.user-review',
        'page_id' => $pageId,
        'parent_content_id' => $container_id,
        'params' => '{"title":"Reviews","name":"sitebooking.user-review"}',
        'order' =>3,
      ));



      // insert comment
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.comments',
        'page_id' => $pageId,
        'parent_content_id' => $middleId,
        'order' => 3,
      ));

      // Insert service-provider
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-provider',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'params' => '{"title":"Provider","name":"sitebooking.service-provider"}',
        'order' => 1,
      ));

      // Insert service-rating
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-rating',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'params' => '{"title":"Rating","name":"sitebooking.service-rating"}',
        'order' => 2,
      ));

      // Insert review-button
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.review-button',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'order' => 3,
      ));

      // Insert service-options
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-options',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'params' => '{"title":"Options","name":"sitebooking.service-options"}',
        'order' => 4,
      ));

      // Insert service timing
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-timing',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'params' => '{"title":"Opening Time","name":"sitebooking.service-timing"}',
        'order' => 5,
      ));

      // Insert provider-location
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.provider-location',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'order' => 6,
      ));


      // insert similar-services
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-suggestion',
        'params' => '{"title":"Similar Services","name":"sitebooking.service-suggestion"}',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'order' => 7,
      ));

      // Insert similar service-providers
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.service-provider-suggestion',
        'page_id' => $pageId,
        'parent_content_id' => $rightId,
        'params' => '{"title":"Similar Providers","name":"sitebooking.service-provider-suggestion"}',
        'order' => 8,
      ));
      
    }
  }

  protected function _addUserMyAppointmentPage()
  {
    $db = $this->getDb();

    // profile page
    $pageId = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'sitebooking_index_booked-services')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$pageId ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'sitebooking_index_booked-services',
        'displayname' => 'Service Booking - Booked Service Page',
        'title' => 'My Booked Services',
        'description' => 'This page lists a user\'s booked services.',
        'custom' => 0,
      ));
      $pageId = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
      ));
      $topId = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
      ));
      $mainId = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
      ));
      $topMiddleId = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
      ));
      $mainMiddleId = $db->lastInsertId();

      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 1,
      ));
      $mainRightId = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitebooking.browse-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
      ));

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
      ));
    }
  }

}
?>