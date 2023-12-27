<div id='getCertificate' class="getCertification" style='display:none' > <?php echo $this->htmlLink(array('route' => 'sitecourse_learning', 'module' => 'sitecourse', 'controller' => 'learning', 'action' => 'preview-certificate', 'course_id' =>$this->course_id),'Get Certificate', array()); ?>
</div>

<div class="aside_lms_course_content">
  <div class="aside_lms_course_block">
    <div class="aside_lms_course_block_listing">
     <?php if(count($this->topics) > 0): ?>
      <?php foreach ($this->topics as $index => $value): ?>
        <div class="aside_lms_course_block_list">
          <input class="aside_lms_course_block_list_dropdown" type="checkbox" id="topic_<?php  echo $value['topic_id']; ?>">
          <label class="aside_lms_course_block_list_label" for="topic_<?php  echo $value['topic_id']; ?>"><?php  echo $value['title']; ?></label>
          <?php foreach ( $this->lessons as $index1 => $value1 ): ?>
           <?php if($value['topic_id'] == $value1['topic_id']): ?>
            <div class="aside_lms_course_block_list_content">
              <span class="aside_lms_course_block_list_check">
                <?php $change=''; 
                if(array_key_exists($value1['lesson_id'],$this->completedLessons)){
                  $change = 'element_value_changed';
                }
                ?>
                <?php if($value1['type']=='doc'): ?>
                  <label id="label_<?php echo $value1['lesson_id']; ?>" class="aside_lms_course_block_list_listing_label doc_type <?php echo $change;?>">
                     <a onclick= "display('<?php echo $value['topic_id']; ?>','<?php echo $value1['lesson_id']; ?>','<?php echo $value1['course_id']; ?>')"><span><?php  echo $value1['title']; ?></span></a>
                    </label>
                  <?php endif; ?>

                  <?php if($value1['type']=='text'): ?>
                    <label id="label_<?php echo $value1['lesson_id']; ?>" class="aside_lms_course_block_list_listing_label text_type <?php echo $change;?>">          
                      <a onclick= "display('<?php echo $value['topic_id']; ?>','<?php echo $value1['lesson_id']; ?>','<?php echo $value1['course_id']; ?>')"><span><?php  echo $value1['title']; ?></span></a>
                    </label>
                  <?php endif; ?>
                  
                  
                  <?php if($value1['type']=='videoinvite'): ?>
                    <label id="label_<?php echo $value1['lesson_id']; ?>" class="aside_lms_course_block_list_listing_label text_type <?php echo $change;?>">          
                      <a onclick= "display('<?php echo $value['topic_id']; ?>','<?php echo $value1['lesson_id']; ?>','<?php echo $value1['course_id']; ?>')"><span><?php  echo $value1['title']; ?></span></a>
                    </label>
                  <?php endif; ?>

                  <?php if($value1['type']=='video'): ?>
                    <label id="label_<?php echo $value1['lesson_id']; ?>" class="aside_lms_course_block_list_listing_label video_type <?php echo $change;?>">          
                      <a onclick= "display('<?php echo $value['topic_id']; ?>','<?php echo $value1['lesson_id']; ?>','<?php echo $value1['course_id']; ?>')"><span><?php  echo $value1['title']; ?></span></a>
                    </label>
                  <?php endif; ?>
                </span>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

</div>




<script type="text/javascript">

  let ownerId = <?php echo $this->owner_id; ?>;
  let viewerId = <?php echo $this->viewer_id; ?>;
  let lessonCount = <?php echo $this->lessonCount; ?>;
  let topics = <?php echo json_encode($this->topics); ?>;
  let lessons = <?php echo json_encode($this->lessons); ?>;
  let completedLessonCount = <?php echo $this->completedLessonCount; ?>;


  en4.core.runonce.add(function(){ 
    let issuePermission =  '<?php echo $this->issuePermission; ?>';
    let issuedCertificate =  '<?php echo $this->issuedCertificate; ?>';
    if(issuePermission == true && (issuedCertificate == true || lessonCount == completedLessonCount) && (ownerId != viewerId)){
     let element = document.getElementById('getCertificate');
     element.style.display = 'flex';
   }
   
   display(topics[0]['topic_id'],lessons[0]['lesson_id'],lessons[0]['course_id']);
 });


  function display(topic_id,lesson_id,course_id){
    let url1 = '<?php echo $this->url(array('action'=>'toggle-topiccomplete','course_id'=>''),'sitecourse_learning',true); ?>'
    url1 += '/'+course_id;
    scriptJquery.ajax({
      url : url1,
      methodType : 'post',
      data : {
        format : 'json',
        is_ajax : true,
        topic_id : topic_id,
        lesson_id : lesson_id
      },
      success : function(responseJSON) {
        if(responseJSON.completed){
          completedLessonCount++;
        }
        let issuePermission =  '<?php echo $this->issuePermission; ?>';
        let issuedCertificate =  '<?php echo $this->issuedCertificate; ?>';
        let element = document.getElementById('getCertificate');
        if(issuePermission == true && (ownerId != viewerId)){
          if(lessonCount == completedLessonCount){          
            element.style.display = 'flex';
          } else if(issuedCertificate != true && lessonCount != completedLessonCount) {
            element.style.display = 'none';
          }
        }
      }
    });

    let url = '<?php echo $this->url(array('action' => 'display', 'lesson_id' => '', 'course_id' => $this->course_id), 'sitecourse_learning_specific', true);?>'; 
    url+='/'+lesson_id;
    scriptJquery.ajax({
      url : url,
      data : {
        format : 'json'
      },
      success : function(responseJSON) {
        let labelElement = document.getElementById("label_"+lesson_id);
        labelElement.classList.add("element_value_changed");
        const lesson = responseJSON.lesson;
        // show error
        if(!lesson){
          return;
        }
        let html = ''
        let video_url = responseJSON.video_url;
        if(lesson['type'] == 'text'){
          html = `<div class="text-course">
          ${lesson['text']}
          </div>`
        }
        
        if(lesson['type'] == 'videoinvite'){
          html = `<div class="text-course">
          <a href="${lesson['text']}" target="_blank">${lesson['title']}</a>
          </div>`
        }
        
        
        if(lesson['type'] == 'doc'){
          let docDownloadUrl = responseJSON.docDownloadUrl;
          html = `<div class="doc-lesson">
          <p>${docDownloadUrl}</p>
          </div>`
        }

        if(lesson['type'] == 'video') {
          if(responseJSON.video_type == 'upload') {
            html = `<div class="video-course"><video id="video" width="100%" height="340px" controls autoplay>
            <source src="${video_url}" type="video/mp4" />
            Please try after some time
            </video></div>`;
          } else {
            html = `<div class="video-course">
            <iframe src="${video_url}" title="Video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen autoplay></iframe>
            </div>`
          }
        }

        document.getElementById('learning-Content').innerHTML = html;
      }
    });
  }

</script>
