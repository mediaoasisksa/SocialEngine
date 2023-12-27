<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 10217 2014-05-15 13:41:15Z lucas $
 * @author     Sami
 */
?>

<script type="text/javascript">
//<![CDATA[
  scriptJquery(document).ready(function() {
    scriptJquery('#sort').on('change', function(){
      scriptJquery(this).parent('form').submit();
    });

    var category_id = scriptJquery('#category_id');
    if( category_id != null ){
      category_id.on('change', function(){
        scriptJquery(this).parent('form').trigger("submit");
      });
    }
  })
//]]>
</script>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class='albums_manage'>
      <?php foreach( $this->paginator as $album ): ?>
        <li>
          <div class="albums_manage_photo">
            <?php echo $this->htmlLink($album->getHref(), $this->itemBackgroundPhoto($album, 'thumb.normal')) ?>
          </div>
          <div class="albums_manage_options">
            <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'editphotos', 'album_id' => $album->album_id, 'slug' => $album->getSlug()), $this->translate('Manage Photos'), array(
              'class' => 'buttonlink icon_photos_manage'
            )) ?>
            <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'edit', 'album_id' => $album->album_id, 'slug' => $album->getSlug()), $this->translate('Edit Settings'), array(
              'class' => 'buttonlink icon_photos_settings'
            )) ?>
            <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'delete', 'album_id' => $album->album_id, 'slug' => $album->getSlug(), 'format' => 'smoothbox'), $this->translate('Delete Album'), array(
              'class' => 'buttonlink smoothbox icon_photos_delete'
            )) ?>
          </div>
          <div class="albums_manage_info">
            <h3><?php echo $this->htmlLink($album->getHref(), $this->translate($album->getTitle())) ?></h3>
            <div class="albums_manage_info_photos">
              <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
             <div class="start_rating_manage">
              <?php echo $this->partial('_rating.tpl', 'core', array('item' => $album, 'param' => 'show', 'module' => 'album')); ?>
            </div> 
           </div>
            <div class="albums_manage_info_desc">
              <?php echo $album->getDescription() ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php if( $this->paginator->count() > 1 ): ?>
      <?php echo $this->paginationControl($this->paginator, null, null); ?>
    <?php endif; ?>
  <?php else: ?>
    <div class="tip">
      <span id="no-album">
        <?php echo $this->translate('You do not have any albums yet.');?>
        <?php if( $this->canCreate ): ?>
          <?php $create = $this->translate('Be the first to %1$screate%2$s one!', 
                          '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); 
          ?>
          <script type="text/javascript">
            if(!DetectMobileQuick() && !DetectIpad()){
              var create = '<?php echo $create ?>';
              var text = document.getElementById('no-album');
              text.innerHTML = text.innerHTML + create ;
            }
          </script>
        <?php endif; ?>
      </span>
    </div>	
  <?php endif; ?>


<script type="text/javascript">
  scriptJquery('.core_main_album').parent().addClass('active');
</script>
