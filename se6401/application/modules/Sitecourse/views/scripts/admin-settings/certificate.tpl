<h2>
  <?php echo $this->translate('Course Builder / Learning Management Plugin'); ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
    echo $this->form->render($this);
    ?>
    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecourse', 'controller' => 'admin-settings', 'action' => 'preview-certificate'),'Preview certificate',array('target'=>'_blank')) ?>
  </div>
</div>

<script type="text/javascript">


</script>
