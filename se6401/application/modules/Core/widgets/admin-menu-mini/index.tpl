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
<?php if (!empty($this->code)): ?>
	<div class="admin_maintenance_mode">
		<?php echo $this->translate('Your community is currently in maintenance mode and can only be accessed with a passcode: %s', "{$this->code}") ?>
		<span id="exit-maintenance-mode">
			[<a href='javascript:void(0);' onClick='exit_maintenance_mode();'><?php echo $this->translate('exit maintenance mode'); ?></a>]
		</span>
	</div>

	<script type="text/javascript">
	//<![CDATA[
	var exit_maintenance_mode = function(){
		scriptJquery('#exit-maintenance-mode').hide();
		scriptJquery.ajax({
			url: '<?php echo $this->url(array('controller'=>'settings', 'action'=>'general'), 'admin_default') ?>',
			method: 'post',
			data : {
				maintenance_mode:0,
			},
			success: function(response){
				window.location.href=window.location.href;
			},
			error: function(xhr){
				scriptJquery('#exit-maintenance-mode').show();
			}
		});
	}
	//]]>

	</script>
<?php endif; ?>

<div id='global_header_mini_menu_wrapper'>
  <div id='global_header_right_menu'>
		<div id='global_header_mini_left'>
			<button class="toggle_button"><i class="fas fa-bars"></i> </button>
			<?php echo $this->content()->renderWidget('core.admin-menu-logo') ?>
			<?php if( $this->viewer()->getIdentity() ) : ?>
				<span>
					<?php echo $this->itemPhoto($this->viewer(), 'thumb.icon') ?>
					<?php echo $this->translate("Welcome back %s", $this->viewer()->getTitle()) ?>
				</span>
				&nbsp;
			<?php endif; ?>
    </div>
    <div id='global_header_mini_right'>
      <span>
        <?php echo $this->translate("Welcome back %s", $this->viewer()->getTitle()) ?>
      </span>
      <a href='<?php echo $this->url(array(), 'default', true) ?>' target="_blank" class='back_to_network'>  <i class="fas fa-share"></i> <?php echo $this->translate("back to network") ?></a>
      &nbsp;
      <a href='<?php echo $this->url(array(), 'user_logout') ?>' class='sign_out'><i class="fas fa-sign-out-alt"></i> <?php echo $this->translate("sign out") ?></a>
    </div>
  </div>
</div>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.menutype', 'vertical') == 'horizontal') { ?>
  <div class="main_menu_horizontal">
    <?php echo $this->content()->renderWidget('core.admin-menu-main') ?>
  </div>
  <div class="global_header_left">
    <div class="toggle_cross_button"> <i class="fas fa-times"></i> </div>
    <?php echo $this->content()->renderWidget('core.admin-menu-logo') ?>
    <?php echo $this->content()->renderWidget('core.admin-menu-main') ?>
  </div>
<?php } ?>
<script  type="text/javascript">
scriptJquery(".toggle_button").click(function(){
 scriptJquery(".global_header_left").toggleClass("nav-show");
});

scriptJquery(document).click(function(event) {
 if (!scriptJquery(event.target).closest(".toggle_button , .layout_core_admin_menu_main > ul > li > a").length) {
   scriptJquery("html").find(".global_header_left").removeClass("nav-show");
    scriptJquery("html").find("body").removeClass("nav-show");
 }
});

//Menu Toggle
scriptJquery(document).ready(function(){
	scriptJquery(".admin_menu_setting_button a").click(function(){
		scriptJquery(".admin_menu_setting").toggleClass("active");
	});
});

// Submenu Dropdown
scriptJquery(document).ready(function(){
	var menuElement = scriptJquery('.global_header_left').find('.menu_core_admin_main').parent();
	menuElement.addClass('menu_link');
	var submenu = scriptJquery('.main_menu_submenu > li > .active');
	submenu.closest('.menu_link').addClass('active');
	submenu.closest('.menu_link').children().eq(0).addClass('active');
	menuElement.find('ul').hide();
	if(menuElement.find('ul').length)
		menuElement.find('a').addClass('toggled_menu');
	scriptJquery('.navigation').children().eq(0).find ('a').removeClass('toggled_menu')
	scriptJquery('.menu_link.active').find('ul').show();
});
scriptJquery(document).on('click', '.toggled_menu', function () {
	if(scriptJquery(this).hasClass('active')){
		scriptJquery(this).removeClass('active')
		scriptJquery(this).parent().find('ul').slideUp()
	}
	else{
		scriptJquery(this).addClass('active')
		scriptJquery(this).parent().find('ul').slideToggle()
	}
});
</script>
