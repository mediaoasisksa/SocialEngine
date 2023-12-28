<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headScript()->appendFile('https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.mapApiKey', '')); ?>
<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>

<?php if( count($this->subnavigation)): ?>
  <div class='sesbasic-admin-navgation'> <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render(); ?> </div>
<?php endif; ?>
<div class='clear'>
  <div class='settings sescompany_admin_form landing_page_settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<?php 

$slidersharelink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidersharelink', 1);

$la1aboutshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1aboutshow', 1);

$la1countershow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1countershow', 1);

$la1featuresshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresshow', 1);

$la1clientsshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1clientsshow', 1);

$la1contentssshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1contentssshow', 1);


$la2photosshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photosshow', 1);
$la2contactsshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsshow', 1);
$la2teamsshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsshow', 1);

$la1testimonialssshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1testimonialssshow', 1);
?>

<script type="text/javascript">

  en4.core.runonce.add(function() {
    if (document.getElementById('sescompany_la2contactslocation')) {
      new google.maps.places.Autocomplete(document.getElementById('sescompany_la2contactslocation'));
    }
  });
  
  window.addEvent('domready',function() {
    slidersharelink('<?php echo $slidersharelink;?>');
    aboutus('<?php echo $la1aboutshow;?>');
    counter('<?php echo $la1countershow;?>');
    features('<?php echo $la1featuresshow;?>');
    clients('<?php echo $la1clientsshow;?>');
    contents('<?php echo $la1contentssshow;?>');
    photos('<?php echo $la2photosshow;?>');
    contactus('<?php echo $la2contactsshow;?>');
    teams('<?php echo $la2teamsshow;?>');
    testimonials('<?php echo $la1testimonialssshow;?>');
  });
  
  function testimonials(value) {
    if(value == 1) {
      document.getElementById('sescompany_la1testimonialsheading-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_la1testimonialsheading-wrapper').style.display = 'none';
    }
  }
  
  function teams(value) {
    if(value == 1) {
      document.getElementById('sescompany_la2teamsheading-wrapper').style.display = 'block';
      document.getElementById('sescompany_la2teamsbgimage-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_la2teamsheading-wrapper').style.display = 'none';
      document.getElementById('sescompany_la2teamsbgimage-wrapper').style.display = 'none';
    }
  }
  
  function contactus(value) {
    if(value == 1) {
      document.getElementById('sescompany_la2contactsheading-wrapper').style.display = 'block';
      document.getElementById('sescompany_la2contactsbgimage-wrapper').style.display = 'block';
      document.getElementById('sescompany_la2contactsmainimage-wrapper').style.display = 'block';
      document.getElementById('sescompany_la2contactsdescription-wrapper').style.display = 'block';
      document.getElementById('sescompany_la2contactslocation-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_la2contactsheading-wrapper').style.display = 'none';
      document.getElementById('sescompany_la2contactsbgimage-wrapper').style.display = 'none';
      document.getElementById('sescompany_la2contactsmainimage-wrapper').style.display = 'none';
      document.getElementById('sescompany_la2contactsdescription-wrapper').style.display = 'none';
      document.getElementById('sescompany_la2contactslocation-wrapper').style.display = 'none';
    }
  }
  
  function photos(value) {
    if(value == 1) {
      document.getElementById('sescompany_la2photosheading-wrapper').style.display = 'block';
      document.getElementById('sescompany_la2photoslimit-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_la2photosheading-wrapper').style.display = 'none';
      document.getElementById('sescompany_la2photoslimit-wrapper').style.display = 'none';
    }
  }
  
  function contents(value) {
    if(value == 1) {
      document.getElementById('sescompany_mngcontentsbgimage-wrapper').style.display = 'block';
      
      document.getElementById('sescompany_contmodule-wrapper').style.display = 'block';
      document.getElementById('sescompany_contheading-wrapper').style.display = 'block';
      document.getElementById('sescompany_contpopularitycriteria-wrapper').style.display = 'block';
      document.getElementById('sescompany_contlimit-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_mngcontentsbgimage-wrapper').style.display = 'none';
      
      document.getElementById('sescompany_contmodule-wrapper').style.display = 'none';
      document.getElementById('sescompany_contheading-wrapper').style.display = 'none';
      document.getElementById('sescompany_contpopularitycriteria-wrapper').style.display = 'none';
      document.getElementById('sescompany_contlimit-wrapper').style.display = 'none';
    }
  }
  
  function clients(value) {
    if(value == 1) {
      document.getElementById('sescompany_la1clientsbgimage-wrapper').style.display = 'block';
      document.getElementById('sescompany_la1clientsheading-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_la1clientsbgimage-wrapper').style.display = 'none';
      document.getElementById('sescompany_la1clientsheading-wrapper').style.display = 'none';
    }
  }
  
  function features(value) {
    if(value == 1) {
      document.getElementById('sescompany_la1fetbgimage-wrapper').style.display = 'block';
      document.getElementById('sescompany_la1featuresheading-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_la1fetbgimage-wrapper').style.display = 'none';
      document.getElementById('sescompany_la1featuresheading-wrapper').style.display = 'none';
    }
  }
  
  function counter(value) {
    if(value == 1) {
      document.getElementById('sescompany_la1cntbgimage-wrapper').style.display = 'block';
      document.getElementById('sescompany_la1countersheading-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_la1cntbgimage-wrapper').style.display = 'none';
      document.getElementById('sescompany_la1countersheading-wrapper').style.display = 'none';
    }
  }
  
  function slidersharelink(value) {
    if(value == 1) {
      document.getElementById('sescompany_sliderfacebooklink-wrapper').style.display = 'block';
      document.getElementById('sescompany_slidertwitterlink-wrapper').style.display = 'block';
      document.getElementById('sescompany_slidergooglelink-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_sliderfacebooklink-wrapper').style.display = 'none';
      document.getElementById('sescompany_slidertwitterlink-wrapper').style.display = 'none';
      document.getElementById('sescompany_slidergooglelink-wrapper').style.display = 'none';
    }
  }
  
  function aboutus(value) {
    if(value == 1) {
      document.getElementById('sescompany_la1abtheading-wrapper').style.display = 'block';
      document.getElementById('sescompany_la1abtbgimage1-wrapper').style.display = 'block';
      document.getElementById('sescompany_la1abtvideourl-wrapper').style.display = 'block';
      document.getElementById('sescompany_la1abtbgimage2-wrapper').style.display = 'block';
    } else {
      document.getElementById('sescompany_la1abtheading-wrapper').style.display = 'none';
      document.getElementById('sescompany_la1abtbgimage1-wrapper').style.display = 'none';
      document.getElementById('sescompany_la1abtvideourl-wrapper').style.display = 'none';
      document.getElementById('sescompany_la1abtbgimage2-wrapper').style.display = 'none';
    }
  }
</script>