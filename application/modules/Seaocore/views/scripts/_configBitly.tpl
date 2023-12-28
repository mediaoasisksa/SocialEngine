<div id="bitly_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Bitly Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />
	
  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="google-config">
    		<li><p><br/>
    				<b>1. Login to your Bitly account</b><br/>
            Login to your Bitly account from here: <a href="https://bitly.com/a/sign_in" target="blank">https://bitly.com/a/sign_in</a><br/><br/>
<b>2. Create App</b><br/>
Create on “Settings” button on the top right corner of the page, then click “>” button to edit your profile and choose the option “REGISTERED OAUTH APPLICATIONS” from the drop-down list. After that click “REGISTER NEW APP” > “GET REGISTRATION  CODE” button to get the code for registering your app. The code will be received in your registered email account. <br/><br/>
<b>3. Complete Registration</b><br/>
Open your primary email account, you’ll see a mail from Bitly in the inbox. Open the    mail, click on the button “COMPLETE REGISTRATION” provided in the mail, you’ll be redirected back to your Bitly account displaying REGISTER OAUTH APP box opened.<br/><br/>
<b>4. Fill all the details required for registration</b><br/>
&nbsp; &nbsp;APPLICATION NAME: specify a unique name for your app<br/>
&nbsp; &nbsp;APPLICATION LINK: The URL of your application<br/>
&nbsp; &nbsp;REDIRECT URIS: Format for redirect URI is http://yoursite.com/user/auth/bitly<br/>
&nbsp; &nbsp;APPLICATION DESCRIPTION: give a brief description of how your app uses Bitly.<br/>
After filling all these details, click “REGISTER APP” to register the app.<br/><br/>
<b>5.</b> Your app will now get listed under REGISTERED OAUTH APPLICATIONS, click on the app name, you’ll get the Client ID and Client secret for your app. If you need, you can also regenerate new Client ID and Secret clicking on “REGENERATE” button.<br/><br/>
<b>6.</b> Go to the section “Settings” > OPTIONS > Settings > Advanced Settings > API Support, i.e. to the link <a href="https://bitly.com/a/your_api_key" target="blank">https://bitly.com/a/your_api_key</a> and get username and API key.<br/><br/>
<b>7. Save the details to Admin Panel</b><br/>
Go to section “Admin” > “Plugins” > “Advanced Activity Feed” > “Global Settings” > “Third Party Settings” and save the keys generated in above step in the fields provided for Bitly keys.<br/><br/>

[You can refer this <a href="https://youtu.be/dQdH7Xn66Zk" target="blank">video</a> for more details.]<br/><br/>
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
