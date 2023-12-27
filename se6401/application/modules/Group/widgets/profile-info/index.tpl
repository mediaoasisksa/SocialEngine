<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9987 2013-03-20 00:58:10Z john $
 * @author		 John
 */
?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('group.enable.rating', 1)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/rating.js'); ?>
  <script type="text/javascript">
    var modulename = 'group';
    var pre_rate = <?php echo $this->group->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var resource_id = <?php echo $this->group->group_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';
    var resource_type = 'group';
    var rating_text = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    var ratingIcon = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('group.ratingicon', 'fas fa-star'); ?>';
  </script>
<?php } ?>
<h3><?php echo $this->translate("Group Info") ?></h3>

<ul>
  <li class="group_stats_title">
    <span>
      <?php echo $this->group->getTitle() ?>
    </span>
    <?php if( !empty($this->group->category_id) &&
    ($category = $this->group->getCategory()) instanceof Core_Model_Item_Abstract &&
    !empty($category->title)): ?>
    <?php echo $this->htmlLink(array('route' => 'group_general', 'action' => 'browse', 'category_id' => $this->group->category_id), $this->translate((string)$category->title)) ?>
    <?php endif; ?>
    <?php if( '' !== ($description = Engine_Api::_()->core()->smileyToEmoticons($this->group->description)) ): ?>
    <div class="group_stats_description">
      <?php echo $this->viewMore($description, null, null, null, true) ?>
    </div>
    <?php endif; ?>
  </li>
  <li class="group_stats_info">
   <div class="group_stats_staff">
      <?php foreach( $this->staff as $info ): ?>
        <?php echo $info['user']->__toString() ?>
        <?php if( $this->group->isOwner($info['user']) ): ?>
        (<?php echo ( !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->translate('owner') ) ?>)
        <?php else: ?>
        (<?php echo ( !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->translate('officer') ) ?>)
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <ul>
      <li><i class="far fa-eye"></i><?php echo $this->translate(array('%s total view', '%s Total views', $this->group->view_count), $this->locale()->toNumber($this->group->view_count)) ?></li>
      <li><i class="far fa-user"></i><?php echo $this->translate(array('%s total member', '%s Total members', $this->group->member_count), $this->locale()->toNumber($this->group->member_count)) ?></li>
      <li><i class="far fa-clock"></i><span><?php echo $this->translate('Last updated %s', $this->timestamp($this->group->modified_date)) ?></span></li>
    </ul>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('group.enable.rating', 1)) { ?>
      <?php echo $this->partial('_rating.tpl', 'core', array('rated' => $this->rated, 'param' => 'create', 'module' => 'group')); ?>
    <?php } ?>
    <br/>
  </li>
</ul>

<script type="text/javascript">
    scriptJquery('.core_main_group').parent().addClass('active');
</script>
