<div id="google_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Google Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />
	
  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="google-config">
    	<li>
    		<p>
    			<br/>
<b>1. Login to your developer account </b><br/>
Go to the link: <a href="https://console.developers.google.com" target="blank">https://console.developers.google.com</a> and login using your Gmail account details.
<br/><br/>
<b>2. Create New Google Project</b><br/>
Go to the link <a href="https://console.developers.google.com/cloud-resource-manager" target="blank">https://console.developers.google.com/cloud-resource-manager</a> and click on “Create Project” to create a project for your website.
<br/><br/>
<b>3. Choose a name</b><br/>
Specify a name for your project and click “Create” to create a project. You’ll see a rotating notification icon in the right side. Once it stops rotating, click on it and you’ll see a notification that your project has been created. Click on the notification to open the project dashboard.<br/><br/>
<b>4. Open the APIs & Services Dashboard</b><br/>
Click on the Products & services button (in the left-top side), then go to section “APIs & Services” > “Dashboard” to open the APIs dashboard.<br/><br/>
<b>5. Enable API</b><br/>
Click on the link “+ENABLE APIS AND SERVICES” here, a search box would be shown, search for “Contacts API”. Click “Contacts API” from search results, click on “ENABLE” button to enable the API.<br/><br/>
<b>6. Fill details for OAuth Consent screen</b><br/>
Now, go to “Credentials” > “OAuth Consent screen” and fill the required details for your project (Email address, Product name shown to users, Homepage URL, Product logo URL, Privacy policy URL, Terms of service URL)  and then save the details.<br/><br/>
<b>7. Create Credentials</b><br/>
Now, you need to create API credentials for your project. To do this click on “Credentials” > “Create credentials” > “OAuth Client ID” to create a Client ID. First, you must set a product name on the oauth consent screen before creating app credentials.<br/><br/>
<b>8. Enter Redirect URIs</b><br/>
Choose “Web application” as the ‘Application Type’ here, you will get fields ‘Authorized JavaScript Origins’ and ‘Authorized redirect URIs’, fill in these details:<br/>
<b>Format for Authorized Redirect URLs =></b> <br/>
<b>[For Invite Feature]:</b> http://yoursite.com/seaocore/usercontacts/getgooglecontacts <br/>
<b>[For Login Feature]:</b> http://yoursite.com/sitelogin/auth/google?google_connected=1<br/>
<b>Authorized JavaScript Origins =></b> http://yoursite.com<br/>
Click “Create” to create the credentials, a pop-up will open showing the OAuth client credentials.
<br/><br/>
<b>9. Save the details in SocialEngine Website Admin panel</b><br/>
Copy client ID and client secret from here and save the same in the Contact Importer Settings on the page http://www.yoursite.com/admin/suggestion/global/global section of your site. This page/section will open or will be accessible only if you have enabled Suggestions and Recommendation plugin on your site.
<br/><br/>
<b>10. Domain Verification</b><br/>
You need to verify domain ownership to allow webhook notifications to be sent to your external domains. Google verifies that the user owns each of the listed domains via Search Console.<br/>
<b>Verification for apps</b><br/>
To start the verification process for apps, follow the steps below:<br/>
<b>1. </b>Make sure the <a href="https://console.cloud.google.com/apis/credentials/consent" target="blank">OAuth consent screen</a> details in the Cloud Console APIs & Services Credentials are up to date.<br/>
You must have a privacy policy URL.<br/>
Add URLs for your Home Page and Terms of Service if you have them.<br/>
<b>2. </b>Verify your website ownership through <a href="https://www.google.com/webmasters/tools/home" target="blank">Search Console</a> by using an account that is a Project Owner or a Project Editor on your OAuth project. The same account must be a verified owner of the property in Search Console. Learn more about <a href="https://support.google.com/webmasters/answer/2453966?hl=en" target="blank">Search Console permissions</a>. We can't approve your OAuth verification request until your site ownership verification is complete. <a href="https://support.google.com/webmasters/answer/35179?hl=en" target="blank">Learn more about site verification</a>.<br/>
<b>3. </b>To start the verification process, submit a verification request by completing the <a href="https://support.google.com/code/contact/oauth_app_verification" target="blank" target="blank">OAuth Developer Verification Form</a>. To make sure the verification process goes quickly, review the OAuth Developer Verification Form FAQ. Please note if you add any new redirect URLs or JavaScript origins, or if you change your Product Name after verification, you will have to go through verification again.<br/><br/>
<b>11. Check if the app is working fine</b><br/>
Now, your app has been configured for Google invite service,. Please go to the page http://www.yoursite.com/suggestions/friends_suggestions and choose to invite your friends from ‘Gmail account’, it will ask that if you want to allow this app to access your gmail account information, if you will allow then it will list your Gmail friends in the pop-up and from here you can send them invitation to join the website.
<br/><br/>

[You can refer this <a href="https://youtu.be/i_UH9HI0-Mk" target="blank">video</a> for more details.]
    			<br/><br/>
    		</p>
    	</li>
		</ul>
	</div>
<script type="text/javascript">
  if(scriptJquery('#'+id).css('display') == 'block') {
      scriptJquery('#'+id).css('display', 'none');
    } else {
      scriptJquery('#'+id).css('display', 'block');
    }
  }
</script>
</div>
