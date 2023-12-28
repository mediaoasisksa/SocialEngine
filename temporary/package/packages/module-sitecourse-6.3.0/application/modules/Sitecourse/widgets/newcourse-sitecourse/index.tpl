<?php if ($this->canCreate): ?>
  <div class="create_course">
    <?php
			echo $this->htmlLink(
                      array('route' => 'sitecourse_general', 'module' => 'sitecourse', 'controller' => 'index', 'action' => 'create',),
                    $this->translate('Create Course'), array())
    ?>
  </div>
<?php endif; ?>
