<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: list.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */
?>
<div class="layout_middle">
 <div class="generic_layout_container">
<h2>  
  <?php echo $this->event->__toString()." ".$this->translate("&#187; Photos") ?>
</h2>

<?php if( $this->canUpload ): ?>
  <div class="event_photos_list_options">
    <?php echo $this->htmlLink(array(
        'route' => 'event_extended',
        'controller' => 'photo',
        'action' => 'upload',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Upload Photos'), array(
        'class' => 'buttonlink icon_event_photo_new'
    )) ?>
  </div>
<?php endif; ?>

<div class='layout_middle'>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
    <br />
  <?php endif; ?>
  <ul class="thumbs thumbs_nocaptions thumbs_event grid_wrapper">
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <?php echo $this->itemBackgroundPhoto($photo, 'thumb.main')?>          
        </a>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
</div>
</div>
</div>
