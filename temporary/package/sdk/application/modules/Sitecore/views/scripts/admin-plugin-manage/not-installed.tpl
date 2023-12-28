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
<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/admin/core.js');?>
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
  <h3>Pending Plugin Installations</h3>
  Below are the list of plugins which are purchased by you but are not yet installed on this website. You can install these plugins by clicking on ‘Install’ button visible along with the plugin name.
  <br /><br />

  <?php if(empty($this->error)): ?>
   
<div class="seaocore_tip">
  <span>
    <?php $count = count($this->notInstalledPluginsArray); ?>
    <?php echo $this->translate(array("%s product found", "%s products found", $count), $this->locale()->toNumber($count))
    ?>
  </span>
</div>  

  <div id="seaocore_light_box" class="seaocore_downloading_white_content">
    <span id="seaocore_loading_text"></span>
    <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/admin/loading1.gif" />
  </div>
  <div id="seaocore_fade_overlay" class="seaocore_downloading_black_overlay"></div>
<?php if( $count ): ?>
  <div class="admin_table_form">  
    <table class='admin_table'>
      <thead>
        <tr>
          <th style='width: 1%;'><input type="checkbox" class="seaocore_multi-upgrade_checkall" name="multi-upgrade" onclick="checkUncheckSeaoAll(this)"></th>
          <th style='width: 90%;'><?php echo $this->translate("Plugin Name") ?></th> 
          <th style='width: 10%; text-align: center;'></th>
        </tr>
      </thead>
      <tbody>  
          <?php foreach( $this->notInstalledPluginsArray as $item ): ?>
            <tr>
              <?php
              if( isset($item['ptype']) && ($item['ptype'] == 'siteandroidapp') )
                $item['ptype'] = 'sitemobileandroidapp';

              if( isset($item['ptype']) && ($item['ptype'] == 'siteiosapp') )
                $item['ptype'] = 'sitemobileiosapp';
              $url = $this->url(array('module' => 'sitecore', 'controller' => 'settings', 'action' => 'upgrade-plugin', 'name' => @base64_encode($item['name']), 'version' => $item['product_version'], 'ptype' => $item['ptype'], 'key' => $item['key'], 'title' => str_replace("/", "_", @base64_encode($item['title'])), 'calling' => 'seaocore'), 'admin_default', true);
              $title = $this->translate("Install Plugin %s", $item['title']);
              ?>
              <td> <input id="<?php echo $item['ptype']; ?>" type="checkbox" class="seaocore_multi-upgrade" name="multi-upgrade" data-url='<?php echo $url; ?>'></td>
              <td><?php echo $item['title']; ?></td>  
              <td style="text-align: right;"><button title="<?php echo $title; ?>" onclick="sendInstallRequest('<?php echo $url; ?>', '<?php echo $item['ptype']; ?>')">Install</button>  
              </td>
            </tr>
          <?php endforeach; ?> 
      </tbody>
    </table> 
    <button title="Install Selected" style="float: right; margin-top: 20px" onclick="installSelectedPlugins()">Install All</button>  
  </div> 
<?php endif; ?>
  <div id="seaocore_light" class="seaocore_downloading_white_content">
    <?php echo $this->translate('Downloading packages! please wait '); ?>
    <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/loadings.gif" alt="" />
  </div> 
  <div id="seaocore_fade" class="seaocore_downloading_black_overlay"></div>
  <br />  
  <?php else : ?>
    <div class="seaocore_tip">
      <span>
        Not able to load plugin information
      </span>
    </div>
  <?php endif; ?>
<script type="text/javascript">
  
  var selectedItemLength = 0; 
  function addMessage(message) {
    $('seao_user_auth_details').prepend(new Element('span', {'id': 'seao_message', 'text': message, 'style': 'color: red'}));
  } 
  function installSelectedPlugins() {
    var ready = confirm("are you ready to upgarde?");
    if (ready == true) {
      elements = $$('.seaocore_multi-upgrade:checked');
      if (elements.length < 1)
        return;
      selectedItemLength = elements.length - 1;
      item = elements[selectedItemLength];
      sendInstallRequest(item.get('data-url'), item.id);
    }
  }
  function sendInstallRequest(url, itemName) {  
    var request = (new Request.JSON({
      'url': url,
      'method': 'post',
      'data': {
        'format': 'json',
      },
      'onRequest' : function() {
        $('seaocore_light').style.display = 'block';
        $('seaocore_fade').style.display = 'block';
      },
      'onComplete': function (responseJSON) {
        if (selectedItemLength < 1) { 
          window.location = en4.core.baseUrl + 'install/manage/select';
        } else {
          selectedItemLength--;
          item = elements[selectedItemLength];
          sendInstallRequest(item.get('data-url'), item.id);
        }
      }
    }));
    request.send(); 
  }  
</script>
<?php endif; ?>