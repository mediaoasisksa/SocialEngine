<?php if (count($this->dashboardNavigation) > 0): ?>
  <div class="sitebooking_dashboard_options">
      <div class="quicklinks">
      <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->dashboardNavigation)->render();
      ?>
      </div> 
  </div>
<?php endif; ?>