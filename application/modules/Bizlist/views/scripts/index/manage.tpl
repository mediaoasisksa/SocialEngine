<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
  var pageAction =function(page){
    scriptJquery('#page').val(page);
    scriptJquery('#filter_form').trigger("submit");
  }

  var searchBizlists = function() {
    scriptJquery('#filter_form').trigger("submit");
  }

  en4.core.runonce.add(function(){
    scriptJquery('#filter_form input[type=text]').each(function(f) {
        if (f.value == '' && f.id.match(/\min$/)) {
            //new OverText(f, {'textOverride':'min','element':'span'});
            //f.set('class', 'integer_field_unselected');
        }
        if (f.value == '' && f.id.match(/\max$/)) {
            //new OverText(f, {'textOverride':'max','element':'span'});
            //f.set('class', 'integer_field_unselected');
        }
    });
  });

  scriptJquery(window).on('onChangeFields', function() {
    var firstSep = scriptJquery('li.browse-separator-wrapper').eq(0);
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.next();
      if( nextEl.hasClass('browse-separator-wrapper')) {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && ( nextEl.css('display') == 'none' );
      }
    } while(nextEl);
      if(lastSep) {
        lastSep.css('display', (allHidden ? 'none' : ''));
      }
  });
</script>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    // Enable links
    scriptJquery('.bizlists_browse_info_blurb').enableLinks();
  });
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    //'topLevelId' => (int) @$this->topLevelId,
    //'topLevelValue' => (int) @$this->topLevelValue
  ))
?>

  <?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have already created the maximum number of listings allowed. If you would like to create a new listing, please delete an old one first.');?>
      </span>
    </div>
    <br/>
  <?php endif; ?>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="bizlists_manage list_wrapper">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='bizlists_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.normal')) ?>
          </div>
          <div class='bizlists_options'>
            <?php echo $this->htmlLink(array(
              'route' => 'bizlist_specific',
              'action' => 'edit',
              'bizlist_id' => $item->getIdentity(),
            ), $this->translate('Edit Listing'), array(
              'class' => 'buttonlink icon_bizlist_edit'
            )) ?>
            
            <?php if( $this->allowed_upload ): ?>
              <?php echo $this->htmlLink(array(
                  'route' => 'bizlist_extended',
                  'controller' => 'photo',
                  'action' => 'upload',
                  'bizlist_id' => $item->getIdentity(),
                ), $this->translate('Add Photos'), array(
                  'class' => 'buttonlink icon_bizlist_photo_new'
              )) ?>
            <?php endif; ?>

            <?php if( !$item->closed ): ?>
              <?php echo $this->htmlLink(array(
                'route' => 'bizlist_specific',
                'action' => 'close',
                'bizlist_id' => $item->getIdentity(),
                'closed' => 1,
              ), $this->translate('Close Listing'), array(
                'class' => 'buttonlink icon_bizlist_close'
              )) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array(
                'route' => 'bizlist_specific',
                'action' => 'close',
                'bizlist_id' => $item->getIdentity(),
                'closed' => 0,
              ), $this->translate('Open Listing'), array(
                'class' => 'buttonlink icon_bizlist_open'
              )) ?>
            <?php endif; ?>
            
            <?php echo $this->htmlLink(array('route' => 'bizlist_specific', 'action' => 'delete', 'bizlist_id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Listing'), array(
              'class' => 'buttonlink smoothbox icon_bizlist_delete'
            )) ?>
          </div>
          <div class='bizlists_info'>
            <div class='bizlists_browse_info_title'>
              <h3>
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                <?php if( $item->closed ): ?>
                  <i class="bizlist_close_icon"></i>
                <?php endif;?>
              </h3>
            </div>
            <div class='bizlists_browse_info_date'>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              -
              <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
             <div class='bizlists_ratong_manage_page'> 
               <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'bizlist')); ?>
              </div>
            </div>
                       <div class='bizlists_browse_info_des'>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 92) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any business listing that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any business listings.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new listing.', $this->url(array('action' => 'create'), 'bizlist_general'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null); ?>

<script type="text/javascript">
  scriptJquery('.core_main_bizlist').parent().addClass('active');
</script>
