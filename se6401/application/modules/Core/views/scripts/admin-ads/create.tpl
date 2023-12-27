<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

?>
<script type="text/javascript">
var myCalStart = false;
var myCalEnd = false;

en4.core.runonce.add(function(){
  scriptJquery(`<button type="button" class="event_calendar"></button>`).insertBefore(scriptJquery('#start_time-date').attr("type","text").attr("autocomplete","off").datepicker({
    dateFormat: "mm/dd/yy"
    })
  );
  
  scriptJquery(`<button type="button" class="event_calendar"></button>`).insertBefore(scriptJquery('#end_time-date').attr("type","text").attr("autocomplete","off").datepicker({
    dateFormat: "mm/dd/yy"
    })
  );
});


var updateTextFields = function(endsettings)
{
  var endtime_element = document.getElementById("end_time-wrapper");
  endtime_element.style.display = "none";

  if (endsettings.value == 0)
  {
    endtime_element.style.display = "none";
    return;
  }

  if (endsettings.value == 1)
  {
    endtime_element.style.display = "block";
    return;
  }
}
en4.core.runonce.add(updateTextFields);
</script>
<h2>
  <?php echo $this->translate("Ads") ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>
<div class='create_ad settings'>
  <?php echo $this->form->render($this); ?>
</div>
