<h2>
  <?php echo $this->translate('Services Booking & Appointments Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>

  <?php echo $this->form->render($this); ?>
  </div>
</div>
