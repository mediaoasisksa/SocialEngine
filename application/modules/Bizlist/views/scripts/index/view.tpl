<?php
   /**
    * SocialEngine
    *
    * @category   Application_Extensions
    * @package    Bizlist
    * @copyright  Copyright 2006-2020 Webligo Developments
    * @license    http://www.socialengine.com/license/
    * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
    * @author     Jung
    */
   ?>
<?php
   $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/simplelightbox/js/slick.min.js'); 
   $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/simplelightbox/js/simpleLightbox.js'); 
   $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/simplelightbox/css/simpleLightbox.css'); 
   $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/simplelightbox/css/slick.css');
?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('bizlist.enable.rating', 1)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/rating.js'); ?>
  <script type="text/javascript">
    var modulename = 'bizlist';
    var pre_rate = <?php echo $this->bizlist->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var resource_id = <?php echo $this->bizlist->bizlist_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';
    var resource_type = 'bizlist';
    var rating_text = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    var ratingIcon = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('bizlist.ratingicon', 'fas fa-star'); ?>';
  </script>
<?php } ?>
<?php if( !$this->bizlist): ?>
<?php echo $this->translate('The business you are looking for does not exist or has been deleted.');?>
<?php return; // Do no render the rest of the script in this mode
   endif; ?>
<script type="text/javascript">
   en4.core.runonce.add(function() {
       // Enable links
       scriptJquery('.bizlist_entrylist_entry_body').enableLinks();
   });
   
   var tagAction = function(tag_id){
   var url = "<?php echo $this->url(array('module' => 'bizlist','action'=>'index'), 'bizlist_general', true) ?>?tag_id="+tag_id;
   window.location.href = url;
   }
</script>
<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'bizlist', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>' style='display:none;'>
   <input type="hidden" id="tag" name="tag" value=""/>
</form>
<div class='bizlist_view'>
   <div class="bizlist_top">
      <?php if($this->bizlist->photo_id) { ?>
      <div class="bizlist_left">
         <div class="bizlists_thumbs_nav vertical slider">
            <?php $mainPhoto = 0; ?>
            <?php foreach( $this->paginator as $photo ): ?>
            <div>
               <div>
                  <div class="bizlists_thumbs_description" style="display: none;">
                     <?php if( '' != $photo->getDescription() ): ?>
                     <?php echo Engine_Api::_()->core()->smileyToEmoticons($photo->getDescription()); ?>
                     <?php endif; ?>
                  </div>
                  <?php echo $this->htmlImage($photo->getPhotoUrl(), $photo->getTitle(), array('id' => 'media_photo',"class"=>"bizlists_thumbs")); ?>
               </div>
            </div>
            <?php endforeach;?>
         </div>
         <div class="bizlists_thumbs_main_image">
            <div class="bizlists_thumbs_main">
            </div>
            <div class="bizlists_thumbs_description">
            </div>
         </div>
      </div>
      <?php } ?>
      <div class="bizlist_right">
         <h2>
            <?php echo $this->bizlist->getTitle(); ?>
            <?php if( $this->bizlist->closed == 1 ): ?>
            	<i class="bizlist_close_icon"></i>
            <?php endif;?>
         </h2>
         <div class="bizlist_entrylist_entry_date">
            <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->bizlist->getParent(), $this->bizlist->getParent()->getTitle()) ?>
            <?php echo $this->timestamp($this->bizlist->creation_date) ?>
         </div>
         <?php if ($this->bizlist->closed == 1):?>
         <div class="tip">
            <span>
            <?php echo $this->translate('This business listing has been closed by the poster.');?>
            </span>
         </div>
         <?php endif; ?>
         <h3><?php echo $this->translate('About'); ?></h3>
         <div class="rich_content_body">
         <?php echo Engine_Api::_()->core()->smileyToEmoticons(nl2br($this->bizlist->body)); ?>
       </div>
      </div>
   </div>
   <div class="bizlist_entrylist_entry_body">
      <div class="bizlists_entrylist">
        <?php echo $this->fieldValueLoop($this->bizlist, $this->fieldStructure) ?>
      </div>
      <div class="bizlists_tags">
         <?php if (engine_count($this->bizlistTags )):?>
         <?php foreach ($this->bizlistTags as $tag): ?>
         <?php if (!empty($tag->getTag()->text)):?>
         <a href='javascript:void(0);' class="tag" onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
         <?php endif; ?>
         <?php endforeach; ?>
         <?php endif; ?>
      </div>
   </div>
   <div class="bizlists_entrylist_options">
     <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('bizlist.enable.rating', 1)) { ?>
        <?php echo $this->partial('_rating.tpl', 'core', array('rated' => $this->rated, 'param' => 'create', 'module' => 'bizlist')); ?>
      <?php } ?>
      <?php if( $this->canUpload ): ?>
      <?php echo $this->htmlLink(array(
         'route' => 'bizlist_extended',
         'controller' => 'photo',
         'action' => 'upload',
         'bizlist_id' => $this->bizlist->getIdentity(),
         ), $this->translate('Add Photos')) ?>
      &nbsp;|&nbsp;
      <?php endif; ?>
      <?php if( $this->canEdit ): ?>
      <?php echo $this->htmlLink(array(
         'route' => 'bizlist_specific',
         'action' => 'edit',
         'bizlist_id' => $this->bizlist->getIdentity(),
         //'format' => 'smoothbox'
         ), $this->translate("Edit")/*, array('class' => 'smoothbox')*/); ?>
      &nbsp;|&nbsp;
      <?php endif; ?>
      <?php if( $this->canDelete ): ?>
      <?php echo $this->htmlLink(array(
         'route' => 'bizlist_specific',
         'action' => 'delete',
         'bizlist_id' => $this->bizlist->getIdentity(),
         'format' => 'smoothbox'
         ), $this->translate("Delete"), array('class' => 'smoothbox')); ?>
      &nbsp;|&nbsp;
      <?php endif; ?>
      <?php if( $this->canEdit ): ?>
      <?php if( !$this->bizlist->closed ): ?>
      <?php echo $this->htmlLink(array(
         'route' => 'bizlist_specific',
         'action' => 'close',
         'bizlist_id' => $this->bizlist->getIdentity(),
         'closed' => 1,
         'QUERY' => array(
         'return_url' => $this->url(),
         ),
         ), $this->translate('Close')) ?>
      <?php else: ?>
      <?php echo $this->htmlLink(array(
         'route' => 'bizlist_specific',
         'action' => 'close',
         'bizlist_id' => $this->bizlist->getIdentity(),
         'closed' => 0,
         'QUERY' => array(
         'return_url' => $this->url(),
         ),
         ), $this->translate('Open')) ?>
      <?php endif; ?>
      &nbsp;|&nbsp;
      <?php endif; ?>
      <?php if( $this->viewer()->getIdentity() ): ?>
      <?php echo $this->htmlLink(array(
         'module' => 'activity',
         'controller' => 'index',
         'action' => 'share',
         'route' => 'default',
         'type' => 'bizlist',
         'id' => $this->bizlist->getIdentity(),
         'format' => 'smoothbox'
         ), $this->translate("Share"), array('class' => 'smoothbox')); ?>
      &nbsp;|&nbsp;
      <?php echo $this->htmlLink(array(
         'module' => 'core',
         'controller' => 'report',
         'action' => 'create',
         'route' => 'default',
         'subject' => $this->bizlist->getGuid(),
         'format' => 'smoothbox'
         ), $this->translate("Report"), array('class' => 'smoothbox')); ?>
      &nbsp;|&nbsp;
      <?php endif ?>
      <?php echo $this->translate(array('%s view', '%s views', $this->bizlist->view_count), $this->locale()->toNumber($this->bizlist->view_count)) ?>
      
      <br/>
   </div>
</div>
<script type="text/javascript">
   scriptJquery('.core_main_bizlist').parent().addClass('active');
   scriptJquery(document).on("click",".bizlists_thumbs",function(){
     var tab = scriptJquery(this).attr("tab-index");
     var elm = scriptJquery(".bizlists_mth[tab-index="+tab+"]");
     scriptJquery(".bizlists_thumbs_main").prepend(elm.clone());
     scriptJquery(".bizlists_thumbs_main").siblings(".bizlists_thumbs_description").html(elm.find(".description").html());
     elm.remove();
     scriptJquery(".bizlists_thumbs_main a").hide();
     scriptJquery(".bizlists_mth").eq(0).show();
     $items = scriptJquery('.bizlists_mth');
     var lightbox = $items.simpleLightbox();
   });
   (function() {
     scriptJquery(".bizlists_thumbs").each(function(index){
       var src = scriptJquery(this).attr("src");
       scriptJquery(this).attr("tab-index",index);
       var description = scriptJquery(this).siblings(".bizlists_thumbs_description");
       scriptJquery(".bizlists_thumbs_main").append(`<a href="${src}" class="bizlists_mth" tab-index="${index}">
         <div class="description" style="display:none;">${description.html()}</div>
         <img src="${src}" alt="" id="media_photo" class="bizlists_mthumb"></a>`);
     });
     scriptJquery(".bizlists_thumbs").eq(0).trigger("click");
   })();
   
   scriptJquery(document).on('ready', function() {
     scriptJquery(".vertical").slick({
   vertical: true,
   infinite: false,
   draggable: false,
   arrows:true,
       slidesToShow: 4,
   slidesToScroll: 1,
   responsive: [
   {
     breakpoint: 1024,
     settings: {
       slidesToShow: 3,
       slidesToScroll: 3
     }
   },
   {
     breakpoint: 767,
     settings: {
       slidesToShow: 1,
       slidesToScroll: 1,
   vertical: false,
   arrows: false,
   dots: true
     }
   },
    ]
     });
   });
   
</script>
