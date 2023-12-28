<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    delete-token.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
    <p>
        <span style="font-weight: bold">Step 1:</span> <?php echo APPLICATION_PATH . '/boot' ?>
    </p>
    <br />
    <p>
        <span style="font-weight: bold">Step 2:</span> Now copy Siteapi.php file from location:<br />
        <i><span style="font-weight: bold"><?php echo APPLICATION_PATH . '/application/modules/Siteapi/settings/Siteapi.php' ?></span></i>
    </p>
<br />
    <p>
        <span style="font-weight: bold">Step 3:</span> Place the above inside boot folder:<br />
        <i><span style="font-weight: bold"><?php echo APPLICATION_PATH . '/boot' ?></span></i>
    </p>
<br />
    <p>
        <span style="font-weight: bold">Step 4:</span> You have successfully modified your Boot File, API calling is now enabled and started for your website.
    </p>
    <br />
    <div style="float: right">
        <button onclick='javascript:parent.Smoothbox.close()'>Cancel</button>
    </div>
</div>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>