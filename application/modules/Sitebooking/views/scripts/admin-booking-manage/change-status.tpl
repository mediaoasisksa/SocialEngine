<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Change Booking Status?") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to change status of this booking?") ?>
    </p>
    <br />
    <p>
      <select id="" name="status">
        
         <option value="booked" <?php if ($this->status === 'booked') echo "selected"; ?> ><?php echo 'Pending for Approval' ?></option>

        <option value="pending" <?php if ($this->status === 'pending') echo "selected"; ?> ><?php echo 'Approved' ?></option>

        <option value="rejected" <?php if ($this->status === 'rejected') echo "selected"; ?> ><?php echo $this->translate("Reject") ?></option>

        <option value="canceled" <?php if ($this->status === 'canceled') echo "selected"; ?> ><?php echo $this->translate("Cancel") ?></option>

        <option value="completed" <?php if ($this->status === 'completed') echo "selected"; ?> ><?php echo $this->translate("Mark as Completed") ?></option>
      </select>
      <button type='submit'><?php echo $this->translate("Change") ?></button>
      <?php echo $this->translate(" or ") ?> 
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
      <?php echo $this->translate("cancel") ?></a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>