<div id="yahoo_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Yahoo Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="guideline_2">
    	<li><p>
<br/>
<b>1. Login to your Yahoo developer account</b><br/>
Go to the link: <a href="https://developer.yahoo.com/apps/" target="blank">https://developer.yahoo.com/apps/</a> and login<br/><br/>
<b>2. Create an app</b><br/>
Click “Create an App” to create an app for your site.<br/><br/>
<b>3. Fill all details</b><br/>
Fill all these below required details for your app:<br/>
&nbsp; &nbsp;> Application name <br/>
&nbsp; &nbsp;> Application Type : Choose the type as Web Application.<br/>
&nbsp; &nbsp;> Description<br/>
&nbsp; &nbsp;> Home Page URL<br/>
&nbsp; &nbsp;> Callback Domain<br/>
   Format for callback domain: http://yoursite.com<br/>
&nbsp; &nbsp;> API Permissions: For this field, choose “Contacts” as we need this permission to import Yahoo contacts. You need only Read permission for reading the contacts.<br/>
Then, click on “Create App” to create the app.<br/><br/>
<b>4. Get Consumer Key and Secret</b><br/>
On the next page, you’ll get your Client ID (Consumer Key)<br/>
And Client Secret (Consumer Secret), you need to fill these details in the Admin panel of your site.<br/><br/>
<b>5. Save the details in SocialEngine Admin panel</b><br/>
Save the (Consumer Key) and (Consumer Secret) in the Contact Importer Settings on the page http://www.yoursite.com/admin/suggestion/global/global section of your site.<br/><br/>
<b>6. Check if the app is working fine</b><br/>
Your Yahoo app has been configured, please go to the page http://www.yoursite.com/suggestions/friends_suggestions and choose to invite your friends from Yahoo account, it should list your Yahoo friends and from here you can send them invitation to join the site.<br/><br/>
[You can refer this <a href="https://youtu.be/_foXxde2OGo" target="blank">video</a> for more details.]
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
