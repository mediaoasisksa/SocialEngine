<?php
/**
 * SocialEngineAddons
 * @package    Seaocore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: not-installed.tpl 9915 2013-02-15 01:30:19Z alex $
 * @author     John
 */
?> 
<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/admin/core.js");
?>
<h2>
  <?php echo $this->translate("SocialEngineAddOns Core Plugin"); ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>   
<?php include_once APPLICATION_PATH . '/application/modules/Sitecore/views/scripts/_loginSEAO.tpl';?>

<?php if( !empty($this->seaoDetailsSession) ): ?>  
  <h3>Pending Plugin Activation</h3>
  Below is the list of plugins which are installed by you but are not activated yet. You can activate these plugins by clicking on ‘Activate Now’ button in front of the desired plugin. [Note: If ‘Activate Now’ button is not working then please click on the plugin’s name to continue the process of activation.]
  <br /><br /> 
 
<div class="seaocore_tip">
  <span>
    <?php $count = count($this->notActivatedPluginsArray); ?>
    <?php echo $this->translate(array("%s product found", "%s products found", $count), $this->locale()->toNumber($count))
    ?>
  </span>
</div> 
   
<?php if( count($this->notActivatedPluginsArray) ): ?>
  <div class="admin_table_form">  
    <table class='admin_table'>
      <thead>
        <tr> 
          <th  style='width: 1%;'>S.N</th>
          <th style='width: 70%;'>Plugin Title</th>  
          <th style="width:29%;" align="right"></th>
        </tr>
      </thead>
      <tbody> 
        <?php $i = 0; ?>
        <?php foreach( $this->notActivatedPluginsArray as $key => $title ): ?>
          <tr> 
            <td><?php echo ++$i; ?></td>
            <td><a id="<?php echo $key;?>" target = "_blank" ><?php echo $title; ?><a></td>  
            <td class="fright"><button name="<?php echo $key;?>" onclick="window.open(this.id)" class="global_settings_link" >Activate Now</button></td>
          </tr>
        <?php endforeach; ?> 
      </tbody>
    </table> 
  </div> 
<?php endif; ?>
  <br />   
  <script type="text/javascript">
    //ADD THE HREF TO THE MODULES NAME
    window.addEvent('domready', function(){
      $$('.global_settings_link').each(function(key){
        var url = en4.module.globalSettingsUrl(key.name);
        key.id = url;
        $(key.name).href = url;
      });
    });
  </script>
<?php endif; ?>