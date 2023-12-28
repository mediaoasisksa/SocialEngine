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

<div class="seaocore_settings_form">
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">

    window.addEventListener('DOMContentLoaded', function () {
        cacheStatus();
        
        if(scriptJquery("#siteapi_caching_status-1") && scriptJquery("#siteapi_caching_status-1").is(':checked'))
            cacheLifetime();
    });

    function cacheStatus() {
        if (scriptJquery("#siteapi_caching_status-0") && scriptJquery("#siteapi_caching_status-0").is(':checked')) {
            scriptJquery('#siteapi_lifetime_status-wrapper').css( 'display', 'none' );
            scriptJquery('#siteapi_caching_lifetime-wrapper').css( 'display', 'none' );
        } else {
            scriptJquery('#siteapi_lifetime_status-wrapper').css( 'display', 'block' );
            scriptJquery('#siteapi_caching_lifetime-wrapper').css( 'display', 'block' );
        }
    }

    function cacheLifetime() {
        if (scriptJquery("#siteapi_lifetime_status-0") && scriptJquery("#siteapi_lifetime_status-0").is(':checked')) {
            scriptJquery('#siteapi_caching_lifetime-wrapper').css( 'display', 'none' );
        } else {
            scriptJquery('#siteapi_caching_lifetime-wrapper').css( 'display', 'block' );
        }
    }

</script>