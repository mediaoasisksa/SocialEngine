<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: admin.tpl 10227 2014-05-16 22:43:27Z andres $
 * @author     John
 */
?>
<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ($this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
    <base href="<?php echo rtrim('//' . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />
    <?php $this->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0');?>
   <?php // ALLOW HOOKS INTO META?>
    <?php echo $this->hooks('onRenderLayoutAdmin', $this) ?>

    <?php // TITLE/META?>
    <?php
    $counter = (int) $this->layout()->counter;
    $staticBaseUrl = $this->layout()->staticBaseUrl;

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->headTitle()
        ->setSeparator(' - ');
    $pageTitleKey = strtoupper('pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
        . '-' . $request->getControllerName());
    $pageTitle = $this->translate($pageTitleKey);
    if ($pageTitle && $pageTitle != $pageTitleKey) {
        $this
            ->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
    }
    $this
        ->headTitle($this->translate("Control Panel"))
    ;
    $this->headMeta()
        ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
        ->appendHttpEquiv('Content-Language', $this->locale()->getLocale()->__toString());
    if ($this->subject() && $this->subject()->getIdentity()) {
        $this->headTitle($this->subject()->getTitle());
        $this->headMeta()->appendName('description', $this->subject()->getDescription());
        $this->headMeta()->appendName('keywords', $this->subject()->getKeywords());
    }

    // Get body identity
    if (isset($this->layout()->siteinfo['identity'])) {
        $identity = $this->layout()->siteinfo['identity'];
    } else {
        $identity = $request->getModuleName() . '-' .
            $request->getControllerName() . '-' .
            $request->getActionName();
    }
    ?>
    <?php echo $this->headTitle()->toString()."\n" ?>
    <?php echo $this->headMeta()->toString()."\n" ?>

    <?php // LINK/STYLES?>
    <?php $favicon = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.site.favicon',false); ?>
    <?php
    $this->headLink(array(
        'rel' => 'shortcut icon',
        'href' => ($favicon ? Engine_Api::_()->core()->getFileUrl($favicon) : $staticBaseUrl . ( isset($this->layout()->favicon) ? $this->layout()->favicon : 'favicon.ico')),
        'type' => 'image/x-icon'),
        'PREPEND');
    if (APPLICATION_ENV != 'development') {
        $this->headLink()
            ->prependStylesheet('application/css.php?request=application/modules/Core/externals/styles/admin/main.css');
    } else {
        $this->headLink()
            ->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/css.php?request=application/modules/Core/externals/styles/admin/main.css');
    }
    // Process
    foreach ($this->headLink()->getContainer() as $dat) {
        if (!empty($dat->href)) {
            if (false === strpos($dat->href, '?')) {
                $dat->href .= '?c=' . $counter;
            } else {
                $dat->href .= '&c=' . $counter;
            }
        }
    }
    ?>
    <?php echo $this->headLink()->toString()."\n" ?>
    <?php echo $this->headStyle()->toString()."\n" ?>

    <?php // TRANSLATE?>
    <?php $this->headScript()->prependScript($this->headTranslate()->toString()) ?>

    <?php // SCRIPTS?>
    <script type="text/javascript">
        <?php echo $this->headScript()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>

        Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()); ?>');
        
        en4.orientation = '<?php echo $orientation ?>';
        en4.core.environment = '<?php echo APPLICATION_ENV ?>';
        en4.core.language.setLocale('<?php echo $this->locale()->getLocale()->__toString() ?>');
        en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
        en4.core.staticBaseUrl = '<?php echo $this->escape($staticBaseUrl) ?>';
        en4.core.loader = scriptJquery.crtEle('img', {src: en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'});
        <?php if ($this->subject()): ?>
        en4.core.subject = {
            type : '<?php echo $this->subject()->getType(); ?>',
            id : <?php echo $this->subject()->getIdentity(); ?>,
            guid : '<?php echo $this->subject()->getGuid(); ?>'
        };
        <?php endif; ?>
        <?php if ($this->viewer()->getIdentity()): ?>
        en4.user.viewer = {
            type : '<?php echo $this->viewer()->getType(); ?>',
            id : <?php echo $this->viewer()->getIdentity(); ?>,
            guid : '<?php echo $this->viewer()->getGuid(); ?>'
        };
        <?php endif; ?>
        if( <?php echo(Zend_Controller_Front::getInstance()->getRequest()->getParam('ajax', false) ? 'true' : 'false') ?> ) {
            en4.core.dloader.attach();
        }
        <?php echo $this->headScript()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
        var dateFormatCalendar = "<?php echo Engine_Api::_()->core()->dateFormatCalendar(); ?>";
    </script>
    <link rel="stylesheet" href="<?php echo $staticBaseUrl . 'externals/font-awesome/css/all.min.css'; ?>">
    <link href="<?php echo $staticBaseUrl . 'externals/bootstrap/css/bootstrap.css'; ?>" media="screen" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo $staticBaseUrl . 'externals/jQuery/jquery-ui.css'; ?>">
    <script type="text/javascript" src="<?php echo $staticBaseUrl . 'externals/jQuery/jquery.min.js' ?>"></script>
    <?php 
    $this->headScript()
        ->prependFile($staticBaseUrl . 'externals/smoothbox/smoothbox4.js')
        ->prependFile($staticBaseUrl . 'externals/mdetect/mdetect.js')
        ->prependFile($staticBaseUrl . 'application/modules/User/externals/scripts/core.js')
        ->prependFile($staticBaseUrl . 'application/modules/Core/externals/scripts/core.js')
        ->prependFile($staticBaseUrl . 'externals/bootstrap/js/bootstrap.js');
    if($request->getControllerName() == 'admin-content') {
      $this->headScript()->prependFile($staticBaseUrl . 'application/modules/Core/externals/scripts/admin/layoutchoo.js')
          ->prependFile($staticBaseUrl . 'application/modules/Core/externals/scripts/admin/layout.js')
          ->prependFile($staticBaseUrl . 'application/modules/Core/externals/scripts/admin/adminlayout.js');
    }
    
    $this->headScript()->prependFile($staticBaseUrl . 'externals/jQuery/core.js')
        ->prependFile($staticBaseUrl . 'externals/jQuery/jquery-ui.js');

    // Process
    foreach ($this->headScript()->getContainer() as $dat) {
        if (!empty($dat->attributes['src'])) {
            if (false === strpos($dat->attributes['src'], '?')) {
                $dat->attributes['src'] .= '?c=' . $counter;
            } else {
                $dat->attributes['src'] .= '&c=' . $counter;
            }
        }
    }
    ?>
    <?php echo $this->headScript()->toString()."\n" ?>
    <script type="text/javascript">
      var $ = scriptJquery;
    </script>
    <script type="text/javascript">
        //<![CDATA[
        var changeEnvironmentMode = function(mode, btn) {
            btn = scriptJquery(btn);
            if( btn ) {
                btn.attr('class', '');
            }
            if(scriptJquery('div.admin_home_environment button') ) {
                scriptJquery('div.admin_home_environment button').attr('class', 'button_disabled');
            }
            if(scriptJquery('div.admin_home_environment_description')) {
                scriptJquery('div.admin_home_environment_description').attr('text', 'Changing mode - please wait...');
            }
            scriptJquery.ajax({
                url: '<?php echo $this->url(array('action'=>'change-environment-mode'), 'admin_default', true) ?>?'+'format=json&environment_mode='+mode,
                method: 'post',
                success: function(responseJSON){
                    if ($type(responseJSON) == 'object') {
                        if (responseJSON.success || !$type(responseJSON.error))
                            window.location.href = window.location.href;
                        else
                            alert(responseJSON.error);
                    } else
                        alert('An unknown error occurred; changes have not been saved.');
                }
            });
        }
        var post_max_size = '<?php echo Engine_Api::_()->core()->convertPHPSizeToBytes(ini_get('upload_max_filesize')); ?>';
        var max_photo_upload_limit = "<?php echo Engine_Api::_()->authorization()->getPermission($this->viewer(), 'user', 'maxphotolimit'); ?>";
        var photo_upload_text = "<?php echo $this->translate('Max upload of %s allowed.', Engine_Api::_()->authorization()->getPermission($this->viewer(), 'user', 'maxphotolimit')); ?>";
        //]]>
    </script>
</head>
<?php $menuType = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.menutype', 'vertical'); ?>
<body id="global_page_<?php echo $identity ?>" class="menu_<?php echo $menuType; ?>">

<?php if ('development' == APPLICATION_ENV): ?>
    <div class="development_mode_warning">
        Your site is currently in development mode (which may decrease performance).
        When you've finished changing your settings, remember to
        <a href="javascript:void(0)" onClick="changeEnvironmentMode('production', this);this.blur();">return to production mode</a>.
    </div>
<?php endif ?>

<div class="admin_panel_wrapper">
<!-- TOP HEADER BAR -->
<div id='global_header_wrapper'>
	<div id='global_header'>
    <?php if($menuType == 'vertical') { ?>
      <div class="global_header_left">
        <div class="toggle_cross_button"> <i class="fas fa-times"></i> </div>
        <?php echo $this->content()->renderWidget('core.admin-menu-logo') ?>
        <?php echo $this->content()->renderWidget('core.admin-menu-main') ?>
     </div>
    <?php } ?>
	</div>
</div>

<!-- BEGIN CONTENT -->
<div id='global_content_wrapper'>
 <div class="global_header_menu_mini">
   <?php echo $this->content()->renderWidget('core.admin-menu-mini') ?>
 </div>
 <div id='global_content'>
   <?php echo $this->layout()->content ?>
  </div>
</div>
  </div>
 </body>
</html>
