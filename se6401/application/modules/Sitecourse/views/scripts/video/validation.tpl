<script type="text/javascript">
  <?php if($this->valid):?>
    var valid = true;
    var informationVideoContent = <?php echo json_encode($this->information);?>;
  <?php else:?>
    var valid = false;
    var informationVideoContent = false;
  <?php endif; ?>
</script>
<?php die; ?>
