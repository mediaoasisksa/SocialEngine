<?php if($this->can_request): ?>
<?php echo $this->form->render($this) ?>
<?php else: ?>
  <p>No of request quota is exceded</p>

<?php endif; ?>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
