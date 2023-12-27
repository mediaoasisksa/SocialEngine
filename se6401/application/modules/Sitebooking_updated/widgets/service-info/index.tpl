<?php if ($this->showContent): ?>
  <?php if (!empty($this->otherDetails)): ?>
    <h3>Profile Mapping Information</h3>
  <?php endif;?>
  <div class='sitebooking_pro_specs'>
    <?php if (!empty($this->otherDetails)): ?>
      <?php echo $this->otherDetails;?>
    <?php endif; ?>
  </div>
<?php endif; ?>