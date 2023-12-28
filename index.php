<?php
/**
 * @package     Engine_Core
 * @version     $Id: index.php 9764 2012-08-17 00:04:31Z matthew $
 * @copyright   Copyright (c) 2008 Webligo Developments
 * @license     http://www.socialengine.com/license/
 */
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
// Check version
if (version_compare(phpversion(), '5.2.11', '<')) {
    printf('PHP 5.2.11 is required, you have %s', phpversion());
    exit(1);
}

// Constants
define('_ENGINE_R_BASE', dirname($_SERVER['SCRIPT_NAME']));
define('_ENGINE_R_FILE', $_SERVER['SCRIPT_NAME']);
define('_ENGINE_R_REL', 'application');
$getRequestUri = htmlspecialchars($_SERVER['REQUEST_URI']);if(isset($getRequestUri) && !empty($getRequestUri) && strstr($getRequestUri, "api/rest"))  define('_ENGINE_R_TARG', 'siteapi.php');else  define('_ENGINE_R_TARG', 'index.php');
// Main
include dirname(__FILE__)
    . DIRECTORY_SEPARATOR
    . _ENGINE_R_REL . DIRECTORY_SEPARATOR
    . _ENGINE_R_TARG;
