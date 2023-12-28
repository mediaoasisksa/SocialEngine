 
<h2>
  <?php echo $this->translate("SocialEngineAddOns Core Plugin"); ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?> 

<div class="seaocore_settings_form">
    <div class='settings'>
		<?php echo $this->form->render($this); ?>
    </div>
</div>