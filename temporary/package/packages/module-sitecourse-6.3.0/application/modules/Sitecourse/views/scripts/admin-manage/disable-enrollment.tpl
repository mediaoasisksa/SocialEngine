  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Disable Enrollment") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to disable the enrollment for this course?") ?>
      </p>
      <br />
      </div>
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->course_id?>"/>
        <button type='submit'><?php echo $this->translate("Disable Enrollment") ?></button>
       <?php echo $this->translate("or") ?><a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
