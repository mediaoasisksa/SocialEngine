<div class="admin_seaocore_files_wrapper">
  <ul class="admin_seaocore_files seaocore_faq">  
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How should I start with creating Services and Service Providers on my site?");?></a>
        <div class='faq' style='display:none;' id='faq_1'>
          <?php echo $this->translate("After plugin installation, follow the steps below:");?>
          <ul>
            <li>
              <?php echo $this->translate("Start by configuring the Global Settings for your plugin.");?>
            </li>
            <li>
              <?php echo $this->translate("Then create the categories, sub-categories and 3rd level categories.");?>
            </li>
            <li>
              <?php echo $this->translate("Then go to the Profile Fields section to create custom fields if required for any services on your site and configure mapping between services categories and profile types such that custom profile fields can be based on the categories, sub-categories and 3rd level categories for your services.");?>
            </li>
            <li>
              <?php echo $this->translate("Configure the reviews and ratings settings for your site from the Reviews & Ratings section.");?>
            </li>
            <li>
              <?php echo $this->translate("Customize various widgetized pages from the Layout Editor section.");?>
            </li>
          <br />
          </ul>
        <?php echo $this->translate("You can now start creating the service providers from front end and then services within service providers on your site.");?>
        </div>
      </li>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("How can the users set the time durations for appointments booking for any service created by them?");?></a>
        <div class='faq' style='display:none;' id='faq_2'>
          <?php echo $this->translate('To set the time durations for services created within this plugin, users can go to Service Dashboard > Set Availability section and select the service from the dropdown for which he need to set the time durations and set the required time durations from there. He can set off on particular days too, so no bookings can be made for those days.');?>
        </div>
      </li>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("How can I associate a service provider with a category?");?></a>
        <div class='faq' style='display:none;' id='faq_3'>
          <?php echo $this->translate('This is not possible to directly associate a service provider with a category. The categories of the services made within a service provider automatically become the categories of the service provider. So, if a user search a provider based on any category, then those providers will get listed which have services belonging to that category. ');?>
        </div>
      </li>

      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate('Is it necessary to do Category to Service Profile Mapping for each category?');?></a>
        <div class='faq' style='display:none;' id='faq_4'>
          <?php echo $this->translate('No, you just need to do mapping for only those Categories, for which you want to show some additional information on the Service Creation Page.');?>
        </div>
      </li>

      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate('I previously saved dollar as the Currency Type from Global Settings, but now when I  am changing that to Euro, then currencies are not being converted to the correct values?');?></a>
        <div class='faq' style='display:none;' id='faq_5'>
          <?php echo $this->translate('The setting “Currency Type” in the Global Settings is just for the display of the costs of services, there will not be any change in the values of costs, just currency signs will be changed. So, admin need to first decide which currency type he wants to use for showing services costs as changing that later will not do conversions in currency values.');?>
        </div>
      </li>

      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I am living in the US but my clients or users who may book the services offered by me can be from all over the world, so I am wondering how will they be able to book the services according to their time zones as if I have set the available time durations as per my timezone?");?></a>
        <div class='faq' style='display:none;' id='faq_6'>
          <?php echo $this->translate('The plugin will automatically convert the time set for different services availability durations according to the timezone of the viewer, the times will change to the timezone set in the general settings of the user who is viewing the service.');?>
        </div>
      </li>    

      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("Why I as a user able to book a service for more than 1 time for a single time slot?");?></a>
        <div class='faq' style='display:none;' id='faq_7'>
          <?php echo $this->translate('Suppose, if you want to book a service for you and your friend, then you need to book the service for two times for the same time period, that is why you are allowed to book a service for more than 1 time for a single time slot.');?>
        </div>
      </li>  

         
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I want to do some changes in the layout of the Service and Service Providers Home Page, how can I do that?");?></a>
      <div class='faq' style='display:none;' id='faq_8'>
      <?php echo $this->translate('To do the changes in the layout of the pages, please go to Admin > Layout Editor section, open the page you want to change the layout of which and then');?>
      </div>
    </li>
  </ul>
</div>


<script type="text/javascript">
  function faq_show(id) {
  if(scriptJquery('#'+id).css('display') == 'block') {
    scriptJquery('#'+id).css('display','none');
  } else {
    scriptJquery('#'+id).css('display','block');
  }
  }
</script>