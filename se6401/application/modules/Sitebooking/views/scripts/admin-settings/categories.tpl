
<?php $this->tinyMCESEAO()->addJS(); ?>

<?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:false;'></iframe>

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

<div class='settings clr'>
  <h3><?php echo $this->translate("Services Categories") ?></h3>

  <p class="description"><?php echo $this->translate('Below, you can add and manage the various categories, sub-categories and 3rd level categories for the Services on your site. Sub-categories are very useful as they allow you to further categorize and organize the Services on your site beyond the superficial categories. You can also add Icons, URL Components and Meta Information for categories, sub-categories and 3rd level categories. To do so, click on desired category name, edit it and click on "Save Changes" to save your changes.'); ?></p>
</div>

<div class="clr mtop10">
  <div class="sitebooking_categories_left fleft">      

    <a class="buttonlink seaocore_icon_add" href="<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'categories', 'category_id' => 0, 'perform' => 'add'), "admin_default", true); ?>"><?php echo $this->translate("Add Category"); ?></a>
    <br />

    <div id='categories' class="sitebooking_cat_list_wrapper clr">
      <?php 

      ?>
      <?php foreach ($this->categories as $value): 

        ?>
        <div id="cat_<?php echo $value['category_id']; ?>" class="sitebooking_cat_list">
          <input type="hidden" id="cat_<?php echo $value['category_id']; ?>_input_count" value="<?php echo $value["count"] ?>">
          <?php $category_name = $this->translate($value['category_name']); ?>

          <?php $url = $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'categories', 'category_id' => $value['category_id'], 'perform' => 'edit'), "admin_default", true); ?>
          <?php $link = "<a href='$url' title='$category_name' id='cat_" . $value['category_id'] . "_title' >" . $category_name . "</a>"; ?>

          <div class="sitebooking_cat">
            <a href="javascript:void(0);" onclick="showsubcate(1,<?php echo $value['category_id']; ?>, 1);" id="hide_cate_<?php echo $value['category_id']; ?>" title="<?php echo $this->translate('Collapse'); ?>" class="sitebooking_cat_showhide"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitebooking/externals/images/minus.png' border='0' /></a>
            <a href="javascript:void(0);" onclick="showsubcate(2,<?php echo $value['category_id']; ?>, 1);" style="display:none;" id="show_cate_<?php echo $value['category_id']; ?>" title="<?php echo $this->translate('Expand'); ?>" class="sitebooking_cat_showhide"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitebooking/externals/images/plus.png' border='0' /></a>
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitebooking/externals/images/folder_open_yellow.gif' border='0' class='sitebooking_cat_handle'/>
            <div class="sitebooking_cat_det <?php if ($this->category_id == $value['category_id']): ?> sitebooking_cat_selected <?php endif; ?>">
              <span class="sitebooking_cat_det_options" title="Number of times this Category have been used">
                [<?php echo $value["count"] ?>] | 

                <a class="smoothbox" href="<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'mapping-category', 'category_id' => $value['category_id']), "admin_default", true); ?>" title="<?php echo $this->translate("Delete Category"); ?>"><?php echo $this->translate("Delete"); ?></a> 
              </span>
              <?php echo "<span class='sitebooking_cat_det_name' id='cat_" . $value['category_id'] . "_span'>$link</span>" ?> 
            </div>      
            <?php $url = $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'categories', 'category_id' => $value['category_id'], 'perform' => 'add'), "admin_default", true); ?>
            <?php $subcate = $this->translate("Sub Categories") . " - <a href='$url'> " . $this->translate("[Add New]") . "</a>" ?>
            <?php echo "<div class='sitebooking_cat_new' id=subcate_admin_new_" . $value["category_id"] . ">$subcate</div>" ?>
          </div>

          <script type="text/javascript">
              window.addEvent('domready', function() {
                createSortable("subcats_<?php echo $value['category_id'] ?>", "img.handle_subcat_<?php echo $value['category_id'] ?>");
              });
          </script>
          <div id="subcats_<?php echo $value['category_id']; ?>" class="sitebooking_sub_cat_wrapper">
            <?php foreach ($value['sub_categories'] as $subcategory): ?>
              <div id="cat_<?php echo $subcategory['sub_cat_id']; ?>" class="sitebooking_cat_list">
                <input type="hidden" id="cat_<?php echo $subcategory['sub_cat_id']; ?>_input_count" value="<?php echo $subcategory['count'] ?>">
                <?php $subcatname = $this->translate($subcategory['sub_cat_name']); ?>
                <?php $url = $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'categories', 'category_id' => $subcategory["sub_cat_id"], 'perform' => 'edit'), "admin_default", true); ?>
                <?php $subcats = "<a href='$url' title='$subcatname' id='cat_" . $subcategory["sub_cat_id"] . "_title'>$subcatname</a>" ?>
                <div class="sitebooking_cat">
                  <a href="javascript:void(0);" onclick="showsubcate(1, <?php echo $subcategory['sub_cat_id'] ?>, '2');" id="treehide_cate_<?php echo $subcategory['sub_cat_id']; ?>" title="<?php echo $this->translate('Collapse'); ?>" class="sitebooking_cat_showhide"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitebooking/externals/images/minus.png' border='0' /></a>
                  <a href="javascript:void(0);" onclick="showsubcate(2, <?php echo $subcategory['sub_cat_id'] ?>, '2');" style="display:none;" id="treeshow_cate_<?php echo $subcategory['sub_cat_id']; ?>" title="<?php echo $this->translate('Expand'); ?>" class="sitebooking_cat_showhide"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitebooking/externals/images/plus.png' border='0' /></a>
                  <?php echo "<img src='".$this->layout()->staticBaseUrl."application/modules/Sitebooking/externals/images/folder_open_green.gif' border='0' class='sitebooking_cat_handle handle_subcat_" . $value['category_id'] . "'>" ?>
                  <div class="sitebooking_cat_det <?php if ($this->category_id == $subcategory['sub_cat_id']): ?> sitebooking_cat_selected <?php endif; ?>">
                    <span class="sitebooking_cat_det_options">[<?php echo $subcategory["count"] ?>] | 

                      <a class="smoothbox" href="<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'delete-category', 'category_id' => $subcategory['sub_cat_id']), "admin_default", true); ?>" title="<?php echo $this->translate("Delete Category"); ?>"><?php echo $this->translate("Delete"); ?></a>
                    </span>
                    <?php echo "<span class='sitebooking_cat_det_name' id='cat_" . $subcategory["sub_cat_id"] . "_span'>$subcats</span>" ?>
                  </div>  
                  <?php $url = $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'categories', 'category_id' => $subcategory['sub_cat_id']), "admin_default", true); ?>
                  <?php $treesubcate = $this->translate('3%s Level Category', "<sup>rd</sup>") . " - <a href='$url'> " . $this->translate("[Add New]") . "</a>" ?>
                  <?php $subcategory_id = $subcategory['sub_cat_id'];
                  ?>
                  <?php echo "<div class='sitebooking_cat_new' id=treesubcate_admin_new_" . $subcategory["sub_cat_id"] . ">$treesubcate</div>";
                  ?>  
                </div>

                <script type="text/javascript">
                <!--
                  window.addEvent('domready', function() {
                  createSortable("treesubcats_<?php echo $subcategory['sub_cat_id'] ?>", "img.handle_treesubcat_<?php echo $subcategory['sub_cat_id'] ?>");
                });
                //-->
                </script>
                <div id="treesubcats_<?php echo $subcategory['sub_cat_id']; ?>" class="sitebooking_sub_cat_wrapper">
                  <?php if (isset($subcategory['tree_sub_cat'])): ?>
                    <?php foreach ($subcategory['tree_sub_cat'] as $treesubcategory):

                     ?>

                      <div id="cat_<?php echo $treesubcategory['tree_sub_cat_id']; ?>" class="sitebooking_cat_list">
                        <input type="hidden" id="cat_<?php echo $treesubcategory['tree_sub_cat_id']; ?>_input_count" value="<?php echo $treesubcategory['count'] ?>">
                        <?php $treesubcatname = $this->translate($treesubcategory['tree_sub_cat_name']); ?>
                        <?php $url = $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'categories', 'category_id' => $treesubcategory["tree_sub_cat_id"], 'perform' => 'edit'), "admin_default", true); ?>
                        <?php $treesubcats = "<a href='$url' title='$treesubcatname' id='cat_" . $treesubcategory["tree_sub_cat_id"] . "_title'>$treesubcatname</a>" ?>
                        <div class="sitebooking_cat">
                          <?php echo "<img src='".$this->layout()->staticBaseUrl."application/modules/Sitebooking/externals/images/folder_open_blue.gif' border='0'  class='sitebooking_cat_handle handle_treesubcat_" . $subcategory['sub_cat_id'] . "'>" ?>
                          <div class="sitebooking_cat_det <?php if ($this->category_id == $treesubcategory['tree_sub_cat_id']): ?> sitebooking_cat_selected <?php endif; ?>">
                            <span class="sitebooking_cat_det_options">[<?php echo $treesubcategory["count"] ?>] |

                              <a class="smoothbox" href="<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'delete-category', 'category_id' => $treesubcategory['tree_sub_cat_id']), "admin_default", true); ?>" title="<?php echo $this->translate("Delete Category"); ?>"><?php echo $this->translate("Delete"); ?></a>
                            </span>
                            <?php echo "<span class='sitebooking_cat_det_name' id='cat_" . $treesubcategory["tree_sub_cat_id"] . "_span'>$treesubcats</span>" ?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>          
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="sitebooking_categories_right">
    <div class='settings'>
      <?php echo $this->form->render($this); ?>
    </div>
  </div>
</div>  

<script type="text/javascript">
  function createSortable(divId, handleClass)
  {
    new Sortables($(divId), {handle: handleClass, onComplete: function() {
        changeorder(this.serialize(), divId);
      }
    });
  }

  Sortables.implement({
    serialize: function() {
      var serial = [];
      this.list.getChildren().each(function(el, i) {
        serial[i] = el.getProperty('id');
      }, this);
      return serial;
    }
  });

  window.addEvent('domready', function() {
    createSortable('categories', 'img.sitebooking_cat_handle');
  });

  //THIS FUNCTION CHANGES THE ORDER OF ELEMENTS
  function changeorder(sitebookingorder, divId)
  {
    $('ajaxframe').src = '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'settings', 'action' => 'categories'), 'admin_default', true) ?>?task=changeorder&sitebookingorder=' + sitebookingorder + '&divId=' + divId;
  }

  function showsubcate(option, cate_id, level) {

    if (level == 1) {
      if (option == 1) {
        $('subcate_admin_new_' + cate_id).style.display = 'none';
        $('subcats_' + cate_id).style.display = 'none';
        $('hide_cate_' + cate_id).style.display = 'none';
        $('show_cate_' + cate_id).style.display = 'block';
      } else if (option == 2) {
        $('subcate_admin_new_' + cate_id).style.display = 'block';
        $('subcats_' + cate_id).style.display = 'block';
        $('hide_cate_' + cate_id).style.display = 'block';
        $('show_cate_' + cate_id).style.display = 'none';
      }
    } else if (level == 2) {
      if (option == 1) {
        $('treesubcate_admin_new_' + cate_id).style.display = 'none';
        $('treesubcats_' + cate_id).style.display = 'none';
        $('treehide_cate_' + cate_id).style.display = 'none';
        $('treeshow_cate_' + cate_id).style.display = 'block';
      } else if (option == 2) {
        $('treesubcate_admin_new_' + cate_id).style.display = 'block';
        $('treesubcats_' + cate_id).style.display = 'block';
        $('treehide_cate_' + cate_id).style.display = 'block';
        $('treeshow_cate_' + cate_id).style.display = 'none';
      }
    }
  }

  if ($('top_content')) {
    <?php
    echo $this->tinyMCESEAO()->render(array('element_id' => '"top_content"',
      'language' => $this->language,
      'directionality' => $this->directionality,
      'upload_url' => $this->upload_url));
    ?>
  }

  if ($('bottom_content')) {
    <?php
    echo $this->tinyMCESEAO()->render(array('element_id' => '"bottom_content"',
      'language' => $this->language,
      'directionality' => $this->directionality,
      'upload_url' => $this->upload_url));
    ?>
  }
</script>
