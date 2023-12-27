
<?php 

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/styles/style_sitecourse_dashboard.css');
?>


<div class="course_builder_dashboard">
  <div class="course_builder_dashboard_container">
    <div class="course_builder_dashboard_mains course_browse_widget">
      <div class="course_builder_dashboard_main_block">
        <?php if(count($this->topics) > 0): ?>
          <!-- <form method="POST"> -->
            <div class="topics">
              <?php foreach ($this->topics as $topic): ?>

                <div>
                  <span class="course_builder_dashboard_list_accordion">
                    <p> 
                      <?php  echo $topic['title']; ?>
                    </p>
                    <span class="course_builder_dashboard_list_icons">
                      <span class="course_builder_dashboard_list_lectures">
                        <p>
                          <?php echo count($this->lessons[$topic['topic_id']]); ?>
                          <?php echo $this->translate("Lectures"); ?>
                        </p>
                      </span>
                      <span class="course_builder_dashboard_icons_list dropdown_icon">
                      <i class="fas fa-chevron-down"></i>
                    </span>
                  </span>
                </span>
                <div class="course_builder_dashboard_list_accordion_panel">
                  <!-- lessons listing belongs to topic -->
                  <?php foreach($this->lessons[$topic['topic_id']] as $lesson): ?>
                      <div class="course_builder_topic_lessons">
                        <?php  echo $lesson['title']; ?>
                      </div>
                  <?php endforeach; ?>          
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p><?php echo $this->translate("Add a topic to build course"); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
  var acc = document.getElementsByClassName(
    "course_builder_dashboard_list_accordion"
    );
  var i;

  for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function () {
      this.classList.toggle("course_builder_dashboard_list_accordion_active");
      var panel = this.nextElementSibling;
      if (panel.style.display === "block") {
        panel.style.display = "none";
      } else {
        panel.style.display = "block";
      }
    });
  }
</script>



