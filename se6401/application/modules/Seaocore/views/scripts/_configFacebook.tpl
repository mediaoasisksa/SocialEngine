<div id="facebook_helpsteps">
    <div class="tabs">
        <ul class="navigation">
            <li class='active' id="facebookapp_help">
                <a class="menu_seaocore_admin_main seaocore_admin_upgrade" href="javascript:void(0);" onclick= "show_fbseacorehelp('facebookapp');">App Configuration</a>
            </li>
            <?php $facebookse = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse'); ?>
            <?php if (!empty($facebookse) && (Engine_Api::_()->seaocore()->checkVersion($facebookse->version, '4.7.1') == 1) && 0): ?>
                <li id="fbpostreview_help">
                    <a class="menu_seaocore_admin_main seaocore_admin_info" href="javascript:void(0);" onclick= "show_fbseacorehelp('fbpostreview');">Post Reivew for a Like Action</a>
                </li>
            <?php endif; ?>
            <li id="facebookappsubmission_help">
                <a class="menu_seaocore_admin_main seaocore_admin_upgrade" href="javascript:void(0);" onclick= "show_fbseacorehelp('facebookappsubmission');">App Submission</a>
            </li>
        </ul>
    </div>

    <!--Facebook App Configuration Help Section-->

    <div id="facebookapp_helpsteps">
        <h3><?php echo $this->translate("Guidelines to configure Facebook Application") ?></h3>
        <p><?php echo $this->translate('Please follow the steps given below.') ?></p><br/>

        <div class="admin_seaocore_guidelines_wrapper">
            <ul class="admin_seaocore_guidelines" id="guideline_1">
                <li><p>
                    <br/>
<b>1. Login to your Developer Account</b><br/>
You should have a Facebook Developer Account in order to create a Facebook App. If you already have one then go to the page <a href="https://developers.facebook.com/apps" target="blank">https://developers.facebook.com/apps</a> and login with your Facebook credentials or else you will first need to register here as a ‘Developer’.
<br/><br/>
<b>2. Create New Facebook App</b><br/>
Click on the button “+ Add a New App” in order to start the process or if you already have an app created for your website, then click on “My Apps” and select the app you want to use.
<br/><br/>
<b>3. Choose a name</b><br/>
Choose a name for your app under ‘Display Name’ and enter ‘Contact Email’ and click on ‘Create App ID’. After that a recaptcha pop-up will open up, verify the recaptcha to proceed.
<br/><br/>
<b>4. You will be redirected to this URL > https://developers.facebook.com/apps/22xxxxx16/add/</b>
<br/><br/>
<b>5. Fill in Basic App Details</b><br/>
Go to the section ‘Settings’ (at the left hand side) > ‘Basic’ and fill all the required fields: Display name, Namespace, App Domains, Contact Email, Privacy Policy URL, Terms of Service URL, Business Use.<br/>
<?php echo $this->translate("<b>Your site's domain</b> => <b>" . $_SERVER['HTTP_HOST'] . "</b> in the 'App Domain' field in the 'Basic Info' section. Please note that the domain should not contain 'www.' prefix. "); ?><br />
'Namespace' section will be the name which will be used by your Facebook App as App Name.<br/><br/>
<b>6. Choose a Category</b><br/>
Choose the Category > “Business and Pages” for your app.<br/><br/>
<b>7. Uploading App Icon</b><br/>
Upload an ‘App Icon’ for your app (Icon size should be ‘1024 x 1024’).<br/><br/>
<b>8. Business Use</b><br/>
Choose whether your app is for your business or whether it provides services to other businesses. <br/><br/>
<b>9. Choosing the Platform</b><br/>
Click on “+ Add Platform” to add platforms for your app. Add ‘Website’ as the platform and then provide the URL of your website for the field “Website”.<br/><br/>
<b>10. Save the details in the Admin Panel of SocialEngine Website</b><br/>
Go to the “Admin Panel” > “Settings” > “Facebook Integration” and fill the ‘App Id’ and ‘App Secret’ of your app which you can get from section ‘Settings’ > ‘Basic’ of your Facebook App dashboard.<br/>


For the setting “Integrate Features What features would you like to integrate?” provided in the Admin panel, choose the option that you require for your site out of three options (None, Login only, Publish to Facebook) provided.<br/>

Now, if you want to have login with Facebook feature for your app, then you need to follow these below step too:<br/>

Click on PRODUCTS in the app dashboard and add “Facebook Login”. Then go to “Facebook Login” > “Settings” and fill all the required details for your app including the valid OAuth Redirect URIs for your app and save the settings. Now, your users can login via their Facebook account on your site.<br/>
Please also make sure that “Facebook” is allowed in the Contact Importer settings of Suggestions/Recommendations Plugin for inviting friends option.
<br/><br/>
<b>11. Check the functioning of the Facebook App</b><br/>
Now, go to the page http://www.yoursite.com/suggestions/friends_suggestions and choose to invite your friends from Facebook, you will see a Send Dialogue box from where you can send invite to your Facebook friends. You can select the friends to whom you want to send invite message.<br/><br/>
[You can refer this <a href="https://youtu.be/oy9d_x5cy4g" target="blank">video</a> for more details.]
<br/><br/>
                </p></li>







<!-- 
                <li>
                    <div class="steps">
                        <a href="javascript:void(0);" onClick="guideline_show('fbstep-1');"><?php echo $this->translate("Step 1"); ?></a>
                        <div id="fbstep-1" style='display: none;'>
                            <p>
                                <?php echo $this->translate("Go to this URL for steps to configure basic Facebook Integration on your SocialEngine website : "); ?><a href="https://www.youtube.com/watch?v=ofv09taAKLI" target="_blank" style="color:#5BA1CD;">https://www.youtube.com/watch?v=ofv09taAKLI</a><br/>    
                            </p>
                        </div>
                    </div>	
                </li>	 -->

    <!--             <li>	
                    <div class="steps">
                        <a href="javascript:void(0);" onClick="guideline_show('fbstep-2');"><?php echo $this->translate("Step 2"); ?></a>
                        <div id="fbstep-2" style='display: none;'>
                            <h3><?php echo $this->translate(" Details to be filled while creating Facebook Application: "); ?><br /></h3>

                            <p>
                                <?php echo $this->translate("a)  <b>Your site's domain</b> => <b>" . $_SERVER['HTTP_HOST'] . "</b> in the 'App Domain' field in the 'Basic Info' section. Please note that the domain should not contain 'www.' prefix. "); ?><br />
                                <?php echo $this->translate("b) <b>'Namespace'</b> section. This will be the name which will be used by your Facebook App as App Name.") ?><br />

                                <?php //echo $this->translate("c) Make sure the \"Sandbox Mode\" is disabled.")?><br />

                                <?php echo $this->translate("c) <b>Your site's url</b>") . ' => <b>' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/</b> in the 'Site URL' field in the 'Website' section."; ?><br />
                            </p>


                        </div>
                    </div>
                </li> -->
                <!-- <li>
                    <div class="steps">

                        <p>
                        <h3>Facebook invitations</h3>
                        If Facebook invitations are vital for your social community then you need to do the following changes in your Facebook App, since they have done some changes in their invitation policies from April 30, 2015. <br/><br/>
                        1) Make sure that you have followed above step 1 and step 2.<br/><br/>
                        2) You need to change your Facebook App category to “Games” from App Details >> App Info >> Category section. It is necessary as facebook has restricted invitations for all other type of categories.<br/><br/>
                        3) Now add “Secure Canvas URL” [Facebook allows only HTTPS based URL] by clicking on Add Platform >> Facebook Canvas.<br/><br/>

                        We understand that many clients might not have installed SSL certificate on their server thus, cannot provide URL based on HTTPS Protocol so, there are two steps below, following which you will be able to add appropriate “Secure Canvas URL”. <br/><br/>

                        <b>Method 1</b>: This methods applies only if you are having / arranged website running on  HTTPS protocol.<br/><br/>
                        Firstly, download a sample canvas file from <span><a style="margin-right: 3px;" href="<?php echo $this->url(array('action' => 'download-sample-canvas')) ?><?php echo '?path=' . urlencode('canvas_app.php'); ?>" target="downloadframe" class="buttonlink icon_sitepages_download_csv"><?php echo $this->translate('here') ?></a>.</span><br/><br/>
                        Secondly, place this file at the root directory of your website.<br/><br/>
                        Thirdly, open : https://www.example.com/canvas_app.php in browser. Here, “https://www.example.com” >> is your HTTPS based website URL and canvas_app.php >> is the ‘file name’ which you have uploaded in second step. If you are able to open above created URL, then it seems that you have uploaded the file correctly.<br/><br/>
                        Now, use the below Secure Canvas URL for your Facebook App:<br/><br/>

                        https://www.example.com/canvas_app.php?redirect= <?php echo urlencode(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl()) . "&community_title=" . Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?><br/><br/>

                        <b>Method 2</b>: If you are not having website running on HTTPS protocol then you can simply copy the below ‘Secure Canvas URL’: <br/> <?php echo 'https://demo.socialengineaddons.com/canvas_app.php?redirect=' . urlencode(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl()) . "&community_title=" . Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?><br/><br/>

                        [<b>Note</b>: we are using our demo website URL here, but it does not mean that we are saving any kind of data like Facebook data, Facebook Invites or user data on our site, we have provided this URL only because many clients do not have HTTPS URL based protocol on their site.]<br/><br/>

                        <h3 style="border-top: 1px solid #eee; padding: 15px 0px 5px;">FAQs for Facebook Invitations</h3>

                        <div style="margin:5px 0">
                        <b>Q</b>: I want to change the text shown while redirecting to the URL on https://www.facebook.com i.e: “Please click the button below to use your invite and signup on...” according to my requirement. How will I be able to do it?<br>
                        <p><b>Ans</b>: If you are choosing method 1, you can do your required changes in file: canvas_app.php, which you have uploaded at the root directory of your website. But in case of method 2, since the file is located at our server, we will not be able to change the text. It is common for every client.</p></div><br/>

                        <div style="margin:5px 0">
                        <b>Q</b>: After doing above changes, invitation request are coming in Notifications section of facebook instead of Message section. What might be the reason?<br/>
                        <p><b>Ans</b>: Since Facebook has done changes in their invitation policies, the invitation requests will be  shown under Notifications section instead of Message section.<br/>
                        </p></div>
                        </p>
                    </div>
                </li> -->
            </ul>
        </div>
    </div>


    <!--End Here-->

    <!--Post Review for a Facebook Like action Help Section-->

    <div id="fbpostreview_helpsteps" style="display:none;">
        <h3><?php echo $this->translate("Guidelines to post review and create custom action type on Facebbook for an action type used") ?></h3>
        <p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />
        <div class="admin_seaocore_guidelines_wrapper">
            <ul class="admin_seaocore_guidelines" id="guideline_1">
                <li>
                    <div class="steps">
                        <a href="javascript:void(0);" onClick="guideline_show('fbreviewstep-1');"><?php echo $this->translate("1) Post Review for an action type on Facebook"); ?></a>
                        <div id="fbreviewstep-1" style='display: none;'>
                            <p>
                                <?php echo $this->translate("Go to this URL for steps to post review on Facebook for an action type : "); ?><a href="https://developers.facebook.com/docs/opengraph/submission-process/#submitbuiltin" target="_blank" style="color:#5BA1CD;">https://developers.facebook.com/docs/opengraph/submission-process/</a><br/>    
                            </p>
                        </div>
                    </div>	
                </li>

                <li>
                    <div class="steps">
                        <a href="javascript:void(0);" onClick="guideline_show('fbreviewstep-2');"><?php echo $this->translate("2) Creating Custom Action"); ?></a>
                        <div id="fbreviewstep-2" style='display: none;'>
                            <p>
                                <?php echo $this->translate("Go to this URL for steps to create custom action types for your Facebook App : "); ?><a href="https://developers.facebook.com/docs/opengraph/creating-action-types/" target="_blank" style="color:#5BA1CD;">https://developers.facebook.com/docs/opengraph/creating-action-types/</a><br/>    
                            </p>
                        </div>
                    </div>	
                </li>
                <li>
                    <div class="steps">
                        <a href="javascript:void(0);" onClick="guideline_show('fbreviewstep-3');"><?php echo $this->translate("3) Creating Custom Action"); ?></a>
                        <div id="fbreviewstep-3" style='display: none;'>
                            <p>
                                <?php echo $this->translate("Go to this URL for steps to create custom action types for your Facebook App : "); ?><a href="https://developers.facebook.com/docs/opengraph/creating-action-types/" target="_blank" style="color:#5BA1CD;">https://developers.facebook.com/docs/opengraph/creating-action-types/</a><br/>    
                            </p>
                        </div>
                    </div>	
                </li>

            </ul>

        </div>

    </div>

    <!--FACEBOOK APP SUBMISSION STARTS-->
    <div id="facebookappsubmission_helpsteps" style="display:none;">
        <h3><?php echo $this->translate("Description") ?></h3>
        <br />
        <div class="admin_seaocore_guidelines_wrapper">
            <ul class="admin_seaocore_guidelines" id="guideline_1">
                <li>
                    <div class="steps">
                        <div id="fbreviewstep-1" >
                            <p>Problem in configuring Facebook Application for your website or unable to get approval for your Facebook Applications? Do not worry, read these guidelines <a href="http://www.google.com/url?q=http%3A%2F%2Fwww.socialengineaddons.com%2Fpage%2Ffacebook-application-submission&sa=D&sntz=1&usg=AFQjCNGrbxulx87zvxIDNBTojfRyH3gRJA">“Facebook Application Submission”</a> and solve your queries quickly.<br/>
                                However if you are still getting problem then you can purchase our service <a href="http://www.google.com/url?q=http%3A%2F%2Fwww.socialengineaddons.com%2Fservices%2Ffacebook-application-configuration-and-submission-service&sa=D&sntz=1&usg=AFQjCNHo-Nsd4_Ej_2o9aIyZP1thJxxNFw">“Facebook Application Configuration and Submission Service”</a> and let us configure it for you.<br/>    
                            </p>
                        </div>
                    </div>	
                </li>
            </ul>
        </div>
    </div>
    <!--FACEBOOK APP SUBMISSION ENDS-->
</div>

<script type="text/javascript">
     function guideline_show(id) {
        if (scriptJquery('#'+id).css('display') == 'block') {
            scriptJquery('#'+id).css('display', 'none');
        } else {
            scriptJquery('#'+id).css('display', 'block');
        }
    }

    var previous_tabfb = 'facebookapp';
    function show_fbseacorehelp(active_tab) {

        if (active_tab != previous_tabfb) {
            scriptJquery('#'+previous_tabfb + '_help').removeClass('active');
            scriptJquery('#'+active_tab + '_help').addClass('active');
            scriptJquery('#'+active_tab + '_helpsteps').css('display', 'block');
            scriptJquery('#'+previous_tabfb + '_helpsteps').css('display', 'none');
            previous_tabfb = active_tab;
        }

    }
</script>


<!--End Here-->
