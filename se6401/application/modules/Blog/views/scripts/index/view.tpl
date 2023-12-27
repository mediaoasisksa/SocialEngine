<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Jung
 */
?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('blog.enable.rating', 1)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/rating.js'); ?>
  <script type="text/javascript">
    var modulename = 'blog';
    var pre_rate = <?php echo $this->blog->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var resource_id = <?php echo $this->blog->blog_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';
    var resource_type = 'blog';
    var rating_text = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    var ratingIcon = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('blog.ratingicon', 'fas fa-star'); ?>';
  </script>
<?php } ?>
<h2>
  <?php echo $this->blog->getTitle() ?>
</h2>
<ul class='blogs_entrylist'>
  <li>
    <div class="blog_entrylist_entry_date">
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('blog.enable.rating', 1)) { ?>
        <div class="blog_rating_view_page">
          <?php echo $this->partial('_rating.tpl', 'core', array('rated' => $this->rated, 'param' => 'create', 'module' => 'blog')); ?>
        </div>
      <?php } ?>
      <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?>
      <?php echo $this->timestamp($this->blog->creation_date) ?>
      <?php if( $this->category ): ?>
      -
      <?php echo $this->translate('Filed in') ?>
      <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $this->category->category_id?>);'><?php echo $this->translate($this->category->category_name) ?></a>
      <?php endif; ?>
      <?php if (engine_count($this->blogTags )):?>
      -
      <?php foreach ($this->blogTags as $tag): ?>
      <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
      <?php endforeach; ?>
      <?php endif; ?>
      -
      <?php echo $this->translate(array('%s view', '%s views', $this->blog->view_count), $this->locale()->toNumber($this->blog->view_count)) ?>
      <br/>
    </div>
    <div class="blog_entrylist_entry_body rich_content_body">
      <?php echo Engine_Api::_()->core()->smileyToEmoticons($this->blog->body); ?>
    </div>
  </li>
</ul>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    // Enable links
    scriptJquery('.blog_entrylist_entry_body').enableLinks();
  });

  var tagAction = function(tag_id){
    var url = "<?php echo $this->url(array('module' => 'blog','action'=>'index'), 'blog_general', true) ?>?tag_id="+tag_id;
    window.location.href = url;
  }

  scriptJquery('.core_main_blog').parent().addClass('active');
</script>
