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

    window.addEvent('domready', function () {
        cacheStatus();
        
        if($("siteapi_caching_status-1") && $("siteapi_caching_status-1").checked)
            cacheLifetime();
    });

    function cacheStatus() {
        if ($("siteapi_caching_status-0") && $("siteapi_caching_status-0").checked) {
            $('siteapi_lifetime_status-wrapper').style.display = 'none';
            $('siteapi_caching_lifetime-wrapper').style.display = 'none';
        } else {
            $('siteapi_lifetime_status-wrapper').style.display = 'block';
            $('siteapi_caching_lifetime-wrapper').style.display = 'block';
        }
    }

    function cacheLifetime() {
        if ($("siteapi_lifetime_status-0") && $("siteapi_lifetime_status-0").checked) {
            $('siteapi_caching_lifetime-wrapper').style.display = 'none';
        } else {
            $('siteapi_caching_lifetime-wrapper').style.display = 'block';
        }
    }

</script>