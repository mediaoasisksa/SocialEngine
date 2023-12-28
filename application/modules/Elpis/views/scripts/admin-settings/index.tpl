<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 2022-06-20
 */

?>
<h2><?php echo $this->translate('Elpis Theme') ?></h2>

<?php if( engine_count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>
<h2><?php echo $this->translate('Global Settings') ?></h2>
<p><?php echo $this->translate("These settings affect all members in your community.") ?></p>
<?php echo 'More info: <a href="https://community.socialengine.com/blogs/597/72/elpis-theme" target="_blank">See KB article</a>'; ?>
<br />	
<br />
<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  function confirmChangeLandingPage(value){
    if((value == 1 || value == 2) && !confirm('Are you sure want to set the default Landing page of this theme as the Landing page of your website. For old landing page you will have to manually make changes in the Landing page from Layout Editor. Backup page of your current landing page will get created with the name "Backup - Landing Page".')) {
      scriptJquery('#elpis_changelanding-0').prop('checked',true);
    } else if(value == 0) {
    } else {
      scriptJquery('#elpis_changelanding-0').removeAttr('checked');
      scriptJquery('#elpis_changelanding-0').prop('checked',false);
    }
  }
</script>
