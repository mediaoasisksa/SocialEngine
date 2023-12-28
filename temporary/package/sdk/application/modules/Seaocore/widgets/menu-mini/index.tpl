<!-- SOME MULTICURRENCY RELATED WORK START HERE -->
<?php $isMulticurrencyEnable = Engine_Api::_()->hasModuleBootstrap('sitemulticurrency') && $this->settings('sitemulticurrency.enabled', 1) && $this->settings('sitemulticurrency.headermenu.enabled', 1); ?>

<?php
$infoCurrency = $this->settings('sitemulticurrency.currency.information', 'currencySymbol');
$viewCurrency = false;
$allowedCurr = $isMulticurrencyEnable ? Engine_Api::_()->getDbTable('currencyrates', 'sitemulticurrency')->getAllowedCurrencies() : array();
?>

<?php if( count($allowedCurr) > 1 ): ?>
  <?php
  $viewCurrency = $infoCurrency;
  ?>

  <script type="text/javascript">

    //SET THE SELECTED CURRENCY COUNTRY IMAGE ICON ON PAGE LOAD
    window.addEvent('domready', function () {
      if ('<?php echo $infoCurrency; ?>' == 'countryFlag') {
        $('flag_display').src = $('flag_display').src + '<?php echo Engine_Api::_()->sitemulticurrency()->getSelectedCurrency(); ?>.png';
      } else {
        if (document.getElementById('currency_symbol')) {
          document.getElementById('currency_symbol').innerHTML = '<?php echo Engine_Api::_()->sitemulticurrency()->getSelectedCurrency(); ?>';
        }
      }
    });
  </script>
<?php endif; ?>
<!-- MULTICURRENCY RELATED WORK END HERE -->

<?php $menuCount = 1; ?>

<div id='core_menu_mini_menu'>
  <ul class="seaocore_mini_menu_items <?php echo empty($this->showIcons) ? ' _show_icon_labels' : ''; ?>">
    <?php foreach( $this->navigation as $item ): ?>
      <?php $menuName = substr($item->class, strrpos(trim($item->class), ' ') + 1); ?>
      <?php if( in_array($menuName, array('core_mini_admin', 'core_mini_settings')) ): ?>
        <?php continue; ?>           
      <?php endif; ?>
      <?php if( $menuName == 'sitemenu_mini_currency' && (empty($isMulticurrencyEnable) || count($allowedCurr) <= 1) ): ?>                        
        <?php continue; ?>                        
      <?php endif; ?>

      <?php if( !empty($this->changeMyLocation) && $menuCount == $this->changeMyLocationPosition ): ?>
        <li class="seaocore_mimi_menu_item seaocore_mimi_menu_seaocore_change-my-location">
          <?php echo $this->content()->renderWidget('seaocore.change-my-location', array('detactLocation' => 0, 'updateUserLocation' => 0, 'showLocationPrivacy' => 0, 'showSeperateLink' => 0, 'placedInMiniMenu' => 1, 'locationbox_width' => $this->locationbox_width, 'widgetContentId' => $this->identity)) ?>
        </li>
      <?php endif; ?>
      <li class="seaocore_mimi_menu_item seaocore_mimi_menu_<?php echo $menuName; ?> <?php echo in_array($menuName, $this->pullDownMiniMenus) ? ' seaocore_mimi_menu_pulldown_item' : ' ' ?>">
        <?php
        $href = $item->getHref();
        // You can give href an array to use router
        if( is_array($href) ) {
          $href = $this->htmlLink()->url($href);
        }
        $attribs = array_filter(array(
          'class' => $item->class,
          'alt' => (!empty($item->alt) ? $item->alt : null ),
          'target' => (!empty($item->target) ? $item->target : null ),
          'title' => $item->get('title') ? $this->translate($item->get('title')) : $this->translate(strip_tags($item->getLabel())),
          'href' => $href ?: 'javascript:void(0)',
        ));
        ?>
        <?php $label = $item->getLabel(); ?>
        <?php
        if( in_array($menuName, array('core_mini_profile')) ) {
          $attribs['title'] = $this->translate('My Profile');
        }
        if( in_array($menuName, array('core_mini_messages')) ) {
          $label = explode('(', $label)[0];
        }
        if( in_array($menuName, array('core_mini_update')) ) {
          $label = 'Notifications';
          $attribs['title'] = $this->translate($label);
        }
        ?>
        <?php $bubbleCount = 0; ?>
        <?php $bubbleType = ''; ?>
        <?php if( in_array($menuName, $this->pullDownMiniMenus) ): ?>
          <?php $pullDownParams = $this->miniMenuPulldownSEAO($menuName, $item); ?>
          <?php $id = $menuName . '_pulldown_' . rand(1000, 9999); ?>
          <?php $attribs['href'] = 'javascript:void(0);'; ?>
          <?php $attribs['class'] .= ' _not_close'; ?>
          <?php $bubbleCount = $pullDownParams['bubbleCount']; ?>
          <?php $bubbleType = $pullDownParams['bubbleType']; ?>
          <div class="seaocore_pulldown_wrapper pulldown_contents_wrapper <?php echo $menuName . '_pulldown' ?>" id="<?php echo $id ?>" data-action="<?php echo $pullDownParams['action'] ?>">
            <div class="seaocore_pulldown_arrow"></div>
            <?php if( !empty($pullDownParams['header']['title']) ): ?>

              <div class="seocore-pulldown-header">
                <span class="seocore-pulldown-heading"><?php echo $this->translate($pullDownParams['header']['title']); ?></span>
                <?php if( !empty($pullDownParams['header']['actionLink']) ): ?>
                  <?php echo $this->htmlLink($pullDownParams['header']['actionLink']['href'], $this->translate($pullDownParams['header']['actionLink']['label']), $pullDownParams['header']['actionLink']['options']); ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <div class="seaocore_pulldown_contents" id="seaocore_<?php echo $id ?>_pulldown_contents">
              <?php if( !empty($pullDownParams['content']) ): ?>
                <?php echo $pullDownParams['content']; ?>
              <?php else: ?>
                <div class="pulldown_loading txt_center">
                  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif' alt="<?php echo $this->translate('Loading'); ?>" />
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
        <?php
        $xhtml = '';
        foreach( (array) $attribs as $key => $val ) {
          $key = $this->escape($key);
          if( ('on' == substr($key, 0, 2) ) ) {
            $val = str_replace('\'', '&#39;', $val);
          } else {
            $val = $this->escape($val);
          }
          if( strpos($val, '"') !== false ) {
            $xhtml .= " $key='$val'";
          } else {
            $xhtml .= " $key=\"$val\"";
          }
        }
        ?>
        <a <?php echo $xhtml; ?>>
          <?php if( $bubbleCount ): ?>
          <span class="_count_bubble <?php echo $bubbleType ? "_count_bubble_" . $bubbleType : "" ?>" style="display:block;" ><?php echo $bubbleCount; ?></span>
          <?php else: ?>
            <span class="_count_bubble <?php echo $bubbleType ? "_count_bubble_" . $bubbleType : "" ?>"></span>
          <?php endif; ?>
          <span class="_icon"></span>
          <span class="_label"><?php echo $this->translate($label) ?></span>
        </a>
      </li>
      <?php $menuCount++; ?>
    <?php endforeach; ?>
    <?php if( !empty($this->changeMyLocation) && (empty($this->changeMyLocationPosition) || $menuCount < $this->changeMyLocationPosition ) ): ?>
      <li class="seaocore_mimi_menu_item seaocore_mimi_menu_seaocore_change-my-location">
        <?php echo $this->content()->renderWidget('seaocore.change-my-location', array('detactLocation' => 0, 'updateUserLocation' => 0, 'showLocationPrivacy' => 0, 'showSeperateLink' => 0, 'placedInMiniMenu' => 1, 'locationbox_width' => $this->locationbox_width, 'widgetContentId' => $this->identity)) ?>
      </li>
    <?php endif; ?> 
  </ul>
</div>

<!--<span  style="display: none;" class="updates_pulldown" id="core_mini_updates_pulldown">
  <div class="pulldown_contents_wrapper">
    <div class="pulldown_contents">
      <ul class="notifications_menu" id="notifications_menu">
        <div class="notifications_loading" id="notifications_loading">
          <i class="fa fa-spin fa-spinner" style='margin-right: 5px;' ></i>
<?php echo $this->translate("Loading ...") ?>
        </div>
      </ul>
    </div>
    <div class="pulldown_options">
<?php
echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'), $this->translate('View All Updates'), array('id' => 'notifications_viewall_link'))
?>
<?php
echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
  'id' => 'notifications_markread_link',
))
?>
    </div>
  </div>
</span>-->


<script type='text/javascript'>
  en4.core.runonce.add(en4.seaocore.miniMenu.init);
</script>
