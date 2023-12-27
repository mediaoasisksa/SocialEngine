<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<script type="text/javascript">
  scriptJquery(document).ready(function() {
    var tabContainerSwitch = window.tabContainerSwitch = function(element) {
      element = scriptJquery(element);
      if( element.prop('tagName').toLowerCase() == 'a' ) {
        element = element.parents('li');
      }

      var myContainer = element.parents('.tabs_parent').parent();
      element.parents('.tabs_parent').addClass('tab_collapsed');
      myContainer.children('div:not(.tabs_alt)').hide();
      myContainer.find('ul > li').removeClass('active');
      element.attr('class').split(' ').forEach(function(className){
        className = className.trim();
        if( className.match(/^tab_[0-9]+$/) ) {
          myContainer.children('div.' + className).show();
          element.addClass('active');
        }
      });
      
    }
    var moreTabSwitch = window.moreTabSwitch = function(el) {
      el = scriptJquery(el);
      el.toggleClass('tab_open');
      el.toggleClass('tab_closed');
    }
    scriptJquery('.tab_collapsed_action').on('click', function(event) {
      scriptJquery(event.target).parents('.tabs_alt').toggleClass('tab_collapsed');
    });
  });
</script>

<div class='tabs_alt tabs_parent tab_collapsed'>
  <span class="tab_collapsed_action"></span>
  <ul id='main_tabs'>
    <?php foreach( $this->tabs as $key => $tab ): ?>
      <?php
        $class   = array();
        $class[] = 'tab_' . $tab['id'];
        $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
        if( $this->activeTab == $tab['id'] || $this->activeTab == $tab['name'] )
          $class[] = 'active';
        $class = join(' ', $class);
      ?>
      <?php if( $key < $this->max ): ?>
        <li class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="tabContainerSwitch(this, '<?php echo $tab['containerClass'] ?>');"><?php echo $this->translate(!empty($tab['title']) ? $tab['title'] : "")  ?><?php if( !empty($tab['childCount']) ): ?><span>(<?php echo $tab['childCount'] ?>)</span><?php endif; ?></a></li>
      <?php endif;?>
    <?php endforeach; ?>
    <?php if (engine_count($this->tabs) > $this->max):?>
    <li class="tab_closed more_tab" onclick="moreTabSwitch(this);">
      <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
      <div class="tab_pulldown_contents_wrapper">
        <div class="tab_pulldown_contents">
          <ul>
          <?php foreach( $this->tabs as $key => $tab ): ?>
            <?php
              $class   = array();
              $class[] = 'tab_' . $tab['id'];
              $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
              if( $this->activeTab == $tab['id'] || $this->activeTab == $tab['name'] ) $class[] = 'active';
              $class = join(' ', array_filter($class));
            ?>
            <?php if( $key >= $this->max ): ?>
              <li class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="tabContainerSwitch(this, '<?php echo $tab['containerClass'] ?>')"><?php echo $this->translate($tab['title']) ?><?php if( !empty($tab['childCount']) ): ?><span> (<?php echo $tab['childCount'] ?>)</span><?php endif; ?></a></li>
            <?php endif;?>
          <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </li>
    <?php endif;?>
  </ul>
</div>

<?php echo $this->childrenContent ?>
