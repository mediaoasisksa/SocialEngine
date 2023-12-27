<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: editphotos.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */
?>
<?php
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl."externals/selectize/css/normalize.css");
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'externals/selectize/js/selectize.js');
?>
<script type="text/javascript">
  var attachAutoSuggest = function (tagId) {
    en4.core.runonce.add(function() {
      scriptJquery('#'+tagId).selectize({
        maxItems: 10,
        valueField: 'label',
        labelField: 'label',
        searchField: 'label',
        create: true,
        load: function(query, callback) {
            if (!query.length) return callback();
            scriptJquery.ajax({
              url: '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>',
              data: { value: query },
              success: function (transformed) {
                callback(transformed);
              },
              error: function () {
                  callback([]);
              }
            });
        }
      });
    });
  }
</script>
<div class="layout_middle">
  <div class="generic_layout_container">
  <div class="headline">
  <h2>
    <?php echo $this->translate('Photo Albums');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
  </div>
</div>
</div>
<div class="layout_middle">
  <div class="generic_layout_container">
    <h3>
      <?php echo $this->htmlLink($this->album->getHref(), $this->album->getTitle()) ?>
      (<?php echo $this->translate(array('%s photo', '%s photos', $this->album->count()),$this->locale()->toNumber($this->album->count())) ?>)
    </h3>
<?php if( $this->paginator->count() > 0 ): ?>
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>
<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
  <?php echo $this->form->album_id; ?>
  <ul class='albums_editphotos'>
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <div class="albums_editphotos_photo">
          <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.normal'))  ?>
        </div>
        <div class="albums_editphotos_info">
          <?php
            $key = $photo->getGuid();
            echo $this->form->getSubForm($key)->render($this);
          ?>
          <div class="albums_editphotos_cover">
            <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->album->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
          </div>
          <div class="albums_editphotos_label">
            <label><?php echo $this->translate('Album Cover');?></label>
        </div>
        </div>
      </li>
      <script type="text/javascript">
        attachAutoSuggest('<?php echo $key . '-tags'; ?>');
      </script>
    <?php endforeach; ?>
  </ul>
  <?php echo $this->form->submit->render(); ?>
</form>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
  </div>
</div>
