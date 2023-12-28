<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Donna
 */
?>

<h2><?php echo $this->translate("Travel Plugin") ?></h2>

<?php if( engine_count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
    <div class='settings'>

      <?php echo $this->form->render($this); ?>

    </div>
  </div>
<script type="text/javascript">
  scriptJquery(document).ready(function() {
    scriptJquery('input[type=radio][name=travel_enable_rating]:checked').trigger('change');
  });
  
  function showHideRatingSetting(value) {
    if(value == 1) {
      scriptJquery('#travel_ratingicon-wrapper').show();
    } else {
      scriptJquery('#travel_ratingicon-wrapper').hide();
    }
  }
</script>
