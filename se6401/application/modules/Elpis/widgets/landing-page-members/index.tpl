<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 2022-06-20
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Elpis/externals/styles/styles.css'); ?>

<div class="elpis_lp_members">
  <div class="container no-padding">
    <div class="row justify-content-lg-center">
      <?php foreach( $this->paginator as $user ): ?>
      <div class="col-lg-2 col-md-3 col-6">
        <div class="elips_member_box"> <?php echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile', $user->getTitle()), array('class' => 'popularmembers_thumb')) ?>
          <div class='info'>
            <div class='name'> <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?> </div>
            <div class='friends'> <?php echo $this->translate(array('%s friend', '%s friends', $user->member_count),$this->locale()->toNumber($user->member_count)) ?> </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if( $this->paginator->getPages()->pageCount > 1 ): ?>
    <?php echo $this->partial('_widgetLinks.tpl', 'core', array(
        'url' => $this->url(array('action' => 'browse'), 'user_general', true),
        'param' => array('orderby' => 'member_count')
        )); ?>
    <?php endif; ?>
  </div>
</div>
