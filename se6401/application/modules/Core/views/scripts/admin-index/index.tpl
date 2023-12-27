<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<?php $menuType = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.menutype', 'vertical'); ?> 
<div class="admin_home_wrapper">
  <div class="admin_home_right">
     <div class="admin_menu_setting">
      <div class="admin_menu_setting_inner">
        <div class="admin_menu_setting_inner_right">
          <h3><?php echo $this->translate("Menu Type "); ?><i class="fas fa-angle-double-right"></i></h3>
          <ul>
            <li>
              <input onclick="menuType('horizontal')" name="menutype" type="radio" id="f-option" name="selector" value="horizontal" <?php if($menuType == 'horizontal') { ?> checked ="checked" <?php } ?> >
              <label for="f-option"><?php echo $this->translate("Horizontal"); ?></label>
              <div class="check"></div>
            </li>
            <li>
              <input onclick="menuType('vertical')" name="menutype" type="radio" id="s-option" name="selector" value="vertical" <?php if($menuType == 'vertical') { ?> checked ="checked" <?php } ?>>
              <label for="s-option"><?php echo $this->translate("Vertical"); ?></label>
              <div class="check"><div class="inside"></div>
            </div>
            </li>
          </ul>
        </div>
      </div>
     </div>
    <?php echo $this->content()->renderWidget('core.admin-statistics') ?>
    <?php echo $this->content()->renderWidget('core.admin-environment') ?>
  </div>
  <div class="admin_home_middle">
    <?php echo $this->content()->renderWidget('core.admin-dashboard') ?>
    <?php echo $this->content()->renderWidget('core.admin-content-show') ?>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.newsupdates')) { ?>
      <?php echo $this->content()->renderWidget('core.admin-news') ?>
    <?php } ?>
  </div>
</div>
<script>
  function menuType(value) {
    var checkBox = document.getElementById("newsupdates");
    (scriptJquery.ajax({
      method: 'post',
      dataType: 'json',
      url: en4.core.baseUrl + 'core/index/adminmenutype/',
      data: {
        format: 'json',
        //showcontent: checkBox.checked,
        value: value,
      },
      success : function(responseHTML) {
        location.reload();
      }
    }));
    return false;
  }
</script>
