<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate('SocialEngine REST API Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
<div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
</div>
<?php endif; ?>

<?php if (!Engine_Api::_()->getApi('Core', 'siteapi')->isRootFileValid() && !is_writable(APPLICATION_PATH . "/index.php")): ?>
<div class="seaocore_tip">
    <span>
            <?php $url = $this->url(array('action' => 'edit-root-file'), 'admin_default', false); ?>                        
            <?php echo 'We are unable to modify your root file(index.php) automatically, it seems that only file owner can do the file changes. So, please <a href="' . $url . '" class="smoothbox">click here</a> to do the changes manually.' ?>
    </span>
</div>
<?php else: ?>

    <?php if (!Engine_Api::_()->getApi('Core', 'siteapi')->isRootFileValid() && !isset($this->displayPermissionError)): ?>
<div class="seaocore_tip">
    <span>
                <?php echo 'API calling is not working as you have not "Modified Root File" via below given setting, please configure it to start API calling for your website.' ?>
    </span>
</div>
    <?php endif; ?>

    <?php if (isset($this->displayPermissionError) && !empty($this->displayPermissionError)): ?>
<div class="seaocore_tip">
    <span>
                 <?php $url = $this->url(array('action' => 'edit-boot-file'), 'admin_default', false); ?>                        
            <?php echo 'We are unable to modify your boot file automatically, it seems that only file owner can do the file changes. So, please <a href="' . $url . '" class="smoothbox">click here</a> to do the changes manually.' ?>
    </span>
</div>
    <?php endif; ?>

 <?php if (isset($this->isextraCodeAvailable) && !empty($this->isextraCodeAvailable) && !isset($this->displayPermissionError)): ?>
<div class="seaocore_tip">
    <span>
        <?php $url = $this->url(array('action' => 'remove-extra-code'), 'admin_default', false); ?>     
                <?php echo 'There is some extra code in your root file(index.php) which may generate log errors. So, please <a href="' . $url . '" class="smoothbox">click here</a> to do the changes manually.' ?>
    </span>
</div>
    <?php endif; ?>

    <?php if ($this->backTo == 'android'): ?>
<div>
            <?php
            echo $this->htmlLink(array('module' => 'siteandroidapp', 'controller' => 'settings'), $this->translate('Back to Android Mobile Application'), array(
                'class' => 'buttonlink icon_siteapi_admin_back',
            ))
            ?>
</div>
<br />
    <?php endif; ?>

<div class="seaocore_settings_form">
    <a href="<?php echo $this->url(array('module' => 'siteapi', 'controller' => 'admin-settings', 'action' => 'help-create-api'), 'default', true) ?>"
       class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteapi/externals/images/help.gif);padding-left:23px;"><?php
               echo
               $this->translate("Guidelines for extending APIs to other modules")
               ?></a>
    <div class='settings' style="margin-top:15px;">
            <?php echo $this->form->render($this); ?>
    </div>
    <a herf="javascript:void(0)" name="siteapi_ssl_verification"></a>
</div>
<?php endif; ?>