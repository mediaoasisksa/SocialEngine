<div id="windowlive_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Windows Live Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="guideline_4">

    	<li>
    		<p>
    			<br/>
    			<b>1. Access the Windows Live application management site</b><br/>
Go to the page <a href="http://go.microsoft.com/fwlink/?LinkID=144070" target="blank">http://go.microsoft.com/fwlink/?LinkID=144070</a> and login with your your Windows Live ID. (Note: If this is your first visit to this site, you will see several pages that configure your Windows Live ID for use with the site.)<br/><br/>
<b>2.</b> After you are logged in, you’ll see a page listing all your applications, click on  button “Add an App” provided in the right side on the top of the page to create a new app.<br/><br/>
<b>3.</b> On the next page, you need to provide a name for your app in the field “Application name” and then click “Create” button to create the app.<br/><br/>
<b>4.</b> Next page will be for registration of your app, you will see below details on the page:<br/><br/>
 &nbsp; &nbsp;<b>>Name:</b> This field is for name of your app, this will be shown pre-filled with the name that you   provided in the 3rd step, but if you need to change the name, you can edit it from here.<br/><br/>
 &nbsp; &nbsp;<b>>Application Id:</b> This will show the app id.<br/><br/>
 &nbsp; &nbsp;<b>>Application Secrets:</b> You can generate application secrets for your app from here. Click “Generate New Password” to create a new password for your site, a dialogue box will open up showing the password, copy and save this password securely. This is the only time when you can see this password, so remember to save it somewhere securely. If you want to view it for the next time you need to ‘Generate New Password’ for the same application.<br/><br/>
 &nbsp; &nbsp;<b>>Platform:</b> Add a platform for your app, you need to select “Web” platform here. On selecting “Web” platform, you’ll see following two fields:<br/>
 &nbsp; &nbsp; &nbsp; &nbsp;<b>=>Redirect URLs :</b> Format for REdirect URLs should be as below:<br/>
&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;<b>[For Login Feature]:</b><br/>
&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;https://yoursite.com/sitelogin/auth/outlook<br />
&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;This would allow users to login via their Outlook accounts.<br />
&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;<b>[For Invite Friends Feature]:</b><br/>       
&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;https://yoursite.com/seaocore/auth/windowslive<br />
&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;[Your URL must begin with https://]<br/><br/>
&nbsp; &nbsp; &nbsp; &nbsp;<b>=>Logout URL: </b>Format for logout URL:<br/>
&nbsp; &nbsp; &nbsp; &nbsp;https://yoursite.com/logout<br/>
&nbsp; &nbsp; &nbsp; &nbsp;[Your URL must begin with https://]<br/><br/>
&nbsp; &nbsp;<b>>Microsoft Graph Permissions:</b><br/>
&nbsp; &nbsp; &nbsp; &nbsp;<b>=>Delegated Permissions:</b> Select the permission “Contacts.Read” for your app.<br/>
&nbsp; &nbsp; &nbsp; &nbsp;<b>=>Application Permissions:</b> Not need to add any application permission.<br/><br/>
&nbsp; &nbsp;<b>>Profile:</b><br/>
&nbsp; &nbsp; &nbsp; &nbsp;<b>=>Logo:</b> Add a Logo for your app.[The logo must be a transparent 48 x 48 or 50 x 50 pixel image in a GIF, PNG or JPEG file that is 15 KB or smaller.]<br/>
&nbsp; &nbsp; &nbsp; &nbsp;<b>=>Home page URL:</b> Fill the URL of the Landing page of your site here.<br/>
&nbsp; &nbsp; &nbsp; &nbsp;<b>=>Terms of Service URL:</b> Fill the URL of the Terms of Service  page of your site here.<br/>
&nbsp; &nbsp; &nbsp; &nbsp;<b>=>Privacy Statement URL:</b> Fill the URL of the Privacy Policy page of your site here.<br/><br/>
&nbsp; &nbsp;<b>>Advanced Option:</b> This step is not needed, so uncheck the checkbox against “Live SDK support”.<br/><br/>
<b>5. Save the details</b><br/>
Save all the details filled by you by clicking on the “Save” button.<br/><br/>
<b>6. Save the details in the Admin Panel</b><br/>
These steps 6 and 7 are for invite functionality only. For login functionality, you need to fill these API details in the admin panel section of Social Login and Sign-up Plugin.<br/>
For invite friends feature, save the (Application Id) and (Private Key (the password that you generated in step 4 > “Application secrets”) in the <b>Contact Importer Settings</b> on the page<br/>
http://yoursite.com/admin/suggestion/global/global section of your site. 
<br/><br/>
<b>7. Check if the app is working fine</b><br/>
Your Windows Live app has been configured, please go to the page http://yoursite.com/suggestions/friends_suggestions and choose to invite your friends from Windows Live, it should list your Windows Live contacts and from here you can send them invitation to join the site.<br/><br/>
[You can refer this <a href="https://youtu.be/h1cAYbpLU90" target="blank">video</a> for more details.]
    			<br/>
    			<br/>
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
