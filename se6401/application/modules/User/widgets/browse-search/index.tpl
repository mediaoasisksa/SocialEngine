
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) $this->topLevelId,
    'topLevelValue' => (int) $this->topLevelValue
));
?>
<?php  
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/selectize/js/selectize.js');
?>
<?php
echo $this->form
  ->setAction($this->url(array(), 'user_general', true))
  ->render($this);
?>

<script type="text/javascript">
    en4.core.runonce.add(function () {
        var formElement = scriptJquery('.layout_user_browse_search .field_search_criteria');
        // On search
        formElement.on('submit', function (event) {
            if (!window.searchMembers) {
                return;
            }
            searchMembers();
        });

        scriptJquery(window).on('onChangeFields', function () {
            var firstSep = scriptJquery('li.browse-separator-wrapper');
            var lastSep;
            var nextEl = firstSep;
            var allHidden = true;
            do {
                nextEl = nextEl.next();
                if (nextEl.hasClass('browse-separator-wrapper')) {
                    lastSep = nextEl;
                    nextEl = false;
                } else {
                    allHidden = allHidden && (nextEl.css('display') == 'none');
                }
            } while (nextEl.length);
            if (lastSep.length) {
                lastSep.css('display', (allHidden ? 'none' : ''));
            }
        });
    });
</script>
