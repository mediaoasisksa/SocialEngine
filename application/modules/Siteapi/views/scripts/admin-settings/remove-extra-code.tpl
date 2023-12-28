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
        <span style="font-weight: bold">Step 1:</span> <?php echo APPLICATION_PATH . '/index.php' ?>
    </p>
    <br />
    <p>
        <span style="font-weight: bold">Step 2:</span> Now find the below code:<br />
         <i><span style="font-weight: bold">$getRequestUri = htmlspecialchars($_SERVER['REQUEST_URI']);if(isset($getRequestUri) && !empty($getRequestUri) && strstr($getRequestUri, "api/rest"))  define('_ENGINE_R_TARG', 'siteapi.php');else  define('_ENGINE_R_TARG', 'index.php');</span></i>
    </p>
<br />
    <p>
        <span style="font-weight: bold">Step 3:</span> Remove the above code<br />
    </p>
<br />
    <p>
        <span style="font-weight: bold">Step 4:</span> You have successfully modified your Root File and removed extra code.
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