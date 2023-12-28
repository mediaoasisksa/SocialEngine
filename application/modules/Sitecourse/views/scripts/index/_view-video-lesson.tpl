<?php $video = $this->video; ?>

<?php if($video['type'] == 'upload'): ?>
<video id="video" width="100%" height="340px" controls>
    <source src="<?php echo $this->video_url; ?>" type="video/mp4" />
        Please try after some time
</video>
<?php else: ?>
    <iframe width="700" height="400" src="<?php echo $this->video_url; ?>" frameborder="0" allowfullscreen></iframe>
<?php endif; ?>
