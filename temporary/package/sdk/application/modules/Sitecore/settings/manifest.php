<?php

$filePath = str_replace('Sitecore', 'Seaocore', (__FILE__));
if( file_exists($filePath) ) {
  $seaocoreData = include $filePath;
} else {
  $seaocoreData = array(
    'package' => array(
      'version' => '4.10.3',
      'directories' => array(),
      'files' => array(),
    )
  );
}
return array(
  'package' => array(
    'type' => 'module',
    'name' => 'sitecore',
    'version' => $seaocoreData['package']['version'],
    'path' => 'application/modules/Sitecore',
    'title' => 'SocialEngineAddOns Core Plugin',
    'description' => 'SEAddOns Core Plugin',
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'callback' => array(
      'path' => 'application/modules/Sitecore/settings/install.php',
      'class' => 'Sitecore_Installer',
      'priority' => 2000,
    ),
    'actions' => array(
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => array_merge(array(
      0 => 'application/modules/Sitecore',
//      1 => 'externals/tinymce/plugins/jbimages',
//      2 => 'externals/font-awesome/',
      ), $seaocoreData['package']['directories']),
    'files' => array_merge(array(
      0 => 'application/languages/en/sitecore.csv',
      1 => 'application/libraries/PEAR/Services/Twitter.php',
//      2 => 'application/modules/Core/View/Helper/ItemPhoto.php',
//      3 => 'application/modules/Core/View/Helper/ItemBackgroundPhoto.php',
      4 => 'application/modules/Storage/Service/Abstract.php',
      ), $seaocoreData['package']['files']),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      // 'event' => 'addActivity',
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Sitecore_Plugin_Core'
    ),
    array(
      'event' => 'onRenderLayoutDefaultSimple',
      'resource' => 'Sitecore_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutMobileDefault',
      'resource' => 'Sitecore_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutMobileDefaultSimple',
      'resource' => 'Sitecore_Plugin_Core',
    ),
  ),
);
?>