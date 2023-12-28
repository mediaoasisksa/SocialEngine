<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    seaocore
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');?>
<div id="seao_layout_width_<?php echo $this->identity ?>"> </div>
<script type="text/javascript"> 
  var layoutWidth = '<?php echo $this->layoutWidth ?>';
  var layoutWidthWidget = scriptJquery('#seao_layout_width_<?php echo $this->identity ?>');
  if(layoutWidthWidget) {
    if(layoutWidthWidget.parent().parent('.layout_right')) {
      layoutWidthWidget.parent().parent('.layout_right').css('width', layoutWidth);
      layoutWidthWidget.parent().css('display', 'none');
    } 
    if(layoutWidthWidget.parent().parent('.layout_left')) {
      layoutWidthWidget.parent().parent('.layout_left').css('width', layoutWidth);
      layoutWidthWidget.parent().css('display', 'none');
    } 
    if(layoutWidthWidget.parent().parent('.layout_middle')) {
      layoutWidthWidget.parent().parent('.layout_middle').css('width', layoutWidth);
      layoutWidthWidget.parent().css('display', 'none');
    }
  }
</script>
