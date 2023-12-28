<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upgrade.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('SocialEngineAddOns Core Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php 
  endif;
include_once APPLICATION_PATH . '/application/modules/Sitecore/views/scripts/_loginSEAO.tpl'; 
if( !empty($this->seaoDetailsSession) ):
  include_once APPLICATION_PATH . '/application/modules/Sitecore/views/scripts/_upgradePluginsNavigation.tpl';
  if($this->selectedMenuType == "all")
    echo $this->content()->renderWidget('sitecore.seaocores-upgrade');
  else if($this->selectedMenuType == "page")
    echo $this->content()->renderWidget('sitepage.extension-upgrade');
  else if($this->selectedMenuType == "business")
    echo $this->content()->renderWidget('sitebusiness.extension-upgrade');
  else if($this->selectedMenuType == "group")
    echo $this->content()->renderWidget('sitegroup.extension-upgrade');
  else if($this->selectedMenuType == "event")
    echo $this->content()->renderWidget('siteevent.extension-upgrade');
  else if($this->selectedMenuType == "review")
    echo $this->content()->renderWidget('sitereview.extension-upgrade');
  elseif($this->selectedMenuType == "themes")
    echo $this->content()->renderWidget('sitecore.seaocores-upgrade-themes');
  elseif($this->selectedMenuType == "disabled")
    echo $this->content()->renderWidget('sitecore.seaocores-upgrade', array('type' => "disabled"));
endif;
?>
<div id="seaocore_light" class="seaocore_downloading_white_content">
    <?php echo $this->translate('Downloading packages ! please wait '); ?>
    <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/loadings.gif" alt="" />
</div>
<div id="seaocore_fade" class="seaocore_downloading_black_overlay"></div>