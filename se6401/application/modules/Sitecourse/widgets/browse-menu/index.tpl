
<div class="headline">
  <h2>
    <?php echo $this->translate('Courses');?>
  </h2>
  <?php if($this->navigation && count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>
