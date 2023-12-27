<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9905 2013-02-14 02:46:28Z alex $
 * @author     John
 */
?>
<div class="admin_manage_news_checkbox">
  <div class="admin_manage_news"><input type="checkbox" id="newsupdates" onclick="showHide(2)" <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.newsupdates')) { ?> checked <?php } ?>> <?php echo $this->translate("Show News & Updates"); ?></div>
</div>
<script>
  function showHide(value) {
    var checkBox = document.getElementById("newsupdates");
    (scriptJquery.ajax({
      method: 'post',
      dataType: 'json',
      url: en4.core.baseUrl + 'core/index/showadmincontent/',
      data: {
        format: 'json',
        showcontent: checkBox.checked,
        value: value,
      },
      success : function(responseHTML) {
        location.reload();
      }
    }));
    return false;
  }
</script>
