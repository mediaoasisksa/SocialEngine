<h2>
  <?php echo $this->translate('Course Builder / Learning Management Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<?php if(!$this->hasLanguageDirectoryPermissions):?>
<div class="seaocore_tip">
  <span>
    <?php echo "Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory for change the pharse course and courses." ?>
  </span>
</div>

<?php endif; ?>

<div class='clear'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>
  </div>
</div>
