<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
?>
<?php
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/create_edit_category.js');
?>
<div class='global_form'>
  <?php echo $this->form->render($this) ?>
  <a href="javascript: void(0);" onclick="return addAnotherOption();" id="addOptionLink"><?php echo $this->translate("Add another option") ?></a>
  <script type="text/javascript">
  
    var modulename = 'poll';
    en4.core.runonce.add(function() {
      <?php if(isset($this->category_id) && $this->category_id != 0) { ?>
        showSubCategory('<?php echo $this->category_id; ?>','<?php echo $this->subcat_id; ?>');
      <?php } else { ?>
        if(document.getElementById('subcat_id-wrapper'))
          document.getElementById('subcat_id-wrapper').style.display = "none";
      <?php } ?>
      <?php if(isset($this->subsubcat_id)) { ?>
        <?php if(isset($this->subcat_id) && $this->subcat_id != 0) { ?>
          showSubSubCategory('<?php echo $this->subcat_id; ?>' ,'<?php echo $this->subsubcat_id; ?>');
        <?php } else { ?>
          if(document.getElementById('subsubcat_id-wrapper'))
            document.getElementById('subsubcat_id-wrapper').style.display = "none";
        <?php } ?>
      <?php } else { ?>
        if(document.getElementById('subsubcat_id-wrapper'))
          document.getElementById('subsubcat_id-wrapper').style.display = "none";
      <?php } ?>
    });
  
    //<!--
    en4.core.runonce.add(function() {
      var maxOptions = <?php echo $this->maxOptions ?>;
      var options = <?php echo Zend_Json::encode($this->options) ?>;
      var optionParent = scriptJquery('#options').parent();

      var addAnotherOption = window.addAnotherOption = function (dontFocus, label) {
        if (maxOptions && scriptJquery('input.pollOptionInput').length >= maxOptions) {
          return !alert(new String('<?php echo $this->string()->escapeJavascript($this->translate("A maximum of %s options are permitted.")) ?>').replace(/%s/, maxOptions));
          return false;
        }

        var optionElement = scriptJquery.crtEle('input', {
          'type': 'text',
          'name': 'optionsArray[]',
          'class': 'pollOptionInput',
          'value': (typeof label != 'undefined' ? label : ''),
        }).keydown(function(event) {
          if (event.key == 'Enter') {
            if (scriptJquery(this).val().trim().length > 0) {
              addAnotherOption();
              return false;
            } else
              return true;
          } else
            return true;
        });
        
        if( dontFocus ) {
          optionElement.appendTo(optionParent);
        } else {
          optionElement.appendTo(optionParent).focus();
        }

        scriptJquery('#addOptionLink').appendTo(optionParent);

        if( maxOptions && scriptJquery('input.pollOptionInput').length >= maxOptions ) {
          scriptJquery('#addOptionLink').remove();
        }
      }
      
      // Do stuff
      if( $type(options) == 'array' && options.length > 0 ) {
        options.each(function(label) {
          addAnotherOption(true, label);
        });
        if( options.length == 1 ) {
          addAnotherOption(true);
        }
      } else {
        // display two boxes to start with
        addAnotherOption(true);
        addAnotherOption(true);
      }
    });
    // -->
  </script>
</div>


<script type="text/javascript">
  scriptJquery('.core_main_poll').parent().addClass('active');
</script>
