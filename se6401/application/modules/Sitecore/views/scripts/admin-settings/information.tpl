<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    https://socialapps.tech/license/
 * @version    $Id: information.tpl 2010-11-18 9:40:21Z SocialApps.tech $
 * @author     SocialApps.tech
 */
?>
<h2>
  <?php echo $this->translate('SocialApps.tech Core Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<?php echo $this->content()->renderWidget('sitecore.seaocores-information') ?>