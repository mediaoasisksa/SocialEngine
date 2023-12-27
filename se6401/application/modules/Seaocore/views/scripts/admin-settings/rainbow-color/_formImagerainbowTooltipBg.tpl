<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowTooltipBg.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/Mini_Color_Picker/jquery.minicolors.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/Mini_Color_Picker/jquery.minicolors.css');
?>

<script type="text/javascript">
   window.addEventListener('DOMContentLoaded', function(){
    scriptJquery('#myRainbow1').minicolors({
     format:'hex',
     defaultValue:'<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#000000') ?>',
     change: function(color) {
        scriptJquery('#sitetagcheckin_tooltip_bgcolor').val(this.value);
      }
    });
});
</script>

<?php
echo '
  <div id="sitetagcheckin_tooltip_bgcolor-wrapper" class="form-wrapper">
    <div id="sitetagcheckin_tooltip_bgcolor-label" class="form-label">
      <label for="sitetagcheckin_tooltip_bgcolor" class="optional">
              ' . $this->translate('Tooltip Background Color') . '
      </label>
    </div>
    <div id="sitetagcheckin_tooltip_bgcolor-element" class="form-element">
      <p class="description">' . $this->translate('Select a background color for the tooltips that are displayed on clicking location markers on maps. (Click on the rainbow below to choose your color.)') . '</p>
      <input name="sitetagcheckin_tooltip_bgcolor" id="sitetagcheckin_tooltip_bgcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#ffffff') . ' type="text">
      <input name="myRainbow1" id="myRainbow1">
    </div>
  </div>
'
?>
