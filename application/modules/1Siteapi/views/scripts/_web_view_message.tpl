<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Siteandroidapp
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _upgrade_messages.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    function dismiss_webview(modName) {
        document.cookie = modName + "_webview" + "=" + 1;
        $('dismiss_webview').style.display = 'none';
    }
</script>

<?php
$pluginTitle = Engine_Api::_()->getApi('core', 'siteapi')->webViewRestrictedModulesList();
if(empty($pluginTitle)) 
    return;

$moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
if (!isset($_COOKIE[$moduleName . '_webview'])):
    ?>
    <div id="dismiss_webview">
        <div class="seaocore-notice">
            <div class="seaocore-notice-icon">
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
            </div>
            <div style="float:right;">
                <button onclick="dismiss_webview('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
            </div>
            <div class="seaocore-notice-text ">
                <?php
                $titles = @implode(", ", $pluginTitle);
                $titles = '<span style="font-weight: bold;">' . $titles . '</span>';
                echo "<span style='font-weight:bold;'>WebView feature</span> brings in even those 3rd-party plugins and features in your app, that are not natively integrated with the app. Hence, all the main features of your website can be made available in your Mobile Apps. It also brings realtime updates to your app. For details, please <a href='https://www.socialengineaddons.com/page/enabling-socialengineaddons-3rd-party-plugins-ios-android-apps-webview' target='_blank'>read here</a>.";
//                echo "Our Mobile App’s are having In-App Browser functionality, which will enable you to view the non-integrated modules in Web View. So, if you want to use this functionality and using our '<a href='http://www.socialengineaddons.com/catalog/themes' target='_blank'>Themes</a>' then please disable these plugin: $titles";
                ?>
            </div>	
        </div>
    </div>
<?php endif; ?>