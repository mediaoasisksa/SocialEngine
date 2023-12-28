  <?php if(count($this->categories) > 0): ?>
    <form method="post" class="global_form_popup">
      <div>
        <h3><?php echo $this->translate("Delete Course Category?") ?></h3>
        <p>
          <?php echo $this->translate("Are you sure that you want to delete this category? It will not be recoverable after being deleted.") ?>
        </p>
        <br />
        <div>
          <?php $categories = $this->categories;
          ?>

          <?php if(count($categories) > 0): ?>
            <p><?php echo $this->translate("The selected category will be replaced with current category for all courses created under this category.") ?></p>
            <select name="category" id="category">
              <?php foreach($categories as $index=>$row): ?>
                <option value='<?=$row['category_id'];?>'><?=$row['category_name'];?>  </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>

        </div>
        <br />
        <p>
          <input type="hidden" name="confirm" value="<?php echo $this->course_id?>"/>
          <button type='submit'><?php echo $this->translate("Delete") ?></button>
          or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>cancel</a>
        </p>
      </div>
    </form>

  <?php else: ?>
    <p><?=$this->translate('Cannot delete single category.Please add a category then delete.')?><p>

    <?php endif; ?>

    <?php if( @$this->closeSmoothbox ): ?>
      <script type="text/javascript">
        TB_close();
      </script>
      <?php endif; ?>
