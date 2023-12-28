<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
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

  var searchEmployments = function() {
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
    scriptJquery('.employments_browse_info_blurb').enableLinks();
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
    <ul class="employments_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='employments_browse_info'>
            <div class='employments_browse_info_title'>
              <h3>
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                <?php if( $item->closed ): ?>
                <i class="employments_close_icon"></i>
                <?php endif;?>
              </h3>
              <div class="dropdown">
                <button type="button" id="manageoption" data-bs-toggle="dropdown" aria-expanded="false">
                  <i></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="manageoption">
                  <li><?php echo $this->htmlLink(array('route' => 'employment_specific', 'action' => 'edit', 'employment_id' => $item->getIdentity(),),$this->translate('Edit Listing'), array('class' => 'dropdown-item buttonlink icon_employment_edit')) ?>
                  <?php if( $this->allowed_upload ): ?>
                    <li>
                      <?php echo $this->htmlLink(array('route' => 'employment_extended','controller' => 'photo','action' => 'upload','employment_id' => $item->getIdentity(),), $this->translate('Add Photos'), array('class' => 'dropdown-item buttonlink icon_employment_photo_new')) ?>
                    </li>
                  <?php endif; ?>
                  <li>
                    <?php if( !$item->closed ): ?>
                      <?php echo $this->htmlLink(array('route' => 'employment_specific','action' => 'close','employment_id' => $item->getIdentity(),
                        'closed' => 1,), $this->translate('Close Listing'), array('class' => 'dropdown-item buttonlink icon_employment_close')) ?>
                    <?php else: ?>
                      <?php echo $this->htmlLink(array('route' => 'employment_specific', 'action' => 'close', 'employment_id' => $item->getIdentity(), 'closed' => 0,), $this->translate('Open Listing'), array('class' => 'dropdown-item buttonlink icon_employment_open')) ?>
                    <?php endif; ?>
                  </li>
                  <li>
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'employment', 'controller' => 'index', 'action' => 'delete', 'employment_id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Listing'), array('class' => 'dropdown-item buttonlink smoothbox icon_employment_delete')) ?>
                  </li>
                </ul>
              </div>
            </div>
            <div class='employments_browse_info_des'>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 92) ?>
            </div>
          </div>
          <div class="employments_browse_footer">
            <div class="employments_browse_footer_info">
              <span><i class="far fa-user"></i><?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?></span>
              <span><i class="far fa-clock"></i><?php echo $this->timestamp(strtotime($item->creation_date)) ?></span>
            </div>
            <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'employment')); ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any employment listing that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any employment listings.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new listing.', $this->url(array('action' => 'create'), 'employment_general'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null); ?>


<script type="text/javascript">
  scriptJquery('.core_main_employment').parent().addClass('active');
</script>
