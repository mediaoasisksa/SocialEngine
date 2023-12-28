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
<script type="text/javascript">

    function dismiss(type) {
        var d = new Date();
        // Expire after 1 month
        d.setTime(d.getTime() + (86400 * 30));
        var expires = "expires=" + d.toGMTString();
        document.cookie = type + "=" + 1 + "; " + expires;
        $('dismissMobileBox').style.display = 'none';
    }

</script>

<?php
// Add iOS App Calling Condition
$callingURL = null;
if (!isset($_COOKIE['dismissiOSAppToOpen']) && (($this->user_agent == 'iphone') || ($this->user_agent == 'ipad'))):
    $getAppleResult = file_get_contents("https://itunes.apple.com/lookup?bundleId=" . $this->appBuilderParams['package_name']);
    if (isset($getAppleResult) && !empty($getAppleResult)) {
        $getDecodedAppleResponce = Zend_Json::decode($getAppleResult);
        $callingURL = $getDecodedAppleResponce['results'][0]['trackViewUrl'];
    }
    ?>
<div id="dismissMobileBox">
    <div class="seaocore-notice-icon">
                    <?php if(!empty($this->logo)): ?>
        <img src="<?php echo $this->logo; ?>" alt="Uploaded App Image" />
                    <?php else: ?>
        <img src="application/modules/Siteiosapp/externals/images/iOS-app-default-icon.png" alt="Uploaded App Image" />
                    <?php endif; ?>
    </div>            
    <div class="seaocore-notice-info">
                        <?php
                        echo $this->defaultMessages;
                        ?>
    </div>


    <a onclick="window.location.href = '<?php echo $callingURL; ?>'">
        <span class="seaocore-download">INSTALL</span>

    </a>

    <div class="dismiss-icon">
        <a onclick="dismiss('dismissAndroidAppToOpen');">
                            <?php echo $this->translate(''); ?>
        </a>
    </div>


</div>
<div style="clear:both;"></div>
    <?php
// @Todo: Add for Android
elseif (!isset($_COOKIE['dismissAndroidAppToOpen']) && ($this->user_agent == 'android')):
    $callingURL = "https://play.google.com/store/apps/details?id=" . $this->appBuilderParams['package_name'];
    ?>
<div id="dismissMobileBox">
    <div class="seaocore-notice-icon">
                    <?php if(!empty($this->logo)): ?>
        <img src="<?php echo $this->logo; ?>" alt="Uploaded App Image" />
                    <?php else: ?>
        <img src="application/modules/Siteandroidapp/externals/images/Android-app-default-icon.png" alt="Uploaded App Image" />
                    <?php endif; ?>
    </div>            
    <div class="seaocore-notice-info">
                        <?php
                        echo $this->defaultMessages;
                        ?>
    </div>


    <a onclick="window.location.href = '<?php echo $callingURL; ?>'">
        <span class="seaocore-download">INSTALL</span>

    </a>
    <div class="dismiss-icon">
        <a onclick="dismiss('dismissAndroidAppToOpen');">
                            <?php echo $this->translate(''); ?>
        </a>
    </div>

</div>
<div style="clear:both;"></div>
<?php endif; ?>
