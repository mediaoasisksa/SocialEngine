<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: typography.tpl 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>

<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>
<div class='clear sesbasic_admin_form company_typography_setting'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script>
  window.addEvent('domready',function() {
    usegooglefont('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.googlefonts', 0);?>');
  });
  
  function usegooglefont(value) {
  
    if(value == 1) {
    
//       $('company_googlebody_fontfamily').value = 'Open Sans';
//       $('company_googleheading_fontfamily').value = 'Open Sans';
//       if('company_body-wrapper')
//         $('company_body-wrapper').style.display = 'none';
      if($('company_bodygrp'))
        $('company_bodygrp').style.display = 'none';
//       if('company_heading-wrapper')
//         $('company_heading-wrapper').style.display = 'none';
      if($('company_headinggrp'))
        $('company_headinggrp').style.display = 'none';
//       if('company_mainmenu-wrapper')
//         $('company_mainmenu-wrapper').style.display = 'none';
      if($('company_mainmenugrp'))
        $('company_mainmenugrp').style.display = 'none';
//       if('company_tab-wrapper')
//         $('company_tab-wrapper').style.display = 'none';
      if($('company_tabgrp'))
        $('company_tabgrp').style.display = 'none';
        
      if($('company_googlebodygrp'))
        $('company_googlebodygrp').style.display = 'block';
      if($('company_googleheadinggrp'))
        $('company_googleheadinggrp').style.display = 'block';
      if($('company_googlemainmenugrp'))
        $('company_googlemainmenugrp').style.display = 'block';
      if($('company_googletabgrp'))
        $('company_googletabgrp').style.display = 'block';
    } else {
//       if('company_body-wrapper')
//         $('company_body-wrapper').style.display = 'block';
      if($('company_bodygrp'))
        $('company_bodygrp').style.display = 'block';
//       if('company_heading-wrapper')
//         $('company_heading-wrapper').style.display = 'block';
      if($('company_headinggrp'))
        $('company_headinggrp').style.display = 'block';
//       if('company_mainmenu-wrapper')
//         $('company_mainmenu-wrapper').style.display = 'block';
      if($('company_mainmenugrp'))
        $('company_mainmenugrp').style.display = 'block';
//       if('company_tab-wrapper')
//         $('company_tab-wrapper').style.display = 'block';
      if($('company_tabgrp'))
        $('company_tabgrp').style.display = 'block';
        
      if($('company_googlebodygrp'))
        $('company_googlebodygrp').style.display = 'none';
      if($('company_googleheadinggrp'))
        $('company_googleheadinggrp').style.display = 'none';
      if($('company_googlemainmenugrp'))
        $('company_googlemainmenugrp').style.display = 'none';
      if($('company_googletabgrp'))
        $('company_googletabgrp').style.display = 'none';
        
        
    }
  }
</script>
<!--<?php 
  $url = "https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyDczHMCNc0JCmJACM86C7L8yYdF9sTvz1A";
  $results = json_decode(file_get_contents($url),true);
  
  $string = 'https://fonts.googleapis.com/css?family=';
  foreach($results['items'] as $re) {
  	$string .= $re['family'] . '|';
  }
?>

<link href="<?php echo $string; ?>" type="text/css" rel="stylesheet" />
<style type="text/css">
 <?php foreach($results['items'] as $re) { ?>
      
	select option[value="<?php echo $re['family'];?>"]{
		font-family:<?php echo $re['family'];?>;
	}
	<?php } ?>
	-->
</style>
