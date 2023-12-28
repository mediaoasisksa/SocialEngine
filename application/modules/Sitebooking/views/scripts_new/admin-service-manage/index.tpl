<?php echo $this->form->render($this); ?>

<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected service entries?');?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  console.log(multidelete_form);
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<h2>
  <?php echo $this->translate('Services Booking & Appointments Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>
  
<h3><?php echo $this->translate('Manage Services'); ?></h3>
<p><?php echo $this->translate('This page lists all the services your users have posted. You can use this page to monitor these services and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific service entries. Leaving the filter fields blank will show all the service entries on your social network. Here, you can also make services featured / un-featured, sponsored / un-sponsored, new / remove from new, and approve / dis-approve them.'); ?></p><br>

<div class="admin_search ">
    <div class="search">
        <form method="get" class="global_form_box" action="" width="100%">

            <div>
                <label>
                    <?php echo $this->translate("Title") ?>
                </label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="title" value="<?php echo $this->translate($this->title) ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Provider") ?>
                </label>  
                <?php if (empty($this->provider)): ?>
                    <input type="text" name="provider" /> 
                <?php else: ?> 
                    <input type="text" name="provider" value="<?php echo $this->translate($this->provider) ?>" />
                <?php endif; ?>
            </div>

            <div id="category_id-wrapper" class="form-wrapper">
                
                <div id="category_id-label" class="form-label">
                    <label for="category_id" class="optional">Category</label>
                </div>

                <div id="category_id-element" class="form-element">
                    <select name="category_id" id="category_id" onclick=" sitebooking_addOptions(this.value)" >
                    </select>
                </div>

            </div>

            <!-- sub_category select element -->
            <div id="first_level_category_id-wrapper" class="form-wrapper" style="display:none">
                <!-- style="display:none" -->
                <div id="first_level_category_id-label" class="form-label">
                    <label for="first_level_category_id" class="optional">Sub-Category-1</label>
                </div>

                <div id="first_level_category_id-element" class="form-element">
                    <select name="first_level_category_id" id="first_level_category_id" onclick='sitebooking_addSubOptions(this.value)'>
                    </select>
                </div>

            </div>

            <!-- sub_sub_category select element -->
            <div id="second_level_category_id-wrapper" class="form-wrapper" style="display:none">

                <div id="second_level_category_id-label" class="form-label">
                    <label for="second_level_category_id" class="optional">Sub-Category-2</label>
                </div>

                <div id="second_level_category_id-element" class="form-element">
                    <select name="second_level_category_id" id="second_level_category_id"></select>
                </div>

            </div>  


            <script type="text/javascript">

                function sitebooking_Options() {

                    var data = <?php echo $jsondata = json_encode(Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray());?>;

                    var categories = [{"category_id" : "-1", "category_name" : "select "}];

                    //Pushing data into categories array
                    for(let i = 0; i < data.length; i++)
                    {
                        if(data[i]['first_level_category_id'] == 0 && data[i]['second_level_category_id'] == 0){
                            categories.push({
                                            "category_id" : data[i]['category_id'],
                                            "category_name" : data[i]['category_name']
                                }
                            );
                        }

                    }

                    var sub_categories_id = document.getElementById("first_level_category_id-wrapper");

                    if(categories.length > 1)
                    {
                        for(let i = 0; i < categories.length; i++ )
                        {
                            var x = document.getElementById("category_id");
                            var option = document.createElement("option");
                            option.value= categories[i]['category_id'];
                            option.id = categories[i]['category_id'];
                            if( categories[i]['category_id'] == <?php echo $this->category_id ?>)
                            {
                                option.setAttribute('selected', true);
                            }
                            option.text = categories[i]['category_name'];
                            x.add(option);
                        }

                    }

                    else
                    {
                        sub_categories_id.style.display = "none";

                    }   

                }

                sitebooking_Options();

            </script>


            <script type="text/javascript">

                function sitebooking_addOptions(element_value) {

                  console.log(element_value);

                    var data = <?php echo $jsondata = json_encode(Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray());?>


                    var categories = [{"category_id" : "-1", "category_name" : " select "}];

                    //removing select options
                    document.getElementById("first_level_category_id").getElements('option').forEach(function(el) {
                    el.parentNode.removeChild(el);
                    });


                    //Pushing data into categories array
                    for(let i = 0; i < data.length; i++)
                    {
                      if(data[i]['first_level_category_id'] == element_value && data[i]['second_level_category_id'] == 0){
                        categories.push({
                              "category_id" : data[i]['category_id'],
                              "category_name" : data[i]['category_name']
                        }
                        );
                      }

                    }

                    var sub_categories_id = document.getElementById("first_level_category_id-wrapper");

                    if(categories.length > 1)
                    {
                        sub_categories_id.style.display = "block";
                        for(let i = 0; i < categories.length; i++ )
                        {
                            var x = document.getElementById("first_level_category_id");
                            var option = document.createElement("option");
                            option.value= categories[i]['category_id'];
                            option.id = categories[i]['category_id'];
                            if( categories[i]['category_id'] == <?php echo $this->subcategory_id ?>)
                            {
                              console.log(<?php echo $this->subcategory_id ?>);
                              option.setAttribute('selected', true);
                            }
                            option.text = categories[i]['category_name'];
                            x.add(option);
                            console.log(x);
                        }
                    }
                    else
                    {
                        show();
                        sub_categories_id.style.display = "none";
                    }  

                    var sub_sub_categories_id = document.getElementById("second_level_category_id-wrapper");
                    sub_sub_categories_id.style.display = "none";

                }

                if(<?php echo $this->category_id ?>)
                 sitebooking_addOptions(<?php echo $this->category_id ?>);

            </script>


            <script type="text/javascript">

               var sub_sub_categories_id = document.getElementById("second_level_category_id-wrapper");
                  sub_sub_categories_id.style.display = "none";

                function show() {
                  var sub_sub_categories_id = document.getElementById("second_level_category_id-wrapper");
                  sub_sub_categories_id.style.display = "none";
                }  

                function sitebooking_addSubOptions(element_value) {

                    var data = <?php echo $jsondata = json_encode(Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray());?>

                    var categories = [{"category_id" : "-1", "category_name" : " select "}];

                    //removing select options
                    document.getElementById("second_level_category_id").getElements('option').forEach(function(el) {
                    el.parentNode.removeChild(el);
                    });

                    for(let i = 0; i < data.length; i++)
                    {
                      if(data[i]['second_level_category_id'] == element_value){
                        categories.push({
                              "category_id" : data[i]['category_id'],
                              "category_name" : data[i]['category_name']
                        }
                        );
                      }

                    }

                    var sub_sub_categories_id = document.getElementById("second_level_category_id-wrapper");

                    if(categories.length > 1)
                    {
                        sub_sub_categories_id.style.display = "block";
                        for(let i = 0; i < categories.length; i++ )
                        {
                            var x = document.getElementById("second_level_category_id");
                            var option = document.createElement("option");
                            option.value= categories[i]['category_id'];
                            option.id = categories[i]['category_id'];
                            if( categories[i]['category_id'] == <?php echo $this->subsubcategory_id ?>)
                            {
                              option.setAttribute('selected', true);
                            }
                            option.text = categories[i]['category_name'];
                            x.add(option);
                        }
                    }
                    else
                    {
                        sub_sub_categories_id.style.display = "none";
                    }   
                }

                if(<?php echo $this->subcategory_id ?>)
                    sitebooking_addSubOptions(<?php echo $this->subcategory_id ?>);

            </script>    


            <div>
                <label>
                    <?php echo $this->translate("Featured") ?>  
                </label>
                <select id="" name="featured">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->featured == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1" <?php if ($this->featured == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
                </select>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Sponsored") ?> 
                </label>
                <select id="sponsored" name="sponsored">
                    <option value="0"  ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->sponsored == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1"  <?php if ($this->sponsored == 1) echo "selected"; ?>><?php echo $this->translate("No") ?></option>
                </select>
            </div>    

            <div>
                <label>
                    <?php echo $this->translate("New") ?> 
                </label>
                <select id="newlabel" name="newlabel">
                    <option value="0"  ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->newlabel == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1"  <?php if ($this->newlabel == 1) echo "selected"; ?>><?php echo $this->translate("No") ?></option>
                </select>
            </div> 

            <div>
                <label>
                    <?php echo $this->translate("Trending") ?> 
                </label>
                <select id="Hot" name="Hot">
                    <option value="0"  ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->Hot == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1"  <?php if ($this->Hot == 1) echo "selected"; ?>><?php echo $this->translate("No") ?></option>
                </select>
            </div>   

             <div style="margin-left: 0; margin-top: 10px;">
                <label>
                    <?php echo $this->translate("Approved") ?>  
                </label>
                <select id="sponsored" name="approved">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->approved == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1" <?php if ($this->approved == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
                </select>
            </div>

            <div style=" margin-top: 10px;">
                <label>
                    <?php echo $this->translate("Published") ?>  
                </label>
                <select id="" name="status">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->statu
					== 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1" <?php if ($this->status == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
                </select>
            </div>

            <div style=" margin-top: 10px;">
                <label>
                    <?php echo $this->translate("Browse By") ?> 
                </label>
                <select id="" name="servicebrowse">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="1" <?php if ($this->servicebrowse == 1) echo "selected"; ?> ><?php echo $this->translate("Most Viewed") ?></option>
                    <option value="2" <?php if ($this->servicebrowse == 2) echo "selected"; ?> ><?php echo $this->translate("Most Recent") ?></option>
                </select>
            </div>
            
            <div class="mtop10" style=" margin-top: 27px;">
                <button type="submit" name="search" value="search"><?php echo $this->translate("Search") ?></button>
            </div>
        </form>
    </div>
</div>

<div class='admin_members_results'>
    <?php $counter = $this->paginator->getTotalItemCount(); ?>
    <?php if (!empty($counter)): ?>
        <div class="">
            <?php echo $this->translate(array('%s services found.', '%s services found.', $counter), $this->locale()->toNumber($counter)) ?>
        </div>
    <?php elseif($this->search == 1): ?>
        <div class="tip"><span>
            <?php echo $this->translate("No results were found.") ?></span>
        </div>
    <?php return; endif; ?>
    
</div>

<?php if( count($this->paginator ) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th class='admin_table_short'>ID</th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Title") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Rating") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Provider") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Amount") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Approved") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Published") ?></th>
      <th align="center" class="admin_table_centered"  title="<?php echo $this->translate('Featured'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('F'); ?></a></th>
      <th align="center" class="admin_table_centered"  title="<?php echo $this->translate('Sponserd'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('sponserde', 'ASC');"><?php echo $this->translate('S'); ?></a></th>
      <th align="center" class="admin_table_centered"  title="<?php echo $this->translate('New'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('new', 'ASC');"><?php echo $this->translate('N'); ?></a></th>
      <th align="center" class="admin_table_centered"  title="<?php echo $this->translate('Trending'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('Trending', 'ASC');"><?php echo $this->translate('T'); ?></a></th>
      

      <th align="center" class="admin_table_centered"><?php echo $this->translate("Date") ?></th>
      <th align="center" class="admin_table_centered"><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->getTitle() ?></td>
        <td align="center" class="admin_table_centered"><?php echo $item->rating; ?></td>
        <td><?php echo $item->provider_title ?></td>
        <td><?php echo $this->locale()->toCurrency($item['price'],Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></td>
        <?php if ($item->approved == 1): ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'approved', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Make Dis-Approved')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'approved', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Make Approved')))) ?></td>
        <?php endif; ?> 
        <td align="center" class="admin_table_centered"><?php if($item->status == "1")echo "Yes";else echo "No"; ?></td>

        <?php if ($item->featured == 1): ?> 
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'featured', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.gif', '', array('title' => $this->translate('Make Un-featured')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'featured', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.gif', '', array('title' => $this->translate('Make Featured')))) ?></td>
        <?php endif; ?>

        <?php if ($item->sponsored == 1): ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'sponsored', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('title' => $this->translate('Make Unsponsored')))); ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'sponsored', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unsponsored.png', '', array('title' => $this->translate('Make Sponsored')))); ?>
        <?php endif; ?>   

        <?php if ($item->newlabel == 1): ?> 
          <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'newlabel', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitebooking/externals/images/icon/new.png', '', array('title' => $this->translate('Remove New Label')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'newlabel', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitebooking/externals/images/icon/new-disable.png', '', array('title' => $this->translate('Set New Label')))) ?></td>
        <?php endif; ?>
        <?php if ($item->hot == 1): ?> 
          <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'hot', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitebooking/externals/images/admin/trending.png', '', array('title' => $this->translate('Remove Trending')))) ?></td>
        <?php else: ?>
          <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitebooking', 'controller' => 'service-manage', 'action' => 'hot', 'ser_id' => $item->ser_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitebooking/externals/images/admin/nontrending.png', '', array('title' => $this->translate('Set Trending')))) ?></td>
        <?php endif; ?>
        <td><?php echo $item->creation_date ?></td>
        <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view')) ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service', 'action' => 'edit', 'ser_id' => $item->ser_id, 'pro_id' => $item->pro_id),
                $this->translate("edit")); ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'admin-service-manage', 'action' => 'delete', 'id' => $item->ser_id,'format' => 'smoothbox'),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
</div>
</form>

<br/>
<div>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  'params' => $this->formValues,
)); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no service entries by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>