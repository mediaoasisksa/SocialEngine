<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: header-settings.tpl 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>

<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'header-settings'), $this->translate('Header Settings')) ?>
    </li>
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'index'), $this->translate('Main Menu Icons')) ?>
    </li>
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'manage-search'), $this->translate('Manage Search Module')) ?>
    </li>
  </ul>
</div>

<div class='clear sesbasic_admin_form company_header_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script>

window.addEvent('domready', function() {
  showSocialShare("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.show.socialshare', 1); ?>");
  showOption(2);
  showextralinks("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.heshowextralinks', 1); ?>");
});

function showextralinks(value) {
  if(value == 1) {
    $('sescompany_heshowextraphoneicon-wrapper').style.display = 'block';
    $('sescompany_heshowextraphonenumber-wrapper').style.display = 'block';
    $('sescompany_heshowextraemailicon-wrapper').style.display = 'block';
    $('sescompany_heshowextraemailnumber-wrapper').style.display = 'block';
  } else {
    $('sescompany_heshowextraphoneicon-wrapper').style.display = 'none';
    $('sescompany_heshowextraphonenumber-wrapper').style.display = 'none';
    $('sescompany_heshowextraemailicon-wrapper').style.display = 'none';
    $('sescompany_heshowextraemailnumber-wrapper').style.display = 'none';
  }
}

function showOption(value) {
  if(value == 3) {
    $('sescompany_header_fixed-wrapper').style.display = 'none';
    $('sescompany_enable_footer-wrapper').style.display = 'block';
    $('company_menu_logo_top_space-wrapper').style.display = 'block';
  } else {
    $('sescompany_header_fixed-wrapper').style.display = 'block';
    $('company_menu_logo_top_space-wrapper').style.display = 'block';
    $('sescompany_enable_footer-wrapper').style.display = 'none';
  }
}

function showSocialShare(value) {

  if(value == 1) {
    if($('sescompany_facebookurl-wrapper'))
      $('sescompany_facebookurl-wrapper').style.display = 'block';
    if($('sescompany_googleplusurl-wrapper'))
      $('sescompany_googleplusurl-wrapper').style.display = 'block';
    if($('sescompany_twitterurl-wrapper'))
      $('sescompany_twitterurl-wrapper').style.display = 'block';
    if($('sescompany_pinteresturl-wrapper'))
      $('sescompany_pinteresturl-wrapper').style.display = 'block';

  } else {
    if($('sescompany_facebookurl-wrapper'))
      $('sescompany_facebookurl-wrapper').style.display = 'none';
    if($('sescompany_googleplusurl-wrapper'))
      $('sescompany_googleplusurl-wrapper').style.display = 'none';
    if($('sescompany_twitterurl-wrapper'))
      $('sescompany_twitterurl-wrapper').style.display = 'none';
    if($('sescompany_pinteresturl-wrapper'))
      $('sescompany_pinteresturl-wrapper').style.display = 'none';
  }
}
</script>