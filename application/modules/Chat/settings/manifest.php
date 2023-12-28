<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 10194 2014-05-01 17:41:40Z mfeineman $
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'chat',
    'version' => '6.2.0',
    'revision' => '$Revision: 10194 $',
    'path' => 'application/modules/Chat',
    'repository' => 'socialengine.com',
    'title' => 'Chat',
    'description' => 'Chat',
    'author' => 'Webligo Developments',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '5.0.0',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Chat/settings/install.php',
      'class' => 'Chat_Installer',
    ),
    'directories' => array(
      'application/modules/Chat',
    ),
    'files' => array(
      'application/languages/en/chat.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Chat_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutAdminDefault',
      'resource' => 'Chat_Plugin_Core',
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Chat_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(

  ),
  // Routes --------------------------------------------------------------------
); ?>
