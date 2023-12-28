<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 10110 2013-10-31 02:04:11Z andres $
 * @author     Jung
 */
?>
<?php
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl."externals/selectize/css/normalize.css");
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'externals/selectize/js/selectize.js');
  $headScript->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/create_edit_category.js');
?>

<?php 
if(0) {
$spamSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam;
$recaptchaVersionSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam_recaptcha_version;
if($recaptchaVersionSettings == 0  && $spamSettings['recaptchaprivatev3'] && $spamSettings['recaptchapublicv3']) { ?>
  <script type="text/javascript"
    src="https://www.google.com/recaptcha/api.js?render=<?php echo $spamSettings['recaptchapublicv3']; ?>">
  </script>
  <script type="text/javascript">
    grecaptcha.ready(function () {
      grecaptcha.execute('<?php echo $spamSettings['recaptchapublicv3']; ?>', { action: 'login' }).then(function (token) {
        var recaptchaResponse = document.getElementById('recaptchaResponse');
        recaptchaResponse.value = token;
      });
    });
  </script>
<?php } } ?>


<script type="text/javascript">
  en4.core.runonce.add(function(){
    scriptJquery('#tags').selectize({
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
  
  var modulename = 'employment';
  var category_id = '<?php echo $this->category_id; ?>';
  var subcat_id = '<?php echo $this->subcat_id; ?>';
  var subsubcat_id = '<?php echo $this->subsubcat_id; ?>';

  en4.core.runonce.add(function() {
    if(category_id && category_id != 0) {
      showSubCategory(category_id, subcat_id);
    } else {
      if(scriptJquery('#category_id').val()) {
        showSubCategory(scriptJquery('#category_id').val());
      } else {
        if(document.getElementById('subcat_id-wrapper'))
          document.getElementById('subcat_id-wrapper').style.display = "none";
      }
    }

    if(subsubcat_id) {
      if(subcat_id && subcat_id != 0) {
        showSubSubCategory(subcat_id, subsubcat_id);
      } else {
        if(document.getElementById('subsubcat_id-wrapper'))
          document.getElementById('subsubcat_id-wrapper').style.display = "none";
      }
    } else if(subcat_id) {
      showSubSubCategory(subcat_id);
    }
    else {
      if(document.getElementById('subsubcat_id-wrapper'))
        document.getElementById('subsubcat_id-wrapper').style.display = "none";
    }
  });
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
//'topLevelId' => (int) @$this->topLevelId,
//'topLevelValue' => (int) @$this->topLevelValue
))
?>
<div class="layout_middle">
  <div class="generic_layout_container">
      <div class="headline">
        <h2>
          <?php echo $this->translate('Employment Listings');?>
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
  <div class="generic_layout_container employments_form">
<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form employments_edit">
  <div>
    <div>
      <h3>
        <?php echo $this->translate($this->form->getTitle()) ?>
      </h3>

      <div class="form-elements">
        <?php echo $this->form->getDecorator('FormErrors')->setElement($this->form)->render("");?>
        <?php echo $this->form->title; ?>
        <?php echo $this->form->tags; ?>
        <?php if($this->form->category_id) echo $this->form->category_id; ?>
        <?php if($this->form->subcat_id) echo $this->form->subcat_id; ?>
        <?php if($this->form->subsubcat_id) echo $this->form->subsubcat_id; ?>
        <?php echo $this->form->body; ?>
        <?php echo $this->form->getSubForm('fields'); ?>
        <?php if($this->form->networks) echo $this->form->networks; ?>
        <?php if($this->form->auth_view) echo $this->form->auth_view; ?>
        <?php if($this->form->auth_comment) echo $this->form->auth_comment; ?>
      </div>
      <?php echo $this->form->employment_id; ?>
      <?php echo $this->form->execute->render(); ?>
      </div>
    </div>
  </form>
 </div>
</div>
