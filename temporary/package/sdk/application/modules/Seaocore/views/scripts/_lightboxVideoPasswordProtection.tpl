<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _lightboxPasswordProtection.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<form method="post" action="" class="global_form" enctype="application/x-www-form-urlencoded" id="sitevideo_check_password_protection">
    <div>
        <div>
            <h3><?php echo $this->translate("Private Video"); ?></h3>
            <p class="form-description"><?php echo $this->translate("This is password protected video."); ?></p>
            <div class="form-elements">
                <div class="form-wrapper" id="password-wrapper"><div class="form-label" id="password-label">
                        <label class="required" for="password"><?php echo $this->translate("Password"); ?></label></div>
                    <div class="form-element" id="password-element">
                        <input type="password" value="" id="password" name="password">
                        <p class="description"><?php echo $this->translate("To view this video, please provide the correct password."); ?></p>
                    </div>
                </div>
                <div class="form-wrapper" id="submitForm-wrapper"><div class="form-label" id="submitForm-label">&nbsp;</div>
                    <div class="form-element" id="submitForm-element">
                        <button onclick="checkPasswordProtection($('sitevideo_check_password_protection'));
                                return false" type="submit" id="submitForm" name="submitForm"><?php echo $this->translate("Access"); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>