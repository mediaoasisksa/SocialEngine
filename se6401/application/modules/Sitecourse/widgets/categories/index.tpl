
<?php 
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
 . 'application/modules/Seaocore/externals/styles/styles.css');

$temp_menu_route = "sitecourse_general";
?>

<script type="text/javascript">
var form;
var categoryAction =function(category,categoryname, url)
{

 if(document.getElementById('filter_form_category')) {
  form=document.getElementById('filter_form_category');
  if(document.getElementById('category')){
   form.elements['category_id'].value = category;
   if(form.elements['category_id'])
    form.elements['category_id'].value = category;
  }
  if(document.getElementById('categoryname')){
   form.elements['categoryname'].value = categoryname;
  }
  form.submit();
 } 
 else if(document.getElementById('filter_form_category')){
  form=document.getElementById('filter_form_category');
  if(url == '') {
   window.location.href='<?php echo $this->url(array('action' => 'index'), $temp_menu_route, true)?>';
  } else {
   window.location.href= url;
  }
 }

}

var subcategoryAction = function(category,subcategory,categoryname,subcategoryname, url)
{

 if(document.getElementById('filter_form_category')) {
  form=document.getElementById('filter_form_category');
  if(document.getElementById('category')){
   form.elements['category_id'].value = category;
   if(form.elements['category_id'])
    form.elements['category_id'].value = category;
  }

  if(document.getElementById('categoryname')){
   form.elements['categoryname'].value = categoryname;
  }

  if(document.getElementById('subcategory')){
   form.elements['subcategory_id'].value = subcategory;
   if(form.elements['subcategory_id'])
    form.elements['subcategory_id'].value = subcategory;
  }
  if(document.getElementById('subcategoryname')){
   form.elements['subcategoryname'].value = subcategoryname;
  }

  form.submit();
 } else if(document.getElementById('filter_form_category')){
  form=document.getElementById('filter_form_category');
  if(url == '') {
   window.location.href='<?php echo $this->url(array('action' => 'index'), $temp_menu_route, true)?>';
  } else {
   window.location.href= url;
  }
 }   
}

function show_subcat(cat_id)
{
 if(document.getElementById('subcat_' + cat_id)) {

  if(document.getElementById('subcat_' + cat_id).style.display == 'block') {
   document.getElementById('subcat_' + cat_id).style.display = 'none';
   document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/icons/plus16.gif';
  }
  else if(document.getElementById('subcat_' + cat_id).style.display == '') {
   document.getElementById('subcat_' + cat_id).style.display = 'none';
   document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/icons/plus16.gif';
  }
  else {
   document.getElementById('subcat_' + cat_id).style.display = 'block';
   document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/icons/minus16.gif';
  }
 }
}



window.addEventListener('DOMContentLoaded', function() {
 var subcategory_default = '<?php echo $this->subcategorys; ?>;'
 if(subcategory_default == 0)
  show_subcat('<?php echo $this->category; ?>');

});
</script>

<?php if (count($this->categories)): ?>
 <form id='filter_form_category' class='global_form_box' method='get' style='display: none;'>
  <input type="hidden" id="category" name="category_id"  value=""/>
  <input type="hidden" id="categoryname" name="categoryname"  value=""/>
  <input type="hidden" id="subcategory" name="subcategory_id" value=""/>
  <input type="hidden" id="subcategoryname" name="subcategoryname"  value=""/>
 </form>
</form>
<ul class="seaocore_browse_category">
 <li>
  <a href="javascript:subcategoryAction(0,0,0,0,0)" <?php if ($this->category == 0): ?>class="bold"<?php endif; ?>><?php echo $this->translate("All Categories"); ?></a>
 </li>
 <?php foreach ($this->categories as $category) : ?>


  <?php $total_subcat = count($category['sub_categories']); ?>
  <?php if ($total_subcat > 0): ?>
   <li>
    <div class="cat" >
     <a href="javascript:show_subcat('<?php echo $category['category_id']; ?>')" id='button_<?php echo $category['category_id'] ?>'>
      <?php if ($this->category != $category['category_id']): ?>
       <img alt=""  src='<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitecourse/externals/images/icons/plus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id'] ?>'/>
      <?php elseif ($this->subcategorys != 0 && $this->category == $category['category_id']): ?>
       <img alt="" src='<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitecourse/externals/images/icons/minus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id'] ?>'/>
      <?php elseif ($this->category != 0 && $this->category == $category['category_id']): ?>
       <img alt="" src='<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitecourse/externals/images/icons/plus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id']; ?>'/>
      <?php endif; ?>
     </a>
     <a <?php if ($this->category == $category['category_id']): ?> class="bold"<?php endif; ?> href='javascript:void(0);' onclick="javascript:categoryAction('<?php echo $category['category_id']; ?>','','<?php echo $this->url(array('action' => 'index', 'category' => $category["category_id"], 'categoryname' => ''), $temp_menu_route, true);?>');">
      <?php echo $this->translate($category['category_name']) ?>
     </a>
    </div>

    <div class="subcat" id="subcat_<?php echo $category['category_id'] ?>" <?php if ($this->category != $category['category_id'] || $this->subcategorys == 0): ?>style="display:none;"<?php endif; ?> >
     <?php foreach ($category['sub_categories'] as $subcategory) : ?>
       <div class="subcat_second">
        <img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/icons/minus16_disabled.gif" class='icon' border="0" />
        <a <?php if ($this->subcategorys == $subcategory['sub_cat_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>','<?php echo $subcategory['category_id'] ?>','<?php echo $category['category_name'] ?>','<?php echo $subcategory['category_name'] ?>','<?php echo $this->url(array('action' => 'index', 'category' => $category["category_id"], 'categoryname' => $category['category_name'],'subcategory' => $subcategory["category_id"],'subcategoryname' => $subcategory['category_name']), $temp_menu_route, true);?>',0, 0);">
         <?php echo $this->translate($subcategory['category_name']) ?>
        </a>
       </div>  
     <?php endforeach; ?>
    </div>
   </li>
  <?php else: ?>
   <li>
    <div class="cat">
     <img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/icons/minus16_disabled.gif" class='icon' border="0" />
     <a <?php if ($this->category == $category['category_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:categoryAction('<?php echo $category["category_id"] ?>','<?php echo $category['category_name']; ?>','<?php echo $this->url(array('action' => 'index', 'category' => $category["category_id"], 'categoryname' => ''), $temp_menu_route, true);?>');"><?php echo $this->translate($category['category_name']) ?>
    </a>
   </div>
  </li>
 <?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
