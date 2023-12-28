<?php 
$routeStartP = "courses";
$routeStartS = "course";
$module=null;$controller=null;$action=null;$getURL = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
  $getURL = $request->getRequestUri();
}
if (empty($request) || !($module == "default" && ( strpos( $getURL, '/install') !== false))) {
  $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.UrlP', "courses");
  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.UrlS', "course");
}

return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sitecourse',
    'version' => '6.3.0',
    'path' => 'application/modules/Sitecourse',
    'title' => 'Course Builder / Learning Management Plugin',
    'description' => 'Course Builder / Learning Management Plugin',
    'author' => '<a href="http://www.socialapps.tech" style="text-decoration:underline;" target="_blank">SocialApps.tech</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Sitecourse/settings/install.php',
      'class' => 'Sitecourse_Installer',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Sitecourse',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/sitecourse.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Sitecourse_Plugin_Core',
    ),
  ),
   'composer' => array(
      'event' => array(
          'script' => array('_composeSitecourse.tpl', 'sitecourse'),
      ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'sitecourse_lesson',
    'sitecourse_course',
    'sitecourse_category',
    'sitecourse_topic',
    'sitecourse_video',
    'sitecourse_lesson',
    'sitecourse_review',
    'sitecourse_favourite',
    'sitecourse_announcement',
    'sitecourse_report',
    'sitecourse_order',
    'sitecourse_buyerdetail',
    'sitecourse_ordercourse',
    'sitecourse_gateway',
    'sitecourse_transaction',
    'sitecourse_reviewlike'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'sitecourse_general' => array(
      'route' => $routeStartP.'/:action/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|create|subcategory|validateurl|manage|favourites|ajax-load|tagscloud)',

      ),
    ),
    'sitecourse_specific' => array(
      'route' => $routeStartS.'/:action/:course_id/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'index',
        'action' => 'edit',
      ),
      'reqs' => array(
        'action' => '(edit|publish|course-delete|messageowner|togglefavourite|report|course-details)',
      ),
    ),
    'sitecourse_entry_profile' => array(
      'route' => $routeStartS.'/:action/:url/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'index',
        'action' => 'profile',
      ),
      'reqs' => array(
        'action' => '(profile)',
      ),
    ),
    'sitecourse_dashboard' => array(
      'route' => $routeStartP.'/dashboard/:action/:course_id',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'dashboard',
        'action' => 'topics',
      ),
      'reqs' => array(
        'action' => '(index|targetstudents|topics|add-topic|course-overview|orderchange|intro-video|getlessons|getvideo|lesson|announcements|create-announcement|enrolled-members|course-picture|course-intro-video|transactions)',
      ),
    ),
    'sitecourse_topic_specific' => array(
      'route' => $routeStartP.'/dashboard/:action/:topic_id/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'dashboard',
        'action' => 'edit-topic',
      ),
      'reqs' => array(
        'action' => '(add-lesson-video-invite|edit-topic|delete-topic|add-lesson|add-doclesson)',
      ),

    ),
    'sitecourse_lesson_specific' => array(
      'route' => $routeStartP.'/dashboard/:action/:lesson_id/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'dashboard',
        'action' => 'delete-lesson',
      ),
      'reqs' => array(
        'action' => '(delete-lesson)',
      ),

    ),
    'sitecourse_doc_specific' => array(
      'route' => $routeStartP.'/doc/:action/:topic_id',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'doc',
        'action' => 'add-doclesson',
      ),
      'reqs' => array(
        'action' => '(add-doclesson|add-lesson-video-invite)',
      ),

    ),
    'sitecourse_video_general' => array(
      'route' => $routeStartP . '/video/:action/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'video',
        'action' => 'index'
      ),
      'reqs' => array(
        'action' => '(index|create|validation)',
      ),
    ),
    'sitecourse_general_category' => array(
      'route' => $routeStartP.'/:category_id/:categoryname/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'category_id' => '\d+'
      ),
    ),
    'sitecourse_general_subcategory' => array(
      'route' => $routeStartP.'/:category_id/:categoryname/:subcategory_id/:subcategoryname/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'category_id' => '\d+',
        'subcategory_id' => '\d+'

      ),
    ),
    'sitecourse_review' => array(
      'route' => $routeStartP.'/review/:action/:course_id',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'review',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|add-review|check-previous-like|like|delete)',
      ),
    ),
    'sitecourse_review_specific' => array(
      'route' => $routeStartS.'/:action/:review_id/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'review',
        'action' => 'delete-review',
      ),
      'reqs' => array(
        'action' => '(delete-review)',
      ),
    ),
    'sitecourse_learning' => array(
      'route' => $routeStartS.'/learning/:action/:course_id',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'learning',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|toggle-topiccomplete|preview-certificate|display)',
      ),
    ),
    'sitecourse_learning_specific' => array(
      'route' => $routeStartS.'/learning/:action/:course_id/:lesson_id',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'learning',
        'action' => 'display',
      ),
      'reqs' => array(
        'action' => '(download|display)',
      ),
    ),
    'sitecourse_order' => array(
      'route' => $routeStartP.'/order/:action/:course_id/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'order',
        'action' => 'buyer-details',
      ),
      'reqs' => array(
        'action' => '(buyer-details|checkout|success|payment-info|set-course-gateway-info|payment)',
      ),
    ),
    'sitecourse_announcement' => array(
      'route' => $routeStartP.'/review/:action/:announcement_id',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'dashboard',
        'action' => 'edit-announcement',
      ),
      'reqs' => array(
        'action' => '(edit-announcement|delete-announcement)',
      ),
    ),
    'sitecourse_order' => array(
      'route' => $routeStartP.'/order/:action/:course_id/*',
      'defaults' => array(
        'module' => 'sitecourse',
        'controller' => 'order',
        'action' => 'buyer-details',
      ),
      'reqs' => array(
        'action' => '(buyer-details|checkout|success|payment-info|set-course-gateway-info|payment)',
      ),
    ),
  ),
); 
?>

