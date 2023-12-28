<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Jung
 */
?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('employment.enable.rating', 1)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/rating.js'); ?>
  <script type="text/javascript">
    var modulename = 'employment';
    var pre_rate = <?php echo $this->employment->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var resource_id = <?php echo $this->employment->employment_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';
    var resource_type = 'employment';
    var rating_text = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    var ratingIcon = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('employment.ratingicon', 'fas fa-star'); ?>';
  </script>
<?php } ?>
<?php if( !$this->employment): ?>
<?php echo $this->translate('The employment listing you are looking for does not exist or has been deleted.');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    // Enable links
    scriptJquery('.employment_entrylist_entry_body').enableLinks();
  });
  
  var tagAction = function(tag_id){
    var url = "<?php echo $this->url(array('module' => 'employment','action'=>'index'), 'employment_general', true) ?>?tag_id="+tag_id;
    window.location.href = url;
  }
</script>

<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'employment', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>' style='display:none;'>
<input type="hidden" id="tag" name="tag" value=""/>
</form>

<div class='employment_view'>
  <div class="employment_top employment_right">
    <h2>
      <?php echo $this->employment->getTitle(); ?>
      <?php if( $this->employment->closed == 1 ): ?>
        <i class="employments_close_icon"></i>
      <?php endif;?>
    </h2>
    <div class="employment_entrylist_entry_date">
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('employment.enable.rating', 1)) { ?>
        <div class="employment_rating_view_page">
          <?php echo $this->partial('_rating.tpl', 'core', array('rated' => $this->rated, 'param' => 'create', 'module' => 'employment')); ?>
        </div>
      <?php } ?>
      <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->employment->getParent(), $this->employment->getParent()->getTitle()) ?>
      <?php echo $this->timestamp($this->employment->creation_date) ?>
    </div>
    <div class="employment_entrylist_fields">
      <?php echo $this->fieldValueLoop($this->employment, $this->fieldStructure) ?>
    </div>
    <?php if ($this->employment->closed == 1):?>
      <div class="tip">
        <span>
          <?php echo $this->translate('This employment listing has been closed by the poster.');?>
        </span>
      </div>
    <?php endif; ?>
  </div>
  <div class="employment_entrylist_entry_body">
    <h3><?php echo $this->translate('Job Description'); ?></h3>
    <div class="rich_content_body">
      <?php echo Engine_Api::_()->core()->smileyToEmoticons(nl2br($this->employment->body)); ?>
    </div>
    <div class="employments_tags">
      <?php if (engine_count($this->employmentTags )):?>
        <?php foreach ($this->employmentTags as $tag): ?>
          <?php if (!empty($tag->getTag()->text)):?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);' class="tag">#<?php echo $tag->getTag()->text?></a>&nbsp;
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
  <div class="employment_entrylis_options">
    <?php if( $this->canUpload ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'employment_extended',
    'controller' => 'photo',
    'action' => 'upload',
    'employment_id' => $this->employment->getIdentity(),
    ), $this->translate('Add Photos')) ?>
    &nbsp;|&nbsp;
    <?php endif; ?>
    <?php if( $this->canEdit ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'employment_specific',
    'action' => 'edit',
    'employment_id' => $this->employment->getIdentity(),
    //'format' => 'smoothbox'
    ), $this->translate("Edit")/*, array('class' => 'smoothbox')*/); ?>
    &nbsp;|&nbsp;
    <?php endif; ?>
    <?php if( $this->canDelete ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'employment_specific',
    'action' => 'delete',
    'employment_id' => $this->employment->getIdentity(),
    'format' => 'smoothbox'
    ), $this->translate("Delete"), array('class' => 'smoothbox')); ?>
    &nbsp;|&nbsp;
    <?php endif; ?>
    <?php if( $this->canEdit ): ?>
    <?php if( !$this->employment->closed ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'employment_specific',
    'action' => 'close',
    'employment_id' => $this->employment->getIdentity(),
    'closed' => 1,
    'QUERY' => array(
    'return_url' => $this->url(),
    ),
    ), $this->translate('Close')) ?>
    <?php else: ?>
    <?php echo $this->htmlLink(array(
    'route' => 'employment_specific',
    'action' => 'close',
    'employment_id' => $this->employment->getIdentity(),
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
    'type' => 'employment',
    'id' => $this->employment->getIdentity(),
    'format' => 'smoothbox'
    ), $this->translate("Share"), array('class' => 'smoothbox')); ?>
    &nbsp;|&nbsp;
    <?php echo $this->htmlLink(array(
    'module' => 'core',
    'controller' => 'report',
    'action' => 'create',
    'route' => 'default',
    'subject' => $this->employment->getGuid(),
    'format' => 'smoothbox'
    ), $this->translate("Report"), array('class' => 'smoothbox')); ?>
    &nbsp;|&nbsp;
    <?php endif ?>
    <?php echo $this->translate(array('%s view', '%s views', $this->employment->view_count), $this->locale()->toNumber($this->employment->view_count)) ?>
  </div>
</div>
<script type="text/javascript">
  scriptJquery('.core_main_employment').parent().addClass('active');
</script>
