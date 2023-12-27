<?php if (count($this->dashboardNavigation) > 0): ?>
  <div class="quicklinks">
  <?php
  // Render the menu
  echo $this->navigation()->menu()->setContainer($this->dashboardNavigation)->render();
  ?>
  </div> 
<?php endif; ?>