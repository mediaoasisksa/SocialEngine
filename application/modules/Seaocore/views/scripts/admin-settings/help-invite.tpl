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

<script type="text/javascript">
var previous_tab = 'facebook';
  function show_seacorehelp(active_tab) { 
  	
  	if (active_tab != previous_tab) {
        scriptJquery('#' + previous_tab + '_help').removeClass ('active');
        scriptJquery('#' + active_tab + '_help').addClass ('active');
        scriptJquery('#' + active_tab + '_helpsteps').css('display', 'block');
        scriptJquery('#' + previous_tab + '_helpsteps').css('display', 'none');
        previous_tab =active_tab; 
    }
   
  }
</script>
These invite services will allow you to integrate some third party social media sites with your SocialEngine based website. Based on this integration, contacts are imported from these third party social media sites to your SocialEngine based community and your users can invite their friends from these third party social media sites to join your website. For example, users can send invitation to your Facebook friends to join the website.<br /><br />

<a href="javascript:void(0);" class="buttonlink"
style="background-image:url(./application/modules/Seaocore/externals/images/back.png);padding-left:23px;" onclick="javascript:history.go(-1)">
    <?php echo $this->translate("Back to Previous Page") ?></a>
    
<div class="tabs">
  <ul class="navigation">
    <li class='active' id="facebook_help">
        <a class="menu_seaocore_admin_main seaocore_admin_upgrade" href="javascript:void(0);" onclick= "show_seacorehelp ('facebook');">Facebook Help</a>
    </li>
    <li id="google_help">
        <a class="menu_seaocore_admin_main seaocore_admin_info" href="javascript:void(0);" onclick= "show_seacorehelp ('google');">Google Help</a>
    </li>
    <!-- <li id="yahoo_help">
        <a class="menu_seaocore_admin_main seaocore_admin_news" href="javascript:void(0);" onclick= "show_seacorehelp ('yahoo');">Yahoo Help</a>
    </li> -->
    <li id="windowlive_help">
        <a class="menu_seaocore_admin_main seaocore_admin_main_infotooltip" href="javascript:void(0);" onclick= "show_seacorehelp ('windowlive');">Windows Live Help</a>
    </li>
    
     <li id="twitter_help">
        <a class="menu_seaocore_admin_main seaocore_admin_main_infotooltip" href="javascript:void(0);" onclick= "show_seacorehelp ('twitter');">Twitter Help</a>
    </li>
    
    <li id="bitly_help">
        <a class="menu_seaocore_admin_main seaocore_admin_main_infotooltip" href="javascript:void(0);" onclick= "show_seacorehelp ('bitly');">Bitly Help</a>
    </li>    
  </ul></div>

<?php //SHOW FACEBOOK FAQ:

include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_configFacebook.tpl'; 
// include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_configYahoo.tpl';

include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_configWindowlive.tpl';
include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_configGoogle.tpl';
include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_configTwitter.tpl';
include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_configBitly.tpl';

?>
