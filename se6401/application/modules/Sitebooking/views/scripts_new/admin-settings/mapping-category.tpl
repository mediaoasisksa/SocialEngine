<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>

<?php if (@$this->closeSmoothbox || $this->close_smoothbox): ?>
  <?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
  <script type="text/javascript">
    window.parent.location.href = '<?php echo $baseurl ?>' + '/admin/sitebooking/settings/categories';
    window.parent.Smoothbox.close();
  </script>
<?php endif; ?>

<script type="text/javascript">
  function closeSmoothbox() {
    window.parent.Smoothbox.close();
  }
</script>

