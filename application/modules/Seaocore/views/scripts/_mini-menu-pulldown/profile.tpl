<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: post.tpl 2011-09-26 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $viewer = Engine_Api::_()->user()->getViewer(); ?>

<ul>
  <li><a href="<?php echo $viewer->getHref(); ?>"><?php echo $this->translate("My Profile"); ?></a></li>
</ul>
<?php echo $this->navigation()->menu()->setPartial(null)->setContainer($this->settingsNavigation)->render(); ?>
<ul>
  <?php if( $viewer->level_id == 1 || $viewer->level_id == 2 ): ?>
    <li>
      <a href="<?php echo $this->url(array(), 'admin_default', true) ?>"><?php echo $this->translate('Administrator'); ?></a>
    </li>
  <?php endif; ?>
  <li>
    <a href="<?php echo $this->url(array(), 'user_logout', true) ?>"><?php echo $this->translate('Logout'); ?></a>
  </li>
</ul>