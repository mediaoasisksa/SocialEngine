<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formsocialkeys.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php  
  $seaocoreInviteLink = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'help-invite'), 'admin_default', true); 
  $mapGuide = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map-guidelines'), 'admin_default', true);
?>  

<div class="form-sub-heading-main"> Major Setup </div> 

<div id="seaocore_google_map_key-wrapper" class="form-wrapper border0">
  <div id="seaocore_google_map_key-label" class="form-label">
    <label for="seaocore_google_map_key">Google Places API Key
      <a href="<?php echo $mapGuide; ?>" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif" target = "_blank" ></a>
    </label>
  </div>
  <div id="seaocore_google_map_key-element" class="form-element">
    <p class="description" >
      Google Places API Key for your website.
    </p>
    <input name="seaocore_google_map_key" id="seaocore_google_map_key" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key'); ?>" type="text">
  </div>
</div> 

<div class="border_top form-sub-heading"></div> 

<div id="video_youtube_apikey-wrapper" class="form-wrapper border0">
  <div id="video_youtube_apikey-label" class="form-label">
    <label for="video_youtube_apikey">YouTube API Key</label>
  </div>
  <div id="video_youtube_apikey-element" class="form-element">
    <p class="description">
      While posting videos on your site, users can choose YouTube as a source. This requires a valid YouTube API key.<br>To learn how to create that key with correct permissions, read our
      <a href="http://support.socialengine.com/php/customer/portal/articles/2018371-create-your-youtube-api-key" target="_blank" >KB Article</a>
    </p>
    <input name="video_youtube_apikey" id="video_youtube_apikey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey'); ?>" type="text">
  </div>
</div> 

<div class="border_top form-sub-heading"> 
  <div>Facebook Key Settings</div>
  <div><a href="<?php echo $seaocoreInviteLink; ?>" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
</div>

<div id="core_facebook_appid-wrapper" class="form-wrapper border0">
  <div id="core_facebook_appid-label" class="form-label">
    <label for="core_facebook_appid">App ID</label>
  </div>
  <div id="core_facebook_appid-element" class="form-element">
    <input name="core_facebook_appid" id="core_facebook_appid" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook.appid'); ?>" type="text">
  </div>
</div>
 

<div id="core_facebook_secret-wrapper" class="form-wrapper border0">
  <div id="core_facebook_secret-label" class="form-label">
    <label for="core_facebook_secret">App Secret</label>
  </div>
  <div id="core_facebook_secret-element" class="form-element">
    <p class="description" >
      This is a 36 character string of letters and numbers ' . 
              'provided by Facebook when you create an Application in your account.
    </p>
    <input name="core_facebook_secret" id="core_facebook_secret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook.secret'); ?>" type="text"> 
  </div>
</div> 


<div class="border_top form-sub-heading"> 
  <div>Twitter Key Settings</div>
  <div><a href="<?php echo $seaocoreInviteLink; ?>" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
</div>

<div id="core_twitter_key-wrapper" class="form-wrapper border0">
  <div id="core_twitter_key-label" class="form-label">
    <label for="core_twitter_key">App Consumer Key</label>
  </div>
  <div id="core_twitter_key-element" class="form-element">
    <input name="core_twitter_key" id="core_twitter_key" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter.key'); ?>" type="text">
  </div>
</div>
 

<div id="core_twitter_secret-wrapper" class="form-wrapper border0">
  <div id="core_twitter_secret-label" class="form-label">
    <label for="core_twitter_secret">App Consumer Secret</label>
  </div>
  <div id="core_twitter_secret-element" class="form-element"> 
    <input name="core_twitter_secret" id="core_twitter_secret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter.secret'); ?>" type="text"> 
  </div>
</div>  

<div class="border_top form-sub-heading"> 
  <div>ReCaptcha Key Settings</div> 
</div>

<div id="recaptchapublic-wrapper" class="form-wrapper">
  <div id="recaptchapublic-label" class="form-label">
    <label for="recaptchapublic" class="optional"> Public Key</label>
  </div>
  <div id="recaptchapublic-element" class="form-element">
    <p class="description">You can obtain API credentials at: <a href="https://www.google.com/recaptcha">https://www.google.com/recaptcha</a></p>
    <input type="text" name="recaptchapublic" id="recaptchapublic" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.spam.recaptchapublic'); ?>">
  </div>
</div>
<div id="recaptchaprivate-wrapper" class="form-wrapper">
  <div id="recaptchaprivate-label" class="form-label">
    <label for="recaptchaprivate" class="optional"> Private Key</label>
  </div>
  <div id="recaptchaprivate-element" class="form-element">
    <p class="description">You can obtain API credentials at: <a href="https://www.google.com/recaptcha">https://www.google.com/recaptcha</a></p>
    <input type="text" name="recaptchaprivate" id="recaptchaprivate" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.spam.recaptchaprivate'); ?>">
  </div>
</div>


<div class="form-sub-heading-main"> Minor Setup </div>  

<?php  if(in_array('sitelogin', $this->installedModules)):?>

  <div class=" form-sub-heading"> 
    <div>Google Key Settings</div>
    <div><a href="<?php echo $seaocoreInviteLink; ?>" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
  </div> 

  <div id="google_clientId-wrapper" class="form-wrapper">
    <div id="google_clientId-label" class="form-label">
      <label for="google_clientId" class="optional"> Client ID</label>
    </div>
    <div id="google_clientId-element" class="form-element">
      <p class="description">Please put the client id provided by Google when you have created a project.</p>
      <input type="text" name="google_clientId" id="google_clientId" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.google.clientId'); ?>">
    </div>
  </div>
  <div id="google_clientSecret-wrapper" class="form-wrapper">
    <div id="google_clientSecret-label" class="form-label">
      <label for="google_clientSecret" class="optional"> Client Secret Key</label>
    </div>
    <div id="google_clientSecret-element" class="form-element">
      <p class="description">This is a 36 character string of letters and numbers provided by Google when you have created a project.</p>
      <input type="text" name="google_clientSecret" id="google_clientSecret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.google.clientSecret'); ?>">
    </div>
  </div>
  
<?php endif; ?>



<div class="border_top form-sub-heading"> 
  <div>Yahoo Key Settings</div>
  <div><a href="<?php echo $seaocoreInviteLink; ?>" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
</div>

<div id="yahoo_clientId-wrapper" class="form-wrapper">
  <div id="yahoo_clientId-label" class="form-label">
    <label for="yahoo_clientId" class="optional"> Client ID</label>
  </div>
  <div id="yahoo_clientId-element" class="form-element">
    <p class="description">Please put the client id provided by Yahoo when you have created an application.</p>
    <input type="text" name="yahoo_clientId" id="yahoo_clientId" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.apikey'); ?>">
  </div>
</div>
<div id="yahoo_clientSecret-wrapper" class="form-wrapper">
  <div id="yahoo_clientSecret-label" class="form-label">
    <label for="yahoo_clientSecret" class="optional"> Client Secret Key</label>
  </div>
  <div id="yahoo_clientSecret-element" class="form-element">
    <p class="description">This is a string of letters and numbers provided by Yahoo when you have created an application.</p>
    <input type="text" name="yahoo_clientSecret" id="yahoo_clientSecret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.secretkey'); ?>">
  </div>
</div>

<div class="border_top form-sub-heading"> 
  <div>LinkedIn Key Settings</div>
  <div><a href="<?php echo $seaocoreInviteLink; ?>" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
</div>

<div id="linkedIn_clientId-wrapper" class="form-wrapper">
  <div id="linkedIn_clientId-label" class="form-label">
    <label for="linkedIn_clientId" class="optional"> Client ID</label>
  </div>
  <div id="linkedIn_clientId-element" class="form-element">
    <p class="description">Please put the client id provided by LinkedIn when you have created a application.</p>
    <input type="text" name="linkedIn_clientId" id="linkedIn_clientId" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey'); ?>">
  </div>
</div>
<div id="linkedIn_clientSecret-wrapper" class="form-wrapper">
  <div id="linkedIn_clientSecret-label" class="form-label">
    <label for="linkedIn_clientSecret" class="optional"> Client Secret Key</label>
  </div>
  <div id="linkedIn_clientSecret-element" class="form-element">
    <p class="description">This is a character string of letters and numbers provided by LinkedIn when you have created a application.</p>
    <input type="text" name="linkedIn_clientSecret" id="linkedIn_clientSecret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey'); ?>">
  </div>
</div>  

<div class="border_top form-sub-heading"> 
  <div>Instagram Key Settings</div>
  <div><a href="https://www.instagram.com/developer/" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
</div>

<div id="instagram_clientId-wrapper" class="form-wrapper">
  <div id="instagram_clientId-label" class="form-label">
    <label for="instagram_clientId" class="optional"> Client ID</label>
  </div>
  <div id="instagram_clientId-element" class="form-element">
    <p class="description">Please put the client id provided by Instagram when you have created an application.</p>
    <input type="text" name="instagram_clientId" id="instagram_clientId" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('instagram.apikey'); ?>">
  </div>
</div>
<div id="instagram_clientSecret-wrapper" class="form-wrapper">
  <div id="instagram_clientSecret-label" class="form-label">
    <label for="instagram_clientSecret" class="optional"> Client Secret Key</label>
  </div>
  <div id="instagram_clientSecret-element" class="form-element"><p class="description">This is a string of letters and numbers provided by Instagram when you have created an application.</p>
    <input type="text" name="instagram_clientSecret" id="instagram_clientSecret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('instagram.secretkey'); ?>">
  </div>
</div>

<div class="border_top form-sub-heading"> 
  <div>Windowlive Key Settings</div>
  <div><a href="<?php echo $seaocoreInviteLink; ?>" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
</div>

<div id="windowlive_apikey-wrapper" class="form-wrapper">
  <div id="windowlive_apikey-label" class="form-label">
    <label for="windowlive_apikey" class="optional"> Appid</label>
  </div>
  <div id="windowlive_apikey-element" class="form-element">
    <input type="text" name="windowlive_apikey" id="windowlive_apikey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.apikey'); ?>">
  </div>
</div>
<div id="windowlive_secretkey-wrapper" class="form-wrapper">
  <div id="windowlive_secretkey-label" class="form-label">
    <label for="windowlive_secretkey" class="optional"> Secret Key</label>
  </div>
  <div id="windowlive_secretkey-element" class="form-element">
    <input type="text" name="windowlive_secretkey" id="windowlive_secretkey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.secretkey'); ?>">
  </div>
</div>
<div id="windowlive_policyurl-wrapper" class="form-wrapper">
  <div id="windowlive_policyurl-label" class="form-label">
    <label for="windowlive_policyurl" class="optional"> Policyurl Url</label>
  </div>
  <div id="windowlive_policyurl-element" class="form-element">
    <input type="text" name="windowlive_policyurl" id="windowlive_policyurl" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.policyurl'); ?>">
  </div>
</div>  


<?php  if(in_array('sitelogin', $this->installedModules)):?>
  
  <div class="border_top form-sub-heading"> 
    <div>Flickr Key Settings</div>
    <div><a href="https://www.flickr.com/services/developer/api/" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
  </div>

  <div id="flickr_clientId-wrapper" class="form-wrapper">
    <div id="flickr_clientId-label" class="form-label"><label for="flickr_clientId" class="optional">App ID</label></div>
    <div id="flickr_clientId-element" class="form-element">
      <p class="description">Please put the app id provided by Flickr when you have created an application.</p>
      <input type="text" name="flickr_clientId" id="flickr_clientId" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.flickr.clientId'); ?>">
    </div>
  </div>
  <div id="flickr_Secret-wrapper" class="form-wrapper">
    <div id="flickr_Secret-label" class="form-label">
      <label for="flickr_Secret" class="optional">App Secret Key</label>
    </div>
    <div id="flickr_Secret-element" class="form-element">
      <p class="description">This is a string of letters and numbers provided by Flickr when you have created an application.</p> 
      <input type="text" name="flickr_Secret" id="flickr_Secret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.flickr.clientSecret'); ?>">
    </div>
  </div> 

  <div class="border_top form-sub-heading"> 
    <div>Vkontakte Key Settings</div>
    <div><a href="https://vk.com/dev" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
  </div>

  <div id="vk_clientId-wrapper" class="form-wrapper">
    <div id="vk_clientId-label" class="form-label">
      <label for="vk_clientId" class="optional"> Application ID</label>
    </div>
    <div id="vk_clientId-element" class="form-element">
      <p class="description">Please put the application id provided by Vkontakte when you have created an application.</p>
      <input type="text" name="vk_clientId" id="vk_clientId" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.vk.clientId'); ?>">
    </div>
  </div>
  <div id="vk_clientSecret-wrapper" class="form-wrapper">
    <div id="vk_clientSecret-label" class="form-label">
      <label for="vk_clientSecret" class="optional"> Secure Key</label>
    </div>
    <div id="vk_clientSecret-element" class="form-element">
      <p class="description">This is a string of letters and numbers provided by Vkontakte when you have created an application.</p>
      <input type="text" name="vk_clientSecret" id="vk_clientSecret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.vk.clientSecret'); ?>">
    </div>
  </div> 
 
  <div class="border_top form-sub-heading"> 
    <div>Pinterest Key Settings</div>
    <div><a href="https://developers.pinterest.com/" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
  </div>

  <div id="pinterest_clientId-wrapper" class="form-wrapper">
    <div id="pinterest_clientId-label" class="form-label">
      <label for="pinterest_clientId" class="optional"> App ID</label>
    </div>
    <div id="pinterest_clientId-element" class="form-element">
      <p class="description">Please put the app id provided by Pinterest when you have created an application.</p>
      <input type="text" name="pinterest_clientId" id="pinterest_clientId" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.pinterest.clientId'); ?>">
    </div>
  </div>

  <div id="pinterest_clientSecret-wrapper" class="form-wrapper">
    <div id="pinterest_clientSecret-label" class="form-label"><label for="pinterest_clientSecret" class="optional"> App Secret Key</label>
    </div>
    <div id="pinterest_clientSecret-element" class="form-element">
      <p class="description">This is a string of letters and numbers provided by Pinterest when you have created an application.</p>
      <input type="text" name="pinterest_clientSecret" id="pinterest_clientSecret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.pinterest.clientSecret'); ?>">
    </div>
  </div> 

  <div class="border_top form-sub-heading"> 
    <div>Outlook Key Settings</div>
    <div><a href="https://apps.dev.microsoft.com/#/appList" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
  </div>

  <div id="outlook_clientId-wrapper" class="form-wrapper">
    <div id="outlook_clientId-label" class="form-label">
      <label for="outlook_clientId" class="optional"> Application ID</label>
    </div>
    <div id="outlook_clientId-element" class="form-element">
      <p class="description">Please put the application id provided by Outlook when you have created an application.</p>
      <input type="text" name="outlook_clientId" id="outlook_clientId" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.outlook.clientId'); ?>">
    </div>
  </div>

  <div id="outlook_clientSecret-wrapper" class="form-wrapper">
    <div id="outlook_clientSecret-label" class="form-label">
      <label for="outlook_clientSecret" class="optional"> Application Secret Key</label>
    </div>
    <div id="outlook_clientSecret-element" class="form-element">
      <p class="description">This is a string of letters and numbers provided by Outlook when you have created an application.</p>
      <input type="text" name="outlook_clientSecret" id="outlook_clientSecret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.outlook.clientSecret'); ?>">
    </div>
  </div> 

<?php endif; ?> 

  <div class="border_top form-sub-heading"> 
    <div>Bitly Key Settings</div>
    <div><a href="<?php echo $seaocoreInviteLink; ?>" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
  </div>

  <div id="bitly_apikey-wrapper" class="form-wrapper" style="border:none;">
    <div id="bitly_apikey-label" class="form-label">
      <label for="bitly_apikey" > Api Key </label>
    </div>
    <div id="bitly_apikey-element" class="form-element">
      <input name="bitly_apikey" id="bitly_apikey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.apikey'); ?>" type="text">
    </div>
  </div>
  <div id="bitly_secretkey-wrapper" class="form-wrapper" style="border:none;">
    <div id="bitly_secretkey-label" class="form-label">
      <label for="bitly_secretkey" > Secret Key </label>
    </div>
    <div id="bitly_secretkey-element" class="form-element">
      <input name="bitly_secretkey" id="bitly_secretkey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.secretkey'); ?>" type="text">
    </div>
  </div> 

  <div class="border_top form-sub-heading"> 
    <div>Janrain Key Settings</div>
    <div><a href="http://support.socialengine.com/questions/213/Admin-Panel-Settings-Janrain-Integration" target = "_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif"></a></div>
  </div>

  <div id="core_janrain_domain-wrapper" class="form-wrapper border0">
    <div id="core_janrain_domain-label" class="form-label">
      <label for="core_janrain_domain">Application Domain</label>
    </div>
    <div id="core_janrain_domain-element" class="form-element">
      <p class="description" >
        In the form username.rpxnow.com
      </p>
      <input name="core_janrain_domain" id="core_janrain_domain" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.janrain.domain'); ?>" type="text">
    </div>
  </div>
   

  <div id="core_janrain_id-wrapper" class="form-wrapper border0">
    <div id="core_janrain_id-label" class="form-label">
      <label for="core_janrain_id">Application ID</label>
    </div>
    <div id="core_janrain_id-element" class="form-element"> 
      <input name="core_janrain_id" id="core_janrain_id" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.janrain.id'); ?>" type="text"> 
    </div>
  </div>  

  <div id="core_janrain_key-wrapper" class="form-wrapper border0">
    <div id="core_janrain_key-label" class="form-label">
      <label for="core_janrain_key">API Key</label>
    </div>
    <div id="core_janrain_key-element" class="form-element"> 
      <input name="core_janrain_key" id="core_janrain_key" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.janrain.key'); ?>" type="text"> 
    </div>
  </div>  
 

<style type="text/css">
  .form-sub-heading{
    margin-top:10px; 
    height: 1em; 
  }
  .form-sub-heading div{
    display: block;
    overflow: hidden;
    padding: 4px 6px 4px 0px;
    font-weight: bold;
    float:left;
    margin-top:10px;
  }
  .border_top{
    border-top: 1px solid #9b9797; 
  }
  .form-sub-heading-main{
    margin-top:10px;
    border-bottom: 4px solid #9b9797; 
    height: 1em; 
    display: block;
    overflow: hidden;
    padding: 10px 6px 32px 0px;
    font-weight: bold; 
    font-size: 16px
  }
</style>