
<?php
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/create_edit_category.js');
?>
<script>
  var modulename = 'bizlist';
  en4.core.runonce.add(function() {
    if(document.getElementById('category_id')) {
      <?php if(isset($_GET['category_id']) && $_GET['category_id'] != 0) { ?>
          showSubCategory('<?php echo $_GET['category_id']; ?>','<?php echo $_GET['subcat_id']; ?>');
        <?php if(isset($_GET['subsubcat_id'])){ ?>
          showSubSubCategory("<?php echo $_GET['subcat_id']; ?>","<?php echo $_GET['subsubcat_id']; ?>");
        <?php } else {?>
          if(document.getElementById('subsubcat_id-wrapper'))
            document.getElementById('subsubcat_id-wrapper').style.display = "none";
        <?php } ?>
      <?php } else { ?>
        if(document.getElementById('subcat_id-wrapper'))
          document.getElementById('subcat_id-wrapper').style.display = "none";
        if(document.getElementById('subsubcat_id-wrapper'))
          document.getElementById('subsubcat_id-wrapper').style.display = "none";
      <?php } ?>
    }
  });
</script>
<script type="text/javascript">
  var pageAction = function(page){
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
      if(nextEl.hasClass('browse-separator-wrapper')) {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && ( nextEl.css('display') == 'none' );
      }
    } while(nextEl && nextEl.length);
    if(lastSep) {
      lastSep.css('display', (allHidden ? 'none' : ''));
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

<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php endif ?>
