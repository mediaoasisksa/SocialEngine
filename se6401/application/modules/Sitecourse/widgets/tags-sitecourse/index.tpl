<?php 
// include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">

  var tagAction = function(tag){
    form=document.getElementById('filter_form_tag');  
    form.elements['tag'].value = tag;
    document.getElementById('filter_form_tag').submit();

  }
</script>

<form id='filter_form_tag' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitecourse_general', true) ?>' style='display: none;'>
  <input type="hidden" id="tag" name="tag"  value=""/>
</form>
<ul class="sitecourse_sidebar_list">
  <li>
    <?php foreach ($this->tag_array as $key => $frequency): ?>
      <?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
      <?php $step = ($step > 15) ? 15 : $step; ?>
      <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $this->tag_id_array[$key]; ?>);' style="float:none;font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>
    <?php endforeach; ?>
    <br/>
  </li>

</ul>

<?php if(!$this->notShowExploreTags): ?>
<?php echo $this->htmlLink(array('route' => "sitecourse_general", 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;'), array('class' => 'common_btn')) ?> 
<?php endif; ?>


<script type="text/javascript">
  window.addEventListener('DOMContentLoaded', () => {
    const el = document.querySelector('.layout_sitecourse_tags_sitecourse');
    if(el) {
      const child = el.firstElementChild;
      child.textContent += ' (<?php echo count($this->tag_array); ?>)';
    }
  })

</script>
