<?php ?>
<div class="lp_contact_section lp_section_box sesbasic_bxs">
  <section id="lp_contact" class="lp_contact_box_main sesbasic_clearfix" style="background: url(<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsbgimage', ''); ?>);">
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsheading')) { ?>
      <h2><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsheading', 'Contact Us'); ?></h2>
    <?php } ?>
    <div class="lp_contact_box sesbasic_clearfix wow slideInUp">
      <div class="lp_contact_img"> <img src="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsmainimage', ''); ?>" /> </div>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsdescription')) { ?>
          <div class="lp_contact_address">
            <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsdescription', '<h3>HEAD OFFICE</h3><p>70 abc road,India&nbsp;<br>Phone: 2122454485&nbsp;<br>Fax: 2122454485&nbsp;<br>Zip Code:20692&nbsp;<br>Email: support@mail.com</p><h3>CUSTOMER CARE</h3><p>1800-1234-5678</p><h3>VISIT US</h3><p>www.abc.com</p>'); ?>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>
  <?php $location = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactslocation', ''); ?>
  <?php $url = 'https://www.google.com/maps/embed/v1/place?key=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.mapApiKey', '') . '&q='.$location; ?>
  <div class="lp_contact_map">
    <iframe src="<?php echo $url; ?>" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
  </div>
</div>