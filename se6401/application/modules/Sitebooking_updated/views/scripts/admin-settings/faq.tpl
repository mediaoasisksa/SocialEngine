<h2>
  <?php echo $this->translate('Services Booking & Appointments Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php
include_once APPLICATION_PATH .
    '/application/modules/Sitebooking/views/scripts/admin-settings/faq_help.tpl';
?>



