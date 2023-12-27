<?php foreach ($this->tagData as $value): ?>
<span class="sitebooking_tags"> 
  <a href='<?php echo $this->url(array('action' => 'index'), "sitebooking_provider_general"); ?>?tag=<?php echo urlencode($value->text) ?>&tag_id=<?php echo $value->tag_id ?>' title=''><?php echo $value->text; ?></a>
</span>

<?php endforeach; ?>  