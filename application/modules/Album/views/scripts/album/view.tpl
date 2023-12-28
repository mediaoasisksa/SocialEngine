<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Sami
 */
?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('album.enable.rating', 1)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/rating.js'); ?>
  <script type="text/javascript">
    var modulename = 'album';
    var pre_rate = <?php echo $this->album->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var resource_id = <?php echo $this->album->album_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';
    var resource_type = 'album';
    var rating_text = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    var ratingIcon = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('album.ratingicon', 'fas fa-star'); ?>';
  </script>
<?php } ?>
<script type="text/javascript">
  <?php if( $this->mine || $this->canEdit ): ?>
    var SortablesInstance;
    en4.core.runonce.add(function() {
      scriptJquery('.thumbs_nocaptions > li').addClass('sortable');
      SortablesInstance = scriptJquery('.thumbs_nocaptions').sortable({
        stop: function( event, ui ) {
          var ids = [];
          scriptJquery('.thumbs_nocaptions > li').each(function(e) {
            var el = scriptJquery(this);
            ids.push(el.attr('id').match(/\d+/)[0]);
          });
          // Send request
          var url = '<?php echo $this->url(array('action' => 'order')) ?>';
          scriptJquery.ajax({
              url : url,
              dataType : 'json',
              data : {
                  format : 'json',
                  order : ids
              }
          });
        }
      });
    });
  <?php endif ?>
    
  function orderChange(value) {
    var url = '<?php echo $this->album->getHref(); ?>';
    if('<?php echo $this->page; ?>') {
      url  = url + '/page/'+ '<?php echo $this->page; ?>';
    }
    url  = url + '/sorting/'+ scriptJquery( "#album_order option:selected").val();
    window.location.href = url;
  }
</script>

<?php if( '' != trim($this->album->getDescription()) ): ?>
  <p><?php echo Engine_Api::_()->core()->smileyToEmoticons($this->album->getDescription()); ?></p>
<?php endif ?>
<div class="album_options">
   <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('album.enable.rating', 1)) { ?>
    <?php echo $this->partial('_rating.tpl', 'core', array('rated' => $this->rated, 'param' => 'create', 'module' => 'album')); ?>
  <?php } ?>

  <?php if( $this->mine || $this->canEdit ): ?>
    <?php echo $this->htmlLink(array('route' => 'album_general', 'action' => 'upload', 'album_id' => $this->album->album_id), $this->translate('Add More Photos'), array(
    'class' => 'buttonlink icon_photos_new'
    )) ?>
    <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'editphotos', 'album_id' => $this->album->album_id), $this->translate('Manage Photos'), array(
    'class' => 'buttonlink icon_photos_manage'
    )) ?>
    <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'edit', 'album_id' => $this->album->album_id), $this->translate('Edit Settings'), array(
    'class' => 'buttonlink icon_photos_settings'
    )) ?>
    <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'delete', 'album_id' => $this->album->album_id, 'format' => 'smoothbox'), $this->translate('Delete Album'), array(
    'class' => 'buttonlink smoothbox icon_photos_delete'
    )) ?>
  <?php endif;?>
      
  <br/>
  <select name="sorting" id="album_order" onchange="orderChange(this.value);">
    <option value="newest" <?php if($this->sorting == 'newest') { ?> selected="selected" <?php } ?> ><?php echo $this->translate("Newest"); ?></option>
    <option value="oldest" <?php if($this->sorting == 'oldest') { ?> selected="selected" <?php } ?>><?php echo $this->translate("Oldest"); ?></option>
    <option value="ASC" <?php if($this->sorting == 'ASC') { ?> selected="selected" <?php } ?>><?php echo $this->translate("Set Order"); ?></option>
  </select>
</div>

<div class="container no-padding">
  <div class="row thumbs_nocaptions">
    <?php foreach( $this->paginator as $photo ): ?>
      <div class="col-lg-3 col-6 grid_outer"  id="thumbs-photo-<?php echo $photo->photo_id ?>">
        <div class="grid_wrapper albums_grid">
            <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
            <?php echo $this->itemBackgroundPhoto($photo, 'thumb.normal')?>
            <div class="info_stat_grid">
              <?php if( $photo->like_count > 0 ) :?>
              <span>
                    <i class="fa fa-thumbs-up"></i>
                <?php echo  $this->locale()->toNumber($photo->like_count) ?>
                  </span>
              <?php endif; ?>
              <?php if( $photo->comment_count > 0 ) :?>
              <span>
                    <i class="fa fa-comment"></i>
                <?php echo  $this->locale()->toNumber($photo->comment_count) ?>
                  </span>
              <?php endif; ?>
              <?php if( $photo->view_count > 0 ) :?>
              <span class="album_view_count">
                    <i class="fa fa-eye"></i>
                <?php echo  $this->locale()->toNumber($photo->view_count) ?>
                  </span>
              <?php endif; ?>
            </div>
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('album.enable.rating', 1)) { ?>
              <div class="browse_photos_rating">
                <?php echo $this->partial('_rating.tpl', 'core', array('item' => $photo, 'param' => 'show', 'module' => 'album_photo')); ?>
              </div>
            <?php } ?>
          </a>
      </div>
     </div>
    <?php endforeach;?>
  </div>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
</div>
<script type="text/javascript">
    scriptJquery('.core_main_album').parent().addClass('active');
</script>
