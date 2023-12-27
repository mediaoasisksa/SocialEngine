<h2>
  <?php echo $this->translate('Services Booking & Appointments Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->childNavigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->childNavigation)->render() ?>
  </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Category to Service Profile Mapping") ?> </h3>
        <p class="form-description">
          <?php echo $this->translate("This mapping will associate a Service Profile Type with Main Category only. After such a mapping for a category, services belonging to that category will be able to have profile information fields associated with that profile type.") ?>
        </p>

        <?php

         if (count($this->categories) > 0): ?>
          <table class='admin_table sitebooking_mapping_table' width="100%">
            <thead>
              <tr>
                <th>
            <div class="sitebooking_mapping_table_name fleft"><b class="bold"><?php echo $this->translate("Category Name") ?></b></div>
            <div class="sitebooking_mapping_table_value fleft"><b class="bold"><?php echo $this->translate("Associated Profile") ?></b></div>
            <div class="sitebooking_mapping_table_option fleft"><b class="bold"><?php echo $this->translate("Mapping") ?></b></div>
            </th>
            </tr>
            </thead>
            <tbody>
            
              <?php foreach ($this->categories as $category): 

                ?> 
                    
                <tr>
                  <td>
                    <div class="sitebooking_mapping_table_name fleft">
                      <span><b class="bold"><?php echo $category['category_name']; ?></b></span>
                      <?php if (Count($category['sub_categories']) >= 1): ?>
                        <span id="fewer_link_cats_<?php echo $category['category_id']; ?>" >    
                          <a href="javascript:void(0)" onclick="fewer_subcats('<?php echo $category['category_id']; ?>');" title="<?php echo $this->translate('Click to hide sub-categories') ?>">[-]</a>
                        </span>                      

                        <span id="more_link_cats_<?php echo $category['category_id']; ?>" style="display:none;">    
                          <a href="javascript:void(0)" onclick="more_subcats('<?php echo $category['category_id']; ?>');" title="<?php echo $this->translate('Click to show sub-categories') ?>">[+]</a>
                        </span>

                      <?php endif; ?>
                    </div>
                    <div class="sitebooking_mapping_table_value fleft">
                      <ul>
                        <li><?php echo $this->translate($category['cat_profile_type_label']); ?></li>
                      </ul>
                    </div>

                    <div class="sitebooking_mapping_table_option fleft">
                      <?php if (empty($category['cat_profile_type_id'])): ?>
                        <?php
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $category['category_id']), $this->translate('Add'), array(
                          'class' => 'smoothbox',
                        ))
                        ?>
                      <?php else: ?>

                        <?php if ($this->totalProfileTypes > 1): ?>
                          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'profilemaps', 'action' => 'edit', 'category_id' => $category['category_id'], 'profile_type' => $category['cat_profile_type_id']), $this->translate('Edit'), array('class' => 'smoothbox')) ?> | 
                        <?php endif; ?>    

                        <?php
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'profilemaps', 'action' => 'remove', 'category_id' => $category['category_id']), $this->translate('Remove'), array(
                          'class' => 'smoothbox',
                        ))
                        ?>
                <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>                  
            </tbody>
          </table>
        <?php else: ?>
          <br/>
          <div class="tip">
            <span><?php echo $this->translate("There are currently no categories to be mapped.") ?></span>
          </div>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>