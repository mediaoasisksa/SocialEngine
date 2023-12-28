<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manage-search.tpl 2015-10-28 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>

<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>

<div class='tabs'>
  <ul class="navigation">
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'header-settings'), $this->translate('Header Settings')) ?>
    </li>
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'index'), $this->translate('Main Menu Icons')) ?>
    </li>
    <li  class="active">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'manage-search'), $this->translate('Manage Search Module')) ?>
    </li>
  </ul>
</div>
<script type="text/javascript">

  var SortablesInstance;

  window.addEvent('load', function() {
    SortablesInstance = new Sortables('menu_list', {
      clone: true,
      constrain: false,
      handle: '.item_label',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

  
 var reorder = function(e) {
     var menuitems = e.parentNode.childNodes;
     var ordering = {};
     var i = 1;
     for (var menuitem in menuitems)
     {
       var child_id = menuitems[menuitem].id;

       if ((child_id != undefined))
       {
         ordering[child_id] = i;
         i++;
       }
     }
    ordering['format'] = 'json';

    // Send request
    var url = '<?php echo $this->url(array('action' => 'order-manage-search')) ?>';
    var request = new Request.JSON({
      'url' : url,
      'method' : 'POST',
      'data' : ordering,
      onSuccess : function(responseJSON) {
      }
    });

    request.send();
  }
</script>


<h3><?php echo "Manage Modules for Search"; ?></h3>
<p><?php echo "This page lists all the modules / plugins which are compatible for AJAX based Global Search via this theme. Below, you can enable / disable any module and add / remove icon for them.<br />
Enable / disable modules modules search and choose number of modules to be shown from the “Header Settings” of this plugin.<br />
To reorder the modules, click on their row and drag them up or down.
"; ?> </p>
<br />
<?php if(count($this->getAllSearchOptions) > 0):?>
  <div class="sesbasic_manage_table">
    <form>
      <div class="sesbasic_manage_table_head">
        <div style="width:20%">
          <?php echo "Type";?>
        </div>
        <div style="width:20%">
          <?php echo "Title";?>
        </div>
        <div style="width:20%" class="admin_table_centered">
          <?php echo "Icon";?>
        </div>
        <div style="width:20%" class="admin_table_centered">
          <?php echo "Status";?>
        </div>
        <div style="width:20%" class="">
          <?php echo "Options";?>
        </div>   
      </div>
      <ul class="sesbasic_manage_table_list" id='menu_list'>
        <?php foreach ($this->getAllSearchOptions as $item): 
        $options = strtoupper('ITEM_TYPE_' . $item->type);
        if($options == 'ITEM_TYPE_CORE_LINK') continue; ?>
          <li class="item_label" id="managesearch_<?php echo $item->managesearchoption_id ?>">
            <input type='hidden'  name='order[]' value='<?php echo $item->managesearchoption_id; ?>'>
						<div style="width:20%;">
				      <?php echo $item->type; ?>
						</div>
					 	<div style="width:20%;">
				      <?php echo $this->translate($item->title); ?>
						</div>
						<div style="width:20%;" class="admin_table_centered">
							<?php if(!empty($item->file_id)):?>
								<img class="" alt="" src="<?php echo $this->storage->get($item->file_id, '')->getPhotoUrl(); ?>" />
						  <?php else: ?>
							  <?php if($options == 'ITEM_TYPE_VIDEO'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/video.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_ALBUM'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/album.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_ALBUM_PHOTO'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/photo.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_USER'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/user.png'; ?>" alt="">
							  <?php elseif($options == 'ITEM_TYPE_BLOG'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/blog.png'; ?>" alt="">
							  <?php elseif($options == 'ITEM_TYPE_BLOG'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/blog.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_CLASSIFIED'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/classified.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_EVENT'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/event.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_GROUP'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/group.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_FORUM_POST'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/topic.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_FORUM_TOPIC'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/topic.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_MUSIC_PLAYLIST'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/music-album.png'; ?>" alt="">
								<?php elseif($options == 'ITEM_TYPE_MUSIC_PLAYLIST_SONG'): ?>
									<img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search-icons/song.png'; ?>" alt="">
			          <?php else: ?>
								  <img src="<?php echo $base_url.'application/modules/Sescompany/externals/images/search.png'; ?>" alt="">
							  <?php endif;?>
							<?php endif;?>
						</div>
				    <div style="width:20%;" class="admin_table_centered">
				      <?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'enabled', 'managesearchoption_id' => $item->managesearchoption_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title' => $this->translate('Disable'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'enabled', 'managesearchoption_id' => $item->managesearchoption_id), $this->htmlImage('application/modules/Sesbasic/externals/images/icons/error.png', '', array('title' => $this->translate('Enable')))) ) ?>
						</div>
						<div style="width:20%;">
						  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'edit-search', 'id' => $item->managesearchoption_id), $this->translate("Edit"), array('class' => 'smoothbox')) ?>
              <?php if(!empty($item->file_id)):?>
		          | 
		          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sescompany', 'controller' => 'manage', 'action' => 'delete-search-icon', 'file_id' => $item->file_id, 'id' => $item->managesearchoption_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
		          <?php endif;?>
            </div>
          </li>
        <?php endforeach; ?>
			</ul>
    </form>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo "You have not uploaded any slide yet.";?>
    </span>
  </div>
<?php endif;?>