<?php
/**
 * SocialApps.tech
 * @package    Seaocore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9915 2013-02-15 01:30:19Z alex $
 * @author     John
 */
?>
<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/admin/core.js");
?>
<h2>
  <?php echo $this->translate("SocialApps.tech Core Plugin"); ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>  

<?php include_once APPLICATION_PATH . '/application/modules/Sitecore/views/scripts/_loginSEAO.tpl';?>

<?php if( !empty($this->seaoDetailsSession) ): ?> 
<h3>Re-configure Plugin License</h3>
You can re-configure license keys of the plugins whose license key are mis-matched or are not configured for this website. Below are the list of plugins which need re-configuration of license key for this website.
<br /><br />
 
<div class="seaocore_tip">
  <span>
    <?php $count = count($this->reconfigurableModules); ?>
    <?php echo $this->translate(array("%s product mismatching configuration", "%s products mismatching configuration", $count),
        $this->locale()->toNumber($count)) ?>
  </span>
  <?php if(!empty($count)): ?>
    <button type='button' id = "configure_selected" class = "configure_selected" style="float: right;"> Re-configure License Keys of Selected Plugins </button>
  <?php endif; ?>
</div> 
 
<div style="display: none;" id="temporary_page_result" ></div>
<div style="display: none;" id="temporary_form_result" ></div>
<div id="seaocore_light_box" class="seaocore_downloading_white_content">
    <span id="seaocore_loading_text"></span>
    <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/admin/loading1.gif" />
</div>
<div id="seaocore_fade_overlay" class="seaocore_downloading_black_overlay"></div>

<?php if( count($this->reconfigurableModules) ): ?>
  <div class="admin_table_form" style="clear: both" >  
    <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>">  
      <table style="width: 100%" class='admin_table'>
        <thead>
          <tr>
            <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class="select_all" ></th>
            <th style='width: 60%;'>Plugin Name</th>
            <th>Valid License</th>
            <th style='width: 20%;'></th>
          </tr>
        </thead>
        <tbody> 
          <?php foreach( $this->reconfigurableModules as $name => $title ):?>
            <?php $license = $this->purchasedPlugins[$name]['license']; ?>
            <tr>
              <td><input <?php (empty($license)) ? "disabled": "";?> name='<?php echo $name;?>' value = "" type='checkbox' class='checkbox'></td>
              <td><a id="<?php echo $name;?>" class="global_settings_link" target = "_blank" ><?php echo $title; ?><a></td>
              <td>
                <?php if(!empty($license)): ?>
                  <input disabled id = 'licence_<?php echo $name;?>' class = "licence" value = "<?php echo $license; ?>" type='text'>
                <?php endif;?>
              </td>
              <td style="text-align: right;" > 
                <?php if(!empty($license)): ?>
                  <button type='button' id="configure_<?php echo $name;?>" onclick="configureModuleLicence(this.value)" value = '<?php echo $name;?>'><?php echo "Re-configure";?></button>
                <?php endif;?>
                <span id="loading_image_<?php echo $name;?>" style="display: none;"><img src="application/modules/Seaocore/externals/images/core/loading.gif" ></span> 
                <span id="completion_message_<?php echo $name;?>"><?php echo (empty($license)) ? '<span style = "color : red;">License Not Found</span>': "";?></span>
              </td> 
            </tr>
          <?php endforeach; ?> 
        </tbody>
      </table>
    </form>
  </div> 
  <br / > 
  <div class='buttons'> 
    <button type='button' id = "configure_selected" class = "configure_selected" style="float: right;"> Re-configure License Keys of Selected Plugins</button>
  </div>
<?php endif; ?>

<script type="text/javascript">
  var onlyUpdateKeysPlugunList = <?php echo $this->onlyUpdateKeysPlugunList; ?>;
  function showLoadingImage(flag) {
    if(flag == true) {
      scriptJquery('#seaocore_light_box').css('display','block');
      scriptJquery('#seaocore_fade_overlay').css('display','block');
      scriptJquery('#seaocore_loading_text').html("Configuring Licences! please wait.."); 
    } else {
      scriptJquery('#seaocore_light_box').css('display','none');
      scriptJquery('#seaocore_fade_overlay').css('display','none');
    }
  } 
  function getDataString(data) {
    var params = "";
    Object.keys(data).map(function(k){
      if(Array.isArray(data[k])){
        for(i = 0; i < data[k].length; i++) {
          params = params + k+"="+data[k][i]+"&"; 
        }
      } else {
        params = params + k+"="+data[k]+"&";  
      } 
    });
    return params;
  }
  function hideLoadingImages(lastItem, name) {
    scriptJquery('#loading_image_'+name).hide();
    if(lastItem == name) {
      showLoadingImage(false);
    }
  } 
  function addSubmissionMessage(name, success) {
    scriptJquery('#completion_message_'+ name).addClass("error_configure");
    scriptJquery('#completion_message_'+ name).html("Failed to Reconfigure");
    scriptJquery('#configure_'+ name).hide();
    if(success) {
      scriptJquery('#completion_message_'+ name).addClass("success_configure");
      scriptJquery('#completion_message_'+ name).html("Successfully Reconfigured");
      scriptJquery('#multimodify_form').find("input[name="+ name +"]")[0].disabled = true;
    } 
  } 
  function changeLicencekey(settingsPageElements, name) {
    if(settingsPageElements.find('#'+ name +'_lsettings')[0] && scriptJquery('#licence_'+ name).length) {
      settingsPageElements.find('#'+ name +'_lsettings').val(scriptJquery('#licence_'+ name).val());
    }
  }
  function configureModuleLicence(name, lastItem = false) { 

    if(!lastItem) {
      lastItem = name;
    }
    if(scriptJquery('#licence_' + name).length &&( scriptJquery('#licence_' + name).val() == false || scriptJquery('#licence_' + name).val().length == 0)&& !scriptJquery('#licence_' + name).hasClass('licence_error')) {
      //scriptJquery('#licence_' + name).addClass('licence_error');
      scriptJquery('#configure_' + name).disabled = "disabled";
      return;
    } 
    if( scriptJquery("#loading_image_"+name) && scriptJquery("#loading_image_"+name).css('display') == 'inline-block') {
      return;
    }

    if(inArray(name, onlyUpdateKeysPlugunList)) {
      var url = en4.core.baseUrl + "admin/sitecore/plugin-manage/save-licence";
      var request = scriptJquery.ajax({
        'dataType' : 'json',
        'url' : url,
        'method':'post',
        'data' : {
          'moduleName': name,
          'moduleLicenceKey' : scriptJquery('#licence_'+ name).val()
        },
        beforeSend : function() { 
          scriptJquery("#loading_image_"+name).css('display','inline-block'); 
        },
        success : function(responseJSON) { 
          if(responseJSON.status == true) {
            addSubmissionMessage(name, true); 
          } else {
            addSubmissionMessage(name, false); 
          }
          hideLoadingImages(lastItem, name); 
        },
        failure : function() {
          addSubmissionMessage(name, false); 
          hideLoadingImages(lastItem, name); 
        }
      });
      // request.send();
    } else {
      var url =  en4.module.globalSettingsUrl(name);
      var request = scriptJquery.ajax({
        'url' : url,
        'dataType' : 'html',
        'method':'get',
        'data' : { },
        beforeSend : function() { 
          scriptJquery("#loading_image_"+name).css('display','inline-block'); 
        },
        success : function(responseHTML) { 

          scriptJquery('#temporary_page_result').html(responseHTML);
          var settingsPageData = scriptJquery('#temporary_page_result');  
          //CHNAGE THE OLD LICENCE KEY WITH LATEST ONE
          if(settingsPageData.find('#'+ name +'_lsettings')[0] && scriptJquery('#licence_'+ name).val()) {

              changeLicencekey(settingsPageData, name); 
              if((name == "sitevideo" || name == "sitevideointegration") && settingsPageData.find('#sitevideointegration_lsettings')[0]) {
                  changeLicencekey(settingsPageData, "sitevideo"); 
                  changeLicencekey(settingsPageData, "sitevideointegration"); 
              }
              if((name == "document" || name == "documentintegration") && settingsPageData.find('#documentintegration_lsettings')[0]) {
                  changeLicencekey(settingsPageData, "document"); 
                  changeLicencekey(settingsPageData, "documentintegration"); 
              }
              if(inArray(name, ['siteevent', 'siteeventticket', 'siteeventrepeat', 'siteeventemail', 'siteeventinvite', 'siteeventdocument'])) {
                changeLicencekey(settingsPageData, "siteevent"); 
                changeLicencekey(settingsPageData, "siteeventticket"); 
                changeLicencekey(settingsPageData, "siteeventrepeat"); 
                changeLicencekey(settingsPageData, "siteeventemail"); 
                changeLicencekey(settingsPageData, "siteeventinvite"); 
                changeLicencekey(settingsPageData, "siteeventdocument"); 
              }
              if(inArray(name, ['sitereview', 'sitereviewlistingtype', 'sitereviewpaidlisting'])) {
                  changeLicencekey(settingsPageData, "sitereview"); 
                  changeLicencekey(settingsPageData, "sitereviewlistingtype"); 
                  changeLicencekey(settingsPageData, "sitereviewpaidlisting"); 
              }
          }
          //SUBMIT THE FORM WITH LATEST LICENCE KEY 
          var globalSettingForm = settingsPageData.find('form');
          if(globalSettingForm) {
            var params = '';
            var data = globalSettingForm.serialize();
            var url = globalSettingForm.attr('action');
            // params = getDataString(data);
            var request = scriptJquery.ajax({
              'url' : url,
              'dataType' : 'html',
              'method':'post',
              'data' : data,
              success : function(responseHTML) {
                    scriptJquery('#temporary_form_result').html(responseHTML);
                    if(scriptJquery('#temporary_form_result').find('.form-notices').length > 0) {
                      addSubmissionMessage(name, true); 
                    } else if(scriptJquery('#temporary_form_result').find('.form-errors').length > 0) {
                      addSubmissionMessage(name, false); 
                    } else {
                      if(scriptJquery('#temporary_form_result').find('.global_form').length > 0) {
                        //form submimtted but message not shown of success
                        addSubmissionMessage(name, true); 
                      } else {
                        addSubmissionMessage(name, true); 
                      }
                    }
                    hideLoadingImages(lastItem, name); 
              },
              failure : function() {
                  addSubmissionMessage(name, false); 
                  hideLoadingImages(lastItem, name); 
              }
            });
            // request.send(params);  
          } else {
              addSubmissionMessage(name, false); 
              hideLoadingImages(lastItem, name); 
          }
        },
        failure : function() {
            addSubmissionMessage(name, false); 
            hideLoadingImages(lastItem, name); 
        }
      });
      // request.send();
    } 
  }

  scriptJquery('.configure_selected').on('click', function(){  
    modules = getCheckedModules();
    if(modules.length == 0) {
      scriptJquery('.message_box').css('display', 'inline-block');
      return;
    }
    for (var i = 0; i < modules.length; i++) {
      if( scriptJquery('#licence_' + modules[i]) && (scriptJquery('#licence_' + modules[i]).val() == 0 || scriptJquery('#licence_' + modules[i]).val() == false)) {
        //scriptJquery('#licence_' + modules[i]).addClass('licence_error');
        scriptJquery('#configure_' + modules[i]).disabled = "disabled";
        continue;
      }
      if(i == 0) {
        showLoadingImage(true);
      }
      configureModuleLicence(modules[i], modules[modules.length - 1]); 
    }  
  });

  function getCheckedModules() {
    var modules = new Array();
    var inputs = scriptJquery('#multimodify_form').find("input[class=checkbox]:checked");
    for (i = 0; i < inputs.length; i++) {
       modules[i] = inputs[i].name;
    }
    return modules;
  }

  function selectAll() {
    var i; 
    var inputs = scriptJquery('#multimodify_form').find("input[class=checkbox]");
    for (i = 0; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = scriptJquery('#multimodify_form').find("input[class=select_all]")[0].checked;
      }
    }
  } 

  scriptJquery('.checkbox').on('click', function(){
    if(this.checked){
      scriptJquery('.message_box').css('display', 'none');
    }  
  }); 
  //ADD THE HREF TO THE MODULES NAME
  window.addEventListener('DOMContentLoaded', function(){
    scriptJquery('.global_settings_link').each(function(key){
      var url = en4.module.globalSettingsUrl(this.id);
      this.href = url;
    });
  });
</script>

<style type="text/css">
  .licence_error {
    border-color: red !important;
  }
  .error_configure {
    color: red !important;
  }
  .success_configure {
    color: rgb(115, 244, 54) !important;
  }
  #seaocore_light_box img{
    width: 52%;
  }
</style>  
 <?php endif; ?>