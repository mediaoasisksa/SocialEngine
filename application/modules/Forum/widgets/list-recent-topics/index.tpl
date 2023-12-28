<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class="generic_list_wrapper">
    <ul class="generic_list_widget">
      <?php foreach( $this->paginator as $topic ):
        $user = $topic->getOwner('user');
        $forum = $topic->getParent();
        ?>
        <li>
          <div class='photo'>
            <?php echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.icon'), array('class' => 'thumb_icon')) ?>
          </div>
          <div class='info'>
            <div class='title'>
              <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            </div>
            <div class='author'>
              <span> <?php echo $this->translate('By') ?>
              <?php echo $this->htmlLink($user->getHref(), $this->translate($user->getTitle())) ?></span>
             <span> <?php echo $this->translate('In') ?>
              <?php echo $this->htmlLink($forum->getHref(), $this->translate($forum->getTitle())) ?> </span>
            </div>
            <div class='date'>
              <?php echo $this->timestamp($topic->creation_date) ?>
            </div>
            <div class='description'>
              <?php echo $this->viewMore(strip_tags($topic->getDescription()), 45) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    
    <?php //if( $this->paginator->getPages()->pageCount > 1 ): ?>
      <?php //echo $this->partial('_widgetLinks.tpl', 'core', array('url' => $this->url(array(), 'forum_general', true))); ?>
    <?php //endif; ?>
</div>
