<h2>
    <?php echo $this->translate('Course Builder / Learning Management Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
<script type="text/javascript">
  function faq_show(id) {
    if(document.getElementById(id).style.display == 'block') {
      document.getElementById(id).style.display = 'none';
    } else {
      document.getElementById(id).style.display = 'block';
    }
  }
</script>






  <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">  
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Is this Plugin Integrated with Other Plugins?");?></a>
                <div class='faq' style='display: none;' id='faq_1'>
                    <?php echo $this->translate('Yes, This Plugin is Integrated with <a target="blank" href="https://socialapps.tech/socialengine-advanced-activity-feeds-wall-plugin">Advanced Activity Feeds</a> & <a target="blank" href="https://socialapps.tech/socialengine-advanced-search-plugin">Advanced Search Plugin</a>.');?>
                    
                </div>
            </li>
            <li>
          <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I want to configure the various widgets of this plugin according to my requirements? How can I do it ?");?></a>
                <div class='faq' style='display: none;' id='faq_2'>
                    <?php echo $this->translate('To configure the various widgets of this plugin according to your requirements');?>               
                <ul>
                    <li><?php echo $this->translate('Please place those widgets at the desired locations from the Layout Editor section.');?>
                </li>
                <li><?php echo $this->translate('Select the Pages from the Editing dropdown in the Admin Panel and click on "edit" option against the desired widgets.');?></li>
            </ul>
            </div>
            </li>
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Is there any limit for a member to post courses and the topics in the courses?");?></a>
                <div class='faq' style='display: none;' id='faq_3'>
                    <?php echo $this->translate('There is no limit to add topics but for courses, limits can be set for each particular member level from the Member Level Settings under Admin Panel.');?>
                </div>
            </li>

            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate('Can the course owner delete the courses & the topics after they are purchased?');?></a>
                <div class='faq' style='display: none;' id='faq_4'>
                    <?php echo $this->translate('No, Course owner cannot delete the courses and any of the topics and the lessons added in the topics after the users enrolled in the course.');?>
                </div>
            </li>

            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate('If the courses are purchased by the users, can the price of the courses be changed?');?></a>
                <div class='faq' style='display: none;' id='faq_5'>
                    <?php echo $this->translate('No, the price of the courses cannot be changed if the users are enrolled in the courses.');?>
                </div>
            </li>

            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Can admin have the ability to limit the reminders for approval of the courses?");?></a>
                <div class='faq' style='display: none;' id='faq_6'>
                    <?php echo $this->translate('Yes, there is a setting of Maximum Reminder for Approval on the Admin Panel under Member Level Settings where admin can set the limit to maximum reminder for course owners.');?>
                </div>
            </li>      
      
        <li>
          <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("How can I approve the request from the course owners?");?></a>
          <div class='faq' style='display: none;' id='faq_7'>
            <?php echo $this->translate('To Approve the requests from the course owners, follow these steps:');?>
            <ul>
                <li>
                    <?php echo $this->translate('Go to Manage Courses tab on admin panel.');?>
                </li>
                <li>
                    <?php echo $this->translate('Go to Approve Request sub-tab on the Manage Courses tab.');?>
                </li>
                <li>
                    <?php echo $this->translate('Click on the Approve Icon(Green Tick) to Approve the courses.');?>
                </li>
            </ul>
          </div>
        </li>

            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("What are the actions I can take on the reported courses in which users are enrolled?");?></a>
                <div class='faq' style='display: none;' id='faq_8'>
                    <?php echo $this->translate('Admin can disable the future enrollment in the courses which are reported by other users.');?>
                </div>
            </li>

            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("What are the actions I can take on the reported courses in which no users are enrolled yet?");?></a>
                <div class='faq' style='display: none;' id='faq_9'>
                    <?php echo $this->translate("Admin have the ability to delete, disapprove and disable the future enrollment of the courses which are published and reported by no users are enrolled in them.");?>
                </div>
            </li>

      <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("What are the fields we need to submit for publishing the courses?");?></a>
                <div class='faq' style='display: none;' id='faq_12'>
                    <?php echo $this->translate("Course owners need to add the Intro Video and at least 1 topic and 1 lesson to the course before publishing the courses.");?>
                </div>
            </li>
      
      <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate('How can I add the Background Image and the Company logo on the Certificates?');?></a>
                <div class='faq' style='display: none;' id='faq_13'>
                    <?php echo $this->translate('To add the Background Image and Company logo, follow these steps:');?>
                    <ul>
                        <li>
                            <?php echo $this->translate('Go to File & Media Manager section under Appearance on the Admin Panel and upload the Images and the logo.');?>
                        </li>
                        <li>
                            <?php echo $this->translate('Go to Certificates tab on the Course Plugin on the Admin Panel.');?>
                        </li>
                        <li>
                            <?php echo $this->translate('Select the files for Company logo and Background Image from the dropdown uploaded in the File & Media Manager section.');?>
                        </li>
                        <li>
                            <?php echo $this->translate('Click on the Save Settings button to save the Images.');?>
                        </li>
                    </ul>
                </div>
            </li>      

             <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate('What is FFMPEG Path in this plugin? Where will I get it?');?></a>
                <div class='faq' style='display: none;' id='faq_14'>
                    <?php echo $this->translate('Follow the steps below to install FFMPEG and to define path for it in Global Settings:');?>
                    <ul>
                        <li>
                            <?php echo $this->translate('Download FFMPEG static files from
                            <a target="blank" href="https://ffmpeg.org/download.html">https://ffmpeg.org/download.html</a>.');?>
                        </li>
                        <li>
                            <?php echo $this->translate('Place FFMPEG files in Document Root. For e.g. /var/www/html or /home/abc/public_html.');?>
                        </li>
                        <li>
                            <?php echo $this->translate("Go to 'Video Utilities' > 'Video Settings tab' in the admin panel of this plugin.");?>
                        </li>
                        <li>
                            <?php echo $this->translate("Define FFMPEG path in 'Path to FFMPEG' field.");?>
                        </li>
                        <li>
                            <?php echo $this->translate("Click on the 'Save Changes' button to save the new changes in the settings.");?>
                        </li>
                        <li>
                            <?php echo $this->translate('Give 777 Recursive permission to the FFMPEG folder.');?>
                        </li>
                        <li>
                            <?php echo $this->translate("You can check the version of FFMPEG that you have installed along with the supported video formats in the 'Global Settings' > 'Video Utilities' section available in the admin panel of this plugin.");?>
                        </li>
                    </ul>
                </div>
            </li>   
      <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_53');"><?php echo $this->translate('Do I need to upload videos in any specific format or it supports all formats?');?></a>
                <div class='faq' style='display: none;' id='faq_53'>
                    <?php echo $this->translate('No, FFMPEG converts all the video formats dynamically for your website. So, you do not need to bother about video formats and resizing.');?>
                </div>
            </li>      

      <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_54');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
                <div class='faq' style='display: none;' id='faq_54'>
                    <?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode.");?>
                </div>
            </li> 
   
        </ul>
    </div>
 
