<h2>
  <?php echo $this->translate('Course Builder / Learning Management Plugin') ?>
</h2>

<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/sitecourse/level/index/id/'+level_id;
    //alert(level_id);
  }
</script>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>
