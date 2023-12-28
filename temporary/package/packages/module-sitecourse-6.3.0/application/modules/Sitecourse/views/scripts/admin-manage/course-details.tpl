<?php $course = $this->course; ?>
<h2><?php echo $this->translate('Course Details'); ?></h2>

<div class="course-details">
  <p><?php echo $this->translate('Title')?> : <?= $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$this->translate($course->getTitle()),array('target'=>'_blank')); ?></p>
  <p><?php echo $this->translate('Owner'); ?> : <?= $this->htmlLink($course->getOwner()->getHref(), $course->getOwner()->getTitle(), array('target' => '_blank')); ?></p>
  <p><?php echo $this->translate('Creation Date');?> : <?= $course->creation_date; ?></p>
  <p><?php echo $this->translate('Price'); ?> : <?= $course->price; ?></p>
  <p><?php echo $this->translate('Duration'); ?> : <?= $course->duration; ?></p>
  <p><?php echo $this->translate('Difficulty Level');?> : <?= $course->difficulty_level; ?></p>
  <p><?php echo $this->translate('Reviews');?> : <?= $this->reviews; ?></p>
  <p><?php echo $this->translate('Added as Favourites');?> : <?= $this->favourites; ?> </p>
  <p><?php echo $this->translate('Enrollment Count'); ?>: <?= $this->enrolled_count; ?></p>
  <p><?php echo $this->translate('Category'); ?>: <?= $this->category; ?></p>
  <p><?php echo $this->translate('Difficulty'); ?>: <?= $this->difficulty; ?></p>
</div>
<div class="profile-image">
  <img src="<?= $this->image; ?>" alt="Profile image"/>
</div>
<button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Cancel'); ?></button>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
