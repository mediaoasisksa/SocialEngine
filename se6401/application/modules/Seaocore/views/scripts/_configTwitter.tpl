<div id="twitter_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Twitter Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />
	
  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="twitter-config">
    	<li><p><br/>
<b>1. Login to your Developer Account</b><br/>
First of all, you need to link your mobile no. with your Twitter profile if you haven’t linked it already. It is necessary for creating Twitter application. For linking mobile no, go to this URL: <a href="https://twitter.com/settings/devices" target="blank">https://twitter.com/settings/devices</a>, login to your Twitter account and link your mobile number.
<br/><br/>

<b>2. Create an App</b><br/>
Now, go to the link: <a href="https://apps.twitter.com/" target="blank">https://apps.twitter.com/</a> and click on the button “Create New App”.
<br/><br/>

<b>3. Go to App Settings</b><br/>
Fill all the required details for your app i.e. Name, Add Icon & Description.<br/>
<br/><br/>

<b>4. Permissions</b><br/>
Click on the tab “Permissions” to choose what type of permission is required for your app. For importing Twitter contacts, we need “Read only” permission only. So, please select “Read Only” against the field “What type of access does your application need? And then update settings.
<br/><br/>

<b>5. Authentication settings</b><br/>
Edit Authentication Settings and enable ->Enable 3-legged OAuth.
<br/><br/>

<b>6. Entering the required details</b><br/>
Fill all the required details for your app i.e. Add callback URLs, Website URL. <br />
Format for callback URL: <br />
<b>[For Login]:</b> https://yoursite.com/sitelogin/auth/twitter <br/>
<b>[For Invite]:</b> https://yoursite.com/seaocore/auth/twitter<br/>
                   https://yoursite.com/user/auth/twitter<br/>

Then, click on the checkbox shown for accepting ‘Developer Agreement’ and then click on “Create your Twitter Application” to create you Twitter app.
<br/><br/>

<b>7. Keys and Access Tokens</b><br/>
Click on the tab “Keys and Access Tokens” to know the Consumer Key & Consumer Secret for your app.<br/><br/>

<b>8. Save the details in the Admin Panel</b><br/>
Copy the Consumer Key & Consumer Secret  found in above step and paste it to the respective fields from http://www.yoursite.com/admin/user/settings/twitter section of your site.
<br/><br/>

<b>9. Check if the app is working fine</b><br/>
Your Twitter app has been configured. To Invite your friends from your twitter account,<a href="https://socialapps.tech/socialengine-suggestions-recommendations-plugin" target="_blank">Suggestions / Recommendations Plugin</a> will be required on the site. Please go to the page <a href="http://www.yoursite.com/suggestions/friends_suggestions" target="_blank">http://www.yoursite.com/suggestions/friends_suggestions</a> and choose to invite your friends from your Twitter account, it should list your Twitter contacts and from here you can send them an invitation to join the site.

    		<br/><br/>
    	</p></li>
  	</ul>
	</div>
<script type="text/javascript">
   function guideline_show(id) {
    if(scriptJquery('#'+id).css('display') == 'block') {
      scriptJquery('#'+id).css('display', 'none');
    } else {
      scriptJquery('#'+id).css('display', 'block');
    }
  }
</script>
</div>
